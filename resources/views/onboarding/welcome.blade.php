@extends('layouts.app')

@section('title', __('messages.onboarding_welcome'))

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-lg p-8 text-center border-2 border-indigo-100">
        <div class="text-6xl mb-6">🎉</div>
        
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
            {{ __('messages.welcome_to_syncmyday') }}
        </h1>
        
        <p class="text-lg text-gray-600 mb-8">
            {{ __('messages.onboarding_intro') }}
        </p>
        
        <div class="space-y-4 text-left mb-8">
            <div class="flex items-start p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg border border-indigo-100">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                    1
                </div>
                <div class="ml-4">
                    <h3 class="font-bold text-gray-900 text-lg">{{ __('messages.onboarding_step1_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.onboarding_step1_desc') }}</p>
                </div>
            </div>
            
            <div class="flex items-start p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                    2
                </div>
                <div class="ml-4">
                    <h3 class="font-bold text-gray-900 text-lg">{{ __('messages.onboarding_step2_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.onboarding_step2_desc') }}</p>
                </div>
            </div>
            
            <div class="flex items-start p-4 bg-gradient-to-r from-pink-50 to-red-50 rounded-lg border border-pink-100">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                    3
                </div>
                <div class="ml-4">
                    <h3 class="font-bold text-gray-900 text-lg">{{ __('messages.onboarding_step3_title') }}</h3>
                    <p class="text-gray-600 text-sm">{{ __('messages.onboarding_step3_desc') }}</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('connections.index') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:opacity-90 font-bold text-lg shadow-xl transform hover:scale-105 transition">
            {{ __('messages.get_started') }}
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
        
        <p class="mt-6 text-sm text-gray-500">
            {{ __('messages.onboarding_duration') }}
        </p>
    </div>
</div>
@endsection

