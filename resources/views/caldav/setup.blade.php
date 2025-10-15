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
                {{ __('messages.connect_caldav_calendar') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">
            {{ __('messages.connect_caldav_description') }}
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
            <h3 class="text-xl font-bold text-gray-900">{{ __('messages.choose_calendar_provider') }}</h3>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Apple iCloud -->
            <label class="relative cursor-pointer block" id="label-icloud">
                <input 
                    type="radio" 
                    name="provider_type" 
                    value="icloud" 
                    checked
                    class="peer/icloud absolute opacity-0"
                    onchange="toggleProviderFields(); updateRadioStyles();"
                >
                <div class="h-full bg-gray-50 peer-checked/icloud:bg-white rounded-xl p-4 border-2 border-purple-200 peer-checked/icloud:border-purple-500 peer-checked/icloud:shadow-lg hover:border-purple-400 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-r from-gray-800 to-black flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-900">Apple iCloud</h4>
                        </div>
                        <div class="radio-indicator relative flex-shrink-0 w-7 h-7 rounded-full border-[3px] border-purple-600 bg-purple-600 flex items-center justify-center transition-all shadow-sm">
                            <svg class="radio-check w-4 h-4 text-white transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 16 16">
                                <path d="M3 8l4 4 6-8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>{{ __('messages.username') }}:</strong> {{ __('messages.your_apple_id_email') }}</p>
                        <p><strong>{{ __('messages.password') }}:</strong> <a href="https://appleid.apple.com/account/manage" target="_blank" class="text-purple-600 hover:text-purple-700 underline">{{ __('messages.app_specific_password') }}</a></p>
                        <p class="text-xs text-gray-500 mt-2">✓ {{ __('messages.easy_setup_auto_config') }}</p>
                    </div>
                </div>
            </label>
            
            <!-- Other CalDAV -->
            <label class="relative cursor-pointer block" id="label-other">
                <input 
                    type="radio" 
                    name="provider_type" 
                    value="other" 
                    class="peer/other absolute opacity-0"
                    onchange="toggleProviderFields(); updateRadioStyles();"
                >
                <div class="h-full bg-gray-50 peer-checked/other:bg-white rounded-xl p-4 border-2 border-purple-200 peer-checked/other:border-purple-500 peer-checked/other:shadow-lg hover:border-purple-400 transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-600 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                            <h4 class="font-bold text-gray-900">{{ __('messages.other_caldav') }}</h4>
                        </div>
                        <div class="radio-indicator relative flex-shrink-0 w-7 h-7 rounded-full border-[3px] border-gray-300 flex items-center justify-center transition-all shadow-sm">
                            <svg class="radio-check w-4 h-4 text-white opacity-0 transition-opacity" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 16 16">
                                <path d="M3 8l4 4 6-8"/>
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><strong>{{ __('messages.server_url') }}:</strong> {{ __('messages.your_caldav_server') }}</p>
                        <p><strong>{{ __('messages.username') }}:</strong> {{ __('messages.your_account_username') }}</p>
                        <p><strong>{{ __('messages.password') }}:</strong> {{ __('messages.your_account_password') }}</p>
                        <p class="text-xs text-gray-500 mt-2">{{ __('messages.caldav_examples') }}</p>
                    </div>
                </div>
            </label>
        </div>
        
        <script>
        function updateRadioStyles() {
            // Get all radio indicators
            const indicators = document.querySelectorAll('.radio-indicator');
            const checks = document.querySelectorAll('.radio-check');
            
            // Get radio buttons
            const icloudRadio = document.querySelector('input[value="icloud"]');
            const otherRadio = document.querySelector('input[value="other"]');
            
            // Reset all to unchecked state
            indicators.forEach(indicator => {
                indicator.classList.remove('border-purple-600', 'bg-purple-600');
                indicator.classList.add('border-gray-300');
            });
            checks.forEach(check => {
                check.classList.add('opacity-0');
            });
            
            // Apply checked state to selected radio
            if (icloudRadio.checked) {
                const icloudIndicator = document.querySelector('#label-icloud .radio-indicator');
                const icloudCheck = document.querySelector('#label-icloud .radio-check');
                icloudIndicator.classList.remove('border-gray-300');
                icloudIndicator.classList.add('border-purple-600', 'bg-purple-600');
                icloudCheck.classList.remove('opacity-0');
            } else if (otherRadio.checked) {
                const otherIndicator = document.querySelector('#label-other .radio-indicator');
                const otherCheck = document.querySelector('#label-other .radio-check');
                otherIndicator.classList.remove('border-gray-300');
                otherIndicator.classList.add('border-purple-600', 'bg-purple-600');
                otherCheck.classList.remove('opacity-0');
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', updateRadioStyles);
        </script>
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
                        {{ __('messages.how_to_get_app_password') }}
                    </h4>
                    <ol class="space-y-3 text-sm">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">1</span>
                            <span class="text-gray-700">{!! __('messages.apple_step_1') !!}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">2</span>
                            <span class="text-gray-700">{!! __('messages.apple_step_2') !!}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">3</span>
                            <span class="text-gray-700">{!! __('messages.apple_step_3') !!}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">4</span>
                            <span class="text-gray-700">{!! __('messages.apple_step_4') !!}</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs mr-3">5</span>
                            <span class="text-gray-700">{{ __('messages.apple_step_5') }}</span>
                        </li>
                    </ol>
                </div>

                <!-- Apple ID -->
                <div class="mb-6">
                    <label for="apple_id" class="flex items-center space-x-2 text-sm font-bold text-gray-900 mb-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ __('messages.your_apple_id') }} <span class="text-red-500">*</span></span>
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
                        <span>{{ __('messages.app_specific_password') }} <span class="text-red-500">*</span></span>
                    </label>
                    <input 
                        type="text" 
                        name="app_password" 
                        id="app_password" 
                        placeholder="xxxx-xxxx-xxxx-xxxx"
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-purple-500 focus:ring focus:ring-purple-200 focus:ring-opacity-50 transition font-mono"
                    >
                    <p class="mt-2 text-sm text-gray-600">
                        {{ __('messages.app_password_format') }}
                    </p>
                    @error('app_password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Common Issues Warning -->
                <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4 mt-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="text-sm text-gray-700">
                            <strong class="text-gray-900">Common issues:</strong>
                            <ul class="mt-2 ml-4 list-disc space-y-1">
                                <li><strong>DO NOT</strong> use your regular Apple password - you must generate an App-Specific Password</li>
                                <li>Make sure your Apple ID is the email you use to sign in to iCloud</li>
                                <li>Copy the App-Specific Password immediately after generation (you won't see it again)</li>
                                <li>You can include or omit the dashes in the password (both work)</li>
                            </ul>
                        </div>
                    </div>
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
                        <span>{{ __('messages.caldav_server_url') }} <span class="text-red-500">*</span></span>
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
                        <span>{{ __('messages.username') }} <span class="text-red-500">*</span></span>
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
                        <span>{{ __('messages.password') }} <span class="text-red-500">*</span></span>
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
                        <span>{{ __('messages.email_address') }} <span class="text-gray-500">({{ __('messages.optional') }})</span></span>
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
                        {{ __('messages.email_if_different') }}
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
                <span>{{ __('messages.credentials_encrypted_secure') }}</span>
            </div>
            <button 
                type="submit"
                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition duration-150 shadow-lg hover:shadow-xl"
            >
                {{ __('messages.test_connection_continue') }}
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
                {!! __('messages.caldav_privacy_security') !!}
            </div>
        </div>
    </div>
</div>
@endsection

