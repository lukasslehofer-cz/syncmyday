#!/usr/bin/env php
<?php

/**
 * Cron Job: Process Queue Jobs (Shared Hosting Compatible)
 * 
 * Processes queue jobs without using proc_open.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-queue.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-queue.php
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cronSecret = config('app.cron_secret');

if (php_sapi_name() !== 'cli') {
    $providedToken = $_GET['token'] ?? '';
    if (empty($cronSecret) || !hash_equals($cronSecret, $providedToken)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'time' => date('Y-m-d H:i:s')]);
        exit(1);
    }
    header('Content-Type: application/json');
}

try {
    $startTime = microtime(true);
    $maxTime = 240; // 4 minutes max execution time
    $maxJobs = 50; // Max 50 jobs per run
    
    $output = [];
    $output[] = '[' . date('Y-m-d H:i:s') . '] Processing queue jobs...';
    
    $queue = app('queue');
    $processed = 0;
    $failed = 0;
    
    // Process jobs until queue is empty or limits reached
    while ($processed < $maxJobs && (microtime(true) - $startTime) < $maxTime) {
        try {
            // Pop a job from the default queue
            $job = $queue->connection()->pop();
            
            if (!$job) {
                // No more jobs in queue
                break;
            }
            
            // Process the job
            $job->fire();
            
            // If job processed successfully, delete it
            if (!$job->isDeleted() && !$job->isReleased() && !$job->hasFailed()) {
                $job->delete();
            }
            
            $processed++;
            
        } catch (\Exception $e) {
            $failed++;
            $output[] = "✗ Job failed: " . $e->getMessage();
            
            \Illuminate\Support\Facades\Log::error('Queue job failed', [
                'error' => $e->getMessage(),
            ]);
            
            // Mark job as failed if it exists
            if (isset($job) && method_exists($job, 'fail')) {
                $job->fail($e);
            }
        }
    }
    
    if ($processed === 0) {
        $output[] = 'No jobs in queue';
    } else {
        $output[] = "Processed {$processed} job(s), {$failed} failed";
    }
    
    $response = [
        'status' => 'success',
        'processed' => $processed,
        'failed' => $failed,
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
    $error = 'Queue processing error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error($error, ['trace' => $e->getTraceAsString()]);
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}
