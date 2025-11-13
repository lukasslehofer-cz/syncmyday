<?php

namespace App\Observers;

use App\Models\CalendarConnection;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use App\Services\Calendar\CalDavCalendarService;
use Illuminate\Support\Facades\Log;

class CalendarConnectionObserver
{
    /**
     * Handle the CalendarConnection "deleting" event.
     * 
     * Clean up all blockers created by this connection (as target)
     * and delete sync rules if this connection is the source or last target
     */
    public function deleting(CalendarConnection $connection)
    {
        Log::info('Cleaning up before deleting connection', [
            'connection_id' => $connection->id,
            'provider' => $connection->provider,
            'email' => $connection->provider_email,
        ]);

        // Step 1: Stop all webhook subscriptions
        $this->stopWebhookSubscriptions($connection);

        // Step 2: Delete all blockers created BY this connection (as SOURCE)
        $this->deleteBlockersFromSource($connection);

        // Step 3: Delete all blockers IN this connection (as TARGET)
        $this->deleteBlockersInTarget($connection);

        // Step 4: Explicitly delete sync rules where this is the SOURCE
        // (Don't rely only on DB cascade - be explicit)
        $this->deleteSourceSyncRules($connection);

        // Step 5: Clean up sync rules where this is the only/last target
        $this->cleanupOrphanedSyncRules($connection);

        // Mappings will be deleted automatically by cascade delete in DB
    }
    
