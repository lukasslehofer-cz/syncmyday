<?php

namespace App\Http\Controllers;

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
        
        return view('billing.index', [
            'user' => $user,
            'proPriceId' => config('services.stripe.pro_price_id'),
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

            // Create Checkout Session with trial
            $session = Session::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => config('services.stripe.pro_price_id'),
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

            // Create Checkout Session (no trial)
            $session = Session::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => config('services.stripe.pro_price_id'),
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

            // Check if we should redirect to onboarding (for new registrations)
            if ($request->query('redirect') === 'onboarding') {
                return redirect()->route('onboarding.start')
                    ->with('success', __('messages.registration_success'));
            }

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
     * Create portal session for subscription management
     */
    public function portal()
    {
        $user = auth()->user();

        if (!$user->stripe_customer_id) {
            return redirect()->route('billing')
                ->with('error', __('messages.no_subscription'));
        }

        try {
            $session = \Stripe\BillingPortal\Session::create([
                'customer' => $user->stripe_customer_id,
                'return_url' => route('billing'),
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Portal session creation failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('error', __('messages.billing_error'));
        }
    }
}

