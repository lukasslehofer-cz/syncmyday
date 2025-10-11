@extends('layouts.app')

@section('title', 'Billing')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">Billing & Subscription</h1>
        <p class="text-lg text-gray-600">Simple pricing for powerful calendar sync</p>
    </div>
    
    <!-- Current Plan Status (for Pro users with active subscription) -->
    @if($user->subscription_tier === 'pro' && $user->stripe_subscription_id)
    <div class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ __('messages.pro_subscription_active') }}</p>
                    @if($user->subscription_ends_at)
                    <p class="text-sm text-gray-600">
                        {{ __('messages.renews_on', ['date' => $user->subscription_ends_at->format('j. F Y')]) }}
                    </p>
                    @endif
                </div>
            </div>
            
            @if($user->stripe_customer_id)
            <a href="{{ route('billing.manage') }}" class="inline-flex items-center px-6 py-3 border-2 border-green-600 text-green-700 font-semibold rounded-xl hover:bg-green-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('messages.manage_subscription') }}
            </a>
            @endif
        </div>
    </div>
    
    <!-- Trial Status (for users in trial without payment method) -->
    @elseif($user->isInTrial())
    <div class="mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xl font-bold text-gray-900">{{ __('messages.trial_period_active') }}</p>
                    <p class="text-sm text-gray-600">
                        {{ __('messages.trial_remaining_days', ['days' => $user->getRemainingTrialDays(), 'date' => $user->subscription_ends_at->format('j. F Y')]) }}
                    </p>
                    @if(!$user->stripe_subscription_id)
                    <p class="text-sm font-medium text-amber-600 mt-1">{{ __('messages.payment_method_not_set') }}</p>
                    @else
                    <p class="text-sm text-green-600 mt-1">{{ __('messages.payment_method_set') }}</p>
                    @endif
                </div>
            </div>
            
            @if(!$user->stripe_subscription_id)
            <form action="{{ route('billing.trial-checkout') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-semibold rounded-xl hover:opacity-90 shadow-md transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    {{ __('messages.set_payment_card') }}
                </button>
            </form>
            @else
            <a href="{{ route('billing.manage') }}" class="inline-flex items-center px-6 py-3 border-2 border-blue-600 text-blue-700 font-semibold rounded-xl hover:bg-blue-50 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ __('messages.manage_subscription') }}
            </a>
            @endif
        </div>
    </div>
    
    <!-- Expired trial banner -->
    @elseif($user->subscription_tier === 'free')
    <div class="mb-8 bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-2xl p-8 text-center">
        <div class="flex items-center justify-center space-x-2 mb-3">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <h3 class="text-2xl font-bold text-gray-900">{{ __('messages.trial_period_expired') }}</h3>
        </div>
        <p class="text-lg text-gray-600 mb-2">{{ __('messages.activate_subscription_continue') }}</p>
        <p class="text-sm text-gray-500">{{ __('messages.pricing_yearly_cancelable') }}</p>
    </div>
    @endif
    
    <!-- Pricing Card -->
    @if($user->subscription_tier === 'free' || ($user->isInTrial() && !$user->stripe_subscription_id))
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
                <h3 class="text-3xl font-bold text-gray-900 mb-4">{{ __('messages.one_plan_for_all') }}</h3>
                
                <!-- Pricing -->
                <div class="mb-6">
                    @if($user->subscription_tier === 'free')
                    <div class="flex items-center justify-center space-x-3 mb-3">
                        <div class="inline-flex items-center px-4 py-2 bg-green-100 border-2 border-green-500 rounded-xl">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-lg font-bold text-green-700">{{ __('messages.activate_subscription') }}</span>
                        </div>
                    </div>
                    @endif
                    
                    <div class="flex items-baseline justify-center mb-2">
                        <span class="text-6xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $formattedPrice }}</span>
                        <span class="text-2xl text-gray-700 ml-3">{{ __('messages.per_year') }}</span>
                    </div>
                    <p class="text-gray-600">{{ __('messages.after_trial_month') }}</p>
                </div>
                
                <p class="text-lg text-gray-700 mb-8">{{ __('messages.full_features_no_limits') }}</p>
            </div>
            
            <!-- Features -->
            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 mb-8">
                <h4 class="text-lg font-bold text-gray-900 mb-4 text-center">{{ __('messages.what_you_get') }}</h4>
                <ul class="space-y-3">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900"><strong>{{ __('messages.unlimited_sync_rules') }}</strong></span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900"><strong>{{ __('messages.unlimited_calendars') }}</strong></span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">{{ __('messages.realtime_sync_webhooks') }}</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">{{ __('messages.google_microsoft_support') }}</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">{{ __('messages.email_calendars') }}</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">{{ __('messages.advanced_filters') }}</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">{{ __('messages.priority_support') }}</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mt-0.5 shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <span class="ml-3 text-gray-900">{{ __('messages.all_future_features_free') }}</span>
                    </li>
                </ul>
            </div>
            
            <!-- CTA -->
            @if($user->isInTrial() && !$user->stripe_subscription_id)
            <form action="{{ route('billing.trial-checkout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-5 px-6 bg-gradient-to-r from-amber-500 to-orange-600 text-white text-lg rounded-xl font-bold hover:opacity-90 shadow-xl transform hover:scale-105 transition">
                    {{ __('messages.set_payment_for_auto_renewal') }}
                </button>
            </form>
            
            <p class="text-center text-sm text-gray-600 mt-4">
                {{ __('messages.trial_payment_info_1') }}<br>
                {{ __('messages.trial_payment_info_2') }}<br>
                {{ __('messages.trial_payment_info_3') }}
            </p>
            @else
            <form action="{{ route('billing.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-5 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-lg rounded-xl font-bold hover:opacity-90 shadow-xl transform hover:scale-105 transition">
                    {{ __('messages.activate_subscription_price') }}
                </button>
            </form>
            
            <p class="text-center text-sm text-gray-600 mt-4">
                {{ __('messages.payment_info_1') }}<br>
                {{ __('messages.payment_info_2') }}<br>
                {{ __('messages.payment_info_3') }}
            </p>
            @endif
        </div>
    </div>
    @endif
    
    <!-- FAQ Section -->
    <div class="mt-16 max-w-3xl mx-auto">
        <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">{{ __('messages.faq') }}</h3>
        <div class="space-y-4">
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">{{ __('messages.faq_trial_month') }}</h4>
                <p class="text-gray-600">{{ __('messages.faq_trial_month_answer') }}</p>
            </div>
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">{{ __('messages.faq_cancel_anytime') }}</h4>
                <p class="text-gray-600">{{ __('messages.faq_cancel_anytime_answer') }}</p>
            </div>
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">{{ __('messages.faq_payment_methods') }}</h4>
                <p class="text-gray-600">{{ __('messages.faq_payment_methods_answer') }}</p>
            </div>
            <div class="bg-white rounded-xl p-6 border-2 border-gray-200 hover:border-indigo-300 transition">
                <h4 class="font-bold text-gray-900 mb-2">{{ __('messages.faq_after_trial') }}</h4>
                <p class="text-gray-600">{{ __('messages.faq_after_trial_answer') }}</p>
            </div>
        </div>
    </div>
    
    <!-- Trust Badges -->
    <div class="mt-12 text-center">
        <p class="text-sm text-gray-500 mb-4">{{ __('messages.secure_payments_via') }}</p>
        <div class="flex items-center justify-center space-x-8">
            <div class="text-gray-400 font-bold text-2xl">STRIPE</div>
            <div class="text-gray-400 font-bold text-xl">🔒 SSL</div>
        </div>
    </div>
</div>
@endsection
