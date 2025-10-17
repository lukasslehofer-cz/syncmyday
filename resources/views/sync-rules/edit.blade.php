@extends('layouts.app')

@section('title', __('messages.edit_sync_rule'))

@section('content')
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
                {{ __('messages.edit_sync_rule') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">{{ __('messages.edit_sync_rule_description') }}</p>
    </div>
    
    <form action="{{ route('sync-rules.update', $rule) }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-6 lg:p-8 space-y-8">
            <!-- Rule Name -->
            <div>
                <label for="rule_name" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ __('messages.rule_name') }} <span class="text-red-500">*</span></span>
                </label>
                <input type="text" name="name" id="rule_name" value="{{ old('name', $rule->name) }}" required
                    placeholder="{{ __('messages.rule_name_placeholder') }}"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium transition">
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.rule_name_hint') }}</p>
            </div>
            
            <!-- Source Calendar (Read-only) -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                    <div>
                        <label class="block text-lg font-bold text-gray-900">{{ __('messages.source_calendar') }}</label>
                        <p class="text-sm text-gray-600">{{ __('messages.cannot_change_source') }}</p>
                    </div>
                </div>
                
                <div class="bg-white/70 rounded-xl p-4 border-2 border-blue-200">
                    <p class="font-semibold text-gray-900">
                        @if($rule->sourceConnection)
                            {{ $rule->sourceConnection->name ?? $rule->sourceConnection->provider_email }}
                        @else
                            {{ $rule->sourceEmailConnection->name }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        @if($rule->sourceConnection)
                            {{ $rule->sourceConnection->account_email ?? $rule->sourceConnection->provider_email }}
                        @else
                            {{ $rule->sourceEmailConnection->email_address }}
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Target Calendars (Read-only) -->
            <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-6 border border-purple-100">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <label class="block text-lg font-bold text-gray-900">{{ __('messages.target_calendars') }}</label>
                        <p class="text-sm text-gray-600">{{ __('messages.cannot_change_targets') }}</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    @foreach($rule->targets as $target)
                    <div class="bg-white/70 rounded-xl p-4 border-2 border-purple-200">
                        <p class="font-semibold text-gray-900">
                            @if($target->targetConnection)
                                {{ $target->targetConnection->name ?? $target->targetConnection->provider_email }}
                            @else
                                {{ $target->targetEmailConnection->name }}
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 mt-1">
                            @if($target->targetConnection)
                                {{ $target->targetConnection->account_email ?? $target->targetConnection->provider_email }}
                            @else
                                {{ $target->targetEmailConnection->email_address }}
                            @endif
                        </p>
                    </div>
                    @endforeach
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
                <input type="text" name="blocker_title" id="blocker_title" value="{{ old('blocker_title', $rule->blocker_title) }}" required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium transition">
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.blocker_title_description') }}</p>
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
                        <input type="checkbox" name="filters[busy_only]" value="1" {{ (old('filters.busy_only', $rule->filters['busy_only'] ?? false)) ? 'checked' : '' }} class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <span class="block font-semibold text-gray-900">{{ __('messages.only_busy_events') }}</span>
                            <span class="block text-sm text-gray-600">{{ __('messages.only_busy_events_description') }}</span>
                        </div>
                    </label>
                    <label class="flex items-center p-4 bg-gray-50 rounded-xl border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                        <input type="checkbox" name="filters[ignore_all_day]" value="1" {{ (old('filters.ignore_all_day', $rule->filters['ignore_all_day'] ?? false)) ? 'checked' : '' }} class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <div class="ml-3">
                            <span class="block font-semibold text-gray-900">{{ __('messages.ignore_all_day') }}</span>
                            <span class="block text-sm text-gray-600">{{ __('messages.skip_all_day_description') }}</span>
                        </div>
                    </label>
                    
                    <!-- Time & Day Filter -->
                    <div class="p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="time_filter_enabled" name="time_filter_enabled" value="1" {{ (old('time_filter_enabled', $rule->time_filter_enabled)) ? 'checked' : '' }} class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <div class="ml-3">
                                <span class="block font-semibold text-gray-900">{{ __('messages.enable_time_filter') }}</span>
                                <span class="block text-sm text-gray-600">{{ __('messages.time_filter_description') }}</span>
                            </div>
                        </label>
                        
                        <div id="time_filter_options" class="mt-4 space-y-3 {{ (old('time_filter_enabled', $rule->time_filter_enabled)) ? '' : 'hidden' }}">
                    <!-- Filter Type Selection -->
                    <div class="space-y-2">
                        <label class="flex items-center p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                            <input type="radio" name="time_filter_type" value="workdays" class="time-filter-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ (old('time_filter_type', $rule->time_filter_type) === 'workdays') ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-900">{{ __('messages.workdays') }}</span>
                        </label>
                        
                        <!-- Workdays Time Range -->
                        <div id="workdays_time_options" class="ml-7 space-y-2 {{ (old('time_filter_type', $rule->time_filter_type) === 'workdays') ? '' : 'hidden' }}">
                            <label class="block text-xs font-semibold text-gray-700">{{ __('messages.time_range') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.from') }}</label>
                                    <input type="time" name="workdays_time_start" value="{{ old('workdays_time_start', $rule->time_filter_type === 'workdays' && $rule->time_filter_start ? substr($rule->time_filter_start, 0, 5) : '08:00') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.to') }}</label>
                                    <input type="time" name="workdays_time_end" value="{{ old('workdays_time_end', $rule->time_filter_type === 'workdays' && $rule->time_filter_end ? substr($rule->time_filter_end, 0, 5) : '18:00') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <label class="flex items-center p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                            <input type="radio" name="time_filter_type" value="weekends" class="time-filter-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ (old('time_filter_type', $rule->time_filter_type) === 'weekends') ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-900">{{ __('messages.weekends') }}</span>
                        </label>
                        
                        <label class="flex items-center p-3 bg-white rounded-lg border-2 border-gray-200 hover:border-indigo-300 cursor-pointer transition">
                            <input type="radio" name="time_filter_type" value="custom" class="time-filter-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300" {{ (old('time_filter_type', $rule->time_filter_type) === 'custom') ? 'checked' : '' }}>
                            <span class="ml-3 text-gray-900">{{ __('messages.custom') }}</span>
                        </label>
                    </div>
                    
                    <!-- Custom Options (shown only when 'custom' is selected) -->
                    <div id="custom_time_options" class="ml-7 space-y-3 {{ (old('time_filter_type', $rule->time_filter_type) === 'custom') ? '' : 'hidden' }}">
                        <!-- Time Range -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('messages.time_range') }}</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.from') }}</label>
                                    <input type="time" name="time_filter_start" value="{{ old('time_filter_start', $rule->time_filter_start ? substr($rule->time_filter_start, 0, 5) : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">{{ __('messages.to') }}</label>
                                    <input type="time" name="time_filter_end" value="{{ old('time_filter_end', $rule->time_filter_end ? substr($rule->time_filter_end, 0, 5) : '') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Days of Week -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-2">{{ __('messages.days_of_week') }}</label>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                @php
                                    $selectedDays = old('time_filter_days', $rule->time_filter_days ?? []);
                                @endphp
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="1" {{ in_array(1, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.monday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="2" {{ in_array(2, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.tuesday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="3" {{ in_array(3, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.wednesday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="4" {{ in_array(4, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.thursday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="5" {{ in_array(5, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.friday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="6" {{ in_array(6, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.saturday') }}</span>
                                </label>
                                <label class="flex items-center p-2 bg-white rounded border border-gray-300 hover:border-indigo-400 cursor-pointer text-xs">
                                    <input type="checkbox" name="time_filter_days[]" value="7" {{ in_array(7, $selectedDays) ? 'checked' : '' }} class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <span class="ml-2">{{ __('messages.sunday') }}</span>
                                </label>
                            </div>
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
                {{ __('messages.save_changes') }}
            </button>
        </div>
    </form>

    <!-- Delete Sync Rule -->
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl shadow-xl border-2 border-red-200 mt-8">
        <div class="px-6 py-5 border-b border-red-200">
            <h2 class="text-xl font-bold text-red-900">{{ __('messages.delete_sync_rule') }}</h2>
        </div>
        <div class="p-6 lg:p-8">
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 mb-1">{{ __('messages.action_cannot_be_undone') }}</p>
                        <p class="text-sm text-gray-600">{{ __('messages.delete_sync_rule_warning') }}</p>
                        <ul class="text-sm text-gray-600 mt-2 ml-4 list-disc">
                            <li>{{ __('messages.delete_sync_rule_warning_1') }}</li>
                            <li>{{ __('messages.delete_sync_rule_warning_2') }}</li>
                            <li>{{ __('messages.delete_sync_rule_warning_3') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('sync-rules.destroy', $rule) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_sync_rule_confirm') }}')">
                @csrf
                @method('DELETE')
                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 shadow-lg transform hover:scale-105 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ __('messages.delete_this_sync_rule') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
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
</script>
@endsection

