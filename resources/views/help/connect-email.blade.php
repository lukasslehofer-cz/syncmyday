@extends('layouts.public')

@section('title', 'Connect Email Calendar')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-green-500 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Connect Email Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Receive calendar invites via email</p>
    </div>
</div>

<div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">What's an Email Calendar?</h3>
            <p class="text-blue-800 mb-2">An <strong>Email Calendar</strong> is a unique way to sync calendars by forwarding calendar invitations (.ics files) via email. This is perfect for calendars that don't have API access or when you want to keep certain calendars completely separate.</p>
            <p class="text-blue-800 mb-0"><strong>How it works:</strong> When events are created in your source calendar, SyncMyDay sends email invitations to a special address. Those invitations automatically show up as blocker events.</p>
        </div>
    </div>
</div>

<h2>When to Use Email Calendars</h2>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-green-900 mb-3">✅ Great For</h3>
        <ul class="text-green-800 space-y-2 mb-0">
            <li>Calendars without API support</li>
            <li>Legacy email clients (Thunderbird, Lotus Notes)</li>
            <li>Receiving blocker invites in your email inbox</li>
            <li>Simple one-way synchronization</li>
            <li>Maximum privacy (events via secure email only)</li>
        </ul>
    </div>
    
    <div class="p-6 bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-yellow-900 mb-3">⚠️ Consider Alternatives If</h3>
        <ul class="text-yellow-800 space-y-2 mb-0">
            <li>You need real-time sync (email has delays)</li>
            <li>Your calendar supports API access (Google, Microsoft)</li>
            <li>You need two-way synchronization</li>
            <li>You want automatic acceptance (email calendars require manual actions)</li>
        </ul>
    </div>
</div>

<h2>Two Ways to Use Email Calendars</h2>

<div class="space-y-6 mb-8">
    <div class="border-2 border-indigo-200 rounded-xl p-6 bg-indigo-50">
        <div class="flex items-start">
            <div class="w-12 h-12 rounded-lg bg-indigo-600 flex items-center justify-center mr-4 flex-shrink-0">
                <span class="text-white font-bold text-2xl">1</span>
            </div>
            <div>
                <h3 class="!mt-0 !mb-2 text-xl font-bold text-indigo-900">Receive Blockers via Email</h3>
                <p class="text-indigo-800 mb-0">When you have events in Google/Microsoft calendar, SyncMyDay sends email invitations to any email address you specify. You can accept these invitations in your email client (Outlook, Thunderbird, etc.) and they'll show up in your calendar.</p>
            </div>
        </div>
    </div>
    
    <div class="border-2 border-purple-200 rounded-xl p-6 bg-purple-50">
        <div class="flex items-start">
            <div class="w-12 h-12 rounded-lg bg-purple-600 flex items-center justify-center mr-4 flex-shrink-0">
                <span class="text-white font-bold text-2xl">2</span>
            </div>
            <div>
                <h3 class="!mt-0 !mb-2 text-xl font-bold text-purple-900">Forward Invites to SyncMyDay</h3>
                <p class="text-purple-800 mb-0">Get a unique email address from SyncMyDay (like <code>abc123@syncmyday.com</code>). When you receive calendar invitations at this address, SyncMyDay automatically creates blocker events in your other connected calendars.</p>
            </div>
        </div>
    </div>
</div>

<h2>Setup Guide</h2>

