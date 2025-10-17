<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use App\Helpers\CacheHelper;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Channel;
use Google\Service\Calendar\Event;
use Illuminate\Support\Facades\Log;

/**
 * Google Calendar Service
 * 
 * Handles all interactions with Google Calendar API:
 * - OAuth token management
 * - Calendar operations (list, get events)
 * - Event CRUD operations
 * - Webhook subscriptions (watch channels)
 */
class GoogleCalendarService
{
    private GoogleClient $client;
    private ?GoogleCalendar $service = null;
    private ?CalendarConnection $currentConnection = null;

    // Google API rate limits: 1,000,000 queries/day, ~1000 queries/100 seconds per user
    private const RATE_LIMIT_MAX = 100; // Max requests per minute per connection
    private const RATE_LIMIT_DECAY = 1; // 1 minute window

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        
        // Use current domain for redirect URI (multi-domain support)
        $configRedirect = config('services.google.redirect');
        $this->client->setRedirectUri($this->replaceWithCurrentDomain($configRedirect));
        
        $this->client->setScopes(config('services.google.scopes'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent'); // Always show account picker + consent to get refresh token
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
     * Execute API call with rate limiting
     */
    private function rateLimitedCall(string $operation, \Closure $callback)
    {
        if ($this->currentConnection) {
            $identifier = "google_api:{$this->currentConnection->id}";
            
            // Check rate limit
            if (!CacheHelper::checkRateLimit($identifier, self::RATE_LIMIT_MAX, self::RATE_LIMIT_DECAY)) {
                Log::channel('sync')->warning('Google API rate limit exceeded', [
                    'connection_id' => $this->currentConnection->id,
                    'operation' => $operation,
                ]);
                
                // Wait a bit and retry once
                sleep(1);
                
                if (!CacheHelper::checkRateLimit($identifier, self::RATE_LIMIT_MAX, self::RATE_LIMIT_DECAY)) {
                    throw new \Exception("Google API rate limit exceeded for connection {$this->currentConnection->id}");
                }
            }
        }
        
        try {
            return $callback();
        } catch (\Google\Service\Exception $e) {
            // Handle rate limit errors from Google
            if ($e->getCode() === 429) {
                Log::channel('sync')->error('Google API rate limit hit (429)', [
                    'connection_id' => $this->currentConnection?->id,
                    'operation' => $operation,
                ]);
                
                // Back off and retry after delay
                sleep(5);
                return $callback();
            }
            
            throw $e;
        }
    }

    /**
     * Get OAuth authorization URL
     */
    public function getAuthUrl(string $state): string
    {
        $this->client->setState($state);
        return $this->client->createAuthUrl();
    }

    /**
     * Exchange authorization code for tokens
     */
    public function handleCallback(string $code): array
    {
        $token = $this->client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            throw new \Exception('OAuth error: ' . $token['error']);
        }

        return $token;
    }

    /**
     * Initialize service with connection tokens
     */
    public function initializeWithConnection(CalendarConnection $connection): void
    {
        $this->currentConnection = $connection;
        
        $this->client->setAccessToken([
            'access_token' => $connection->getAccessToken(),
            'refresh_token' => $connection->getRefreshToken(),
            'expires_in' => $connection->token_expires_at?->diffInSeconds(now()),
        ]);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired() && $connection->getRefreshToken()) {
            $newToken = $this->client->fetchAccessTokenWithRefreshToken($connection->getRefreshToken());
            
            if (isset($newToken['error'])) {
                $connection->update(['status' => 'expired', 'last_error' => $newToken['error']]);
                throw new \Exception('Token refresh failed: ' . $newToken['error']);
            }

            // Update connection with new token
            $connection->setAccessToken($newToken['access_token']);
            $connection->token_expires_at = now()->addSeconds($newToken['expires_in']);
            $connection->save();
        }

        $this->service = new GoogleCalendar($this->client);
    }

    /**
     * Get user's calendar list
     */
    public function getCalendarList(): array
    {
        return $this->rateLimitedCall('getCalendarList', function () {
            $calendarList = $this->service->calendarList->listCalendarList();
            $calendars = [];

            foreach ($calendarList->getItems() as $calendar) {
                $calendars[] = [
                    'id' => $calendar->getId(),
                    'name' => $calendar->getSummary(),
                    'primary' => $calendar->getPrimary() ?? false,
                    'access_role' => $calendar->getAccessRole(),
                ];
            }

            return $calendars;
        });
    }