    /**
     * Stop all webhook subscriptions for this connection
     * CRITICAL: Prevents orphaned webhooks spamming logs after connection deletion
     */
    private function stopWebhookSubscriptions(CalendarConnection $connection)
    {
        $subscriptions = $connection->webhookSubscriptions()->get();
        
        if ($subscriptions->isEmpty()) {
            Log::info('No webhook subscriptions to stop');
            return;
        }
        
        Log::info("Stopping {$subscriptions->count()} webhook subscription(s)");
        
        $service = match($connection->provider) {
            'google' => app(GoogleCalendarService::class),
            'microsoft' => app(MicrosoftCalendarService::class),
            default => null,
        };
        
        if (!$service) {
            Log::warning('Cannot stop webhooks - provider not supported', [
                'provider' => $connection->provider,
            ]);
            return;
        }
        
        try {
            $service->initializeWithConnection($connection);
            
            $stoppedCount = 0;
            $errorCount = 0;
            
            foreach ($subscriptions as $subscription) {
                try {
                    if ($connection->provider === 'google') {
                        $service->stopWebhook(
                            $subscription->provider_subscription_id,
                            $subscription->resource_id
                        );
                    } else {
                        // Microsoft
                        $service->stopWebhook($subscription->provider_subscription_id);
                    }
                    
                    $stoppedCount++;
                    
                    Log::info('Webhook subscription stopped', [
                        'subscription_id' => $subscription->id,
                        'provider_subscription_id' => $subscription->provider_subscription_id,
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::warning('Failed to stop webhook subscription', [
                        'subscription_id' => $subscription->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            Log::info('Webhook subscriptions cleanup completed', [
                'stopped' => $stoppedCount,
                'errors' => $errorCount,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize service for webhook cleanup', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Delete all blockers that were created FROM this connection (as source)
     * These blockers exist in OTHER target calendars
     */
    private function deleteBlockersFromSource(CalendarConnection $connection)
    {
        // Find all mappings where this connection is the SOURCE
        $mappings = SyncEventMapping::where('source_connection_id', $connection->id)->get();
        
        if ($mappings->isEmpty()) {
            Log::info('No source blockers to clean up');
            return;
        }
        
        Log::info("Found {$mappings->count()} blocker(s) created from this connection");
        
        $deletedCount = 0;
        $errorCount = 0;
        
        foreach ($mappings as $mapping) {
            try {
                // Delete blocker from target calendar
                if ($mapping->target_connection_id) {
                    // API calendar target
                    $targetConnection = $mapping->targetConnection;
                    
                    if ($targetConnection && $targetConnection->status === 'active') {
                        $service = match($targetConnection->provider) {
                            'google' => app(GoogleCalendarService::class),
                            'microsoft' => app(MicrosoftCalendarService::class),
                            'caldav' => app(CalDavCalendarService::class),
                            default => null,
                        };
                        
                        if ($service) {
                            $service->initializeWithConnection($targetConnection);
                            $service->deleteBlocker(
                                $mapping->target_calendar_id,
                                $mapping->target_event_id
                            );
                            
                            $deletedCount++;
                        }
                    }
                } elseif ($mapping->target_email_connection_id) {
                    // Email calendar target - send CANCEL
                    $targetEmail = $mapping->targetEmailConnection;
                    
                    if ($targetEmail && $targetEmail->target_email) {
                        $imipService = app(\App\Services\Email\ImipEmailService::class);
                        
                        // Get event details from log or use defaults
                        $imipService->sendBlockerInvitation(
                            $targetEmail,
                            $targetEmail->target_email,
                            $mapping->target_event_id,
                            'Cancelled',
                            new \DateTime(), // Dummy dates for cancellation
                            new \DateTime(),
                            'CANCEL',
                            $mapping->sequence ?? 0
                        );
                        
                        $deletedCount++;
                    }
                }
                
                // Log the deletion
                \App\Models\SyncLog::create([
                    'user_id' => $connection->user_id,
                    'sync_rule_id' => $mapping->sync_rule_id,
                    'action' => 'deleted',
                    'source_event_id' => $mapping->source_event_id,
                    'target_event_id' => $mapping->target_event_id,
                ]);
                
                // Delete the mapping
                $mapping->delete();
                
            } catch (\Exception $e) {
                $errorCount++;
                Log::warning('Failed to delete source blocker', [
                    'mapping_id' => $mapping->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        Log::info('Source blockers cleanup completed', [
            'deleted' => $deletedCount,
            'errors' => $errorCount,
        ]);
    }
    
    /**
     * Delete all blockers IN this connection (as target)
     * These blockers were created by OTHER source calendars
     */
    private function deleteBlockersInTarget(CalendarConnection $connection)
    {
        // Find all mappings where this connection is a TARGET
        $mappings = SyncEventMapping::where('target_connection_id', $connection->id)->get();
        
        if ($mappings->isEmpty()) {
            Log::info('No target blockers to clean up');
            return;
        }

        Log::info("Found {$mappings->count()} blocker(s) in this connection");

        // Initialize service for this connection
        try {
            $service = match($connection->provider) {
                'google' => app(GoogleCalendarService::class),
                'microsoft' => app(MicrosoftCalendarService::class),
                'caldav' => app(CalDavCalendarService::class),
                default => null,
            };
            
            if (!$service) {
                Log::warning('Unknown provider for target cleanup', [
                    'provider' => $connection->provider,
                ]);
                return;
            }
            
            $service->initializeWithConnection($connection);
            
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($mappings as $mapping) {
                try {
                    // Delete the blocker in this calendar
                    $service->deleteBlocker(
                        $mapping->target_calendar_id,
                        $mapping->target_event_id
                    );
                    
                    $deletedCount++;
                    
                    Log::debug('Target blocker deleted', [
                        'mapping_id' => $mapping->id,
                        'target_event_id' => $mapping->target_event_id,
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::warning('Failed to delete target blocker', [
                        'mapping_id' => $mapping->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            Log::info('Target blockers cleanup completed', [
                'deleted' => $deletedCount,
                'errors' => $errorCount,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize service for target cleanup', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Handle the CalendarConnection "deleted" event.
     */
    public function deleted(CalendarConnection $connection)
    {
        Log::info('Calendar connection deleted', [
            'connection_id' => $connection->id,
            'provider' => $connection->provider,
        ]);
    }

    /**
     * Explicitly delete sync rules where this connection is the SOURCE
     * 
     * IMPORTANT: We don't rely only on DB cascade here because:
     * 1. Foreign key constraints might not be properly set up in all environments
     * 2. Being explicit prevents orphaned rules
     * 3. This triggers SyncRuleObserver which cleans up blockers properly
     */
    private function deleteSourceSyncRules(CalendarConnection $connection)
    {
        $sourceRules = \App\Models\SyncRule::where('source_connection_id', $connection->id)->get();
        
        if ($sourceRules->isEmpty()) {
            Log::info('No sync rules with this connection as source');
            return;
        }
        
        Log::info("Found {$sourceRules->count()} sync rule(s) with this connection as source - deleting explicitly", [
            'connection_id' => $connection->id,
            'rule_ids' => $sourceRules->pluck('id')->toArray(),
        ]);
        
        foreach ($sourceRules as $rule) {
            try {
                Log::info('Deleting sync rule - source connection removed', [
                    'rule_id' => $rule->id,
                    'source_connection_id' => $connection->id,
                    'user_id' => $rule->user_id,
                ]);
                
                // This will trigger SyncRuleObserver::deleting which cleans up blockers
                $rule->delete();
                
            } catch (\Exception $e) {
                Log::error('Failed to delete source sync rule', [
                    'rule_id' => $rule->id,
                    'connection_id' => $connection->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        Log::info('Source sync rules cleanup completed', [
            'connection_id' => $connection->id,
            'deleted_count' => $sourceRules->count(),
        ]);
    }

    /**
     * Clean up sync rules that would become orphaned after deleting this connection
     */
    private function cleanupOrphanedSyncRules(CalendarConnection $connection)
    {
        // Rules where this connection is a TARGET
        
        $targetRules = \App\Models\SyncRuleTarget::where('target_connection_id', $connection->id)
            ->with('syncRule')
            ->get();
        
        if ($targetRules->isEmpty()) {
            return;
        }
        
        Log::info("Found {$targetRules->count()} sync rule target(s) to review");
        
        foreach ($targetRules as $target) {
            $rule = $target->syncRule;
            
            if (!$rule) {
                continue;
            }
            
            // Count how many targets this rule has
            $targetCount = $rule->targets()->count();
            
            if ($targetCount <= 1) {
                // This is the only or last target - delete the entire rule
                Log::info('Deleting sync rule - removing last target', [
                    'rule_id' => $rule->id,
                    'target_connection_id' => $connection->id,
                ]);
                
                // The rule deletion will trigger SyncRuleObserver which will clean up blockers
                $rule->delete();
            } else {
                Log::info('Sync rule has multiple targets - keeping it', [
                    'rule_id' => $rule->id,
                    'target_count' => $targetCount,
                    'removing_target' => $connection->id,
                ]);
            }
        }
    }
}

