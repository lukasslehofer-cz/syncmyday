@extends('layouts.app')

@section('title', 'Billing')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Billing & Subscription</h1>
        <p class="text-lg text-gray-600">Simple pricing for powerful calendar sync</p>
    </div>
    
    <!-- Current Plan Status (for Pro users) -->
    @if($user->subscription_tier === 'pro')
    <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">Pro Subscription Active</p>
                    @if($user->subscription_ends_at)
                    <p class="text-sm text-gray-600">
                        Renews on {{ $user->subscription_ends_at->format('j. F Y') }}
                    </p>
                    @endif
                </div>
            </div>
            
            @if($user->stripe_customer_id)
            <a href="{{ route('billing.portal') }}" class="inline-flex items-center px-6 py-3 border-2 border-green-600 text-green-700 font-semibold rounded-xl hover:bg-green-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Manage Subscription
            </a>
            @endif
        </div>
    </div>
    @else
    <!-- Trial Info Banner for non-Pro users -->
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-8 text-center">
        <div class="flex items-center justify-center space-x-2 mb-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900">Získejte 1. měsíc zdarma!</h3>
        </div>
        <p class="text-lg text-gray-600 mb-2">Vyzkoušejte plnou funkčnost bez jakýchkoli omezení</p>
        <p class="text-sm text-gray-500">Není vyžadována platební karta • Kdykoliv zrušitelné</p>
    </div>
    @endif
    
    <!-- Pricing Card -->
    @if($user->subscription_tier !== 'pro')
    <div class="max-w-2xl mx-auto">
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-2xl p-10 border-2 border-indigo-500 relative">
            <!-- Popular Badge -->
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                <span class="inline-flex items-center px-8 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-base font-bold rounded-full shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    SyncMyDay Pro
                </span>
            </div>
            
            <div class="text-center mb-8 mt-4">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">Jeden plán pro všechny</h3>
                
                <!-- Pricing -->
                <div class="mb-6">
                    <div class="flex items-center justify-center space-x-3 mb-3">
                        <div class="inline-flex items-center px-4 py-2 bg-green-100 border-2 border-green-500 rounded-xl">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-lg font-bold text-green-700">1. měsíc ZDARMA</span>
                        </div>
                    </div>
                    
                    <div class="flex items-baseline justify-center mb-2">
                        <span class="text-6xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">249 Kč</span>
                        <span class="text-2xl text-gray-700 ml-3">/ rok</span>
                    </div>
                    <p class="text-gray-600">Po uplynutí zkušebního měsíce</p>
                </div>
                
                <p class="text-lg text-gray-700 mb-8">Plná funkčnost, žádná omezení, kdykoliv zrušitelné</p>
            </div>
            
            <!-- Features -->
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 mb-8">
                <h4 class="text-lg font-bold text-gray-900 mb-4 text-center">Co získáte:</h4>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900"><strong>Neomezený počet pravidel synchronizace</strong></span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900"><strong>Neomezený počet připojených kalendářů</strong></span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">Synchronizace v reálném čase (webhooky)</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">Podpora Google Calendar a Microsoft 365</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">Emailové kalendáře</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">Pokročilé filtry (busy only, work hours, ...)</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">Prioritní podpora</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">Všechny budoucí funkce zdarma</span>
                    </li>
                </ul>
            </div>
            
            <!-- CTA -->
            <form action="{{ route('billing.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-5 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-lg rounded-xl font-bold hover:opacity-90 shadow-xl transform hover:scale-105 transition">
                    Začít s 1. měsícem zdarma
                </button>
            </form>
            
            <p class="text-center text-sm text-gray-600 mt-4">
                ✓ Není vyžadována platební karta<br>
                ✓ Kdykoliv zrušitelné<br>
                ✓ Po měsíci pouze 249 Kč/rok
            </p>
        </div>
    </div>
    @endif
    
    <!-- FAQ Section -->
    <div class="mt-16 max-w-3xl mx-auto">
        <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">Často kladené otázky</h3>
        <div class="space-y-4">
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">Jak funguje zkušební měsíc zdarma?</h4>
                <p class="text-gray-600">První měsíc máte plný přístup ke všem funkcím SyncMyDay Pro zcela zdarma. Není vyžadována platební karta. Po uplynutí měsíce se můžete rozhodnout, zda chcete pokračovat s placeným předplatným (249 Kč/rok) nebo ne.</p>
            </div>
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">Mohu předplatné kdykoliv zrušit?</h4>
                <p class="text-gray-600">Ano! Předplatné můžete zrušit kdykoliv. Pokud zrušíte, budete mít přístup k Pro funkcím až do konce zaplaceného období.</p>
            </div>
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">Jaké platební metody přijímáte?</h4>
                <p class="text-gray-600">Přijímáme všechny hlavní platební karty přes Stripe, včetně Visa, Mastercard a American Express.</p>
            </div>
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">Co se stane po zkušebním měsíci?</h4>
                <p class="text-gray-600">Po uplynutí prvního měsíce zdarma budete automaticky požádáni o nastavení platby. Pokud si nepřejete pokračovat, můžete jednoduše předplatné neaktivovat a aplikace zůstane funkční s omezeními.</p>
            </div>
        </div>
    </div>
    
    <!-- Trust Badges -->
    <div class="mt-12 text-center">
        <p class="text-sm text-gray-500 mb-4">Bezpečné platby zajištěné přes</p>
        <div class="flex items-center justify-center space-x-8">
            <div class="text-gray-400 font-bold text-2xl">STRIPE</div>
            <div class="text-gray-400 font-bold text-xl">🔒 SSL</div>
        </div>
    </div>
</div>
@endsection
