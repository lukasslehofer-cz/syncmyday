<?php

namespace App\Observers;

use App\Models\CalendarConnection;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
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

        // Step 1: Clean up sync rules where this is the only/last target
        $this->cleanupOrphanedSyncRules($connection);

        // Step 2: Find all mappings where this connection is a TARGET
        $mappings = SyncEventMapping::where('target_connection_id', $connection->id)->get();
        
        if ($mappings->isEmpty()) {
            Log::info('No blockers to clean up for this connection');
            return;
        }

        Log::info("Found {$mappings->count()} blocker(s) to delete");

        // Initialize service for this connection
        try {
            $service = $connection->provider === 'google' 
                ? app(GoogleCalendarService::class) 
                : app(MicrosoftCalendarService::class);
            
            $service->initializeWithConnection($connection);
            
            $deletedCount = 0;
            $errorCount = 0;
            
            foreach ($mappings as $mapping) {
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
                    Log::warning('Failed to delete blocker during connection cleanup', [
                        'mapping_id' => $mapping->id,
                        'target_event_id' => $mapping->target_event_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
            
            Log::info('Connection cleanup completed', [
                'connection_id' => $connection->id,
                'deleted' => $deletedCount,
                'errors' => $errorCount,
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to initialize service for connection cleanup', [
                'connection_id' => $connection->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Mappings will be deleted automatically by cascade delete in DB
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
     * Clean up sync rules that would become orphaned after deleting this connection
     */
    private function cleanupOrphanedSyncRules(CalendarConnection $connection)
    {
        // Rules where this connection is a SOURCE will be auto-deleted by cascade
        // But we need to check rules where this is a TARGET
        
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

