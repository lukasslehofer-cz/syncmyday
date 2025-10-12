@extends('layouts.help')

@section('title', 'Connect Microsoft 365')

@section('content')
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Connect Microsoft 365</h1>
        <p class="text-lg text-gray-600 !mb-0">Outlook, Office 365, and Exchange Online</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">What's Included</h3>
            <p class="text-blue-800 mb-2">This guide covers all Microsoft calendar services:</p>
            <ul class="text-blue-800 space-y-1 mb-0">
                <li><strong>Outlook.com</strong> - Personal Microsoft accounts (@outlook.com, @hotmail.com, @live.com)</li>
                <li><strong>Microsoft 365</strong> - Work or school accounts</li>
                <li><strong>Office 365</strong> - Business subscriptions</li>
                <li><strong>Exchange Online</strong> - Enterprise email and calendars</li>
            </ul>
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
            <p>From your SyncMyDay dashboard, navigate to <strong>Calendars</strong> in the main menu, or go directly to the <a href="{{ route('connections.index') }}">Calendar Connections page</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard with "Calendars" menu highlighted</p>
                <p class="text-sm">Navigation bar showing the Calendars option</p>
            </div>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Click "Connect Microsoft 365"</h3>
            <p>Find and click the <strong>Microsoft 365</strong> button with the Microsoft logo.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar provider options with Microsoft 365 button</p>
                <p class="text-sm">Shows the connection interface with Microsoft 365 option highlighted</p>
            </div>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Sign in with Microsoft</h3>
            <p>You'll be redirected to Microsoft's secure sign-in page. Enter your Microsoft email address:</p>
            <ul>
                <li><strong>Personal:</strong> @outlook.com, @hotmail.com, @live.com</li>
                <li><strong>Work/School:</strong> Your organization's email (e.g., you@company.com)</li>
            </ul>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Work/School Account?</p>
                        <p class="text-yellow-800 text-sm mb-0">Your organization may need to approve SyncMyDay. Contact your IT administrator if you see an approval request message.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Microsoft sign-in page</p>
                <p class="text-sm">Official Microsoft login screen requesting email address</p>
            </div>
        </div>
    </div>
    
    <!-- Step 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Enter Your Password</h3>
            <p>After entering your email, you'll be prompted for your password. Enter your Microsoft account password.</p>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Your password is safe</p>
                        <p class="text-blue-800 text-sm mb-0">You're entering your password directly on Microsoft's website. SyncMyDay never sees or stores your password.</p>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-600">If you have multi-factor authentication (MFA) enabled, you'll need to approve the sign-in on your phone or authenticator app.</p>
        </div>
    </div>
    
    <!-- Step 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Grant Permissions</h3>
            <p>Microsoft will show a permissions screen asking if you want to let SyncMyDay access your calendar. Click <strong>Accept</strong> to continue.</p>
            
            <p><strong>What permissions does SyncMyDay need?</strong></p>
            <ul>
                <li><strong>Read your calendars:</strong> To detect when you have events scheduled</li>
                <li><strong>Create and modify calendar events:</strong> To create blocker events</li>
                <li><strong>Delete calendar events:</strong> To remove blocker events when needed</li>
                <li><strong>Maintain access to data:</strong> For continuous synchronization</li>
            </ul>
            
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-green-900 font-semibold mb-1">Privacy First</p>
                        <p class="text-green-800 text-sm mb-0">We only read event timing (start/end). We never access event titles, descriptions, locations, or attendee information.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Microsoft permissions consent screen</p>
                <p class="text-sm">Shows the list of requested permissions with "Accept" button</p>
            </div>
        </div>
    </div>
    
    <!-- Step 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Select Calendars to Sync</h3>
            <p>After granting permissions, you'll return to SyncMyDay where you can select which Microsoft calendars you want to use for syncing.</p>
            
            <p>Most accounts will have at least:</p>
            <ul>
                <li><strong>Calendar</strong> - Your main calendar</li>
                <li><strong>Birthdays</strong> - Contact birthdays (you can skip this)</li>
            </ul>
            
            <p>You may also see:</p>
            <ul>
                <li>Shared team calendars</li>
                <li>Resource calendars (meeting rooms, equipment)</li>
                <li>Calendars shared with you by colleagues</li>
            </ul>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar selection interface</p>
                <p class="text-sm">Checkboxes showing available calendars from the Microsoft account</p>
            </div>
        </div>
    </div>
    
    <!-- Step 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Connection Complete!</h3>
            <p>Your Microsoft 365 calendar is now connected and ready to use. You'll see it in your Calendar Connections list with an "Active" status.</p>
            
            <div class="p-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
                <h4 class="text-lg font-semibold text-purple-900 mb-2">✅ You're All Set!</h4>
                <ul class="text-purple-800 space-y-1 mb-0">
                    <li>Real-time synchronization is enabled via webhooks</li>
                    <li>Changes to your calendar are detected within seconds</li>
                    <li>Ready to create sync rules and start syncing!</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Successfully connected Microsoft 365 calendar</p>
                <p class="text-sm">Calendar connections page showing the new Microsoft calendar with green "Active" badge</p>
            </div>
        </div>
    </div>
