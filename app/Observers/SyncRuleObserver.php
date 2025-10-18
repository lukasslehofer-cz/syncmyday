<?php

namespace App\Observers;

use App\Models\SyncRule;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use App\Services\Calendar\CalDavCalendarService;
use Illuminate\Support\Facades\Log;

class SyncRuleObserver
{
    /**
     * Handle the SyncRule "deleting" event.
     * 
     * Clean up all blockers created by this sync rule AND its child rules.
     * This is important because CASCADE delete in DB doesn't trigger observer for child rules.
     */
    public function deleting(SyncRule $rule)
    {
        Log::info('Cleaning up blockers before deleting sync rule', [
            'rule_id' => $rule->id,
            'user_id' => $rule->user_id,
        ]);

        // IMPORTANT: If this is a main rule (parent_rule_id IS NULL), 
        // also clean up blockers for child rules (reverse rules)
        // because CASCADE delete won't trigger their observer
        // 
        // Note: We need to check in DB, not rely on $rule->parent_rule_id
        // because it might not be loaded
        $childRules = SyncRule::where('parent_rule_id', $rule->id)->get();
        
        if ($childRules->isNotEmpty()) {
            Log::info("Found {$childRules->count()} child rule(s), cleaning up their blockers first", [
                'main_rule_id' => $rule->id,
                'child_rule_ids' => $childRules->pluck('id')->toArray(),
            ]);
            
            foreach ($childRules as $childRule) {
                $this->cleanupBlockersForRule($childRule);
            }
        }

        // Clean up blockers for this rule
        $this->cleanupBlockersForRule($rule);
    }
    
    /**
     * Clean up all blockers for a specific rule
     */
    private function cleanupBlockersForRule(SyncRule $rule): void
    {
        Log::info('Cleaning up blockers for rule', [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
        ]);

        // Find all mappings for this rule
        $mappings = SyncEventMapping::where('sync_rule_id', $rule->id)
            ->with(['targetConnection', 'targetEmailConnection'])
            ->get();
        
        if ($mappings->isEmpty()) {
            Log::info('No blockers to clean up for this sync rule');
            return;
        }

        Log::info("Found {$mappings->count()} blocker(s) to delete");

        // Separate API calendar mappings from email mappings
        $apiMappings = $mappings->whereNotNull('target_connection_id');
        $emailMappings = $mappings->whereNotNull('target_email_connection_id');
        
        $deletedCount = 0;
        $errorCount = 0;
        
        // Process API calendar mappings (Google, Microsoft, Apple/CalDAV)
        $grouped = $apiMappings->groupBy('target_connection_id');
        
        foreach ($grouped as $connectionId => $connectionMappings) {
            $targetConnection = $connectionMappings->first()->targetConnection;
            
            if (!$targetConnection) {
                Log::warning('Target connection not found', [
                    'connection_id' => $connectionId,
                ]);
                continue;
            }
            
            try {
                // Initialize service for this target connection
                $service = match($targetConnection->provider) {
                    'google' => app(GoogleCalendarService::class),
                    'microsoft' => app(MicrosoftCalendarService::class),
                    'caldav', 'apple' => app(CalDavCalendarService::class),
                    default => throw new \Exception("Unsupported provider: {$targetConnection->provider}"),
                };
                
                $service->initializeWithConnection($targetConnection);
                
                foreach ($connectionMappings as $mapping) {
                    try {
                        // Delete the blocker in the calendar
                        $service->deleteBlocker(
                            $mapping->target_calendar_id,
                            $mapping->target_event_id
                        );
                        
                        $deletedCount++;
                        
                        Log::debug('Blocker deleted', [
                            'mapping_id' => $mapping->id,
                            'target_event_id' => $mapping->target_event_id,
                        ]);
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::warning('Failed to delete blocker during rule cleanup', [
                            'mapping_id' => $mapping->id,
                            'target_event_id' => $mapping->target_event_id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
                
            } catch (\Exception $e) {
                $errorCount += $connectionMappings->count();
                Log::error('Failed to initialize service for rule cleanup', [
                    'connection_id' => $connectionId,
                    'error' => $e->getMessage(),
                ]);
            }
        }
        
        // Process email calendar mappings (send CANCEL iMIP emails)
        if ($emailMappings->isNotEmpty()) {
            $imipService = app(\App\Services\Email\ImipEmailService::class);
            
            foreach ($emailMappings as $mapping) {
                $targetEmailConnection = $mapping->targetEmailConnection;
                
                if (!$targetEmailConnection || !$targetEmailConnection->target_email) {
                    $errorCount++;
                    Log::warning('Target email connection not found or invalid', [
                        'mapping_id' => $mapping->id,
                        'target_email_connection_id' => $mapping->target_email_connection_id,
                    ]);
                    continue;
                }
                
                try {
                    // Send CANCEL iMIP to remove blocker from email calendar
                    $imipService->sendBlockerInvitation(
                        $targetEmailConnection,
                        $targetEmailConnection->target_email,
                        $mapping->target_event_id,
                        'Cancelled',
                        new \DateTime(),
                        new \DateTime(),
                        'CANCEL',
                        $mapping->sequence ?? 0
                    );
                    
                    $deletedCount++;
                    
                    Log::debug('Email blocker cancelled', [
                        'mapping_id' => $mapping->id,
                        'target_email' => $targetEmailConnection->target_email,
                        'target_event_id' => $mapping->target_event_id,
                    ]);
                    
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::warning('Failed to cancel email blocker during rule cleanup', [
                        'mapping_id' => $mapping->id,
                        'target_email' => $targetEmailConnection->target_email,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        Log::info('Sync rule cleanup completed', [
            'rule_id' => $rule->id,
            'deleted' => $deletedCount,
            'errors' => $errorCount,
        ]);

        // Mappings will be deleted automatically by cascade delete in DB
    }

    /**
     * Handle the SyncRule "deleted" event.
     * 
     * Log the deletion to sync_logs for Recent Activity dashboard
     */
    public function deleted(SyncRule $rule)
    {
        Log::info('Sync rule deleted', [
            'rule_id' => $rule->id,
            'user_id' => $rule->user_id,
        ]);
        
        // Log to SyncLog for Recent Activity display
        // This creates a user-visible log entry that sync rule was deleted
        // Note: Using NULL for sync_rule_id because the rule was already deleted from DB
        // (foreign key constraint would fail if we used $rule->id here)
        \App\Models\SyncLog::logSync(
            $rule->user_id,
            null, // Rule already deleted, can't reference it
            'deleted',
            null,
            null,
            null,
            null,
            null,
            "Sync rule '{$rule->name}' was deleted and all blockers were removed"
        );
    }
}

