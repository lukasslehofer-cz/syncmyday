@extends('layouts.help')

@section('title', 'Connect Google Calendar')

@section('content')
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-blue-500 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Connect Google Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Quick and secure OAuth connection</p>
    </div>
</div>

<div class="p-6 bg-green-50 border border-green-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-green-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-green-900 mb-2 leading-tight">Why Google Calendar?</h3>
            <p class="text-green-800 mb-0"><strong>Google Calendar is the easiest calendar to connect!</strong> It uses secure OAuth authentication, so you never share your password with us. Setup takes less than 2 minutes, and synchronization is instant thanks to real-time webhooks.</p>
        </div>
    </div>
</div>

<h2>Step-by-Step Guide</h2>

<div class="space-y-8">
    <!-- Step 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Go to Calendar Connections</h3>
            <p>From your SyncMyDay dashboard, click on <strong>Calendars</strong> in the main menu, or go directly to the <a href="{{ route('connections.index') }}">Calendar Connections page</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard with "Calendars" menu item highlighted</p>
                <p class="text-sm">Shows the main navigation with the Calendars link clearly visible</p>
            </div>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Click "Connect Google Calendar"</h3>
            <p>On the Calendar Connections page, find the <strong>Google Calendar</strong> button with the Google logo and click it.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar Connections page showing the "Connect Google Calendar" button</p>
                <p class="text-sm">Shows the grid of calendar provider options with Google Calendar prominently displayed</p>
            </div>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Sign in with Google</h3>
            <p>You'll be redirected to Google's secure login page. Sign in with the Google account that has the calendar you want to connect.</p>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Multiple Google accounts?</p>
                        <p class="text-yellow-800 text-sm mb-0">Make sure you sign in with the correct account. You can connect multiple Google accounts later by repeating this process.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Google Sign-In page</p>
                <p class="text-sm">Shows the official Google login screen requesting email/password</p>
            </div>
        </div>
    </div>
    
    <!-- Step 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Grant Permissions</h3>
            <p>Google will ask for permission to let SyncMyDay access your calendar. Review the permissions and click <strong>Allow</strong>.</p>
            
            <p><strong>What permissions does SyncMyDay need?</strong></p>
            <ul>
                <li><strong>View events on all your calendars:</strong> To read event times (not titles/details)</li>
                <li><strong>Add and edit events:</strong> To create blocker events</li>
                <li><strong>Delete events:</strong> To remove blocker events when source events are deleted</li>
            </ul>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Don't worry about privacy!</p>
                        <p class="text-blue-800 text-sm mb-0">Even though we request permission to "view events", we only read the start/end times and status. We never access or store event titles, descriptions, or other details.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Google OAuth consent screen</p>
                <p class="text-sm">Shows the permission request dialog with "Allow" button</p>
            </div>
        </div>
    </div>
    
    <!-- Step 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Select Which Calendars to Sync</h3>
            <p>After granting permissions, you'll be redirected back to SyncMyDay. You'll see a list of all calendars in your Google account. Select which ones you want to make available for syncing.</p>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 mb-1"><strong>Pro Tip:</strong> You can select multiple calendars from the same Google account! This is useful if you have separate calendars for:</p>
                <ul class="text-purple-800 text-sm mb-0">
                    <li>Personal events</li>
                    <li>Family events</li>
                    <li>Shared team calendars</li>
                    <li>Project-specific calendars</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar selection dialog</p>
                <p class="text-sm">Shows checkboxes for each calendar available in the Google account</p>
            </div>
        </div>
    </div>
    
    <!-- Step 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Done! Calendar Connected</h3>
            <p>Your Google Calendar is now connected and will appear in your list of calendar connections with a green "Active" status badge.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ What happens next?</h4>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li>Your calendar is ready to use in sync rules</li>
                    <li>SyncMyDay will receive real-time notifications when events change</li>
                    <li>You can now create sync rules to start synchronizing!</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Connected calendars list showing Google Calendar with "Active" status</p>
                <p class="text-sm">Shows the calendar connections page with the newly connected Google Calendar</p>
            </div>
        </div>
    </div>
</div>

<h2>Next Steps</h2>

<div class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('connections.index') }}" class="block p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Connect Another Calendar</h3>
        </div>
        <p class="mb-0">You need at least 2 calendars to create a sync rule. Connect a work calendar, personal calendar, or another service.</p>
    </a>
    
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Create Your First Sync Rule</h3>
        </div>
        <p class="mb-0">Learn how to set up synchronization between your calendars with filters and custom options.</p>
    </a>
</div>

<!-- Technical Details (Collapsible) -->
<div class="mt-12" x-data="{ open: false }">
    <button @click="open = !open" class="w-full p-6 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl text-left transition flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            <div>
                <h3 class="!mb-0 !mt-0 text-lg font-semibold text-gray-900">Technical Details</h3>
                <p class="text-sm text-gray-600 !mb-0">For developers and technical users</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>OAuth 2.0 Flow</h4>
        <p>SyncMyDay uses Google's OAuth 2.0 authentication with the following scopes:</p>
        <ul>
            <li><code>https://www.googleapis.com/auth/calendar.readonly</code> - Read calendar data</li>
            <li><code>https://www.googleapis.com/auth/calendar.events</code> - Create/modify/delete events</li>
        </ul>
        
        <h4>Real-Time Synchronization</h4>
        <p>We use Google Calendar Push Notifications (webhooks) to receive instant updates:</p>
        <ul>
            <li>A webhook is registered for each connected calendar</li>
            <li>Google sends notifications within seconds of any event changes</li>
            <li>Webhooks are automatically renewed every 7 days</li>
            <li>If webhook delivery fails, we fall back to polling every 15 minutes</li>
        </ul>
        
        <h4>API Quotas</h4>
        <p>Google Calendar API has the following quotas:</p>
        <ul>
            <li><strong>Queries per day:</strong> 1,000,000 (shared across all SyncMyDay users)</li>
            <li><strong>Queries per 100 seconds per user:</strong> 500</li>
        </ul>
        <p>SyncMyDay's architecture is optimized to stay well within these limits for typical usage.</p>
        
        <h4>Token Storage</h4>
        <p>OAuth access tokens and refresh tokens are:</p>
        <ul>
            <li>Encrypted at rest using AES-256</li>
            <li>Stored securely in our database</li>
            <li>Automatically refreshed when they expire (every 60 minutes)</li>
            <li>Immediately deleted when you disconnect the calendar</li>
        </ul>
        
        <h4>Revoking Access</h4>
        <p>You can revoke SyncMyDay's access at any time:</p>
        <ul>
            <li><strong>From SyncMyDay:</strong> Click "Disconnect" on the Calendar Connections page</li>
            <li><strong>From Google:</strong> Visit <a href="https://myaccount.google.com/permissions" target="_blank">myaccount.google.com/permissions</a> and remove SyncMyDay</li>
        </ul>
    </div>
</div>
@endsection

