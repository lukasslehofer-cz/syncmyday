{{-- Cookie Consent Banner --}}
<div x-data="cookieConsent()" x-show="showBanner" x-cloak
     class="fixed bottom-0 left-0 right-0 z-[9999] p-4 sm:p-6"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 translate-y-full"
     x-transition:enter-end="opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 translate-y-0"
     x-transition:leave-end="opacity-0 translate-y-full">
    
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            
            {{-- Main Banner --}}
            <div x-show="!showSettings" class="p-4 sm:p-6">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl gradient-bg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.cookie_consent_title') }}</h3>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ __('messages.cookie_consent_text') }}
                            <a href="{{ route('privacy') }}" class="text-indigo-600 hover:text-indigo-700 underline font-medium">{{ __('messages.cookie_privacy_link') }}</a>
                        </p>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row gap-2 lg:flex-shrink-0">
                        <button @click="showSettings = true" 
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition order-3 sm:order-1">
                            {{ __('messages.cookie_settings') }}
                        </button>
                        <button @click="rejectAll()" 
                                class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-xl transition order-2">
                            {{ __('messages.cookie_reject_all') }}
                        </button>
                        <button @click="acceptAll()" 
                                class="px-6 py-2.5 text-sm font-medium text-white gradient-bg hover:opacity-90 rounded-xl shadow-md transition order-1 sm:order-3">
                            {{ __('messages.cookie_accept_all') }}
                        </button>
                    </div>
                </div>
            </div>
            
            {{-- Settings Panel --}}
            <div x-show="showSettings" x-cloak class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ __('messages.cookie_settings') }}</h3>
                    <button @click="showSettings = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="space-y-4 mb-6">
                    {{-- Necessary Cookies --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 pr-4">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-semibold text-gray-900">{{ __('messages.cookie_necessary') }}</h4>
                                    <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-700 rounded-full">{{ __('messages.cookie_always_active') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ __('messages.cookie_necessary_desc') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <div class="w-12 h-7 bg-indigo-500 rounded-full flex items-center justify-end px-1 cursor-not-allowed opacity-70">
                                    <div class="w-5 h-5 bg-white rounded-full shadow"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Analytics Cookies --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 pr-4">
                                <h4 class="font-semibold text-gray-900">{{ __('messages.cookie_analytics') }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ __('messages.cookie_analytics_desc') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button @click="preferences.analytics = !preferences.analytics" 
                                        :class="preferences.analytics ? 'bg-indigo-500' : 'bg-gray-300'"
                                        class="w-12 h-7 rounded-full flex items-center px-1 transition-colors cursor-pointer">
                                    <div :class="preferences.analytics ? 'translate-x-5' : 'translate-x-0'"
                                         class="w-5 h-5 bg-white rounded-full shadow transform transition-transform"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Marketing Cookies --}}
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 pr-4">
                                <h4 class="font-semibold text-gray-900">{{ __('messages.cookie_marketing') }}</h4>
                                <p class="text-sm text-gray-600 mt-1">{{ __('messages.cookie_marketing_desc') }}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <button @click="preferences.marketing = !preferences.marketing" 
                                        :class="preferences.marketing ? 'bg-indigo-500' : 'bg-gray-300'"
                                        class="w-12 h-7 rounded-full flex items-center px-1 transition-colors cursor-pointer">
                                    <div :class="preferences.marketing ? 'translate-x-5' : 'translate-x-0'"
                                         class="w-5 h-5 bg-white rounded-full shadow transform transition-transform"></div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-2 justify-end">
                    <button @click="rejectAll()" 
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 rounded-xl transition">
                        {{ __('messages.cookie_reject_all') }}
                    </button>
                    <button @click="acceptAll()" 
                            class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                        {{ __('messages.cookie_accept_all') }}
                    </button>
                    <button @click="savePreferences()" 
                            class="px-6 py-2.5 text-sm font-medium text-white gradient-bg hover:opacity-90 rounded-xl shadow-md transition">
                        {{ __('messages.cookie_save_settings') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function cookieConsent() {
        return {
            showBanner: false,
            showSettings: false,
            preferences: {
                necessary: true,
                analytics: true,
                marketing: true
            },
            
            init() {
                const stored = localStorage.getItem('cookie_consent');
                if (stored) {
                    this.preferences = JSON.parse(stored);
                    this.loadScripts();
                } else {
                    this.showBanner = true;
                }
                
                // Listen for manual trigger to open settings
                window.addEventListener('open-cookie-settings', () => {
                    this.showBanner = true;
                    this.showSettings = true;
                });
            },
            
            acceptAll() {
                this.preferences = {
                    necessary: true,
                    analytics: true,
                    marketing: true
                };
                this.save();
            },
            
            rejectAll() {
                this.preferences = {
                    necessary: true,
                    analytics: false,
                    marketing: false
                };
                this.save();
            },
            
            savePreferences() {
                this.save();
            },
            
            save() {
                localStorage.setItem('cookie_consent', JSON.stringify(this.preferences));
                this.showBanner = false;
                this.showSettings = false;
                this.loadScripts();
            },
            
            loadScripts() {
                // Google Consent Mode v2: always signal the current choice
                // (granted or denied) so GA can model conversions either way.
                window.dispatchEvent(new CustomEvent('cookie-consent-updated', { detail: this.preferences }));

                // Conditional script loading (Meta Pixel) - only on granted consent
                if (this.preferences.analytics) {
                    window.dispatchEvent(new CustomEvent('cookie-consent-analytics'));
                }
                if (this.preferences.marketing) {
                    window.dispatchEvent(new CustomEvent('cookie-consent-marketing'));
                }
            }
        }
    }
    
    // Global function to open cookie settings from footer link
    function openCookieSettings() {
        window.dispatchEvent(new CustomEvent('open-cookie-settings'));
    }
</script>

