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
            <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">
                Connect CalDAV Calendar
            </h1>
        </div>
        <p class="text-lg text-gray-600">
            Connect your Apple iCloud, Nextcloud, or other CalDAV calendar
        </p>
    </div>

    <!-- How it works -->
    <div class="mb-8 bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-2xl p-6 lg:p-8">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Supported Providers</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Apple iCloud -->
            <div class="bg-white rounded-xl p-4 border border-purple-200">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-900 flex items-center justify-center">
                        <span class="text-white font-bold text-sm"></span>
                    </div>
                    <h4 class="font-bold text-gray-900">Apple iCloud</h4>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><strong>URL:</strong> <code class="bg-gray-100 px-2 py-1 rounded text-xs">https://caldav.icloud.com</code></p>
                    <p><strong>Username:</strong> Your Apple ID email</p>
                    <p><strong>Password:</strong> <a href="https://appleid.apple.com/account/manage" target="_blank" class="text-purple-600 hover:text-purple-700 underline">App-specific password</a></p>
                </div>
            </div>
            
            <!-- Nextcloud -->
            <div class="bg-white rounded-xl p-4 border border-purple-200">
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                        <span class="text-white font-bold text-sm">N</span>
                    </div>
                    <h4 class="font-bold text-gray-900">Nextcloud</h4>
                </div>
                <div class="space-y-2 text-sm text-gray-600">
                    <p><strong>URL:</strong> <code class="bg-gray-100 px-2 py-1 rounded text-xs">https://your.nextcloud.com/remote.php/dav</code></p>
                    <p><strong>Username:</strong> Your Nextcloud username</p>
                    <p><strong>Password:</strong> Your password or app password</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('caldav.test') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6 lg:p-8 space-y-6">
            <!-- CalDAV URL -->
            <div>
                <label for="url" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span>CalDAV Server URL <span class="text-red-500">*</span></span>
                </label>
                <input 
                    type="url" 
                    name="url" 
                    id="url" 
                    value="{{ old('url') }}" 
                    required
                    placeholder="https://caldav.icloud.com"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition font-mono text-sm"
                >
                @error('url')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Username <span class="text-red-500">*</span></span>
                </label>
                <input 
                    type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username') }}" 
                    required
                    placeholder="your.email@icloud.com"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition"
                >
                @error('username')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Password <span class="text-red-500">*</span></span>
                </label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required
                    placeholder="••••••••••••••••"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition"
                >
                <p class="mt-2 text-sm text-gray-600">
                    For Apple iCloud, use an <a href="https://appleid.apple.com/account/manage" target="_blank" class="text-purple-600 hover:text-purple-700 underline">app-specific password</a>
                </p>
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email (optional) -->
            <div>
                <label for="email" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Email Address <span class="text-gray-500">(Optional)</span></span>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    placeholder="your.email@example.com"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition"
                >
                <p class="mt-2 text-sm text-gray-600">
                    If different from username, provide your email for display purposes
                </p>
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Footer -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 lg:px-8 flex flex-col sm:flex-row items-center justify-between space-y-3 sm:space-y-0">
            <div class="flex items-center space-x-2 text-sm text-gray-600">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Your credentials are encrypted and secure</span>
            </div>
            <button 
                type="submit"
                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition duration-150 shadow-lg hover:shadow-xl"
            >
                Test Connection & Continue
            </button>
        </div>
    </form>

    <!-- Security Note -->
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-xl p-4">
        <div class="flex items-start space-x-3">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <div class="text-sm text-gray-600">
                <strong class="text-gray-900">Privacy & Security:</strong> Your CalDAV credentials are encrypted using AES-256 encryption and stored securely. We only use them to sync your calendar events. Your data is never shared with third parties.
            </div>
        </div>
    </div>
</div>
@endsection

