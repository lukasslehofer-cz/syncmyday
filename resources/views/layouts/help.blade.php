<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Help Center') - SyncMyDay</title>
    <link rel="icon" type="image/png" href="/syncmyday-logo.png">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
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
            margin-top: 2rem;
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
            text-decoration: underline;
            font-weight: 500;
        }
        
        .help-content a:hover {
            color: #4f46e5;
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
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 via-white to-indigo-50 antialiased">
    <!-- Header -->
    <header class="bg-white/90 backdrop-blur-md shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
                </a>
                
                <div class="flex items-center space-x-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-gray-900">Sign In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white gradient-bg rounded-lg hover:opacity-90 shadow-md">
                            Sign Up
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-white gradient-bg rounded-lg hover:opacity-90 shadow-md">
                            Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="min-h-screen py-8 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8">
                <!-- Sidebar Navigation -->
                <aside class="lg:col-span-3">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Help Center</h3>
                        
                        <nav class="space-y-1">
                            <a href="{{ route('help.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Overview
                            </a>
                            
                            <a href="{{ route('help.faq') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.faq') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                FAQ
                            </a>
                            
                            <div class="pt-4 pb-2">
                                <h4 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Connection Guides</h4>
                            </div>
                            
                            <a href="{{ route('help.connect-google') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.connect-google') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                </svg>
                                Google Calendar
                            </a>
                            
                            <a href="{{ route('help.connect-microsoft') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.connect-microsoft') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                                </svg>
                                Microsoft 365
                            </a>
                            
                            <a href="{{ route('help.connect-apple') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.connect-apple') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                                </svg>
                                Apple iCloud
                            </a>
                            
                            <a href="{{ route('help.connect-caldav') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.connect-caldav') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                                </svg>
                                CalDAV (Generic)
                            </a>
                            
                            <a href="{{ route('help.connect-email') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.connect-email') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                Email Calendars
                            </a>
                            
                            <div class="pt-4 pb-2">
                                <h4 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Using SyncMyDay</h4>
                            </div>
                            
                            <a href="{{ route('help.sync-rules') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('help.sync-rules') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Creating Sync Rules
                            </a>
                        </nav>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-2">Need more help?</p>
                            <a href="mailto:support@syncmyday.com" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">
                                Contact Support →
                            </a>
                        </div>
                    </div>
                </aside>
                
                <!-- Main Content Area -->
                <div class="mt-8 lg:mt-0 lg:col-span-9">
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 sm:p-12 lg:p-16">
                        <div class="help-content">
                            @yield('content')
                        </div>
                    </div>
                    
                    <!-- Feedback Section -->
                    <div class="mt-8 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-100 rounded-2xl p-6">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Was this helpful?</h3>
                                <p class="text-sm text-gray-600 mb-3">Let us know if you have feedback or need additional help.</p>
                                <a href="mailto:support@syncmyday.com" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                    Send Feedback
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <p class="text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} SyncMyDay. All rights reserved.
            </p>
        </div>
    </footer>
</body>
</html>

