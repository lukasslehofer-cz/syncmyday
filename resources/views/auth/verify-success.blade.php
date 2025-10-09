@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Success Icon -->
            <div class="flex justify-center mb-6">
                <div class="rounded-full bg-green-100 p-4">
                    <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-3xl font-bold text-gray-900 mb-4">
                {{ __('emails.email_verified_title') }}
            </h2>

            <!-- Message -->
            <p class="text-center text-gray-600 mb-8">
                {{ __('emails.email_verified_message') }}
            </p>

            <!-- Success Box -->
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-green-700 text-center">
                    ✓ {{ __('emails.email_verified_confirmation') }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3">
                <a href="{{ route('dashboard') }}" class="block w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition duration-200 shadow-md text-center">
                    {{ __('emails.go_to_dashboard') }}
                </a>
                
                <a href="{{ route('onboarding.start') }}" class="block w-full bg-white text-purple-600 font-semibold py-3 px-4 rounded-lg border-2 border-purple-600 hover:bg-purple-50 transition duration-200 text-center">
                    {{ __('emails.start_onboarding') }}
                </a>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 text-center">
                    {{ __('emails.email_verified_next_steps') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

