@extends('layouts.public')

@section('title', __('messages.contact_us'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ __('messages.contact_us') }}</h1>
        <p class="text-lg text-gray-600">{{ __('messages.contact_us_description') }}</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-start">
        <svg class="w-5 h-5 text-green-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-start">
        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
        <form action="{{ route('contact.send') }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.your_name') }}</label>
                <input type="text" id="name" name="name" value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-300 @enderror">
                @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.your_email') }}</label>
                <input type="email" id="email" name="email" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('email') border-red-300 @enderror">
                @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.subject') }}</label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('subject') border-red-300 @enderror">
                @error('subject')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">{{ __('messages.your_message') }}</label>
                <textarea id="message" name="message" rows="6" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('message') border-red-300 @enderror">{{ old('message') }}</textarea>
                @error('message')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:opacity-90 shadow-md transition">
                {{ __('messages.send_message') }}
            </button>
        </form>
    </div>

    <!-- Alternative Contact Options -->
    <div class="mt-8 p-6 bg-gray-50 border border-gray-200 rounded-xl">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('messages.other_ways_to_reach_us') }}</h3>
        <div class="space-y-3">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ __('messages.email') }}</p>
                    <a href="mailto:support@syncmyday.{{ config('app.locale') === 'cs' ? 'cz' : 'eu' }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        support@syncmyday.{{ config('app.locale') === 'cs' ? 'cz' : 'eu' }}
                    </a>
                </div>
            </div>
            <div class="flex items-start">
                <svg class="w-5 h-5 text-indigo-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ __('messages.help_center') }}</p>
                    <a href="{{ route('help.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700">
                        {{ __('messages.browse_help_articles') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

