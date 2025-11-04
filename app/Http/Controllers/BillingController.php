<?php

namespace App\Http\Controllers;

use App\Helpers\PricingHelper;
use App\Models\FakturoidInvoice;
use App\Services\FakturoidService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Checkout\Session;
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
        $monthlyPrice = $monthlyCurrency['symbol'] . number_format($monthlyCurrency['amount'], 0, '.', ',');
        $yearlyPrice = $yearlyCurrency['symbol'] . number_format($yearlyCurrency['amount'], 0, '.', ',');
        
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
        if (!in_array($interval, ['monthly', 'yearly'])) {
            return redirect()->back()
                ->with('error', 'Invalid subscription interval.');
        }

        try {
            // Determine currency to use
            // If user doesn't have Stripe customer yet, use current locale currency
            // If user has Stripe customer, use saved currency (prevents currency conflicts)
            $currency = $user->stripe_currency ?? PricingHelper::getCurrencyCode($user->locale);
            
            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
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
            
            if (!$priceId) {
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
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('billing'),
                'metadata' => [
                    'user_id' => $user->id,
                    'interval' => $interval,
                ],
                'subscription_data' => [
                    'description' => 'SyncMyDay Pro Subscription - ' . ucfirst($interval),
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
        
        if (!$sessionId) {
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
                    : $subscription->current_period_end;
                
                // If timestamp is invalid (null or 0), default to 30 days from now
                $endsAt = $timestamp && $timestamp > 0
                    ? \Carbon\Carbon::createFromTimestamp($timestamp)
                    : now()->addDays(30);
                
                $user->update([
                    'subscription_tier' => 'pro',
                    'subscription_ends_at' => $endsAt,
                ]);

                Log::info('User subscribed to Pro', [
                    'user_id' => $user->id,
                    'is_trial' => $subscription->status === 'trialing',
                    'subscription_id' => $session->subscription,
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
        
        if (!$userId) {
            Log::warning('No user_id in checkout session metadata');
            return;
        }

        $user = \App\Models\User::find($userId);
        
        if (!$user) {
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
     * Handle subscription updated/created
     */
    private function handleSubscriptionUpdated($subscription)
    {
        $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();
        
        if (!$user) {
            // Try to find by customer ID
            $user = \App\Models\User::where('stripe_customer_id', $subscription->customer)->first();
            
            if (!$user) {
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
            : $subscription->current_period_end;
        
        // If timestamp is invalid (null or 0), default to 30 days from now
        $endsAt = $timestamp && $timestamp > 0
            ? \Carbon\Carbon::createFromTimestamp($timestamp)
            : now()->addDays(30);
        
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
        ]);
    }

    /**
     * Handle payment succeeded
     */
    private function handlePaymentSucceeded($invoice)
    {
        $customerId = $invoice->customer;
        $user = \App\Models\User::where('stripe_customer_id', $customerId)->first();
        
        if (!$user) {
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
        
        // Get subscription to determine next billing date and interval
        $nextBillingDate = null;
        $interval = 'yearly'; // default
        if ($user->stripe_subscription_id) {
            try {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                // Protect against null/0 timestamps
                if ($subscription->current_period_end && $subscription->current_period_end > 0) {
                    $nextBillingDate = \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)
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
            $fakturoidService = new FakturoidService();
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
     * Handle subscription deleted (cancellation)
     */
    private function handleSubscriptionDeleted($subscription)
    {
        $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();
        
        if (!$user) {
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
        
        if (!$user) {
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

        if (!$user->stripe_customer_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            // Get subscription details from Stripe
            $subscription = null;
            $paymentMethod = null;
            $invoices = [];

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
            }

            // Get Fakturoid invoices (newest first)
            $fakturoidInvoices = $user->fakturoidInvoices()
                ->where('status', 'created')
                ->orderBy('issued_at', 'desc')
                ->get();

            return view('billing.manage', [
                'user' => $user,
                'subscription' => $subscription,
                'paymentMethod' => $paymentMethod,
                'invoices' => $invoices,
                'fakturoidInvoices' => $fakturoidInvoices,
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
        if (!$invoice->isCreated()) {
            return redirect()->route('billing.manage')
                ->with('error', __('messages.invoice_not_available'));
        }

        try {
            $fakturoidService = new FakturoidService();
            $pdfResponse = $fakturoidService->downloadPdf($invoice->fakturoid_id);

            if ($pdfResponse) {
                // Set proper filename with invoice number
                $filename = $invoice->fakturoid_number 
                    ? "invoice-{$invoice->fakturoid_number}.pdf"
                    : "invoice-{$invoice->id}.pdf";
                
                return $pdfResponse->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
            }

            Log::error('Failed to download Fakturoid PDF', [
                'invoice_id' => $invoice->id,
                'fakturoid_id' => $invoice->fakturoid_id,
            ]);

            return redirect()->route('billing.manage')
                ->with('error', __('messages.invoice_download_failed'));

        } catch (\Exception $e) {
            Log::error('Exception downloading invoice PDF', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
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

        if (!$user->stripe_customer_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            $session = Session::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'mode' => 'setup',
                'success_url' => route('billing.manage') . '?payment_method_updated=1',
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
     * Cancel subscription
     */
    public function cancelSubscription()
    {
        $user = auth()->user();

        if (!$user->stripe_subscription_id) {
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
                'ends_at' => $subscription->current_period_end,
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

        if (!$user->stripe_subscription_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            // Get subscription to check payment method
            $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

            // If payment method was removed, redirect to add new one
            if (!$subscription->default_payment_method) {
                Log::info('Subscription reactivation requires new payment method', [
                    'user_id' => $user->id,
                    'subscription_id' => $user->stripe_subscription_id,
                ]);

                // Create Checkout Session to add payment method (setup mode)
                $session = Session::create([
                    'customer' => $user->stripe_customer_id,
                    'payment_method_types' => ['card'],
                    'mode' => 'setup',
                    'success_url' => route('billing.reactivate-with-payment') . '?session_id={CHECKOUT_SESSION_ID}',
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

        if (!$sessionId || !$user->stripe_subscription_id) {
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
}

