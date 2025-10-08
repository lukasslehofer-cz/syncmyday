<?php

namespace App\Observers;

use App\Models\SyncRule;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Support\Facades\Log;

class SyncRuleObserver
{
    /**
     * Handle the SyncRule "deleting" event.
     * 
     * Clean up all blockers created by this sync rule
     */
    public function deleting(SyncRule $rule)
    {
        Log::info('Cleaning up blockers before deleting sync rule', [
            'rule_id' => $rule->id,
            'user_id' => $rule->user_id,
        ]);

        // Find all mappings for this rule
        $mappings = SyncEventMapping::where('sync_rule_id', $rule->id)
            ->with('targetConnection')
            ->get();
        
        if ($mappings->isEmpty()) {
            Log::info('No blockers to clean up for this sync rule');
            return;
        }

        Log::info("Found {$mappings->count()} blocker(s) to delete");

        // Group by target connection to minimize service initializations
        $grouped = $mappings->groupBy('target_connection_id');
        
        $deletedCount = 0;
        $errorCount = 0;
        
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
                $service = $targetConnection->provider === 'google' 
                    ? app(GoogleCalendarService::class) 
                    : app(MicrosoftCalendarService::class);
                
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
        
        Log::info('Sync rule cleanup completed', [
            'rule_id' => $rule->id,
            'deleted' => $deletedCount,
            'errors' => $errorCount,
        ]);

        // Mappings will be deleted automatically by cascade delete in DB
    }

    /**
     * Handle the SyncRule "deleted" event.
     */
    public function deleted(SyncRule $rule)
    {
        Log::info('Sync rule deleted', [
            'rule_id' => $rule->id,
            'user_id' => $rule->user_id,
        ]);
    }
}

