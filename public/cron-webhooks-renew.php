#!/usr/bin/env php
<?php

/**
 * Cron Job: Renew Webhook Subscriptions (Shared Hosting Compatible)
 * 
 * Runs every 6 hours to renew expiring webhook subscriptions.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-webhooks-renew.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-webhooks-renew.php
 * 
 * Security: Requires CRON_SECRET token to prevent unauthorized access
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
    
    $output[] = '[' . date('Y-m-d H:i:s') . '] Renewing webhook subscriptions...';
    
    // Execute the job directly
    $job = new \App\Jobs\RenewWebhookSubscriptionsJob();
    $job->handle(
        app(\App\Services\Calendar\GoogleCalendarService::class),
        app(\App\Services\Calendar\MicrosoftCalendarService::class)
    );
    
    $output[] = 'Webhook renewal completed';
    
    $response = [
        'status' => 'success',
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
    $error = 'Webhook renewal error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error($error, ['trace' => $e->getTraceAsString()]);
    
    $response = ['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')];
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

