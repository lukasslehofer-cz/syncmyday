<?php

namespace App\Services\Sync;

use App\Models\CalendarConnection;
use App\Models\SyncRule;
use App\Models\SyncLog;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use App\Services\Email\ImipEmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Sync Engine
 * 
 * Core synchronization logic that:
 * 1. Fetches changed events from source calendar
 * 2. Applies filters based on sync rules
 * 3. Creates/updates/deletes blockers in target calendars
 * 4. Prevents sync loops using transaction IDs
 * 5. Logs all operations
 */
class SyncEngine
{
    private GoogleCalendarService $googleService;
    private MicrosoftCalendarService $microsoftService;
    private ImipEmailService $imipEmail;

    public function __construct(
        GoogleCalendarService $googleService,
        MicrosoftCalendarService $microsoftService,
        ImipEmailService $imipEmail
    ) {
        $this->googleService = $googleService;
        $this->microsoftService = $microsoftService;
        $this->imipEmail = $imipEmail;
    }

    /**
     * Sync all active rules for a calendar connection
     */
    public function syncConnection(CalendarConnection $connection): void
    {
        Log::channel('sync')->info('Syncing connection', [
            'connection_id' => $connection->id,
            'provider' => $connection->provider,
        ]);

        // Get all active sync rules for this connection as source
        $rules = SyncRule::where('source_connection_id', $connection->id)
            ->where('is_active', true)
            ->with(['targets.targetConnection', 'targets.targetEmailConnection'])
            ->get();

        foreach ($rules as $rule) {
            try {
                $this->syncRule($rule, $connection);
            } catch (\Exception $e) {
                Log::channel('sync')->error('Rule sync failed', [
                    'rule_id' => $rule->id,
                    'error' => $e->getMessage(),
                ]);

                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    'error',
                    null,
                    null,
                    null,
                    null,
                    null,
                    $e->getMessage()
                );
            }
        }

