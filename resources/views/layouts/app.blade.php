<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'SyncMyDay - Calendar Sync')</title>
    
    <!-- Tailwind CSS via CDN for MVP -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 antialiased">
    <!-- Navigation -->
    @auth
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <span class="text-xl font-bold text-indigo-600">SyncMyDay</span>
                    </a>
                    
                    <!-- Desktop Navigation -->
                    <div class="hidden sm:ml-8 sm:flex sm:space-x-4">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('connections.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('connections.*') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                            Calendars
                        </a>
                        <a href="{{ route('sync-rules.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('sync-rules.*') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700' }}">
                            Sync Rules
                        </a>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    @if(auth()->user()->subscription_tier === 'free')
                    <a href="{{ route('billing') }}" class="hidden sm:inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        Upgrade to Pro
                    </a>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Mobile Navigation -->
        <div class="sm:hidden border-t border-gray-200">
            <div class="pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('connections.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('connections.*') ? 'bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    Calendars
                </a>
                <a href="{{ route('sync-rules.index') }}" class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('sync-rules.*') ? 'bg-indigo-50 border-l-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50' }}">
                    Sync Rules
                </a>
                <a href="{{ route('billing') }}" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:bg-gray-50">
                    Billing
                </a>
            </div>
        </div>
    </nav>
    @endauth
    
    <!-- Flash Messages -->
    @if(session('success') || session('error') || session('warning'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
        @endif
        
        @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
        @endif
        
        @if(session('warning'))
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Main Content -->
    <main class="py-6 sm:py-10">
        @yield('content')
    </main>
    
    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} SyncMyDay. Privacy-first calendar synchronization.
                @can('admin')
                | <a href="{{ route('admin.dashboard') }}" class="text-indigo-600 hover:text-indigo-700">Admin</a>
                @endcan
            </p>
        </div>
    </footer>
</body>
</html>

