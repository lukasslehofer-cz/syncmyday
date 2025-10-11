<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "deleting" event.
     * 
     * Clean up all user data before account deletion:
     * - Delete all calendar connections (triggers blocker cleanup via CalendarConnectionObserver)
     * - Delete all email calendar connections (triggers blocker cleanup via EmailCalendarConnectionObserver)
     * - Sync rules and mappings will be cascade deleted by DB foreign keys
     */
    public function deleting(User $user): void
    {
        Log::info('Cleaning up user account before deletion', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        // Delete all API calendar connections
        // Each delete() will trigger CalendarConnectionObserver::deleting()
        // which will delete all blockers from source and target calendars
        $apiConnections = $user->calendarConnections()->get();
        $apiConnectionCount = $apiConnections->count();
        
        if ($apiConnectionCount > 0) {
            Log::info("Deleting {$apiConnectionCount} API calendar connection(s)", [
                'user_id' => $user->id,
            ]);
            
            foreach ($apiConnections as $connection) {
                try {
                    $connection->delete();
                } catch (\Exception $e) {
                    Log::error('Failed to delete API calendar connection', [
                        'connection_id' => $connection->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Delete all email calendar connections
        // Each delete() will trigger EmailCalendarConnectionObserver::deleting()
        // which will delete all blockers sent to those email addresses
        $emailConnections = $user->emailCalendarConnections()->get();
        $emailConnectionCount = $emailConnections->count();
        
        if ($emailConnectionCount > 0) {
            Log::info("Deleting {$emailConnectionCount} email calendar connection(s)", [
                'user_id' => $user->id,
            ]);
            
            foreach ($emailConnections as $connection) {
                try {
                    $connection->delete();
                } catch (\Exception $e) {
                    Log::error('Failed to delete email calendar connection', [
                        'connection_id' => $connection->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        Log::info('User cleanup completed', [
            'user_id' => $user->id,
            'api_connections_deleted' => $apiConnectionCount,
            'email_connections_deleted' => $emailConnectionCount,
        ]);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Log::info('User account deleted', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Handle the User "force deleted" event.
     * 
     * This is called when a soft-deleted user is permanently deleted.
     */
    public function forceDeleted(User $user): void
    {
        Log::info('User account permanently deleted', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }
}
