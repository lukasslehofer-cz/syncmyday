<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Stránka nenalezena | SyncMyDay</title>
    <link rel="icon" type="image/png" href="/syncmyday-logo.png">
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
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
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .spin-slow {
            animation: spin-slow 20s linear infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8">
        <!-- Logo -->
        <div class="mb-8">
            <a href="{{ url('/') }}" class="flex items-center space-x-2">
                <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">SyncMyDay</span>
            </a>
        </div>
        
        <!-- Main Content -->
        <div class="max-w-2xl mx-auto text-center">
            <!-- Animated 404 -->
            <div class="mb-8 relative">
                <!-- Background Circle -->
                <div class="absolute inset-0 flex items-center justify-center opacity-10">
                    <div class="w-96 h-96 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 spin-slow"></div>
                </div>
                
                <!-- 404 Text -->
                <div class="relative">
                    <h1 class="text-9xl sm:text-[12rem] font-extrabold bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent float-animation">
                        404
                    </h1>
                </div>
            </div>
            
            <!-- Calendar Icon with Clock -->
            <div class="mb-8 flex justify-center">
                <div class="relative">
                    <div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <!-- Confused emoji -->
                    <div class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-2xl">
                        😵
                    </div>
                </div>
            </div>
            
            <!-- Message -->
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
                Ouha! Tato událost nebyla nalezena
            </h2>
            
            <p class="text-lg text-gray-600 mb-4 max-w-lg mx-auto">
                Vypadá to, že tato stránka byla nesynchronizována s realitou... 
                nebo možná smazána rychleji, než jsme ji stihli najít. 🗓️
            </p>
            
            <p class="text-base text-gray-500 mb-8 max-w-md mx-auto">
                Možná se stránka přesunula, nebo jste klikli na starý odkaz. 
                Zkusme vás vrátit zpátky na cestu!
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl shadow-lg transition transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Zpět na hlavní stránku
                </a>
                
                @auth
                <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-indigo-600 font-semibold rounded-xl shadow-md border-2 border-indigo-200 transition transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Přejít na Dashboard
                </a>
                @else
                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-indigo-600 font-semibold rounded-xl shadow-md border-2 border-indigo-200 transition transform hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Přihlásit se
                </a>
                @endauth
            </div>
            
            <!-- Fun Facts -->
            <div class="mt-16 p-6 bg-white/60 backdrop-blur rounded-2xl shadow-lg border border-indigo-100 max-w-md mx-auto">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="text-left">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">Věděli jste?</h3>
                        <p class="text-sm text-gray-600">
                            První HTTP chyba 404 byla zaznamenána v roce 1994. Od té doby pomohla milionům uživatelů zjistit, že něco není tam, kde by mělo být! 🚀
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="mt-16 text-center">
            <p class="text-sm text-gray-500">
                Potřebujete pomoc? <a href="{{ route('contact') }}" class="text-indigo-600 hover:text-indigo-700 font-medium underline">Kontaktujte nás</a>
            </p>
        </div>
    </div>
</body>
</html>

