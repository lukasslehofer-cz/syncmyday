@extends('layouts.app')

@section('title', __('messages.calendar_details'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('connections.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div class="flex-1">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    {{ $connection->name ?? __('messages.calendar') }}
                </h1>
            </div>
            @if($connection->status === 'active')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 shadow-sm">
                    {{ __('messages.active') }}
                </span>
            @elseif($connection->status === 'expired')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 shadow-sm">
                    {{ __('messages.expired') }}
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 shadow-sm">
                    {{ ucfirst($connection->status) }}
                </span>
            @endif
        </div>
        <p class="text-lg text-gray-600">
            @if($connection->provider === 'google')
                {{ __('messages.google_calendar_details_description') }}
            @elseif($connection->provider === 'microsoft')
                {{ __('messages.microsoft_calendar_details_description') }}
            @elseif($connection->provider === 'caldav')
                {{ __('messages.caldav_calendar_details_description') }}
            @endif
        </p>
    </div>

    <!-- Connection Info -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.connection_details') }}</h2>
            </div>
        </div>
        
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.provider') }}</label>
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                        @if($connection->provider === 'google')
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-100 text-blue-700">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Google Calendar
                            </span>
                        @elseif($connection->provider === 'microsoft')
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-purple-100 text-purple-700">
                                <svg class="w-4 h-4 mr-2" viewBox="0 0 23 23">
                                    <path fill="#f35325" d="M1 1h10v10H1z"/>
                                    <path fill="#81bc06" d="M12 1h10v10H12z"/>
                                    <path fill="#05a6f0" d="M1 12h10v10H1z"/>
                                    <path fill="#ffba08" d="M12 12h10v10H12z"/>
                                </svg>
                                Microsoft 365
                            </span>
                        @elseif($connection->provider === 'caldav')
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-gray-100 text-gray-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                CalDAV
                            </span>
                        @endif
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.account_email') }}</label>
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-900 font-mono break-all">{{ $connection->account_email ?? $connection->provider_email }}</p>
                    </div>
                </div>

                @if($selectedCalendar)
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.selected_calendar') }}</label>
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl px-4 py-3">
                        <div class="flex items-center space-x-3">
                            @if($selectedCalendar['primary'] ?? false)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                                    {{ __('messages.primary') }}
                                </span>
                            @endif
                            <span class="font-semibold text-gray-900">{{ $selectedCalendar['name'] }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($connection->available_calendars)
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.available_calendars') }}</label>
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-900 font-medium">{{ count($connection->available_calendars) }} {{ __('messages.calendars_count') }}</p>
                    </div>
                </div>
                @endif

                @if($connection->last_sync_at)
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-2">{{ __('messages.last_sync') }}</label>
                    <div class="bg-gray-50 border-2 border-gray-200 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-900 font-medium">{{ $connection->last_sync_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sync Rules -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.sync_rules') }}</h2>
            </div>
        </div>
        <div class="p-6 lg:p-8">
            @if($syncRulesAsSource->count() > 0 || $syncRulesAsTarget->count() > 0)
                <!-- Rules as Source -->
                @if($syncRulesAsSource->count() > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                        {{ __('messages.as_source') }} ({{ $syncRulesAsSource->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($syncRulesAsSource as $rule)
                        <a href="{{ route('sync-rules.edit', $rule) }}" class="flex items-center justify-between p-4 bg-gray-50 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:shadow-md transition cursor-pointer group">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900 group-hover:text-gray-700">
                                    → 
                                    @foreach($rule->targets as $index => $target)
                                        @if($index > 0), @endif
                                        @if($target->targetConnection)
                                            {{ $target->targetConnection->name ?? $target->targetConnection->provider_email }}
                                        @elseif($target->targetEmailConnection)
                                            {{ $target->targetEmailConnection->name }}
                                        @endif
                                    @endforeach
                                </span>
                            </div>
                            <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $rule->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $rule->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Rules as Target -->
                @if($syncRulesAsTarget->count() > 0)
                <div>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        {{ __('messages.as_target') }} ({{ $syncRulesAsTarget->count() }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($syncRulesAsTarget as $rule)
                        <a href="{{ route('sync-rules.edit', $rule) }}" class="flex items-center justify-between p-4 bg-gray-50 border-2 border-gray-200 rounded-xl hover:border-gray-300 hover:shadow-md transition cursor-pointer group">
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900 group-hover:text-gray-700">
                                    @if($rule->sourceConnection)
                                        {{ $rule->sourceConnection->name ?? $rule->sourceConnection->provider_email }}
                                    @elseif($rule->sourceEmailConnection)
                                        {{ $rule->sourceEmailConnection->name }}
                                    @endif
                                    →
                                </span>
                            </div>
                            <span class="ml-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $rule->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $rule->is_active ? __('messages.active') : __('messages.inactive') }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            @else
                <div class="text-center py-8">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-gray-500 mb-4">{{ __('messages.no_sync_rules_for_calendar') }}</p>
                    <a href="{{ route('sync-rules.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('messages.create_first_rule') }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    @if($connection->last_error)
    <!-- Error Information -->
    <div class="bg-white rounded-2xl shadow-xl border-2 border-red-200 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-200 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-red-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-red-900">{{ __('messages.connection_error') }}</h2>
            </div>
        </div>
        <div class="p-6 lg:p-8">
            <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4">
                <p class="text-sm font-bold text-red-900 mb-2">{{ __('messages.last_error') }}:</p>
                <p class="text-sm text-red-700">{{ $connection->last_error }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.statistics') }}</h2>
            </div>
        </div>
        
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <p class="text-sm font-bold text-gray-600 mb-2">{{ __('messages.received_blockers') }}</p>
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl px-4 py-3">
                        <p class="text-3xl font-bold text-indigo-600">{{ number_format($receivedBlockers) }}</p>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-gray-600 mb-2">{{ __('messages.sent_blockers') }}</p>
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-xl px-4 py-3">
                        <p class="text-3xl font-bold text-green-600">{{ number_format($sentBlockers) }}</p>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-sm font-bold text-gray-600 mb-2">{{ __('messages.last_sync_event') }}</p>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 border-2 border-purple-200 rounded-xl px-4 py-3">
                        <p class="text-xl font-bold text-purple-600">
                            @if($lastSyncEvent)
                                {{ $lastSyncEvent->created_at->diffForHumans() }}
                            @else
                                {{ __('messages.never') }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-gray-50 px-6 py-4 rounded-xl border border-gray-200 flex justify-between items-center">
        <a href="{{ route('connections.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 transition">
            {{ __('messages.back') }}
        </a>
        
        <div class="flex gap-3">
            <form action="{{ route('connections.refresh', $connection) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg transform hover:scale-105 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ __('messages.refresh') }}
                </button>
            </form>
            
            <a href="{{ route('connections.edit', $connection) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                {{ __('messages.edit') }}
            </a>
        </div>
    </div>
</div>
@endsection

