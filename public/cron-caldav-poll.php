<?php

/**
 * CalDAV/Apple Polling Cron Job
 * 
 * Polls all active CalDAV and Apple connections for changes and triggers synchronization.
 * This script is designed to be accessed via HTTP on shared hosting where
 * proc_open is disabled.
 * 
 * SECURITY:
 * - Uses CRON_SECRET token from .env
 * 
 * SETUP:
 * - Set up a cron job to call this URL every 5-15 minutes:
 *   https://yourdomain.com/cron-caldav-poll.php?token=YOUR_CRON_SECRET
 * 
 * FREQUENCY:
 * - Recommended: Every 5-10 minutes (CalDAV/Apple don't support webhooks)
 */

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Security check for CRON_SECRET
$cronSecret = config('app.cron_secret');
if (empty($cronSecret) || $cronSecret !== ($_GET['token'] ?? null)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden: Invalid or missing cron token.']);
    exit;
}

use App\Models\CalendarConnection;
use App\Services\Sync\SyncEngine;
use Illuminate\Support\Facades\Log;

$startTime = microtime(true);
$output = [];
$output[] = 'Starting CalDAV/Apple polling...';

try {
    // Get all active CalDAV and Apple connections (both use polling)
    $connections = CalendarConnection::whereIn('provider', ['caldav', 'apple'])
        ->where('status', 'active')
        ->get();
    
    if ($connections->isEmpty()) {
        $output[] = 'No active CalDAV/Apple connections to poll.';
        echo json_encode([
            'status' => 'success',
            'message' => 'No CalDAV connections',
            'output' => implode("\n", $output),
            'duration' => round(microtime(true) - $startTime, 2) . 's',
            'time' => date('Y-m-d H:i:s'),
        ]);
        exit;
    }
    
    $output[] = "Found {$connections->count()} CalDAV/Apple connection(s) to poll";
    
    $syncEngine = app(SyncEngine::class);
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($connections as $connection) {
        try {
            $output[] = "Polling: {$connection->account_email} (ID: {$connection->id})";
            
            Log::channel('sync')->info('Polling CalDAV/Apple connection', [
                'connection_id' => $connection->id,
                'provider' => $connection->provider,
                'email' => $connection->account_email,
            ]);
            
            // Trigger sync for this connection
            $syncEngine->syncConnection($connection);
            
            $output[] = "  ✓ Successfully polled {$connection->account_email}";
            $successCount++;
            
        } catch (\Exception $e) {
            $output[] = "  ✗ Failed to poll {$connection->account_email}: {$e->getMessage()}";
            $errorCount++;
            
            Log::channel('sync')->error('CalDAV/Apple polling failed', [
                'connection_id' => $connection->id,
                'provider' => $connection->provider,
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
    
    Log::error('CalDAV/Apple polling cron failed', [
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

