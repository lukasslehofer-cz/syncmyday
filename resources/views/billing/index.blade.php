@extends('layouts.app')

@section('title', 'Billing')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Billing & Subscription</h1>
        <p class="mt-2 text-gray-600">Manage your subscription plan</p>
    </div>
    
    <!-- Current Plan -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Current Plan</h2>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-gray-900">
                    {{ ucfirst($user->subscription_tier) }}
                    @if($user->subscription_tier === 'pro')
                    <span class="text-base font-normal text-gray-500">Plan</span>
                    @endif
                </p>
                @if($user->subscription_tier === 'pro' && $user->subscription_ends_at)
                <p class="text-sm text-gray-500 mt-1">
                    Renews on {{ $user->subscription_ends_at->format('F j, Y') }}
                </p>
                @endif
            </div>
            
            @if($user->subscription_tier === 'pro' && $user->stripe_customer_id)
            <a href="{{ route('billing.portal') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Manage Subscription
            </a>
            @endif
        </div>
    </div>
    
    <!-- Pricing Plans -->
    @if($user->subscription_tier === 'free')
    <div class="grid md:grid-cols-2 gap-6">
        <!-- Free Plan -->
        <div class="bg-white rounded-lg shadow p-6 border-2 border-gray-200">
            <h3 class="text-xl font-semibold mb-2">Free</h3>
            <p class="text-3xl font-bold mb-4">$0<span class="text-base font-normal text-gray-500">/month</span></p>
            
            <ul class="space-y-3 mb-6">
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    1 sync rule
                </li>
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Unlimited calendar connections
                </li>
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Real-time sync
                </li>
            </ul>
            
            <button disabled class="w-full py-2 px-4 bg-gray-300 text-gray-600 rounded-md cursor-not-allowed">
                Current Plan
            </button>
        </div>
        
        <!-- Pro Plan -->
        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg shadow-lg p-6 border-2 border-indigo-500">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xl font-semibold">Pro</h3>
                <span class="px-2 py-1 bg-indigo-600 text-white text-xs font-semibold rounded">POPULAR</span>
            </div>
            <p class="text-3xl font-bold mb-4">$9<span class="text-base font-normal text-gray-600">/month</span></p>
            
            <ul class="space-y-3 mb-6">
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <strong>Unlimited sync rules</strong>
                </li>
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Unlimited calendar connections
                </li>
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Priority support
                </li>
                <li class="flex items-center text-sm">
                    <svg class="h-5 w-5 text-indigo-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    Advanced filters
                </li>
            </ul>
            
            <form action="{{ route('billing.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2 px-4 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 font-medium">
                    Upgrade to Pro
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

