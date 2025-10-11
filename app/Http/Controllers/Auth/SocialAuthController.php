<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CalendarConnection;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Stripe;

class SocialAuthController extends Controller
{
    /**
     * Redirect to Google OAuth for login/registration
     */
    public function redirectToGoogle()
    {
        $state = Str::random(40);
        
        // Store state in cache instead of session (works with SameSite=lax cookies)
        Cache::put("oauth_state_{$state}", [
            'action' => 'login',
            'created_at' => now(),
        ], now()->addMinutes(10));
        
        // Create Google client with login-specific redirect URI
        $client = new \Google\Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(url('/auth/google/callback'));
        $client->setScopes(config('services.google.scopes'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setState($state);
        
        return redirect($client->createAuthUrl());
    }

    /**
     * Handle Google OAuth callback for login/registration
     */
    public function handleGoogleCallback(Request $request)
    {
        // Verify state from cache (not session - works with SameSite=lax)
        $state = $request->state;
        $stateData = Cache::get("oauth_state_{$state}");
        
        // Debug logging
        Log::info('Google OAuth callback received', [
            'has_code' => $request->has('code'),
            'has_error' => $request->has('error'),
            'state_from_request' => $state,
            'state_exists_in_cache' => $stateData !== null,
        ]);

        // Verify state
        if (!$stateData) {
            Log::warning('OAuth state not found or expired', [
                'state' => $state,
            ]);
            return redirect()->route('login')
                ->with('error', __('messages.oauth_state_mismatch'));
        }
        
        // Delete state from cache (one-time use)
        Cache::forget("oauth_state_{$state}");

        // Check if user denied access
        if ($request->has('error')) {
            Log::info('Google OAuth login cancelled by user', [
                'error' => $request->error,
            ]);
            
            return redirect()->route('login')
                ->with('warning', __('messages.oauth_cancelled'));
        }

        // Check if code is present
        if (!$request->has('code')) {
            Log::error('Google OAuth login callback missing code', [
                'request' => $request->all(),
            ]);
            
            return redirect()->route('login')
                ->with('error', __('messages.oauth_failed'));
        }

        try {
            // Create Google client with login-specific redirect URI
            $client = new \Google\Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(url('/auth/google/callback'));
            
            // Exchange code for tokens
            $tokens = $client->fetchAccessTokenWithAuthCode($request->code);
            
            if (isset($tokens['error'])) {
                throw new \Exception('OAuth error: ' . $tokens['error']);
            }
            
            // Get user info from Google
            $client = new \Google\Client();
            $client->setAccessToken($tokens);
            $calendarService = new \Google\Service\Calendar($client);
            
            // Get account info
            $primaryCalendar = $calendarService->calendars->get('primary');
            $googleEmail = $primaryCalendar->getId();
            $googleId = $primaryCalendar->getId();
            
            // Find or create user
            $user = User::where('oauth_provider', 'google')
                        ->where('oauth_provider_id', $googleId)
                        ->first();

            if (!$user) {
                // Check if email already exists with different provider
                $existingUser = User::where('email', $googleEmail)->first();
                
                if ($existingUser) {
                    return redirect()->route('login')
                        ->with('error', 'This email is already registered. Please use your original login method or contact support.');
                }

                // Create new user
                $user = User::create([
                    'name' => $googleEmail, // We'll use email as name for now
                    'email' => $googleEmail,
                    'oauth_provider' => 'google',
                    'oauth_provider_id' => $googleId,
                    'oauth_provider_email' => $googleEmail,
                    'email_verified_at' => now(), // OAuth users are pre-verified
                    'locale' => app()->getLocale(),
                    'timezone' => session('detected_timezone', 'UTC'), // Will be updated on first dashboard load
                    'subscription_tier' => 'pro',
                    'subscription_ends_at' => now()->addDays(31), // 31-day trial
                ]);

                // Create Stripe customer
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $customer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                        'metadata' => [
                            'user_id' => $user->id,
                        ],
                    ]);
                    $user->update(['stripe_customer_id' => $customer->id]);
                } catch (\Exception $e) {
                    Log::error('Stripe customer creation failed for OAuth user', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                }

                Log::info('New user created via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }

            // Login the user
            Auth::login($user, true);

            // Now connect the calendar automatically
            $this->connectGoogleCalendar($user, $tokens, $googleId, $googleEmail);

            // Check if user needs to setup payment method
            if (!$user->stripe_subscription_id) {
                return $this->redirectToStripeCheckout($user, 'oauth-google');
            }

            // Redirect to dashboard with success message
            return redirect()->route('dashboard')
                ->with('success', 'Welcome! Your Google account and calendar have been connected successfully.');

        } catch (\Exception $e) {
            Log::error('Google OAuth login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Failed to login with Google. Please try again or use email/password.');
        }
    }

    /**
     * Redirect to Microsoft OAuth for login/registration
     */
    public function redirectToMicrosoft()
    {
        $state = Str::random(40);
        
        // Store state in cache instead of session (works with SameSite=lax cookies)
        Cache::put("oauth_state_{$state}", [
            'action' => 'login',
            'created_at' => now(),
        ], now()->addMinutes(10));
        
        // Build auth URL with login-specific redirect URI
        $scopes = implode(' ', config('services.microsoft.scopes'));
        $tenant = config('services.microsoft.tenant', 'common');
        $clientId = config('services.microsoft.client_id');
        $redirectUri = url('/auth/microsoft/callback');
        
        $authUrl = sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/authorize?client_id=%s&response_type=code&redirect_uri=%s&response_mode=query&scope=%s&state=%s&prompt=select_account',
            $tenant,
            $clientId,
            urlencode($redirectUri),
            urlencode($scopes),
            $state
        );
        
        return redirect($authUrl);
    }

    /**
     * Handle Microsoft OAuth callback for login/registration
     */
    public function handleMicrosoftCallback(Request $request)
    {
        // Verify state from cache (not session - works with SameSite=lax)
        $state = $request->state;
        $stateData = Cache::get("oauth_state_{$state}");
        
        Log::info('Microsoft OAuth callback received', [
            'has_code' => $request->has('code'),
            'has_error' => $request->has('error'),
            'state_from_request' => $state,
            'state_exists_in_cache' => $stateData !== null,
        ]);
        
        // Verify state
        if (!$stateData) {
            Log::warning('OAuth state not found or expired', [
                'state' => $state,
            ]);
            return redirect()->route('login')
                ->with('error', __('messages.oauth_state_mismatch'));
        }
        
        // Delete state from cache (one-time use)
        Cache::forget("oauth_state_{$state}");

        // Check if user denied access
        if ($request->has('error')) {
            Log::info('Microsoft OAuth login cancelled by user', [
                'error' => $request->error,
            ]);
            
            return redirect()->route('login')
                ->with('warning', __('messages.oauth_cancelled'));
        }

        // Check if code is present
        if (!$request->has('code')) {
            Log::error('Microsoft OAuth login callback missing code', [
                'request' => $request->all(),
            ]);
            
            return redirect()->route('login')
                ->with('error', __('messages.oauth_failed'));
        }

        try {
            // Exchange code for tokens using login-specific redirect URI
            $response = \Illuminate\Support\Facades\Http::asForm()->post(
                'https://login.microsoftonline.com/' . config('services.microsoft.tenant', 'common') . '/oauth2/v2.0/token',
                [
                    'client_id' => config('services.microsoft.client_id'),
                    'client_secret' => config('services.microsoft.client_secret'),
                    'code' => $request->code,
                    'redirect_uri' => url('/auth/microsoft/callback'),
                    'grant_type' => 'authorization_code',
                    'scope' => implode(' ', config('services.microsoft.scopes')),
                ]
            );

            if (!$response->successful()) {
                throw new \Exception('OAuth error: ' . $response->body());
            }

            $tokens = $response->json();
            
            // Get user info from Microsoft
            $graph = new \Microsoft\Graph\Graph();
            $graph->setAccessToken($tokens['access_token']);
            
            $msUser = $graph->createRequest('GET', '/me')
                ->setReturnType(\Microsoft\Graph\Model\User::class)
                ->execute();
                
            $microsoftId = $msUser->getId();
            $microsoftEmail = $msUser->getUserPrincipalName() ?? $msUser->getMail();
            $displayName = $msUser->getDisplayName() ?? $microsoftEmail;
            
            // Find or create user
            $user = User::where('oauth_provider', 'microsoft')
                        ->where('oauth_provider_id', $microsoftId)
                        ->first();

            if (!$user) {
                // Check if email already exists with different provider
                $existingUser = User::where('email', $microsoftEmail)->first();
                
                if ($existingUser) {
                    return redirect()->route('login')
                        ->with('error', 'This email is already registered. Please use your original login method or contact support.');
                }

                // Create new user
                $user = User::create([
                    'name' => $displayName,
                    'email' => $microsoftEmail,
                    'oauth_provider' => 'microsoft',
                    'oauth_provider_id' => $microsoftId,
                    'oauth_provider_email' => $microsoftEmail,
                    'email_verified_at' => now(), // OAuth users are pre-verified
                    'locale' => app()->getLocale(),
                    'timezone' => session('detected_timezone', 'UTC'), // Will be updated on first dashboard load
                    'subscription_tier' => 'pro',
                    'subscription_ends_at' => now()->addDays(31), // 31-day trial
                ]);

                // Create Stripe customer
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $customer = \Stripe\Customer::create([
                        'email' => $user->email,
                        'name' => $user->name,
                        'metadata' => [
                            'user_id' => $user->id,
                        ],
                    ]);
                    $user->update(['stripe_customer_id' => $customer->id]);
                } catch (\Exception $e) {
                    Log::error('Stripe customer creation failed for OAuth user', [
                        'error' => $e->getMessage(),
                        'user_id' => $user->id,
                    ]);
                }

                Log::info('New user created via Microsoft OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            }

            // Login the user
            Auth::login($user, true);

            // Now connect the calendar automatically
            $this->connectMicrosoftCalendar($user, $tokens, $microsoftId, $microsoftEmail);

            // Check if user needs to setup payment method
            if (!$user->stripe_subscription_id) {
                return $this->redirectToStripeCheckout($user, 'oauth-microsoft');
            }

            // Redirect to dashboard with success message
            return redirect()->route('dashboard')
                ->with('success', 'Welcome! Your Microsoft account and calendar have been connected successfully.');

        } catch (\Exception $e) {
            Log::error('Microsoft OAuth login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', 'Failed to login with Microsoft. Please try again or use email/password.');
        }
    }

    /**
     * Connect Google calendar for the user
     */
    private function connectGoogleCalendar(User $user, array $tokens, string $accountId, string $email): void
    {
        try {
            // Get available calendars
            $client = new \Google\Client();
            $client->setAccessToken($tokens);
            $calendarService = new \Google\Service\Calendar($client);
            
            $calendarList = $calendarService->calendarList->listCalendarList();
            $calendars = [];
            foreach ($calendarList->getItems() as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'name' => $calendar->getSummary(),
                    'primary' => $calendar->getPrimary() ?? false,
                    'access_role' => $calendar->getAccessRole(),
                ];
            }

            // Create or update calendar connection
            $connection = CalendarConnection::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => 'google',
                    'provider_account_id' => $accountId,
                ],
                [
                    'provider_email' => $email,
                    'available_calendars' => $calendars,
                    'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
                    'status' => 'active',
                    'last_error' => null,
                ]
            );

            // Set encrypted tokens
            $connection->setAccessToken($tokens['access_token']);
            if (isset($tokens['refresh_token'])) {
                $connection->setRefreshToken($tokens['refresh_token']);
            }
            $connection->save();

            Log::info('Google calendar auto-connected for OAuth user', [
                'user_id' => $user->id,
                'connection_id' => $connection->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to auto-connect Google calendar for OAuth user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            // Don't throw - user is already logged in
        }
    }

    /**
     * Connect Microsoft calendar for the user
     */
    private function connectMicrosoftCalendar(User $user, array $tokens, string $accountId, string $email): void
    {
        try {
            // Get available calendars
            $graph = new \Microsoft\Graph\Graph();
            $graph->setAccessToken($tokens['access_token']);
            
            $calendarList = $graph->createRequest('GET', '/me/calendars')
                ->setReturnType(\Microsoft\Graph\Model\Calendar::class)
                ->execute();
                
            $calendars = [];
            foreach ($calendarList as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'name' => $calendar->getName(),
                    'primary' => $calendar->getIsDefaultCalendar() ?? false,
                    'access_role' => $calendar->getCanEdit() ? 'owner' : 'reader',
                ];
            }

            // Create or update calendar connection
            $connection = CalendarConnection::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => 'microsoft',
                    'provider_account_id' => $accountId,
                ],
                [
                    'provider_email' => $email,
                    'available_calendars' => $calendars,
                    'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
                    'status' => 'active',
                    'last_error' => null,
                ]
            );

