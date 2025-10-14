{{-- Onboarding Progress Bar --}}
@auth
@php
    $user = auth()->user();
    // Check if user has completed onboarding steps
    $hasConnections = $user->calendarConnections()->count() > 0;
    $hasRules = $user->syncRules()->count() > 0;
    $onboardingComplete = $hasConnections && $hasRules;
    
    // Check if user has dismissed the progress bar (for current session only)
    $dismissedProgress = session('onboarding_progress_dismissed', false);
    
    // Show if:
    // - User is in trial
    // - NOT all steps completed
    // - User hasn't dismissed it (resets on new login)
    $shouldShow = $user->isInTrial() && !$onboardingComplete && !$dismissedProgress;
@endphp

@if($shouldShow)
<div id="onboarding-progress" class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 shadow-lg sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <!-- Progress Steps -->
            <div class="flex items-center space-x-2 sm:space-x-6 flex-1">
                <!-- Step 1: Connect Calendars -->
                <a href="{{ route('connections.index') }}" class="flex items-center space-x-2 sm:space-x-3 group hover:opacity-80 transition">
                    <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full {{ $hasConnections ? 'bg-green-500' : 'bg-white/20 group-hover:bg-white/30' }} flex items-center justify-center font-bold text-white shadow-lg transition">
                        @if($hasConnections)
                            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <span class="text-lg sm:text-xl">1</span>
                        @endif
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-white font-semibold text-sm">{{ __('messages.onboarding_step1_title') }}</p>
                        <p class="text-white/80 text-xs">{{ __('messages.onboarding_step1_desc') }}</p>
                    </div>
                </a>
                
                <!-- Arrow -->
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                
                <!-- Step 2: Create Rules -->
                <a href="{{ route('sync-rules.create') }}" class="flex items-center space-x-2 sm:space-x-3 group hover:opacity-80 transition">
                    <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full {{ $hasRules ? 'bg-green-500' : 'bg-white/20 group-hover:bg-white/30' }} flex items-center justify-center font-bold text-white shadow-lg transition">
                        @if($hasRules)
                            <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <span class="text-lg sm:text-xl">2</span>
                        @endif
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-white font-semibold text-sm">{{ __('messages.onboarding_step2_title') }}</p>
                        <p class="text-white/80 text-xs">{{ __('messages.onboarding_step2_desc') }}</p>
                    </div>
                </a>
                
                <!-- Arrow -->
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white/60 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
                
                <!-- Step 3: Done! -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-full {{ $onboardingComplete ? 'bg-yellow-400 animate-bounce' : 'bg-white/20' }} flex items-center justify-center font-bold text-white shadow-lg">
                        @if($onboardingComplete)
                            <span class="text-2xl">🎉</span>
                        @else
                            <span class="text-lg sm:text-xl">3</span>
                        @endif
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-white font-semibold text-sm">{{ __('messages.onboarding_step3_title') }}</p>
                        <p class="text-white/80 text-xs">{{ __('messages.onboarding_step3_desc') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Close Button -->
            <form action="{{ route('onboarding.dismiss') }}" method="POST" class="flex-shrink-0">
                @csrf
                <button type="submit" class="text-white/80 hover:text-white p-2 rounded-lg hover:bg-white/10 transition" title="{{ __('messages.dismiss') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endauth

