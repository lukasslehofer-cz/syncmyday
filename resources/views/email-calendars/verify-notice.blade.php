@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8 flex items-center justify-center">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Icon -->
            <div class="flex justify-center mb-6">
                <div class="rounded-full bg-purple-100 p-4">
                    <svg class="h-12 w-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>

            <!-- Title -->
            <h2 class="text-center text-3xl font-bold text-gray-900 mb-4">
                {{ __('emails.verify_email_calendar_title') }}
            </h2>

            <!-- Message -->
            <p class="text-center text-gray-600 mb-4">
                {{ __('emails.verify_email_calendar_message') }}
            </p>

            <!-- Email Calendar Info -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                <p class="text-sm text-purple-900 font-medium">{{ $emailCalendar->name }}</p>
                <p class="text-sm text-purple-700 mt-1">{{ $emailCalendar->target_email }}</p>
            </div>

            <!-- Status Message -->
            @if (session('status') == __('emails.verification_link_sent'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700 text-center">
                        {{ __('emails.verification_link_sent') }}
                    </p>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700 text-center">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <!-- Resend Form -->
            <form method="POST" action="{{ route('email-calendars.verification.resend', $emailCalendar) }}">
                @csrf
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold py-3 px-4 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition duration-200 shadow-md">
                    {{ __('emails.resend_verification_email') }}
                </button>
            </form>

            <!-- Back to Dashboard -->
            <div class="mt-6 text-center">
                <a href="{{ route('connections.index') }}" class="text-purple-600 hover:text-purple-800 font-medium text-sm">
                    {{ __('emails.back_to_connections') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

