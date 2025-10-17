<?php

namespace App\Http\Controllers;

use App\Models\SyncLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show dashboard
     */
    public function index()
    {
        $user = auth()->user();

        $connections = $user->calendarConnections()
            ->orderBy('created_at', 'desc')
            ->get();
        
        $emailCalendars = $user->emailCalendarConnections()
            ->orderBy('created_at', 'desc')
            ->get();

        $syncRules = $user->syncRules()
            ->with(['sourceConnection', 'sourceEmailConnection', 'targets'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get stats for last 24 hours
        $recentSyncsCount = SyncLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->where('action', '!=', 'error')
            ->count();
        
        $recentErrorsCount = SyncLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->where('action', 'error')
            ->count();

        // Get last 20 logs for activity list
        $recentLogs = SyncLog::where('user_id', $user->id)
            ->with(['syncRule.sourceConnection', 'syncRule.sourceEmailConnection', 'syncRule.targets.targetConnection', 'syncRule.targets.targetEmailConnection'])
            ->recent(20)
            ->get();

        $stats = [
            'total_connections' => $connections->count() + $emailCalendars->count(),
            'active_connections' => $connections->where('status', 'active')->count() + $emailCalendars->where('status', 'active')->count(),
            'total_rules' => $syncRules->count(),
            'active_rules' => $syncRules->where('is_active', true)->count(),
            'recent_syncs' => $recentSyncsCount,
            'recent_errors' => $recentErrorsCount,
        ];

        return view('dashboard', compact('connections', 'emailCalendars', 'syncRules', 'recentLogs', 'stats'));
    }
}

