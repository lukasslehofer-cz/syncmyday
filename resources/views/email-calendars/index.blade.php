@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Email-Based Calendars</h1>
        <p class="mt-2 text-sm text-gray-600">
            Connect calendars by forwarding invitations to a unique email address
        </p>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Add New Button -->
    <div class="mb-6">
        <a href="{{ route('email-calendars.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Email Calendar
        </a>
    </div>

    <!-- Email Calendars List -->
    @if($connections->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No email calendars yet</h3>
            <p class="mt-2 text-sm text-gray-500">Get started by creating your first email calendar connection.</p>
            <div class="mt-6">
                <a href="{{ route('email-calendars.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    Add Email Calendar
                </a>
            </div>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($connections as $connection)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Name & Status -->
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900">{{ $connection->name }}</h3>
                                @if($connection->status === 'active')
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">Active</span>
                                @elseif($connection->status === 'paused')
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded">Paused</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded">Error</span>
                                @endif
                            </div>

                            <!-- Email Address -->
                            <div class="mb-3 bg-gray-50 rounded-lg p-3 border border-gray-200">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Forward invitations to:</label>
                                <div class="flex items-center gap-2">
                                    <code class="flex-1 text-sm font-mono text-gray-900">{{ $connection->email_address }}</code>
                                    <button onclick="navigator.clipboard.writeText('{{ $connection->email_address }}')" class="px-2 py-1 bg-white border border-gray-300 text-gray-700 text-xs rounded hover:bg-gray-50">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="flex gap-6 text-sm text-gray-600 mb-3">
                                <div>
                                    <span class="font-medium">{{ $connection->emails_received }}</span> emails received
                                </div>
                                <div>
                                    <span class="font-medium">{{ $connection->events_processed }}</span> events synced
                                </div>
                                @if($connection->last_email_at)
                                    <div>
                                        Last email: {{ $connection->last_email_at->diffForHumans() }}
                                    </div>
                                @endif
                            </div>

                            @if($connection->description)
                                <p class="text-sm text-gray-600">{{ $connection->description }}</p>
                            @endif

                            @if($connection->last_error)
                                <div class="mt-2 text-sm text-red-600">
                                    ⚠️ {{ $connection->last_error }}
                                </div>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="ml-4 flex flex-col gap-2">
                            <a href="{{ route('email-calendars.show', $connection) }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50">
                                View Details
                            </a>
                            <a href="{{ route('email-calendars.test', $connection) }}" class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50">
                                Test
                            </a>
                            <form action="{{ route('email-calendars.destroy', $connection) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this email calendar?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-1.5 bg-white border border-red-300 text-red-600 text-sm rounded hover:bg-red-50">
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

