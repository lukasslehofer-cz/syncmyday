<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    /**
     * Show account settings page
     */
    public function index()
    {
        $user = auth()->user();
        
        $timezones = \DateTimeZone::listIdentifiers();
        
        // Get available locales for current domain
        $locales = \App\Helpers\LocaleHelper::getAvailableLocalesWithNames();
        
        return view('account.index', [
            'user' => $user,
            'timezones' => $timezones,
            'locales' => $locales,
        ]);
    }

    /**
     * Update account information (name, timezone, locale)
     */
    public function updateInfo(Request $request)
    {
        $user = auth()->user();
        
        // Get available locales for validation
        $availableLocales = \App\Helpers\LocaleHelper::getAvailableLocales();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'timezone' => 'required|timezone',
            'locale' => 'required|string|in:' . implode(',', $availableLocales),
        ]);

        $user->update($validated);
        
        // Apply locale change immediately for current session
        \Illuminate\Support\Facades\App::setLocale($validated['locale']);

        Log::info('User updated account info', [
            'user_id' => $user->id,
            'changes' => $validated,
        ]);

        return redirect()->route('account.index')
            ->with('success', __('messages.account_updated_successfully'));
    }

    /**
     * Update password (for users who already have password)
     */
    public function updatePassword(Request $request)
    {
        $user = auth()->user();

        // Must have existing password to change it
        if (!$user->password) {
            return redirect()->route('account.index')
                ->with('error', 'You must first add a password.');
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Verify current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'The provided password does not match your current password.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Log::info('User changed password', [
            'user_id' => $user->id,
        ]);

        return redirect()->route('account.index')
            ->with('success', 'Password updated successfully.');
    }

    /**
     * Add password for OAuth users (backup login method)
     */
    public function addPassword(Request $request)
    {
        $user = auth()->user();

        // Can only add password if don't have one
        if ($user->password) {
            return redirect()->route('account.index')
                ->with('error', 'You already have a password. Use "Change Password" instead.');
        }

        $validated = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Set password
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        Log::info('User added password to OAuth account', [
            'user_id' => $user->id,
            'oauth_provider' => $user->oauth_provider,
        ]);

        return redirect()->route('account.index')
            ->with('success', 'Password added successfully! You can now login with email and password.');
    }

    /**
     * Disconnect OAuth provider (only if user has password as backup)
     */
    public function disconnectOAuth(Request $request)
    {
        $user = auth()->user();

        // Can only disconnect OAuth if user has password as backup
        if (!$user->password) {
            return redirect()->route('account.index')
                ->with('error', 'You must add a password before disconnecting OAuth. You need at least one login method.');
        }

        // Can only disconnect if has OAuth
        if (!$user->oauth_provider) {
            return redirect()->route('account.index')
                ->with('error', 'No OAuth provider connected.');
        }

        $provider = $user->getOAuthProviderName();

        // Remove OAuth data
        $user->update([
            'oauth_provider' => null,
            'oauth_provider_id' => null,
            'oauth_provider_email' => null,
        ]);

        Log::info('User disconnected OAuth provider', [
            'user_id' => $user->id,
            'provider' => $provider,
        ]);

        return redirect()->route('account.index')
            ->with('success', $provider . ' has been disconnected. You can now connect a different OAuth provider if you wish.');
    }

    /**
     * Delete account and all associated data
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();

        // Require password confirmation (for non-OAuth users)
        if (!$user->isOAuthUser()) {
            $request->validate([
                'password' => 'required|string',
            ]);

            if (!Hash::check($request->password, $user->password)) {
                return back()->withErrors([
                    'password' => 'The provided password is incorrect.'
                ]);
            }
        }

        Log::warning('User account deletion initiated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'is_oauth' => $user->isOAuthUser(),
        ]);

        // Cancel Stripe subscription and delete customer if exists
        if ($user->stripe_customer_id) {
            try {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                
                // Delete the entire customer (this cancels subscriptions and removes payment methods)
                $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
                $customer->delete();
                
                Log::info('Stripe customer deleted for deleted user', [
                    'user_id' => $user->id,
                    'customer_id' => $user->stripe_customer_id,
                    'subscription_id' => $user->stripe_subscription_id,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to delete Stripe customer during account deletion', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                
                // Fallback: try to cancel subscription directly if customer deletion fails
                if ($user->stripe_subscription_id) {
                    try {
                        $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);
                        $subscription->cancel();
                    } catch (\Exception $e2) {
                        Log::error('Failed to cancel subscription fallback', [
                            'user_id' => $user->id,
                            'error' => $e2->getMessage(),
                        ]);
                    }
                }
            }
        }

        // Delete will cascade to:
        // - calendar_connections
        // - sync_rules (and targets via observer)
        // - sync_event_mappings (via rules cascade)
        // - sync_logs
        // - email_calendar_connections
        
        $userId = $user->id;
        $userEmail = $user->email;
        
        // Logout before deletion
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Soft delete user
        $user->delete();

        Log::info('User account deleted', [
            'user_id' => $userId,
            'email' => $userEmail,
        ]);

        // Send account deleted confirmation email
        try {
            \Mail::to($userEmail)->send(new \App\Mail\AccountDeletedMail($user));
            
            Log::info('Account deleted email sent', [
                'user_id' => $userId,
                'email' => $userEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send account deleted email', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('home')
            ->with('success', 'Your account has been deleted successfully.');
    }
}

