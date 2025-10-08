<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - SyncMyDay</title>
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
                
                <h1 class="text-4xl font-extrabold mb-4">Join SyncMyDay!</h1>
                <p class="text-lg text-indigo-100 mb-8">Start syncing your calendars in just 2 minutes. No credit card required.</p>
                
                <div class="space-y-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                        <h3 class="font-bold text-xl mb-4">What you'll get:</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Real-time calendar synchronization</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Connect multiple calendars</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Privacy-first approach</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span>Flexible sync rules</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-sm text-indigo-100">
                        <p>✨ Free plan includes basic features</p>
                        <p>🚀 Upgrade to Pro for unlimited sync rules</p>
                    </div>
                </div>
            </div>
            
            <!-- Decorative elements -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        </div>
        
        <!-- Right Side - Register Form -->
        <div class="lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl p-8 lg:p-10">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-2">Create Account</h2>
                        <p class="text-gray-600">Get started with your free account</p>
                    </div>
                    
                    @if($errors->any())
                    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4">
                        <div class="space-y-2">
                            @foreach($errors->all() as $error)
                            <div class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm font-medium text-red-800">{{ $error }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf
                        
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="John Doe"
                            >
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email address</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="you@example.com"
                            >
                        </div>
                        
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                            <input 
                                type="password" 
                                name="password" 
                                id="password" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Minimum 8 characters"
                            >
                            <p class="mt-2 text-xs text-gray-500">Must be at least 8 characters long</p>
                        </div>
                        
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                            <input 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                                placeholder="Re-enter your password"
                            >
                        </div>
                        
                        <div class="flex items-start">
                            <input 
                                type="checkbox" 
                                id="terms" 
                                required 
                                class="w-4 h-4 mt-1 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Terms of Service</a> and <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <button 
                            type="submit" 
                            class="w-full py-3 px-4 gradient-bg text-white font-semibold rounded-xl hover:opacity-90 shadow-lg transform hover:scale-[1.02] transition"
                        >
                            Create Account
                        </button>
                    </form>
                    
                    <div class="mt-8 text-center">
                        <p class="text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                                Sign in instead
                            </a>
                        </p>
                    </div>
                    
                    <div class="mt-6">
                        <a href="{{ route('home') }}" class="flex items-center justify-center text-sm text-gray-500 hover:text-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
