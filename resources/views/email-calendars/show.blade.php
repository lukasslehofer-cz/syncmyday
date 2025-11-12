@extends('layouts.app')

@section('title', __('messages.email_calendar_details'))

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
                <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                    {{ $emailCalendar->name }}
                </h1>
            </div>
            @if($emailCalendar->status === 'active')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-100 text-green-700 shadow-sm">
                    {{ __('messages.active') }}
                </span>
            @elseif($emailCalendar->status === 'paused')
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700 shadow-sm">
                    {{ __('messages.paused') }}
                </span>
            @else
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-red-100 text-red-700 shadow-sm">
                    {{ __('messages.error') }}
                </span>
            @endif
        </div>
        <p class="text-lg text-gray-600">
            {{ __('messages.email_calendar_details_description') }}
        </p>
    </div>

    <!-- Email Address -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.your_unique_email') }}</h2>
            </div>
        </div>
        
        <div class="p-6 lg:p-8">
            <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-xl p-4 mb-6">
                <div class="flex items-center gap-3">
                    <code class="flex-1 text-base font-mono text-gray-900 break-all font-semibold">{{ $emailCalendar->email_address }}</code>
                    <button 
                        onclick="navigator.clipboard.writeText('{{ $emailCalendar->email_address }}'); this.textContent='✓'; setTimeout(() => this.textContent='{{ __('messages.copy') }}', 2000)"
                        class="px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:opacity-90 text-white text-sm font-semibold rounded-lg shadow-md transition transform hover:scale-105"
                    >
                        {{ __('messages.copy') }}
                    </button>
                </div>
            </div>
            
            <div class="space-y-4 text-sm text-gray-700">
                <div class="flex items-start space-x-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    <div>
                        <p class="font-bold text-gray-900">{{ __('messages.as_source') }}:</p>
                        <p class="mt-1">{{ __('messages.as_source_description') }}</p>
                    </div>
                </div>
                
                @if($emailCalendar->target_email)
                <div class="flex items-start space-x-3 p-3 bg-green-50 rounded-lg border border-green-100">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <p class="font-bold text-gray-900">{{ __('messages.as_target') }}:</p>
                        <p class="mt-1">{{ __('messages.as_target_description') }} <code class="bg-green-100 px-2 py-0.5 rounded text-xs font-mono">{{ $emailCalendar->target_email }}</code></p>
                    </div>
                </div>
                @else
                <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <div>
                        <p class="font-bold text-gray-900">{{ __('messages.as_target') }}:</p>
                        <p class="mt-1 text-gray-500">{{ __('messages.as_target_not_configured') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Setup Instructions -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 mb-6 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-amber-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.setup_instructions') }}</h2>
            </div>
        </div>
        
        <div class="p-6 lg:p-8">
        
        <div class="space-y-4">
            <!-- Outlook/Exchange -->
            <details class="border border-gray-200 rounded-lg">
                <summary class="px-4 py-3 cursor-pointer hover:bg-gray-50 font-medium text-gray-900">
                    📌 Microsoft Outlook / Exchange
                </summary>
                <div class="px-4 py-3 border-t border-gray-200 text-sm text-gray-700 space-y-2">
                    <p class="font-medium">Email Forwarding Rule:</p>
                    <ol class="list-decimal list-inside space-y-1 ml-2">
                        <li>Open Outlook → <strong>File → Manage Rules & Alerts</strong></li>
                        <li>Click <strong>New Rule</strong></li>
                        <li>Choose "Apply rule on messages I receive"</li>
                        <li>Add conditions:
                            <ul class="list-disc list-inside ml-4 mt-1">
                                <li>with specific words in the subject: <code class="bg-gray-100 px-1">meeting, invitation</code></li>
                                <li>with an attachment</li>
                            </ul>
                        </li>
                        <li>Action: <strong>Forward it to</strong> <code class="bg-gray-100 px-1">{{ $emailCalendar->email_address }}</code></li>
                        <li>Click <strong>Finish</strong></li>
                    </ol>
                </div>
            </details>

            <!-- Gmail -->
            <details class="border border-gray-200 rounded-lg">
                <summary class="px-4 py-3 cursor-pointer hover:bg-gray-50 font-medium text-gray-900">
                    📌 Gmail
                </summary>
                <div class="px-4 py-3 border-t border-gray-200 text-sm text-gray-700 space-y-2">
                    <p class="font-medium">Gmail Forwarding Filter:</p>
                    <ol class="list-decimal list-inside space-y-1 ml-2">
                        <li>Open Gmail → <strong>Settings (⚙️) → See all settings</strong></li>
                        <li>Go to <strong>Filters and Blocked Addresses</strong></li>
                        <li>Click <strong>Create a new filter</strong></li>
                        <li>In "Has the words" enter: <code class="bg-gray-100 px-1">filename:ics</code></li>
                        <li>Click <strong>Create filter</strong></li>
                        <li>Check <strong>Forward it to</strong> and select/add <code class="bg-gray-100 px-1">{{ $emailCalendar->email_address }}</code></li>
                        <li>Click <strong>Create filter</strong></li>
                    </ol>
                    <p class="text-xs text-gray-600 mt-2">Note: You may need to verify the forwarding address first.</p>
                </div>
            </details>
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
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            @if($emailCalendar->last_error)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4">
                    <p class="text-sm font-bold text-red-900 mb-2">{{ __('messages.last_error') }}:</p>
                    <p class="text-sm text-red-700">{{ __('messages.calendar_' . $emailCalendar->last_error, ['provider' => __('messages.email_calendar_type')]) }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-gray-50 px-6 py-4 rounded-xl border border-gray-200 flex justify-between items-center">
        <a href="{{ route('connections.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 transition">
            {{ __('messages.back') }}
        </a>
        
        <a href="{{ route('email-calendars.edit', $emailCalendar) }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{ __('messages.edit') }}
        </a>
    </div>
</div>
@endsection
