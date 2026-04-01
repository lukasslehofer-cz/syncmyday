<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use App\Models\CalendarConnection;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Get URL with current domain (multi-domain support)
     */
    private function getCurrentDomainUrl(string $path): string
    {
        $appUrl = rtrim(config('app.url'), '/');
        $currentUrl = rtrim(url('/'), '/');
        
        return $currentUrl . $path;
    }
    
    /**
     * Detect Microsoft account type (personal vs work/school)
     */
    private function detectMicrosoftAccountType(string $email, array $tokens): string
    {
        // Parse JWT token to check tenant type
        if (isset($tokens['access_token'])) {
            $accessToken = $tokens['access_token'];
            $tokenParts = explode('.', $accessToken);
            
            if (count($tokenParts) === 3) {
                try {
                    $payload = json_decode(base64_decode(strtr($tokenParts[1], '-_', '+/')), true);
                    
                    // Check tenant ID - personal accounts use specific tenant ID
                    if (isset($payload['tid'])) {
                        $tenantId = $payload['tid'];
                        // Personal Microsoft accounts have specific tenant IDs
                        if (in_array($tenantId, [
                            '9188040d-6c67-4c5b-b112-36a304b66dad', // Common personal account tenant
                            'consumers', // Sometimes represented as 'consumers'
                        ])) {
                            return 'personal';
                        }
                    }
                    
                    // Check issuer
                    if (isset($payload['iss'])) {
                        if (str_contains($payload['iss'], 'consumers')) {
                            return 'personal';
                        }
                    }
                } catch (\Exception $e) {
                    Log::debug('Failed to parse JWT token for account type detection', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        // Fallback: Check email domain for known personal account patterns
        $personalDomains = ['outlook.com', 'hotmail.com', 'live.com', 'msn.com'];
        $emailDomain = strtolower(substr(strrchr($email, '@'), 1));
        
        if (in_array($emailDomain, $personalDomains)) {
            return 'personal';
        }
        
        // If we can't determine, assume work/school account
        return 'work';
    }
    
    /**
     * Redirect to Google OAuth for login/registration
     */
    public function redirectToGoogle()
    {
        $state = Str::random(40);
        
        // Get timezone key from request (sent via JavaScript before redirect)
        $timezoneKey = request()->query('timezone_key');
        
        // Store state in cache instead of session (works with SameSite=lax cookies)
        Cache::put("oauth_state_{$state}", [
            'action' => 'login',
            'created_at' => now(),
            'timezone_key' => $timezoneKey, // Store timezone key to retrieve later
        ], now()->addMinutes(10));
        
        // Create Google client with login-specific redirect URI
        $client = new \Google\Client();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri($this->getCurrentDomainUrl('/auth/google/callback'));
        $client->setScopes(config('services.google.scopes'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account');
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
            $client->setRedirectUri($this->getCurrentDomainUrl('/auth/google/callback'));
            
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
                // Check if email already exists with different provider (ignore soft-deleted)
                $existingUser = User::where('email', $googleEmail)
                                    ->whereNull('deleted_at')
                                    ->first();
                
                if ($existingUser) {
                    return redirect()->route('login')
                        ->with('error', __('messages.email_already_registered'));
                }

                // Get timezone from cache using the key stored in state
                $timezone = 'UTC';
                if (!empty($stateData['timezone_key'])) {
                    $cachedTimezone = Cache::get("timezone_{$stateData['timezone_key']}");
                    if ($cachedTimezone) {
                        $timezone = $cachedTimezone;
                        Cache::forget("timezone_{$stateData['timezone_key']}");
                        
                        Log::info('Retrieved timezone from cache for new Google user', [
                            'timezone' => $timezone,
                        ]);
                    }
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
                    'timezone' => $timezone,
                    'registration_domain' => \App\Helpers\EmailHelper::getCurrentDomain(),
                    'subscription_tier' => 'pro',
                    'subscription_ends_at' => now()->addDays(config('services.stripe.trial_period_days')),
                ]);

                // Send welcome email (OAuth users don't trigger Verified event)
                try {
                    Mail::to($user->email)->send(new WelcomeMail($user));
                } catch (\Exception $e) {
                    Log::error('Failed to send welcome email for OAuth user', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                Log::info('New user created via Google OAuth', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                // Track sign_up conversion
                $metaEventId = app(\App\Services\MetaConversionsApiService::class)->sendEvent('CompleteRegistration', $request, $user, [
                    'content_name' => 'google',
                    'status' => true,
                ]);
                session()->flash('track_signup', [
                    'method' => 'google',
                    'user_id' => $user->id,
                    'meta_event_id' => $metaEventId,
                ]);
            }

            // Login the user
            Auth::login($user, true);

            // Now connect the calendar automatically
            $this->connectGoogleCalendar($user, $tokens, $googleId, $googleEmail);

            // Redirect to onboarding for new users, dashboard for existing users
            if ($user->wasRecentlyCreated) {
                return redirect()->route('onboarding.start')
                    ->with('success', __('messages.oauth_google_login_success'));
            }

            // Existing user - go to dashboard
            return redirect()->route('dashboard')
                ->with('success', __('messages.oauth_login_welcome_back'));

        } catch (\Exception $e) {
            Log::error('Google OAuth login failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', __('messages.oauth_google_login_failed'));
        }
    }

    /**
     * Redirect to Microsoft OAuth for login/registration
     */
    public function redirectToMicrosoft()
    {
        $state = Str::random(40);
        
        // Get timezone key from request (sent via JavaScript before redirect)
        $timezoneKey = request()->query('timezone_key');
        
        // Store state in cache instead of session (works with SameSite=lax cookies)
        Cache::put("oauth_state_{$state}", [
            'action' => 'login',
            'created_at' => now(),
            'timezone_key' => $timezoneKey, // Store timezone key to retrieve later
        ], now()->addMinutes(10));
        
        // Build auth URL with login-specific redirect URI
        $scopes = implode(' ', config('services.microsoft.scopes'));
        $tenant = config('services.microsoft.tenant', 'common');
        $clientId = config('services.microsoft.client_id');
        $redirectUri = $this->getCurrentDomainUrl('/auth/microsoft/callback');
        
        Log::info('Microsoft OAuth - Redirect initiated', [
            'redirect_uri' => $redirectUri,
            'tenant' => $tenant,
            'has_client_id' => !empty($clientId),
        ]);
        
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
            Log::info('Microsoft OAuth - Starting token exchange', [
                'redirect_uri' => $this->getCurrentDomainUrl('/auth/microsoft/callback'),
                'has_code' => !empty($request->code),
            ]);
            
            $response = \Illuminate\Support\Facades\Http::asForm()->post(
                'https://login.microsoftonline.com/' . config('services.microsoft.tenant', 'common') . '/oauth2/v2.0/token',
                [
                    'client_id' => config('services.microsoft.client_id'),
                    'client_secret' => config('services.microsoft.client_secret'),
                    'code' => $request->code,
                    'redirect_uri' => $this->getCurrentDomainUrl('/auth/microsoft/callback'),
                    'grant_type' => 'authorization_code',
                    'scope' => implode(' ', config('services.microsoft.scopes')),
                ]
            );

            Log::info('Microsoft OAuth - Token exchange response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'has_body' => !empty($response->body()),
            ]);

            if (!$response->successful()) {
                Log::error('Microsoft OAuth - Token exchange failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('OAuth error: ' . $response->body());
            }

            $tokens = $response->json();
            
            Log::info('Microsoft OAuth - Tokens received', [
                'has_access_token' => isset($tokens['access_token']),
                'has_refresh_token' => isset($tokens['refresh_token']),
                'expires_in' => $tokens['expires_in'] ?? null,
                'scope' => $tokens['scope'] ?? null,
                'token_type' => $tokens['token_type'] ?? null,
            ]);
            
            // Get user info from Microsoft
            Log::info('Microsoft OAuth - Calling Graph API /me');
            $graph = new \Microsoft\Graph\Graph();
            
            $accessToken = is_array($tokens['access_token']) ? $tokens['access_token']['access_token'] ?? $tokens['access_token'] : $tokens['access_token'];
            $graph->setAccessToken($accessToken);
            
            Log::info('Microsoft OAuth - Graph instance created', [
                'token_length' => strlen($accessToken),
                'token_preview' => substr($accessToken, 0, 20) . '...',
            ]);
            
            try {
                $msUser = $graph->createRequest('GET', '/me')
                    ->setReturnType(\Microsoft\Graph\Model\User::class)
                    ->execute();
                    
                Log::info('Microsoft OAuth - Graph API /me succeeded', [
                    'user_id' => $msUser->getId(),
                    'email' => $msUser->getUserPrincipalName() ?? $msUser->getMail(),
                ]);
            } catch (\Microsoft\Graph\Exception\GraphException $e) {
                Log::error('Microsoft OAuth - Graph API /me failed', [
                    'error' => $e->getMessage(),
                    'code' => $e->getCode(),
                ]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Microsoft OAuth - Unexpected error calling Graph API', [
                    'error' => $e->getMessage(),
                    'class' => get_class($e),
                ]);
                throw $e;
            }
                
            $microsoftId = $msUser->getId();
            $microsoftEmail = $msUser->getUserPrincipalName() ?? $msUser->getMail();
            $displayName = $msUser->getDisplayName() ?? $microsoftEmail;
            
            if (empty($microsoftId)) {
                Log::error('Microsoft OAuth - Empty Microsoft ID received', [
                    'user_object' => $msUser,
                ]);
                throw new \Exception('Microsoft ID is empty');
            }
            
            if (empty($microsoftEmail)) {
                Log::error('Microsoft OAuth - Empty email received', [
                    'user_principal_name' => $msUser->getUserPrincipalName(),
                    'mail' => $msUser->getMail(),
                ]);
                throw new \Exception('Microsoft email is empty');
            }
            
            Log::info('Microsoft OAuth - User info extracted', [
                'microsoft_id' => $microsoftId,
                'email' => $microsoftEmail,
                'display_name' => $displayName,
            ]);
            
            // Find or create user
            Log::info('Microsoft OAuth - Searching for existing user', [
                'microsoft_id' => $microsoftId,
                'email' => $microsoftEmail,
            ]);
            
            // First, try to find by oauth_provider + oauth_provider_id
            $user = User::where('oauth_provider', 'microsoft')
                        ->where('oauth_provider_id', $microsoftId)
                        ->first();

            if (!$user) {
                Log::info('Microsoft OAuth - User not found by provider ID, checking for existing email', [
                    'email' => $microsoftEmail,
                ]);
                
                // Check if email already exists (ignore soft-deleted)
                $existingUser = User::where('email', $microsoftEmail)
                                    ->whereNull('deleted_at')
                                    ->first();
                
                if ($existingUser) {
                    // If user exists with same provider but different provider_id, update it
                    if ($existingUser->oauth_provider === 'microsoft') {
                        Log::info('Microsoft OAuth - Found user with same email and provider, updating oauth_provider_id', [
                            'user_id' => $existingUser->id,
                            'old_provider_id' => $existingUser->oauth_provider_id,
                            'new_provider_id' => $microsoftId,
                        ]);
                        
                        $existingUser->update([
                            'oauth_provider_id' => $microsoftId,
                            'oauth_provider_email' => $microsoftEmail,
                        ]);
                        
                        $user = $existingUser;
                    } else {
                        // Different provider - show error
                        Log::warning('Microsoft OAuth - Email already exists with different provider', [
                            'email' => $microsoftEmail,
                            'existing_provider' => $existingUser->oauth_provider,
                        ]);
                        return redirect()->route('login')
                            ->with('error', __('messages.email_already_registered'));
                    }
                }
                
                // If user still doesn't exist, create new one
                if (!$user) {
                    // Get timezone from cache using the key stored in state
                    $timezone = 'UTC';
                    if (!empty($stateData['timezone_key'])) {
                        $cachedTimezone = Cache::get("timezone_{$stateData['timezone_key']}");
                        if ($cachedTimezone) {
                            $timezone = $cachedTimezone;
                            Cache::forget("timezone_{$stateData['timezone_key']}");
                            
                            Log::info('Retrieved timezone from cache for new Microsoft user', [
                                'timezone' => $timezone,
                            ]);
                        }
                    }

                    // Create new user
                    Log::info('Microsoft OAuth - Creating new user', [
                        'email' => $microsoftEmail,
                        'display_name' => $displayName,
                        'timezone' => $timezone,
                    ]);
                    
                    try {
                        $user = User::create([
                            'name' => $displayName,
                            'email' => $microsoftEmail,
                            'oauth_provider' => 'microsoft',
                            'oauth_provider_id' => $microsoftId,
                            'oauth_provider_email' => $microsoftEmail,
                            'email_verified_at' => now(), // OAuth users are pre-verified
                            'locale' => app()->getLocale(),
                            'timezone' => $timezone,
                            'registration_domain' => \App\Helpers\EmailHelper::getCurrentDomain(),
                            'subscription_tier' => 'pro',
                            'subscription_ends_at' => now()->addDays(config('services.stripe.trial_period_days')),
                        ]);
                        
                        Log::info('Microsoft OAuth - User created successfully', [
                            'user_id' => $user->id,
                        ]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        Log::error('Microsoft OAuth - Database error creating user', [
                            'error' => $e->getMessage(),
                            'code' => $e->getCode(),
                        ]);
                        throw $e;
                    }

                    // Send welcome email (OAuth users don't trigger Verified event)
                    try {
                        Mail::to($user->email)->send(new WelcomeMail($user));
                    } catch (\Exception $e) {
                        Log::error('Failed to send welcome email for OAuth user', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    Log::info('New user created via Microsoft OAuth', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);
                    
                    // Track sign_up conversion
                    $metaEventId = app(\App\Services\MetaConversionsApiService::class)->sendEvent('CompleteRegistration', $request, $user, [
                        'content_name' => 'microsoft',
                        'status' => true,
                    ]);
                    session()->flash('track_signup', [
                        'method' => 'microsoft',
                        'user_id' => $user->id,
                        'meta_event_id' => $metaEventId,
                    ]);
                }
            }

            // Login the user
            Log::info('Microsoft OAuth - Logging in user', [
                'user_id' => $user->id,
            ]);
            
            Auth::login($user, true);
            
            Log::info('Microsoft OAuth - User logged in, attempting calendar connection');

            // Try to connect the calendar automatically, but don't fail login if it fails
            $calendarConnected = false;
            $adminConsentRequired = false;
            try {
                // Pass the Graph instance that already has the token set
                $this->connectMicrosoftCalendar($user, $tokens, $microsoftId, $microsoftEmail, $graph);
                $calendarConnected = true;
                Log::info('Microsoft OAuth - Calendar connection completed successfully');
            } catch (\Exception $e) {
                // Log error but don't fail login
                Log::warning('Microsoft OAuth - Calendar connection failed during login', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                    'error_code' => $e->getCode(),
                ]);
                
                // Check if it's an admin consent issue (401 error)
                if ($e->getCode() === 401) {
                    $adminConsentRequired = true;
                    Log::info('Microsoft OAuth - Admin consent detected, will redirect to instructions');
                }
            }

            // If admin consent is required, redirect to admin consent screen immediately
            if ($adminConsentRequired) {
                // Find the pending connection that was created
                $pendingConnection = \App\Models\CalendarConnection::where('user_id', $user->id)
                    ->where('provider', 'microsoft')
                    ->where('provider_account_id', $microsoftId)
                    ->where('status', 'error')
                    ->where('last_error', 'like', '%Admin consent required%')
                    ->first();
                
                if ($pendingConnection) {
                    Log::info('Microsoft OAuth - Redirecting to admin consent screen', [
                        'user_id' => $user->id,
                        'connection_id' => $pendingConnection->id,
                    ]);
                    
                    // Build admin consent URL
                    $clientId = config('services.microsoft.client_id');
                    $currentUrl = rtrim(url('/'), '/');
                    $redirectUri = $currentUrl . '/admin-consent/microsoft/callback';
                    $scopes = implode(' ', config('services.microsoft.scopes'));
                    
                    $adminConsentUrl = sprintf(
                        'https://login.microsoftonline.com/organizations/v2.0/adminconsent?client_id=%s&redirect_uri=%s&scope=%s&state=%s',
                        $clientId,
                        urlencode($redirectUri),
                        urlencode($scopes),
                        $pendingConnection->id // Pass connection ID for callback
                    );
                    
                    // Redirect directly to admin consent screen
                    return redirect($adminConsentUrl);
                }
            }

            // Redirect to onboarding for new users, dashboard for existing users
            if ($user->wasRecentlyCreated) {
                $message = $calendarConnected 
                    ? __('messages.oauth_microsoft_login_success')
                    : __('messages.oauth_account_created_approval_needed');
                return redirect()->route('onboarding.start')
                    ->with($calendarConnected ? 'success' : 'warning', $message);
            }

            // Existing user - go to dashboard
            $message = $calendarConnected
                ? __('messages.oauth_login_welcome_back')
                : __('messages.oauth_login_welcome_back_approval_needed');
            return redirect()->route('dashboard')
                ->with($calendarConnected ? 'success' : 'warning', $message);

        } catch (\Microsoft\Graph\Exception\GraphException $e) {
            Log::error('Microsoft OAuth login failed - GraphException', [
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', __('messages.oauth_microsoft_login_failed'));
        } catch (\Exception $e) {
            Log::error('Microsoft OAuth login failed', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('login')
                ->with('error', __('messages.oauth_microsoft_login_failed'));
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
            $primaryCalendarId = null;
            foreach ($calendarList->getItems() as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'name' => $calendar->getSummary(),
                    'primary' => $calendar->getPrimary() ?? false,
                    'access_role' => $calendar->getAccessRole(),
                ];
                
                // Remember the primary calendar ID
                if ($calendar->getPrimary()) {
                    $primaryCalendarId = $calendar->getId();
                }
            }
            
            // If no primary found, use first calendar
            if (!$primaryCalendarId && count($calendars) > 0) {
                $primaryCalendarId = $calendars[0]['id'];
            }

            // Create or update calendar connection
            $connection = CalendarConnection::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => 'google',
                    'provider_account_id' => $accountId,
                ],
                [
                    'name' => __('messages.google_calendar'),
                    'provider_email' => $email,
                    'available_calendars' => $calendars,
                    'selected_calendar_id' => $primaryCalendarId,
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
    private function connectMicrosoftCalendar(User $user, array $tokens, string $accountId, string $email, ?\Microsoft\Graph\Graph $graphInstance = null): void
    {
        try {
            // Detect account type for better error messages
            $accountType = $this->detectMicrosoftAccountType($email, $tokens);
            
            Log::info('Microsoft OAuth - Connecting calendar', [
                'user_id' => $user->id,
                'email' => $email,
                'account_type' => $accountType,
                'has_access_token' => isset($tokens['access_token']),
                'token_expires_in' => $tokens['expires_in'] ?? null,
                'has_graph_instance' => $graphInstance !== null,
            ]);
            
            // Ensure we have a valid access token
            if (empty($tokens['access_token'])) {
                throw new \Exception('Access token is empty');
            }
            
            $accessToken = is_array($tokens['access_token']) ? $tokens['access_token']['access_token'] ?? $tokens['access_token'] : $tokens['access_token'];
            
            Log::info('Microsoft OAuth - Preparing to call /me/calendars', [
                'token_length' => strlen($accessToken),
                'token_preview' => substr($accessToken, 0, 20) . '...',
                'account_type' => $accountType,
            ]);
            
            // Always create a fresh Graph instance for calendar call to avoid any state issues
            $calendarGraph = new \Microsoft\Graph\Graph();
            $calendarGraph->setAccessToken($accessToken);
            
            Log::info('Microsoft OAuth - Calling /me/calendars with fresh Graph instance');
            
            try {
                // Try using Graph API first
                $calendarList = $calendarGraph->createRequest('GET', '/me/calendars')
                    ->setReturnType(\Microsoft\Graph\Model\Calendar::class)
                    ->execute();
            } catch (\Microsoft\Graph\Exception\GraphException $e) {
                $errorCode = $e->getCode();
                $isAdminConsentIssue = ($errorCode === 401 && $accountType === 'work');
                
                Log::warning('Microsoft OAuth - Graph API /me/calendars failed', [
                    'error' => $e->getMessage(),
                    'code' => $errorCode,
                    'user_id' => $user->id,
                    'account_type' => $accountType,
                    'likely_admin_consent_issue' => $isAdminConsentIssue,
                ]);
                
                // If it's likely an admin consent issue for work account, handle gracefully
                if ($isAdminConsentIssue) {
                    Log::info('Microsoft OAuth - Work account 401 detected, creating pending connection');
                    
                    // Create connection record with pending status
                    CalendarConnection::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'provider' => 'microsoft',
                            'provider_account_id' => $accountId,
                        ],
                        [
                            'name' => __('messages.microsoft_calendar'),
                            'provider_email' => $email,
                            'status' => 'error',
                            'last_error' => 'Admin consent required for work account',
                            'available_calendars' => null,
                            'selected_calendar_id' => null,
                        ]
                    );
                    
                    // Throw to trigger the outer catch and set session flag
                    throw new \Exception('Admin consent required for work account', 401);
                }
                
                // For other errors, try direct HTTP call as fallback
                Log::info('Microsoft OAuth - Trying direct HTTP call as fallback');
                
                try {
                    $httpResponse = \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $accessToken,
                        'Accept' => 'application/json',
                    ])->get('https://graph.microsoft.com/v1.0/me/calendars');
                    
                    if ($httpResponse->successful()) {
                        Log::info('Microsoft OAuth - Direct HTTP call succeeded', [
                            'status' => $httpResponse->status(),
                            'calendar_count' => count($httpResponse->json('value', [])),
                        ]);
                        
                        // Convert HTTP response to Calendar objects
                        $calendarData = $httpResponse->json('value', []);
                        $calendarList = [];
                        foreach ($calendarData as $calData) {
                            $calendar = new \Microsoft\Graph\Model\Calendar();
                            $calendar->setProperties($calData);
                            $calendarList[] = $calendar;
                        }
                    } else {
                        Log::error('Microsoft OAuth - Direct HTTP call also failed', [
                            'status' => $httpResponse->status(),
                            'body' => $httpResponse->body(),
                            'account_type' => $accountType,
                        ]);
                        throw $e; // Re-throw original exception
                    }
                } catch (\Exception $httpException) {
                    Log::error('Microsoft OAuth - Direct HTTP call exception', [
                        'error' => $httpException->getMessage(),
                        'account_type' => $accountType,
                    ]);
                    throw $e; // Re-throw original GraphException
                }
            }
            
            Log::info('Microsoft OAuth - /me/calendars succeeded', [
                'calendar_count' => count($calendarList),
            ]);
                
            $calendars = [];
            $primaryCalendarId = null;
            foreach ($calendarList as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'name' => $calendar->getName(),
                    'primary' => $calendar->getIsDefaultCalendar() ?? false,
                    'access_role' => $calendar->getCanEdit() ? 'owner' : 'reader',
                ];
                
                // Remember the primary calendar ID
                if ($calendar->getIsDefaultCalendar()) {
                    $primaryCalendarId = $calendar->getId();
                }
            }
            
            // If no primary found, use first calendar
            if (!$primaryCalendarId && count($calendars) > 0) {
                $primaryCalendarId = $calendars[0]['id'];
            }

            // Create or update calendar connection
            $connection = CalendarConnection::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'provider' => 'microsoft',
                    'provider_account_id' => $accountId,
                ],
                [
                    'name' => __('messages.microsoft_calendar'),
                    'provider_email' => $email,
                    'available_calendars' => $calendars,
                    'selected_calendar_id' => $primaryCalendarId,
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
                'error_code' => $e->getCode(),
                'user_id' => $user->id,
                'email' => $email,
                'account_type' => $accountType ?? 'unknown',
                'is_admin_consent_issue' => ($e->getCode() === 401 && ($accountType ?? 'unknown') === 'work'),
            ]);
            // Re-throw to trigger session flag in handleMicrosoftCallback
            throw $e;
        }
    }

}

