#!/usr/bin/env php
<?php

/**
 * Cron Job: Sync Calendars (Shared Hosting Compatible)
 * 
 * Runs every 5 minutes to sync calendars according to active sync rules.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-calendars-sync.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-calendars-sync.php
 * 
 * Security: Requires CRON_SECRET token to prevent unauthorized access
 */

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel application
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Security check - require CRON_SECRET token
$cronSecret = config('app.cron_secret');

// If accessed via HTTP, verify token
if (php_sapi_name() !== 'cli') {
    $providedToken = $_GET['token'] ?? '';
    
    if (empty($cronSecret)) {
        http_response_code(503);
        echo json_encode([
            'error' => 'CRON_SECRET not configured',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
    
    if (!hash_equals($cronSecret, $providedToken)) {
        http_response_code(401);
        echo json_encode([
            'error' => 'Unauthorized - invalid token',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
    
    header('Content-Type: application/json');
}

// Execute the sync command directly
try {
    $startTime = microtime(true);
    $output = [];
    
    $output[] = '[' . date('Y-m-d H:i:s') . '] Starting calendar synchronization...';
    
    // Check if queue is available (for VPS/dedicated servers)
    $queueDriver = config('queue.default');
    $useQueue = in_array($queueDriver, ['database', 'redis', 'beanstalkd', 'sqs']);
    
    if ($useQueue) {
        $output[] = "Using queue driver: {$queueDriver}";
    } else {
        $output[] = "Using synchronous processing (queue not available)";
    }
    
    // OPTIMIZATION: Get only rules that need syncing
    // Skip recently queued rules (processed in last 10 minutes)
    $rules = \App\Models\SyncRule::where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('queued_at')
              ->orWhere('queued_at', '<', now()->subMinutes(10));
        })
        ->with(['sourceConnection'])
        ->orderBy('queue_priority', 'desc') // High priority first
        ->orderBy('last_triggered_at', 'asc') // Oldest first
        ->limit($useQueue ? 200 : 50) // Process more with queue, less without
        ->get();
    
    if ($rules->isEmpty()) {
        $output[] = 'No sync rules need processing';
        
        $response = [
            'status' => 'success',
            'queued' => 0,
            'synced' => 0,
            'output' => implode("\n", $output),
            'duration' => round(microtime(true) - $startTime, 2) . 's',
            'time' => date('Y-m-d H:i:s'),
        ];
        
        if (php_sapi_name() === 'cli') {
            echo implode("\n", $output) . "\n";
        } else {
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
        exit(0);
    }
    
    $queued = 0;
    $synced = 0;
    $skipped = 0;
    $errors = 0;
    
    foreach ($rules as $rule) {
        try {
            // Get source connection
            $sourceConnection = $rule->sourceConnection;
            
            if (!$sourceConnection) {
                $skipped++;
                continue; // Email calendar - will be processed separately
            }
            
            if ($sourceConnection->status !== 'active') {
                $skipped++;
                continue;
            }
            
            if ($useQueue) {
                // QUEUE MODE: Dispatch job for async processing
                $rule->update(['queued_at' => now()]);
                
                \App\Jobs\SyncRuleJob::dispatch($rule->id, $sourceConnection->id)
                    ->onQueue('sync');
                
                $queued++;
                
                // Log only first few to avoid spam
                if ($queued <= 5) {
                    $output[] = "Queued rule #{$rule->id}: {$sourceConnection->provider_email}";
                }
            } else {
                // SYNC MODE: Process immediately (for shared hosting)
                $syncEngine = app(\App\Services\Sync\SyncEngine::class);
                $syncEngine->syncRule($rule, $sourceConnection);
                
                $synced++;
                
                if ($synced <= 5) {
                    $output[] = "Synced rule #{$rule->id}: {$sourceConnection->provider_email}";
                }
            }
            
        } catch (\Exception $e) {
            $errors++;
            
            if ($errors <= 3) {
                $output[] = "  ✗ Error (rule #{$rule->id}): " . $e->getMessage();
            }
            
            \Illuminate\Support\Facades\Log::error('Calendar sync failed for rule', [
                'rule_id' => $rule->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    // Summary output
    if ($useQueue) {
        if ($queued > 5) {
            $output[] = "... and " . ($queued - 5) . " more rules queued";
        }
        $output[] = "Completed: {$queued} queued, {$skipped} skipped, {$errors} errors";
    } else {
        if ($synced > 5) {
            $output[] = "... and " . ($synced - 5) . " more rules synced";
        }
        $output[] = "Completed: {$synced} synced, {$skipped} skipped, {$errors} errors";
    }
    
    $response = [
        'status' => 'success',
        'mode' => $useQueue ? 'queue' : 'sync',
        'queued' => $queued,
        'synced' => $synced,
        'skipped' => $skipped,
        'errors' => $errors,
        'output' => implode("\n", $output),
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo implode("\n", $output) . "\n";
    } else {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit(0);
    
} catch (\Exception $e) {
    $error = 'Calendar sync error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error('Calendar sync error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    $response = [
        'status' => 'error',
        'error' => $error,
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

