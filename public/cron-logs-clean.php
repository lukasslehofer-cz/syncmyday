#!/usr/bin/env php
<?php

/**
 * Cron Job: Clean Old Logs (Shared Hosting Compatible)
 * 
 * Runs daily to clean up old sync logs (default: 30 days).
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-logs-clean.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-logs-clean.php
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
    $days = 30;
    
    $output = [];
    $output[] = '[' . date('Y-m-d H:i:s') . "] Cleaning up sync logs older than {$days} days...";
    
    $deleted = \App\Models\SyncLog::where('created_at', '<', now()->subDays($days))->delete();
    
    $output[] = "Deleted {$deleted} log entries";
    
    $response = [
        'status' => 'success',
        'deleted' => $deleted,
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
    $error = 'Logs cleanup error: ' . $e->getMessage();
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

