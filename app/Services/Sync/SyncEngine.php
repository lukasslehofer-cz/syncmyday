<?php

namespace App\Services\Sync;

use App\Models\CalendarConnection;
use App\Models\SyncRule;
use App\Models\SyncLog;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use App\Services\Calendar\CalDavCalendarService;
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
    private CalDavCalendarService $calDavService;
    private ImipEmailService $imipEmail;

    public function __construct(
        GoogleCalendarService $googleService,
        MicrosoftCalendarService $microsoftService,
        CalDavCalendarService $calDavService,
        ImipEmailService $imipEmail
    ) {
        $this->googleService = $googleService;
        $this->microsoftService = $microsoftService;
        $this->calDavService = $calDavService;
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
        
        Log::channel('sync')->info('Found sync rules for connection', [
            'connection_id' => $connection->id,
            'rule_count' => $rules->count(),
        ]);

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
        // Check if user has active subscription (soft-lock for expired trials)
        if (!$rule->user->hasActiveSubscription()) {
            Log::channel('sync')->warning('Sync skipped - user subscription expired', [
                'rule_id' => $rule->id,
                'user_id' => $rule->user_id,
            ]);
            return;
        }
        
        // OPTIMIZATION: Eager load all targets and their connections to prevent N+1 queries
        $rule->load([
            'targets.targetConnection',
            'targets.targetEmailConnection'
        ]);
        
        Log::channel('sync')->info('Starting sync rule', [
            'rule_id' => $rule->id,
            'source_calendar_id' => $rule->source_calendar_id,
            'target_count' => $rule->targets->count(),
        ]);
        
        // Initialize source service
        $sourceService = $this->getService($sourceConnection);
        $sourceService->initializeWithConnection($sourceConnection);

        // Get webhook subscription for this calendar to get sync token
        $subscription = $sourceConnection->webhookSubscriptions()
            ->where('calendar_id', $rule->source_calendar_id)
            ->where('status', 'active')
            ->first();

        $oldSyncToken = $subscription?->sync_token;
        $hadSyncToken = !empty($oldSyncToken);

        Log::channel('sync')->info('Sync token status before fetch', [
            'rule_id' => $rule->id,
            'has_subscription' => $subscription !== null,
            'had_sync_token' => $hadSyncToken,
            'sync_token_preview' => $oldSyncToken ? substr($oldSyncToken, 0, 50) . '...' : null,
        ]);

        // Fetch changed events
        $changedData = $this->fetchChangedEvents(
            $sourceService,
            $sourceConnection->provider,
            $rule->source_calendar_id,
            $subscription?->sync_token
        );
        
        Log::channel('sync')->info('Fetched events from source', [
            'rule_id' => $rule->id,
            'provider' => $sourceConnection->provider,
            'calendar_id' => $rule->source_calendar_id,
            'event_count' => count($changedData['events'] ?? []),
            'had_sync_token' => $hadSyncToken,
            'received_sync_token' => isset($changedData['sync_token']),
            'new_sync_token_preview' => isset($changedData['sync_token']) ? substr($changedData['sync_token'], 0, 50) . '...' : null,
        ]);

        // Update sync token
        if ($subscription && isset($changedData['sync_token'])) {
            $subscription->update(['sync_token' => $changedData['sync_token']]);
            
            Log::channel('sync')->info('Sync token saved to database', [
                'subscription_id' => $subscription->id,
                'connection_id' => $sourceConnection->id,
                'calendar_id' => $rule->source_calendar_id,
            ]);
        } elseif ($subscription && !isset($changedData['sync_token'])) {
            Log::channel('sync')->warning('No sync token received from provider', [
                'subscription_id' => $subscription->id,
                'provider' => $sourceConnection->provider,
            ]);
        } elseif (!$subscription) {
            Log::channel('sync')->warning('No webhook subscription found - cannot save sync token', [
                'connection_id' => $sourceConnection->id,
                'calendar_id' => $rule->source_calendar_id,
            ]);
        }

        // OPTIMIZATION: Pre-fetch all existing mappings for this rule
        // This reduces N queries to 1 query for all events
        $sourceEventIds = [];
        foreach ($changedData['events'] as $event) {
            $sourceEventIds[] = $this->getEventId($event);
        }
        
        $existingMappings = collect();
        if (!empty($sourceEventIds)) {
            $existingMappings = SyncEventMapping::where('sync_rule_id', $rule->id)
                ->whereIn('source_event_id', $sourceEventIds)
                ->get()
                ->groupBy(function ($mapping) {
                    // Group by source_event_id and target key for quick lookup
                    $targetKey = $mapping->target_connection_id 
                        ? "{$mapping->target_connection_id}:{$mapping->target_calendar_id}"
                        : "email:{$mapping->target_email_connection_id}";
                    return "{$mapping->source_event_id}|{$targetKey}";
                });
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
            
            $this->processEvent($event, $rule, $sourceService, $sourceConnection, $existingMappings);
            $processedCount++;
        }
        
        if ($skippedCount > 0) {
            Log::channel('sync')->debug('Skipped events outside sync range', [
                'rule_id' => $rule->id,
                'processed' => $processedCount,
                'skipped' => $skippedCount,
            ]);
        }

        // DELETED EVENTS DETECTION for full sync (when no sync token)
        // When using full sync, Google doesn't include deleted events
        // We need to detect them by comparing existing mappings with current events
        if (!$hadSyncToken && $processedCount > 0) {
            $this->detectDeletedEventsInFullSync($rule, $sourceEventIds, $sourceConnection, $sourceService, $existingMappings);
        }

        // Mark initial sync as completed if this rule has email targets
        // This enables sending emails for subsequent changes
        if (!$rule->initial_sync_completed) {
            $hasEmailTargets = $rule->targets->contains(function ($target) {
                return $target->isEmailTarget();
            });
            
            if ($hasEmailTargets) {
                $rule->update([
                    'initial_sync_completed' => true,
                    'last_triggered_at' => now(),
                ]);
                
                Log::channel('sync')->info('Initial sync completed for rule with email targets', [
                    'rule_id' => $rule->id,
                    'processed_events' => $processedCount,
                ]);
            } else {
                $rule->update(['last_triggered_at' => now()]);
            }
        } else {
            $rule->update(['last_triggered_at' => now()]);
        }
    }

    /**
     * Process a single event
     */
    private function processEvent(
        $event,
        SyncRule $rule,
        $sourceService,
        CalendarConnection $sourceConnection,
        $existingMappings = null
    ): void {
        $transactionId = Str::uuid()->toString();
        $sourceEventId = $this->getEventId($event);

        // Check if this is our own blocker - skip to prevent loops
        if ($sourceService->isOurBlocker($event)) {
            Log::channel('sync')->debug('Skipping own blocker (by category/tag)', ['event_id' => $sourceEventId]);
            return;
        }
        
        // ADDITIONAL ANTI-LOOP: Check if this event is a target blocker we just created
        // This catches blockers before they're marked with category/tag (race condition)
        $isTargetBlocker = SyncEventMapping::where('target_event_id', $sourceEventId)
            ->where('target_connection_id', $sourceConnection->id)
            ->where('target_calendar_id', $rule->source_calendar_id)
            ->exists();
        
        if ($isTargetBlocker) {
            Log::channel('sync')->debug('Skipping target blocker (by mapping)', [
                'event_id' => $sourceEventId,
                'calendar_id' => $rule->source_calendar_id,
            ]);
            return;
        }

        // Check if event is deleted/cancelled
        $isDeleted = $this->isEventDeleted($event, $sourceConnection->provider);

        Log::channel('sync')->info('Processing event', [
            'event_id' => $sourceEventId,
            'rule_id' => $rule->id,
            'is_deleted' => $isDeleted,
            'provider' => $sourceConnection->provider,
            'transaction_id' => $transactionId,
        ]);

        // Apply filters
        if (!$isDeleted && !$rule->shouldSyncEvent($this->normalizeEvent($event, $sourceConnection->provider))) {
            // Don't log filtered events to DB - would spam dashboard
            Log::channel('sync')->debug('Event filtered out by rules', [
                'event_id' => $sourceEventId,
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
                        Log::channel('sync')->info('Deleting blocker in email target', [
                            'event_id' => $sourceEventId,
                            'target_email_connection_id' => $target->target_email_connection_id,
                            'transaction_id' => $transactionId,
                        ]);
                        $this->deleteBlockerInEmailTarget($event, $target, $rule, $transactionId, $existingMappings);
                    } else {
                        $this->createOrUpdateBlockerInEmailTarget($event, $target, $sourceConnection, $rule, $transactionId, $existingMappings);
                    }
                } else {
                    // Target is an API calendar (Google/Microsoft)
                    $targetService = $this->getService($target->targetConnection);
                    $targetService->initializeWithConnection($target->targetConnection);

                    if ($isDeleted) {
                        Log::channel('sync')->info('Deleting blocker in API target', [
                            'event_id' => $sourceEventId,
                            'target_connection_id' => $target->target_connection_id,
                            'target_calendar_id' => $target->target_calendar_id,
                            'target_provider' => $target->targetConnection->provider,
                            'transaction_id' => $transactionId,
                        ]);
                        $this->deleteBlockerInTarget($event, $target, $targetService, $rule, $transactionId, $existingMappings);
                    } else {
                        $this->createOrUpdateBlockerInTarget($event, $target, $targetService, $sourceConnection, $rule, $transactionId, $existingMappings);
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
        string $transactionId,
        $existingMappings = null
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
        // OPTIMIZATION: Use pre-loaded mappings if available
        $mappingKey = $target->target_connection_id 
            ? "{$sourceEventId}|{$target->target_connection_id}:{$target->target_calendar_id}"
            : "{$sourceEventId}|email:{$target->target_email_connection_id}";
        
        $mapping = $existingMappings && isset($existingMappings[$mappingKey])
            ? $existingMappings[$mappingKey]->first()
            : SyncEventMapping::findMapping(
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
        string $transactionId,
        $existingMappings = null
    ): void {
        $sourceEventId = $this->getEventId($sourceEvent);
        
        Log::channel('sync')->info('Looking for mapping to delete', [
            'source_event_id' => $sourceEventId,
            'rule_id' => $rule->id,
            'target_connection_id' => $target->target_connection_id,
            'target_calendar_id' => $target->target_calendar_id,
            'transaction_id' => $transactionId,
        ]);
        
        // Find the mapping
        // OPTIMIZATION: Use pre-loaded mappings if available
        $mappingKey = $target->target_connection_id 
            ? "{$sourceEventId}|{$target->target_connection_id}:{$target->target_calendar_id}"
            : "{$sourceEventId}|email:{$target->target_email_connection_id}";
        
        $mapping = $existingMappings && isset($existingMappings[$mappingKey])
            ? $existingMappings[$mappingKey]->first()
            : SyncEventMapping::findMapping(
                $rule->id,
                $sourceEventId,
                $target->target_connection_id,
                $target->target_calendar_id
            );

        if ($mapping) {
            Log::channel('sync')->info('Mapping found, deleting blocker', [
                'mapping_id' => $mapping->id,
                'source_event_id' => $sourceEventId,
                'target_event_id' => $mapping->target_event_id,
                'target_calendar_id' => $target->target_calendar_id,
                'transaction_id' => $transactionId,
            ]);
            
            try {
                // Delete the blocker in target calendar
                $targetService->deleteBlocker(
                    $target->target_calendar_id,
                    $mapping->target_event_id
                );
                
                Log::channel('sync')->info('Blocker deleted successfully in target', [
                    'target_event_id' => $mapping->target_event_id,
                    'target_calendar_id' => $target->target_calendar_id,
                ]);
                
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
                    'target_calendar_id' => $target->target_calendar_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
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
            Log::channel('sync')->warning('No mapping found for deleted event', [
                'source_event_id' => $sourceEventId,
                'rule_id' => $rule->id,
                'target_connection_id' => $target->target_connection_id,
                'target_calendar_id' => $target->target_calendar_id,
                'transaction_id' => $transactionId,
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
        string $transactionId,
        $existingMappings = null
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
        
        if (!$targetEmailConnection->hasVerifiedTargetEmail()) {
            Log::channel('sync')->warning('Email calendar target_email not verified', [
                'email_calendar_id' => $targetEmailConnection->id,
                'target_email' => $targetEmailConnection->target_email,
            ]);
            return;
        }
        
        $sourceEventId = $this->getEventId($sourceEvent);
        $start = $this->getEventStart($sourceEvent, $sourceConnection->provider);
        $end = $this->getEventEnd($sourceEvent, $sourceConnection->provider);

        // Check if we already have a mapping for this event -> email target
        // OPTIMIZATION: Use pre-loaded mappings if available
        $mappingKey = "{$sourceEventId}|email:{$target->target_email_connection_id}";
        
        $mapping = $existingMappings && isset($existingMappings[$mappingKey])
            ? $existingMappings[$mappingKey]->first()
            : SyncEventMapping::where([
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

        // BATCH MODE for initial sync: Create mapping but don't send email
        // This prevents flooding user's inbox with hundreds of emails on first sync
        if (!$rule->initial_sync_completed && !$mapping) {
            Log::channel('sync')->debug('Initial sync batch mode - creating mapping without sending email', [
                'rule_id' => $rule->id,
                'source_event_id' => $sourceEventId,
                'target_email' => $targetEmailConnection->target_email,
            ]);
            
            // Store times in UTC
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
            
            // Create mapping without sending email
            SyncEventMapping::create([
                'sync_rule_id' => $rule->id,
                'source_connection_id' => $sourceConnection->id,
                'source_calendar_id' => $rule->source_calendar_id,
                'source_event_id' => $sourceEventId,
                'target_connection_id' => null,
                'target_email_connection_id' => $target->target_email_connection_id,
                'target_calendar_id' => null,
                'target_event_id' => $eventUid,
                'event_start' => $startToStore,
                'event_end' => $endToStore,
                'sequence' => 0, // Not sent yet
            ]);
            
            Log::channel('sync')->info('Initial sync: Mapping created without email', [
                'rule_id' => $rule->id,
                'source_event_id' => $sourceEventId,
                'target_email' => $targetEmailConnection->target_email,
            ]);
            
            return; // Skip sending email during initial sync
        }

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
                        'target_connection_id' => null, // Email target has no API connection
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
                    'source_to_target',
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
        string $transactionId,
        $existingMappings = null
    ): void {
        $sourceEventId = $this->getEventId($sourceEvent);
        
        // Find the mapping
        // OPTIMIZATION: Use pre-loaded mappings if available
        $mappingKey = "{$sourceEventId}|email:{$target->target_email_connection_id}";
        
        $mapping = $existingMappings && isset($existingMappings[$mappingKey])
            ? $existingMappings[$mappingKey]->first()
            : SyncEventMapping::where([
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
                        'source_to_target',
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
        return match($connection->provider) {
            'google' => $this->googleService,
            'microsoft' => $this->microsoftService,
            'caldav', 'apple' => $this->calDavService, // Apple uses CalDAV protocol
            default => throw new \Exception("Unsupported provider: {$connection->provider}"),
        };
    }

    /**
     * Normalize event data across providers
     */
    private function normalizeEvent($event, string $provider): array
    {
        return match($provider) {
            'google' => $this->normalizeGoogleEvent($event),
            'microsoft' => $this->normalizeMicrosoftEvent($event),
            'caldav', 'apple' => $this->normalizeCalDavEvent($event), // Apple uses CalDAV protocol
            default => throw new \Exception("Unsupported provider for normalization: {$provider}"),
        };
    }
    
    private function normalizeGoogleEvent($event): array
    {
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
    }
    
    private function normalizeMicrosoftEvent($event): array
    {
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
    
    private function normalizeCalDavEvent($event): array
    {
        // CalDAV events are already normalized in CalDavCalendarService::parseVEvent()
        return [
            'id' => $event['id'],
            'start' => $event['start']->format('c'),
            'end' => $event['end']->format('c'),
            'isAllDay' => $event['isAllDay'],
            'busyStatus' => $event['busyStatus'],
            'showAs' => $event['showAs'],
        ];
    }

    private function getEventId($event): string
    {
        return is_array($event) ? $event['id'] : $event->getId();
    }

    private function getEventStart($event, string $provider): ?\DateTime
    {
        try {
            return match($provider) {
                'google' => new \DateTime($event->getStart()->getDateTime() ?? $event->getStart()->getDate()),
                'microsoft' => new \DateTime($event['start']['dateTime']),
                'caldav', 'apple' => $event['start'], // Already DateTime object
                default => null,
            };
        } catch (\Exception $e) {
            return null;
        }
    }

    private function getEventEnd($event, string $provider): ?\DateTime
    {
        try {
            return match($provider) {
                'google' => new \DateTime($event->getEnd()->getDateTime() ?? $event->getEnd()->getDate()),
                'microsoft' => new \DateTime($event['end']['dateTime']),
                'caldav', 'apple' => $event['end'], // Already DateTime object
                default => null,
            };
        } catch (\Exception $e) {
            return null;
        }
    }

    private function isEventDeleted($event, string $provider): bool
    {
        $isDeleted = match($provider) {
            'google' => $event->getStatus() === 'cancelled',
            'microsoft' => (isset($event['@removed']) && $event['@removed']) || 
                          (isset($event['@odata.removed']) && $event['@odata.removed']),
            'caldav', 'apple' => isset($event['status']) && $event['status'] === 'cancelled',
            default => false,
        };
        
        if ($isDeleted) {
            Log::channel('sync')->info('Event detected as deleted', [
                'event_id' => $this->getEventId($event),
                'provider' => $provider,
                'status' => $provider === 'google' ? $event->getStatus() : ($event['status'] ?? 'unknown'),
                'removed_property' => $provider === 'microsoft' ? 
                    (isset($event['@removed']) ? '@removed' : '@odata.removed') : null,
            ]);
        }
        
        return $isDeleted;
    }

    /**
     * Detect deleted events during full sync by comparing existing mappings with current events
     * This is necessary because Google/Microsoft don't return deleted events in full sync
     */
    private function detectDeletedEventsInFullSync(
        SyncRule $rule,
        array $currentEventIds,
        CalendarConnection $sourceConnection,
        $sourceService,
        $existingMappings
    ): void {
        // Get all mappings for this rule
        $allMappings = SyncEventMapping::where('sync_rule_id', $rule->id)
            ->where('source_connection_id', $sourceConnection->id)
            ->get();
        
        if ($allMappings->isEmpty()) {
            return;
        }
        
        // Find mappings for events that are NOT in current event list
        $deletedMappings = $allMappings->filter(function ($mapping) use ($currentEventIds) {
            return !in_array($mapping->source_event_id, $currentEventIds);
        });
        
        if ($deletedMappings->isEmpty()) {
            return;
        }
        
        Log::channel('sync')->info('Detected deleted events in full sync', [
            'rule_id' => $rule->id,
            'total_mappings' => $allMappings->count(),
            'current_events' => count($currentEventIds),
            'deleted_count' => $deletedMappings->count(),
            'deleted_event_ids' => $deletedMappings->pluck('source_event_id')->toArray(),
        ]);
        
        // Process each deleted mapping
        foreach ($deletedMappings as $mapping) {
            $transactionId = \Str::uuid()->toString();
            
            // Find the target for this mapping
            $target = $rule->targets()->where(function ($query) use ($mapping) {
                if ($mapping->target_connection_id) {
                    $query->where('target_connection_id', $mapping->target_connection_id);
                } else {
                    $query->where('target_email_connection_id', $mapping->target_email_connection_id);
                }
            })->first();
            
            if (!$target) {
                Log::channel('sync')->warning('Target not found for deleted mapping', [
                    'mapping_id' => $mapping->id,
                    'source_event_id' => $mapping->source_event_id,
                ]);
                continue;
            }
            
            try {
                if ($target->isEmailTarget()) {
                    // Delete blocker in email target
                    Log::channel('sync')->info('Deleting orphaned blocker in email target', [
                        'source_event_id' => $mapping->source_event_id,
                        'target_email_connection_id' => $target->target_email_connection_id,
                        'transaction_id' => $transactionId,
                    ]);
                    
                    // Send cancellation email
                    $targetEmail = $target->targetEmailConnection;
                    if ($targetEmail && $targetEmail->target_email) {
                        $imipService = app(\App\Services\Email\ImipEmailService::class);
                        $imipService->sendBlockerInvitation(
                            $targetEmail,
                            $targetEmail->target_email,
                            $mapping->target_event_id,
                            'Cancelled',
                            new \DateTime(),
                            new \DateTime(),
                            'CANCEL',
                            $mapping->sequence ?? 0
                        );
                    }
                } else {
                    // Delete blocker in API target
                    Log::channel('sync')->info('Deleting orphaned blocker in API target', [
                        'source_event_id' => $mapping->source_event_id,
                        'target_connection_id' => $target->target_connection_id,
                        'target_calendar_id' => $target->target_calendar_id,
                        'target_event_id' => $mapping->target_event_id,
                        'target_provider' => $target->targetConnection->provider,
                        'transaction_id' => $transactionId,
                    ]);
                    
                    $targetService = $this->getService($target->targetConnection);
                    $targetService->initializeWithConnection($target->targetConnection);
                    
                    try {
                        $targetService->deleteBlocker(
                            $target->target_calendar_id,
                            $mapping->target_event_id
                        );
                        
                        Log::channel('sync')->info('Orphaned blocker deleted successfully', [
                            'target_event_id' => $mapping->target_event_id,
                        ]);
                    } catch (\Exception $e) {
                        Log::channel('sync')->warning('Failed to delete orphaned blocker', [
                            'target_event_id' => $mapping->target_event_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                
                // Delete the mapping
                $mapping->delete();
                
                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    'deleted',
                    'source_to_target',
                    $mapping->source_event_id,
                    $mapping->target_event_id,
                    null,
                    null,
                    'Detected as deleted in full sync',
                    $transactionId
                );
                
            } catch (\Exception $e) {
                Log::channel('sync')->error('Failed to process deleted mapping', [
                    'mapping_id' => $mapping->id,
                    'source_event_id' => $mapping->source_event_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }
    }
}