    /**
     * Get account information
     */
    public function getAccountInfo(): array
    {
        $calendar = $this->service->calendars->get('primary');
        return [
            'id' => $calendar->getId(),
            'email' => $calendar->getId(), // For Google, calendar ID is usually the email
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
        return $this->rateLimitedCall('createBlocker', function () use ($calendarId, $title, $start, $end, $transactionId) {
            $event = new Event([
                'summary' => $title,
                'description' => 'Auto-synced by SyncMyDay',
                'start' => ['dateTime' => $start->format(\DateTime::RFC3339)],
                'end' => ['dateTime' => $end->format(\DateTime::RFC3339)],
                'transparency' => 'opaque', // Shows as busy
                'visibility' => 'private',
                'extendedProperties' => [
                    'private' => [
                        'syncmyday' => 'true',
                        'transaction_id' => $transactionId,
                    ],
                ],
            ]);

            $createdEvent = $this->service->events->insert($calendarId, $event);
            
            Log::channel('sync')->info('Google blocker created', [
                'calendar_id' => $calendarId,
                'event_id' => $createdEvent->getId(),
                'transaction_id' => $transactionId,
            ]);

            return $createdEvent->getId();
        });
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
        $this->rateLimitedCall('updateBlocker', function () use ($calendarId, $eventId, $title, $start, $end, $transactionId) {
            $event = $this->service->events->get($calendarId, $eventId);
            
            // Update event details
            $event->setSummary($title);
            $event->setStart(new \Google_Service_Calendar_EventDateTime([
                'dateTime' => $start->format(\DateTime::RFC3339),
                'timeZone' => 'UTC',
            ]));
            $event->setEnd(new \Google_Service_Calendar_EventDateTime([
                'dateTime' => $end->format(\DateTime::RFC3339),
                'timeZone' => 'UTC',
            ]));

            $this->service->events->update($calendarId, $eventId, $event);
            
            Log::channel('sync')->info('Google blocker updated', [
                'calendar_id' => $calendarId,
                'event_id' => $eventId,
                'transaction_id' => $transactionId,
            ]);
        });
    }

    /**
     * Delete a blocker event
     */
    public function deleteBlocker(string $calendarId, string $eventId): void
    {
        Log::channel('sync')->info('Attempting to delete Google blocker', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
        ]);
        
        $this->rateLimitedCall('deleteBlocker', function () use ($calendarId, $eventId) {
            try {
                $this->service->events->delete($calendarId, $eventId);
                
                Log::channel('sync')->info('Google blocker deleted successfully', [
                    'calendar_id' => $calendarId,
                    'event_id' => $eventId,
                ]);
            } catch (\Exception $e) {
                Log::channel('sync')->error('Failed to delete Google blocker', [
                    'calendar_id' => $calendarId,
                    'event_id' => $eventId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                throw $e;
            }
        });
    }

    /**
     * Check if event is a SyncMyDay blocker
     */
    public function isOurBlocker($event): bool
    {
        $props = $event->getExtendedProperties();
        if (!$props) {
            return false;
        }
        
        $private = $props->getPrivate();
        return isset($private['syncmyday']) && $private['syncmyday'] === 'true';
    }

    /**
     * Create a webhook subscription (watch channel)
     */
    public function createWebhook(string $calendarId, string $webhookUrl): array
    {
        $channel = new Channel([
            'id' => \Str::uuid()->toString(),
            'type' => 'web_hook',
            'address' => $webhookUrl,
        ]);

        $watchResponse = $this->service->events->watch($calendarId, $channel);

        return [
            'subscription_id' => $watchResponse->getId(),
            'resource_id' => $watchResponse->getResourceId(),
            'expires_at' => \Carbon\Carbon::createFromTimestampMs($watchResponse->getExpiration()),
        ];
    }

    /**
     * Stop a webhook subscription
     */
    public function stopWebhook(string $subscriptionId, string $resourceId): void
    {
        $channel = new Channel();
        $channel->setId($subscriptionId);
        $channel->setResourceId($resourceId);

        $this->service->channels->stop($channel);
    }

    /**
     * Get events changed since last sync
     */
    public function getChangedEvents(string $calendarId, ?string $syncToken = null): array
    {
        return $this->rateLimitedCall('getChangedEvents', function () use ($calendarId, $syncToken) {
        $optParams = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
        ];

        if ($syncToken) {
            // Incremental sync: use sync token (gets only changes)
            $optParams['syncToken'] = $syncToken;
        } else {
            // Full sync: apply time range filters
            $pastDays = config('sync.time_range.past_days', 7);
            $futureMonths = config('sync.time_range.future_months', 6);
            
            $optParams['timeMin'] = now()->subDays($pastDays)->toRfc3339String();
            $optParams['timeMax'] = now()->addMonths($futureMonths)->toRfc3339String();
            
            Log::channel('sync')->debug('Google full sync with time range', [
                'calendar_id' => $calendarId,
                'time_min' => $optParams['timeMin'],
                'time_max' => $optParams['timeMax'],
            ]);
        }

        try {
            $events = $this->service->events->listEvents($calendarId, $optParams);
            
            return [
                'events' => iterator_to_array($events->getItems()),
                'sync_token' => $events->getNextSyncToken(),
            ];
        } catch (\Google\Service\Exception $e) {
            // If sync token is invalid, do a full sync
            if ($e->getCode() === 410) {
                Log::channel('sync')->warning('Google sync token expired, doing full sync', [
                    'calendar_id' => $calendarId,
                ]);
                return $this->getChangedEvents($calendarId, null);
            }
            throw $e;
        }
        });
    }
}