<div class="space-y-8">
    <!-- Step 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Go to Calendar Connections</h3>
            <p>Navigate to <strong>Calendars</strong> in the menu, or go to the <a href="{{ route('connections.index') }}">Calendar Connections page</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard with Calendars menu highlighted</p>
                <p class="text-sm">Navigation bar showing the Calendars option</p>
            </div>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Click "Connect Email Calendar"</h3>
            <p>Find and click the <strong>Email Calendar</strong> button with the envelope icon.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar providers with Email Calendar option</p>
                <p class="text-sm">Shows the connection interface with Email Calendar highlighted</p>
            </div>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Choose Your Setup Method</h3>
            <p>You'll see two options:</p>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4 class="!mt-0 text-lg font-semibold text-blue-900 mb-2">Option A: Receive Invitations</h4>
                    <p class="text-blue-800 text-sm mb-2">Enter an email address where you want to receive calendar invitations. This email should be connected to a calendar application (Outlook, Thunderbird, Apple Mail, etc.).</p>
                    <p class="text-blue-800 text-sm font-semibold mb-0">Example: <code>mywork@company.com</code></p>
                </div>
                
                <div class="p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4 class="!mt-0 text-lg font-semibold text-purple-900 mb-2">Option B: Get a Unique Address</h4>
                    <p class="text-purple-800 text-sm mb-2">SyncMyDay generates a unique email address for you (like <code>abc123@syncmyday.com</code>). Forward calendar invitations to this address, and we'll process them automatically.</p>
                    <p class="text-purple-800 text-sm font-semibold mb-0">No email input needed—just click "Generate Address"</p>
                </div>
            </div>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mt-4">
                <p class="text-yellow-900 text-sm mb-0"><strong>You can use both methods!</strong> Create one email calendar for receiving invites and another for sending invites to SyncMyDay.</p>
            </div>
        </div>
    </div>
    
    <!-- Step 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Give It a Name</h3>
            <p>Enter a friendly name for this email calendar, such as:</p>
            <ul>
                <li><code>Work Email Calendar</code></li>
                <li><code>Thunderbird Calendar</code></li>
                <li><code>Outlook Desktop</code></li>
            </ul>
            <p>This helps you identify which email calendar is which if you create multiple.</p>
        </div>
    </div>
    
    <!-- Step 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Save and Connect</h3>
            <p>Click <strong>"Connect"</strong> or <strong>"Save"</strong>. Your email calendar will appear in your connections list.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ Email Calendar Connected!</h4>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li>If you chose <strong>Option A</strong>: You'll receive email invitations at your specified address when events are synced</li>
                    <li>If you chose <strong>Option B</strong>: Copy the unique address and set up email forwarding (next step)</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Successfully connected email calendar</p>
                <p class="text-sm">Shows the email calendar in connections list with status</p>
            </div>
        </div>
    </div>
</div>

<h2>Setting Up Email Forwarding (Option B)</h2>

<p>If you chose to get a unique SyncMyDay address, you need to forward calendar invitations to it:</p>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
            </div>
            Gmail
        </h3>
        <ol class="space-y-2 mb-0">
            <li>Go to Gmail Settings (⚙️ → See all settings)</li>
            <li>Click the <strong>"Forwarding and POP/IMAP"</strong> tab</li>
            <li>Click <strong>"Add a forwarding address"</strong></li>
            <li>Enter your SyncMyDay address (e.g., <code>abc123@syncmyday.com</code>)</li>
            <li>Gmail will send a confirmation code to that address (check with us!)</li>
            <li>Once confirmed, set up a filter to forward only calendar invitations</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                </svg>
            </div>
            Outlook / Microsoft 365
        </h3>
        <ol class="space-y-2 mb-0">
            <li>Go to Outlook Settings (⚙️ → View all Outlook settings)</li>
            <li>Navigate to <strong>Mail → Forwarding</strong></li>
            <li>Enable forwarding and enter your SyncMyDay address</li>
            <li>Save changes</li>
            <li>Optionally, create a rule to forward only emails with <code>.ics</code> attachments</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center mr-3">
                <span class="text-white font-bold">📧</span>
            </div>
            Other Email Clients
        </h3>
        <p class="mb-2">Most email clients support forwarding rules. Look for:</p>
        <ul class="mb-0">
            <li><strong>Filters</strong> or <strong>Rules</strong> in settings</li>
            <li>Create a rule: "When message has attachment with <code>.ics</code> extension"</li>
            <li>Action: "Forward to <code>your-syncmyday-address@syncmyday.com</code>"</li>
        </ul>
    </div>
</div>

<h2>Creating Sync Rules with Email Calendars</h2>

<p>Once your email calendar is connected, you can use it in sync rules:</p>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-indigo-900 mb-3">As a Target (Receive Invites)</h3>
        <p class="text-indigo-800 mb-3"><strong>Example:</strong> Google Calendar → Email Calendar</p>
        <ul class="text-indigo-700 space-y-1 mb-0 text-sm">
            <li>Source: Your Google work calendar</li>
            <li>Target: Email calendar with <code>personal@example.com</code></li>
            <li>Result: You receive email invitations for all work events at your personal email</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-purple-200 bg-purple-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-purple-900 mb-3">As a Source (Forward Invites)</h3>
        <p class="text-purple-800 mb-3"><strong>Example:</strong> Email Calendar → Google Calendar</p>
        <ul class="text-purple-700 space-y-1 mb-0 text-sm">
            <li>Source: Email calendar with unique address</li>
            <li>Target: Your Google work calendar</li>
            <li>Result: Calendar invites sent to your unique address appear as blockers in Google</li>
        </ul>
    </div>
</div>

<h2>How Email Calendar Sync Works</h2>

