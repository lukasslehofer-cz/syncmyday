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
        <a href="{{ route('billing') }}" class="text-blue-600 hover:text-blue-800 inline-flex items-center mb-4">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            {{ __('messages.back_to_billing') }}
        </a>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('messages.manage_subscription') }}</h1>
    </div>

    {{-- Subscription Status --}}
    @if($subscription)
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.subscription_status') }}</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">{{ __('messages.plan') }}</p>
                <p class="text-lg font-semibold text-gray-900">Pro</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ __('messages.status') }}</p>
                <p class="text-lg font-semibold">
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
                <p class="text-sm text-gray-600">
                    @if($subscription->cancel_at_period_end)
                        {{ __('messages.ends_on') }}
                    @else
                        {{ __('messages.renews_on') }}
                    @endif
                </p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->format('j. F Y') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ __('messages.price') }}</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ number_format($subscription->items->data[0]->price->unit_amount / 100, 2) }}
                    {{ strtoupper($subscription->items->data[0]->price->currency) }} / {{ __('messages.year') }}
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
                    <p class="text-orange-800 text-sm mt-1">{{ __('messages.subscription_end_date_notice', ['date' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)->format('j. F Y')]) }}</p>
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
    @endif

    {{-- Payment Method --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">{{ __('messages.payment_method') }}</h2>
            <form method="POST" action="{{ route('billing.update-payment-method') }}">
                @csrf
                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    {{ __('messages.update_card') }}
                </button>
            </form>
        </div>

        @if($paymentMethod)
        <div class="flex items-center">
            {{-- Card Icon --}}
            <div class="w-16 h-10 bg-gradient-to-br from-gray-700 to-gray-900 rounded-lg flex items-center justify-center mr-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-gray-900 font-medium">{{ ucfirst($paymentMethod->card->brand) }} •••• {{ $paymentMethod->card->last4 }}</p>
                <p class="text-sm text-gray-600">{{ __('messages.expires') }} {{ $paymentMethod->card->exp_month }}/{{ $paymentMethod->card->exp_year }}</p>
            </div>
        </div>
        @else
        <p class="text-gray-600">{{ __('messages.no_payment_method') }}</p>
        @endif
    </div>

    {{-- Invoices --}}
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ __('messages.invoices') }}</h2>

        @php
            // Filter out invoices with 0 amount (trial invoices)
            $paidInvoices = $invoices && $invoices->data 
                ? collect($invoices->data)->filter(function($invoice) {
                    return $invoice->amount_paid > 0;
                  })
                : collect([]);
        @endphp

        @if($paidInvoices->isNotEmpty())
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
                            {{ \Carbon\Carbon::createFromTimestamp($invoice->created)->format('j. M Y') }}
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

    {{-- Cancel Subscription --}}
    @if($subscription && !$subscription->cancel_at_period_end)
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-red-200">
        <h2 class="text-xl font-semibold text-red-900 mb-2">{{ __('messages.cancel_subscription') }}</h2>
        <p class="text-gray-600 mb-4">{{ __('messages.cancel_subscription_description') }}</p>
        
        <form method="POST" action="{{ route('billing.cancel') }}" onsubmit="return confirm('{{ __('messages.cancel_confirmation') }}')">
            @csrf
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-medium transition">
                {{ __('messages.cancel_subscription') }}
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

