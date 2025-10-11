@extends('layouts.app')

@section('title', 'Calendar Connections')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Calendar Connections</h1>
        <p class="text-lg text-gray-600">Connect your calendars to start synchronizing your schedule</p>
    </div>
    
    <!-- Add New Connection -->
    <div class="bg-white rounded-2xl shadow-lg p-6 lg:p-8 mb-8 border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Calendar</h2>
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Google Calendar -->
            <a href="{{ route('oauth.google') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl hover:border-blue-400 hover:shadow-xl transition transform hover:scale-105">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-4">
                        <div class="h-16 w-16 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 mb-2">Google Calendar</p>
                        <p class="text-sm text-gray-600">Connect via OAuth</p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg class="w-6 h-6 text-blue-400 group-hover:text-blue-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>
            
            <!-- Microsoft Calendar -->
            <a href="{{ route('oauth.microsoft') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl hover:border-purple-400 hover:shadow-xl transition transform hover:scale-105">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-4">
                        <div class="h-16 w-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 mb-2">Microsoft 365</p>
                        <p class="text-sm text-gray-600">Connect via OAuth</p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg class="w-6 h-6 text-purple-400 group-hover:text-purple-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>

            <!-- CalDAV Calendar -->
            <a href="{{ route('caldav.setup') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-orange-50 to-amber-50 border-2 border-orange-200 rounded-xl hover:border-orange-400 hover:shadow-xl transition transform hover:scale-105">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-4">
                        <div class="h-16 w-16 bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 mb-2">Apple / CalDAV</p>
                        <p class="text-sm text-gray-600">iCloud, Nextcloud, etc.</p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg class="w-6 h-6 text-orange-400 group-hover:text-orange-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>

            <!-- Email Calendar -->
            <a href="{{ route('email-calendars.create') }}" class="group relative overflow-hidden p-6 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl hover:border-green-400 hover:shadow-xl transition transform hover:scale-105">
                <div class="flex flex-col items-center text-center">
                    <div class="flex-shrink-0 mb-4">
                        <div class="h-16 w-16 bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-gray-900 mb-2">Email Calendar</p>
                        <p class="text-sm text-gray-600">Forward via email</p>
                    </div>
                </div>
                <div class="absolute top-4 right-4">
                    <svg class="w-6 h-6 text-green-400 group-hover:text-green-600 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Existing Connections -->
    @if($connections->isEmpty() && $emailCalendars->isEmpty())
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">No Calendars Connected</h3>
        <p class="text-gray-500 mb-8">Connect your first calendar to start syncing your schedule</p>
        <p class="text-sm text-gray-400">Choose from Google Calendar, Microsoft 365, or Email Calendar above</p>
    </div>
    @else
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-900">Your Connected Calendars</h2>
        </div>
        
        <ul class="divide-y divide-gray-200">
            {{-- API Connections (Google, Microsoft) --}}
            @foreach($connections as $connection)
            <li class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            @if($connection->provider === 'google')
                            <div class="h-14 w-14 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                            </div>
                            @elseif($connection->provider === 'microsoft')
                            <div class="h-14 w-14 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                                </svg>
                            </div>
                            @elseif($connection->provider === 'caldav')
                            <div class="h-14 w-14 bg-gradient-to-r from-orange-500 to-amber-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">
                                @if($connection->provider === 'caldav')
                                    CalDAV Calendar
                                @else
                                    {{ ucfirst($connection->provider) }} Calendar
                                @endif
                            </p>
                            <p class="text-sm text-gray-600 font-medium">{{ $connection->account_email ?? $connection->provider_email }}</p>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="text-xs text-gray-500">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $connection->available_calendars ? count($connection->available_calendars) : 0 }} calendar(s)
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        @if($connection->status === 'active')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 shadow-sm">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 shadow-sm">
                            {{ ucfirst($connection->status) }}
                        </span>
                        @endif
                        
                        <form action="{{ route('connections.refresh', $connection) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Refresh
                            </button>
                        </form>
                        
                        @if($connection->provider === 'caldav')
                        <form action="{{ route('caldav.disconnect', $connection) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Are you sure you want to disconnect this CalDAV calendar?\n\nThis will automatically:\n• Delete all sync rules using this calendar as SOURCE\n• Delete sync rules where this is the ONLY target\n• Remove ALL blocker events created from this calendar\n\nThis action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove
                            </button>
                        </form>
                        @else
                        <form action="{{ route('connections.destroy', $connection) }}" method="POST" class="inline" onsubmit="return confirm('⚠️ Are you sure you want to disconnect this calendar?\n\nThis will automatically:\n• Delete all sync rules using this calendar as SOURCE\n• Delete sync rules where this is the ONLY target\n• Remove ALL blocker events created from this calendar\n• Stop all webhooks\n\nThis action cannot be undone!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                
                @if($connection->last_error)
                <div class="mt-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 p-4 rounded-xl">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Connection Error</p>
                            <p class="text-sm text-red-700 mt-1">{{ $connection->last_error }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </li>
            @endforeach

            {{-- Email Calendar Connections --}}
            @foreach($emailCalendars as $emailCalendar)
            <li class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-14 w-14 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-gray-900">
                                {{ $emailCalendar->name }}
                            </p>
                            <p class="text-sm text-gray-600 font-mono bg-gray-100 px-3 py-1 rounded-lg inline-block mt-1">
                                {{ $emailCalendar->email_address }}
                            </p>
                            <div class="flex items-center mt-2 space-x-4">
                                <span class="text-xs text-gray-500">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $emailCalendar->events_processed }} events
                                </span>
                                <span class="text-xs text-gray-500">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $emailCalendar->emails_received }} emails
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        @if($emailCalendar->status === 'active')
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 shadow-sm">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 shadow-sm">
                            {{ ucfirst($emailCalendar->status) }}
                        </span>
                        @endif
                        
                        <a href="{{ route('email-calendars.show', $emailCalendar) }}" class="px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition">
                            <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Details
                        </a>
                        
                        <form action="{{ route('email-calendars.destroy', $emailCalendar) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this email calendar?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
                
                @if($emailCalendar->last_error)
                <div class="mt-4 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 p-4 rounded-xl">
                    <div class="flex items-start space-x-2">
                        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-red-800">Connection Error</p>
                            <p class="text-sm text-red-700 mt-1">{{ $emailCalendar->last_error }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
@endsection
