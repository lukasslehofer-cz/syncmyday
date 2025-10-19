#!/usr/bin/env php
<?php

/**
 * Cron Job: Send Renewal Reminders (Shared Hosting Compatible)
 * 
 * Runs daily at 9:00 to send renewal reminder notifications 3 days before subscription renewal.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-renewal-reminders.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-renewal-reminders.php
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
    $output[] = '[' . date('Y-m-d H:i:s') . '] Checking for subscriptions renewing in 3 days...';
    
    // Find users with active paid subscriptions renewing in 3 days
    $threeDaysFromNow = now()->addDays(3);
    
    $users = \App\Models\User::where('subscription_tier', 'pro')
        ->whereNotNull('stripe_subscription_id') // Has active Stripe subscription
        ->whereDate('subscription_ends_at', $threeDaysFromNow->toDateString())
        ->get();

    $output[] = "Found {$users->count()} subscription(s) renewing in 3 days";

    $sent = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($users as $user) {
        try {
            $output[] = "Processing user: {$user->email} (renewal date: {$user->subscription_ends_at->format('Y-m-d')})";
            
            // Get subscription details from Stripe
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
            
            // Skip if subscription is in trial (should not happen with our query, but safety check)
            if ($subscription->status === 'trialing') {
                $output[] = "⚠ Skipping user {$user->email} - still in trial";
                $skipped++;
                continue;
            }

            // Get amount and currency from subscription
            $amount = ($subscription->plan->amount ?? 0) / 100;
            $currency = strtoupper($subscription->plan->currency ?? $user->stripe_currency ?? 'EUR');
            $renewalDate = $user->subscription_ends_at->format('d.m.Y');

            // Send email
            \Illuminate\Support\Facades\Mail::to($user->email)->send(
                new \App\Mail\RenewalReminderMail($user, $amount, $currency, $renewalDate)
            );

            \Illuminate\Support\Facades\Log::info('Renewal reminder email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
                'renewal_date' => $renewalDate,
                'amount' => $amount,
                'currency' => $currency,
            ]);

            $output[] = "✓ Sent renewal reminder to: {$user->email}";
            $sent++;

        } catch (\Exception $e) {
            $output[] = "✗ Failed to send to {$user->email}: {$e->getMessage()}";
            \Illuminate\Support\Facades\Log::error('Failed to send renewal reminder email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            $errors++;
        }
    }

    $output[] = "Finished! Sent: {$sent}, Skipped: {$skipped}, Errors: {$errors}";
    
    $response = [
        'status' => 'success',
        'sent' => $sent,
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
    $error = 'Renewal reminders error: ' . $e->getMessage();
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

