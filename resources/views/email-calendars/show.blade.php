@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('email-calendars.index') }}" class="text-sm text-blue-600 hover:text-blue-700 mb-2 inline-block">
            ← Back to Email Calendars
        </a>
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $emailCalendar->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">Email-based calendar connection</p>
            </div>
            @if($emailCalendar->status === 'active')
                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-lg">Active</span>
            @elseif($emailCalendar->status === 'paused')
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-lg">Paused</span>
            @else
                <span class="px-3 py-1 bg-red-100 text-red-800 text-sm font-medium rounded-lg">Error</span>
            @endif
        </div>
    </div>

    <!-- Email Address -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">📧 Your Unique Email Address</h2>
        <div class="bg-white rounded-lg p-4 border border-blue-300">
            <div class="flex items-center gap-3">
                <code class="flex-1 text-lg font-mono text-gray-900 break-all">{{ $emailCalendar->email_address }}</code>
                <button 
                    onclick="navigator.clipboard.writeText('{{ $emailCalendar->email_address }}'); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy', 2000)"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition"
                >
                    Copy
                </button>
            </div>
        </div>
        <p class="mt-3 text-sm text-gray-700">
            <strong>As Source:</strong> Forward calendar invitations to this address to create blockers in your other calendars (set up in Sync Rules).
        </p>
        @if($emailCalendar->target_email)
        <p class="mt-1 text-sm text-gray-700">
            <strong>As Target:</strong> iMIP invitations will be sent to <code class="bg-blue-100 px-1 rounded text-xs">{{ $emailCalendar->target_email }}</code>
        </p>
        @else
        <p class="mt-1 text-sm text-gray-500">
            <strong>As Target:</strong> No target email configured - set one to use this calendar as a target in sync rules.
        </p>
        @endif
    </div>

    <!-- Setup Instructions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">⚙️ Setup Instructions</h2>
        
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

    <!-- Sync Rules -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">🔄 Sync Rules</h2>
        <p class="text-sm text-gray-600 mb-4">
            Configure how this email calendar synchronizes with your other calendars.
        </p>
        <a href="{{ route('sync-rules.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium">
            Manage Sync Rules →
        </a>
    </div>

    <!-- Statistics -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">📊 Statistics</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600">Emails Received</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($emailCalendar->emails_received) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Events Processed</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($emailCalendar->events_processed) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Last Email</p>
                <p class="text-2xl font-bold text-gray-900">
                    @if($emailCalendar->last_email_at)
                        {{ $emailCalendar->last_email_at->diffForHumans() }}
                    @else
                        Never
                    @endif
                </p>
            </div>
        </div>

        @if($emailCalendar->last_error)
        <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm font-medium text-red-900">Last Error:</p>
            <p class="text-sm text-red-700 mt-1">{{ $emailCalendar->last_error }}</p>
        </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3">
        <form action="{{ route('email-calendars.destroy', $emailCalendar) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this email calendar connection?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 border border-red-600 text-red-600 hover:bg-red-50 rounded-lg font-medium">
                Delete Calendar
            </button>
        </form>
    </div>
</div>
@endsection
