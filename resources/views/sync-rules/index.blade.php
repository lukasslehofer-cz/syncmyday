@extends('layouts.app')

@section('title', __('messages.sync_rules'))

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ __('messages.sync_rules') }}</h1>
            <p class="text-lg text-gray-600">{{ __('messages.sync_rules_description') }}</p>
        </div>
        
        @if(auth()->user()->canCreateSyncRule())
        <a href="{{ route('sync-rules.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('messages.create_rule') }}
        </a>
        @else
        <a href="{{ route('billing') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            {{ __('messages.upgrade_for_more_rules') }}
        </a>
        @endif
    </div>
    
    @if($rules->isEmpty())
    <div class="bg-white rounded-2xl shadow-lg p-12 text-center border border-gray-100">
        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
            <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.no_sync_rules_yet') }}</h3>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">{{ __('messages.no_sync_rules_description') }}</p>
        @if(auth()->user()->canCreateSyncRule())
        <a href="{{ route('sync-rules.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('messages.create_first_rule') }}
        </a>
        @else
        <a href="{{ route('billing') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            {{ __('messages.upgrade_to_create_rules') }}
        </a>
        @endif
    </div>
    @else
    <div class="space-y-6">
        @foreach($rules as $rule)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $rule->name ?? __('messages.unnamed_rule') }}
                                </h3>
                                <p class="text-xs text-gray-500">
                                    {{ trans_choice('messages.syncing_to_targets', $rule->targets->count(), ['count' => $rule->targets->count()]) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('sync-rules.edit', $rule) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-xl font-semibold text-sm shadow-sm transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            {{ __('messages.edit') }}
                        </a>
                        
                        <form action="{{ route('sync-rules.toggle', $rule) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl font-semibold text-sm shadow-sm transition {{ $rule->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                @if($rule->is_active)
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.active') }}
                                @else
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.paused') }}
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Details -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Source -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-900">{{ __('messages.source_calendar') }}</h4>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                            @if($rule->isEmailSource())
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $rule->sourceEmailConnection->name }}</p>
                                    <p class="text-sm text-gray-600 font-mono bg-white/70 px-2 py-1 rounded mt-1 truncate">
                                        {{ $rule->sourceEmailConnection->email_address }}
                                    </p>
                                    <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Email</span>
                                </div>
                            </div>
                            @else
                            <div class="flex items-start space-x-3">
                                @if($rule->sourceConnection->provider === 'google')
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                </div>
                                @elseif($rule->sourceConnection->provider === 'microsoft')
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                                    </svg>
                                </div>
                                @else
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-gray-800 to-black rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $rule->sourceConnection->name ?? $rule->sourceConnection->provider_email }}</p>
                                    <p class="text-sm text-gray-600 font-mono bg-white/70 px-2 py-1 rounded mt-1 truncate">
                                        {{ $rule->sourceConnection->account_email ?? $rule->sourceConnection->provider_email }}
                                    </p>
                                    <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold 
                                        @if($rule->sourceConnection->provider === 'google') bg-blue-100 text-blue-700
                                        @elseif($rule->sourceConnection->provider === 'microsoft') bg-purple-100 text-purple-700
                                        @elseif($rule->sourceConnection->provider === 'apple') bg-gray-800 text-white
                                        @else bg-gray-100 text-gray-700
                                        @endif rounded-full">
                                        @if($rule->sourceConnection->provider === 'apple')
                                            Apple
                                        @elseif($rule->sourceConnection->provider === 'caldav')
                                            CalDAV
                                        @else
                                            {{ ucfirst($rule->sourceConnection->provider) }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Targets -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-2 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-900">{{ __('messages.target_calendars') }}</h4>
                        </div>
                        <div class="space-y-3">
                            @foreach($rule->targets as $target)
                            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-4 border border-purple-100">
                                @if($target->isEmailTarget())
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900">{{ $target->targetEmailConnection->name }}</p>
                                        <p class="text-sm text-gray-600 font-mono bg-white/70 px-2 py-1 rounded mt-1 truncate">
                                            {{ $target->targetEmailConnection->email_address }}
                                        </p>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Email</span>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-start space-x-3">
                                    @if($target->targetConnection->provider === 'google')
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                        </svg>
                                    </div>
                                    @elseif($target->targetConnection->provider === 'microsoft')
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                                        </svg>
                                    </div>
                                    @else
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-gray-800 to-black rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900">{{ $target->targetConnection->name ?? $target->targetConnection->provider_email }}</p>
                                        <p class="text-sm text-gray-600 font-mono bg-white/70 px-2 py-1 rounded mt-1 truncate">
                                            {{ $target->targetConnection->account_email ?? $target->targetConnection->provider_email }}
                                        </p>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold 
                                            @if($target->targetConnection->provider === 'google') bg-blue-100 text-blue-700
                                            @elseif($target->targetConnection->provider === 'microsoft') bg-purple-100 text-purple-700
                                            @elseif($target->targetConnection->provider === 'apple') bg-gray-800 text-white
                                            @else bg-gray-100 text-gray-700
                                            @endif rounded-full">
                                            @if($target->targetConnection->provider === 'apple')
                                                Apple
                                            @elseif($target->targetConnection->provider === 'caldav')
                                                CalDAV
                                            @else
                                                {{ ucfirst($target->targetConnection->provider) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Additional Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('messages.direction') }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $rule->direction)) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('messages.blocker_title') }}</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">"{{ $rule->blocker_title }}"</p>
                        </div>
                        @if($rule->last_triggered_at)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('messages.last_triggered') }}</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $rule->last_triggered_at->diffForHumans() }}</p>
                        </div>
                        @endif
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">{{ __('messages.status') }}</p>
                            <p class="text-sm font-semibold {{ $rule->is_active ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $rule->is_active ? __('messages.running') : __('messages.paused') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
