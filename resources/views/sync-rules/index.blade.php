@extends('layouts.app')

@section('title', 'Sync Rules')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Sync Rules</h1>
            <p class="mt-2 text-gray-600">Manage how your calendars sync</p>
        </div>
        
        @if(auth()->user()->canCreateSyncRule())
        <a href="{{ route('sync-rules.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Create Rule
        </a>
        @else
        <a href="{{ route('billing') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Upgrade for More Rules
        </a>
        @endif
    </div>
    
    @if($rules->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <p class="text-gray-500 mb-4">No sync rules yet. Create your first rule to start syncing!</p>
        @if(auth()->user()->canCreateSyncRule())
        <a href="{{ route('sync-rules.create') }}" class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Create First Rule
        </a>
        @endif
    </div>
    @else
    <div class="space-y-4">
        @foreach($rules as $rule)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between">
                <div class="flex-grow">
                    <div class="flex items-center space-x-3 mb-3">
                        <h3 class="text-lg font-semibold text-gray-900">
                            @if($rule->isEmailSource())
                                📧 {{ $rule->sourceEmailConnection->name }}
                            @else
                                {{ $rule->sourceConnection->provider_email }}
                            @endif
                        </h3>
                        <span class="text-gray-400">→</span>
                        <span class="text-gray-700">
                            {{ $rule->targets->count() }} target(s)
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600">
                        <p>
                            <strong>Source:</strong>
                            @if($rule->isEmailSource())
                                📧 {{ $rule->sourceEmailConnection->name }} ({{ $rule->sourceEmailConnection->email_address }})
                            @else
                                {{ $rule->sourceConnection->provider_email }} 
                                ({{ $rule->source_calendar_id }})
                            @endif
                        </p>
                        <p>
                            <strong>Targets:</strong>
                            @foreach($rule->targets as $target)
                                @if($target->isEmailTarget())
                                    📧 {{ $target->targetEmailConnection->name }}
                                    @if($target->targetEmailConnection->target_email)
                                        ({{ $target->targetEmailConnection->target_email }})
                                    @endif
                                @else
                                    {{ $target->targetConnection->provider_email }}
                                @endif
                                @if(!$loop->last), @endif
                            @endforeach
                        </p>
                        <p>
                            <strong>Direction:</strong> {{ ucfirst(str_replace('_', ' ', $rule->direction)) }}
                        </p>
                        <p>
                            <strong>Blocker Title:</strong> "{{ $rule->blocker_title }}"
                        </p>
                        @if($rule->last_triggered_at)
                        <p>
                            <strong>Last Triggered:</strong> {{ $rule->last_triggered_at->diffForHumans() }}
                        </p>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-center space-x-2 ml-4">
                    <form action="{{ route('sync-rules.toggle', $rule) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1 rounded {{ $rule->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} text-sm font-medium">
                            {{ $rule->is_active ? 'Active' : 'Paused' }}
                        </button>
                    </form>
                    
                    <form action="{{ route('sync-rules.destroy', $rule) }}" method="POST" onsubmit="return confirm('⚠️ Are you sure?\n\nThis will:\n• Delete this sync rule\n• Remove ALL blocker events created by this rule from target calendars\n\nThis action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

