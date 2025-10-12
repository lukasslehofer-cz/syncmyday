@extends('layouts.help')

@section('title', 'Creating Sync Rules')

@section('content')
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl gradient-bg flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Creating Sync Rules</h1>
        <p class="text-lg text-gray-600 !mb-0">Set up automatic calendar synchronization</p>
    </div>
</div>

<div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-indigo-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-indigo-900 mb-2">What's a Sync Rule?</h3>
            <p class="text-indigo-800 mb-2">A <strong>sync rule</strong> defines how events from one calendar (the <em>source</em>) should be synchronized to another calendar (the <em>target</em>) as blocker events.</p>
            <p class="text-indigo-800 mb-0"><strong>Example:</strong> "Sync all busy events from my personal Google Calendar to my work Outlook calendar as 'Busy' blockers."</p>
        </div>
    </div>
</div>

<h2>Before You Start</h2>

<div class="p-6 bg-yellow-50 border border-yellow-200 rounded-xl mb-8">
    <p class="font-semibold text-yellow-900 mb-2">✅ Make sure you have:</p>
    <ul class="text-yellow-800 space-y-1 mb-0">
        <li><strong>At least 2 calendars connected</strong> - You need a source calendar and a target calendar</li>
        <li><strong>Both calendars showing "Active" status</strong> - Check the Calendar Connections page</li>
        <li><strong>Events in your source calendar</strong> - For testing the sync</li>
    </ul>
</div>

<h2>Step-by-Step Guide</h2>

<div class="space-y-8">
    <!-- Step 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Go to Sync Rules</h3>
            <p>Navigate to <strong>Sync Rules</strong> in the main menu, or go directly to the Sync Rules page from your dashboard.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard with "Sync Rules" menu highlighted</p>
                <p class="text-sm">Navigation showing the Sync Rules option</p>
            </div>
        </div>
    </div>
    
    <!-- Step 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Click "Create New Sync Rule"</h3>
            <p>On the Sync Rules page, click the <strong>"Create New Sync Rule"</strong> or <strong>"+ New Rule"</strong> button.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Sync Rules page with "Create New" button</p>
                <p class="text-sm">Shows the button to create a new sync rule</p>
            </div>
        </div>
    </div>
    
    <!-- Step 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Select Source Calendar</h3>
            <p>Choose which calendar's events you want to sync <strong>FROM</strong>.</p>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <p class="text-blue-900 text-sm mb-2"><strong>What's a Source Calendar?</strong></p>
                <p class="text-blue-800 text-sm mb-0">The source calendar is where your real events are. When you create, update, or delete events in this calendar, SyncMyDay will automatically create or update blocker events in your target calendar(s).</p>
            </div>
            
            <p><strong>Common examples:</strong></p>
            <ul>
                <li><strong>Personal calendar</strong> (source) → Work calendar (target): Block work time when you have personal appointments</li>
                <li><strong>Work calendar</strong> (source) → Personal calendar (target): Block personal time when you're in work meetings</li>
            </ul>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Source calendar dropdown showing connected calendars</p>
                <p class="text-sm">Dropdown menu listing all available source calendars</p>
            </div>
        </div>
    </div>
    
    <!-- Step 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Select Target Calendar(s)</h3>
            <p>Choose one or more calendars where blocker events should be created.</p>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 text-sm mb-2"><strong>Pro Tip: Multiple Targets</strong></p>
                <p class="text-purple-800 text-sm mb-0">You can select multiple target calendars! For example, sync your personal events to both your work Google Calendar AND your work Outlook calendar simultaneously.</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Target calendar selection with checkboxes</p>
                <p class="text-sm">Shows multiple calendars that can be selected as targets</p>
            </div>
        </div>
    </div>
    
    <!-- Step 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Configure Blocker Event Title</h3>
            <p>Enter the text that will appear as the title for all blocker events created by this rule.</p>
            
            <p><strong>Popular titles:</strong></p>
            <ul>
                <li><code>Busy</code> - Simple and universal</li>
                <li><code>Personal Time</code> - Indicates private time</li>
                <li><code>Not Available</code> - Clear unavailability</li>
                <li><code>Meeting</code> - Generic placeholder</li>
                <li><code>🔒 Private</code> - With emoji for visual distinction</li>
            </ul>
            
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                <p class="text-green-900 text-sm mb-1"><strong>Remember:</strong></p>
                <p class="text-green-800 text-sm mb-0">The blocker title is what others will see in your calendar. Choose something appropriate for your context (work, personal, etc.).</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Blocker title input field</p>
                <p class="text-sm">Text field showing example blocker title "Busy"</p>
            </div>
        </div>
    </div>
    
    <!-- Step 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Set Up Filters (Optional but Recommended)</h3>
            <p>Filters control <strong>which events</strong> get synced. This is where you can fine-tune your synchronization.</p>
            
            <h4 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Available Filters:</h4>
            
            <!-- Busy Events Only -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-gray-900">Sync Only Busy Events</h4>
                        <p class="text-gray-700 text-sm mb-2">Only sync events that are marked as "Busy". Skip events marked as "Free" or "Tentative".</p>
                        <p class="text-gray-600 text-xs mb-0"><strong>Use case:</strong> Prevent tentative meetings from blocking your other calendars until they're confirmed.</p>
                    </div>
                </div>
            </div>
            
            <!-- Ignore All-Day Events -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-gray-900">Ignore All-Day Events</h4>
                        <p class="text-gray-700 text-sm mb-2">Don't sync all-day events like holidays, birthdays, or out-of-office days.</p>
                        <p class="text-gray-600 text-xs mb-0"><strong>Use case:</strong> All-day events often don't need to block your other calendars (e.g., public holidays).</p>
                    </div>
                </div>
            </div>
            
            <!-- Work Hours Only -->
            <div class="mb-6 p-4 border-2 border-indigo-300 rounded-lg bg-indigo-50">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-indigo-900">Work Hours Only</h4>
                        <p class="text-indigo-800 text-sm mb-3">Only sync events that fall within specific hours and days.</p>
                        
                        <div class="grid md:grid-cols-2 gap-3">
                            <div class="p-3 bg-white border border-indigo-200 rounded">
                                <p class="text-xs font-semibold text-indigo-900 mb-1">Hours</p>
                                <p class="text-sm text-indigo-700 mb-0">9:00 AM - 5:00 PM</p>
                            </div>
                            <div class="p-3 bg-white border border-indigo-200 rounded">
                                <p class="text-xs font-semibold text-indigo-900 mb-1">Days</p>
                                <p class="text-sm text-indigo-700 mb-0">Mon, Tue, Wed, Thu, Fri</p>
                            </div>
                        </div>
                        
                        <p class="text-indigo-700 text-xs mt-3 mb-0"><strong>Use case:</strong> Only block your work calendar during work hours. Personal events in the evening or weekends won't sync.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Filter options with checkboxes and time selectors</p>
                <p class="text-sm">Shows the filter configuration interface</p>
            </div>
        </div>
    </div>
    
    <!-- Step 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Review and Save</h3>
            <p>Review your sync rule settings and click <strong>"Create Sync Rule"</strong> or <strong>"Save"</strong>.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ Sync Rule Created!</h4>
                <p class="text-green-800 mb-2">Your calendars are now syncing automatically. What happens next:</p>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li><strong>Initial sync:</strong> All existing events from your source calendar will be synced within minutes</li>
                    <li><strong>Real-time updates:</strong> New, updated, or deleted events will sync automatically</li>
                    <li><strong>You can pause or edit:</strong> The sync rule anytime from the Sync Rules page</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Confirmation page showing active sync rule</p>
                <p class="text-sm">Shows the newly created sync rule with its settings</p>
            </div>
        </div>
    </div>
