<?php

namespace App\Jobs;

use App\Models\CalendarConnection;
use App\Models\SyncRule;
use App\Services\Sync\SyncEngine;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Queue Job for Sync Rule Processing
 * 
 * Processes a single sync rule asynchronously to improve performance
 * and allow parallel processing of multiple rules.
 */
class SyncRuleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Delete the job if its models no longer exist.
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $syncRuleId,
        public int $connectionId
    ) {
        $this->onQueue('sync');
    }

    /**
     * Execute the job.
     */
    public function handle(SyncEngine $engine): void
    {
        $startTime = microtime(true);
        
        try {
            // Load models
            $rule = SyncRule::find($this->syncRuleId);
            $connection = CalendarConnection::find($this->connectionId);
            
            if (!$rule || !$connection) {
                Log::channel('sync')->warning('Sync job skipped - models not found', [
                    'rule_id' => $this->syncRuleId,
                    'connection_id' => $this->connectionId,
                ]);
                return;
            }
            
            // Check if rule is still active
            if (!$rule->is_active) {
                Log::channel('sync')->debug('Sync job skipped - rule not active', [
                    'rule_id' => $rule->id,
                ]);
                return;
            }
            
            // Check if connection is active
            if ($connection->status !== 'active') {
                Log::channel('sync')->debug('Sync job skipped - connection not active', [
                    'rule_id' => $rule->id,
                    'connection_id' => $connection->id,
                    'status' => $connection->status,
                ]);
                return;
            }
            
            // Perform the sync
            Log::channel('sync')->info('Starting sync job', [
                'rule_id' => $rule->id,
                'connection_id' => $connection->id,
                'user_id' => $rule->user_id,
            ]);
            
            $engine->syncRule($rule, $connection);
            
            // Calculate duration
            $durationMs = round((microtime(true) - $startTime) * 1000);
            
            // Update stats
            $rule->update([
                'queued_at' => null, // Clear queue flag
                'last_sync_duration_ms' => $durationMs,
                'sync_error_count' => 0, // Reset error count on success
            ]);
            
            $connection->update([
                'last_sync_duration_ms' => $durationMs,
                'sync_error_count' => 0,
            ]);
            
            Log::channel('sync')->info('Sync job completed', [
                'rule_id' => $rule->id,
                'duration_ms' => $durationMs,
            ]);
            
        } catch (\Exception $e) {
            $durationMs = round((microtime(true) - $startTime) * 1000);
            
            Log::channel('sync')->error('Sync job failed', [
                'rule_id' => $this->syncRuleId,
                'connection_id' => $this->connectionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'duration_ms' => $durationMs,
            ]);
            
            // Increment error count
            if ($rule = SyncRule::find($this->syncRuleId)) {
                $rule->increment('sync_error_count');
                
                // Disable rule if too many errors
                if ($rule->sync_error_count >= 10) {
                    $rule->update(['is_active' => false]);
                    Log::channel('sync')->error('Sync rule disabled due to too many errors', [
                        'rule_id' => $rule->id,
                        'user_id' => $rule->user_id,
                    ]);
                }
            }
            
            if ($connection = CalendarConnection::find($this->connectionId)) {
                $connection->increment('sync_error_count');
            }
            
            throw $e; // Re-throw to trigger retry logic
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::channel('sync')->error('Sync job permanently failed after all retries', [
            'rule_id' => $this->syncRuleId,
            'connection_id' => $this->connectionId,
            'error' => $exception->getMessage(),
        ]);
        
        // Clear queue flag so it can be retried in next cron run
        if ($rule = SyncRule::find($this->syncRuleId)) {
            $rule->update(['queued_at' => null]);
        }
    }
}

