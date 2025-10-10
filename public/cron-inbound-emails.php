#!/usr/bin/env php
<?php

/**
 * Cron Job Runner for Inbound Email Processing
 * 
 * This script processes inbound calendar emails via IMAP polling.
 * It can be called directly from cron or via HTTP request.
 * 
 * Usage:
 * 1. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-inbound-emails.php
 * 2. Via HTTP: https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_CRON_SECRET
 * 
 * Security: Requires CRON_SECRET token to prevent unauthorized access
 */

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader (one level up from public/)
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel application (one level up from public/)
$app = require_once __DIR__.'/../bootstrap/app.php';

// Make kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Security check - require CRON_SECRET token
$cronSecret = env('CRON_SECRET');

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
    
    // Set content type for HTTP response
    header('Content-Type: application/json');
}

// Run the command
$output = new \Symfony\Component\Console\Output\BufferedOutput();
$status = $kernel->call('app:process-inbound-emails', [], $output);

// Get command output
$commandOutput = $output->fetch();

// Prepare response
$response = [
    'status' => $status === 0 ? 'success' : 'error',
    'exit_code' => $status,
    'output' => $commandOutput,
    'time' => date('Y-m-d H:i:s'),
];

// Output for logging
if (php_sapi_name() === 'cli') {
    echo "[" . date('Y-m-d H:i:s') . "] Inbound email processing completed with status: {$status}\n";
    echo $commandOutput . "\n";
} else {
    echo json_encode($response, JSON_PRETTY_PRINT);
}

// Exit with status code
exit($status);

