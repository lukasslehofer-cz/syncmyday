@extends('layouts.app')

@section('title', __('messages.edit_email_calendar'))

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('connections.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                {{ __('messages.edit_email_calendar') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">
            {{ __('messages.edit_email_calendar_description') }}
        </p>
    </div>

    <!-- Email Address Info (read-only) -->
    <div class="mb-6 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
        <div class="flex items-start space-x-3">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-gray-900 mb-1">{{ __('messages.calendar_email_address') }}</h3>
                <p class="text-sm text-gray-700 font-mono bg-white px-3 py-1 rounded-lg inline-block">
                    {{ $emailCalendar->email_address }}
                </p>
                <p class="text-xs text-gray-600 mt-2">{{ __('messages.email_calendar_cannot_change_email') }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('email-calendars.update', $emailCalendar) }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf
        @method('PUT')

        <div class="p-6 lg:p-8 space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    <span>{{ __('messages.calendar_name') }} <span class="text-red-500">*</span></span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $emailCalendar->name) }}" 
                    required
                    placeholder="{{ __('messages.calendar_name_placeholder') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-medium transition"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.calendar_name_hint') }}</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>{{ __('messages.description') }} ({{ __('messages.optional') }})</span>
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="3"
                    placeholder="{{ __('messages.description_placeholder') }}"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-medium transition"
                >{{ old('description', $emailCalendar->description) }}</textarea>
                @error('description')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sender Whitelist -->
            <div>
                <label for="sender_whitelist" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>{{ __('messages.allowed_senders') }} ({{ __('messages.optional') }})</span>
                </label>
                <textarea 
                    name="sender_whitelist" 
                    id="sender_whitelist" 
                    rows="4"
                    placeholder="user@company.com&#10;*@company.com&#10;&#10;Leave empty to allow all senders"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-mono text-sm transition"
                >{{ old('sender_whitelist', is_array($emailCalendar->sender_whitelist) ? implode("\n", $emailCalendar->sender_whitelist) : '') }}</textarea>
                @error('sender_whitelist')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">
                    {!! __('messages.allowed_senders_hint') !!}
                </p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('connections.index') }}" class="px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-100 transition">
                {{ __('messages.cancel') }}
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('messages.save_changes') }}
            </button>
        </div>
    </form>

    <!-- Delete Email Calendar -->
    <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-2xl shadow-xl border-2 border-red-200 mt-8">
        <div class="px-6 py-5 border-b border-red-200">
            <h2 class="text-xl font-bold text-red-900">{{ __('messages.delete_email_calendar') }}</h2>
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
                        <p class="text-sm text-gray-600">{{ __('messages.delete_email_calendar_warning') }}</p>
                        <ul class="text-sm text-gray-600 mt-2 ml-4 list-disc">
                            <li>{{ __('messages.delete_calendar_warning_1') }}</li>
                            <li>{{ __('messages.delete_calendar_warning_2') }}</li>
                            <li>{{ __('messages.delete_calendar_warning_3') }}</li>
                            <li>{{ __('messages.delete_email_calendar_warning_email') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('email-calendars.destroy', $emailCalendar) }}" method="POST" onsubmit="return confirm('{{ __('messages.delete_email_calendar_confirm') }}')">
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
                        {{ __('messages.delete_this_email_calendar') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

