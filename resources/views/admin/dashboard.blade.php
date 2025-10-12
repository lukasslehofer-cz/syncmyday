@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">System overview and monitoring</p>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Total Users</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-green-600 mt-1">+{{ $stats['active_users'] }} active</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Pro Subscribers</p>
            <p class="text-3xl font-bold text-indigo-600">{{ $stats['pro_users'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['total_users'] > 0 ? round(($stats['pro_users']/$stats['total_users'])*100) : 0 }}% conversion</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Connections</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_connections'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['google_connections'] }} Google, {{ $stats['microsoft_connections'] }} MS</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Sync Rules</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_rules'] }}</p>
            <p class="text-xs text-gray-500 mt-1">of {{ $stats['total_rules'] }} total</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Webhooks</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_webhooks'] }}</p>
            <p class="text-xs text-gray-500 mt-1">active subscriptions</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Syncs (24h)</p>
            <p class="text-3xl font-bold text-green-600">{{ $stats['recent_syncs'] }}</p>
            <p class="text-xs text-gray-500 mt-1">last 24 hours</p>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500 mb-1">Errors (24h)</p>
            <p class="text-3xl font-bold {{ $stats['recent_errors'] > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $stats['recent_errors'] }}</p>
            <p class="text-xs text-gray-500 mt-1">last 24 hours</p>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="grid md:grid-cols-3 gap-4">
        <a href="{{ route('admin.users') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50">
            <h3 class="font-semibold text-gray-900 mb-2">Users</h3>
            <p class="text-sm text-gray-600">View and manage users</p>
        </a>
        
        <a href="{{ route('admin.connections') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50">
            <h3 class="font-semibold text-gray-900 mb-2">Connections</h3>
            <p class="text-sm text-gray-600">Monitor calendar connections</p>
        </a>
        
        <a href="{{ route('admin.webhooks') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50">
            <h3 class="font-semibold text-gray-900 mb-2">Webhooks</h3>
            <p class="text-sm text-gray-600">Check webhook subscriptions</p>
        </a>
        
        <a href="{{ route('admin.logs') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50">
            <h3 class="font-semibold text-gray-900 mb-2">Sync Logs</h3>
            <p class="text-sm text-gray-600">View synchronization logs</p>
        </a>
        
        <a href="{{ route('health') }}" target="_blank" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50">
            <h3 class="font-semibold text-gray-900 mb-2">Health Check</h3>
            <p class="text-sm text-gray-600">System health status</p>
        </a>
        
        <a href="{{ route('admin.blog.index') }}" class="bg-white rounded-lg shadow p-6 hover:bg-gray-50">
            <h3 class="font-semibold text-gray-900 mb-2">Blog Articles</h3>
            <p class="text-sm text-gray-600">Manage blog content</p>
        </a>
    </div>
</div>
@endsection

