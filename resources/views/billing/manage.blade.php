@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    {{-- Success/Error Messages --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if(request('payment_method_updated'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-green-800 font-medium">{{ __('messages.payment_method_updated') }}</p>
        </div>
    </div>
    @endif

    {{-- Header --}}
    <div class="mb-8">
        <div class="flex items-center space-x-3 mb-4">
            <a href="{{ route('billing') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h1 class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                {{ __('messages.manage_subscription') }}
            </h1>
        </div>
        <p class="text-lg text-gray-600">
            {{ __('messages.manage_subscription_description') }}
        </p>
    </div>

    {{-- Subscription Status --}}
    @if($subscription)
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.subscription_status') }}</h2>
            </div>
        </div>
        
        <div class="p-6 lg:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.plan') }}</p>
                    <p class="text-lg font-bold text-gray-900">Pro</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.status') }}</p>
                    <p class="text-lg font-bold">
                        @if($subscription->status === 'active')
                            <span class="text-green-600">✓ {{ __('messages.active') }}</span>
                        @elseif($subscription->status === 'trialing')
                            <span class="text-blue-600">🎉 {{ __('messages.trial_active') }}</span>
                        @elseif($subscription->cancel_at_period_end)
                            <span class="text-orange-600">⚠️ {{ __('messages.cancelling') }}</span>
                        @else
                            <span class="text-gray-600">{{ ucfirst($subscription->status) }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">
                        @if($subscription->cancel_at_period_end)
                            {{ __('messages.ends_on') }}
                        @else
                            {{ __('messages.renews_on') }}
                        @endif
                    </p>
                    <p class="text-lg font-bold text-gray-900">
                        {{ \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->translatedFormat('j. F Y') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium">{{ __('messages.price') }}</p>
                    <p class="text-lg font-bold text-gray-900">
                        @php
                            $price = $subscription->items->data[0]->price;
                            $interval = $price->recurring->interval ?? 'year';
                            $intervalLabel = $interval === 'month' ? __('messages.month') : __('messages.year');
                        @endphp
                        {{ number_format($price->unit_amount / 100, 2) }}
                        {{ strtoupper($price->currency) }} / {{ $intervalLabel }}
                    </p>
                </div>
            </div>

            @if($subscription->cancel_at_period_end)
            <div class="mt-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-orange-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="text-orange-900 font-medium">{{ __('messages.subscription_will_end') }}</p>
                        <p class="text-orange-800 text-sm mt-1">{{ __('messages.subscription_end_date_notice', ['date' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->translatedFormat('j. F Y')]) }}</p>
                        <form method="POST" action="{{ route('billing.reactivate') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="text-sm bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg font-medium transition">
                                {{ __('messages.reactivate_subscription') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Scheduled Interval Change Notice --}}
    @if($subscription && $scheduledInterval && $scheduleChangeDate)
    @php
        $scheduledIntervalLabel = $scheduledInterval === 'month' ? __('messages.monthly_plan') : __('messages.yearly_plan');
        $changeDate = \Carbon\Carbon::createFromTimestamp($scheduleChangeDate)->translatedFormat('j. F Y');
    @endphp
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-300 overflow-hidden mb-6">
        <div class="p-6 lg:p-8">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">📅 {{ __('messages.scheduled_interval_change') }}</h3>
                    <p class="text-gray-700 mb-4">
                        {{ __('messages.scheduled_interval_change_description', ['interval' => $scheduledIntervalLabel, 'date' => $changeDate]) }}
                    </p>
                    <form method="POST" action="{{ route('billing.cancel-scheduled-change') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-sm px-4 py-2 bg-white border-2 border-blue-600 text-blue-700 font-semibold rounded-lg hover:bg-blue-50 transition">
                            {{ __('messages.cancel_scheduled_change') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Change Subscription Interval --}}
    @if($subscription && !$subscription->cancel_at_period_end && in_array($subscription->status, ['active', 'trialing']) && !$scheduledInterval)
    @php
        $currentPrice = $subscription->items->data[0]->price;
        $currentInterval = $currentPrice->recurring->interval ?? 'year';
        $currency = $user->stripe_currency ?? \App\Helpers\PricingHelper::getCurrencyCode($user->locale);
        
        // Determine alternative interval
        $alternativeInterval = $currentInterval === 'month' ? 'yearly' : 'monthly';
        $alternativeIntervalLabel = $alternativeInterval === 'monthly' ? __('messages.monthly_plan') : __('messages.yearly_plan');
        
        // Get pricing info for both intervals
        $currentCurrencyInfo = \App\Helpers\PricingHelper::getCurrencyInfo($currency, $currentInterval === 'month' ? 'monthly' : 'yearly');
        $alternativeCurrencyInfo = \App\Helpers\PricingHelper::getCurrencyInfo($currency, $alternativeInterval);
        
        // Calculate savings if switching to yearly
        $monthlyTotal = $currentCurrencyInfo['amount'] * 12;
        $yearlySavings = $alternativeInterval === 'yearly' && $monthlyTotal > 0 
            ? round((($monthlyTotal - $alternativeCurrencyInfo['amount']) / $monthlyTotal) * 100, 0) 
            : 0;
    @endphp
    
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.change_subscription_interval') }}</h2>
            </div>
        </div>
        
        <div class="p-6 lg:p-8">
            <div class="mb-4">
                <p class="text-sm text-gray-600 font-medium mb-2">{{ __('messages.current_interval') }}</p>
                <p class="text-lg font-bold text-gray-900">
                    {{ $currentInterval === 'month' ? __('messages.monthly_plan') : __('messages.yearly_plan') }}
                    ({{ number_format($currentPrice->unit_amount / 100, 2) }} {{ strtoupper($currentPrice->currency) }} / {{ $currentInterval === 'month' ? __('messages.per_month') : __('messages.per_year') }})
                </p>
            </div>
            
            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl p-6 border-2 border-indigo-200">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $alternativeIntervalLabel }}</h3>
                        <div class="flex items-baseline space-x-2 mb-2">
                            <span class="text-3xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                                {{ $alternativeCurrencyInfo['symbol'] }}{{ number_format($alternativeCurrencyInfo['amount'], 0, '.', ',') }}
                            </span>
                            <span class="text-gray-600">
                                / {{ $alternativeInterval === 'monthly' ? __('messages.per_month') : __('messages.per_year') }}
                            </span>
                        </div>
                        @if($yearlySavings > 0)
                        <div class="inline-flex items-center px-3 py-1 bg-green-100 border border-green-300 rounded-full mt-2">
                            <span class="text-xs font-bold text-green-700">💰 {{ __('messages.yearly_savings_badge', ['percent' => $yearlySavings]) }}</span>
                        </div>
                        @elseif($alternativeInterval === 'monthly')
                        <p class="text-sm text-gray-600 mt-2">{{ __('messages.monthly_flexibility') }}</p>
                        @endif
                    </div>
                    
                    <div class="flex-shrink-0">
                        <form method="POST" action="{{ route('billing.change-interval') }}">
                            @csrf
                            <input type="hidden" name="interval" value="{{ $alternativeInterval }}">
                            <button type="submit" class="w-full md:w-auto px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold rounded-xl hover:opacity-90 transition shadow-lg">
                                {{ $alternativeInterval === 'monthly' ? __('messages.switch_to_monthly') : __('messages.switch_to_yearly') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm text-blue-800">{{ __('messages.interval_change_notice_end_of_period') }}</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Payment Method --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100 px-6 py-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ __('messages.payment_method') }}</h2>
                </div>
                @if($subscription && !$subscription->cancel_at_period_end && $paymentMethod)
                <form method="POST" action="{{ route('billing.update-payment-method') }}">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        {{ __('messages.update_card') }}
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="p-6 lg:p-8">

        @if($paymentMethod)
        <div class="flex items-center">
            {{-- Card Icon --}}
            <div class="w-16 h-10 bg-gradient-to-br from-gray-700 to-gray-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-gray-900 font-bold">{{ ucfirst($paymentMethod->card->brand) }} •••• {{ $paymentMethod->card->last4 }}</p>
                <p class="text-sm text-gray-600 font-medium">{{ __('messages.expires') }} {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}</p>
            </div>
        </div>
            @else
            <p class="text-gray-600">{{ __('messages.no_payment_method') }}</p>
            @if($subscription && $subscription->cancel_at_period_end)
            <p class="text-sm text-gray-500 mt-2">{{ __('messages.payment_method_removed_after_cancellation') }}</p>
            @endif
            @endif
        </div>
    </div>

    {{-- Invoices --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-indigo-100 px-6 py-5">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center shadow-md">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900">{{ __('messages.invoices') }}</h2>
            </div>
        </div>

        <div class="p-6 lg:p-8">

        @php
            // Check if we have Fakturoid invoices (new system)
            $hasFakturoidInvoices = isset($fakturoidInvoices) && $fakturoidInvoices->isNotEmpty();
            
            // Filter out Stripe invoices with 0 amount (trial invoices) - fallback for old invoices
            $paidInvoices = $invoices && $invoices->data 
                ? collect($invoices->data)->filter(function($invoice) {
                    return $invoice->amount_paid > 0;
                  })
                : collect([]);
        @endphp

        @if($hasFakturoidInvoices)
        {{-- Show Fakturoid invoices (new system) --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.invoice_number') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.date') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.amount') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">{{ __('messages.download') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($fakturoidInvoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm font-mono text-gray-900">
                            {{ $invoice->fakturoid_number ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ $invoice->issued_at ? $invoice->issued_at->translatedFormat('j. M Y') : '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ number_format($invoice->amount, 2) }} {{ strtoupper($invoice->currency) }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ __('messages.paid') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            <a href="{{ route('billing.invoice-pdf', $invoice->id) }}" class="text-blue-600 hover:text-blue-800 font-medium inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ __('messages.download_pdf') }}
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @elseif($paidInvoices->isNotEmpty())
        {{-- Show Stripe invoices (old system fallback) --}}
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.date') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.amount') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">{{ __('messages.status') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-600 uppercase">{{ __('messages.download') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($paidInvoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ \Carbon\Carbon::createFromTimestamp($invoice->created)->translatedFormat('j. M Y') }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                            {{ number_format($invoice->amount_paid / 100, 2) }} {{ strtoupper($invoice->currency) }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($invoice->status === 'paid')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ __('messages.paid') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-right">
                            @if($invoice->invoice_pdf)
                            <a href="{{ $invoice->invoice_pdf }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ __('messages.download_pdf') }}
                            </a>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @else
        <p class="text-gray-600">{{ __('messages.no_invoices_yet') }}</p>
        @endif
        </div>
    </div>

    {{-- Cancel Subscription --}}
    @if($subscription && !$subscription->cancel_at_period_end)
    <div class="bg-white rounded-2xl shadow-xl border-2 border-red-200 mt-8">
        <div class="p-6 lg:p-8">
            <h2 class="flex items-center space-x-2 text-lg font-bold text-gray-700 mb-3">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>{{ __('messages.cancel_subscription') }}</span>
            </h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('messages.cancel_subscription_description') }}</p>
            
            <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('{{ __('messages.cancel_confirmation') }}')" class="inline">
                @csrf
                <button 
                    type="submit" 
                    class="text-sm text-red-600 hover:text-red-700 font-bold underline transition"
                >
                    {{ __('messages.cancel_subscription') }}
                </button>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

