<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - SyncMyDay</title>
    <link rel="icon" type="image/png" href="/syncmyday-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        
        /* Help content styling */
        .help-content h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 1rem;
        }
        
        .help-content h2 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #374151;
            margin-top: 2.5rem;
            margin-bottom: 1.25rem;
            padding-top: 1rem;
            border-top: 2px solid #e5e7eb;
        }
        
        .help-content h2:first-of-type {
            margin-top: 1.5rem;
            border-top: none;
            padding-top: 0;
        }
        
        .help-content h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #4b5563;
            margin-top: 0.125rem;
            margin-bottom: 1rem;
        }
        
        .help-content h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #6b7280;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .help-content p {
            color: #6b7280;
            line-height: 1.75;
            margin-bottom: 1.25rem;
        }
        
        .help-content strong {
            color: #374151;
            font-weight: 600;
        }
        
        .help-content ul, .help-content ol {
            margin-left: 1.75rem;
            margin-bottom: 1.5rem;
            color: #6b7280;
        }
        
        .help-content ul {
            list-style-type: disc;
        }
        
        .help-content ol {
            list-style-type: decimal;
        }
        
        .help-content ul li, .help-content ol li {
            margin-bottom: 0.75rem;
            line-height: 1.75;
        }
        
        .help-content a {
            color: #6366f1;
            text-decoration: none;
            font-weight: 500;
        }
        
        .help-content a:hover {
            color: #4f46e5;
            text-decoration: underline;
        }
        
        /* Only underline links in paragraphs and lists */
        .help-content p a,
        .help-content li a {
            text-decoration: underline;
        }
        
        .help-content code {
            background-color: #f3f4f6;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            color: #db2777;
            font-family: monospace;
        }
        
        .help-content pre {
            background-color: #1f2937;
            color: #f3f4f6;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }
        
        .help-content pre code {
            background-color: transparent;
            padding: 0;
            color: inherit;
        }
        
        /* Image placeholder styles */
        .img-placeholder {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 3rem 1.5rem;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            margin: 1.5rem 0;
        }
        
        /* Step counter */
        .step-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            font-weight: bold;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    @auth
    <!-- Authenticated User Header -->
    <header class="bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
                    </a>

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
                </div>

                <div class="flex items-center space-x-4">
                    @if(auth()->user()->subscription_tier === 'free')
                    <a href="{{ route('billing') }}" class="hidden md:inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-white gradient-bg hover:opacity-90 shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        {{ __('messages.upgrade_pro') }}
                    </a>
                    @endif

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
                </div>
            </div>
        </div>
    </header>
    @else
    <!-- Guest User Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</h1>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}#features" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_features') }}</a>
                    <a href="{{ route('home') }}#how-it-works" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_how_it_works') }}</a>
                    <a href="{{ route('home') }}#pricing" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_pricing') }}</a>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">{{ __('messages.sign_in') }}</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white gradient-bg rounded-lg hover:opacity-90 shadow-md">
                        {{ __('messages.sign_up') }}
                    </a>
                </div>
            </div>
        </nav>
    </header>
    @endauth

    <!-- Main Content -->
    <main class="min-h-screen">
        @if(View::hasSection('sidebar'))
        <div class="py-8 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                    <!-- Sidebar -->
                    <aside class="lg:col-span-3 mb-8 lg:mb-0">
                        @yield('sidebar')
                    </aside>
                    
                    <!-- Main Content -->
                    <div class="lg:col-span-9">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        @else
        @yield('content')
        @endif
    </main>

    @auth
    <!-- Authenticated User Footer (Light) -->
    <footer class="bg-white border-t border-gray-200 py-12 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
                    </div>
                    <p class="text-sm text-gray-600">{{ __('messages.footer_tagline') }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.product') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.dashboard') }}</a></li>
                        <li><a href="{{ route('connections.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.calendars') }}</a></li>
                        <li><a href="{{ route('sync-rules.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.sync_rules') }}</a></li>
                        <li><a href="{{ route('billing') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.pricing') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.support') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('help.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.help_center') }}</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-sm text-gray-600 hover:text-indigo-600">Blog</a></li>
                        <li><a href="{{ route('contact') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.contact_us') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('messages.legal') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('privacy') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.privacy_policy') }}</a></li>
                        <li><a href="{{ route('terms') }}" class="text-sm text-gray-600 hover:text-indigo-600">{{ __('messages.terms_of_service') }}</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-gray-200">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} SyncMyDay. {{ __('messages.all_rights_reserved') }}
                </p>
            </div>
        </div>
    </footer>
    @else
    <!-- Guest User Footer (Dark) -->
    <footer class="bg-gray-900 text-gray-300 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-white">SyncMyDay</span>
                    </div>
                    <p class="text-sm text-gray-400">{{ __('messages.footer_tagline') }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold mb-3">{{ __('messages.product') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('home') }}#features" class="text-sm text-gray-400 hover:text-white">{{ __('messages.home_features') }}</a></li>
                        <li><a href="{{ route('home') }}#how-it-works" class="text-sm text-gray-400 hover:text-white">{{ __('messages.home_how_it_works') }}</a></li>
                        <li><a href="{{ route('home') }}#pricing" class="text-sm text-gray-400 hover:text-white">{{ __('messages.home_pricing') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold mb-3">{{ __('messages.support') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('help.index') }}" class="text-sm text-gray-400 hover:text-white">{{ __('messages.help_center') }}</a></li>
                        <li><a href="{{ route('blog.index') }}" class="text-sm text-gray-400 hover:text-white">Blog</a></li>
                        <li><a href="{{ route('contact') }}" class="text-sm text-gray-400 hover:text-white">{{ __('messages.contact_us') }}</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold mb-3">{{ __('messages.legal') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('privacy') }}" class="text-sm text-gray-400 hover:text-white">{{ __('messages.privacy_policy') }}</a></li>
                        <li><a href="{{ route('terms') }}" class="text-sm text-gray-400 hover:text-white">{{ __('messages.terms_of_service') }}</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-gray-800">
                <p class="text-center text-sm text-gray-400">
                    &copy; {{ date('Y') }} SyncMyDay. {{ __('messages.all_rights_reserved') }}
                </p>
            </div>
        </div>
    </footer>
    @endauth
</body>
</html>

