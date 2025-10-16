<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Performance Monitoring Helper
 * 
 * Provides methods to monitor system performance and health.
 * Used for dashboards, alerts, and optimization.
 */
class PerformanceMonitor
{
    /**
     * Get overall system health metrics
     */
    public static function getHealthMetrics(): array
    {
        return Cache::remember('health_metrics', now()->addMinutes(5), function () {
            return [
                'database' => self::getDatabaseMetrics(),
                'sync' => self::getSyncMetrics(),
                'queue' => self::getQueueMetrics(),
                'connections' => self::getConnectionMetrics(),
                'errors' => self::getErrorMetrics(),
            ];
        });
    }
    
    /**
     * Get database health and size metrics
     */
    public static function getDatabaseMetrics(): array
    {
        try {
            // Table sizes
            $tableSizes = DB::select("
                SELECT 
                    TABLE_NAME as name,
                    TABLE_ROWS as rows,
                    ROUND(DATA_LENGTH / 1024 / 1024, 2) AS data_mb,
                    ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS index_mb,
                    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS total_mb
                FROM information_schema.TABLES
                WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME IN (
                        'sync_logs', 
                        'sync_event_mappings', 
                        'calendar_connections',
                        'sync_rules',
                        'users'
                    )
                ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC
            ");
            
            $totalSize = array_sum(array_column($tableSizes, 'total_mb'));
            
            return [
                'total_size_mb' => round($totalSize, 2),
                'tables' => $tableSizes,
                'health' => $totalSize < 1000 ? 'good' : ($totalSize < 5000 ? 'warning' : 'critical'),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Could not retrieve database metrics',
                'health' => 'unknown',
            ];
        }
    }
    
    /**
     * Get sync performance metrics
     */
    public static function getSyncMetrics(): array
    {
        $now = now();
        
        // Active rules
        $activeRules = DB::table('sync_rules')
            ->where('is_active', true)
            ->count();
        
        // Syncs in last hour
        $syncsLastHour = DB::table('sync_logs')
            ->where('created_at', '>=', $now->copy()->subHour())
            ->count();
        
        // Errors in last hour
        $errorsLastHour = DB::table('sync_logs')
            ->where('created_at', '>=', $now->copy()->subHour())
            ->where('action', 'error')
            ->count();
        
        // Success rate
        $successRate = $syncsLastHour > 0 
            ? round((($syncsLastHour - $errorsLastHour) / $syncsLastHour) * 100, 1)
            : 100;
        
        // Average sync duration (from last 100 syncs)
        $avgDuration = DB::table('sync_rules')
            ->whereNotNull('last_sync_duration_ms')
            ->avg('last_sync_duration_ms');
        
        // Rules with high error count
        $problematicRules = DB::table('sync_rules')
            ->where('sync_error_count', '>', 5)
            ->where('is_active', true)
            ->count();
        
        return [
            'active_rules' => $activeRules,
            'syncs_last_hour' => $syncsLastHour,
            'errors_last_hour' => $errorsLastHour,
            'success_rate' => $successRate,
            'avg_duration_ms' => round($avgDuration ?? 0),
            'problematic_rules' => $problematicRules,
            'health' => $successRate > 95 ? 'good' : ($successRate > 85 ? 'warning' : 'critical'),
        ];
    }
    
    /**
     * Get queue metrics (if using queue)
     */
    public static function getQueueMetrics(): array
    {
        try {
            $queueDriver = config('queue.default');
            
            if ($queueDriver === 'database') {
                // Count pending jobs
                $pendingJobs = DB::table('jobs')
                    ->where('queue', 'sync')
                    ->count();
                
                // Count failed jobs
                $failedJobs = DB::table('failed_jobs')
                    ->count();
                
                // Oldest pending job
                $oldestJob = DB::table('jobs')
                    ->where('queue', 'sync')
                    ->orderBy('created_at', 'asc')
                    ->first();
                
                $oldestJobAge = $oldestJob 
                    ? now()->diffInMinutes($oldestJob->created_at)
                    : 0;
                
                return [
                    'driver' => $queueDriver,
                    'pending_jobs' => $pendingJobs,
                    'failed_jobs' => $failedJobs,
                    'oldest_job_age_minutes' => $oldestJobAge,
                    'health' => $oldestJobAge < 30 ? 'good' : ($oldestJobAge < 60 ? 'warning' : 'critical'),
                ];
            }
            
            return [
                'driver' => $queueDriver,
                'status' => 'Queue driver does not support metrics',
            ];
            
        } catch (\Exception $e) {
            return [
                'error' => 'Could not retrieve queue metrics',
            ];
        }
    }
    
    /**
     * Get calendar connection metrics
     */
    public static function getConnectionMetrics(): array
    {
        $totalConnections = DB::table('calendar_connections')->count();
        $activeConnections = DB::table('calendar_connections')
            ->where('status', 'active')
            ->count();
        $expiredConnections = DB::table('calendar_connections')
            ->where('status', 'expired')
            ->count();
        $errorConnections = DB::table('calendar_connections')
            ->where('status', 'error')
            ->count();
        
        // Connections by provider
        $byProvider = DB::table('calendar_connections')
            ->select('provider', DB::raw('count(*) as count'))
            ->where('status', 'active')
            ->groupBy('provider')
            ->get();
        
        // Email calendars
        $emailCalendars = DB::table('email_calendar_connections')
            ->where('status', 'active')
            ->count();
        
        return [
            'total' => $totalConnections,
            'active' => $activeConnections,
            'expired' => $expiredConnections,
            'error' => $errorConnections,
            'by_provider' => $byProvider,
            'email_calendars' => $emailCalendars,
            'health' => $errorConnections / max($totalConnections, 1) < 0.1 ? 'good' : 'warning',
        ];
    }
    
    /**
     * Get error metrics for alerting
     */
    public static function getErrorMetrics(): array
    {
        $now = now();
        
        // Errors by hour (last 24 hours)
        $errorsByHour = [];
        for ($i = 23; $i >= 0; $i--) {
            $hourStart = $now->copy()->subHours($i);
            $hourEnd = $hourStart->copy()->addHour();
            
            $count = DB::table('sync_logs')
                ->where('action', 'error')
                ->whereBetween('created_at', [$hourStart, $hourEnd])
                ->count();
            
            $errorsByHour[$hourStart->format('H:00')] = $count;
        }
        
        // Recent critical errors
        $recentErrors = DB::table('sync_logs')
            ->where('action', 'error')
            ->where('created_at', '>=', $now->copy()->subHour())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Most common errors (last 7 days)
        $commonErrors = DB::table('sync_logs')
            ->select('error_message', DB::raw('count(*) as count'))
            ->where('action', 'error')
            ->where('created_at', '>=', $now->copy()->subDays(7))
            ->whereNotNull('error_message')
            ->groupBy('error_message')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'by_hour' => $errorsByHour,
            'recent' => $recentErrors,
            'common' => $commonErrors,
        ];
    }
    
    /**
     * Check if system needs attention
     */
    public static function needsAttention(): bool
    {
        $metrics = self::getHealthMetrics();
        
        // Check for critical conditions
        $conditions = [
            $metrics['sync']['success_rate'] < 85,
            $metrics['sync']['errors_last_hour'] > 100,
            $metrics['connections']['error'] / max($metrics['connections']['total'], 1) > 0.2,
            isset($metrics['queue']['oldest_job_age_minutes']) && $metrics['queue']['oldest_job_age_minutes'] > 60,
        ];
        
        return in_array(true, $conditions);
    }
    
    /**
     * Log performance snapshot for historical tracking
     */
    public static function logPerformanceSnapshot(): void
    {
        $metrics = self::getHealthMetrics();
        
        Log::channel('sync')->info('Performance snapshot', [
            'sync_success_rate' => $metrics['sync']['success_rate'],
            'active_rules' => $metrics['sync']['active_rules'],
            'avg_duration_ms' => $metrics['sync']['avg_duration_ms'],
            'database_size_mb' => $metrics['database']['total_size_mb'] ?? 0,
            'active_connections' => $metrics['connections']['active'],
        ]);
    }
}

