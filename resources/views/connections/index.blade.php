@extends('layouts.app')

@section('title', 'Calendar Connections')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Calendar Connections</h1>
        <p class="mt-2 text-gray-600">Connect your calendars to start syncing</p>
    </div>
    
    <!-- Add New Connection -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Add Calendar</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Google Calendar -->
            <a href="{{ route('oauth.google') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📅</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-lg font-medium text-gray-900">Google Calendar</p>
                    <p class="text-sm text-gray-500">Connect your Google account</p>
                </div>
            </a>
            
            <!-- Microsoft Calendar -->
            <a href="{{ route('oauth.microsoft') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📆</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-lg font-medium text-gray-900">Microsoft Calendar</p>
                    <p class="text-sm text-gray-500">Connect your Microsoft 365 account</p>
                </div>
            </a>

            <!-- Email Calendar -->
            <a href="{{ route('email-calendars.create') }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📧</span>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-lg font-medium text-gray-900">Email Calendar</p>
                    <p class="text-sm text-gray-500">Forward invitations via email</p>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Existing Connections -->
    @if($connections->isEmpty() && $emailCalendars->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-500">No calendars connected yet. Add your first calendar above!</p>
    </div>
    @else
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <ul class="divide-y divide-gray-200">
            {{-- API Connections (Google, Microsoft) --}}
            @foreach($connections as $connection)
            <li class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 {{ $connection->provider === 'google' ? 'bg-blue-100' : 'bg-purple-100' }} rounded-lg flex items-center justify-center">
                                <span class="text-2xl">{{ $connection->provider === 'google' ? '📅' : '📆' }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">
                                {{ ucfirst($connection->provider) }} Calendar
                            </p>
                            <p class="text-sm text-gray-500">{{ $connection->provider_email }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $connection->available_calendars ? count($connection->available_calendars) : 0 }} calendar(s) available
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        @if($connection->status === 'active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ ucfirst($connection->status) }}
                        </span>
                        @endif
                        
                        <form action="{{ route('connections.refresh', $connection) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                                Refresh
                            </button>
                        </form>
                        
                        <form action="{{ route('connections.destroy', $connection) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Are you sure you want to disconnect this calendar?\n\nThis will automatically:\n• Delete all sync rules using this calendar as SOURCE\n• Delete sync rules where this is the ONLY target\n• Remove ALL blocker events created from this calendar\n• Stop all webhooks\n\nThis action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($connection->last_error)
                <div class="mt-4 bg-red-50 p-3 rounded">
                    <p class="text-sm text-red-700">Error: {{ $connection->last_error }}</p>
                </div>
                @endif
            </li>
            @endforeach

            {{-- Email Calendar Connections --}}
            @foreach($emailCalendars as $emailCalendar)
            <li class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="text-2xl">📧</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">
                                {{ $emailCalendar->name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <code class="bg-gray-100 px-2 py-0.5 rounded text-xs">{{ $emailCalendar->email_address }}</code>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ $emailCalendar->events_processed }} events synced • {{ $emailCalendar->emails_received }} emails received
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        @if($emailCalendar->status === 'active')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ ucfirst($emailCalendar->status) }}
                        </span>
                        @endif
                        
                        <a href="{{ route('email-calendars.show', $emailCalendar) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            View
                        </a>
                        
                        <form action="{{ route('email-calendars.destroy', $emailCalendar) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this email calendar?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($emailCalendar->last_error)
                <div class="mt-4 bg-red-50 p-3 rounded">
                    <p class="text-sm text-red-700">Error: {{ $emailCalendar->last_error }}</p>
                </div>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection

