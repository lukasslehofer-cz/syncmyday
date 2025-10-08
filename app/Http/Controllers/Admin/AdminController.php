<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CalendarConnection;
use App\Models\SyncRule;
use App\Models\SyncLog;
use App\Models\WebhookSubscription;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::has('calendarConnections')->count(),
            'pro_users' => User::where('subscription_tier', 'pro')->count(),
            'total_connections' => CalendarConnection::count(),
            'active_connections' => CalendarConnection::where('status', 'active')->count(),
            'google_connections' => CalendarConnection::where('provider', 'google')->count(),
            'microsoft_connections' => CalendarConnection::where('provider', 'microsoft')->count(),
            'total_rules' => SyncRule::count(),
            'active_rules' => SyncRule::where('is_active', true)->count(),
            'total_webhooks' => WebhookSubscription::count(),
            'active_webhooks' => WebhookSubscription::where('status', 'active')->count(),
            'recent_syncs' => SyncLog::where('created_at', '>', now()->subHours(24))->count(),
            'recent_errors' => SyncLog::where('action', 'error')
                ->where('created_at', '>', now()->subHours(24))
                ->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * List all users
     */
    public function users()
    {
        $users = User::withCount(['calendarConnections', 'syncRules'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.users', compact('users'));
    }

    /**
     * Show user details
     */
    public function userDetails(User $user)
    {
        $user->load(['calendarConnections', 'syncRules.targets', 'syncLogs']);

        return view('admin.user-details', compact('user'));
    }

    /**
     * List all connections
     */
    public function connections()
    {
        $connections = CalendarConnection::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.connections', compact('connections'));
    }

    /**
     * List webhook subscriptions
     */
    public function webhooks()
    {
        $webhooks = WebhookSubscription::with('calendarConnection.user')
            ->orderBy('expires_at', 'asc')
            ->paginate(50);

        return view('admin.webhooks', compact('webhooks'));
    }

    /**
     * View recent sync logs
     */
    public function logs(Request $request)
    {
        $query = SyncLog::with(['user', 'syncRule'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->has('action') && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->paginate(100);

        return view('admin.logs', compact('logs'));
    }

    /**
     * Health check endpoint
     */
    public function health()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
        ];

        $allHealthy = !in_array(false, $checks);

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now()->toIso8601String(),
        ], $allHealthy ? 200 : 503);
    }

    private function checkDatabase(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkRedis(): bool
    {
        try {
            \Redis::ping();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkQueue(): bool
    {
        try {
            // Check if there are any failed jobs
            $failedJobs = \DB::table('failed_jobs')
                ->where('failed_at', '>', now()->subHours(1))
                ->count();
            return $failedJobs === 0;
        } catch (\Exception $e) {
            return false;
        }
    }
}

