@extends('layouts.app')

@section('title', __('messages.create_sync_rule'))

@section('content')
<style>
/* Custom select styling */
.custom-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236366f1'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1.5em 1.5em;
    padding-right: 3rem;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.custom-select:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Safari fix: Don't use font-weight on options - causes Safari to use serif font */
.custom-select option {
    font-family: inherit;
    font-weight: normal;
}

.custom-select optgroup {
    font-family: inherit;
    font-weight: normal;
    color: #4b5563;
    background-color: #f9fafb;
}

/* Blue select for source */
.select-blue {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%233b82f6'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
}

/* Purple select for targets */
.select-purple {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239333ea'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
}

/* Loading overlay */
#loading-overlay {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('sync-rules.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ __('messages.create_sync_rule') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">{{ __('messages.create_sync_rule_description') }}</p>
    </div>
    
    <form action="{{ route('sync-rules.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf
        
        <div class="p-6 lg:p-8 space-y-8">
            <!-- Rule Name -->
            <div>
                <label for="rule_name" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ __('messages.rule_name') }} <span class="text-red-500">*</span></span>
                </label>
                <input type="text" name="name" id="rule_name" value="{{ old('name') }}" required
                    placeholder="{{ __('messages.rule_name_placeholder') }}"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium transition">
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.rule_name_hint') }}</p>
            </div>
            
            <!-- Source Calendar -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                    <div>
                        <label class="block text-lg font-bold text-gray-900">{{ __('messages.source_calendar') }}</label>
                        <p class="text-sm text-gray-600">{{ __('messages.source_calendar_description') }}</p>
                    </div>
                </div>
                
                <select name="source_type_and_id" id="source_connection_select" required class="custom-select select-blue w-full px-4 py-3 border-2 border-blue-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition shadow-sm hover:border-blue-300">
                    <option value="">{{ __('messages.select_calendar') }}...</option>
                    
                    @if($apiConnections->count() > 0)
                    <optgroup label="{{ __('messages.api_calendars') }}">
                        @foreach($apiConnections as $connection)
                        <option value="api-{{ $connection->id }}">
                            {{ $connection->name ?? (ucfirst($connection->provider) . ' - ' . $connection->provider_email) }}
                        </option>
                        @endforeach
                    </optgroup>
                    @endif
                    
                    @if($emailConnections->count() > 0)
                    <optgroup label="{{ __('messages.email_calendars') }}">
                        @foreach($emailConnections as $connection)
                        <option value="email-{{ $connection->id }}">
                            {{ $connection->name }}
                        </option>
                        @endforeach
                    </optgroup>
                    @endif
                </select>
            </div>
            
            <!-- Target Calendars -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div>
                            <label class="block text-lg font-bold text-gray-900">{{ __('messages.target_calendars') }}</label>
                            <p class="text-sm text-gray-600">{{ __('messages.target_calendars_description') }}</p>
                        </div>
                    </div>
                    
                    <button type="button" id="add-target" class="inline-flex items-center px-4 py-2 bg-purple-100 text-purple-700 font-semibold rounded-lg hover:bg-purple-200 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('messages.add_target') }}
                    </button>
                </div>
                
                <div id="targets-container" class="space-y-4">
                    <!-- Target rows will be added here dynamically -->
                </div>
            </div>
            
            <!-- Blocker Title -->
            <div>
                <label for="blocker_title" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ __('messages.blocker_title') }}</span>
                </label>
                <input type="text" name="blocker_title" id="blocker_title" value="Busy — Sync" required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium transition">
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.blocker_title_description') }}</p>
            </div>
            
            <!-- Direction -->
            <div>
                <label class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span>{{ __('messages.sync_direction') }}</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                        <input type="radio" name="direction" value="one_way" checked class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <div class="ml-3">
                            <span class="block font-semibold text-gray-900">{{ __('messages.one_way') }}</span>
                            <span class="block text-sm text-gray-600">{{ __('messages.one_way_description') }}</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                        <input type="radio" name="direction" value="two_way" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <div class="ml-3">
                            <span class="block font-semibold text-gray-900">{{ __('messages.two_way') }}</span>
                            <span class="block text-sm text-gray-600">{{ __('messages.two_way_description') }}</span>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Filters -->
            <div>
                <label class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    <span>{{ __('messages.filters') }}</span>
                </label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                        <input type="checkbox" name="filters[busy_only]" value="1" checked class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <span class="block font-semibold text-gray-900">{{ __('messages.only_busy_events') }}</span>
                            <span class="block text-sm text-gray-600">{{ __('messages.only_busy_events_description') }}</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                        <input type="checkbox" name="filters[ignore_all_day]" value="1" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <span class="block font-semibold text-gray-900">{{ __('messages.ignore_all_day') }}</span>
                            <span class="block text-sm text-gray-600">{{ __('messages.skip_all_day_description') }}</span>
                        </div>
                    </label>
                    
                    <!-- Time & Day Filter -->
                    <div class="p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="time_filter_enabled" name="time_filter_enabled" value="1" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <div class="ml-3">
                                <span class="block font-semibold text-gray-900">{{ __('messages.enable_time_filter') }}</span>
                                <span class="block text-sm text-gray-600">{{ __('messages.time_filter_description') }}</span>
                            </div>
                        </label>
                        
                        <div id="time_filter_options" class="mt-4 space-y-3 hidden">
                    <!-- Filter Type Selection -->
                    <div class="space-y-2">
                        <label class="flex items-center p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                            <input type="radio" name="time_filter_type" value="workdays" class="time-filter-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-3 text-gray-900">{{ __('messages.workdays') }}</span>
                        </label>
                        
                        <!-- Workdays Time Range -->
                        <div id="workdays_time_options" class="ml-7 space-y-2 hidden">
                            <label class="block text-xs font-semibold text-gray-700">{{ __('messages.time_range') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.from') }}</label>
                                    <input type="time" name="workdays_time_start" value="08:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.to') }}</label>
                                    <input type="time" name="workdays_time_end" value="18:00" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <label class="flex items-center p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                            <input type="radio" name="time_filter_type" value="weekends" class="time-filter-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-3 text-gray-900">{{ __('messages.weekends') }}</span>
                        </label>
                        
                        <label class="flex items-center p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                            <input type="radio" name="time_filter_type" value="custom" class="time-filter-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            <span class="ml-3 text-gray-900">{{ __('messages.custom') }}</span>
                        </label>
                    </div>
                    
                    <!-- Custom Options (shown only when 'custom' is selected) -->
                    <div id="custom_time_options" class="ml-7 space-y-3 hidden">
                        <!-- Time Range -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('messages.time_range') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.from') }}</label>
                                    <input type="time" name="time_filter_start" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.to') }}</label>
                                    <input type="time" name="time_filter_end" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Days of Week -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('messages.days_of_week') }}</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.monday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="2" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.tuesday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="3" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.wednesday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="4" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.thursday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="5" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.friday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="6" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.saturday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="7" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.sunday') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>
        
        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('sync-rules.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 transition">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('messages.create_rule') }}
            </button>
        </div>
    </form>
</div>

<script>
let targetIndex = 0;

const apiConnections = @json($apiConnections);
const emailConnections = @json($emailConnections);

// Add first target automatically
addTargetRow();

// Add target button
document.getElementById('add-target').addEventListener('click', addTargetRow);

function addTargetRow() {
    const container = document.getElementById('targets-container');
    const index = targetIndex++;
    
    const div = document.createElement('div');
    div.className = 'target-row bg-white rounded-xl p-4 space-y-3 border-2 border-purple-200 shadow-sm';
    div.innerHTML = `
        <div class="flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">{{ __('messages.target') }} ${index + 1}</span>
            ${index > 0 ? `<button type="button" class="remove-target text-red-600 hover:text-red-700 font-medium text-sm">{{ __('messages.remove') }}</button>` : ''}
        </div>
        
        <select name="target_connections[${index}][type_and_id]" class="target-type-and-id-select custom-select select-purple w-full px-4 py-3 border-2 border-purple-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white transition shadow-sm hover:border-purple-300" required>
            <option value="">{{ __('messages.select_calendar') }}...</option>
            ${apiConnections.length > 0 ? `<optgroup label="{{ __('messages.api_calendars') }}">
                ${apiConnections.map(conn => `
                    <option value="api-${conn.id}">
                        ${conn.name || (conn.provider.toUpperCase() + ' - ' + conn.provider_email)}
                    </option>
                `).join('')}
            </optgroup>` : ''}
            ${emailConnections.length > 0 ? `<optgroup label="{{ __('messages.email_calendars') }}">
                ${emailConnections.map(conn => `
                    <option value="email-${conn.id}">
                        ${conn.name}
                    </option>
                `).join('')}
            </optgroup>` : ''}
        </select>
    `;
    
    container.appendChild(div);
    
    if (index > 0) {
        div.querySelector('.remove-target').addEventListener('click', function() {
            div.remove();
        });
    }
}

// Time filter toggle logic
document.getElementById('time_filter_enabled').addEventListener('change', function() {
    const options = document.getElementById('time_filter_options');
    if (this.checked) {
        options.classList.remove('hidden');
    } else {
        options.classList.add('hidden');
        // Clear radio selection and hide all sub-options
        document.querySelectorAll('.time-filter-radio').forEach(radio => radio.checked = false);
        document.getElementById('custom_time_options').classList.add('hidden');
        document.getElementById('workdays_time_options').classList.add('hidden');
    }
});

// Time filter type selection logic
document.querySelectorAll('.time-filter-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        const customOptions = document.getElementById('custom_time_options');
        const workdaysOptions = document.getElementById('workdays_time_options');
        
        // Hide all sub-options first
        customOptions.classList.add('hidden');
        workdaysOptions.classList.add('hidden');
        
        // Show relevant options based on selection
        if (this.value === 'custom') {
            customOptions.classList.remove('hidden');
        } else if (this.value === 'workdays') {
            workdaysOptions.classList.remove('hidden');
        }
    });
});

