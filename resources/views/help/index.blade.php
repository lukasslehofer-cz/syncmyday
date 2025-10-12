@extends('layouts.help')

@section('title', 'Help Center')

@section('content')
<h1>Welcome to SyncMyDay Help Center</h1>

<p class="text-xl text-gray-600 mb-8">Everything you need to know about keeping your calendars in sync.</p>

<div class="grid md:grid-cols-2 gap-6 mb-12">
    <a href="{{ route('help.faq') }}" class="group p-6 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl hover:shadow-lg transition">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 group-hover:text-indigo-600 transition">Frequently Asked Questions</h2>
        </div>
        <p class="text-gray-600">Quick answers to common questions about security, payments, and how SyncMyDay works.</p>
    </a>
    
    <a href="{{ route('help.sync-rules') }}" class="group p-6 bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-100 rounded-xl hover:shadow-lg transition">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 group-hover:text-purple-600 transition">Creating Sync Rules</h2>
        </div>
        <p class="text-gray-600">Learn how to set up synchronization between your calendars with filters and options.</p>
    </a>
</div>

<h2>Connect Your Calendars</h2>
<p>Choose your calendar service below to get started:</p>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
    <!-- Google Calendar -->
    <a href="{{ route('help.connect-google') }}" class="group relative overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 transition-all duration-200 hover:border-blue-400 hover:shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-blue-600 shadow-md">
                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-blue-600">Google Calendar</h3>
                <p class="text-xs text-gray-500">Quick OAuth setup</p>
            </div>
        </div>
    </a>
    
    <!-- Microsoft 365 -->
    <a href="{{ route('help.connect-microsoft') }}" class="group relative overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 transition-all duration-200 hover:border-purple-400 hover:shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 shadow-md">
                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-purple-600">Microsoft 365</h3>
                <p class="text-xs text-gray-500">Outlook & Office 365</p>
            </div>
        </div>
    </a>
    
    <!-- Apple iCloud -->
    <a href="{{ route('help.connect-apple') }}" class="group relative overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 transition-all duration-200 hover:border-gray-400 hover:shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-gray-800 to-black shadow-md">
                <svg class="h-7 w-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-gray-700">Apple iCloud</h3>
                <p class="text-xs text-gray-500">App-specific password</p>
            </div>
        </div>
    </a>
</div>

<!-- Second row - centered -->
<div class="flex justify-center gap-3">
    <!-- CalDAV -->
    <a href="{{ route('help.connect-caldav') }}" class="group relative w-full max-w-sm overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 transition-all duration-200 hover:border-indigo-400 hover:shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-500 to-indigo-600 shadow-md">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-indigo-600">CalDAV</h3>
                <p class="text-xs text-gray-500">Fastmail, Nextcloud, etc.</p>
            </div>
        </div>
    </a>
    
    <!-- Email Calendar -->
    <a href="{{ route('help.connect-email') }}" class="group relative w-full max-w-sm overflow-hidden rounded-xl border-2 border-gray-200 bg-white p-4 transition-all duration-200 hover:border-green-400 hover:shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-green-500 to-green-600 shadow-md">
                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="font-semibold text-gray-900 transition-colors group-hover:text-green-600">Email Calendar</h3>
                <p class="text-xs text-gray-500">Forward .ics invites</p>
            </div>
        </div>
    </a>
</div>

<div class="mt-12 p-6 bg-blue-50 border border-blue-200 rounded-xl">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="ml-3">
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Getting Started</h3>
            <p class="text-blue-800 mb-2">To start syncing your calendars:</p>
            <ol class="list-decimal list-inside space-y-1 text-blue-800">
                <li>Connect at least 2 calendars using the guides above</li>
                <li>Create a sync rule to define how events should sync</li>
                <li>Watch as blocker events are automatically created in real-time!</li>
            </ol>
        </div>
    </div>
</div>
@endsection

