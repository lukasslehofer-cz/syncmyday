<?php

namespace App\Observers;

use App\Models\EmailCalendarConnection;
use App\Models\SyncEventMapping;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Support\Facades\Log;

class EmailCalendarConnectionObserver
{
    /**
     * Handle the EmailCalendarConnection "deleting" event.
     * 
     * This is called before the model is deleted.
     * We delete all blockers created from this email calendar.
     */
    public function deleting(EmailCalendarConnection $connection)
    {
        Log::info('Cleaning up blockers for deleted email calendar', [
            'connection_id' => $connection->id,
            'name' => $connection->name,
        ]);
        
        // Find all event mappings where this email calendar is the source
        $mappings = SyncEventMapping::where('email_connection_id', $connection->id)->get();
        
        if ($mappings->isEmpty()) {
            Log::info('No blockers to clean up');
            return;
        }
        
        $deleted = 0;
        $errors = 0;
        
        foreach ($mappings as $mapping) {
            try {
                // Delete blocker from target calendar
                if ($mapping->target_connection_id) {
                    // API calendar target (Google/Microsoft)
                    $targetConnection = $mapping->targetConnection;
                    
                    if ($targetConnection && $targetConnection->status === 'active') {
                        if ($targetConnection->provider === 'google') {
                            $service = app(GoogleCalendarService::class);
                        } else {
                            $service = app(MicrosoftCalendarService::class);
                        }
                        
                        $service->initializeWithConnection($targetConnection);
                        
                        try {
                            $service->deleteBlocker(
                                $mapping->target_calendar_id,
                                $mapping->target_event_id
                            );
                            $deleted++;
                        } catch (\Exception $e) {
                            Log::warning('Failed to delete blocker', [
                                'mapping_id' => $mapping->id,
                                'target_event_id' => $mapping->target_event_id,
                                'error' => $e->getMessage(),
                            ]);
                            $errors++;
                        }
                    }
                } elseif ($mapping->target_email_connection_id) {
                    // Email calendar target - send CANCEL
                    $targetEmail = $mapping->targetEmailConnection;
                    
                    if ($targetEmail && $targetEmail->target_email) {
                        try {
                            $imipService = app(\App\Services\Email\ImipEmailService::class);
                            
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
                            $deleted++;
                        } catch (\Exception $e) {
                            Log::warning('Failed to send CANCEL to email target', [
                                'mapping_id' => $mapping->id,
                                'target_email' => $targetEmail->target_email,
                                'error' => $e->getMessage(),
                            ]);
                            $errors++;
                        }
                    }
                }
                
                // Log the deletion
                \App\Models\SyncLog::create([
                    'user_id' => $connection->user_id,
                    'sync_rule_id' => $mapping->sync_rule_id,
                    'action' => 'deleted',
                    'source_event_id' => $mapping->original_event_uid,
                    'target_event_id' => $mapping->target_event_id,
                ]);
                
                // Delete the mapping record
                $mapping->delete();
                
            } catch (\Exception $e) {
                Log::error('Error cleaning up mapping', [
                    'mapping_id' => $mapping->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }
        
        Log::info('Cleanup completed', [
            'deleted' => $deleted,
            'errors' => $errors,
        ]);
    }
}

