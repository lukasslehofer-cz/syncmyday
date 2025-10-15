@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div>
                <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                    Connection Successful!
                </h1>
                <p class="text-lg text-gray-600 mt-1">{{ $email }}</p>
            </div>
        </div>
        <p class="text-lg text-gray-600">
            Select the calendars you want to sync
        </p>
    </div>

    <!-- Calendars List -->
    <form action="{{ route('caldav.complete') }}" method="POST" class="space-y-6">
        @csrf

        @if(count($calendars) === 0)
            <!-- No calendars found -->
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-2xl p-8 text-center">
                <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Calendars Found</h3>
                <p class="text-gray-600 mb-6">We couldn't find any calendars in your CalDAV account that support events.</p>
                <a href="{{ route('caldav.setup') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Try Different Account
                </a>
            </div>
        @else
            <!-- Calendar selection -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="p-6 lg:p-8 space-y-6">
                    <!-- Calendar Name -->
                    <div>
                        <label for="name" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                            <span>{{ __('messages.calendar_name') }} <span class="text-red-500">*</span></span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', 'Apple Calendar') }}" 
                            required
                            placeholder="{{ __('messages.calendar_name_placeholder') }}" 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent font-medium transition"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">{{ __('messages.calendar_name_hint') }}</p>
                    </div>

                    <!-- Select Calendar -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="flex items-center space-x-2 text-sm font-bold text-gray-900">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ __('messages.select_calendar_to_use') }} <span class="text-red-500">*</span></span>
                            </label>
                            <span class="text-sm text-gray-500">{{ count($calendars) }} calendar(s) found</span>
                        </div>

                        <div class="space-y-3">
                            @foreach($calendars as $index => $calendar)
                                <label class="calendar-option flex items-center p-4 border-2 rounded-xl hover:border-purple-300 hover:bg-purple-50 transition cursor-pointer group {{ $index === 0 ? 'border-purple-400 bg-purple-50' : 'border-gray-200' }}">
                                    <input 
                                        type="radio" 
                                        name="selected_calendar_id" 
                                        value="{{ $calendar['id'] }}"
                                        class="calendar-radio w-5 h-5 text-purple-600 border-2 border-gray-300 focus:ring-purple-500 focus:ring-2"
                                        {{ $index === 0 ? 'checked' : '' }}
                                        required
                                        onchange="updateCalendarSelection()"
                                    >
                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center space-x-3">
                                            @if(isset($calendar['color']))
                                                <div class="w-4 h-4 rounded-full border-2 border-gray-300" style="background-color: {{ $calendar['color'] }}"></div>
                                            @endif
                                            <span class="font-semibold text-gray-900 group-hover:text-purple-700">
                                                {{ $calendar['name'] }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1 font-mono">{{ basename($calendar['id']) }}</p>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </label>
                            @endforeach
                        </div>

                        <script>
                        function updateCalendarSelection() {
                            // Remove highlight from all options
                            document.querySelectorAll('.calendar-option').forEach(label => {
                                label.classList.remove('border-purple-400', 'bg-purple-50');
                                label.classList.add('border-gray-200');
                            });
                            
                            // Add highlight to selected option
                            const checkedRadio = document.querySelector('.calendar-radio:checked');
                            if (checkedRadio) {
                                const label = checkedRadio.closest('.calendar-option');
                                label.classList.remove('border-gray-200');
                                label.classList.add('border-purple-400', 'bg-purple-50');
                            }
                        }
                        </script>

                        @error('selected_calendar_id')
                            <p class="mt-4 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-sm text-gray-500">{{ __('messages.select_calendar_hint') }}</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 lg:px-8 flex items-center justify-between">
                    <a href="{{ route('caldav.setup') }}" class="text-gray-600 hover:text-gray-900 font-medium transition">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </a>
                    <button 
                        type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition duration-150 shadow-lg hover:shadow-xl"
                    >
                        Complete Setup
                    </button>
                </div>
            </div>

            <!-- Info note -->
            <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-4">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-purple-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-gray-700">
                        <strong class="text-gray-900">Note:</strong> After setup, you can create sync rules to define how events are synchronized between your calendars. CalDAV calendars are checked for changes every 5 minutes.
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>
@endsection

