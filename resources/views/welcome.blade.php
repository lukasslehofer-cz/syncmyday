<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SyncMyDay - Keep Your Calendars in Sync</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="px-4 py-6 sm:px-6 lg:px-8">
            <nav class="flex justify-between items-center max-w-7xl mx-auto">
                <h1 class="text-2xl font-bold text-indigo-600">SyncMyDay</h1>
                <div class="space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">Login</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Get Started
                    </a>
                </div>
            </nav>
        </header>
        
        <!-- Hero -->
        <main class="flex-grow flex items-center">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
                <h2 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6">
                    Never Double-Book<br>
                    <span class="text-indigo-600">Across Calendars</span>
                </h2>
                
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Automatically sync busy times between your work and personal calendars. 
                    Privacy-first • Simple • Reliable
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-indigo-600 text-white text-lg font-medium rounded-lg hover:bg-indigo-700 shadow-lg">
                        Start Free
                    </a>
                    <a href="#how-it-works" class="px-8 py-3 bg-white text-gray-700 text-lg font-medium rounded-lg hover:bg-gray-50 border border-gray-300">
                        Learn More
                    </a>
                </div>
                
                <!-- Features -->
                <div id="how-it-works" class="mt-20 grid md:grid-cols-3 gap-8 text-left">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-3xl mb-4">🔒</div>
                        <h3 class="text-lg font-semibold mb-2">Privacy First</h3>
                        <p class="text-gray-600">We store only event times, never titles or details. Encrypted at rest.</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-3xl mb-4">⚡</div>
                        <h3 class="text-lg font-semibold mb-2">Instant Sync</h3>
                        <p class="text-gray-600">Webhooks detect changes in real-time, creating blockers within minutes.</p>
                    </div>
                    
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <div class="text-3xl mb-4">🎯</div>
                        <h3 class="text-lg font-semibold mb-2">Smart Rules</h3>
                        <p class="text-gray-600">Filter by busy status, work hours, or all-day events.</p>
                    </div>
                </div>
                
                <!-- Supported Providers -->
                <div class="mt-16">
                    <p class="text-gray-500 text-sm mb-4">Supports:</p>
                    <div class="flex justify-center gap-8 items-center">
                        <span class="text-2xl font-semibold text-gray-700">Google Calendar</span>
                        <span class="text-2xl font-semibold text-gray-700">Microsoft 365</span>
                    </div>
                </div>
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} SyncMyDay. Privacy-first calendar synchronization.
                </p>
            </div>
        </footer>
    </div>
</body>
</html>

