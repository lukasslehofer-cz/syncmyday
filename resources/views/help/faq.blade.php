@extends('layouts.help')

@section('title', 'Frequently Asked Questions')

@section('content')
<h1>Frequently Asked Questions</h1>

<p class="text-xl text-gray-600 mb-8">Quick answers to common questions about SyncMyDay.</p>

<div class="space-y-6" x-data="{ open: null }">
    <!-- Security & Privacy -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">🔒 Security & Privacy</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-1' ? open = null : open = 'security-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Is my calendar data safe?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-1'" x-collapse class="px-6 pb-4">
                    <p><strong>Yes, absolutely.</strong> We take security seriously:</p>
                    <ul>
                        <li><strong>Minimal data storage:</strong> We only store event start/end times and status (busy/free). We never store event titles, descriptions, or attendees.</li>
                        <li><strong>Encrypted at rest:</strong> All data is encrypted in our database.</li>
                        <li><strong>Encrypted in transit:</strong> All connections use HTTPS/TLS.</li>
                        <li><strong>OAuth authentication:</strong> We use industry-standard OAuth for Google and Microsoft, meaning we never see your password.</li>
                        <li><strong>Access tokens are encrypted:</strong> Any credentials we store are encrypted with strong encryption.</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-2' ? open = null : open = 'security-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>What information do you actually store?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-2'" x-collapse class="px-6 pb-4">
                    <p>For each event we sync, we only store:</p>
                    <ul>
                        <li>Start date and time</li>
                        <li>End date and time</li>
                        <li>Status (busy/free/tentative)</li>
                        <li>Which calendar it came from and which calendars we created blockers in</li>
                        <li>A unique ID to track the event</li>
                    </ul>
                    <p><strong>We never store:</strong> Event titles, descriptions, locations, attendees, notes, or any other details about your events.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-3' ? open = null : open = 'security-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Can you see my calendar events?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-3'" x-collapse class="px-6 pb-4">
                    <p><strong>No.</strong> By design, we never receive or store your event titles or details. When we sync, we only read the timing information (when an event starts and ends) and create simple "Busy" blocker events in your other calendars.</p>
                    <p>Your personal calendar events stay private on your calendar service (Google, Microsoft, etc.).</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-4' ? open = null : open = 'security-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>How do I revoke access?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-4'" x-collapse class="px-6 pb-4">
                    <p>You can disconnect any calendar at any time from your <strong>Calendar Connections</strong> page. This will:</p>
                    <ul>
                        <li>Remove all blocker events created by SyncMyDay in that calendar</li>
                        <li>Delete any sync rules using that calendar</li>
                        <li>Revoke our access to that calendar</li>
                    </ul>
                    <p>You can also revoke access directly from your calendar provider (Google, Microsoft, etc.) in their security settings.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Trial & Payments -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">💳 Trial & Payments</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-1' ? open = null : open = 'payment-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>How does the free trial work?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-1'" x-collapse class="px-6 pb-4">
                    <p>New users get <strong>31 days of full Pro access</strong> completely free:</p>
                    <ul>
                        <li>You'll need to add a payment method during registration</li>
                        <li>No charges during the trial period</li>
                        <li>After 31 days, you'll be charged the annual subscription fee</li>
                        <li>Cancel anytime before trial ends to avoid charges</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-2' ? open = null : open = 'payment-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>How much does SyncMyDay cost?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-2'" x-collapse class="px-6 pb-4">
                    <p>SyncMyDay Pro is billed annually at a competitive rate (see pricing page for your region).</p>
                    <p><strong>What's included:</strong></p>
                    <ul>
                        <li>Unlimited calendar connections</li>
                        <li>Unlimited sync rules</li>
                        <li>Real-time synchronization</li>
                        <li>Advanced filters</li>
                        <li>Priority support</li>
                        <li>All future features</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-3' ? open = null : open = 'payment-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Can I cancel anytime?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-3'" x-collapse class="px-6 pb-4">
                    <p><strong>Yes!</strong> You can cancel your subscription at any time from the Billing page.</p>
                    <p>When you cancel:</p>
                    <ul>
                        <li>You keep access until the end of your current billing period</li>
                        <li>No future charges</li>
                        <li>Your data remains accessible during the paid period</li>
                        <li>You can reactivate anytime</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-4' ? open = null : open = 'payment-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>What payment methods do you accept?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-4'" x-collapse class="px-6 pb-4">
                    <p>We use <strong>Stripe</strong> for secure payment processing. Stripe accepts:</p>
                    <ul>
                        <li>All major credit cards (Visa, Mastercard, American Express)</li>
                        <li>Debit cards</li>
                        <li>Various local payment methods depending on your region</li>
                    </ul>
                    <p>Your payment information is never stored on our servers—it's handled entirely by Stripe's secure platform.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- How It Works -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">⚙️ How It Works</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-1' ? open = null : open = 'how-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>What is a "blocker event"?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-1'" x-collapse class="px-6 pb-4">
                    <p>A <strong>blocker event</strong> is a simple calendar event that shows your time as "busy" without revealing any details about what you're actually doing.</p>
                    <p><strong>Example:</strong> If you have a "Doctor's Appointment" in your personal calendar from 2-3 PM, SyncMyDay creates a blocker event called "Busy" (or whatever title you choose) in your work calendar for the same time slot.</p>
                    <p>This prevents double-booking while keeping your personal events private from colleagues.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-2' ? open = null : open = 'how-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>How fast is the synchronization?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-2'" x-collapse class="px-6 pb-4">
                    <p><strong>Real-time!</strong> For Google Calendar and Microsoft 365, we use webhooks to detect changes instantly:</p>
                    <ul>
                        <li>Changes are typically detected within seconds</li>
                        <li>Blocker events are created/updated within 1-2 minutes</li>
                        <li>No manual syncing needed</li>
                    </ul>
                    <p><strong>Note:</strong> CalDAV and email calendars are polled every 15 minutes, so there may be a slight delay.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-3' ? open = null : open = 'how-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>What happens if I delete an event?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-3'" x-collapse class="px-6 pb-4">
                    <p>If you delete an event from your source calendar, SyncMyDay will automatically:</p>
                    <ul>
                        <li>Detect the deletion (usually within minutes)</li>
                        <li>Remove the corresponding blocker event(s) from your target calendar(s)</li>
                        <li>Clean up our database records</li>
                    </ul>
                    <p>Everything stays in sync automatically.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-4' ? open = null : open = 'how-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Can I customize the blocker event title?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-4'" x-collapse class="px-6 pb-4">
                    <p><strong>Yes!</strong> When creating a sync rule, you can set any title you want for blocker events:</p>
                    <ul>
                        <li>"Busy"</li>
                        <li>"Not Available"</li>
                        <li>"Personal Time"</li>
                        <li>"Meeting"</li>
                        <li>Or anything else</li>
                    </ul>
                    <p>All blocker events created by that rule will use your chosen title.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-5' ? open = null : open = 'how-5'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>What are sync filters?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-5' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-5'" x-collapse class="px-6 pb-4">
                    <p>Filters let you control which events get synced:</p>
                    <ul>
                        <li><strong>Busy status only:</strong> Only sync events marked as "Busy" (skip "Free" or "Tentative")</li>
                        <li><strong>Work hours:</strong> Only sync events during business hours (e.g., 9 AM - 5 PM, Monday-Friday)</li>
                        <li><strong>Ignore all-day events:</strong> Skip full-day events like holidays</li>
                        <li><strong>Custom time ranges:</strong> Define specific hours and days when syncing should happen</li>
                    </ul>
                    <p>Learn more in our <a href="{{ route('help.sync-rules') }}">Creating Sync Rules guide</a>.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Troubleshooting -->
    <div>
        <h2 class="!mt-0 !border-t-0 !pt-0">🔧 Troubleshooting</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-1' ? open = null : open = 'trouble-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Events aren't syncing. What should I check?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-1'" x-collapse class="px-6 pb-4">
                    <p>Try these troubleshooting steps:</p>
                    <ol>
                        <li><strong>Check calendar connections:</strong> Make sure both calendars show as "Active" on the Connections page</li>
                        <li><strong>Verify sync rule status:</strong> Ensure your sync rule isn't paused</li>
                        <li><strong>Check filters:</strong> The event might be filtered out (e.g., all-day event with "ignore all-day" enabled)</li>
                        <li><strong>Check event status:</strong> If you have "busy only" filter, tentative events won't sync</li>
                        <li><strong>Wait a few minutes:</strong> CalDAV calendars update every 15 minutes</li>
                        <li><strong>Refresh connection:</strong> Use the "Refresh" button on the Connections page</li>
                    </ol>
                    <p>If issues persist, contact support at <a href="mailto:support@syncmyday.com">support@syncmyday.com</a></p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-2' ? open = null : open = 'trouble-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>I'm seeing duplicate blocker events</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-2'" x-collapse class="px-6 pb-4">
                    <p>SyncMyDay has built-in duplicate prevention, but duplicates can occur if:</p>
                    <ul>
                        <li>You have multiple sync rules syncing the same calendars</li>
                        <li>You manually created similar events before setting up SyncMyDay</li>
                    </ul>
                    <p><strong>To fix:</strong></p>
                    <ol>
                        <li>Review your sync rules and remove any redundant ones</li>
                        <li>Delete manually-created blocker events (SyncMyDay will recreate them automatically if needed)</li>
                        <li>If the issue persists, disconnect and reconnect your calendars</li>
                    </ol>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-3' ? open = null : open = 'trouble-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Calendar connection shows "Error"</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-3'" x-collapse class="px-6 pb-4">
                    <p>Connection errors usually mean:</p>
                    <ul>
                        <li><strong>OAuth token expired:</strong> Simply click "Refresh" to re-authenticate</li>
                        <li><strong>Password changed:</strong> For CalDAV/Apple, update your credentials</li>
                        <li><strong>Permissions revoked:</strong> You may have removed SyncMyDay's access in your calendar provider settings</li>
                        <li><strong>Calendar provider issue:</strong> Temporary outage on Google/Microsoft's end</li>
                    </ul>
                    <p><strong>Solution:</strong> Try the "Refresh" button first. If that doesn't work, disconnect and reconnect the calendar.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-12 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Still have questions?</h3>
    <p class="text-gray-700 mb-4">We're here to help! Our support team typically responds within 24 hours.</p>
    <a href="mailto:support@syncmyday.com" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Contact Support
    </a>
</div>
@endsection

