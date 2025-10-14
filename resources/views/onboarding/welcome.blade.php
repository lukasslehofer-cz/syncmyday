@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-3xl shadow-2xl p-12 text-center border-4 border-indigo-200 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-200 to-purple-200 rounded-full blur-3xl opacity-30 -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-purple-200 to-pink-200 rounded-full blur-3xl opacity-30 -ml-32 -mb-32"></div>
        
        <!-- Content -->
        <div class="relative z-10">
            <!-- Icon -->
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-r from-indigo-600 to-purple-600 shadow-2xl mb-6 animate-bounce">
                <span class="text-5xl">🎉</span>
            </div>
            
            <!-- Heading -->
            <h1 class="text-5xl md:text-6xl font-extrabold mb-4">
                <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                    Welcome to SyncMyDay!
                </span>
            </h1>
            
            <!-- Subheading -->
            <p class="text-2xl text-gray-700 font-semibold mb-3">
                You're all set! 🚀
            </p>
            
            <p class="text-lg text-gray-600 mb-10 max-w-2xl mx-auto">
                Your <strong class="text-indigo-600">free trial</strong> is now active. Let's connect your calendars and start syncing in just a few clicks!
            </p>
            
            <!-- Benefits -->
            <div class="grid md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg">
                    <div class="text-4xl mb-3">⚡</div>
                    <h3 class="font-bold text-gray-900 mb-2">2 Minute Setup</h3>
                    <p class="text-sm text-gray-600">Quick and easy configuration</p>
                </div>
                <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg">
                    <div class="text-4xl mb-3">🔒</div>
                    <h3 class="font-bold text-gray-900 mb-2">100% Private</h3>
                    <p class="text-sm text-gray-600">Your data stays secure</p>
                </div>
                <div class="bg-white/80 backdrop-blur rounded-2xl p-6 shadow-lg">
                    <div class="text-4xl mb-3">🎯</div>
                    <h3 class="font-bold text-gray-900 mb-2">Zero Conflicts</h3>
                    <p class="text-sm text-gray-600">Never double-book again</p>
                </div>
            </div>
            
            <!-- CTA Button -->
            <a href="{{ route('connections.index') }}" class="inline-flex items-center justify-center px-12 py-5 text-xl font-bold text-white bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl hover:opacity-90 shadow-2xl transform hover:scale-105 transition-all duration-200">
                <span>Get Started</span>
                <svg class="w-6 h-6 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
            
            <p class="mt-6 text-sm text-gray-600 font-medium">
                ✨ No credit card required • Cancel anytime
            </p>
        </div>
    </div>
</div>
@endsection

