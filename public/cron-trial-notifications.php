#!/usr/bin/env php
<?php

/**
 * Cron Job: Send Trial Ending Notifications (Shared Hosting Compatible)
 * 
 * Runs daily at 9:00 to send trial ending notifications.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-trial-notifications.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-trial-notifications.php
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
    $output[] = '[' . date('Y-m-d H:i:s') . '] Checking for users with trial ending soon...';
    
    // Users with trial ending in 3 days
    $usersEndingIn3Days = \App\Models\User::where('subscription_tier', 'pro')
        ->whereNotNull('subscription_ends_at')
        ->whereDate('subscription_ends_at', now()->addDays(3)->toDateString())
        ->whereNull('stripe_subscription_id')
        ->get();

    $output[] = "Found {$usersEndingIn3Days->count()} users with trial ending in 3 days";

    $sent3Days = 0;
    foreach ($usersEndingIn3Days as $user) {
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TrialEndingInSevenDaysMail($user));
            $output[] = "✓ Sent 3-day notification to: {$user->email}";
            $sent3Days++;
        } catch (\Exception $e) {
            $output[] = "✗ Failed to send to {$user->email}: {$e->getMessage()}";
        }
    }

    // Users with trial ending in 1 day
    $usersEndingIn1Day = \App\Models\User::where('subscription_tier', 'pro')
        ->whereNotNull('subscription_ends_at')
        ->whereDate('subscription_ends_at', now()->addDay()->toDateString())
        ->whereNull('stripe_subscription_id')
        ->get();

    $output[] = "Found {$usersEndingIn1Day->count()} users with trial ending in 1 day";

    $sent1Day = 0;
    foreach ($usersEndingIn1Day as $user) {
        try {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\TrialEndingTomorrowMail($user));
            $output[] = "✓ Sent 1-day notification to: {$user->email}";
            $sent1Day++;
        } catch (\Exception $e) {
            $output[] = "✗ Failed to send to {$user->email}: {$e->getMessage()}";
        }
    }

    $totalSent = $sent3Days + $sent1Day;
    $output[] = "Finished! Total notifications sent: {$totalSent}";
    
    $response = [
        'status' => 'success',
        'sent' => $totalSent,
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
    $error = 'Trial notifications error: ' . $e->getMessage();
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

