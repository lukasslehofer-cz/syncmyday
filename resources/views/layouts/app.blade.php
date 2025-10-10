<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SyncMyDay - Calendar Sync')</title>
    <link rel="icon" type="image/png" href="/syncmyday-logo.png">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Config -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Smooth transitions */
        * {
            transition: all 0.2s ease-in-out;
        }
        
        /* Gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-light {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-indigo-50 antialiased">
    <!-- Navigation -->
    @auth
    <nav class="bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden md:ml-10 md:flex md:space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('connections.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('connections.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Calendars
                        </a>
                        <a href="{{ route('sync-rules.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sync-rules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            Sync Rules
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->subscription_tier === 'free')
                    <a href="{{ route('billing') }}" class="hidden md:inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white gradient-bg hover:opacity-90 shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Upgrade Pro
                    </a>
                    @endif
                    
                    <!-- User Menu -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center text-white text-xs font-semibold">
                                {{ strtoupper(substr(auth()->user()->email, 0, 2)) }}
                            </div>
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-100">
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-xs text-gray-500">Signed in as</p>
                                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->email }}</p>
                            </div>
                            <a href="{{ route('account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Account Settings</a>
                            <a href="{{ route('billing') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Billing</a>
                            @can('admin')
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Admin Panel</a>
                            @endcan
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div class="md:hidden border-t border-gray-100" x-data="{ open: false }">
            <button @click="open = !open" class="w-full px-4 py-3 text-left text-sm font-medium text-gray-700 hover:bg-gray-50">
                Menu
            </button>
            <div x-show="open" x-cloak class="pb-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('connections.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('connections.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50' }}">
                    Calendars
                </a>
                <a href="{{ route('sync-rules.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('sync-rules.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50' }}">
                    Sync Rules
                </a>
                <a href="{{ route('email-calendars.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('email-calendars.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50' }}">
                    Email Calendars
                </a>
                <a href="{{ route('account.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('account.*') ? 'bg-indigo-50 text-indigo-700 border-l-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50' }}">
                    Account Settings
                </a>
                <a href="{{ route('billing') }}" class="block px-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">
                    Billing
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    @endauth
    
    <!-- Trial Banner -->
    @auth
    @if(auth()->user()->isInTrial())
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b-2 border-amber-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-amber-900">
                            @if(auth()->user()->isTrialExpiringSoon())
                                <strong>⚠️ Zkušební období brzy končí!</strong> Zbývá vám {{ auth()->user()->getRemainingTrialDays() }} {{ auth()->user()->getRemainingTrialDays() === 1 ? 'den' : (auth()->user()->getRemainingTrialDays() <= 4 ? 'dny' : 'dní') }}.
                            @else
                                <strong>🎉 Zkušební období aktivní!</strong> Máte plný přístup k SyncMyDay Pro ještě {{ auth()->user()->getRemainingTrialDays() }} {{ auth()->user()->getRemainingTrialDays() === 1 ? 'den' : (auth()->user()->getRemainingTrialDays() <= 4 ? 'dny' : 'dní') }}.
                            @endif
                        </p>
                        @if(!auth()->user()->stripe_subscription_id)
                        <p class="text-xs text-amber-700 mt-0.5">
                            Nezapomeňte nastavit platební metodu, aby nedošlo k přerušení služby.
                        </p>
                        @endif
                    </div>
                </div>
                @if(!auth()->user()->stripe_subscription_id)
                <a href="{{ route('billing') }}" class="flex-shrink-0 inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                    Nastavit platbu
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                @endif
            </div>
        </div>
    </div>
    @endif
    @endauth
    
    <!-- Flash Messages -->
    @if(session('success') || session('error') || session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6" x-data="{ show: true }">
        @if(session('success'))
        <div x-show="show" class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-400 hover:text-green-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div x-show="show" class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-400 hover:text-red-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif
        
        @if(session('warning'))
        <div x-show="show" class="bg-gradient-to-r from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4 shadow-sm">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                </div>
                <button @click="show = false" class="text-yellow-400 hover:text-yellow-600">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Main Content -->
    <main class="min-h-[calc(100vh-16rem)] py-8 sm:py-12">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-100 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="col-span-1">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <span class="text-lg font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
                        </div>
                        <p class="text-sm text-gray-600">Privacy-first calendar synchronization made simple.</p>
                    </div>
                    
                    <!-- Product -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Product</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">Dashboard</a></li>
                            <li><a href="{{ route('connections.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">Calendars</a></li>
                            <li><a href="{{ route('sync-rules.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">Sync Rules</a></li>
                            <li><a href="{{ route('billing') }}" class="text-sm text-gray-600 hover:text-indigo-600">Pricing</a></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Support</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Documentation</a></li>
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Help Center</a></li>
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Contact Us</a></li>
                        </ul>
                    </div>
                    
                    <!-- Legal -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Legal</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Privacy Policy</a></li>
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">Terms of Service</a></li>
                            @can('admin')
                            <li><a href="{{ route('admin.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Admin Panel</a></li>
                            @endcan
                        </ul>
                    </div>
                </div>
                
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} SyncMyDay. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