</div>

<h2>Common Sync Rule Examples</h2>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 border-2 border-blue-200 bg-blue-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-blue-900 mb-3">🏠 Personal → Work</h3>
        <ul class="text-blue-800 text-sm space-y-2 mb-0">
            <li><strong>Source:</strong> Personal Google Calendar</li>
            <li><strong>Target:</strong> Work Outlook Calendar</li>
            <li><strong>Title:</strong> "Personal Time"</li>
            <li><strong>Filters:</strong> Work hours only (9-5, Mon-Fri), Busy events only</li>
            <li><strong>Result:</strong> Colleagues see you're busy during personal appointments, but only during work hours</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-purple-200 bg-purple-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-purple-900 mb-3">💼 Work → Personal</h3>
        <ul class="text-purple-800 text-sm space-y-2 mb-0">
            <li><strong>Source:</strong> Work Outlook Calendar</li>
            <li><strong>Target:</strong> Personal Google Calendar</li>
            <li><strong>Title:</strong> "Work Meeting"</li>
            <li><strong>Filters:</strong> Ignore all-day events, Busy events only</li>
            <li><strong>Result:</strong> Your personal calendar shows when you have work meetings (useful for family planning)</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-green-200 bg-green-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-green-900 mb-3">👨‍👩‍👧 Family Calendar → Work</h3>
        <ul class="text-green-800 text-sm space-y-2 mb-0">
            <li><strong>Source:</strong> Shared family Google Calendar</li>
            <li><strong>Target:</strong> Work calendar</li>
            <li><strong>Title:</strong> "Family Commitment"</li>
            <li><strong>Filters:</strong> Work hours only</li>
            <li><strong>Result:</strong> Team knows you're unavailable for family events like school pickups</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-orange-200 bg-orange-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-orange-900 mb-3">📅 Multiple Personal → Work</h3>
        <ul class="text-orange-800 text-sm space-y-2 mb-0">
            <li><strong>Source:</strong> Personal calendar</li>
            <li><strong>Targets:</strong> Work Google + Work Outlook + Work iCloud</li>
            <li><strong>Title:</strong> "Busy"</li>
            <li><strong>Filters:</strong> Work hours, Busy only</li>
            <li><strong>Result:</strong> Block all your work calendars at once</li>
        </ul>
    </div>
</div>

<h2>Managing Your Sync Rules</h2>

