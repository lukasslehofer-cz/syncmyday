<?php

namespace App\Services\Email;

use App\Models\EmailCalendarConnection;
use App\Models\SyncEventMapping;
use App\Models\SyncRule;
use App\Models\SyncLog;
use App\Services\Sync\SyncEngine;
use App\Services\Email\ImipEmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Email Calendar Sync Service
 * 
 * Main service for processing incoming calendar emails
 * and creating blockers in target calendars
 */
class EmailCalendarSyncService
{
    public function __construct(
        private EmailParserService $emailParser,
        private IcsParserService $icsParser,
        private SyncEngine $syncEngine,
        private ImipEmailService $imipEmail
    ) {}

    /**
     * Process incoming email for a specific email calendar connection
     *
     * @param string $emailToken The unique token (e.g., "a7b2c9f4")
     * @param string $rawEmail Raw email content
     * @return array Processing results
     */
    public function processIncomingEmail(string $emailToken, string $rawEmail): array
    {
        $transactionId = Str::uuid()->toString();
        
        Log::info('Processing incoming email', [
            'email_token' => $emailToken,
            'transaction_id' => $transactionId,
        ]);

        // Find the email calendar connection
        $connection = EmailCalendarConnection::findByToken($emailToken);
        
        if (!$connection) {
            Log::warning('Email calendar connection not found', [
                'email_token' => $emailToken,
            ]);
            
            return [
                'success' => false,
                'ics_count' => 0,
                'events_processed' => 0,
                'error' => 'Unknown email address',
            ];
        }

        try {
            // Parse email
            $emailData = $this->emailParser->parseEmail($rawEmail);
            
            // Security check: sender must match target_email (source address)
            if ($emailData['from'] && strtolower($emailData['from']) !== strtolower($connection->target_email)) {
                Log::warning('Email from unauthorized sender', [
                    'from' => $emailData['from'],
                    'expected' => $connection->target_email,
                    'connection_id' => $connection->id,
                ]);
                
                return [
                    'success' => false,
                    'ics_count' => 0,
                    'events_processed' => 0,
                    'error' => 'Sender does not match source email',
                ];
            }

            // Update stats
            $connection->incrementEmailReceived();

            // Check for .ics attachments
            if (empty($emailData['ics_attachments'])) {
                Log::info('No .ics attachments found in email', [
                    'connection_id' => $connection->id,
                    'subject' => $emailData['subject'],
                ]);
                
                return [
                    'success' => true,
                    'message' => 'No calendar attachments found',
                    'ics_count' => 0,
                    'events_processed' => 0,
                ];
            }

            // Process each .ics attachment
            $totalEventsProcessed = 0;
            $icsCount = count($emailData['ics_attachments']);
            
            foreach ($emailData['ics_attachments'] as $icsContent) {
                $eventsProcessed = $this->processIcsAttachment($connection, $icsContent, $transactionId);
                $totalEventsProcessed += $eventsProcessed;
            }

            // Mark connection as active (clear any previous errors)
            $connection->markAsActive();

            Log::info('Email processed successfully', [
                'connection_id' => $connection->id,
                'ics_count' => $icsCount,
                'events_processed' => $totalEventsProcessed,
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => true,
                'ics_count' => $icsCount,
                'events_processed' => $totalEventsProcessed,
                'transaction_id' => $transactionId,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to process email', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'transaction_id' => $transactionId,
            ]);

            $connection->markAsError($e->getMessage());

            return [
                'success' => false,
                'ics_count' => 0,
                'events_processed' => 0,
                'error' => $e->getMessage(),
                'transaction_id' => $transactionId,
            ];
        }
    }

