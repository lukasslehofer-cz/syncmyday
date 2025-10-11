<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
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

    public function __construct()
    {
        $this->client = new GoogleClient();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect'));
        $this->client->setScopes(config('services.google.scopes'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent'); // Always show account picker + consent to get refresh token
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
    }

    /**
     * Delete a blocker event
     */
    public function deleteBlocker(string $calendarId, string $eventId): void
    {
        $this->service->events->delete($calendarId, $eventId);
        
        Log::channel('sync')->info('Google blocker deleted', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
        ]);
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
    }
}

