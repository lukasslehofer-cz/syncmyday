<?php

namespace App\Jobs;

use App\Models\CalendarConnection;
use App\Services\Sync\SyncEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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
     */
    public function handle(SyncEngine $syncEngine): void
    {
        Log::channel('webhook')->info('Processing webhook', [
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

        try {
            $syncEngine->syncConnection($connection);
        } catch (\Exception $e) {
            Log::channel('webhook')->error('Webhook processing failed', [
                'connection_id' => $this->connectionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e; // Let queue handle retry
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