    /**
     * Process single .ics attachment
     */
    private function processIcsAttachment(EmailCalendarConnection $connection, string $icsContent, string $transactionId): int
    {
        Log::info('Processing .ics attachment', [
            'connection_id' => $connection->id,
            'ics_length' => strlen($icsContent),
            'ics_preview' => substr($icsContent, 0, 200),
        ]);
        
        // Parse .ics file
        try {
            $events = $this->icsParser->parseIcsFile($icsContent);
            
            Log::info('Parsed .ics file', [
                'connection_id' => $connection->id,
                'events_count' => count($events),
                'events' => array_map(function($e) {
                    return [
                        'uid' => $e['uid'] ?? 'no-uid',
                        'start' => isset($e['start']) ? $e['start']->format('Y-m-d H:i:s') : 'no-start',
                        'end' => isset($e['end']) ? $e['end']->format('Y-m-d H:i:s') : 'no-end',
                    ];
                }, $events),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse .ics file', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return 0;
        }
        
        if (empty($events)) {
            Log::warning('No events found in .ics file', [
                'connection_id' => $connection->id,
            ]);
            return 0;
        }

        $processedCount = 0;

        // Get all active sync rules for this specific email connection (as source)
        $syncRules = SyncRule::where('source_email_connection_id', $connection->id)
            ->where('is_active', true)
            ->with(['targets.targetConnection', 'targets.targetEmailConnection'])
            ->get();
        
        Log::info('Found sync rules for email calendar', [
            'connection_id' => $connection->id,
            'sync_rules_count' => $syncRules->count(),
            'rule_ids' => $syncRules->pluck('id')->toArray(),
        ]);
        
        if ($syncRules->isEmpty()) {
            Log::warning('No active sync rules found for this email calendar', [
                'connection_id' => $connection->id,
            ]);
            return 0;
        }
        
        foreach ($events as $eventData) {
            try {
                if ($this->icsParser->isCancellation($eventData)) {
                    // Handle event cancellation
                    $this->handleEventCancellation($connection, $eventData, $syncRules, $transactionId);
                } else {
                    // Handle event creation/update
                    $this->handleEventCreateOrUpdate($connection, $eventData, $syncRules, $transactionId);
                }
                
                $connection->incrementEventProcessed();
                $processedCount++;
                
            } catch (\Exception $e) {
                Log::error('Failed to process event', [
                    'uid' => $eventData['uid'],
                    'error' => $e->getMessage(),
                    'transaction_id' => $transactionId,
                ]);
            }
        }

        return $processedCount;
    }

    /**
     * Handle event creation or update
     */
    private function handleEventCreateOrUpdate(
        EmailCalendarConnection $connection,
        array $eventData,
        $syncRules,
        string $transactionId
    ): void {
        foreach ($syncRules as $rule) {
            // Apply filters (including time filter)
            if (!$rule->shouldSyncEvent($this->normalizeEventData($eventData))) {
                Log::info('Email event skipped due to filters', [
                    'connection_id' => $connection->id,
                    'event_uid' => $eventData['uid'],
                    'rule_id' => $rule->id,
                ]);
                continue;
            }
            
            foreach ($rule->targets as $target) {
                // Determine if target is API calendar or email calendar
                if ($target->isEmailTarget()) {
                    $this->handleEmailTarget($connection, $eventData, $rule, $target, $transactionId);
                } else {
                    $this->handleApiTarget($connection, $eventData, $rule, $target, $transactionId);
                }
            }
        }
    }
    
    /**
     * Normalize event data to match format expected by shouldSyncEvent
     */
    private function normalizeEventData(array $eventData): array
    {
        return [
            'start' => $eventData['start'],
            'end' => $eventData['end'],
            'isAllDay' => ($eventData['start']->format('H:i:s') === '00:00:00' && 
                          $eventData['end']->format('H:i:s') === '00:00:00'),
            'showAs' => 'busy', // Email events are always considered busy
            'busyStatus' => 'busy',
        ];
    }
    
    /**
     * Handle target that is a CalendarConnection (Google/Microsoft via API)
     */
    private function handleApiTarget(
        EmailCalendarConnection $connection,
        array $eventData,
        SyncRule $rule,
        $target,
        string $transactionId
    ): void {
        $targetConnection = $target->targetConnection;
        
        if (!$targetConnection || $targetConnection->status !== 'active') {
            return;
        }

        // Check if mapping already exists
        $mapping = SyncEventMapping::where([
            'sync_rule_id' => $rule->id,
            'email_connection_id' => $connection->id,
            'source_event_id' => 'email-' . $eventData['uid'],
            'target_connection_id' => $target->target_connection_id,
            'target_calendar_id' => $target->target_calendar_id,
        ])->first();

        // Get target calendar service
        $targetService = match($targetConnection->provider) {
            'google' => app(\App\Services\Calendar\GoogleCalendarService::class),
            'microsoft' => app(\App\Services\Calendar\MicrosoftCalendarService::class),
            'caldav', 'apple' => app(\App\Services\Calendar\CalDavCalendarService::class),
            default => throw new \Exception("Unsupported provider: {$targetConnection->provider}"),
        };
        
        $targetService->initializeWithConnection($targetConnection);

        if ($mapping) {
            // Update existing blocker
            try {
                $targetService->updateBlocker(
                    $target->target_calendar_id,
                    $mapping->target_event_id,
                    $rule->blocker_title,
                    $eventData['start'],
                    $eventData['end'],
                    $transactionId
                );
                
                // Update mapping timestamps
                $maxTimestamp = new \DateTime('2038-01-01');
                $mapping->update([
                    'event_start' => ($eventData['start'] && $eventData['start'] <= $maxTimestamp) ? $eventData['start'] : null,
                    'event_end' => ($eventData['end'] && $eventData['end'] <= $maxTimestamp) ? $eventData['end'] : null,
                ]);
                
                SyncLog::logSync(
                    $rule->user_id,
                    $rule->id,
                    'updated',
                    'email_to_api',
                    $eventData['uid'],
                    $mapping->target_event_id,
                    $eventData['start'],
                    $eventData['end'],
                    null,
                    $transactionId
                );
                
            } catch (\Exception $e) {
                Log::warning('Failed to update blocker, will create new one', [
                    'error' => $e->getMessage(),
                ]);
                $mapping->delete();
                $mapping = null;
            }
        }

        if (!$mapping) {
            // Create new blocker
            $blockerId = $targetService->createBlocker(
                $target->target_calendar_id,
                $rule->blocker_title,
                $eventData['start'],
                $eventData['end'],
                $transactionId
            );

            // Create mapping
            $maxTimestamp = new \DateTime('2038-01-01');
            
            SyncEventMapping::create([
                'sync_rule_id' => $rule->id,
                'source_type' => 'email',
                'email_connection_id' => $connection->id,
                'source_connection_id' => null,
                'source_calendar_id' => $connection->email_address,
                'source_event_id' => 'email-' . $eventData['uid'],
                'target_connection_id' => $target->target_connection_id,
                'target_calendar_id' => $target->target_calendar_id,
                'target_event_id' => $blockerId,
                'event_start' => ($eventData['start'] && $eventData['start'] <= $maxTimestamp) ? $eventData['start'] : null,
                'event_end' => ($eventData['end'] && $eventData['end'] <= $maxTimestamp) ? $eventData['end'] : null,
            ]);

            SyncLog::logSync(
                $rule->user_id,
                $rule->id,
                'created',
                'email_to_api',
                $eventData['uid'],
                $blockerId,
                $eventData['start'],
                $eventData['end'],
                null,
                $transactionId
            );
        }
    }
    
    /**
     * Handle target that is an EmailCalendarConnection (send iMIP email)
     */
    private function handleEmailTarget(
        EmailCalendarConnection $connection,
        array $eventData,
        SyncRule $rule,
        $target,
        string $transactionId
    ): void {
        $targetEmailConnection = $target->targetEmailConnection;
        
        if (!$targetEmailConnection || $targetEmailConnection->status !== 'active') {
            return;
        }
        
        if (!$targetEmailConnection->target_email) {
            Log::warning('Email calendar has no target_email configured', [
                'email_calendar_id' => $targetEmailConnection->id,
            ]);
            return;
        }

        // Check if mapping already exists
        $mapping = SyncEventMapping::where([
            'sync_rule_id' => $rule->id,
            'email_connection_id' => $connection->id,
            'source_event_id' => 'email-' . $eventData['uid'],
            'target_email_connection_id' => $target->target_email_connection_id,
        ])->first();
        
        // Generate a stable event UID for iMIP
        $eventUid = 'syncmyday-' . $connection->id . '-' . md5($eventData['uid']);
        $sequence = $mapping ? $mapping->sequence ?? 0 : 0;

        try {
            // Send iMIP email (REQUEST for create/update)
            $success = $this->imipEmail->sendBlockerInvitation(
                $targetEmailConnection,
                $targetEmailConnection->target_email,
                $eventUid,
                $rule->blocker_title,
                $eventData['start'],
                $eventData['end'],
                'REQUEST',
                $sequence + 1
            );

            if ($success) {
                $maxTimestamp = new \DateTime('2038-01-01');
                
                if ($mapping) {
                    // Update existing mapping
                    $mapping->update([
                        'event_start' => ($eventData['start'] && $eventData['start'] <= $maxTimestamp) ? $eventData['start'] : null,
                        'event_end' => ($eventData['end'] && $eventData['end'] <= $maxTimestamp) ? $eventData['end'] : null,
                        'sequence' => $sequence + 1,
                    ]);
                    
                    SyncLog::logSync(
                        $rule->user_id,
                        $rule->id,
                        'updated',
                        'email_to_email',
                        $eventData['uid'],
                        $eventUid,
                        $eventData['start'],
                        $eventData['end'],
                        null,
                        $transactionId
                    );
                } else {
                    // Create new mapping
                    SyncEventMapping::create([
                        'sync_rule_id' => $rule->id,
                        'source_type' => 'email',
                        'email_connection_id' => $connection->id,
                        'source_connection_id' => null,
                        'source_calendar_id' => $connection->email_address,
                        'source_event_id' => 'email-' . $eventData['uid'],
                        'target_email_connection_id' => $target->target_email_connection_id,
                        'target_calendar_id' => null,
                        'target_event_id' => $eventUid,
                        'event_start' => ($eventData['start'] && $eventData['start'] <= $maxTimestamp) ? $eventData['start'] : null,
                        'event_end' => ($eventData['end'] && $eventData['end'] <= $maxTimestamp) ? $eventData['end'] : null,
                        'sequence' => 1,
                    ]);

                    SyncLog::logSync(
                        $rule->user_id,
                        $rule->id,
                        'created',
                        'email_to_email',
                        $eventData['uid'],
                        $eventUid,
                        $eventData['start'],
                        $eventData['end'],
                        null,
                        $transactionId
                    );
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send iMIP email to target', [
                'target_email' => $targetEmailConnection->target_email,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle event cancellation
     */
    private function handleEventCancellation(
        EmailCalendarConnection $connection,
        array $eventData,
        $syncRules,
        string $transactionId
    ): void {
        // Find all mappings for this event
        $mappings = SyncEventMapping::where('email_connection_id', $connection->id)
            ->where('original_event_uid', $eventData['uid'])
            ->get();

        foreach ($mappings as $mapping) {
            $targetConnection = $mapping->targetConnection;
            
            if (!$targetConnection) {
                $mapping->delete();
                continue;
            }

            try {
                $targetService = match($targetConnection->provider) {
                    'google' => app(\App\Services\Calendar\GoogleCalendarService::class),
                    'microsoft' => app(\App\Services\Calendar\MicrosoftCalendarService::class),
                    'caldav', 'apple' => app(\App\Services\Calendar\CalDavCalendarService::class),
                    default => throw new \Exception("Unsupported provider: {$targetConnection->provider}"),
                };
                
                $targetService->initializeWithConnection($targetConnection);
                $targetService->deleteBlocker($mapping->target_calendar_id, $mapping->target_event_id);
                
                SyncLog::logSync(
                    $mapping->syncRule->user_id,
                    $mapping->sync_rule_id,
                    'deleted',
                    'email_to_target',
                    $eventData['uid'],
                    $mapping->target_event_id,
                    null,
                    null,
                    null,
                    $transactionId
                );
                
                $mapping->delete();
                
            } catch (\Exception $e) {
                Log::warning('Failed to delete blocker during cancellation', [
                    'mapping_id' => $mapping->id,
                    'error' => $e->getMessage(),
                ]);
                
                // Still delete mapping even if blocker deletion failed
                $mapping->delete();
            }
        }
    }
}

