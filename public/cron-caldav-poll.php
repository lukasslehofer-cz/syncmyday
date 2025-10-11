<?php

/**
 * CalDAV Polling Cron Job
 * 
 * Polls all active CalDAV connections for changes and triggers synchronization.
 * This script is designed to be accessed via HTTP on shared hosting where
 * proc_open is disabled.
 * 
 * SECURITY:
 * - Uses bearer token authentication
 * - Token is defined in .env as CRON_SECURITY_TOKEN
 * 
 * SETUP:
 * - Set up a cron job to call this URL every 5-15 minutes:
 *   curl -H "Authorization: Bearer YOUR_TOKEN_HERE" https://yourdomain.com/cron-caldav-poll.php
 * 
 * FREQUENCY:
 * - Recommended: Every 5-10 minutes (CalDAV doesn't support webhooks)
 */

// Security check
$securityToken = $_ENV['CRON_SECURITY_TOKEN'] ?? getenv('CRON_SECURITY_TOKEN');
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

if (!$securityToken) {
    http_response_code(500);
    echo json_encode(['error' => 'Security token not configured']);
    exit;
}

if (!str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized - Bearer token required']);
    exit;
}

$providedToken = substr($authHeader, 7);
if ($providedToken !== $securityToken) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden - Invalid token']);
    exit;
}

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\CalendarConnection;
use App\Services\Sync\SyncEngine;
use Illuminate\Support\Facades\Log;

$startTime = microtime(true);
$output = [];
$output[] = 'Starting CalDAV polling...';

try {
    // Get all active CalDAV connections
    $connections = CalendarConnection::where('provider', 'caldav')
        ->where('status', 'active')
        ->get();
    
    if ($connections->isEmpty()) {
        $output[] = 'No active CalDAV connections to poll.';
        echo json_encode([
            'status' => 'success',
            'message' => 'No CalDAV connections',
            'output' => implode("\n", $output),
            'duration' => round(microtime(true) - $startTime, 2) . 's',
            'time' => date('Y-m-d H:i:s'),
        ]);
        exit;
    }
    
    $output[] = "Found {$connections->count()} CalDAV connection(s) to poll";
    
    $syncEngine = app(SyncEngine::class);
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($connections as $connection) {
        try {
            $output[] = "Polling: {$connection->account_email} (ID: {$connection->id})";
            
            Log::channel('sync')->info('Polling CalDAV connection', [
                'connection_id' => $connection->id,
                'email' => $connection->account_email,
            ]);
            
            // Trigger sync for this connection
            $syncEngine->syncConnection($connection);
            
            $output[] = "  ✓ Successfully polled {$connection->account_email}";
            $successCount++;
            
        } catch (\Exception $e) {
            $output[] = "  ✗ Failed to poll {$connection->account_email}: {$e->getMessage()}";
            $errorCount++;
            
            Log::channel('sync')->error('CalDAV polling failed', [
                'connection_id' => $connection->id,
                'email' => $connection->account_email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Update connection status
            $connection->update([
                'last_error' => $e->getMessage(),
            ]);
        }
    }
    
    $output[] = '';
    $output[] = "Polling complete: {$successCount} successful, {$errorCount} failed";
    
    $status = $errorCount === 0 ? 'success' : ($successCount > 0 ? 'partial' : 'error');
    
    echo json_encode([
        'status' => $status,
        'polled' => $connections->count(),
        'successful' => $successCount,
        'failed' => $errorCount,
        'output' => implode("\n", $output),
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'time' => date('Y-m-d H:i:s'),
    ]);
    
} catch (\Exception $e) {
    $output[] = 'ERROR: ' . $e->getMessage();
    
    Log::error('CalDAV polling cron failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage(),
        'output' => implode("\n", $output),
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'time' => date('Y-m-d H:i:s'),
    ]);
}