<div class="space-y-4">
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pause a Sync Rule
        </h3>
        <p class="text-gray-700 mb-0">Need to temporarily stop syncing? Click the "Pause" button on any sync rule. Blocker events will remain, but new ones won't be created until you resume. Great for vacations or project changes.</p>
    </div>
    
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit a Sync Rule
        </h3>
        <p class="text-gray-700 mb-0">Click "Edit" to change any settings—filters, blocker title, target calendars, etc. Changes apply to new blocker events. Existing blockers remain unchanged unless the source event changes.</p>
    </div>
    
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Delete a Sync Rule
        </h3>
        <p class="text-gray-700 mb-0">Click "Delete" to permanently remove a sync rule. <strong>All blocker events</strong> created by this rule will be automatically deleted from your target calendars. This action cannot be undone.</p>
    </div>
</div>

<h2>Troubleshooting</h2>

<div class="space-y-4" x-data="{ open: null }">
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble1' ? open = null : open = 'trouble1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Blocker events aren't appearing</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble1'" x-collapse class="px-6 pb-4">
            <p class="mb-2"><strong>Check these:</strong></p>
            <ol class="space-y-2 mb-0">
                <li>Sync rule status is "Active" (not paused)</li>
                <li>Source and target calendars show "Active" status</li>
                <li>Event meets filter criteria (check busy status, all-day, work hours)</li>
                <li>Wait a few minutes (CalDAV calendars poll every 15 minutes)</li>
                <li>Check if initial sync has completed (look for sync timestamp)</li>
            </ol>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble2' ? open = null : open = 'trouble2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Too many/too few events are syncing</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble2'" x-collapse class="px-6 pb-4">
            <p class="mb-2">Adjust your filters:</p>
            <ul class="mb-0">
                <li><strong>Too many?</strong> Enable "Ignore all-day events" or "Busy events only" or restrict to work hours</li>
                <li><strong>Too few?</strong> Disable filters to sync all events, or adjust work hours to include more time</li>
                <li><strong>Tip:</strong> Edit your sync rule and try different filter combinations until it works for your needs</li>
            </ul>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble3' ? open = null : open = 'trouble3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Blocker events show wrong times</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble3'" x-collapse class="px-6 pb-4">
            <p class="mb-2">This is usually a timezone issue:</p>
            <ul class="mb-0">
                <li>Check your account timezone in Settings</li>
                <li>Verify source calendar timezone settings</li>
                <li>Check target calendar timezone settings</li>
                <li>If using CalDAV, ensure timezone is properly configured in the calendar service</li>
            </ul>
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
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Connect More Calendars</h3>
        </div>
        <p class="mb-0">Add more calendar connections to create additional sync rules.</p>
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
                <p class="text-sm text-gray-600 !mb-0">How sync rules work under the hood</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Sync Engine Architecture</h4>
        <p>When you create a sync rule, here's what happens:</p>
        <ol>
            <li><strong>Initial Sync:</strong> All events from the source calendar within the time range (default: past 7 days, future 90 days) are synced</li>
            <li><strong>Webhook Registration:</strong> For Google/Microsoft, webhooks are registered to receive real-time notifications</li>
            <li><strong>Event Processing:</strong> Each event is checked against filters before creating a blocker</li>
            <li><strong>Blocker Creation:</strong> A new event is created in target calendar(s) with your custom title</li>
            <li><strong>Tracking:</strong> A database record links the source event to blocker event(s) for future updates/deletions</li>
        </ol>
        
        <h4>Real-Time vs. Polling</h4>
        <ul>
            <li><strong>Google & Microsoft:</strong> Real-time via webhooks (1-2 minute latency)</li>
            <li><strong>CalDAV & Email:</strong> Polling every 15 minutes</li>
            <li><strong>Webhook renewal:</strong> Automatic every 3-7 days (varies by provider)</li>
        </ul>
        
        <h4>Filter Processing</h4>
        <p>Filters are applied in this order:</p>
        <ol>
            <li>Check if event is all-day (if "Ignore all-day events" is enabled)</li>
            <li>Check event status (if "Busy events only" is enabled)</li>
            <li>Check if event time falls within work hours (if configured)</li>
            <li>Check if event day is included in selected days (if work hours enabled)</li>
        </ol>
        <p>An event must pass ALL enabled filters to be synced.</p>
        
        <h4>Duplicate Prevention</h4>
        <p>SyncMyDay prevents duplicate blocker events using:</p>
        <ul>
            <li>Unique identifiers linking source events to blockers</li>
            <li>Hash-based detection of existing blockers</li>
            <li>Cleanup of orphaned blockers when rules are deleted</li>
        </ul>
        
        <h4>Performance</h4>
        <ul>
            <li><strong>Database:</strong> Indexed by user, calendar, and sync rule for fast lookups</li>
            <li><strong>Caching:</strong> Connection tokens and metadata cached in Redis (if available)</li>
            <li><strong>Queues:</strong> Large sync operations processed in background jobs</li>
            <li><strong>Rate limiting:</strong> API calls throttled to respect provider limits</li>
        </ul>
    </div>
</div>
@endsection

