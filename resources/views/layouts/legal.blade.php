<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SyncMyDay')</title>
    <link rel="icon" type="image/png" href="/syncmyday-logo.png">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Prose styling for legal content */
        .legal-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid #6366f1;
        }
        
        .legal-content h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #374151;
            margin-top: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .legal-content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #4b5563;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .legal-content p {
            color: #6b7280;
            line-height: 1.75;
            margin-bottom: 1rem;
        }
        
        .legal-content strong {
            color: #374151;
            font-weight: 600;
        }
        
        .legal-content ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
            color: #6b7280;
        }
        
        .legal-content ul li {
            margin-bottom: 0.5rem;
            line-height: 1.75;
        }
        
        .legal-content a {
            color: #6366f1;
            text-decoration: underline;
            font-weight: 500;
        }
        
        .legal-content a:hover {
            color: #4f46e5;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-indigo-50 antialiased">
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
                    </a>
                    
                    @auth
                    <!-- Authenticated User Menu -->
                    <div class="hidden md:flex items-center space-x-1">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            {{ __('messages.dashboard') }}
                        </a>
                        <a href="{{ route('connections.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('connections.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            {{ __('messages.calendars') }}
                        </a>
                        <a href="{{ route('sync-rules.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sync-rules.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                            {{ __('messages.sync_rules') }}
                        </a>
                    </div>
                    @else
                    <!-- Guest User Menu (Homepage style) -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="{{ route('home') }}#features" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_features') }}</a>
                        <a href="{{ route('home') }}#how-it-works" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_how_it_works') }}</a>
                        <a href="{{ route('home') }}#pricing" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_pricing') }}</a>
                    </div>
                    @endauth
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        @if(auth()->user()->subscription_tier === 'free')
                        <a href="{{ route('billing') }}" class="hidden md:inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white gradient-bg hover:opacity-90 shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            {{ __('messages.upgrade_pro') }}
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
                            
                            <div x-show="open" @click.away="open = false" x-cloak style="display: none;" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 border border-gray-100">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-xs text-gray-500">{{ __('messages.signed_in_as') }}</p>
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('messages.account_settings') }}</a>
                                <a href="{{ route('billing') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('messages.billing') }}</a>
                                @can('admin')
                                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">{{ __('messages.admin_panel') }}</a>
                                @endcan
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        {{ __('messages.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">{{ __('messages.sign_in') }}</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white gradient-bg rounded-lg hover:opacity-90 shadow-md">
                            {{ __('messages.sign_up') }}
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    
    <!-- Alpine.js for interactivity -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Main Content -->
    <main class="min-h-screen py-12">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-100 mt-12">
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
                        <p class="text-sm text-gray-600">{{ __('messages.footer_tagline') }}</p>
                    </div>
                    
                    <!-- Product -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.product') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('home') }}#features" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.home_features') }}</a></li>
                            <li><a href="{{ route('home') }}#how-it-works" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.home_how_it_works') }}</a></li>
                            <li><a href="{{ route('home') }}#pricing" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.home_pricing') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Support -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.support') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.documentation') }}</a></li>
                            <li><a href="#" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.help_center') }}</a></li>
                            <li><a href="mailto:support@syncmyday.com" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.contact_us') }}</a></li>
                        </ul>
                    </div>
                    
                    <!-- Legal -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.legal') }}</h3>
                        <ul class="space-y-2">
                            <li><a href="{{ route('privacy') }}" class="text-sm text-gray-600 hover:text-indigo-600 {{ request()->routeIs('privacy') ? 'font-semibold text-indigo-600' : '' }}">{{ __('messages.privacy_policy') }}</a></li>
                            <li><a href="{{ route('terms') }}" class="text-sm text-gray-600 hover:text-indigo-600 {{ request()->routeIs('terms') ? 'font-semibold text-indigo-600' : '' }}">{{ __('messages.terms_of_service') }}</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-500">
                        &copy; {{ date('Y') }} SyncMyDay. {{ __('messages.all_rights_reserved') }}.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

