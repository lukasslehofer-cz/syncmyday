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

        $syncRules = $user->syncRules()
            ->with(['sourceConnection', 'targets'])
            ->orderBy('created_at', 'desc')
            ->get();

        $recentLogs = SyncLog::where('user_id', $user->id)
            ->recent(20)
            ->get();

        $stats = [
            'total_connections' => $connections->count(),
            'active_connections' => $connections->where('status', 'active')->count(),
            'total_rules' => $syncRules->count(),
            'active_rules' => $syncRules->where('is_active', true)->count(),
            'recent_syncs' => $recentLogs->where('action', '!=', 'error')->count(),
            'recent_errors' => $recentLogs->where('action', 'error')->count(),
        ];

        return view('dashboard', compact('connections', 'syncRules', 'recentLogs', 'stats'));
    }
}

