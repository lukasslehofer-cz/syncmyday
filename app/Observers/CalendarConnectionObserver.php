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

        // Step 1: Delete all blockers created BY this connection (as SOURCE)
        $this->deleteBlockersFromSource($connection);

        // Step 2: Delete all blockers IN this connection (as TARGET)
        $this->deleteBlockersInTarget($connection);

        // Step 3: Clean up sync rules where this is the only/last target
        $this->cleanupOrphanedSyncRules($connection);

        // Mappings will be deleted automatically by cascade delete in DB
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
                        $service = $targetConnection->provider === 'google'
                            ? app(GoogleCalendarService::class)
                            : app(MicrosoftCalendarService::class);
                        
                        $service->initializeWithConnection($targetConnection);
                        $service->deleteBlocker(
                            $mapping->target_calendar_id,
                            $mapping->target_event_id
                        );
                        
                        $deletedCount++;
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
            $service = $connection->provider === 'google' 
                ? app(GoogleCalendarService::class) 
                : app(MicrosoftCalendarService::class);
            
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

