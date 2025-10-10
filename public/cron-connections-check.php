#!/usr/bin/env php
<?php

/**
 * Cron Job: Check Calendar Connections (Shared Hosting Compatible)
 * 
 * Runs hourly to check calendar connections health.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-connections-check.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-connections-check.php
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
    
    $output = [];
    $output[] = '[' . date('Y-m-d H:i:s') . '] Checking calendar connections...';
    
    $connections = \App\Models\CalendarConnection::where('status', 'active')->get();
    $issuesFound = 0;

    foreach ($connections as $connection) {
        if ($connection->isTokenExpired() && !$connection->getRefreshToken()) {
            $connection->update(['status' => 'expired']);
            $issuesFound++;

            \Illuminate\Support\Facades\Log::warning('Connection expired without refresh token', [
                'connection_id' => $connection->id,
                'user_id' => $connection->user_id,
            ]);
            
            $output[] = "⚠ Connection expired: {$connection->account_email}";
        }
    }

    if ($issuesFound > 0) {
        $output[] = "Found {$issuesFound} connection(s) with issues";
    } else {
        $output[] = "All connections healthy";
    }
    
    $response = [
        'status' => 'success',
        'checked' => $connections->count(),
        'issues' => $issuesFound,
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
    $error = 'Connections check error: ' . $e->getMessage();
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

