<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

/**
 * Cache Helper with Fallback Support
 * 
 * Provides caching utilities that work with or without Redis.
 * Falls back gracefully to file cache on shared hosting.
 */
class CacheHelper
{
    /**
     * Cache webhook sync token with automatic fallback
     */
    public static function cacheWebhookToken(int $connectionId, string $calendarId, string $syncToken): void
    {
        $key = "webhook.token.{$connectionId}.{$calendarId}";
        
        try {
            Cache::put($key, $syncToken, now()->addHours(6));
        } catch (\Exception $e) {
            // Cache might not be available - silently fail
            // Token will be read from DB instead
        }
    }
    
    /**
     * Get cached webhook token
     */
    public static function getWebhookToken(int $connectionId, string $calendarId): ?string
    {
        $key = "webhook.token.{$connectionId}.{$calendarId}";
        
        try {
            return Cache::get($key);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Cache calendar events list for rate limit optimization
     */
    public static function cacheCalendarEvents(int $connectionId, string $calendarId, array $events, int $minutes = 5): void
    {
        $key = "calendar.events.{$connectionId}.{$calendarId}";
        
        try {
            Cache::put($key, $events, now()->addMinutes($minutes));
        } catch (\Exception $e) {
            // Silently fail
        }
    }
    
    /**
     * Get cached calendar events
     */
    public static function getCachedCalendarEvents(int $connectionId, string $calendarId): ?array
    {
        $key = "calendar.events.{$connectionId}.{$calendarId}";
        
        try {
            return Cache::get($key);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Cache user's active subscription status
     */
    public static function cacheUserSubscription(int $userId, bool $hasActiveSubscription): void
    {
        $key = "user.subscription.{$userId}";
        
        try {
            Cache::put($key, $hasActiveSubscription, now()->addMinutes(15));
        } catch (\Exception $e) {
            // Silently fail
        }
    }
    
    /**
     * Get cached user subscription status
     */
    public static function getUserSubscription(int $userId): ?bool
    {
        $key = "user.subscription.{$userId}";
        
        try {
            return Cache::get($key);
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Invalidate user subscription cache
     */
    public static function invalidateUserSubscription(int $userId): void
    {
        $key = "user.subscription.{$userId}";
        
        try {
            Cache::forget($key);
        } catch (\Exception $e) {
            // Silently fail
        }
    }
    
    /**
     * Cache rate limit counter
     * 
     * @param string $identifier Unique identifier (e.g., "google_api:connection_123")
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $decayMinutes Time window in minutes
     * @return bool True if within rate limit, false if exceeded
     */
    public static function checkRateLimit(string $identifier, int $maxAttempts, int $decayMinutes = 1): bool
    {
        $key = "rate_limit.{$identifier}";
        
        try {
            $attempts = Cache::get($key, 0);
            
            if ($attempts >= $maxAttempts) {
                return false; // Rate limit exceeded
            }
            
            // Increment counter
            if ($attempts == 0) {
                Cache::put($key, 1, now()->addMinutes($decayMinutes));
            } else {
                Cache::increment($key);
            }
            
            return true; // Within rate limit
            
        } catch (\Exception $e) {
            // If cache fails, allow request (fail open)
            return true;
        }
    }
    
    /**
     * Remember result with fallback to direct execution if cache fails
     */
    public static function remember(string $key, \DateTimeInterface|\DateInterval|int $ttl, \Closure $callback)
    {
        try {
            return Cache::remember($key, $ttl, $callback);
        } catch (\Exception $e) {
            // Cache failed - execute directly without caching
            return $callback();
        }
    }
    
    /**
     * Check if cache is available and working
     */
    public static function isCacheAvailable(): bool
    {
        try {
            $testKey = 'cache_test_' . time();
            Cache::put($testKey, 'test', 1);
            $result = Cache::get($testKey) === 'test';
            Cache::forget($testKey);
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get cache driver name
     */
    public static function getCacheDriver(): string
    {
        try {
            return config('cache.default', 'file');
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
    
    /**
     * Warm up cache for critical data
     * Call this after deployments or cache clears
     */
    public static function warmUpCache(): array
    {
        $warmed = [];
        
        try {
            // Warm up active users count
            $activeUsers = \App\Models\User::whereNotNull('email_verified_at')
                ->whereNull('deleted_at')
                ->count();
            Cache::put('stats.active_users', $activeUsers, now()->addHour());
            $warmed[] = 'active_users';
            
            // Warm up active sync rules count
            $activeSyncRules = \App\Models\SyncRule::where('is_active', true)->count();
            Cache::put('stats.active_sync_rules', $activeSyncRules, now()->addHour());
            $warmed[] = 'active_sync_rules';
            
            // Warm up providers stats
            $providersStats = \Illuminate\Support\Facades\DB::table('calendar_connections')
                ->select('provider', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->where('status', 'active')
                ->groupBy('provider')
                ->get();
            Cache::put('stats.providers', $providersStats, now()->addHour());
            $warmed[] = 'providers_stats';
            
        } catch (\Exception $e) {
            // Silently fail
        }
        
        return $warmed;
    }
    
    /**
     * Clear all application caches
     */
    public static function clearAllCache(): void
    {
        try {
            Cache::flush();
        } catch (\Exception $e) {
            // Try clearing specific keys if flush fails
            $patterns = [
                'webhook.token.*',
                'calendar.events.*',
                'user.subscription.*',
                'rate_limit.*',
                'stats.*',
                'health_metrics',
            ];
            
            foreach ($patterns as $pattern) {
                try {
                    // Note: This only works with some cache drivers
                    Cache::forget($pattern);
                } catch (\Exception $e2) {
                    // Continue
                }
            }
        }
    }
}

