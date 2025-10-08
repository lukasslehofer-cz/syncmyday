@extends('layouts.app')

@section('title', 'Dashboard - SyncMyDay')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Dashboard</h1>
        <p class="text-lg text-gray-600">Welcome back, <span class="font-semibold text-gray-900">{{ auth()->user()->name }}</span>! Here's your sync status.</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Connections Card -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6 border border-blue-100 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-3 py-1 rounded-full">Active</span>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-2">Calendar Connections</p>
            <p class="text-4xl font-bold text-gray-900 mb-1">{{ $stats['active_connections'] }}<span class="text-xl text-gray-500">/{{ $stats['total_connections'] }}</span></p>
            <p class="text-xs text-gray-500">Connected calendars</p>
        </div>
        
        <!-- Sync Rules Card -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl shadow-lg p-6 border border-purple-100 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-purple-600 bg-purple-100 px-3 py-1 rounded-full">Rules</span>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-2">Active Sync Rules</p>
            <p class="text-4xl font-bold text-gray-900 mb-1">{{ $stats['active_rules'] }}<span class="text-xl text-gray-500">/{{ $stats['total_rules'] }}</span></p>
            <p class="text-xs text-gray-500">Configured sync rules</p>
        </div>
        
        <!-- Recent Syncs Card -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl shadow-lg p-6 border border-green-100 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-semibold text-green-600 bg-green-100 px-3 py-1 rounded-full">Success</span>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-2">Recent Syncs</p>
            <p class="text-4xl font-bold text-green-600 mb-1">{{ $stats['recent_syncs'] }}</p>
            <p class="text-xs text-gray-500">Last 20 sync actions</p>
        </div>
        
        <!-- Errors Card -->
        <div class="bg-gradient-to-br from-red-50 to-orange-50 rounded-2xl shadow-lg p-6 border border-red-100 hover:shadow-xl transition">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-red-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                @if($stats['recent_errors'] > 0)
                <span class="text-xs font-semibold text-red-600 bg-red-100 px-3 py-1 rounded-full">Alert</span>
                @else
                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">None</span>
                @endif
            </div>
            <p class="text-sm font-medium text-gray-600 mb-2">Recent Errors</p>
            <p class="text-4xl font-bold {{ $stats['recent_errors'] > 0 ? 'text-red-600' : 'text-gray-400' }} mb-1">{{ $stats['recent_errors'] }}</p>
            <p class="text-xs text-gray-500">Last 20 sync actions</p>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl shadow-lg mb-8 p-6 lg:p-8 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Quick Actions</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('oauth.google') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl hover:shadow-lg hover:scale-105 transition transform">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-1">Connect Google</p>
                        <p class="text-xs text-gray-600">Add Google Calendar</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('oauth.microsoft') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl hover:shadow-lg hover:scale-105 transition transform">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.5,0 L23,6.5 L23,17.5 L11.5,24 L0,17.5 L0,6.5 L11.5,0 Z M11.5,2.5 L2,7.5 L2,16.5 L11.5,21.5 L21,16.5 L21,7.5 L11.5,2.5 Z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-1">Connect Microsoft</p>
                        <p class="text-xs text-gray-600">Add Microsoft 365</p>
                    </div>
                </div>
            </a>
            
            <a href="{{ route('sync-rules.create') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl hover:shadow-lg hover:scale-105 transition transform">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-1">New Sync Rule</p>
                        <p class="text-xs text-gray-600">Create sync rule</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Activity</h2>
        
        @if($recentLogs->isEmpty())
        <div class="text-center py-12">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium mb-2">No recent activity</p>
            <p class="text-sm text-gray-400 mb-6">Create a sync rule to start synchronizing your calendars</p>
            <a href="{{ route('sync-rules.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-md transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Your First Rule
            </a>
        </div>
        @else
        <div class="space-y-3">
            @foreach($recentLogs as $log)
            <div class="relative overflow-hidden p-4 bg-gradient-to-r from-gray-50 to-white border border-gray-200 rounded-xl hover:shadow-md transition">
                <div class="flex items-center space-x-4">
                    @if($log->action === 'created')
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    @elseif($log->action === 'deleted')
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                    @elseif($log->action === 'error')
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-red-400 to-orange-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    @else
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-indigo-500 flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900">
                            @if($log->action === 'created')
                                Blocker Event Created
                            @elseif($log->action === 'updated')
                                Blocker Event Updated
                            @elseif($log->action === 'deleted')
                                Blocker Event Removed
                            @elseif($log->action === 'skipped')
                                Event Skipped
                            @elseif($log->action === 'error')
                                Sync Error Occurred
                            @else
                                {{ ucfirst($log->action) }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium">{{ $log->created_at->diffForHumans() }}</span>
                            @if($log->event_start)
                            • <span>{{ $log->event_start->format('M j, H:i') }}</span>
                            @endif
                        </p>
                        @if($log->action === 'error' && $log->error_message)
                        <p class="text-xs text-red-600 mt-2 font-medium">{{ Str::limit($log->error_message, 80) }}</p>
                        @endif
                    </div>
                    
                    <div class="flex-shrink-0">
                        @if($log->action === 'created')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Success</span>
                        @elseif($log->action === 'error')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">Error</span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Info</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection

