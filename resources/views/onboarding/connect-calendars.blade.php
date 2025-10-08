@extends('layouts.app')

@section('title', 'Connect Calendars - Onboarding')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Progress -->
    <div class="mb-8">
        <div class="flex items-center justify-between text-sm">
            <span class="text-indigo-600 font-medium">Step 1 of 3</span>
            <span class="text-gray-500">Connect calendars</span>
        </div>
        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="h-full bg-indigo-600" style="width: 33%"></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">📅</div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Connect Your Calendars</h1>
            <p class="text-gray-600">Connect at least 2 calendars to start syncing</p>
        </div>

        <!-- Connected Calendars -->
        @if($connections->count() > 0)
        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Connected Calendars ({{ $connections->count() }})</h3>
            <div class="space-y-2">
                @foreach($connections as $connection)
                <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="h-10 w-10 {{ $connection->provider === 'google' ? 'bg-blue-100' : 'bg-purple-100' }} rounded-lg flex items-center justify-center">
                            <span class="text-xl">{{ $connection->provider === 'google' ? '📅' : '📆' }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ ucfirst($connection->provider) }}</p>
                            <p class="text-sm text-gray-500">{{ $connection->provider_email }}</p>
                        </div>
                    </div>
                    <span class="text-green-600 text-sm font-medium">✓ Connected</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Calendar Buttons -->
        <div class="space-y-4 mb-8">
            <h3 class="text-sm font-medium text-gray-700 mb-3">Add Calendar</h3>
            
            <a href="{{ route('oauth.google') }}" class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📅</span>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">Google Calendar</p>
                        <p class="text-sm text-gray-500">Connect your Google account</p>
                    </div>
                </div>
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="{{ route('oauth.microsoft') }}" class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-lg hover:border-indigo-500 hover:bg-indigo-50 transition">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <span class="text-2xl">📆</span>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">Microsoft Calendar</p>
                        <p class="text-sm text-gray-500">Connect your Microsoft 365 account</p>
                    </div>
                </div>
                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between items-center pt-6 border-t">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">
                Skip for now
            </a>
            
            @if($connections->count() >= 2)
            <a href="{{ route('onboarding.create-rule') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                Continue →
            </a>
            @else
            <div class="text-sm text-gray-500">
                Connect at least 2 calendars to continue
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

