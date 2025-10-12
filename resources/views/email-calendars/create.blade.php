@extends('layouts.app')

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
                {{ __('messages.add_email_calendar') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">
            {{ __('messages.add_email_calendar_description') }}
        </p>
    </div>

    <!-- How it works -->
    <div class="mb-8 bg-gradient-to-br from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6 lg:p-8">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">{{ __('messages.how_it_works') }}</h3>
        </div>
        <ol class="space-y-4">
            <li class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">1</div>
                <div class="flex-1">
                    <p class="text-gray-700">
                        {!! __('messages.email_calendar_step_1', ['domain' => config('app.email_domain', 'syncmyday.com')]) !!}
                    </p>
                </div>
            </li>
            <li class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">2</div>
                <div class="flex-1">
                    <p class="text-gray-700">{{ __('messages.email_calendar_step_2') }}</p>
                </div>
            </li>
            <li class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">3</div>
                <div class="flex-1">
                    <p class="text-gray-700">{{ __('messages.email_calendar_step_3') }}</p>
                </div>
            </li>
            <li class="flex items-start space-x-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">4</div>
                <div class="flex-1">
                    <p class="text-gray-700">{!! __('messages.email_calendar_step_4') !!}</p>
                </div>
            </li>
        </ol>
    </div>

    <!-- Form -->
    <form action="{{ route('email-calendars.store') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf

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
                    value="{{ old('name') }}" 
                    required
                    placeholder="{{ __('messages.calendar_name_placeholder') }}" 
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-medium transition"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">{{ __('messages.calendar_name_hint') }}</p>
            </div>

            <!-- Source/Target Email -->
            <div>
                <label for="target_email" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ __('messages.source_email_address') }}</span>
                    <span class="text-red-600">*</span>
                </label>
                <input 
                    type="email" 
                    name="target_email" 
                    id="target_email" 
                    value="{{ old('target_email') }}" 
                    placeholder="{{ __('messages.source_email_placeholder') }}" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent font-medium transition"
                >
                @error('target_email')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        {!! __('messages.source_email_important') !!}
                    </p>
                </div>
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
                >{{ old('description') }}</textarea>
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
                >{{ old('sender_whitelist') }}</textarea>
                @error('sender_whitelist')
                    <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-sm text-gray-500">
                    {!! __('messages.allowed_senders_hint') !!}
                </p>
            </div>

            <!-- Info box -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 mb-2">{{ __('messages.next_steps') }}</h4>
                        <p class="text-sm text-gray-700">
                            {!! __('messages.email_calendar_next_steps') !!}
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
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-105 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('messages.create_email_calendar_button') }}
            </button>
        </div>
    </form>
</div>
@endsection
