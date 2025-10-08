<?php

namespace App\Http\Controllers\OAuth;

use App\Http\Controllers\Controller;
use App\Models\CalendarConnection;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle(GoogleCalendarService $service)
    {
        $state = Str::random(40);
        session(['oauth_state' => $state]);
        
        return redirect($service->getAuthUrl($state));
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request, GoogleCalendarService $service)
    {
        // Verify state
        if ($request->state !== session('oauth_state')) {
            return redirect()->route('dashboard')
                ->with('error', __('messages.oauth_state_mismatch'));
        }

        // Check if user denied access
        if ($request->has('error')) {
            Log::info('Google OAuth cancelled by user', [
                'error' => $request->error,
                'error_description' => $request->error_description,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('connections.index')
                ->with('warning', __('messages.oauth_cancelled'));
        }

        // Check if code is present
        if (!$request->has('code')) {
            Log::error('Google OAuth callback missing code', [
                'request' => $request->all(),
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('connections.index')
                ->with('error', __('messages.oauth_failed'));
        }

        try {
            // Exchange code for tokens
            $tokens = $service->handleCallback($request->code);
            
            // Temporarily set access token directly on the Google client
            $client = new \Google\Client();
            $client->setAccessToken($tokens);
            $calendarService = new \Google\Service\Calendar($client);
            
            // Get account info
            $primaryCalendar = $calendarService->calendars->get('primary');
            $accountInfo = [
                'id' => $primaryCalendar->getId(),
                'email' => $primaryCalendar->getId(),
            ];
            
            // Get available calendars
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

            // Try to update or create connection with retry logic for race conditions
            $maxAttempts = 3;
            $attempt = 0;
            $connection = null;
            
            while ($attempt < $maxAttempts && !$connection) {
                try {
                    // Find or create new connection (don't save yet)
                    $connection = CalendarConnection::firstOrNew([
                        'user_id' => auth()->id(),
                        'provider' => 'google',
                        'provider_account_id' => $accountInfo['id'],
                    ]);
                    
                    // Set all attributes
                    $connection->provider_email = $accountInfo['email'];
                    $connection->available_calendars = $calendars;
                    $connection->token_expires_at = now()->addSeconds($tokens['expires_in'] ?? 3600);
                    $connection->status = 'active';
                    $connection->last_error = null;
                    
                    // Set encrypted tokens BEFORE saving
                    $connection->setAccessToken($tokens['access_token']);
                    if (isset($tokens['refresh_token'])) {
                        $connection->setRefreshToken($tokens['refresh_token']);
                    }
                    
                    // Now save everything at once
                    $connection->save();
                    
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle duplicate key error (race condition)
                    if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                        Log::warning('Duplicate connection detected, attempting to update existing', [
                            'attempt' => $attempt + 1,
                            'user_id' => auth()->id(),
                            'provider' => 'google',
                        ]);
                        
                        // Clear any transaction state and try to load existing connection
                        \DB::rollBack();
                        $connection = null;
                        
                        // Wait a moment and try again with fresh query
                        usleep(100000); // 100ms
                        $attempt++;
                        
                        if ($attempt >= $maxAttempts) {
                            // Last attempt - force update using updateOrCreate
                            $connection = CalendarConnection::updateOrCreate(
                                [
                                    'user_id' => auth()->id(),
                                    'provider' => 'google',
                                    'provider_account_id' => $accountInfo['id'],
                                ],
                                [
                                    'provider_email' => $accountInfo['email'],
                                    'available_calendars' => $calendars,
                                    'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
                                    'status' => 'active',
                                    'last_error' => null,
                                ]
                            );
                            
                            // Set tokens after updateOrCreate
                            $connection->setAccessToken($tokens['access_token']);
                            if (isset($tokens['refresh_token'])) {
                                $connection->setRefreshToken($tokens['refresh_token']);
                            }
                            $connection->save();
                        }
                    } else {
                        // Different error - rethrow
                        throw $e;
                    }
                }
            }

            Log::info('Google calendar connected', [
                'user_id' => auth()->id(),
                'connection_id' => $connection->id,
            ]);

            return redirect()->route('connections.index')
                ->with('success', __('messages.calendar_connected'));

        } catch (\Exception $e) {
            Log::error('Google OAuth failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('connections.index')
                ->with('error', $this->getFriendlyErrorMessage($e, 'Google'));
        }
    }

    /**
     * Redirect to Microsoft OAuth
     */
    public function redirectToMicrosoft(MicrosoftCalendarService $service)
    {
        $state = Str::random(40);
        session(['oauth_state' => $state]);
        
        return redirect($service->getAuthUrl($state));
    }

    /**
     * Handle Microsoft OAuth callback
     */
    public function handleMicrosoftCallback(Request $request, MicrosoftCalendarService $service)
    {
        // Verify state
        if ($request->state !== session('oauth_state')) {
            return redirect()->route('dashboard')
                ->with('error', __('messages.oauth_state_mismatch'));
        }

        // Check if user denied access
        if ($request->has('error')) {
            Log::info('Microsoft OAuth cancelled by user', [
                'error' => $request->error,
                'error_description' => $request->error_description,
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('connections.index')
                ->with('warning', __('messages.oauth_cancelled'));
        }

        // Check if code is present
        if (!$request->has('code')) {
            Log::error('Microsoft OAuth callback missing code', [
                'request' => $request->all(),
                'user_id' => auth()->id(),
            ]);
            
            return redirect()->route('connections.index')
                ->with('error', __('messages.oauth_failed'));
        }

        try {
            // Exchange code for tokens
            $tokens = $service->handleCallback($request->code);
            
            Log::info('Microsoft OAuth - Token received', [
                'has_access_token' => isset($tokens['access_token']),
                'has_refresh_token' => isset($tokens['refresh_token']),
                'expires_in' => $tokens['expires_in'] ?? 'unknown',
                'scope' => $tokens['scope'] ?? 'not provided',
                'token_type' => $tokens['token_type'] ?? 'unknown',
            ]);
            
            // Temporarily use Graph API directly
            $graph = new \Microsoft\Graph\Graph();
            $graph->setAccessToken($tokens['access_token']);
            
            // Get account info
            Log::info('Microsoft OAuth - Calling /me...');
            $user = $graph->createRequest('GET', '/me')
                ->setReturnType(\Microsoft\Graph\Model\User::class)
                ->execute();
            $accountInfo = [
                'id' => $user->getId(),
                'email' => $user->getUserPrincipalName(),
            ];
            Log::info('Microsoft OAuth - /me succeeded', [
                'user_id' => $accountInfo['id'],
                'email' => $accountInfo['email'],
            ]);
            
            // Get available calendars
            Log::info('Microsoft OAuth - Calling /me/calendars...');
            $calendarList = $graph->createRequest('GET', '/me/calendars')
                ->setReturnType(\Microsoft\Graph\Model\Calendar::class)
                ->execute();
            Log::info('Microsoft OAuth - /me/calendars succeeded');
            $calendars = [];
            foreach ($calendarList as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'name' => $calendar->getName(),
                    'primary' => $calendar->getIsDefaultCalendar() ?? false,
                    'access_role' => $calendar->getCanEdit() ? 'owner' : 'reader',
                ];
            }

            // Try to update or create connection with retry logic for race conditions
            $maxAttempts = 3;
            $attempt = 0;
            $connection = null;
            
            while ($attempt < $maxAttempts && !$connection) {
                try {
                    // Find or create new connection (don't save yet)
                    $connection = CalendarConnection::firstOrNew([
                        'user_id' => auth()->id(),
                        'provider' => 'microsoft',
                        'provider_account_id' => $accountInfo['id'],
                    ]);
                    
                    // Set all attributes
                    $connection->provider_email = $accountInfo['email'];
                    $connection->available_calendars = $calendars;
                    $connection->token_expires_at = now()->addSeconds($tokens['expires_in'] ?? 3600);
                    $connection->status = 'active';
                    $connection->last_error = null;
                    
                    // Set encrypted tokens BEFORE saving
                    $connection->setAccessToken($tokens['access_token']);
                    if (isset($tokens['refresh_token'])) {
                        $connection->setRefreshToken($tokens['refresh_token']);
                    }
                    
                    // Now save everything at once
                    $connection->save();
                    
                } catch (\Illuminate\Database\QueryException $e) {
                    // Handle duplicate key error (race condition)
                    if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                        Log::warning('Duplicate connection detected, attempting to update existing', [
                            'attempt' => $attempt + 1,
                            'user_id' => auth()->id(),
                            'provider' => 'microsoft',
                        ]);
                        
                        // Clear any transaction state and try to load existing connection
                        \DB::rollBack();
                        $connection = null;
                        
                        // Wait a moment and try again with fresh query
                        usleep(100000); // 100ms
                        $attempt++;
                        
                        if ($attempt >= $maxAttempts) {
                            // Last attempt - force update using updateOrCreate
                            $connection = CalendarConnection::updateOrCreate(
                                [
                                    'user_id' => auth()->id(),
                                    'provider' => 'microsoft',
                                    'provider_account_id' => $accountInfo['id'],
                                ],
                                [
                                    'provider_email' => $accountInfo['email'],
                                    'available_calendars' => $calendars,
                                    'token_expires_at' => now()->addSeconds($tokens['expires_in'] ?? 3600),
                                    'status' => 'active',
                                    'last_error' => null,
                                ]
                            );
                            
                            // Set tokens after updateOrCreate
                            $connection->setAccessToken($tokens['access_token']);
                            if (isset($tokens['refresh_token'])) {
                                $connection->setRefreshToken($tokens['refresh_token']);
                            }
                            $connection->save();
                        }
                    } else {
                        // Different error - rethrow
                        throw $e;
                    }
                }
            }

            Log::info('Microsoft calendar connected', [
                'user_id' => auth()->id(),
                'connection_id' => $connection->id,
            ]);

            return redirect()->route('connections.index')
                ->with('success', __('messages.calendar_connected'));

        } catch (\Exception $e) {
            Log::error('Microsoft OAuth failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('connections.index')
                ->with('error', $this->getFriendlyErrorMessage($e, 'Microsoft'));
        }
    }

    /**
     * Convert technical errors into user-friendly messages
     */
    private function getFriendlyErrorMessage(\Exception $e, string $provider): string
    {
        $errorMessage = $e->getMessage();
        $statusCode = null;

        // Try to extract HTTP status code
        if (method_exists($e, 'getResponse') && $e->getResponse()) {
            $statusCode = $e->getResponse()->getStatusCode();
        } elseif (preg_match('/resulted in a `(\d+)/', $errorMessage, $matches)) {
            $statusCode = (int) $matches[1];
        }

        // Provide friendly messages based on error type
        switch ($statusCode) {
            case 401:
                return __('messages.calendar_connection_unauthorized', [
                    'provider' => $provider,
                    'hint' => __('messages.calendar_unauthorized_hint')
                ]);
            
            case 403:
                return __('messages.calendar_connection_forbidden', [
                    'provider' => $provider,
                    'hint' => __('messages.calendar_forbidden_hint')
                ]);
            
            case 404:
                return __('messages.calendar_connection_not_found', [
                    'provider' => $provider,
                ]);
            
            case 429:
                return __('messages.calendar_connection_rate_limit', [
                    'provider' => $provider,
                ]);
            
            case 500:
            case 502:
            case 503:
            case 504:
                return __('messages.calendar_connection_server_error', [
                    'provider' => $provider,
                ]);
        }

        // Check for specific error patterns
        if (str_contains($errorMessage, 'invalid_client')) {
            return __('messages.calendar_invalid_credentials', [
                'provider' => $provider,
            ]);
        }

        if (str_contains($errorMessage, 'invalid_grant')) {
            return __('messages.calendar_invalid_grant', [
                'provider' => $provider,
            ]);
        }

        if (str_contains($errorMessage, 'access_denied')) {
            return __('messages.calendar_access_denied', [
                'provider' => $provider,
            ]);
        }

        // Network/connection errors
        if (str_contains($errorMessage, 'cURL error') || str_contains($errorMessage, 'Connection')) {
            return __('messages.calendar_connection_network_error', [
                'provider' => $provider,
            ]);
        }

        // Default friendly message
        return __('messages.calendar_connection_generic_error', [
            'provider' => $provider,
        ]);
    }
}

