<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;

/**
 * Microsoft Calendar Service
 * 
 * Handles all interactions with Microsoft Graph API:
 * - OAuth token management
 * - Calendar operations (list, get events)
 * - Event CRUD operations  
 * - Webhook subscriptions (change notifications)
 */
class MicrosoftCalendarService
{
    private string $clientId;
    private string $clientSecret;
    private string $redirectUri;
    private string $tenant;
    private ?Graph $graph = null;

    public function __construct()
    {
        $this->clientId = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');
        
        // Use current domain for redirect URI (multi-domain support)
        $configRedirect = config('services.microsoft.redirect');
        $this->redirectUri = $this->replaceWithCurrentDomain($configRedirect);
        
        $this->tenant = config('services.microsoft.tenant');
    }
    
    /**
     * Replace APP_URL in redirect URI with current domain
     */
    private function replaceWithCurrentDomain(string $uri): string
    {
        // Only replace in web context, not CLI
        if (app()->runningInConsole()) {
            return $uri;
        }
        
        $appUrl = rtrim(config('app.url'), '/');
        $currentUrl = rtrim(url('/'), '/');
        
        return str_replace($appUrl, $currentUrl, $uri);
    }

    /**
     * Get OAuth authorization URL
     */
    public function getAuthUrl(string $state): string
    {
        $scopes = implode(' ', config('services.microsoft.scopes'));
        
        return sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/authorize?client_id=%s&response_type=code&redirect_uri=%s&response_mode=query&scope=%s&state=%s&prompt=select_account',
            $this->tenant,
            $this->clientId,
            urlencode($this->redirectUri),
            urlencode($scopes),
            $state
        );
    }

    /**
     * Exchange authorization code for tokens
     */
    public function handleCallback(string $code): array
    {
        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token",
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'grant_type' => 'authorization_code',
                'scope' => implode(' ', config('services.microsoft.scopes')),
            ]
        );

        if (!$response->successful()) {
            throw new \Exception('OAuth error: ' . $response->body());
        }

        return $response->json();
    }

    /**
     * Initialize service with connection tokens
     */
    public function initializeWithConnection(CalendarConnection $connection): void
    {
        $accessToken = $connection->getAccessToken();

        // Check if token is expired and refresh if needed
        if ($connection->isTokenExpired() && $connection->getRefreshToken()) {
            $accessToken = $this->refreshAccessToken($connection);
        }

        $this->graph = new Graph();
        $this->graph->setAccessToken($accessToken);
    }

    /**
     * Refresh access token
     */
    private function refreshAccessToken(CalendarConnection $connection): string
    {
        $response = Http::asForm()->post(
            "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token",
            [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'refresh_token' => $connection->getRefreshToken(),
                'grant_type' => 'refresh_token',
                'scope' => implode(' ', config('services.microsoft.scopes')),
            ]
        );

        if (!$response->successful()) {
            $connection->update(['status' => 'expired', 'last_error' => $response->body()]);
            throw new \Exception('Token refresh failed: ' . $response->body());
        }

        $token = $response->json();
        
        // Update connection
        $connection->setAccessToken($token['access_token']);
        if (isset($token['refresh_token'])) {
            $connection->setRefreshToken($token['refresh_token']);
        }
        $connection->token_expires_at = now()->addSeconds($token['expires_in']);
        $connection->save();

        return $token['access_token'];
    }

    /**
     * Get user's calendar list
     */
    public function getCalendarList(): array
    {
        $calendars = $this->graph->createRequest('GET', '/me/calendars')
            ->setReturnType(Model\Calendar::class)
            ->execute();

        $result = [];
        foreach ($calendars as $calendar) {
            $result[] = [
                'id' => $calendar->getId(),
                'name' => $calendar->getName(),
                'primary' => $calendar->getIsDefaultCalendar() ?? false,
                'access_role' => $calendar->getCanEdit() ? 'owner' : 'reader',
            ];
        }

        return $result;
    }

    /**
     * Get account information
     */
    public function getAccountInfo(): array
    {
        $user = $this->graph->createRequest('GET', '/me')
            ->setReturnType(Model\User::class)
            ->execute();

        return [
            'id' => $user->getId(),
            'email' => $user->getUserPrincipalName(),
        ];
    }

    /**
     * Create a busy blocker event
     */
    public function createBlocker(
        string $calendarId,
        string $title,
        \DateTime $start,
        \DateTime $end,
        string $transactionId
    ): string {
        $event = [
            'subject' => $title,
            'body' => [
                'contentType' => 'text',
                'content' => 'Auto-synced by SyncMyDay',
            ],
            'start' => [
                'dateTime' => $start->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $end->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC',
            ],
            'showAs' => 'busy',
            'sensitivity' => 'private',
            'isReminderOn' => false,
            'categories' => ['SyncMyDay'],
            'transactionId' => $transactionId,
        ];

        $response = $this->graph->createRequest('POST', "/me/calendars/{$calendarId}/events")
            ->attachBody($event)
            ->setReturnType(Model\Event::class)
            ->execute();

        Log::channel('sync')->info('Microsoft blocker created', [
            'calendar_id' => $calendarId,
            'event_id' => $response->getId(),
            'transaction_id' => $transactionId,
        ]);

        return $response->getId();
    }

    /**
     * Update a blocker event
     */
    public function updateBlocker(
        string $calendarId,
        string $eventId,
        string $title,
        \DateTime $start,
        \DateTime $end,
        string $transactionId
    ): void {
        $update = [
            'subject' => $title,
            'start' => [
                'dateTime' => $start->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC',
            ],
            'end' => [
                'dateTime' => $end->format('Y-m-d\TH:i:s'),
                'timeZone' => 'UTC',
            ],
        ];

        $this->graph->createRequest('PATCH', "/me/calendars/{$calendarId}/events/{$eventId}")
            ->attachBody($update)
            ->execute();

        Log::channel('sync')->info('Microsoft blocker updated', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
            'transaction_id' => $transactionId,
        ]);
    }

    /**
     * Delete a blocker event
     */
    public function deleteBlocker(string $calendarId, string $eventId): void
    {
        Log::channel('sync')->info('Attempting to delete Microsoft blocker', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
        ]);
        
        try {
            $this->graph->createRequest('DELETE', "/me/calendars/{$calendarId}/events/{$eventId}")
                ->execute();

            Log::channel('sync')->info('Microsoft blocker deleted successfully', [
                'calendar_id' => $calendarId,
                'event_id' => $eventId,
            ]);
        } catch (\Exception $e) {
            Log::channel('sync')->error('Failed to delete Microsoft blocker', [
                'calendar_id' => $calendarId,
                'event_id' => $eventId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Check if event is a SyncMyDay blocker
     */
    public function isOurBlocker($event): bool
    {
        // Handle both array and object responses
        if (is_array($event)) {
            $categories = $event['categories'] ?? [];
        } else {
            $categories = $event->getCategories() ?? [];
        }
        
        return in_array('SyncMyDay', $categories);
    }

    /**
     * Create a webhook subscription
     */
    public function createWebhook(string $calendarId, string $webhookUrl): array
    {
        $subscription = [
            'changeType' => 'created,updated,deleted',
            'notificationUrl' => $webhookUrl,
            'resource' => "/me/calendars/{$calendarId}/events",
            'expirationDateTime' => now()->addDays(3)->toIso8601String(), // Max 3 days for calendar events
            'clientState' => \Str::random(32), // Random secret for validation
        ];

        $response = $this->graph->createRequest('POST', '/subscriptions')
            ->attachBody($subscription)
            ->execute();
        
        // Convert GraphResponse to array
        $data = $response->getBody();

        return [
            'subscription_id' => $data['id'],
            'resource_id' => $data['clientState'],
            'expires_at' => \Carbon\Carbon::parse($data['expirationDateTime']),
        ];
    }

    /**
     * Renew a webhook subscription
     */
    public function renewWebhook(string $subscriptionId): \DateTime
    {
        $update = [
            'expirationDateTime' => now()->addDays(3)->toIso8601String(),
        ];

        $response = $this->graph->createRequest('PATCH', "/subscriptions/{$subscriptionId}")
            ->attachBody($update)
            ->execute();
        
        // Convert GraphResponse to array
        $data = $response->getBody();

        return \Carbon\Carbon::parse($data['expirationDateTime']);
    }

    /**
     * Stop a webhook subscription
     */
    public function stopWebhook(string $subscriptionId): void
    {
        $this->graph->createRequest('DELETE', "/subscriptions/{$subscriptionId}")
            ->execute();
    }

    /**
     * Get events changed since last sync using delta query
     */
    public function getChangedEvents(string $calendarId, ?string $deltaLink = null): array
    {
        if ($deltaLink) {
            // Incremental sync: use delta link (gets only changes)
            $request = $this->graph->createRequest('GET', $deltaLink);
        } else {
            // Full sync: use calendarView with time range filters
            $pastDays = config('sync.time_range.past_days', 7);
            $futureMonths = config('sync.time_range.future_months', 6);
            
            $startDateTime = now()->subDays($pastDays)->format('Y-m-d\TH:i:s');
            $endDateTime = now()->addMonths($futureMonths)->format('Y-m-d\TH:i:s');
            
            Log::channel('sync')->debug('Microsoft full sync with time range', [
                'calendar_id' => $calendarId,
                'start_date_time' => $startDateTime,
                'end_date_time' => $endDateTime,
            ]);
            
            // Use calendarView for initial sync with time filters
            // Then switch to delta for incremental updates
            $url = "/me/calendars/{$calendarId}/calendarView"
                . "?startDateTime={$startDateTime}"
                . "&endDateTime={$endDateTime}";
            
            // Note: $top is not supported with calendarView + change tracking
            // Use Prefer header instead
            $request = $this->graph->createRequest('GET', $url)
                ->addHeaders([
                    'Prefer' => 'odata.track-changes, odata.maxpagesize=50'
                ]);
        }

        $response = $request->execute();
        
        // Convert GraphResponse to array
        $data = $response->getBody();

        return [
            'events' => $data['value'] ?? [],
            'delta_link' => $data['@odata.deltaLink'] ?? null,
            'next_link' => $data['@odata.nextLink'] ?? null,
        ];
    }
}

