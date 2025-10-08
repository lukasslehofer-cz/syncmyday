@extends('layouts.app')

@section('title', 'Sync Logs - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-900">Sync Logs</h1>
        
        <form method="GET" class="flex space-x-2">
            <select name="action" class="px-3 py-2 border border-gray-300 rounded-md">
                <option value="all">All actions</option>
                <option value="created" {{ request('action') === 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('action') === 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('action') === 'deleted' ? 'selected' : '' }}>Deleted</option>
                <option value="error" {{ request('action') === 'error' ? 'selected' : '' }}>Errors</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Direction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Details</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($logs as $log)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $log->created_at->format('M d, H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ $log->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs rounded 
                            {{ $log->action === 'created' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $log->action === 'error' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $log->action === 'deleted' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $log->action === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $log->action === 'skipped' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        ">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $log->direction ? str_replace('_', ' ', $log->direction) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $log->event_start ? $log->event_start->format('M d, H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($log->error_message)
                        <span class="text-red-600">{{ Str::limit($log->error_message, 50) }}</span>
                        @else
                        -
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
</div>
@endsection