        $connection->update(['last_sync_at' => now()]);
    }

    /**
     * Sync a specific rule
     */
    public function syncRule(SyncRule $rule, CalendarConnection $sourceConnection): void
    {
        // Initialize source service
        $sourceService = $this->getService($sourceConnection);
        $sourceService->initializeWithConnection($sourceConnection);

        // Get webhook subscription for this calendar to get sync token
        $subscription = $sourceConnection->webhookSubscriptions()
            ->where('calendar_id', $rule->source_calendar_id)
            ->where('status', 'active')
            ->first();

        // Fetch changed events
        $changedData = $this->fetchChangedEvents(
            $sourceService,
            $sourceConnection->provider,
            $rule->source_calendar_id,
            $subscription?->sync_token
        );

        // Update sync token
        if ($subscription && isset($changedData['sync_token'])) {
            $subscription->update(['sync_token' => $changedData['sync_token']]);
        }

        // Process each event (with time range filtering)
        $pastDays = config('sync.time_range.past_days', 7);
        $futureMonths = config('sync.time_range.future_months', 6);
        $timeMin = now()->subDays($pastDays);
        $timeMax = now()->addMonths($futureMonths);
        
        $processedCount = 0;
        $skippedCount = 0;
        
        foreach ($changedData['events'] as $event) {
            // Filter by time range even for incremental syncs
            $eventStart = $this->getEventStart($event, $sourceConnection->provider);
            
            if ($eventStart) {
                // Skip events outside our sync range
                if ($eventStart < $timeMin || $eventStart > $timeMax) {
                    $skippedCount++;
                    Log::channel('sync')->debug('Event outside sync range, skipping', [
                        'event_id' => $this->getEventId($event),
                        'event_start' => $eventStart->format('Y-m-d H:i:s'),
                        'time_min' => $timeMin->format('Y-m-d H:i:s'),
                        'time_max' => $timeMax->format('Y-m-d H:i:s'),
                    ]);
                    continue;
                }
            }
            
            $this->processEvent($event, $rule, $sourceService, $sourceConnection);
            $processedCount++;
        }
        
        if ($skippedCount > 0) {
            Log::channel('sync')->debug('Skipped events outside sync range', [
                'rule_id' => $rule->id,
                'processed' => $processedCount,
                'skipped' => $skippedCount,
            ]);
        }

        $rule->update(['last_triggered_at' => now()]);
    }

    /**
     * Process a single event
     */
    private function processEvent(
        $event,
        SyncRule $rule,
        $sourceService,
        CalendarConnection $sourceConnection
    ): void {
        $transactionId = Str::uuid()->toString();

        // Check if this is our own blocker - skip to prevent loops
        if ($sourceService->isOurBlocker($event)) {
            Log::channel('sync')->debug('Skipping own blocker', ['event_id' => $this->getEventId($event)]);
            return;
        }

        // Check if event is deleted/cancelled
        $isDeleted = $this->isEventDeleted($event, $sourceConnection->provider);

        // Apply filters
        if (!$isDeleted && !$rule->shouldSyncEvent($this->normalizeEvent($event, $sourceConnection->provider))) {
            // Don't log filtered events to DB - would spam dashboard
            Log::channel('sync')->debug('Event filtered out by rules', [
                'event_id' => $this->getEventId($event),
                'rule_id' => $rule->id,
            ]);
            return;
        }

        // Sync to all targets
        foreach ($rule->targets as $target) {
            try {
                if ($target->isEmailTarget()) {
                    // Target is an email calendar - send iMIP
                    if ($isDeleted) {
                        $this->deleteBlockerInEmailTarget($event, $target, $rule, $transactionId);
                    } else {
                        $this->createOrUpdateBlockerInEmailTarget($event, $target, $sourceConnection, $rule, $transactionId);
                    }
                } else {
                    // Target is an API calendar (Google/Microsoft)
                    $targetService = $this->getService($target->targetConnection);
                    $targetService->initializeWithConnection($target->targetConnection);

                    if ($isDeleted) {
                        $this->deleteBlockerInTarget($event, $target, $targetService, $rule, $transactionId);
                    } else {
                        $this->createOrUpdateBlockerInTarget($event, $target, $targetService, $sourceConnection, $rule, $transactionId);
                    }
                }
            } catch (\Exception $e) {
                $targetId = $target->target_connection_id ?? $target->target_email_connection_id;
                Log::channel('sync')->error('Target sync failed', [
                    'target_id' => $targetId,
                    'error' => $e->getMessage(),
                ]);

                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    'error',
                    'source_to_target',
                    $this->getEventId($event),
                    null,
                    null,
                    null,
                    $e->getMessage(),
                    $transactionId
                );
            }
        }
    }

    /**
     * Create or update blocker in target calendar
     */
    private function createOrUpdateBlockerInTarget(
        $sourceEvent,
        $target,
        $targetService,
        CalendarConnection $sourceConnection,
        SyncRule $rule,
        string $transactionId
    ): void {
        $sourceEventId = $this->getEventId($sourceEvent);
        $start = $this->getEventStart($sourceEvent, $sourceConnection->provider);
        $end = $this->getEventEnd($sourceEvent, $sourceConnection->provider);

        // ANTI-LOOP PROTECTION: Check if this source event is actually a blocker itself
        // If so, check if the original event is in the target calendar we're syncing to
        // This prevents infinite loops in bidirectional sync
        $isBlockerLoop = SyncEventMapping::where('target_calendar_id', $rule->source_calendar_id)
            ->where('target_connection_id', $sourceConnection->id)
            ->where('target_event_id', $sourceEventId)
            ->where('source_calendar_id', $target->target_calendar_id)
            ->where('source_connection_id', $target->target_connection_id)
            ->exists();
        
        if ($isBlockerLoop) {
            Log::channel('sync')->info('Skipping event to prevent sync loop', [
                'source_event_id' => $sourceEventId,
                'source_calendar_id' => $rule->source_calendar_id,
                'target_calendar_id' => $target->target_calendar_id,
                'rule_id' => $rule->id,
            ]);
            
            SyncLog::logSync(
                $rule->user_id,
                $rule->id,
                'skipped',
                'source_to_target',
                $sourceEventId,
                null,
                $start,
                $end,
                'Loop prevention: event is already a blocker from target calendar',
                $transactionId
            );
            
            return; // Skip this event to prevent loop
        }

        // Check if we already have a mapping for this event -> target
        $mapping = SyncEventMapping::findMapping(
            $rule->id,
            $sourceEventId,
            $target->target_connection_id,
            $target->target_calendar_id
        );

        $action = 'created';
        $blockerId = null;

        if ($mapping) {
            // Mapping exists - check if update is needed
            $needsUpdate = false;
            $maxTimestamp = new \DateTime('2038-01-01');
            $debugInfo = [];
            
            // Check if start/end time changed
            $mappingStart = $mapping->event_start;
            $mappingEnd = $mapping->event_end;
            
            if ($mappingStart && $start && $start <= $maxTimestamp) {
                // Normalize both to UTC for comparison
                $mappingStartUtc = clone $mappingStart;
                $mappingStartUtc->setTimezone(new \DateTimeZone('UTC'));
                $startUtc = clone $start;
                $startUtc->setTimezone(new \DateTimeZone('UTC'));
                
                // Compare timestamps (allow 1 minute tolerance for rounding)
                $diffStart = abs($mappingStartUtc->getTimestamp() - $startUtc->getTimestamp());
                
                $debugInfo['start_diff_seconds'] = $diffStart;
                $debugInfo['old_start_utc'] = $mappingStartUtc->format('c');
                $debugInfo['new_start_utc'] = $startUtc->format('c');
                
                if ($diffStart > 60) {
                    $needsUpdate = true;
                    $debugInfo['reason'] = 'start_time_changed';
                }
            } elseif (!$mappingStart && $start) {
                $needsUpdate = true;
                $debugInfo['reason'] = 'no_old_start';
            }
            
            if ($mappingEnd && $end && $end <= $maxTimestamp) {
                // Normalize both to UTC for comparison
                $mappingEndUtc = clone $mappingEnd;
                $mappingEndUtc->setTimezone(new \DateTimeZone('UTC'));
                $endUtc = clone $end;
                $endUtc->setTimezone(new \DateTimeZone('UTC'));
                
                $diffEnd = abs($mappingEndUtc->getTimestamp() - $endUtc->getTimestamp());
                
                $debugInfo['end_diff_seconds'] = $diffEnd;
                $debugInfo['old_end_utc'] = $mappingEndUtc->format('c');
                $debugInfo['new_end_utc'] = $endUtc->format('c');
                
                if ($diffEnd > 60) {
                    $needsUpdate = true;
                    if (!isset($debugInfo['reason'])) {
                        $debugInfo['reason'] = 'end_time_changed';
                    }
                }
            } elseif (!$mappingEnd && $end) {
                $needsUpdate = true;
                if (!isset($debugInfo['reason'])) {
                    $debugInfo['reason'] = 'no_old_end';
                }
            }
            
            if ($needsUpdate) {
                // Event time changed - update the blocker
                try {
                    $targetService->updateBlocker(
                        $target->target_calendar_id,
                        $mapping->target_event_id,
                        $rule->blocker_title,
                        $start,
                        $end,
                        $transactionId
                    );
                    
                    // Update mapping timestamps (store in UTC to avoid timezone issues)
                    $startToStore = null;
                    $endToStore = null;
                    
                    if ($start && $start <= $maxTimestamp) {
                        $startToStore = clone $start;
                        $startToStore->setTimezone(new \DateTimeZone('UTC'));
                    }
                    
                    if ($end && $end <= $maxTimestamp) {
                        $endToStore = clone $end;
                        $endToStore->setTimezone(new \DateTimeZone('UTC'));
                    }
                    
                    $mapping->update([
                        'event_start' => $startToStore,
                        'event_end' => $endToStore,
                    ]);
                    
                    $blockerId = $mapping->target_event_id;
                    $action = 'updated';
                    
                    $debugInfo['event_id'] = $sourceEventId;
                    Log::channel('sync')->info('Blocker updated due to time change', $debugInfo);
                    
                } catch (\Exception $e) {
                    // If update fails (e.g., blocker was manually deleted), create new one
                    Log::channel('sync')->warning('Failed to update blocker, creating new one', [
                        'target_event_id' => $mapping->target_event_id,
                        'error' => $e->getMessage(),
                    ]);
                    
                    $mapping->delete(); // Remove stale mapping
                    $mapping = null; // Will create new one below
                }
            } else {
                // No changes detected - skip update and logging
                Log::channel('sync')->debug('Blocker unchanged, skipping update', [
                    'event_id' => $sourceEventId,
                    'blocker_id' => $mapping->target_event_id,
                ]);
                return; // Early return - don't log anything
            }
        }

        if (!$mapping) {
            // No mapping exists - create new blocker
            $blockerId = $targetService->createBlocker(
                $target->target_calendar_id,
                $rule->blocker_title,
                $start,
                $end,
                $transactionId
            );

            // Create mapping (handle Y2038 problem for dates beyond 2038)
            // Store times in UTC to avoid timezone conversion issues
            $maxTimestamp = new \DateTime('2038-01-01');
            
            $startToStore = null;
            $endToStore = null;
            
            if ($start && $start <= $maxTimestamp) {
                $startToStore = clone $start;
                $startToStore->setTimezone(new \DateTimeZone('UTC'));
            }
            
            if ($end && $end <= $maxTimestamp) {
                $endToStore = clone $end;
                $endToStore->setTimezone(new \DateTimeZone('UTC'));
            }
            
            SyncEventMapping::create([
                'sync_rule_id' => $rule->id,
                'source_connection_id' => $sourceConnection->id,
                'source_calendar_id' => $rule->source_calendar_id,
                'source_event_id' => $sourceEventId,
                'target_connection_id' => $target->target_connection_id,
                'target_calendar_id' => $target->target_calendar_id,
                'target_event_id' => $blockerId,
                'event_start' => $startToStore,
                'event_end' => $endToStore,
            ]);
        }

        SyncLog::logSync(
            $rule->user_id,
            $rule->id,
            $action,
            'source_to_target',
            $sourceEventId,
            $blockerId,
            $start,
            $end,
            null,
            $transactionId
        );
    }

    /**
     * Delete blocker in target calendar
     */
    private function deleteBlockerInTarget(
        $sourceEvent,
        $target,
        $targetService,
        SyncRule $rule,
        string $transactionId
    ): void {
        $sourceEventId = $this->getEventId($sourceEvent);
        
        // Find the mapping
        $mapping = SyncEventMapping::findMapping(
            $rule->id,
            $sourceEventId,
            $target->target_connection_id,
            $target->target_calendar_id
        );

        if ($mapping) {
            try {
                // Delete the blocker in target calendar
                $targetService->deleteBlocker(
                    $target->target_calendar_id,
                    $mapping->target_event_id
                );
                
                // Delete the mapping
                $mapping->delete();
                
                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    'deleted',
                    'source_to_target',
                    $sourceEventId,
                    $mapping->target_event_id,
                    null,
                    null,
                    null,
                    $transactionId
                );
            } catch (\Exception $e) {
                // Blocker might be already deleted manually
                Log::channel('sync')->warning('Failed to delete blocker', [
                    'target_event_id' => $mapping->target_event_id,
                    'error' => $e->getMessage(),
                ]);
                
                // Still delete the mapping
                $mapping->delete();
                
                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    'deleted',
                    'source_to_target',
                    $sourceEventId,
                    null,
                    null,
                    null,
                    'Blocker not found in target calendar (might be already deleted)',
                    $transactionId
                );
            }
        } else {
            // No mapping found - event was probably never synced or filtered out
            Log::channel('sync')->debug('No mapping found for deleted event', [
                'event_id' => $sourceEventId,
                'rule_id' => $rule->id,
            ]);
        }
    }

    /**
     * Create or update blocker in email target (send iMIP)
     */
    private function createOrUpdateBlockerInEmailTarget(
        $sourceEvent,
        $target,
        CalendarConnection $sourceConnection,
        SyncRule $rule,
        string $transactionId
    ): void {
        $targetEmailConnection = $target->targetEmailConnection;
        
        if (!$targetEmailConnection || $targetEmailConnection->status !== 'active') {
            Log::channel('sync')->warning('Email target not active', [
                'target_email_connection_id' => $target->target_email_connection_id,
            ]);
            return;
        }
        
        if (!$targetEmailConnection->target_email) {
            Log::channel('sync')->warning('Email calendar has no target_email configured', [
                'email_calendar_id' => $targetEmailConnection->id,
            ]);
            return;
        }
        
        $sourceEventId = $this->getEventId($sourceEvent);
        $start = $this->getEventStart($sourceEvent, $sourceConnection->provider);
        $end = $this->getEventEnd($sourceEvent, $sourceConnection->provider);

        // Check if we already have a mapping for this event -> email target
        $mapping = SyncEventMapping::where([
            'sync_rule_id' => $rule->id,
            'source_event_id' => $sourceEventId,
            'target_email_connection_id' => $target->target_email_connection_id,
        ])->first();
        
        Log::channel('sync')->debug('Email target mapping check', [
            'source_event_id' => $sourceEventId,
            'sync_rule_id' => $rule->id,
            'target_email_connection_id' => $target->target_email_connection_id,
            'mapping_found' => $mapping ? 'yes' : 'no',
            'mapping_id' => $mapping ? $mapping->id : null,
        ]);
        
        // Generate a stable event UID for iMIP
        $eventUid = 'syncmyday-' . $rule->id . '-' . md5($sourceEventId);
        $sequence = $mapping ? ($mapping->sequence ?? 0) : 0;
        $action = 'created';
        $maxTimestamp = new \DateTime('2038-01-01');

        // Check if update is needed (for existing mappings)
        if ($mapping) {
            $needsUpdate = false;
            $mappingStart = $mapping->event_start;
            $mappingEnd = $mapping->event_end;
            
            // Check if start/end time changed
            if ($mappingStart && $start && $start <= $maxTimestamp) {
                // Normalize both to UTC for comparison
                $mappingStartUtc = clone $mappingStart;
                $mappingStartUtc->setTimezone(new \DateTimeZone('UTC'));
                $startUtc = clone $start;
                $startUtc->setTimezone(new \DateTimeZone('UTC'));
                
                $diffStart = abs($mappingStartUtc->getTimestamp() - $startUtc->getTimestamp());
                
                if ($diffStart > 60) {
                    $needsUpdate = true;
                }
            } elseif (!$mappingStart && $start) {
                $needsUpdate = true;
            }
            
            if ($mappingEnd && $end && $end <= $maxTimestamp) {
                // Normalize both to UTC for comparison
                $mappingEndUtc = clone $mappingEnd;
                $mappingEndUtc->setTimezone(new \DateTimeZone('UTC'));
                $endUtc = clone $end;
                $endUtc->setTimezone(new \DateTimeZone('UTC'));
                
                $diffEnd = abs($mappingEndUtc->getTimestamp() - $endUtc->getTimestamp());
                
                if ($diffEnd > 60) {
                    $needsUpdate = true;
                }
            } elseif (!$mappingEnd && $end) {
                $needsUpdate = true;
            }
            
            if (!$needsUpdate) {
                // No changes - skip sending email
                Log::channel('sync')->debug('Email blocker unchanged, skipping iMIP', [
                    'event_id' => $sourceEventId,
                    'target_email' => $targetEmailConnection->target_email,
                    'start_diff' => $diffStart ?? 0,
                    'end_diff' => $diffEnd ?? 0,
                ]);
                return;
            }
            
            $action = 'updated';
        }

        try {
            // Send iMIP email (REQUEST for create/update)
            $success = $this->imipEmail->sendBlockerInvitation(
                $targetEmailConnection,
                $targetEmailConnection->target_email,
                $eventUid,
                $rule->blocker_title,
                $start,
                $end,
                'REQUEST',
                $sequence + 1
            );

            if ($success) {
                // Store times in UTC to avoid timezone conversion issues
                $startToStore = null;
                $endToStore = null;
                
                if ($start && $start <= $maxTimestamp) {
                    $startToStore = clone $start;
                    $startToStore->setTimezone(new \DateTimeZone('UTC'));
                }
                
                if ($end && $end <= $maxTimestamp) {
                    $endToStore = clone $end;
                    $endToStore->setTimezone(new \DateTimeZone('UTC'));
                }
                
                if ($mapping) {
                    // Update existing mapping
                    $mapping->update([
                        'event_start' => $startToStore,
                        'event_end' => $endToStore,
                        'sequence' => $sequence + 1,
                    ]);
                    
                    Log::channel('sync')->info('Email blocker updated', [
                        'event_id' => $sourceEventId,
                        'target_email' => $targetEmailConnection->target_email,
                        'event_uid' => $eventUid,
                    ]);
                } else {
                    // Create new mapping
                    $newMapping = SyncEventMapping::create([
                        'sync_rule_id' => $rule->id,
                        'source_connection_id' => $sourceConnection->id,
                        'source_calendar_id' => $rule->source_calendar_id,
                        'source_event_id' => $sourceEventId,
                        'target_email_connection_id' => $target->target_email_connection_id,
                        'target_calendar_id' => null,
                        'target_event_id' => $eventUid,
                        'event_start' => $startToStore,
                        'event_end' => $endToStore,
                        'sequence' => 1,
                    ]);
                    
                    Log::channel('sync')->info('Email blocker created and mapping saved', [
                        'event_id' => $sourceEventId,
                        'target_email' => $targetEmailConnection->target_email,
                        'event_uid' => $eventUid,
                        'mapping_id' => $newMapping->id,
                        'sync_rule_id' => $rule->id,
                        'target_email_connection_id' => $target->target_email_connection_id,
                    ]);
                }

                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    $action,
                    'api_to_email',
                    $sourceEventId,
                    $eventUid,
                    $start,
                    $end,
                    null,
                    $transactionId
                );
            }
        } catch (\Exception $e) {
            Log::channel('sync')->error('Failed to send iMIP to email target', [
                'target_email' => $targetEmailConnection->target_email,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete blocker in email target (send CANCEL iMIP)
     */
    private function deleteBlockerInEmailTarget(
        $sourceEvent,
        $target,
        SyncRule $rule,
        string $transactionId
    ): void {
        $sourceEventId = $this->getEventId($sourceEvent);
        
        // Find the mapping
        $mapping = SyncEventMapping::where([
            'sync_rule_id' => $rule->id,
            'source_event_id' => $sourceEventId,
            'target_email_connection_id' => $target->target_email_connection_id,
        ])->first();

        if ($mapping) {
            $targetEmailConnection = $target->targetEmailConnection;
            
            if ($targetEmailConnection && $targetEmailConnection->target_email) {
                try {
                    // Get event times from mapping (might be null for old events)
                    $start = $mapping->event_start ?? new \DateTime();
                    $end = $mapping->event_end ?? new \DateTime('+1 hour');
                    
                    // Send CANCEL iMIP email
                    $this->imipEmail->sendCancellation(
                        $targetEmailConnection,
                        $targetEmailConnection->target_email,
                        $mapping->target_event_id,
                        $rule->blocker_title,
                        $start,
                        $end,
                        $mapping->sequence ?? 0
                    );
                    
                    Log::channel('sync')->info('Email blocker deleted', [
                        'event_id' => $sourceEventId,
                        'target_email' => $targetEmailConnection->target_email,
                        'event_uid' => $mapping->target_event_id,
                    ]);
                    
                    SyncLog::logSync(
                        $rule->user_id,
                        $rule->id,
                        'deleted',
                        'api_to_email',
                        $sourceEventId,
                        $mapping->target_event_id,
                        null,
                        null,
                        null,
                        $transactionId
                    );
                } catch (\Exception $e) {
                    Log::channel('sync')->warning('Failed to send CANCEL iMIP', [
                        'target_email' => $targetEmailConnection->target_email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            // Delete mapping regardless of email send success
            $mapping->delete();
        }
    }

    /**
     * Fetch changed events from calendar
     */
    private function fetchChangedEvents(
        $service,
        string $provider,
        string $calendarId,
        ?string $syncToken
    ): array {
        if ($provider === 'google') {
            return $service->getChangedEvents($calendarId, $syncToken);
        } else {
            $result = $service->getChangedEvents($calendarId, $syncToken);
            return [
                'events' => $result['events'],
                'sync_token' => $result['delta_link'] ?? $syncToken,
            ];
        }
    }

    /**
     * Get calendar service for connection
     */
    private function getService(CalendarConnection $connection)
    {
        return $connection->provider === 'google'
            ? $this->googleService
            : $this->microsoftService;
    }

    /**
     * Normalize event data across providers
     */
    private function normalizeEvent($event, string $provider): array
    {
        if ($provider === 'google') {
            $isAllDay = $event->getStart()->getDate() !== null;
            $transparency = $event->getTransparency();
            
            // All-day events should always be considered 'busy' by default
            // This allows users to control them via the 'ignore_all_day' filter
            // Only non-all-day events respect the transparency setting
            if ($isAllDay) {
                $showAs = 'busy';
            } else {
                $showAs = $transparency === 'transparent' ? 'free' : 'busy';
            }
            
            return [
                'id' => $event->getId(),
                'start' => $event->getStart()->getDateTime() ?? $event->getStart()->getDate(),
                'end' => $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate(),
                'isAllDay' => $isAllDay,
                'busyStatus' => $showAs,
                'showAs' => $showAs,
            ];
        } else {
            // Microsoft
            $isAllDay = $event['isAllDay'] ?? false;
            
            // Same logic for Microsoft: all-day events are 'busy' by default
            if ($isAllDay) {
                $showAs = 'busy';
            } else {
                $showAs = $event['showAs'] ?? 'busy';
            }
            
            return [
                'id' => $event['id'],
                'start' => $event['start']['dateTime'],
                'end' => $event['end']['dateTime'],
                'isAllDay' => $isAllDay,
                'busyStatus' => $showAs,
                'showAs' => $showAs,
            ];
        }
    }

    private function getEventId($event): string
    {
        return is_array($event) ? $event['id'] : $event->getId();
    }

    private function getEventStart($event, string $provider): ?\DateTime
    {
        try {
            if ($provider === 'google') {
                $dateTime = $event->getStart()->getDateTime() ?? $event->getStart()->getDate();
                return new \DateTime($dateTime);
            } else {
                return new \DateTime($event['start']['dateTime']);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEventEnd($event, string $provider): ?\DateTime
    {
        try {
            if ($provider === 'google') {
                $dateTime = $event->getEnd()->getDateTime() ?? $event->getEnd()->getDate();
                return new \DateTime($dateTime);
            } else {
                return new \DateTime($event['end']['dateTime']);
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isEventDeleted($event, string $provider): bool
    {
        if ($provider === 'google') {
            return $event->getStatus() === 'cancelled';
        } else {
            // Microsoft sends a special removed property in delta
            return isset($event['@removed']) && $event['@removed'];
        }
    }
}

