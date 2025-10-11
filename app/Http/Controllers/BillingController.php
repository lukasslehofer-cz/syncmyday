<?php

namespace App\Http\Controllers;

use App\Helpers\PricingHelper;
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
        
        return view('billing.index', [
            'user' => $user,
            'subscription' => $subscription,
            'proPriceId' => config('services.stripe.pro_price_id'),
            'currency' => PricingHelper::getCurrency(),
            'formattedPrice' => PricingHelper::formatPrice(),
        ]);
    }

    /**
     * Create Stripe Checkout session for trial (used after registration)
     */
    public function createTrialCheckoutSession(Request $request)
    {
        $user = auth()->user();

        // Only allow trial checkout for users in trial without payment method
        if (!$user->isInTrial()) {
            return redirect()->route('billing')
                ->with('error', 'Trial checkout is only available for new users.');
        }

        try {
            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
            }

            // Calculate trial end timestamp
            $trialEnd = $user->subscription_ends_at->timestamp;

            // Get correct Price ID based on user's locale
            $priceId = PricingHelper::getPriceId($user->locale);

            // Create Checkout Session with trial
            $session = Session::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_end' => $trialEnd,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ],
                'success_url' => route('onboarding.start') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('register'),
                'metadata' => [
                    'user_id' => $user->id,
                    'is_trial' => true,
                ],
                // Don't create invoice for trial period
                'invoice_creation' => [
                    'enabled' => false,
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe trial checkout session creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('error', __('messages.billing_error'));
        }
    }

    /**
     * Create Stripe Checkout session for Pro subscription (for expired trials)
     */
    public function createCheckoutSession(Request $request)
    {
        $user = auth()->user();

        try {
            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
            }

            // Get correct Price ID based on user's locale
            $priceId = PricingHelper::getPriceId($user->locale);

            // Create Checkout Session (no trial)
            $session = Session::create([
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
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
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
                $endsAt = $subscription->status === 'trialing' && $subscription->trial_end
                    ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end)
                    : \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);
                
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
        $endsAt = $subscription->status === 'trialing' && $subscription->trial_end
            ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end)
            : \Carbon\Carbon::createFromTimestamp($subscription->current_period_end);
        
        $user->update([
            'subscription_tier' => $isActive ? 'pro' : 'free',
            'subscription_ends_at' => $endsAt,
        ]);

        Log::info('Subscription updated', [
            'user_id' => $user->id,
            'status' => $subscription->status,
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

        Log::info('Payment succeeded', [
            'user_id' => $user->id,
            'amount' => $invoice->amount_paid / 100,
            'currency' => $invoice->currency,
        ]);
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
            'subscription_tier' => 'free',
            'subscription_ends_at' => now(),
        ]);

        Log::info('Subscription cancelled', ['user_id' => $user->id]);
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

        Log::warning('Payment failed', [
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
        ]);

        // TODO: Send email notification to user
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

                // Get recent invoices
                $invoices = \Stripe\Invoice::all([
                    'customer' => $user->stripe_customer_id,
                    'limit' => 10,
                ]);
            }

            return view('billing.manage', [
                'user' => $user,
                'subscription' => $subscription,
                'paymentMethod' => $paymentMethod,
                'invoices' => $invoices,
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

                // Create Checkout Session to add payment method
                $priceId = PricingHelper::getPriceId($user->locale);
                
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

