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
            Connect your Apple iCloud or other CalDAV calendar
        </p>
    </div>

    <!-- How it works / Provider Selection -->
    <div class="mb-8 bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-2xl p-6 lg:p-8">
        <div class="flex items-center space-x-3 mb-4">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-purple-500 to-indigo-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Choose Your Calendar Provider</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Apple iCloud -->
            <label class="relative cursor-pointer block">
                <input 
                    type="radio" 
                    name="provider_type" 
                    value="icloud" 
                    checked
                    class="peer absolute opacity-0"
                    onchange="toggleProviderFields()"
                >
                <div class="h-full bg-gray-50 peer-checked:bg-white rounded-xl p-4 border-2 border-purple-200 peer-checked:border-purple-500 peer-checked:shadow-lg hover:border-purple-400 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-gray-800 to-black flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-900">Apple iCloud</h4>
                        </div>
                        <div class="relative flex-shrink-0 w-7 h-7 rounded-full border-[3px] border-gray-300 peer-checked:border-purple-600 peer-checked:bg-purple-600 flex items-center justify-center transition-all shadow-sm">
                            <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 16 16">
                                <path d="M3 8l4 4 6-8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Username:</strong> Your Apple ID email</p>
                        <p><strong>Password:</strong> <a href="https://appleid.apple.com/account/manage" target="_blank" class="text-purple-600 hover:text-purple-700 underline">App-specific password</a></p>
                        <p class="text-xs text-gray-500 mt-2">✓ Easy setup - automatic configuration</p>
                    </div>
                </div>
            </label>
            
            <!-- Other CalDAV -->
            <label class="relative cursor-pointer block">
                <input 
                    type="radio" 
                    name="provider_type" 
                    value="other" 
                    class="peer absolute opacity-0"
                    onchange="toggleProviderFields()"
                >
                <div class="h-full bg-gray-50 peer-checked:bg-white rounded-xl p-4 border-2 border-purple-200 peer-checked:border-purple-500 peer-checked:shadow-lg hover:border-purple-400 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-900">Other CalDAV</h4>
                        </div>
                        <div class="relative flex-shrink-0 w-7 h-7 rounded-full border-[3px] border-gray-300 peer-checked:border-purple-600 peer-checked:bg-purple-600 flex items-center justify-center transition-all shadow-sm">
                            <svg class="w-4 h-4 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 16 16">
                                <path d="M3 8l4 4 6-8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>Server URL:</strong> Your CalDAV server address</p>
                        <p><strong>Username:</strong> Your account username</p>
                        <p><strong>Password:</strong> Your account password</p>
                        <p class="text-xs text-gray-500 mt-2">Nextcloud, Radicale, Baikal, etc.</p>
                    </div>
                </div>
            </label>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('caldav.test') }}" method="POST" class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        @csrf

        <div class="p-6 lg:p-8 space-y-6">
            <!-- Apple iCloud Fields -->
            <div id="icloud-fields">
                <!-- Apple ID Wizard -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 mb-6">
                    <h4 class="font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        How to get App-Specific Password
                    </h4>
                    <ol class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">1</span>
                            <span class="text-gray-700">Go to <a href="https://appleid.apple.com/account/manage" target="_blank" class="text-blue-600 hover:text-blue-700 underline font-medium">appleid.apple.com</a> and sign in</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">2</span>
                            <span class="text-gray-700">Enable <strong>Two-Factor Authentication</strong> (if not already enabled)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">3</span>
                            <span class="text-gray-700">In <strong>Sign-In and Security</strong> section, click <strong>App-Specific Passwords</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">4</span>
                            <span class="text-gray-700">Click <strong>+</strong> and create a password with name <code class="bg-white px-2 py-1 rounded border border-blue-200 font-mono text-xs">SyncMyDay</code></span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">5</span>
                            <span class="text-gray-700">Copy the generated password and paste it below</span>
                        </li>
                    </ol>
                </div>

                <!-- Apple ID -->
                <div class="mb-6">
                    <label for="apple_id" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>Your Apple ID <span class="text-red-500">*</span></span>
                    </label>
                    <input 
                        type="email" 
                        name="apple_id" 
                        id="apple_id" 
                        value="{{ old('apple_id') }}" 
                        placeholder="your.email@icloud.com"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition"
                    >
                    @error('apple_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- App-Specific Password -->
                <div>
                    <label for="app_password" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>App-Specific Password <span class="text-red-500">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="app_password" 
                        id="app_password" 
                        placeholder="xxxx-xxxx-xxxx-xxxx"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition font-mono"
                    >
                    <p class="mt-2 text-sm text-gray-600">
                        The 16-character password you generated (with dashes)
                    </p>
                    @error('app_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Other CalDAV Fields (hidden by default) -->
            <div id="other-fields" style="display: none;">
                <!-- CalDAV URL -->
                <div class="mb-6">
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
                        placeholder="https://your.server.com/path/to/caldav"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition font-mono text-sm"
                    >
                    @error('url')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username -->
                <div class="mb-6">
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
                        placeholder="your-username"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition"
                    >
                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
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
                        placeholder="••••••••••••••••"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition"
                    >
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
        </div>

        <script>
        function toggleProviderFields() {
            const providerType = document.querySelector('input[name="provider_type"]:checked').value;
            const icloudFields = document.getElementById('icloud-fields');
            const otherFields = document.getElementById('other-fields');
            
            if (providerType === 'icloud') {
                icloudFields.style.display = 'block';
                otherFields.style.display = 'none';
                // Set required for iCloud fields
                document.getElementById('apple_id').required = true;
                document.getElementById('app_password').required = true;
                // Remove required from other fields
                document.getElementById('url').required = false;
                document.getElementById('username').required = false;
                document.getElementById('password').required = false;
            } else {
                icloudFields.style.display = 'none';
                otherFields.style.display = 'block';
                // Remove required from iCloud fields
                document.getElementById('apple_id').required = false;
                document.getElementById('app_password').required = false;
                // Set required for other fields
                document.getElementById('url').required = true;
                document.getElementById('username').required = true;
                document.getElementById('password').required = true;
            }
        }
        </script>

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

