@extends('layouts.app')

@section('title', __('messages.complete_calendar_setup'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">
            {{ __('messages.complete_calendar_setup') }}
        </h1>
        <p class="text-lg text-gray-600">
            {{ __('messages.complete_calendar_setup_description') }}
        </p>
    </div>

    <!-- Success Message -->
    <div class="mb-6 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">{{ __('messages.oauth_authorization_successful') }}</h3>
                <p class="text-sm text-gray-700">
                    {{ __('messages.oauth_authorization_successful_description', ['provider' => ucfirst($provider), 'email' => $email]) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('connections.complete-oauth') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6 lg:p-8 space-y-6">
            <!-- Calendar Name -->
            <div>
                <label for="name" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ __('messages.calendar_name') }} <span class="text-red-500">*</span></span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $suggestedName) }}" 
                    required
                    placeholder="{{ __('messages.calendar_name_placeholder') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-medium transition"
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
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ __('messages.select_calendar_to_use') }} <span class="text-red-500">*</span></span>
                    </label>
                    <span class="text-sm text-gray-500">{{ count($calendars) }} calendar(s) found</span>
                </div>

                <div class="space-y-3">
                    @forelse($calendars as $calendar)
                        <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl hover:border-indigo-300 hover:bg-indigo-50 transition cursor-pointer group">
                            <input 
                                type="radio" 
                                name="selected_calendar_id" 
                                value="{{ $calendar['id'] }}"
                                class="w-5 h-5 text-indigo-600 border-2 border-gray-300 focus:ring-indigo-500 focus:ring-2"
                                {{ ($calendar['id'] === old('selected_calendar_id', $primaryCalendarId)) ? 'checked' : '' }}
                                required
                            >
                            <div class="ml-4 flex-1">
                                <div class="flex items-center space-x-3">
                                    @if(isset($calendar['color']))
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300" style="background-color: {{ $calendar['color'] }}"></div>
                                    @endif
                                    <span class="font-semibold text-gray-900 group-hover:text-indigo-700">
                                        {{ $calendar['name'] }}
                                        @if($calendar['primary'] ?? false)
                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ __('messages.primary') }}
                                            </span>
                                        @endif
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1 font-mono">{{ $calendar['id'] }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </label>
                    @empty
                        <p class="text-gray-600">{{ __('messages.no_calendars_found') }}</p>
                    @endforelse
                </div>

                @error('selected_calendar_id')
                    <p class="mt-4 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.select_calendar_hint') }}</p>
            </div>

            <!-- Info box -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-2">{{ __('messages.why_select_calendar') }}</h4>
                        <p class="text-sm text-gray-700">
                            {{ __('messages.why_select_calendar_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('connections.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 transition">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('messages.complete_calendar_setup_button') }}
            </button>
        </div>
    </form>
</div>
@endsection

