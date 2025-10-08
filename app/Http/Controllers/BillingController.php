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
     * Create Stripe Checkout session for Pro subscription
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

            // Create Checkout Session
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
            $user->update([
                'subscription_tier' => 'pro',
                'stripe_subscription_id' => $session->subscription,
            ]);

            Log::info('User subscribed to Pro', ['user_id' => $user->id]);

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
            case 'customer.subscription.updated':
            case 'customer.subscription.created':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
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
     * Handle subscription updated/created
     */
    private function handleSubscriptionUpdated($subscription)
    {
        $user = \App\Models\User::where('stripe_subscription_id', $subscription->id)->first();
        
        if (!$user) {
            Log::warning('User not found for subscription', ['subscription_id' => $subscription->id]);
            return;
        }

        $user->update([
            'subscription_tier' => 'pro',
            'subscription_ends_at' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
        ]);

        Log::info('Subscription updated', ['user_id' => $user->id]);
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