// Show loading overlay on form submit
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="sync-rules"]');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        // Create and show loading overlay
        const overlay = document.createElement('div');
        overlay.id = 'loading-overlay';
        overlay.style.cssText = 'position: fixed; inset: 0; z-index: 9999; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center;';
        
        overlay.innerHTML = `
            <div style="text-align: center;">
                <div style="position: relative; margin-bottom: 1.5rem;">
                    <div style="width: 96px; height: 96px; margin: 0 auto;">
                        <svg class="animate-spin" style="width: 96px; height: 96px; color: #4f46e5; animation: spin 1s linear infinite;" fill="none" viewBox="0 0 24 24">
                            <circle style="opacity: 0.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path style="opacity: 0.75;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                
                <div style="background: rgba(255, 255, 255, 0.98); border-radius: 1rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); padding: 2rem; max-width: 28rem; margin: 0 1rem; border: 2px solid #e0e7ff; backdrop-filter: blur(10px);">
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">
                        {{ __('messages.creating_sync_rule') }}
                    </h3>
                    <p style="color: #4b5563; margin-bottom: 1rem;">
                        {{ __('messages.loading_initial_events') }}
                    </p>
                    <div style="display: flex; align-items: center; justify-content: center; gap: 0.5rem; font-size: 0.875rem; color: #6b7280;">
                        <div class="animate-pulse">●</div>
                        <div class="animate-pulse" style="animation-delay: 0.2s;">●</div>
                        <div class="animate-pulse" style="animation-delay: 0.4s;">●</div>
                    </div>
                    <p style="font-size: 0.75rem; color: #6b7280; margin-top: 1rem;">
                        {{ __('messages.sync_processing_time') }}
                    </p>
                </div>
            </div>
        `;
        
        document.body.appendChild(overlay);
        
        // Disable submit button to prevent double submission
        const submitButton = this.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Note: Form will continue to submit normally, overlay will show until page redirect
    });
});
</script>
@endsection
