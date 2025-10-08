@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="text-6xl mb-6">🎉</div>
        
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Welcome to SyncMyDay!</h1>
        
        <p class="text-lg text-gray-600 mb-8">
            Let's get you set up in just 3 easy steps:
        </p>
        
        <div class="space-y-4 text-left mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                    1
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900">Connect Your Calendars</h3>
                    <p class="text-gray-600 text-sm">Link your Google and/or Microsoft calendars</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                    2
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900">Select Calendars</h3>
                    <p class="text-gray-600 text-sm">Choose which calendars to sync</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-semibold">
                    3
                </div>
                <div class="ml-4">
                    <h3 class="font-semibold text-gray-900">Create Sync Rule</h3>
                    <p class="text-gray-600 text-sm">Set up your first synchronization rule</p>
                </div>
            </div>
        </div>
        
        <a href="{{ route('onboarding.connect-calendars') }}" class="inline-block px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-lg">
            Get Started →
        </a>
        
        <p class="mt-4 text-sm text-gray-500">
            Takes less than 2 minutes
        </p>
    </div>
</div>
@endsection

