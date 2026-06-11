<?php

namespace App\Services\Calendar;

use App\Models\CalendarConnection;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Microsoft\Graph\Exception\GraphException;
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
    private ?string $clientId;

    private ?string $clientSecret;

    private string $redirectUri;

    private string $tenant;

    private ?Graph $graph = null;

    private ?string $userTimezone = null;

    public function __construct()
    {
        $this->clientId = config('services.microsoft.client_id');
        $this->clientSecret = config('services.microsoft.client_secret');

        // Use current domain for redirect URI (multi-domain support)
        $configRedirect = config('services.microsoft.redirect');
        $this->redirectUri = $this->replaceWithCurrentDomain($configRedirect);

        $this->tenant = config('services.microsoft.tenant', 'common');

        // Validate required config
        if (empty($this->clientId) || empty($this->clientSecret)) {
            Log::warning('Microsoft Calendar Service: Missing client_id or client_secret in config');
        }
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

        if (! $response->successful()) {
            throw new \Exception('OAuth error: '.$response->body());
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

        $this->graph = new Graph;
        $this->graph->setAccessToken($accessToken);

        // Store user timezone for later use in createBlocker/updateBlocker
        if ($connection->user) {
            $this->userTimezone = $connection->user->timezone ?? 'UTC';
        } else {
            $this->userTimezone = 'UTC';
        }
    }

    /**
     * Refresh access token
     */
    private function refreshAccessToken(CalendarConnection $connection): string
    {
        $response = Http::asForm()
            ->retry(3, 1000, function ($exception) {
                if ($exception instanceof \Illuminate\Http\Client\ConnectionException) {
                    return true;
                }
                if ($exception instanceof \Illuminate\Http\Client\RequestException) {
                    $status = $exception->response->status();

                    return $status === 429 || ($status >= 500 && $status < 600);
                }

                return false;
            }, throw: false)
            ->post(
                "https://login.microsoftonline.com/{$this->tenant}/oauth2/v2.0/token",
                [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $connection->getRefreshToken(),
                    'grant_type' => 'refresh_token',
                    'scope' => implode(' ', config('services.microsoft.scopes')),
                ]
            );

        if (! $response->successful()) {
            // Distinguish permanent auth failures (invalid_grant: revoked consent, password
            // change, Conditional Access block / AADSTS53003) from transient ones. Permanent
            // failures mark the connection 'expired' so the user is prompted to reconnect;
            // transient ones use 'error' and keep being retried on the next sync.
            $error = $response->json('error');
            $status = $error === 'invalid_grant' ? 'expired' : 'error';
            $connection->update(['status' => $status, 'last_error' => $response->body()]);
            throw new \Exception('Token refresh failed: '.$response->body());
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
     * Inject a Graph client (used in tests to substitute a mock).
     */
    public function setGraphClient(Graph $graph): void
    {
        $this->graph = $graph;
    }

    /**
     * Execute a Graph SDK call with retry on transient errors (5xx, 429, network timeouts).
     * Respects Retry-After header when present. After max attempts, re-throws the last error.
     */
    private function executeWithRetry(callable $fn, string $operation, array $context = []): mixed
    {
        $maxAttempts = 4;
        $baseDelayMs = 1000;

        $attempt = 0;
        while (true) {
            $attempt++;
            try {
                return $fn();
            } catch (\Throwable $e) {
                $isRetryable = $this->isRetryableGraphError($e);
                $isLastAttempt = $attempt >= $maxAttempts;

                if (! $isRetryable || $isLastAttempt) {
                    throw $e;
                }

                $delayMs = $this->computeBackoffMs($e, $attempt, $baseDelayMs);

                Log::channel('sync')->warning('Microsoft Graph transient error - retrying', array_merge($context, [
                    'operation' => $operation,
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'delay_ms' => $delayMs,
                    'status_code' => $this->extractStatusCode($e),
                    'error' => $e->getMessage(),
                ]));

                usleep($delayMs * 1000);
            }
        }
    }

    private function isRetryableGraphError(\Throwable $e): bool
    {
        if ($e instanceof ConnectException) {
            return true;
        }

        $status = $this->extractStatusCode($e);
        if ($status === null) {
            return false;
        }

        return $status === 429 || ($status >= 500 && $status < 600);
    }

    private function extractStatusCode(\Throwable $e): ?int
    {
        if ($e instanceof RequestException && $e->hasResponse()) {
            return $e->getResponse()->getStatusCode();
        }

        // Graph SDK wraps Guzzle BadResponseException; status is preserved via getCode()
        if ($e instanceof GraphException) {
            $code = $e->getCode();
            if ($code >= 100 && $code < 600) {
                return $code;
            }
        }

        $previous = $e->getPrevious();
        if ($previous instanceof RequestException && $previous->hasResponse()) {
            return $previous->getResponse()->getStatusCode();
        }

        return null;
    }

    private function computeBackoffMs(\Throwable $e, int $attempt, int $baseMs): int
    {
        $response = null;
        if ($e instanceof RequestException && $e->hasResponse()) {
            $response = $e->getResponse();
        } else {
            $previous = $e->getPrevious();
            if ($previous instanceof RequestException && $previous->hasResponse()) {
                $response = $previous->getResponse();
            }
        }

        if ($response !== null) {
            $retryAfter = $response->getHeaderLine('Retry-After');
            if ($retryAfter !== '') {
                $seconds = is_numeric($retryAfter)
                    ? (int) $retryAfter
                    : max(0, strtotime($retryAfter) - time());

                return min(60000, max(1000, $seconds * 1000));
            }
        }

        $exp = min(8000, $baseMs * (2 ** ($attempt - 1)));
        $jitter = (int) ($exp * 0.2 * (mt_rand(-100, 100) / 100));

        return max(100, $exp + $jitter);
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
        // Use stored user timezone (set during initializeWithConnection)
        $userTimezone = $this->userTimezone ?? 'UTC';

        // Convert times to user's timezone
        $startConverted = clone $start;
        $startConverted->setTimezone(new \DateTimeZone($userTimezone));

        $endConverted = clone $end;
        $endConverted->setTimezone(new \DateTimeZone($userTimezone));

        $event = [
            'subject' => $title,
            'body' => [
                'contentType' => 'text',
                'content' => 'Auto-synced by SyncMyDay',
            ],
            'start' => [
                'dateTime' => $startConverted->format('Y-m-d\TH:i:s'),
                'timeZone' => $userTimezone,
            ],
            'end' => [
                'dateTime' => $endConverted->format('Y-m-d\TH:i:s'),
                'timeZone' => $userTimezone,
            ],
            'showAs' => 'busy',
            'sensitivity' => 'private',
            'isReminderOn' => false,
            'categories' => ['SyncMyDay'],
            'transactionId' => $transactionId,
        ];

        $response = $this->executeWithRetry(
            fn () => $this->graph->createRequest('POST', "/me/calendars/{$calendarId}/events")
                ->attachBody($event)
                ->setReturnType(Model\Event::class)
                ->execute(),
            'createBlocker',
            ['calendar_id' => $calendarId, 'transaction_id' => $transactionId]
        );

        Log::channel('sync')->info('Microsoft blocker created', [
            'calendar_id' => $calendarId,
            'event_id' => $response->getId(),
            'transaction_id' => $transactionId,
            'timezone' => $userTimezone,
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
        // Use stored user timezone (set during initializeWithConnection)
        $userTimezone = $this->userTimezone ?? 'UTC';

        // Convert times to user's timezone
        $startConverted = clone $start;
        $startConverted->setTimezone(new \DateTimeZone($userTimezone));

        $endConverted = clone $end;
        $endConverted->setTimezone(new \DateTimeZone($userTimezone));

        $update = [
            'subject' => $title,
            'start' => [
                'dateTime' => $startConverted->format('Y-m-d\TH:i:s'),
                'timeZone' => $userTimezone,
            ],
            'end' => [
                'dateTime' => $endConverted->format('Y-m-d\TH:i:s'),
                'timeZone' => $userTimezone,
            ],
        ];

        $this->executeWithRetry(
            fn () => $this->graph->createRequest('PATCH', "/me/calendars/{$calendarId}/events/{$eventId}")
                ->attachBody($update)
                ->execute(),
            'updateBlocker',
            ['calendar_id' => $calendarId, 'event_id' => $eventId, 'transaction_id' => $transactionId]
        );

        Log::channel('sync')->info('Microsoft blocker updated', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
            'transaction_id' => $transactionId,
            'timezone' => $userTimezone,
        ]);
    }

    /**
     * Delete a blocker event
     */
    public function deleteBlocker(string $calendarId, string $eventId): void
    {
        Log::channel('sync')->debug('Attempting to delete Microsoft blocker', [
            'calendar_id' => $calendarId,
            'event_id' => $eventId,
        ]);

        try {
            $this->executeWithRetry(
                fn () => $this->graph->createRequest('DELETE', "/me/calendars/{$calendarId}/events/{$eventId}")
                    ->execute(),
                'deleteBlocker',
                ['calendar_id' => $calendarId, 'event_id' => $eventId]
            );

            Log::channel('sync')->debug('Microsoft blocker deleted successfully', [
                'calendar_id' => $calendarId,
                'event_id' => $eventId,
            ]);

        } catch (\Microsoft\Graph\Exception\GraphException $e) {
            $errorCode = $e->getCode();

            // 404 = not found / already deleted (OK)
            if ($errorCode === 404) {
                Log::channel('sync')->debug('Microsoft blocker already deleted', [
                    'calendar_id' => $calendarId,
                    'event_id' => $eventId,
                ]);

                return;
            }

            // 429 = rate limit / throttling
            if ($errorCode === 429) {
                Log::channel('sync')->warning('Microsoft rate limit hit during blocker cleanup - skipping', [
                    'calendar_id' => $calendarId,
                    'event_id' => $eventId,
                    'note' => 'Event will be cleaned up on next sync or manually',
                ]);

                return;
            }

            // Other errors - log but don't throw (allow rule deletion to continue)
            Log::channel('sync')->warning('Failed to delete Microsoft blocker - continuing anyway', [
                'calendar_id' => $calendarId,
                'event_id' => $eventId,
                'error_code' => $errorCode,
                'error' => $e->getMessage(),
            ]);

        } catch (\Exception $e) {
            // Unexpected error - log but don't throw
            Log::channel('sync')->warning('Unexpected error deleting Microsoft blocker', [
                'calendar_id' => $calendarId,
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
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
     *
     * CRITICAL: Microsoft Graph API endpoints:
     * - /calendarView - supports time filtering BUT does NOT support delta tracking
     * - /events/delta - supports delta tracking BUT does NOT support time filtering
     *
     * Solution: Use /events/delta ALWAYS (returns all events, we filter by time in SyncEngine)
     * This enables proper delta tracking and eliminates full syncs.
     */
    public function getChangedEvents(string $calendarId, ?string $deltaLink = null): array
    {
        $allEvents = [];
        $pageCount = 0;
        $finalDeltaLink = null;

        // Determine initial URL
        if ($deltaLink) {
            // Incremental sync: use delta link (gets only changes)
            $url = $deltaLink;
            Log::channel('sync')->info('Microsoft incremental sync using delta link', [
                'calendar_id' => $calendarId,
            ]);
        } else {
            // Initial sync: use /events/delta endpoint (NOT calendarView)
            // Note: This returns ALL events in calendar (no time filtering)
            // Time filtering is applied in SyncEngine::syncRule()
            $url = "/me/calendars/{$calendarId}/events/delta";

            Log::channel('sync')->info('Microsoft initial sync using delta endpoint', [
                'calendar_id' => $calendarId,
                'note' => 'Fetching all events, time filtering applied in SyncEngine',
            ]);
        }

        // Paginate through ALL pages to get delta link
        do {
            $pageCount++;

            // Build request
            if (strpos($url, 'http') === 0) {
                // Full URL from nextLink or deltaLink - use as-is
                $request = $this->graph->createRequest('GET', $url);
            } else {
                // Relative URL - add Prefer header for initial delta request
                $request = $this->graph->createRequest('GET', $url);

                // CRITICAL: Delta query requires Prefer header, NOT $top parameter
                // Microsoft error: "$top parameter is not supported with change tracking"
                if (! $deltaLink) {
                    $request->addHeaders([
                        'Prefer' => 'odata.maxpagesize=50',
                    ]);
                }
            }

            $response = $this->executeWithRetry(
                fn () => $request->execute(),
                'getChangedEvents',
                ['calendar_id' => $calendarId, 'page' => $pageCount]
            );
            $data = $response->getBody();

            // Collect events from this page
            $pageEvents = $data['value'] ?? [];
            $allEvents = array_merge($allEvents, $pageEvents);

            Log::channel('sync')->debug('Microsoft delta pagination', [
                'calendar_id' => $calendarId,
                'page' => $pageCount,
                'events_in_page' => count($pageEvents),
                'total_events_so_far' => count($allEvents),
                'has_next_link' => isset($data['@odata.nextLink']),
                'has_delta_link' => isset($data['@odata.deltaLink']),
            ]);

            // Check for next page or delta link
            if (isset($data['@odata.deltaLink'])) {
                // Last page - we got the delta link!
                $finalDeltaLink = $data['@odata.deltaLink'];
                $url = null; // Stop pagination

                Log::channel('sync')->info('Microsoft delta link received', [
                    'calendar_id' => $calendarId,
                    'pages_fetched' => $pageCount,
                ]);
            } elseif (isset($data['@odata.nextLink'])) {
                // More pages - continue
                $url = $data['@odata.nextLink'];
            } else {
                // No more pages and no delta link
                // This happens when delta link is EXPIRED or INVALID
                // Return null to force full sync on next run
                $url = null;
                $finalDeltaLink = null; // CRITICAL: Force reset

                Log::channel('sync')->warning('Microsoft delta link expired/invalid - will reset and do full sync next time', [
                    'calendar_id' => $calendarId,
                    'pages_fetched' => $pageCount,
                    'total_events' => count($allEvents),
                    'last_response_keys' => array_keys($data),
                    'action' => 'Resetting delta link to force fresh sync',
                ]);
            }

        } while ($url !== null);

        Log::channel('sync')->info('Microsoft delta sync completed', [
            'calendar_id' => $calendarId,
            'total_pages' => $pageCount,
            'total_events' => count($allEvents),
            'delta_link_received' => $finalDeltaLink !== null,
            'sync_type' => $deltaLink ? 'incremental' : 'initial',
        ]);

        return [
            'events' => $allEvents,
            'delta_link' => $finalDeltaLink,
        ];
    }
}
