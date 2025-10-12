@extends('layouts.help')

@section('title', 'Connect Apple iCloud Calendar')

@section('content')
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-gray-800 to-black flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Connect Apple iCloud Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Using CalDAV with App-Specific Password</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-blue-900 mb-2 leading-tight">Important: App-Specific Password Required</h3>
            <p class="text-blue-800 mb-2">Apple requires an <strong>App-Specific Password</strong> for third-party apps when you have Two-Factor Authentication enabled (which is required for all Apple accounts).</p>
            <p class="text-blue-800 mb-0"><strong>Don't worry!</strong> This guide will walk you through generating one. It takes about 5 minutes.</p>
        </div>
    </div>
</div>

<h2>Prerequisites</h2>

<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl mb-8">
    <p class="mb-3">Before you begin, make sure you have:</p>
    <ul class="space-y-2 mb-0">
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>An iCloud account</strong> (Apple ID) with calendars</span>
        </li>
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Two-Factor Authentication enabled</strong> (enabled by default for all accounts)</span>
        </li>
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Access to appleid.apple.com</strong> to generate an app-specific password</span>
        </li>
    </ul>
</div>

<h2>Step-by-Step Guide</h2>

<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
    <p class="font-semibold text-yellow-900 mb-2">This guide has 2 parts:</p>
    <ol class="text-yellow-800 mb-0 space-y-1">
        <li><strong>Part A:</strong> Generate an App-Specific Password from Apple (5 minutes)</li>
        <li><strong>Part B:</strong> Connect your iCloud calendar in SyncMyDay (2 minutes)</li>
    </ol>
</div>

<h3 class="text-2xl font-bold text-indigo-600 mb-4">Part A: Generate App-Specific Password</h3>

<div class="space-y-8 mb-12">
    <!-- Step 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h4 class="!mt-0">Go to Apple ID Settings</h4>
            <p>Open your browser and go to <a href="https://appleid.apple.com" target="_blank" class="font-semibold">appleid.apple.com</a></p>
            <p>Sign in with your Apple ID email and password.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Apple ID login page at appleid.apple.com</p>
                <p class="text-sm">Shows the Apple ID sign-in form</p>
            </div>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h4 class="!mt-0">Authenticate with Two-Factor</h4>
            <p>Apple will send a verification code to your trusted devices (iPhone, iPad, Mac). Enter the 6-digit code when prompted.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Two-factor authentication code entry</p>
                <p class="text-sm">Shows the 6-digit verification code input</p>
            </div>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h4 class="!mt-0">Navigate to Security Section</h4>
            <p>Once logged in, find and click on the <strong>"Sign-In and Security"</strong> section.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Apple ID account page with "Sign-In and Security" highlighted</p>
                <p class="text-sm">Shows the main Apple ID dashboard</p>
            </div>
        </div>
    </div>
    
    <!-- Step 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h4 class="!mt-0">Click "App-Specific Passwords"</h4>
            <p>In the Security section, scroll down until you find <strong>"App-Specific Passwords"</strong> and click on it.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Security settings with "App-Specific Passwords" option</p>
                <p class="text-sm">Shows the App-Specific Passwords menu item</p>
            </div>
        </div>
    </div>
    
    <!-- Step 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h4 class="!mt-0">Generate New Password</h4>
            <p>Click the <strong>"Generate an app-specific password"</strong> button (or the + icon).</p>
            <p>When prompted for a name, enter something descriptive like:</p>
            <ul class="mb-4">
                <li><code>SyncMyDay</code></li>
                <li><code>SyncMyDay Calendar Sync</code></li>
            </ul>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 text-sm mb-0"><strong>Tip:</strong> The name helps you remember what this password is for, especially if you need to revoke it later.</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dialog for entering app-specific password name</p>
                <p class="text-sm">Shows the input field with "SyncMyDay" entered</p>
            </div>
        </div>
    </div>
    
    <!-- Step 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h4 class="!mt-0">Copy the Password</h4>
            <p>Apple will generate a password that looks like this: <code>abcd-efgh-ijkl-mnop</code></p>
            
            <div class="p-4 bg-red-50 border-2 border-red-300 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-900 mb-1">⚠️ IMPORTANT: Copy this password NOW!</p>
                        <p class="text-red-800 text-sm mb-0">Apple will only show this password once. If you lose it, you'll need to generate a new one. Copy it to your clipboard or paste it directly into SyncMyDay in the next step.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Generated app-specific password displayed</p>
                <p class="text-sm">Shows the password in groups of 4 characters with copy button</p>
            </div>
        </div>
    </div>
