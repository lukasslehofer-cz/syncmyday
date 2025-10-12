@extends('layouts.help')

@section('title', 'Connect CalDAV Calendar')

@section('content')
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mr-4 shadow-lg">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Connect CalDAV Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">For Fastmail, Nextcloud, SOGo, and other CalDAV providers</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">What is CalDAV?</h3>
            <p class="text-blue-800 mb-2"><strong>CalDAV</strong> is an open standard protocol for accessing calendar data over the internet. Many calendar services support CalDAV, making it a flexible option for connecting calendars.</p>
            <p class="text-blue-800 mb-0"><strong>Popular CalDAV providers include:</strong> Fastmail, Nextcloud, SOGo, Radicale, Baikal, Synology Calendar, and many others.</p>
        </div>
    </div>
</div>

<h2>What You'll Need</h2>

<div class="grid md:grid-cols-3 gap-4 mb-8">
    <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-indigo-900 mb-2">1. Server URL</h3>
        <p class="text-indigo-800 text-sm mb-0">The CalDAV server address from your provider (e.g., <code>caldav.fastmail.com</code>)</p>
    </div>
    
    <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-purple-900 mb-2">2. Username</h3>
        <p class="text-purple-800 text-sm mb-0">Usually your email address or account username</p>
    </div>
    
    <div class="p-4 bg-gradient-to-br from-pink-50 to-red-50 border border-pink-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-pink-900 mb-2">3. Password</h3>
        <p class="text-pink-800 text-sm mb-0">Your account password or app-specific password</p>
    </div>
</div>

<h2>Popular CalDAV Providers</h2>

<div class="space-y-4 mb-8">
    <!-- Fastmail -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">F</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">Fastmail</h3>
                    <p class="text-sm text-gray-600 !mb-0">Popular email and calendar service</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>Server URL:</strong> <code>https://caldav.fastmail.com</code></li>
                <li><strong>Username:</strong> Your Fastmail email address</li>
                <li><strong>Password:</strong> Your Fastmail password (or app password if 2FA enabled)</li>
            </ul>
            <p class="mt-4 text-sm text-gray-600 mb-0">📚 <a href="https://www.fastmail.help/hc/en-us/articles/1500000278342" target="_blank">Fastmail CalDAV documentation</a></p>
        </div>
    </div>
    
    <!-- Nextcloud -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-blue-600 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">N</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">Nextcloud</h3>
                    <p class="text-sm text-gray-600 !mb-0">Self-hosted or managed Nextcloud</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>Server URL:</strong> <code>https://your-nextcloud.com/remote.php/dav</code></li>
                <li><strong>Username:</strong> Your Nextcloud username</li>
                <li><strong>Password:</strong> Your Nextcloud password or app password</li>
            </ul>
            <p class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800 mb-0">
                <strong>Tip:</strong> For better security, generate an app-specific password in Nextcloud: Settings → Security → Devices & sessions → Create new app password
            </p>
        </div>
    </div>
    
    <!-- SOGo -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-green-600 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">SOGo</h3>
                    <p class="text-sm text-gray-600 !mb-0">Open-source groupware server</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>Server URL:</strong> <code>https://your-sogo-server.com/SOGo/dav</code></li>
                <li><strong>Username:</strong> Your SOGo username (often email@domain.com)</li>
                <li><strong>Password:</strong> Your SOGo password</li>
            </ul>
        </div>
    </div>
    
    <!-- Synology -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-orange-500 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">Synology Calendar</h3>
                    <p class="text-sm text-gray-600 !mb-0">Synology NAS Calendar package</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>Server URL:</strong> <code>https://your-nas-address.com:5001/calendar</code></li>
                <li><strong>Username:</strong> Your Synology DSM username</li>
                <li><strong>Password:</strong> Your Synology DSM password</li>
            </ul>
            <p class="mt-4 text-sm text-gray-600 mb-0">Make sure Calendar package is installed and CalDAV is enabled in Calendar settings.</p>
        </div>
    </div>
</div>

<h2>Step-by-Step Guide</h2>