</div>

<h2>Common Issues</h2>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            "Your organization needs to approve this app"
        </h3>
        <p><strong>Why this happens:</strong> Your IT department has restricted which apps can access company data.</p>
        <p><strong>Solution:</strong></p>
        <ol>
            <li>Contact your IT administrator or help desk</li>
            <li>Ask them to approve "SyncMyDay" in the Microsoft 365 admin center</li>
            <li>Or request an exception for your account</li>
        </ol>
        <p class="text-sm text-gray-600 mb-0">This is common in larger organizations and is a security best practice.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Connection shows "Error" status
        </h3>
        <p><strong>Common causes:</strong></p>
        <ul>
            <li>Your password was changed</li>
            <li>Multi-factor authentication settings changed</li>
            <li>Organization revoked access</li>
        </ul>
        <p><strong>Solution:</strong> Click the "Refresh" button to re-authenticate, or disconnect and reconnect the calendar.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Can't see a shared calendar
        </h3>
        <p>Shared calendars should appear if they're added to your Outlook. If missing:</p>
        <ol>
            <li>Make sure the calendar is visible in Outlook web or app</li>
            <li>Disconnect and reconnect your Microsoft account</li>
            <li>Ensure you have at least "Can view all details" permission</li>
        </ol>
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
        <p class="mb-0">Connect a personal calendar (Google, Apple) to sync with your work calendar.</p>
    </a>
    
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Create a Sync Rule</h3>
        </div>
        <p class="mb-0">Set up your first synchronization between calendars.</p>
    </a>
</div>

<!-- Technical Details -->
<div class="mt-12" x-data="{ open: false }">
    <button @click="open = !open" class="w-full p-6 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl text-left transition flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            <div>
                <h3 class="!mb-0 !mt-0 text-lg font-semibold text-gray-900">Technical Details</h3>
                <p class="text-sm text-gray-600 !mb-0">For developers and IT administrators</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Microsoft Graph API</h4>
        <p>SyncMyDay uses the Microsoft Graph API with these permissions:</p>
        <ul>
            <li><code>Calendars.ReadWrite</code> - Read and write calendar events</li>
            <li><code>offline_access</code> - Maintain access when user is offline</li>
        </ul>
        
        <h4>OAuth 2.0 Authentication</h4>
        <p>We use the standard OAuth 2.0 authorization code flow:</p>
        <ul>
            <li>Supports both personal Microsoft accounts and Azure AD accounts</li>
            <li>Tokens are refreshed automatically every 60 minutes</li>
            <li>Refresh tokens are valid for 90 days (automatically renewed)</li>
        </ul>
        
        <h4>Real-Time Synchronization</h4>
        <p>Microsoft Graph change notifications (webhooks) enable instant sync:</p>
        <ul>
            <li>Subscriptions are created for each connected calendar</li>
            <li>Notifications are received within 2-3 minutes of changes</li>
            <li>Subscriptions are renewed every 3 days automatically</li>
            <li>Fallback polling occurs every 15 minutes if webhooks fail</li>
        </ul>
        
        <h4>Enterprise Admin Consent</h4>
        <p>IT administrators can pre-approve SyncMyDay for all users:</p>
        <ol>
            <li>Go to Azure AD Portal → Enterprise Applications</li>
            <li>Search for "SyncMyDay" or add via App ID</li>
            <li>Grant admin consent for the organization</li>
            <li>Users can then connect without approval prompts</li>
        </ol>
        
        <h4>API Throttling</h4>
        <p>Microsoft Graph has the following limits:</p>
        <ul>
            <li><strong>Per-app:</strong> 10,000 requests per 10 minutes</li>
            <li><strong>Per-user:</strong> 2,000 requests per second</li>
        </ul>
        <p>SyncMyDay's webhook-based architecture minimizes API calls and stays well within limits.</p>
        
        <h4>Data Residency</h4>
        <p>Your calendar data remains in Microsoft's data centers. SyncMyDay only stores:</p>
        <ul>
            <li>Calendar IDs and names</li>
            <li>Event start/end times (no titles or descriptions)</li>
            <li>Encrypted OAuth tokens</li>
        </ul>
    </div>
</div>
@endsection

