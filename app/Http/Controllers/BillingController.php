<?php

namespace App\Http\Controllers;

use App\Helpers\PricingHelper;
use App\Models\FakturoidInvoice;
use App\Services\FakturoidService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Webhook;

class BillingController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * Show billing page
     */
    public function index()
    {
        $user = auth()->user();

        // Get subscription details from Stripe if user has one
        $subscription = null;
        if ($user->stripe_subscription_id) {
            try {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
            } catch (\Exception $e) {
                Log::warning('Failed to retrieve subscription for billing page', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Determine currency to use for pricing display
        // Priority: 1) Saved Stripe currency, 2) Current locale currency
        $effectiveCurrency = $user->stripe_currency ?? PricingHelper::getCurrencyCode($user->locale);

        // Get currency info for both intervals
        $monthlyCurrency = PricingHelper::getCurrencyInfo($effectiveCurrency, 'monthly');
        $yearlyCurrency = PricingHelper::getCurrencyInfo($effectiveCurrency, 'yearly');

        // Format prices
        $monthlyPrice = $monthlyCurrency['symbol'].number_format($monthlyCurrency['amount'], 0, '.', ',');
        $yearlyPrice = $yearlyCurrency['symbol'].number_format($yearlyCurrency['amount'], 0, '.', ',');

        // Calculate savings
        $monthlyTotal = $monthlyCurrency['amount'] * 12;
        $yearlySavings = $monthlyTotal > 0 ? round((($monthlyTotal - $yearlyCurrency['amount']) / $monthlyTotal) * 100, 0) : 0;

        return view('billing.index', [
            'user' => $user,
            'subscription' => $subscription,
            'monthlyPrice' => $monthlyPrice,
            'yearlyPrice' => $yearlyPrice,
            'yearlySavings' => $yearlySavings,
            'trialDaysRemaining' => $user->getRemainingTrialDays(),
            'effectiveCurrency' => $effectiveCurrency,
        ]);
    }

    /**
     * Create Stripe Checkout session for subscription (monthly or yearly)
     */
    public function createCheckoutSession(Request $request)
    {
        $user = auth()->user();
        $interval = $request->input('interval', 'yearly'); // monthly or yearly

        // Validate interval
        if (! in_array($interval, ['monthly', 'yearly'])) {
            return redirect()->back()
                ->with('error', 'Invalid subscription interval.');
        }

        try {
            // Determine currency to use
            // If user doesn't have Stripe customer yet, use current locale currency
            // If user has Stripe customer, use saved currency (prevents currency conflicts)
            $currency = $user->stripe_currency ?? PricingHelper::getCurrencyCode($user->locale);

            // Create or retrieve Stripe customer
            if (! $user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);

                // Save customer ID and currency
                $user->update([
                    'stripe_customer_id' => $customer->id,
                    'stripe_currency' => $currency,
                ]);

                Log::info('Stripe customer created with currency', [
                    'user_id' => $user->id,
                    'currency' => $currency,
                ]);
            }

            // Get correct Price ID based on saved currency and interval
            $priceId = PricingHelper::getPriceIdByCurrency($currency, $interval);

            if (! $priceId) {
                Log::error('Price ID not found', [
                    'currency' => $currency,
                    'interval' => $interval,
                ]);

                return redirect()->back()
                    ->with('error', 'Pricing configuration error. Please contact support.');
            }

            Log::info('Using Stripe price', [
                'user_id' => $user->id,
                'currency' => $currency,
                'interval' => $interval,
                'price_id' => $priceId,
            ]);

            // Build Checkout Session configuration
            $sessionConfig = [
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('billing.success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing'),
                'metadata' => [
                    'user_id' => $user->id,
                    'interval' => $interval,
                ],
                'subscription_data' => [
                    'description' => 'SyncMyDay Pro Subscription - '.ucfirst($interval),
                    'metadata' => [
                        'user_id' => $user->id,
                        'locale' => $user->locale,
                        'interval' => $interval,
                    ],
                ],
            ];

            // If user is in trial, defer billing until their current trial ends
            // This ensures they get the full trial period before being charged
            // Using billing_cycle_anchor instead of trial_end to avoid Stripe's 2-day minimum restriction
            if ($user->isInTrial() && $user->subscription_ends_at) {
                $sessionConfig['subscription_data']['billing_cycle_anchor'] = $user->subscription_ends_at->timestamp;
                $sessionConfig['subscription_data']['proration_behavior'] = 'none'; // Prevent prorated charges
                $sessionConfig['subscription_data']['metadata']['had_trial'] = 'true';

                Log::info('Checkout session with deferred billing', [
                    'user_id' => $user->id,
                    'billing_starts_at' => $user->subscription_ends_at->toDateTimeString(),
                ]);
            }

            // Create Checkout Session
            $session = Session::create($sessionConfig);

            Log::info('Checkout session created', [
                'user_id' => $user->id,
                'interval' => $interval,
                'price_id' => $priceId,
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'interval' => $interval,
            ]);

            return redirect()->back()
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Handle successful payment
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return redirect()->route('billing');
        }

        try {
            $session = Session::retrieve($sessionId);

            // Update user subscription
            $user = auth()->user();

            // Store subscription ID
            if ($session->subscription) {
                $user->update([
                    'stripe_subscription_id' => $session->subscription,
                ]);

                // Retrieve the subscription to get period end
                $subscription = \Stripe\Subscription::retrieve($session->subscription);

                // Use trial_end for trialing subscriptions, otherwise current_period_end
                // Protect against null/0 timestamps that would create 1970-01-01 (invalid for MySQL)
                $timestamp = $subscription->status === 'trialing' && $subscription->trial_end
                    ? $subscription->trial_end
                    : $this->getSubscriptionPeriodEnd($subscription);

                // If timestamp is invalid (null or 0), fall back to interval-aware default
                $endsAt = $timestamp
                    ? \Carbon\Carbon::createFromTimestamp($timestamp)
                    : $this->fallbackPeriodEnd($subscription);

                $user->update([
                    'subscription_tier' => 'pro',
                    'subscription_ends_at' => $endsAt,
                ]);

                Log::info('User subscribed to Pro', [
                    'user_id' => $user->id,
                    'is_trial' => $subscription->status === 'trialing',
                    'subscription_id' => $session->subscription,
                    'period_end_timestamp' => $timestamp,
                    'subscription_ends_at' => $endsAt->toDateTimeString(),
                ]);

                // Track purchase conversion for GTM/Analytics
                // Get price info from the subscription
                $interval = 'yearly';
                $amount = 0;
                $currency = 'EUR';

                if (isset($subscription->items->data[0]->price)) {
                    $price = $subscription->items->data[0]->price;
                    $amount = ($price->unit_amount ?? 0) / 100;
                    $currency = strtoupper($price->currency ?? 'eur');
                    $interval = ($price->recurring->interval ?? 'year') === 'month' ? 'monthly' : 'yearly';
                }

                $metaEventId = app(\App\Services\MetaConversionsApiService::class)->sendEvent('Purchase', $request, $user, [
                    'value' => $amount,
                    'currency' => $currency,
                ]);
                session()->flash('track_purchase', [
                    'transaction_id' => $session->subscription,
                    'value' => $amount,
                    'currency' => $currency,
                    'interval' => $interval,
                    'meta_event_id' => $metaEventId,
                ]);
            }

            // Check redirect parameter
            $redirect = $request->query('redirect');

            if ($redirect === 'onboarding') {
                return redirect()->route('onboarding.start')
                    ->with('success', __('messages.registration_success'));
            }

            // Default to dashboard (also handles redirect=dashboard explicitly)
            return redirect()->route('dashboard')
                ->with('success', __('messages.subscription_activated'));

        } catch (\Exception $e) {
            Log::error('Payment verification failed', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId,
            ]);

            return redirect()->route('billing')
                ->with('error', __('messages.payment_verification_failed'));
        }
    }

    /**
     * Handle Stripe webhooks
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);

            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'customer.subscription.updated':
            case 'customer.subscription.created':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;

            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response('OK', 200);
    }

    /**
     * Handle checkout session completed
     */
    private function handleCheckoutSessionCompleted($session)
    {
        $userId = $session->metadata->user_id ?? null;

        if (! $userId) {
            Log::warning('No user_id in checkout session metadata');

            return;
        }

        $user = \App\Models\User::find($userId);

        if (! $user) {
            Log::warning('User not found for checkout session', ['user_id' => $userId]);

            return;
        }

        // Update subscription ID
        if ($session->subscription) {
            $user->update([
                'stripe_subscription_id' => $session->subscription,
            ]);

            Log::info('Checkout completed, subscription linked', [
                'user_id' => $user->id,
                'subscription_id' => $session->subscription,
            ]);
        }
    }

    /**
     * Read current_period_end from a Stripe Subscription.
     * In API 2025-04-30.basil this moved from Subscription to SubscriptionItem.
     * Falls back to legacy top-level field for older API versions.
     */
    private function getSubscriptionPeriodEnd($subscription): ?int
    {
        $itemEnd = $subscription->items->data[0]->current_period_end ?? null;
        if ($itemEnd && $itemEnd > 0) {
            return $itemEnd;
        }
        $legacy = $subscription->current_period_end ?? null;

        return $legacy && $legacy > 0 ? $legacy : null;
    }

    /**
     * Read current_period_start from a Stripe Subscription.
     * Same migration story as getSubscriptionPeriodEnd().
     */
    private function getSubscriptionPeriodStart($subscription): ?int
    {
        $itemStart = $subscription->items->data[0]->current_period_start ?? null;
        if ($itemStart && $itemStart > 0) {
            return $itemStart;
        }
        $legacy = $subscription->current_period_start ?? null;

        return $legacy && $legacy > 0 ? $legacy : null;
    }

    /**
     * Last-resort fallback for subscription end date when Stripe returns no period_end.
     * Uses the price recurring interval so a yearly plan does not get clamped to 30 days.
     */
    private function fallbackPeriodEnd($subscription): \Carbon\Carbon
    {
        $interval = $subscription->items->data[0]->price->recurring->interval ?? 'month';

        return $interval === 'year' ? now()->addYear() : now()->addMonth();
    }

    /**
     * Handle subscription updated/created
     */
    private function handleSubscriptionUpdated($subscription)
    {
        $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();

        if (! $user) {
            // Try to find by customer ID
            $user = \App\Models\User::where('stripe_customer_id', $subscription->customer)->first();

            if (! $user) {
                Log::warning('User not found for subscription', ['subscription_id' => $subscription->id]);

                return;
            }

            // Link the subscription
            $user->update(['stripe_subscription_id' => $subscription->id]);
        }

        // Update subscription status
        $isActive = in_array($subscription->status, ['active', 'trialing']);

        // Use trial_end for trialing subscriptions, otherwise current_period_end
        // Protect against null/0 timestamps that would create 1970-01-01 (invalid for MySQL)
        $timestamp = $subscription->status === 'trialing' && $subscription->trial_end
            ? $subscription->trial_end
            : $this->getSubscriptionPeriodEnd($subscription);

        // If timestamp is invalid (null or 0), fall back to interval-aware default
        $endsAt = $timestamp
            ? \Carbon\Carbon::createFromTimestamp($timestamp)
            : $this->fallbackPeriodEnd($subscription);

        $user->update([
            // Always keep 'pro' tier - soft-lock is determined by hasActiveSubscription()
            'subscription_tier' => 'pro',
            'subscription_ends_at' => $endsAt,
            // Clear grace period if subscription is active again
            'grace_period_ends_at' => $isActive ? null : $user->grace_period_ends_at,
        ]);

        Log::info('Subscription updated', [
            'user_id' => $user->id,
            'status' => $subscription->status,
            'is_active' => $isActive,
            'period_end_timestamp' => $timestamp,
            'subscription_ends_at' => $endsAt->toDateTimeString(),
        ]);
    }

    /**
     * Handle payment succeeded
     */
    private function handlePaymentSucceeded($invoice)
    {
        $customerId = $invoice->customer;
        $user = \App\Models\User::where('stripe_customer_id', $customerId)->first();

        if (! $user) {
            return;
        }

        $amount = $invoice->amount_paid / 100;
        $currency = strtoupper($invoice->currency);

        // Ignore zero-amount invoices (trial periods, prorations)
        if ($amount <= 0) {
            Log::info('Ignoring zero-amount invoice', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
            ]);

            return;
        }

        // Idempotency: skip if we already processed this Stripe invoice
        if (FakturoidInvoice::where('stripe_invoice_id', $invoice->id)->exists()) {
            Log::info('Stripe invoice already processed, skipping', [
                'user_id' => $user->id,
                'stripe_invoice_id' => $invoice->id,
            ]);

            return;
        }

        // Get subscription to determine next billing date and interval
        $nextBillingDate = null;
        $interval = 'yearly'; // default
        if ($user->stripe_subscription_id) {
            try {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                $periodEnd = $this->getSubscriptionPeriodEnd($subscription);
                if ($periodEnd) {
                    $nextBillingDate = \Carbon\Carbon::createFromTimestamp($periodEnd)
                        ->format('d.m.Y');
                }
                // Get interval from subscription metadata or price
                if (isset($subscription->metadata->interval)) {
                    $interval = $subscription->metadata->interval;
                } elseif (isset($subscription->items->data[0]->price->recurring->interval)) {
                    $interval = $subscription->items->data[0]->price->recurring->interval === 'month' ? 'monthly' : 'yearly';
                }
            } catch (\Exception $e) {
                Log::warning('Could not retrieve subscription for payment success email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Payment succeeded', [
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'interval' => $interval,
            'next_billing_date' => $nextBillingDate,
        ]);

        // Create Fakturoid invoice
        $this->createFakturoidInvoice($user, $amount, $currency, $interval, $invoice->id);

        // Send payment success email
        try {
            \Mail::to($user->email)->send(new \App\Mail\PaymentSuccessMail($user, $amount, $nextBillingDate));

            Log::info('Payment success email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment success email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Create Fakturoid invoice for payment
     */
    private function createFakturoidInvoice(
        \App\Models\User $user,
        float $amount,
        string $currency,
        string $interval,
        string $stripeInvoiceId
    ): void {
        try {
            // Build description based on interval
            $intervalLabel = $interval === 'monthly' ? 'Monthly' : 'Yearly';
            $description = "SyncMyDay Pro - {$intervalLabel} Subscription";

            // Translate description to user's language
            $descriptionKey = $interval === 'monthly' ? 'subscription_monthly' : 'subscription_yearly';
            if (__("messages.{$descriptionKey}", [], $user->locale) !== "messages.{$descriptionKey}") {
                $description = __("messages.{$descriptionKey}", [], $user->locale);
            }

            // Create pending invoice record first
            $fakturoidInvoice = FakturoidInvoice::create([
                'user_id' => $user->id,
                'stripe_invoice_id' => $stripeInvoiceId,
                'amount' => $amount,
                'currency' => $currency,
                'language' => $user->locale,
                'description' => $description,
                'status' => 'pending',
            ]);

            // Try to create invoice in Fakturoid
            $fakturoidService = new FakturoidService;
            $invoiceData = $fakturoidService->buildInvoiceData(
                $user,
                $amount,
                $currency,
                $description,
                $stripeInvoiceId
            );

            $createdInvoice = $fakturoidService->createInvoice($invoiceData);

            if ($createdInvoice) {
                // Update with Fakturoid data
                $fakturoidInvoice->update([
                    'fakturoid_id' => $createdInvoice['id'],
                    'fakturoid_number' => $createdInvoice['number'] ?? null,
                    'issued_at' => isset($createdInvoice['issued_on'])
                        ? \Carbon\Carbon::parse($createdInvoice['issued_on'])
                        : now(),
                    'status' => 'created',
                    'error_message' => null,
                ]);

                // Mark invoice as paid in Fakturoid
                $payment = $fakturoidService->createPayment($createdInvoice['id']);

                if ($payment) {
                    $fakturoidInvoice->update(['status' => 'paid']);

                    Log::info('Fakturoid invoice marked as paid', [
                        'user_id' => $user->id,
                        'fakturoid_id' => $createdInvoice['id'],
                        'payment_id' => $payment['id'] ?? null,
                    ]);
                } else {
                    Log::warning('Failed to mark Fakturoid invoice as paid', [
                        'user_id' => $user->id,
                        'fakturoid_id' => $createdInvoice['id'],
                    ]);
                }

                // Download and store PDF locally (after payment so PDF shows paid status)
                $this->downloadAndStoreInvoicePdf($fakturoidInvoice, $fakturoidService);

                Log::info('Fakturoid invoice created successfully', [
                    'user_id' => $user->id,
                    'fakturoid_id' => $createdInvoice['id'],
                    'fakturoid_number' => $createdInvoice['number'] ?? null,
                ]);
            } else {
                // Mark as failed
                $fakturoidInvoice->update([
                    'status' => 'failed',
                    'error_message' => 'Failed to create invoice in Fakturoid API',
                    'retry_count' => 1,
                ]);

                Log::error('Failed to create Fakturoid invoice', [
                    'user_id' => $user->id,
                    'local_invoice_id' => $fakturoidInvoice->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Exception while creating Fakturoid invoice', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // If invoice record was created, mark as failed
            if (isset($fakturoidInvoice)) {
                $fakturoidInvoice->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'retry_count' => 1,
                ]);
            }
        }
    }

    /**
     * Download PDF from Fakturoid and store it locally
     *
     * @param  FakturoidInvoice  $invoice  Invoice model
     * @param  FakturoidService  $fakturoidService  Fakturoid service instance
     * @return bool True if successful, false otherwise
     */
    private function downloadAndStoreInvoicePdf(FakturoidInvoice $invoice, FakturoidService $fakturoidService): bool
    {
        if (! $invoice->fakturoid_id) {
            Log::warning('Cannot download PDF: Missing fakturoid_id', [
                'invoice_id' => $invoice->id,
            ]);

            return false;
        }

        try {
            // Download PDF content from Fakturoid API
            $pdfContent = $fakturoidService->downloadPdfContent($invoice->fakturoid_id);

            if (! $pdfContent) {
                Log::warning('Failed to download PDF content from Fakturoid', [
                    'invoice_id' => $invoice->id,
                    'fakturoid_id' => $invoice->fakturoid_id,
                ]);

                return false;
            }

            // Generate filename
            $filename = $invoice->fakturoid_number
                ? "invoice-{$invoice->fakturoid_number}.pdf"
                : "invoice-{$invoice->id}.pdf";

            // Store PDF in storage/app/invoices/ directory
            $storagePath = "invoices/{$filename}";
            Storage::put($storagePath, $pdfContent);

            // Update invoice with PDF path
            $invoice->update([
                'pdf_url' => $storagePath,
            ]);

            Log::info('Invoice PDF downloaded and stored locally', [
                'invoice_id' => $invoice->id,
                'fakturoid_id' => $invoice->fakturoid_id,
                'storage_path' => $storagePath,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Exception downloading and storing invoice PDF', [
                'invoice_id' => $invoice->id,
                'fakturoid_id' => $invoice->fakturoid_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Handle subscription deleted (cancellation)
     */
    private function handleSubscriptionDeleted($subscription)
    {
        $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();

        if (! $user) {
            return;
        }

        $user->update([
            // Keep 'pro' tier - soft-lock is determined by subscription_ends_at being in the past
            'subscription_tier' => 'pro',
            'subscription_ends_at' => now(),
            'grace_period_ends_at' => null, // Clear grace period
            'stripe_subscription_id' => null, // Clear subscription ID
        ]);

        Log::info('Subscription cancelled - account soft-locked', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Handle payment failed
     */
    private function handlePaymentFailed($invoice)
    {
        $customerId = $invoice->customer;
        $user = \App\Models\User::where('stripe_customer_id', $customerId)->first();

        if (! $user) {
            return;
        }

        $amount = ($invoice->amount_due ?? 0) / 100;
        $currency = strtoupper($invoice->currency ?? 'USD');

        // PROTECTION 1: Ignore $0 invoices (trial periods, prorations)
        if ($amount <= 0) {
            Log::info('Ignoring payment_failed for $0 invoice', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'amount' => $amount,
            ]);

            return;
        }

        // PROTECTION 2: Ignore first payment failures on brand new subscriptions
        // These are often 3D Secure / SCA verifications that succeed moments later
        if ($user->stripe_subscription_id) {
            try {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                $subscriptionAge = now()->timestamp - $subscription->created;

                // If subscription is less than 5 minutes old, it might be 3D Secure verification
                if ($subscriptionAge < 300) { // 5 minutes
                    Log::info('Ignoring payment_failed for new subscription (likely 3D Secure)', [
                        'user_id' => $user->id,
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $user->stripe_subscription_id,
                        'subscription_age_seconds' => $subscriptionAge,
                        'amount' => $amount,
                    ]);

                    return;
                }
            } catch (\Exception $e) {
                Log::warning('Failed to check subscription age', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                // Continue with normal flow if we can't check
            }
        }

        // PROTECTION 3: Check if invoice is already paid
        // (Stripe might send payment_failed before payment_succeeded due to webhook timing)
        if (isset($invoice->status) && $invoice->status === 'paid') {
            Log::info('Ignoring payment_failed for already paid invoice', [
                'user_id' => $user->id,
                'invoice_id' => $invoice->id,
                'invoice_status' => $invoice->status,
            ]);

            return;
        }

        // If we got here, it's a real payment failure
        // Set grace period: 3 days from now to fix payment issue
        $gracePeriodEndsAt = now()->addDays(3);
        $user->update([
            'grace_period_ends_at' => $gracePeriodEndsAt,
        ]);

        Log::warning('Payment failed - grace period activated', [
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
            'amount' => $amount,
            'currency' => $currency,
            'grace_period_ends_at' => $gracePeriodEndsAt->toDateTimeString(),
        ]);

        // Send payment failed email
        try {
            // Create simple text email for payment failure
            \Mail::send('emails.payment-failed', [
                'user' => $user,
                'amount' => $amount,
                'currency' => $currency,
                'invoiceUrl' => $invoice->hosted_invoice_url ?? route('billing'),
                'gracePeriodEndsAt' => $gracePeriodEndsAt,
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject(__('emails.payment_failed_subject'));
            });

            Log::info('Payment failed email sent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send payment failed email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Show subscription management page
     */
    public function manage()
    {
        $user = auth()->user();

        if (! $user->stripe_customer_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            // Get subscription details from Stripe
            $subscription = null;
            $paymentMethod = null;
            $invoices = [];
            $schedule = null;
            $scheduledInterval = null;
            $scheduleChangeDate = null;

            if ($user->stripe_subscription_id) {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

                // Get payment method
                if ($subscription->default_payment_method) {
                    $paymentMethod = \Stripe\PaymentMethod::retrieve($subscription->default_payment_method);
                }

                // Get recent invoices from Stripe (fallback for old invoices before Fakturoid)
                $invoices = \Stripe\Invoice::all([
                    'customer' => $user->stripe_customer_id,
                    'limit' => 10,
                ]);

                // Check if there's a scheduled interval change
                if ($subscription->schedule) {
                    try {
                        $schedule = \Stripe\SubscriptionSchedule::retrieve($subscription->schedule);

                        // If schedule has multiple phases, there's a pending change
                        if (count($schedule->phases) > 1) {
                            $currentPhase = $schedule->phases[0];
                            $nextPhase = $schedule->phases[1];

                            // Get the new interval from next phase
                            $nextPriceId = $nextPhase->items[0]->price ?? null;
                            if ($nextPriceId) {
                                $nextPrice = \Stripe\Price::retrieve($nextPriceId);
                                $scheduledInterval = $nextPrice->recurring->interval ?? null;
                                $scheduleChangeDate = $currentPhase->end_date;
                            }
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not retrieve subscription schedule', [
                            'schedule_id' => $subscription->schedule,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Get Fakturoid invoices (newest first)
            $fakturoidInvoices = $user->fakturoidInvoices()
                ->whereIn('status', ['created', 'paid'])
                ->orderBy('issued_at', 'desc')
                ->get();

            return view('billing.manage', [
                'user' => $user,
                'subscription' => $subscription,
                'paymentMethod' => $paymentMethod,
                'invoices' => $invoices,
                'fakturoidInvoices' => $fakturoidInvoices,
                'schedule' => $schedule,
                'scheduledInterval' => $scheduledInterval,
                'scheduleChangeDate' => $scheduleChangeDate,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load subscription management', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->route('billing')
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Download Fakturoid invoice PDF
     */
    public function downloadInvoicePdf(FakturoidInvoice $invoice)
    {
        // Check authorization
        if ($invoice->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if invoice was created successfully
        if (! $invoice->isCreated()) {
            return redirect()->route('billing.manage')
                ->with('error', __('messages.invoice_not_available'));
        }

        try {
            // Generate filename
            $filename = $invoice->fakturoid_number
                ? "invoice-{$invoice->fakturoid_number}.pdf"
                : "invoice-{$invoice->id}.pdf";

            // Step 1: Try to load PDF from local storage (fast path)
            if ($invoice->pdf_url && Storage::exists($invoice->pdf_url)) {
                $pdfContent = Storage::get($invoice->pdf_url);

                Log::info('Invoice PDF served from local storage', [
                    'invoice_id' => $invoice->id,
                    'storage_path' => $invoice->pdf_url,
                ]);

                return response($pdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
            }

            // Step 2: Fallback - download from Fakturoid API
            $fakturoidService = new FakturoidService;
            $pdfContent = $fakturoidService->downloadPdfContent($invoice->fakturoid_id);

            if ($pdfContent) {
                // Store PDF locally for future requests
                $storagePath = "invoices/{$filename}";
                Storage::put($storagePath, $pdfContent);

                // Update invoice with PDF path
                $invoice->update([
                    'pdf_url' => $storagePath,
                ]);

                Log::info('Invoice PDF downloaded from Fakturoid API and cached locally', [
                    'invoice_id' => $invoice->id,
                    'fakturoid_id' => $invoice->fakturoid_id,
                    'storage_path' => $storagePath,
                ]);

                return response($pdfContent, 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
            }

            // Step 3: All methods failed
            Log::error('Failed to download Fakturoid PDF (both local and API failed)', [
                'invoice_id' => $invoice->id,
                'fakturoid_id' => $invoice->fakturoid_id,
                'pdf_url' => $invoice->pdf_url,
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.invoice_download_failed'));

        } catch (\Exception $e) {
            Log::error('Exception downloading invoice PDF', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.invoice_download_failed'));
        }
    }

    /**
     * Create Checkout Session for updating payment method
     */
    public function updatePaymentMethod()
    {
        $user = auth()->user();

        if (! $user->stripe_customer_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            $session = Session::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'mode' => 'setup',
                'success_url' => route('billing.payment-method-updated'),
                'cancel_url' => route('billing.manage'),
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Failed to create payment method update session', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Handle successful payment method update redirect from Stripe
     */
    public function paymentMethodUpdated()
    {
        return redirect()->route('billing.manage')
            ->with('success', __('messages.payment_method_updated'));
    }

    /**
     * Cancel subscription
     */
    public function cancelSubscription()
    {
        $user = auth()->user();

        if (! $user->stripe_subscription_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            // Get subscription to find payment method
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

            // Cancel subscription at period end
            $subscription = \Stripe\Subscription::update(
                $user->stripe_subscription_id,
                ['cancel_at_period_end' => true]
            );

            // Detach payment method (card will be removed)
            if ($subscription->default_payment_method) {
                try {
                    $paymentMethod = \Stripe\PaymentMethod::retrieve($subscription->default_payment_method);
                    $paymentMethod->detach();
                    Log::info('Payment method detached after subscription cancellation', [
                        'user_id' => $user->id,
                        'payment_method_id' => $subscription->default_payment_method,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to detach payment method', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                }
            }

            Log::info('Subscription cancelled by user', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
                'ends_at' => $this->getSubscriptionPeriodEnd($subscription),
            ]);

            return redirect()->route('billing.manage')
                ->with('success', __('messages.subscription_cancelled'));

        } catch (\Exception $e) {
            Log::error('Failed to cancel subscription', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Reactivate cancelled subscription
     */
    public function reactivateSubscription()
    {
        $user = auth()->user();

        if (! $user->stripe_subscription_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            // Get subscription to check payment method
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

            // If payment method was removed, redirect to add new one
            if (! $subscription->default_payment_method) {
                Log::info('Subscription reactivation requires new payment method', [
                    'user_id' => $user->id,
                    'subscription_id' => $user->stripe_subscription_id,
                ]);

                // Create Checkout Session to add payment method (setup mode)
                $session = Session::create([
                    'customer' => $user->stripe_customer_id,
                    'payment_method_types' => ['card'],
                    'mode' => 'setup',
                    'success_url' => route('billing.reactivate-with-payment').'?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('billing.manage'),
                ]);

                return redirect($session->url);
            }

            // Remove cancellation
            \Stripe\Subscription::update(
                $user->stripe_subscription_id,
                ['cancel_at_period_end' => false]
            );

            Log::info('Subscription reactivated by user', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
            ]);

            return redirect()->route('billing.manage')
                ->with('success', __('messages.subscription_reactivated'));

        } catch (\Exception $e) {
            Log::error('Failed to reactivate subscription', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Complete reactivation after payment method setup
     */
    public function reactivateWithPayment(Request $request)
    {
        $user = auth()->user();
        $sessionId = $request->query('session_id');

        if (! $sessionId || ! $user->stripe_subscription_id) {
            return redirect()->route('billing.manage')
                ->with('error', __('messages.billing_error'));
        }

        try {
            // Get the setup session
            $session = Session::retrieve($sessionId);

            // Attach payment method to subscription
            if ($session->setup_intent) {
                $setupIntent = \Stripe\SetupIntent::retrieve($session->setup_intent);

                if ($setupIntent->payment_method) {
                    \Stripe\Subscription::update(
                        $user->stripe_subscription_id,
                        [
                            'default_payment_method' => $setupIntent->payment_method,
                            'cancel_at_period_end' => false,
                        ]
                    );

                    Log::info('Subscription reactivated with new payment method', [
                        'user_id' => $user->id,
                        'subscription_id' => $user->stripe_subscription_id,
                        'payment_method' => $setupIntent->payment_method,
                    ]);

                    return redirect()->route('billing.manage')
                        ->with('success', __('messages.subscription_reactivated'));
                }
            }

            return redirect()->route('billing.manage')
                ->with('error', __('messages.billing_error'));

        } catch (\Exception $e) {
            Log::error('Failed to complete subscription reactivation', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Change subscription interval (monthly ↔ yearly) at end of period
     */
    public function changeSubscriptionInterval(Request $request)
    {
        $user = auth()->user();

        if (! $user->stripe_subscription_id) {
            return redirect()->route('billing.manage')
                ->with('error', __('messages.no_subscription'));
        }

        $newInterval = $request->input('interval');

        // Validate interval
        if (! in_array($newInterval, ['monthly', 'yearly'])) {
            return redirect()->route('billing.manage')
                ->with('error', __('messages.invalid_interval'));
        }

        try {
            // Get current subscription
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

            // Check if subscription is active
            if (! in_array($subscription->status, ['active', 'trialing'])) {
                return redirect()->route('billing.manage')
                    ->with('error', __('messages.subscription_not_active'));
            }

            // Get current interval from subscription
            $currentPrice = $subscription->items->data[0]->price;
            $currentInterval = $currentPrice->recurring->interval ?? null;

            if (! $currentInterval) {
                Log::error('Could not determine current subscription interval', [
                    'user_id' => $user->id,
                    'subscription_id' => $user->stripe_subscription_id,
                ]);

                return redirect()->route('billing.manage')
                    ->with('error', __('messages.billing_error'));
            }

            // Check if interval is actually changing
            if ($currentInterval === $newInterval) {
                return redirect()->route('billing.manage')
                    ->with('error', __('messages.interval_already_set', ['interval' => $newInterval]));
            }

            // Get currency (locked at customer creation)
            $currency = $user->stripe_currency ?? PricingHelper::getCurrencyCode($user->locale);

            // Get new Price ID
            $newPriceId = PricingHelper::getPriceIdByCurrency($currency, $newInterval);

            if (! $newPriceId) {
                Log::error('Price ID not found for interval change', [
                    'user_id' => $user->id,
                    'currency' => $currency,
                    'interval' => $newInterval,
                ]);

                return redirect()->route('billing.manage')
                    ->with('error', __('messages.pricing_configuration_error'));
            }

            // Check if there's already a schedule
            if ($subscription->schedule) {
                try {
                    $existingSchedule = \Stripe\SubscriptionSchedule::retrieve($subscription->schedule);
                    // Release existing schedule first (instance method, not static)
                    $existingSchedule->release();

                    Log::info('Released existing subscription schedule', [
                        'user_id' => $user->id,
                        'schedule_id' => $existingSchedule->id,
                    ]);

                    // Refresh subscription after releasing schedule
                    $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                } catch (\Exception $e) {
                    Log::warning('Could not retrieve/release existing schedule', [
                        'schedule_id' => $subscription->schedule,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Create subscription schedule to change interval at end of period
            // Strategy: Create schedule from existing subscription, then update it to add second phase
            $schedule = \Stripe\SubscriptionSchedule::create([
                'from_subscription' => $user->stripe_subscription_id,
            ]);

            $periodStart = $this->getSubscriptionPeriodStart($subscription);
            $periodEnd = $this->getSubscriptionPeriodEnd($subscription);

            // Now update the schedule to add the second phase with new interval
            $schedule = \Stripe\SubscriptionSchedule::update(
                $schedule->id,
                [
                    'end_behavior' => 'release',
                    'phases' => [
                        [
                            // Phase 1: Current interval until end of current period (preserve existing)
                            'items' => [
                                [
                                    'price' => $currentPrice->id,
                                    'quantity' => 1,
                                ],
                            ],
                            'start_date' => $periodStart,
                            'end_date' => $periodEnd,
                        ],
                        [
                            // Phase 2: New interval starting at end of current period
                            'items' => [
                                [
                                    'price' => $newPriceId,
                                    'quantity' => 1,
                                ],
                            ],
                            // No end_date means it continues indefinitely
                        ],
                    ],
                ]
            );

            Log::info('Subscription interval change scheduled', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
                'schedule_id' => $schedule->id,
                'old_interval' => $currentInterval,
                'new_interval' => $newInterval,
                'change_date' => \Carbon\Carbon::createFromTimestamp($periodEnd)->toDateTimeString(),
                'new_price_id' => $newPriceId,
            ]);

            // Get localized interval label for success message
            $intervalLabel = $newInterval === 'monthly'
                ? __('messages.monthly_plan')
                : __('messages.yearly_plan');

            $changeDate = \Carbon\Carbon::createFromTimestamp($periodEnd)->translatedFormat('j. F Y');

            return redirect()->route('billing.manage')
                ->with('success', __('messages.interval_change_scheduled', [
                    'interval' => $intervalLabel,
                    'date' => $changeDate,
                ]));

        } catch (\Stripe\Exception\StripeException $e) {
            Log::error('Stripe error scheduling subscription interval change', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'interval' => $newInterval,
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.interval_change_error'));
        } catch (\Exception $e) {
            Log::error('Failed to schedule subscription interval change', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'interval' => $newInterval,
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Cancel scheduled interval change
     */
    public function cancelScheduledIntervalChange()
    {
        $user = auth()->user();

        if (! $user->stripe_subscription_id) {
            return redirect()->route('billing.manage')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

            if (! $subscription->schedule) {
                return redirect()->route('billing.manage')
                    ->with('error', __('messages.no_scheduled_change'));
            }

            // Release the schedule (removes it and keeps subscription as-is)
            $schedule = \Stripe\SubscriptionSchedule::retrieve($subscription->schedule);

            // Release the schedule immediately (instance method, not static)
            $schedule->release();

            Log::info('Cancelled scheduled interval change', [
                'user_id' => $user->id,
                'subscription_id' => $user->stripe_subscription_id,
                'schedule_id' => $schedule->id,
            ]);

            return redirect()->route('billing.manage')
                ->with('success', __('messages.scheduled_change_cancelled'));

        } catch (\Exception $e) {
            Log::error('Failed to cancel scheduled interval change', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.billing_error'));
        }
    }
}