<div class="space-y-8">
    <!-- Step 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Gather Your CalDAV Details</h3>
            <p>Before connecting, you need to find your CalDAV server information. This is typically found in:</p>
            <ul>
                <li>Your provider's help documentation</li>
                <li>Account settings page</li>
                <li>Email from your provider when you signed up</li>
            </ul>
            
            <p>You'll need:</p>
            <ol>
                <li><strong>CalDAV Server URL</strong> - e.g., <code>caldav.example.com</code> or <code>https://example.com/dav</code></li>
                <li><strong>Username</strong> - Usually your email address</li>
                <li><strong>Password</strong> - Your account password or app-specific password</li>
            </ol>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <p class="text-blue-900 text-sm mb-0"><strong>Can't find your CalDAV details?</strong> Contact your calendar provider's support or check their documentation for "CalDAV" or "third-party calendar access".</p>
            </div>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Go to Calendar Connections</h3>
            <p>In SyncMyDay, navigate to <strong>Calendars</strong> in the menu, or go to the <a href="{{ route('connections.index') }}">Calendar Connections page</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard with Calendars menu</p>
                <p class="text-sm">Navigation showing the Calendars option</p>
            </div>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Click "Connect CalDAV"</h3>
            <p>Find and click the <strong>CalDAV (Generic)</strong> button.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar provider options with CalDAV button</p>
                <p class="text-sm">Shows the CalDAV connection option</p>
            </div>
        </div>
    </div>
    
    <!-- Step 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Enter Your CalDAV Credentials</h3>
            <p>Fill in the connection form with the details you gathered:</p>
            
            <div class="space-y-4 mb-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">CalDAV Server URL</h4>
                    <p class="text-sm text-gray-700 mb-2">Enter the full CalDAV server address. Examples:</p>
                    <ul class="text-sm text-gray-600 space-y-1 mb-0">
                        <li><code>https://caldav.fastmail.com</code></li>
                        <li><code>https://nextcloud.example.com/remote.php/dav</code></li>
                        <li><code>caldav.example.com</code> (we'll add https:// automatically)</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">Username</h4>
                    <p class="text-sm text-gray-600 mb-0">Usually your email address (e.g., <code>you@example.com</code>) or username</p>
                </div>
                
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">Password</h4>
                    <p class="text-sm text-gray-600 mb-0">Your account password or app-specific password (if your provider requires it)</p>
                </div>
            </div>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">App-Specific Passwords</p>
                        <p class="text-yellow-800 text-sm mb-0">Some providers (like Fastmail with 2FA) require app-specific passwords instead of your regular password. Check your provider's documentation.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: CalDAV connection form</p>
                <p class="text-sm">Form with fields for server URL, username, and password</p>
            </div>
        </div>
    </div>
    
    <!-- Step 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Test Connection</h3>
            <p>Click <strong>"Connect"</strong> or <strong>"Test Connection"</strong>. SyncMyDay will:</p>
            <ol>
                <li>Verify the server URL is reachable</li>
                <li>Authenticate with your credentials</li>
                <li>Discover available calendars</li>
            </ol>
            
            <p>This usually takes 5-10 seconds.</p>
        </div>
    </div>
    
    <!-- Step 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Select Calendars</h3>
            <p>Once connected, you'll see a list of all calendars available on your CalDAV server. Select which ones you want to sync.</p>
            
            <p>Typical calendars you might see:</p>
            <ul>
                <li><strong>Personal</strong> - Your main calendar</li>
                <li><strong>Work</strong> - Work-related events</li>
                <li><strong>Family</strong> - Shared family calendar</li>
                <li>Any custom calendars you've created</li>
            </ul>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar selection with CalDAV calendars</p>
                <p class="text-sm">Shows available calendars with checkboxes</p>
            </div>
        </div>
    </div>
    
    <!-- Step 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Connection Complete!</h3>
            <p>Your CalDAV calendar is now connected and ready to use!</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ What's Next?</h4>
                <ul class="text-green-800 space-y-1 mb-2">
                    <li>Your CalDAV calendar is ready for sync rules</li>
                    <li>Events will sync every 15 minutes</li>
                    <li>You can now create sync rules!</li>
                </ul>
                <p class="text-green-800 text-sm mb-0"><strong>Note:</strong> CalDAV doesn't support real-time webhooks, so we poll for changes every 15 minutes.</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Successfully connected CalDAV calendar</p>
                <p class="text-sm">Shows the calendar with "Active" status in connections list</p>
            </div>
        </div>
    </div>
</div>

<h2>Troubleshooting</h2>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            "Connection failed" or "Unable to connect"
        </h3>
        <p><strong>Check these common issues:</strong></p>
        <ol>
            <li><strong>Server URL format:</strong> Make sure it includes <code>https://</code> or let us add it automatically</li>
            <li><strong>Trailing slashes:</strong> Try with and without trailing slash (<code>/</code>) at the end</li>
            <li><strong>Port number:</strong> Some servers need explicit port (e.g., <code>:8443</code>)</li>
            <li><strong>Self-signed certificates:</strong> If using self-hosted, ensure your SSL certificate is valid</li>
            <li><strong>Firewall:</strong> Make sure your CalDAV server is accessible from the internet</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            "Authentication failed" or "Invalid credentials"
        </h3>
        <p><strong>Common causes:</strong></p>
        <ul>
            <li>Incorrect username or password</li>
            <li>Need to use app-specific password (if 2FA is enabled)</li>
            <li>Username format wrong (try with and without @domain.com)</li>
            <li>Account locked or disabled</li>
        </ul>
        <p><strong>Solution:</strong> Double-check credentials, generate app-specific password if needed, or contact your provider.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            No calendars found
        </h3>
        <p>If connection succeeds but no calendars appear:</p>
        <ul>
            <li>Make sure you have at least one calendar in your account</li>
            <li>Check that the calendars aren't hidden or archived</li>
            <li>Try creating a test calendar in your provider's web interface</li>
            <li>Some CalDAV servers require specific principal URLs (contact support)</li>
        </ul>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Sync is slow
        </h3>
        <p>CalDAV calendars sync every 15 minutes, which is slower than Google/Microsoft:</p>
        <ul>
            <li>This is normal due to CalDAV protocol limitations</li>
            <li>No real-time push notifications available</li>
            <li>Polling frequency balances responsiveness with server load</li>
        </ul>
        <p class="mb-0"><strong>Need faster sync?</strong> Consider using Google Calendar or Microsoft 365, which support real-time webhooks.</p>
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
        <p class="mb-0">Connect a second calendar to start syncing events.</p>
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
        <p class="mb-0">Set up synchronization between your calendars.</p>
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
                <p class="text-sm text-gray-600 !mb-0">For developers and system administrators</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>CalDAV Protocol (RFC 4791)</h4>
        <p>SyncMyDay implements the CalDAV standard using:</p>
        <ul>
            <li><strong>PROPFIND:</strong> Discovering calendars and calendar collections</li>
            <li><strong>REPORT:</strong> Querying calendar data (calendar-query)</li>
            <li><strong>GET:</strong> Fetching individual calendar objects (iCalendar format)</li>
            <li><strong>PUT:</strong> Creating and updating events</li>
            <li><strong>DELETE:</strong> Removing events</li>
        </ul>
        
        <h4>Service Discovery</h4>
        <p>We use WebDAV service discovery to find calendar collections:</p>
        <ol>
            <li>Perform PROPFIND on the provided URL</li>
            <li>Look for <code>calendar-home-set</code> property</li>
            <li>Query the home set for calendar collections</li>
            <li>Present available calendars to user</li>
        </ol>
        
        <h4>Authentication</h4>
        <ul>
            <li><strong>Basic Auth:</strong> Standard HTTP Basic Authentication over HTTPS</li>
            <li><strong>Digest Auth:</strong> Supported if server requires it</li>
            <li>Credentials are encrypted at rest with AES-256</li>
        </ul>
        
        <h4>Polling Strategy</h4>
        <p>Since CalDAV doesn't support push notifications:</p>
        <ul>
            <li>Poll every 15 minutes for changes</li>
            <li>Use <code>getctag</code> (collection tag) to detect changes efficiently</li>
            <li>Only fetch changed events using <code>getetag</code></li>
            <li>Minimize bandwidth and server load</li>
        </ul>
        
        <h4>iCalendar Format</h4>
        <p>Events are exchanged in RFC 5545 iCalendar format:</p>
        <ul>
            <li>Parse <code>VEVENT</code> components</li>
            <li>Extract <code>DTSTART</code>, <code>DTEND</code>, <code>STATUS</code></li>
            <li>Handle recurrence rules (<code>RRULE</code>)</li>
            <li>Support timezone conversion (<code>VTIMEZONE</code>)</li>
        </ul>
        
        <h4>Known Limitations</h4>
        <ul>
            <li><strong>No real-time sync:</strong> 15-minute polling interval</li>
            <li><strong>Server dependencies:</strong> Requires proper CalDAV implementation</li>
            <li><strong>Firewall restrictions:</strong> Server must be internet-accessible</li>
        </ul>
    </div>
</div>
@endsection

