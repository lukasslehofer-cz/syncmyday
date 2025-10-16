#!/usr/bin/env php
<?php

/**
 * Cron Job: Clean Old Logs & Optimize Database (Shared Hosting Compatible)
 * 
 * Deletes sync logs older than retention period and performs database optimization.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-logs-clean.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-logs-clean.php
 * 
 * Schedule: Run daily at night (low traffic time)
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
    
    $output[] = '[' . date('Y-m-d H:i:s') . '] Starting database cleanup & optimization...';
    
    // ========================================================================
    // 1. SYNC LOGS CLEANUP (tiered retention)
    // ========================================================================
    
    // Delete normal logs older than 30 days
    $deletedNormalLogs = DB::table('sync_logs')
        ->where('action', '!=', 'error')
        ->where('created_at', '<', now()->subDays(30))
        ->delete();
    
    $output[] = "Deleted {$deletedNormalLogs} normal sync logs (>30 days)";
    
    // Delete error logs older than 90 days (keep longer for debugging)
    $deletedErrorLogs = DB::table('sync_logs')
        ->where('action', '=', 'error')
        ->where('created_at', '<', now()->subDays(90))
        ->delete();
    
    $output[] = "Deleted {$deletedErrorLogs} error logs (>90 days)";
    
    // Delete "skipped" logs older than 7 days (these are spammy)
    $deletedSkippedLogs = DB::table('sync_logs')
        ->where('action', '=', 'skipped')
        ->where('created_at', '<', now()->subDays(7))
        ->delete();
    
    $output[] = "Deleted {$deletedSkippedLogs} skipped logs (>7 days)";
    
    // ========================================================================
    // 2. OLD SESSIONS CLEANUP
    // ========================================================================
    
    $deletedSessions = DB::table('sessions')
        ->where('last_activity', '<', now()->subDays(7)->timestamp)
        ->delete();
    
    $output[] = "Deleted {$deletedSessions} old sessions (>7 days)";
    
    // ========================================================================
    // 3. ORPHANED MAPPINGS CLEANUP
    // ========================================================================
    
    // Delete mappings for events older than 6 months (outside sync range)
    $deletedOldMappings = DB::table('sync_event_mappings')
        ->where('event_start', '<', now()->subMonths(6))
        ->whereNotNull('event_start')
        ->delete();
    
    $output[] = "Deleted {$deletedOldMappings} old event mappings (>6 months)";
    
    // ========================================================================
    // 4. CACHE CLEANUP
    // ========================================================================
    
    try {
        $deletedCache = DB::table('cache')
            ->where('expiration', '<', now()->timestamp)
            ->delete();
        
        $output[] = "Deleted {$deletedCache} expired cache entries";
    } catch (\Exception $e) {
        // Cache table might not exist if using file cache
        $deletedCache = 0;
        $output[] = "Cache cleanup skipped (table not found)";
    }
    
    // ========================================================================
    // 5. FAILED JOBS CLEANUP (if using database queue)
    // ========================================================================
    
    $deletedFailedJobs = 0;
    try {
        $deletedFailedJobs = DB::table('failed_jobs')
            ->where('failed_at', '<', now()->subDays(30))
            ->delete();
        
        if ($deletedFailedJobs > 0) {
            $output[] = "Deleted {$deletedFailedJobs} old failed jobs (>30 days)";
        }
    } catch (\Exception $e) {
        // failed_jobs table might not exist
    }
    
    // ========================================================================
    // 6. DATABASE STATISTICS
    // ========================================================================
    
    $stats = [];
    
    // Count remaining records
    $stats['sync_logs'] = DB::table('sync_logs')->count();
    $stats['sync_event_mappings'] = DB::table('sync_event_mappings')->count();
    $stats['sync_rules'] = DB::table('sync_rules')->where('is_active', true)->count();
    $stats['users'] = DB::table('users')->whereNull('deleted_at')->count();
    
    // Table sizes
    try {
        $tableSizes = DB::select("
            SELECT 
                TABLE_NAME as 'table',
                ROUND(DATA_LENGTH / 1024 / 1024, 2) AS 'data_mb',
                ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS 'index_mb',
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'total_mb'
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME IN ('sync_logs', 'sync_event_mappings', 'calendar_connections')
            ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
        ");
        
        $output[] = "\n--- Database Statistics ---";
        foreach ($tableSizes as $tableSize) {
            $output[] = "  {$tableSize->table}: {$tableSize->total_mb} MB (data: {$tableSize->data_mb} MB, index: {$tableSize->index_mb} MB)";
        }
    } catch (\Exception $e) {
        // Might not have permission to query information_schema
    }
    
    $output[] = "\n--- Record Counts ---";
    $output[] = "  Active users: {$stats['users']}";
    $output[] = "  Active sync rules: {$stats['sync_rules']}";
    $output[] = "  Sync logs: {$stats['sync_logs']}";
    $output[] = "  Event mappings: {$stats['sync_event_mappings']}";
    
    // ========================================================================
    // 7. OPTIMIZATION (optional - can be slow on large tables)
    // ========================================================================
    
    // Only optimize tables once per week (check if it's Sunday)
    if (date('w') == 0) {
        $output[] = "\n--- Weekly Optimization ---";
        
        try {
            DB::statement('OPTIMIZE TABLE sync_logs');
            $output[] = "  Optimized: sync_logs";
        } catch (\Exception $e) {
            $output[] = "  Optimize failed (might need more privileges)";
        }
        
        try {
            DB::statement('OPTIMIZE TABLE sync_event_mappings');
            $output[] = "  Optimized: sync_event_mappings";
        } catch (\Exception $e) {
            // Might not have OPTIMIZE privilege on shared hosting
        }
    }
    
    $output[] = "\nCleanup completed successfully";
    
    $response = [
        'status' => 'success',
        'deleted' => [
            'normal_logs' => $deletedNormalLogs,
            'error_logs' => $deletedErrorLogs,
            'skipped_logs' => $deletedSkippedLogs,
            'sessions' => $deletedSessions,
            'old_mappings' => $deletedOldMappings,
            'cache' => $deletedCache,
            'failed_jobs' => $deletedFailedJobs,
        ],
        'stats' => $stats,
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
    $error = 'Log cleanup error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error($error, ['trace' => $e->getTraceAsString()]);
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'error' => $error, 'time' => date('Y-m-d H:i:s')], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}
