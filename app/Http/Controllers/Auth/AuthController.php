<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

        Log::info('New user registered with trial', [
            'user_id' => $user->id,
            'email' => $user->email,
            'trial_ends_at' => $user->subscription_ends_at,
        ]);

        // Redirect to onboarding (no payment required during registration)
        return redirect()->route('onboarding.start')
            ->with('success', __('messages.registration_success'));
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
        $user = Auth::user();
        
        // If user completed onboarding steps, mark completion time in DB
        // Progress bar won't show on next login
        if ($user && $user->isInTrial() && !$user->onboarding_completed_at) {
            $hasConnections = $user->calendarConnections()->count() >= 2;
            $hasRules = $user->syncRules()->count() > 0;
            
            if ($hasConnections && $hasRules) {
                $user->update(['onboarding_completed_at' => now()]);
            }
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}

