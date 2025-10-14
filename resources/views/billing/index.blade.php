@extends('layouts.app')

@section('title', __('messages.billing'))

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mb-2">{{ __('messages.billing_and_subscription') }}</h1>
        <p class="text-lg text-gray-600">{{ __('messages.simple_pricing_for_powerful_sync') }}</p>
    </div>
    
    <!-- Current Plan Status (for Pro users with active subscription) -->
    @if($user->subscription_tier === 'pro' && $user->stripe_subscription_id && $subscription)
    @php
        $isCancelling = $subscription->cancel_at_period_end ?? false;
        $isTrialing = $subscription->status === 'trialing';
        
        // Get end/renew date based on subscription status
        $endTimestamp = null;
        if ($isCancelling && isset($subscription->cancel_at)) {
            $endTimestamp = $subscription->cancel_at;
        } elseif (isset($subscription->current_period_end)) {
            $endTimestamp = $subscription->current_period_end;
        }
        
        // Format date
        $renewDate = '';
        if ($endTimestamp) {
            $renewDate = \Carbon\Carbon::createFromTimestamp($endTimestamp)->format('j. F Y');
        } elseif ($user->subscription_ends_at) {
            // Fallback to DB date if Stripe date not available
            $renewDate = $user->subscription_ends_at->format('j. F Y');
        }
    @endphp
    
    <div class="mb-8 bg-gradient-to-r @if($isCancelling) from-orange-50 to-amber-50 border-orange-300 @else from-green-50 to-emerald-50 border-green-200 @endif border-2 rounded-2xl p-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-4">
                @if($isCancelling)
                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-orange-500 to-amber-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                @else
                <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-600 flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                @endif
                <div>
                    <p class="text-xl font-bold text-gray-900">
                        @if($isCancelling)
                            {{ __('messages.subscription_cancelling') }}
                        @elseif($isTrialing)
                            {{ __('messages.pro_trial_active') }}
                        @else
                            {{ __('messages.pro_subscription_active') }}
                        @endif
                    </p>
                    @if($renewDate)
                    <p class="text-sm @if($isCancelling) text-orange-700 @else text-gray-600 @endif">
                        @if($isCancelling)
                            {{ __('messages.ends_on') }} {{ $renewDate }}
                        @else
                            {{ __('messages.renews_on') }} {{ $renewDate }}
                        @endif
                    </p>
                    @endif
                </div>
            </div>
            
            @if($user->stripe_customer_id)
            <a href="{{ route('billing.manage') }}" class="inline-flex items-center px-6 py-3 border-2 @if($isCancelling) border-orange-600 text-orange-700 hover:bg-orange-50 @else border-green-600 text-green-700 hover:bg-green-50 @endif font-semibold rounded-xl transition">
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
    
    <!-- Pricing Cards -->
    @if($user->subscription_tier === 'free' || $user->isInTrial())
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-8">
            <h3 class="text-3xl font-bold text-gray-900 mb-4">{{ __('messages.choose_your_plan') }}</h3>
            <p class="text-lg text-gray-600">{{ __('messages.full_features_no_limits') }}</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Monthly Plan -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-gray-200 hover:border-indigo-300 transition relative">
                <div class="text-center mb-6">
                    <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.monthly_plan') }}</h4>
                    <div class="flex items-baseline justify-center mb-2">
                        <span class="text-5xl font-bold text-gray-900">{{ $monthlyPrice }}</span>
                        <span class="text-xl text-gray-600 ml-2">{{ __('messages.per_month') }}</span>
                    </div>
                    <p class="text-gray-600">{{ __('messages.flexible_cancel_anytime') }}</p>
                </div>
                
                <!-- Features -->
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.unlimited_sync_rules') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.unlimited_calendars') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.realtime_sync_webhooks') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.priority_support') }}</span>
                    </li>
                </ul>
                
                <form action="{{ route('billing.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="interval" value="monthly">
                    <button type="submit" class="w-full py-4 px-6 bg-gray-900 text-white font-bold rounded-xl hover:bg-gray-800 transition shadow-lg">
                        {{ __('messages.choose_monthly') }}
                    </button>
                </form>
            </div>
            
            <!-- Yearly Plan (Recommended) -->
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-2xl p-8 border-2 border-indigo-500 relative transform md:scale-105">
                <!-- Popular Badge -->
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-bold rounded-full shadow-lg">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{ __('messages.best_value') }}
                    </span>
                </div>
                
                <div class="text-center mb-6 mt-2">
                    <h4 class="text-2xl font-bold text-gray-900 mb-2">{{ __('messages.yearly_plan') }}</h4>
                    <div class="flex items-baseline justify-center mb-2">
                        <span class="text-5xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">{{ $yearlyPrice }}</span>
                        <span class="text-xl text-gray-600 ml-2">{{ __('messages.per_year') }}</span>
                    </div>
                    @if($yearlySavings > 0)
                    <div class="inline-flex items-center px-4 py-1 bg-green-100 border border-green-300 rounded-full">
                        <span class="text-sm font-bold text-green-700">💰 {{ __('messages.save_percent', ['percent' => $yearlySavings]) }}</span>
                    </div>
                    @endif
                </div>
                
                <!-- Features -->
                <ul class="space-y-3 mb-8">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.unlimited_sync_rules') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.unlimited_calendars') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.realtime_sync_webhooks') }}</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-700">{{ __('messages.priority_support') }}</span>
                    </li>
                </ul>
                
                <form action="{{ route('billing.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="interval" value="yearly">
                    <button type="submit" class="w-full py-4 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:opacity-90 transition shadow-xl transform hover:scale-105">
                        {{ __('messages.choose_yearly') }}
                    </button>
                </form>
            </div>
        </div>
        
        @if($user->isInTrial())
        <p class="text-center text-sm text-gray-600 mt-8">
            {{ __('messages.trial_days_remaining', ['days' => $trialDaysRemaining]) }}<br>
            {{ __('messages.no_charge_during_trial') }}
        </p>
        @endif
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
