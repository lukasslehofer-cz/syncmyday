@extends('layouts.app')

@section('title', 'Dashboard - SyncMyDay')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
        <p class="mt-2 text-gray-600">Welcome back, {{ auth()->user()->name }}! Here's your sync status.</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Connections</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_connections'] }}/{{ $stats['total_connections'] }}</p>
            <p class="text-xs text-gray-500 mt-1">active</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Sync Rules</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_rules'] }}/{{ $stats['total_rules'] }}</p>
            <p class="text-xs text-gray-500 mt-1">active</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Recent Syncs</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['recent_syncs'] }}</p>
            <p class="text-xs text-gray-500 mt-1">last 20 actions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Errors</p>
            <p class="text-3xl font-bold {{ $stats['recent_errors'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['recent_errors'] }}</p>
            <p class="text-xs text-gray-500 mt-1">last 20 actions</p>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow mb-8 p-6">
        <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('oauth.google') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-xl">📅</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Add Google Calendar</p>
                </div>
            </a>
            
            <a href="{{ route('oauth.microsoft') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-xl">📆</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Add Microsoft Calendar</p>
                </div>
            </a>
            
            <a href="{{ route('sync-rules.create') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <span class="text-xl">➕</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Create Sync Rule</p>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Recent Activity</h2>
        
        @if($recentLogs->isEmpty())
        <p class="text-gray-500 text-center py-8">No recent activity. Create a sync rule to get started!</p>
        @else
        <div class="space-y-3">
            @foreach($recentLogs as $log)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    @if($log->action === 'created')
                    <span class="text-green-500 text-xl">✓</span>
                    @elseif($log->action === 'deleted')
                    <span class="text-gray-500 text-xl">✗</span>
                    @elseif($log->action === 'error')
                    <span class="text-red-500 text-xl">⚠</span>
                    @else
                    <span class="text-blue-500 text-xl">↻</span>
                    @endif
                    
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            @if($log->action === 'created')
                                Blocker created
                            @elseif($log->action === 'updated')
                                Blocker updated
                            @elseif($log->action === 'deleted')
                                Blocker removed
                            @elseif($log->action === 'skipped')
                                Event skipped
                            @elseif($log->action === 'error')
                                Sync failed
                            @else
                                {{ ucfirst($log->action) }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $log->created_at->diffForHumans() }}
                            @if($log->event_start)
                            • {{ $log->event_start->format('M j, H:i') }}
                            @endif
                            @if($log->action === 'error' && $log->error_message)
                            <br><span class="text-red-600">{{ Str::limit($log->error_message, 60) }}</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

