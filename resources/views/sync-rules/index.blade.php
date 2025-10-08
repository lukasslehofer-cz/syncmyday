@extends('layouts.app')

@section('title', 'Sync Rules')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Sync Rules</h1>
            <p class="text-lg text-gray-600">Configure how your calendars synchronize</p>
        </div>
        
        @if(auth()->user()->canCreateSyncRule())
        <a href="{{ route('sync-rules.create') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Rule
        </a>
        @else
        <a href="{{ route('billing') }}" class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Upgrade for More Rules
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
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No Sync Rules Yet</h3>
        <p class="text-gray-500 mb-8 max-w-md mx-auto">Create your first sync rule to start automatically syncing events between your calendars</p>
        @if(auth()->user()->canCreateSyncRule())
        <a href="{{ route('sync-rules.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create Your First Rule
        </a>
        @else
        <a href="{{ route('billing') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Upgrade to Create Rules
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
                                    @if($rule->isEmailSource())
                                        {{ $rule->sourceEmailConnection->name }}
                                    @else
                                        {{ $rule->sourceConnection->provider_email }}
                                    @endif
                                </h3>
                                <p class="text-xs text-gray-500">
                                    Syncing to {{ $rule->targets->count() }} target{{ $rule->targets->count() !== 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <form action="{{ route('sync-rules.toggle', $rule) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 rounded-xl font-semibold text-sm shadow-sm transition {{ $rule->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                                @if($rule->is_active)
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Active
                                @else
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8 7a1 1 0 00-1 1v4a1 1 0 001 1h4a1 1 0 001-1V8a1 1 0 00-1-1H8z" clip-rule="evenodd"/>
                                </svg>
                                Paused
                                @endif
                            </button>
                        </form>
                        
                        <form action="{{ route('sync-rules.destroy', $rule) }}" method="POST" onsubmit="return confirm('⚠️ Are you sure?\n\nThis will:\n• Delete this sync rule\n• Remove ALL blocker events created by this rule from target calendars\n\nThis action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-xl font-semibold text-sm shadow-sm transition">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete
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
                            <h4 class="font-bold text-gray-900">Source Calendar</h4>
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
                                    <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Email Calendar</span>
                                </div>
                            </div>
                            @else
                            <div class="flex items-start space-x-3">
                                @if($rule->sourceConnection->provider === 'google')
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    </svg>
                                </div>
                                @else
                                <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.5,0 L23,6.5 L23,17.5 L11.5,24 L0,17.5 L0,6.5 L11.5,0 Z"/>
                                    </svg>
                                </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-900">{{ $rule->sourceConnection->provider_email }}</p>
                                    <p class="text-sm text-gray-600 bg-white/70 px-2 py-1 rounded mt-1 truncate">
                                        {{ $rule->source_calendar_id }}
                                    </p>
                                    <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold {{ $rule->sourceConnection->provider === 'google' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }} rounded-full">
                                        {{ ucfirst($rule->sourceConnection->provider) }}
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
                            <h4 class="font-bold text-gray-900">Target Calendar(s)</h4>
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
                                        @if($target->targetEmailConnection->target_email)
                                        <p class="text-sm text-gray-600 font-mono bg-white/70 px-2 py-1 rounded mt-1 truncate">
                                            {{ $target->targetEmailConnection->target_email }}
                                        </p>
                                        @endif
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">Email</span>
                                    </div>
                                </div>
                                @else
                                <div class="flex items-start space-x-3">
                                    @if($target->targetConnection->provider === 'google')
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        </svg>
                                    </div>
                                    @else
                                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M11.5,0 L23,6.5 L23,17.5 L11.5,24 L0,17.5 L0,6.5 L11.5,0 Z"/>
                                        </svg>
                                    </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-gray-900">{{ $target->targetConnection->provider_email }}</p>
                                        <span class="inline-block mt-2 px-2 py-1 text-xs font-semibold {{ $target->targetConnection->provider === 'google' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }} rounded-full">
                                            {{ ucfirst($target->targetConnection->provider) }}
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
                            <p class="text-xs text-gray-500 mb-1">Direction</p>
                            <p class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $rule->direction)) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Blocker Title</p>
                            <p class="text-sm font-semibold text-gray-900 truncate">"{{ $rule->blocker_title }}"</p>
                        </div>
                        @if($rule->last_triggered_at)
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Last Triggered</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $rule->last_triggered_at->diffForHumans() }}</p>
                        </div>
                        @endif
                        <div class="bg-gray-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Status</p>
                            <p class="text-sm font-semibold {{ $rule->is_active ? 'text-green-600' : 'text-gray-600' }}">
                                {{ $rule->is_active ? 'Running' : 'Paused' }}
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
