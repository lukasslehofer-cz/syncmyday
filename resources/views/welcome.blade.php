<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.home_page_title') }}</title>
    <link rel="icon" type="image/png" href="/syncmyday-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <style>
        [x-cloak] { display: none !important; }
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .gradient-text { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%); }
        .animated-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-white antialiased">
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-100">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold gradient-text">SyncMyDay</h1>
                </a>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_features') }}</a>
                    <a href="#how-it-works" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_how_it_works') }}</a>
                    <a href="#pricing" class="text-sm font-medium text-gray-600 hover:text-indigo-600">{{ __('messages.home_pricing') }}</a>
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
    
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-32 relative z-10">
            <div class="text-center">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-indigo-500 to-purple-500 text-white text-sm font-semibold mb-6 shadow-lg">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ __('messages.privacy_first') }}
                </div>
                
                <h2 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-gray-900 mb-6 leading-tight">
                    {{ __('messages.home_hero_title') }}<br>
                    <span class="gradient-text">{{ __('messages.home_hero_subtitle') }}</span>
                </h2>
                
                <p class="text-xl sm:text-2xl text-gray-700 mb-10 max-w-3xl mx-auto leading-relaxed">
                    {{ __('messages.home_hero_description') }}<br>
                    <span class="text-indigo-600 font-semibold">{{ __('messages.home_hero_tagline') }}</span>
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-white gradient-bg rounded-xl hover:opacity-90 shadow-xl transform hover:scale-105 transition">
                        {{ __('messages.start_with_first_month_free') }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </a>
                    <a href="#how-it-works" class="inline-flex items-center justify-center px-8 py-4 text-lg font-semibold text-gray-700 bg-white rounded-xl hover:bg-gray-50 border-2 border-indigo-200 shadow-lg transform hover:scale-105 transition">
                        {{ __('messages.home_learn_more') }}
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 md:grid-cols-3 gap-8 max-w-3xl mx-auto">
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-indigo-200 shadow-lg transform hover:scale-105 transition">
                        <div class="text-4xl font-bold gradient-text mb-2">{{ __('messages.home_stat_realtime') }}</div>
                        <div class="text-sm text-gray-600 font-medium">{{ __('messages.home_stat_instant_sync') }}</div>
                    </div>
                    <div class="bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-purple-200 shadow-lg transform hover:scale-105 transition">
                        <div class="text-4xl font-bold gradient-text mb-2">100%</div>
                        <div class="text-sm text-gray-600 font-medium">{{ __('messages.privacy_first') }}</div>
                    </div>
                    <div class="col-span-2 md:col-span-1 bg-white/60 backdrop-blur-sm rounded-2xl p-6 border border-pink-200 shadow-lg transform hover:scale-105 transition">
                        <div class="text-4xl font-bold gradient-text mb-2">{{ __('messages.home_stat_2mins') }}</div>
                        <div class="text-sm text-gray-600 font-medium">{{ __('messages.easy_setup') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Decorative elements -->
        <div class="absolute top-0 right-0 -mr-40 -mt-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 -ml-40 -mb-40 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">{{ __('messages.home_features_section_title') }}</h3>
                <h2 class="text-4xl font-extrabold text-gray-900 mt-2">{{ __('messages.home_features_heading') }}</h2>
                <p class="mt-4 text-xl text-gray-600 max-w-2xl mx-auto">{{ __('messages.home_features_description') }}</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="relative p-8 bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.privacy_first') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.feature_privacy_description') }}</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="relative p-8 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border border-purple-100 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.real_time_synchronization') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.feature_realtime_description') }}</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="relative p-8 bg-gradient-to-br from-pink-50 to-indigo-50 rounded-2xl border border-pink-100 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl gradient-bg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.feature_smart_rules') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.feature_smart_rules_description') }}</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="relative p-8 bg-white rounded-2xl border border-gray-200 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.email_calendars_support') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.feature_email_description') }}</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="relative p-8 bg-white rounded-2xl border border-gray-200 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.easy_setup') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.feature_easy_setup_description') }}</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="relative p-8 bg-white rounded-2xl border border-gray-200 hover:shadow-xl transition">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.feature_duplicate_prevention') }}</h3>
                    <p class="text-gray-600 leading-relaxed">{{ __('messages.feature_duplicate_prevention_description') }}</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- How It Works -->
    <section id="how-it-works" class="py-20 bg-gradient-to-br from-gray-50 to-indigo-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h3 class="text-base font-semibold text-indigo-600 tracking-wide uppercase">{{ __('messages.home_how_it_works_section_title') }}</h3>
                <h2 class="text-4xl font-extrabold text-gray-900 mt-2">{{ __('messages.home_how_it_works_heading') }}</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12">
                <div class="relative text-center">
                    <div class="w-16 h-16 mx-auto rounded-full gradient-bg flex items-center justify-center text-white text-2xl font-bold mb-6 shadow-lg">1</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.calendar_connections') }}</h3>
                    <p class="text-gray-600">{{ __('messages.connect_calendars_to_start_syncing') }}</p>
                </div>
                
                <div class="relative text-center">
                    <div class="w-16 h-16 mx-auto rounded-full gradient-bg flex items-center justify-center text-white text-2xl font-bold mb-6 shadow-lg">2</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.create_sync_rule') }}</h3>
                    <p class="text-gray-600">{{ __('messages.home_step2_description') }}</p>
                </div>
                
                <div class="relative text-center">
                    <div class="w-16 h-16 mx-auto rounded-full gradient-bg flex items-center justify-center text-white text-2xl font-bold mb-6 shadow-lg">3</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">{{ __('messages.home_step3_title') }}</h3>
                    <p class="text-gray-600">{{ __('messages.home_step3_description') }}</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Supported Platforms -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-8">{{ __('messages.home_platforms_title') }}</p>
            <div class="flex flex-wrap justify-center items-center gap-12">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-gray-900">{{ __('messages.google_calendar') }}</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-gray-900">{{ __('messages.microsoft_365') }}</span>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-r from-gray-800 to-black flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-semibold text-gray-900">{{ __('messages.apple_icloud') }}</span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Pricing Section -->
    <section id="pricing" class="py-20 gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-extrabold text-white mb-4">{{ __('messages.simple_pricing_for_powerful_sync') }}</h2>
                <p class="text-xl text-indigo-100">{{ __('messages.full_functionality_no_limits') }}</p>
                <div class="mt-6 inline-flex items-center px-6 py-3 bg-green-100 border-2 border-green-500 rounded-xl">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xl font-bold text-green-700">{{ __('messages.start_free_trial') }}</span>
                </div>
            </div>
            
            <!-- Pricing Cards -->
            <div class="grid md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Monthly Plan -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-200 hover:border-indigo-300 transition relative">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.monthly_plan') }}</h3>
                        <div class="flex items-baseline justify-center mb-2">
                            <span class="text-5xl font-bold text-gray-900">{{ \App\Helpers\PricingHelper::formatPrice(app()->getLocale(), 'monthly') }}</span>
                        </div>
                        <p class="text-gray-600">{{ __('messages.per_month') }}</p>
                        <p class="text-sm text-gray-500 mt-2">{{ __('messages.flexible_cancel_anytime') }}</p>
                    </div>
                    
                    <!-- Features -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">{{ __('messages.unlimited_sync_rules') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">{{ __('messages.unlimited_connected_calendars') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">{{ __('messages.real_time_sync') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700">{{ __('messages.priority_support') }}</span>
                        </div>
                    </div>
                    
                    <!-- CTA Button -->
                    <a href="{{ route('register') }}" class="block w-full px-6 py-3 text-center text-lg font-bold text-indigo-600 bg-white border-2 border-indigo-600 rounded-xl hover:bg-indigo-50 transition">
                        {{ __('messages.start_free_trial_now') }}
                    </a>
                </div>
                
                <!-- Yearly Plan (Recommended) -->
                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-2xl p-8 border-2 border-indigo-500 relative transform md:scale-105">
                    <!-- Best Value Badge -->
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold rounded-full shadow-lg">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ __('messages.best_value') }}
                        </span>
                    </div>
                    
                    <div class="text-center mb-6 mt-4">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.yearly_plan') }}</h3>
                        <div class="flex items-baseline justify-center mb-2">
                            <span class="text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $formattedPrice }}</span>
                        </div>
                        <p class="text-gray-700 font-medium">{{ __('messages.per_year') }}</p>
                        <div class="mt-3 inline-block px-4 py-2 bg-green-100 border-2 border-green-500 rounded-lg">
                            <p class="text-sm font-bold text-green-700">💰 {{ __('messages.save_with_yearly', ['percent' => \App\Helpers\PricingHelper::getYearlySavings(app()->getLocale())]) }}</p>
                        </div>
                    </div>
                    
                    <!-- Features -->
                    <div class="space-y-3 mb-8">
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-900 font-medium">{{ __('messages.unlimited_sync_rules') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-900 font-medium">{{ __('messages.unlimited_connected_calendars') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-900 font-medium">{{ __('messages.real_time_sync') }}</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-900 font-medium">{{ __('messages.priority_support') }}</span>
                        </div>
                    </div>
                    
                    <!-- CTA Button -->
                    <a href="{{ route('register') }}" class="block w-full px-6 py-3 text-center text-lg font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl hover:opacity-90 shadow-xl transform hover:scale-105 transition">
                        {{ __('messages.start_free_trial_now') }}
                    </a>
                </div>
            </div>
            
            <!-- Bottom Note -->
            <div class="text-center mt-8">
                <p class="text-white text-lg">
                    ✓ {{ __('messages.no_credit_card_required') }} • ✓ {{ __('messages.no_commitment') }}
                </p>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold">SyncMyDay</span>
                    </div>
                    <p class="text-sm text-gray-400">{{ __('messages.footer_description') }}</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-semibold mb-3">{{ __('messages.product') }}</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-sm text-gray-400 hover:text-white">{{ __('messages.home_features') }}</a></li>
                        <li><a href="#how-it-works" class="text-sm text-gray-400 hover:text-white">{{ __('messages.home_how_it_works') }}</a></li>
                        <li><a href="#pricing" class="text-sm text-gray-400 hover:text-white">{{ __('messages.billing') }}</a></li>
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
            
            <div class="pt-8 border-t border-gray-800 text-center">
                <p class="text-sm text-gray-400">&copy; {{ date('Y') }} SyncMyDay. {{ __('messages.all_rights_reserved') }}.</p>
            </div>
        </div>
    </footer>
</body>
</html>
