<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\PricingHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class AuthController extends Controller
{
    /**
     * Show registration form
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'locale' => app()->getLocale(),
            'timezone' => $request->timezone ?? 'UTC',
            'subscription_tier' => 'pro',
            'subscription_ends_at' => now()->addDays(config('services.stripe.trial_period_days')),
        ]);

        Auth::login($user);
        
        // Send email verification notification
        $user->sendEmailVerificationNotification();

        // Create Stripe Checkout Session for trial
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Create Stripe customer
            $customer = \Stripe\Customer::create([
                'email' => $user->email,
                'name' => $user->name,
                'metadata' => [
                    'user_id' => $user->id,
                ],
            ]);

            $user->update(['stripe_customer_id' => $customer->id]);

            // Calculate trial end timestamp
            $trialEnd = $user->subscription_ends_at->timestamp;

            // Get correct Price ID based on user's locale
            $priceId = PricingHelper::getPriceId($user->locale);

            // Create Checkout Session with trial
            $session = Session::create([
                'customer' => $customer->id,
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
                        'locale' => $user->locale,
                    ],
                ],
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}&redirect=onboarding',
                'cancel_url' => route('dashboard'),
                'metadata' => [
                    'user_id' => $user->id,
                    'is_trial' => true,
                    'locale' => $user->locale,
                ],
                // Don't create invoice for trial period
                'invoice_creation' => [
                    'enabled' => false,
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe trial checkout session creation failed during registration', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            // If Stripe fails, still let user in but show them billing page
            return redirect()->route('billing')
                ->with('warning', __('messages.setup_payment_method'));
        }
    }

    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user exists and is OAuth user WITHOUT password
        $user = User::where('email', $credentials['email'])->first();
        
        if ($user && $user->isOAuthUser() && !$user->password) {
            return back()->withErrors([
                'email' => 'This account uses ' . $user->getOAuthProviderName() . ' login. Please use the "Continue with ' . $user->getOAuthProviderName() . '" button above, or add a password in Account Settings.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Update timezone if provided and user has UTC
            $user = Auth::user();
            if ($request->has('timezone') && $user->timezone === 'UTC') {
                $timezone = $request->input('timezone');
                // Validate timezone
                if (in_array($timezone, timezone_identifiers_list())) {
                    $user->update(['timezone' => $timezone]);
                }
            }
            
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => __('messages.login_failed'),
        ])->onlyInput('email');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}