            // Set encrypted tokens
            $connection->setAccessToken($tokens['access_token']);
            if (isset($tokens['refresh_token'])) {
                $connection->setRefreshToken($tokens['refresh_token']);
            }
            $connection->save();

            Log::info('Microsoft calendar auto-connected for OAuth user', [
                'user_id' => $user->id,
                'connection_id' => $connection->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to auto-connect Microsoft calendar for OAuth user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);
            // Don't throw - user is already logged in
        }
    }

    /**
     * Redirect user to Stripe checkout for payment method setup
     */
    private function redirectToStripeCheckout(User $user, string $source)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            // Check if user already has Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            } else {
                $customer = \Stripe\Customer::retrieve($user->stripe_customer_id);
            }

            // Calculate trial end timestamp
            $trialEnd = $user->subscription_ends_at->timestamp;

            // Create Checkout Session with trial
            $session = \Stripe\Checkout\Session::create([
                'customer' => $customer->id,
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
                'success_url' => route('billing.success') . '?session_id={CHECKOUT_SESSION_ID}&redirect=dashboard',
                'cancel_url' => route('dashboard'),
                'metadata' => [
                    'user_id' => $user->id,
                    'is_trial' => true,
                    'source' => $source,
                ],
            ]);

            return redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe checkout session creation failed for OAuth user', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'source' => $source,
            ]);

            // If Stripe fails, still let user in but show them billing page
            return redirect()->route('billing')
                ->with('warning', 'Please set up your payment method to continue using SyncMyDay.');
        }
    }
}

