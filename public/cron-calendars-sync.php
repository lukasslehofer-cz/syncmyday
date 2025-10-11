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
    
    // Get sync engine
    $syncEngine = app(\App\Services\Sync\SyncEngine::class);
    
    // Get active sync rules
    $rules = \App\Models\SyncRule::where('is_active', true)
        ->with(['sourceConnection', 'sourceEmailConnection', 'targets.targetConnection', 'targets.targetEmailConnection'])
        ->get();
    
    if ($rules->isEmpty()) {
        $output[] = 'No active sync rules found';
        
        $response = [
            'status' => 'success',
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
    
    $synced = 0;
    $errors = 0;
    
    foreach ($rules as $rule) {
        try {
            // Get source connection
            $sourceConnection = $rule->sourceConnection;
            
            if (!$sourceConnection) {
                $output[] = "Skipping rule #{$rule->id}: No source connection (might be email calendar)";
                continue;
            }
            
            if ($sourceConnection->status !== 'active') {
                $output[] = "Skipping rule #{$rule->id}: Connection not active";
                continue;
            }
            
            $output[] = "Syncing rule #{$rule->id}: {$sourceConnection->provider_email}";
            
            // Sync this rule with proper parameters
            $syncEngine->syncRule($rule, $sourceConnection);
            
            $synced++;
            $output[] = "  ✓ Synced successfully";
            
        } catch (\Exception $e) {
            $errors++;
            $output[] = "  ✗ Error: " . $e->getMessage();
            
            \Illuminate\Support\Facades\Log::error('Calendar sync failed for rule', [
                'rule_id' => $rule->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    $output[] = "Completed: {$synced} synced, {$errors} errors";
    
    $response = [
        'status' => 'success',
        'synced' => $synced,
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

