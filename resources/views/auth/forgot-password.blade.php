<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - SyncMyDay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-text { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 min-h-screen">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side - Branding & Info -->
        <div class="lg:w-1/2 gradient-bg text-white p-12 flex flex-col justify-center relative overflow-hidden">
            <div class="relative z-10 max-w-md mx-auto">
                <div class="flex items-center space-x-2 mb-8">
                    <div class="w-10 h-10 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold">SyncMyDay</span>
                </div>
                
                <h1 class="text-4xl font-extrabold mb-4">{{ __('messages.reset_password_title') }}</h1>
                <p class="text-lg text-indigo-100 mb-8">{{ __('messages.forgot_password_description') }}</p>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/20 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ __('messages.secure_fast') }}</h3>
                            <p class="text-sm text-indigo-100">{{ __('messages.reset_link_sent_instantly') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/20 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ __('messages.valid_for_1_hour') }}</h3>
                            <p class="text-sm text-indigo-100">{{ __('messages.reset_link_expires') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-white/20 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ __('messages.easy_process') }}</h3>
                            <p class="text-sm text-indigo-100">{{ __('messages.click_link_create_password') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        </div>
        
        <!-- Right Side - Forgot Password Form -->
        <div class="lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">{{ __('messages.forgot_password_heading') }}</h2>
                        <p class="text-gray-600">{{ __('messages.forgot_password_subtitle') }}</p>
                    </div>

                    @if(session('success'))
                    <div class="mb-6 bg-green-50 border-2 border-green-200 text-green-800 px-4 py-3 rounded-xl">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-medium">{{ session('success') }}</span>
                        </div>
                        <p class="mt-2 text-sm text-green-700">{{ __('messages.check_inbox_message') }}</p>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="mb-6">
                            <label for="email" class="block text-sm font-bold text-gray-700 mb-2">
                                {{ __('messages.email_address') }}
                            </label>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-indigo-500 transition @error('email') border-red-300 @enderror"
                                placeholder="{{ __('messages.placeholder_your_email') }}"
                            >
                            @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full gradient-bg text-white font-bold py-3 px-6 rounded-xl hover:opacity-90 transition shadow-lg mb-6">
                            {{ __('messages.send_reset_link') }}
                        </button>

                        <div class="text-center">
                            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                {{ __('messages.back_to_login_arrow') }}
                            </a>
                        </div>
                    </form>
                </div>

                <p class="text-center text-sm text-gray-600 mt-8">
                    {{ __('messages.dont_have_account') }} 
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">{{ __('messages.sign_up_link') }}</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>

