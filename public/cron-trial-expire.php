#!/usr/bin/env php
<?php

/**
 * Cron Job: Expire Trials (Shared Hosting Compatible)
 * 
 * Runs daily at 00:00 to expire trial periods.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-trial-expire.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-trial-expire.php
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
    $output[] = '[' . date('Y-m-d H:i:s') . '] Checking for expired trials...';
    
    // Find users in trial (pro tier) without Stripe subscription and expired subscription_ends_at
    $expiredTrials = \App\Models\User::where('subscription_tier', 'pro')
        ->whereNotNull('subscription_ends_at')
        ->where('subscription_ends_at', '<=', now())
        ->whereNull('stripe_subscription_id')
        ->get();

    $output[] = "Found {$expiredTrials->count()} expired trials";

    $expired = 0;
    foreach ($expiredTrials as $user) {
        try {
            $output[] = "Processing user: {$user->email} (trial ended: {$user->subscription_ends_at->format('Y-m-d')})";
            
            $user->expireTrial();
            
            \Illuminate\Support\Facades\Log::info('Trial expired and user downgraded', [
                'user_id' => $user->id,
                'email' => $user->email,
                'trial_ended_at' => $user->subscription_ends_at,
            ]);

            $output[] = "✓ Expired trial for: {$user->email}";
            $expired++;
            
        } catch (\Exception $e) {
            $output[] = "✗ Failed to expire trial for {$user->email}: {$e->getMessage()}";
            \Illuminate\Support\Facades\Log::error('Failed to expire trial', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    $output[] = "Finished! Total trials expired: {$expired}";
    
    $response = [
        'status' => 'success',
        'expired' => $expired,
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
    $error = 'Trial expiration error: ' . $e->getMessage();
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