<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl mb-8">
    <h3 class="!mt-0 text-lg font-semibold text-gray-900 mb-4">The Process</h3>
    
    <div class="space-y-4">
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">1</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Event Created in Source Calendar</p>
                <p class="text-gray-700 text-sm mb-0">An event is created in your source calendar (e.g., Google Calendar)</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">2</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">SyncMyDay Detects Change</p>
                <p class="text-gray-700 text-sm mb-0">We receive a webhook notification (for API calendars) or poll for changes (CalDAV/Email)</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">3</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Email Invitation Sent</p>
                <p class="text-gray-700 text-sm mb-0">An email with a <code>.ics</code> attachment is sent to your email calendar address</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">4</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Event Appears in Email Client</p>
                <p class="text-gray-700 text-sm mb-0">Your email client (Outlook, Thunderbird, etc.) receives the invitation and shows it in your calendar</p>
            </div>
        </div>
    </div>
</div>

<h2>Common Questions</h2>

<div class="space-y-4" x-data="{ open: null }">
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q1' ? open = null : open = 'q1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Do I need to manually accept email invitations?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q1'" x-collapse class="px-6 pb-4">
            <p class="mb-0">It depends on your email client settings. Most email clients can be configured to automatically accept calendar invitations. Check your calendar settings for "Automatically accept meeting requests" or similar options.</p>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q2' ? open = null : open = 'q2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>How fast is email calendar sync?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q2'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Email delivery is usually fast (within minutes), but it depends on email server delays. If you need instant synchronization, consider using Google Calendar or Microsoft 365 which support real-time webhooks.</p>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q3' ? open = null : open = 'q3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Can I use the same email address for multiple email calendars?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q3'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Yes! You can create multiple email calendars that all send to the same email address. This is useful if you want to receive blockers from different source calendars in one place.</p>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q4' ? open = null : open = 'q4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>What if I stop receiving emails?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q4'" x-collapse class="px-6 pb-4">
            <p class="mb-2">Check these potential issues:</p>
            <ul class="mb-0">
                <li>Email caught in spam folder</li>
                <li>Email forwarding rule disabled or broken</li>
                <li>Email calendar connection inactive (check Connections page)</li>
                <li>Sync rule paused or deleted</li>
            </ul>
        </div>
    </div>
</div>

<h2>Next Steps</h2>

<div class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Create a Sync Rule</h3>
        </div>
        <p class="mb-0">Set up your first synchronization using your email calendar.</p>
    </a>
    
    <a href="{{ route('help.faq') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Check the FAQ</h3>
        </div>
        <p class="mb-0">More answers to common questions about SyncMyDay.</p>
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
                <p class="text-sm text-gray-600 !mb-0">For developers and technical users</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>iCalendar Format (RFC 5545)</h4>
        <p>Email invitations use the iCalendar (<code>.ics</code>) format:</p>
        <ul>
            <li>Standard MIME type: <code>text/calendar</code></li>
            <li>Contains <code>VEVENT</code> components with event data</li>
            <li>Includes <code>VTIMEZONE</code> for timezone information</li>
            <li>Uses <code>METHOD:REQUEST</code> for invitations</li>
        </ul>
        
        <h4>Email Sending</h4>
        <p>Outgoing email invitations:</p>
        <ul>
            <li>Sent via Laravel Mail system (SMTP, Mailgun, SendGrid, etc.)</li>
            <li>From address: Configured in <code>.env</code> (<code>MAIL_FROM_ADDRESS</code>)</li>
            <li>Reply-to: <code>noreply@syncmyday.com</code></li>
            <li>Attachment: <code>event.ics</code> file</li>
        </ul>
        
        <h4>Email Receiving (Inbound)</h4>
        <p>For unique SyncMyDay addresses:</p>
        <ul>
            <li>IMAP polling: Checks mailbox every minute</li>
            <li>Webhook support: Mailgun, SendGrid, Postmark</li>
            <li>Parses <code>.ics</code> attachments</li>
            <li>Extracts token from recipient address (e.g., <code>abc123</code> from <code>abc123@syncmyday.com</code>)</li>
        </ul>
        
        <h4>Event Processing</h4>
        <ol>
            <li>Parse <code>.ics</code> file for <code>VEVENT</code> components</li>
            <li>Extract <code>DTSTART</code>, <code>DTEND</code>, <code>SUMMARY</code>, <code>STATUS</code></li>
            <li>Convert to internal event format</li>
            <li>Check sync rules and create blocker events</li>
            <li>Mark email as processed (move to "Processed" folder or delete)</li>
        </ol>
        
        <h4>Security</h4>
        <ul>
            <li>Unique addresses are cryptographically generated tokens</li>
            <li>Token validation prevents unauthorized access</li>
            <li>Email content is sanitized before processing</li>
            <li>Only <code>.ics</code> attachments are processed</li>
        </ul>
    </div>
</div>
@endsection

