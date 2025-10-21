<?php

namespace App\Jobs;

use App\Models\CalendarConnection;
use App\Services\Sync\SyncEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Process Calendar Webhook Job
 * 
 * Triggered when a webhook notification is received from Google or Microsoft.
 * Fetches the changed events and syncs them according to the rules.
 */
class ProcessCalendarWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1min, 5min, 15min
    public $timeout = 90;

    private int $connectionId;
    private string $calendarId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $connectionId, string $calendarId)
    {
        $this->connectionId = $connectionId;
        $this->calendarId = $calendarId;
        $this->onQueue('webhooks');
    }

    /**
     * Execute the job.
     * 
     * Uses cache lock to prevent multiple syncs running simultaneously
     * for the same connection. This prevents:
     * - Race conditions when processing multiple webhooks
     * - Duplicate sync operations
     * - Resource exhaustion from overlapping syncs
     */
    public function handle(SyncEngine $syncEngine): void
    {
        $lockKey = "sync-lock-connection-{$this->connectionId}";
        $lockTtl = 120; // 2 minutes (longer than job timeout of 90s for safety)
        
        // Try to acquire lock (non-blocking)
        $lock = Cache::lock($lockKey, $lockTtl);
        
        if (!$lock->get()) {
            // Lock already held = sync is already running
            Log::channel('webhook')->info('Sync already running for connection, skipping duplicate job', [
                'connection_id' => $this->connectionId,
                'calendar_id' => $this->calendarId,
            ]);
            return;
        }

        // Lock acquired - proceed with sync
        try {
            Log::channel('webhook')->info('Processing webhook (lock acquired)', [
                'connection_id' => $this->connectionId,
                'calendar_id' => $this->calendarId,
            ]);

            $connection = CalendarConnection::find($this->connectionId);

            if (!$connection) {
                Log::channel('webhook')->warning('Connection not found', [
                    'connection_id' => $this->connectionId,
                ]);
                return;
            }

            if (!$connection->isHealthy()) {
                Log::channel('webhook')->warning('Connection not healthy', [
                    'connection_id' => $this->connectionId,
                    'status' => $connection->status,
                ]);
                return;
            }

            // Execute sync
            $syncEngine->syncConnection($connection);
            
            Log::channel('webhook')->info('Webhook processing completed successfully', [
                'connection_id' => $this->connectionId,
            ]);
            
        } catch (\Exception $e) {
            Log::channel('webhook')->error('Webhook processing failed', [
                'connection_id' => $this->connectionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Let queue handle retry
        } finally {
            // Always release lock, even if exception occurred
            $lock->release();
            
            Log::channel('webhook')->debug('Sync lock released', [
                'connection_id' => $this->connectionId,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('webhook')->error('Webhook job failed after all retries', [
            'connection_id' => $this->connectionId,
            'calendar_id' => $this->calendarId,
            'error' => $exception->getMessage(),
        ]);
    }
}