</div>

<h3 class="text-2xl font-bold text-purple-600 mb-4">Part B: Connect in SyncMyDay</h3>

<div class="space-y-8">
    <!-- Step 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h4 class="!mt-0">Go to Calendar Connections</h4>
            <p>Return to SyncMyDay and navigate to <strong>Calendars</strong> in the menu, or go directly to the <a href="{{ route('connections.index') }}">Calendar Connections page</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: SyncMyDay dashboard with Calendars menu</p>
                <p class="text-sm">Navigation showing the Calendars option</p>
            </div>
        </div>
    </div>
    
    <!-- Step 8 -->
    <div class="flex items-start">
        <span class="step-number">8</span>
        <div class="flex-1">
            <h4 class="!mt-0">Click "Connect Apple iCloud"</h4>
            <p>Find and click the <strong>Apple iCloud</strong> button with the Apple logo.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar providers with Apple iCloud option</p>
                <p class="text-sm">Shows the Apple iCloud connection button</p>
            </div>
        </div>
    </div>
    
    <!-- Step 9 -->
    <div class="flex items-start">
        <span class="step-number">9</span>
        <div class="flex-1">
            <h4 class="!mt-0">Enter Your Credentials</h4>
            <p>Fill in the connection form:</p>
            <ul>
                <li><strong>Email:</strong> Your full Apple ID email (e.g., your.email@icloud.com)</li>
                <li><strong>Password:</strong> Paste the app-specific password you copied from Apple (including the dashes, or without—both work)</li>
            </ul>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Use the App-Specific Password</p>
                        <p class="text-blue-800 text-sm mb-0">Do NOT use your regular Apple ID password. Use the app-specific password you just generated. Your regular password won't work.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: iCloud connection form with email and password fields</p>
                <p class="text-sm">Shows the credential input form</p>
            </div>
        </div>
    </div>
    
    <!-- Step 10 -->
    <div class="flex items-start">
        <span class="step-number">10</span>
        <div class="flex-1">
            <h4 class="!mt-0">Select Calendars</h4>
            <p>After connecting, SyncMyDay will fetch your iCloud calendars. Select which ones you want to sync.</p>
            <p>Common iCloud calendars include:</p>
            <ul>
                <li><strong>Home</strong> - Your default personal calendar</li>
                <li><strong>Work</strong> - If you've created a work calendar</li>
                <li><strong>Family</strong> - Shared family calendar</li>
                <li>Any custom calendars you've created</li>
            </ul>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Calendar selection with iCloud calendars</p>
                <p class="text-sm">Shows checkboxes for each available iCloud calendar</p>
            </div>
        </div>
    </div>
    
    <!-- Step 11 -->
    <div class="flex items-start">
        <span class="step-number">11</span>
        <div class="flex-1">
            <h4 class="!mt-0">All Done!</h4>
            <p>Your Apple iCloud calendar is now connected! You'll see it in your calendar connections list.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ What's Next?</h4>
                <ul class="text-green-800 space-y-1 mb-2">
                    <li>Your iCloud calendar is ready to use in sync rules</li>
                    <li>Events will sync every 15 minutes (CalDAV limitation)</li>
                    <li>You can now create sync rules to keep calendars in sync</li>
                </ul>
                <p class="text-green-800 text-sm mb-0"><strong>Note:</strong> iCloud uses CalDAV protocol, which doesn't support real-time webhooks. We poll for changes every 15 minutes to stay up-to-date.</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Successfully connected iCloud calendar</p>
                <p class="text-sm">Shows the calendar in the connections list with "Active" status</p>
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
            "Authentication failed" or "Invalid credentials"
        </h3>
        <p><strong>Common causes:</strong></p>
        <ul>
            <li>Used your regular Apple ID password instead of app-specific password</li>
            <li>Typo in email address or password</li>
            <li>App-specific password was revoked</li>
        </ul>
        <p><strong>Solution:</strong></p>
        <ol>
            <li>Double-check you're using the app-specific password, not your regular password</li>
            <li>Generate a new app-specific password and try again</li>
            <li>Make sure your email is correct (include @icloud.com or @me.com)</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            I don't see "App-Specific Passwords" option
        </h3>
        <p>This usually means Two-Factor Authentication isn't enabled on your account.</p>
        <p><strong>Solution:</strong></p>
        <ol>
            <li>Go to Apple ID settings at appleid.apple.com</li>
            <li>Navigate to Sign-In and Security</li>
            <li>Enable Two-Factor Authentication</li>
            <li>Once enabled, the App-Specific Passwords option will appear</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Sync is slow (15-minute delay)
        </h3>
        <p>This is normal for iCloud calendars. Apple's CalDAV protocol doesn't support real-time webhooks like Google or Microsoft.</p>
        <p><strong>Why?</strong> We poll iCloud every 15 minutes to check for changes. This is the standard approach for CalDAV providers and balances responsiveness with server load.</p>
        <p><strong>Alternative:</strong> If you need instant synchronization, consider using Google Calendar or Microsoft 365 instead, which both support real-time webhooks.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            How do I revoke an app-specific password?
        </h3>
        <p>If you need to revoke access:</p>
        <ol>
            <li>Go to appleid.apple.com</li>
            <li>Sign in and navigate to Sign-In and Security</li>
            <li>Click on App-Specific Passwords</li>
            <li>Find "SyncMyDay" in the list</li>
            <li>Click "Revoke" next to it</li>
        </ol>
        <p class="mb-0">You can also disconnect the calendar from SyncMyDay's Calendar Connections page, and we'll stop using the credentials.</p>
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
        <p class="mb-0">Connect a work calendar (Google, Microsoft) to sync with your personal iCloud calendar.</p>
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
        <p class="mb-0">Start syncing events between your calendars.</p>
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
        <h4>CalDAV Protocol</h4>
        <p>Apple iCloud uses the CalDAV protocol (RFC 4791):</p>
        <ul>
            <li><strong>Server URL:</strong> <code>https://caldav.icloud.com</code></li>
            <li><strong>Principal URL:</strong> Automatically discovered via DAV service discovery</li>
            <li><strong>Authentication:</strong> Basic Auth with Apple ID + app-specific password</li>
        </ul>
        
        <h4>Polling Interval</h4>
        <p>Since CalDAV doesn't support push notifications, we poll every 15 minutes:</p>
        <ul>
            <li>Uses PROPFIND requests to check calendar metadata</li>
            <li>Only downloads changed events (using ETags)</li>
            <li>Minimizes bandwidth and respects Apple's rate limits</li>
        </ul>
        
        <h4>Credential Storage</h4>
        <ul>
            <li>App-specific passwords are encrypted with AES-256</li>
            <li>Stored securely in our database</li>
            <li>Never transmitted in plain text (always HTTPS)</li>
            <li>Immediately deleted when calendar is disconnected</li>
        </ul>
        
        <h4>Compatibility</h4>
        <p>This connection method works with:</p>
        <ul>
            <li>iCloud.com calendars</li>
            <li>Calendars synced to iCloud from iOS devices</li>
            <li>Calendars synced from macOS Calendar app</li>
            <li>Shared iCloud calendars (if you have write permission)</li>
        </ul>
        
        <h4>Limitations</h4>
        <ul>
            <li><strong>No real-time sync:</strong> 15-minute polling interval</li>
            <li><strong>App-specific password required:</strong> Cannot use regular password</li>
            <li><strong>Two-factor authentication required:</strong> All iCloud accounts now require 2FA</li>
        </ul>
    </div>
</div>
@endsection

