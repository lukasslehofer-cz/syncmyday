<?php

namespace App\Services;

use App\Models\CalendarConnection;
use App\Models\WebhookSubscription;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Support\Facades\Log;

class WebhookSubscriptionService
{
    public const RESULT_CREATED = 'created';
    public const RESULT_EXISTS = 'exists';
    public const RESULT_SKIPPED_PROVIDER = 'skipped_provider';
    public const RESULT_SKIPPED_LOCALHOST = 'skipped_localhost';
    public const RESULT_FAILED = 'failed';

    /**
     * Ensure an active webhook subscription exists for a (connection, calendar)
     * pair. Idempotent — safe to call repeatedly.
     */
    public function ensureSubscription(CalendarConnection $connection, string $calendarId): string
    {
        // CalDAV and Apple (which uses CalDAV) don't support webhooks - they use polling instead
        if (in_array($connection->provider, ['caldav', 'apple'])) {
            Log::info('Skipping webhook creation for CalDAV/Apple (uses polling)', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
                'provider' => $connection->provider,
            ]);

            return self::RESULT_SKIPPED_PROVIDER;
        }

        // Skip webhooks for localhost (requires HTTPS)
        $appUrl = config('app.url');
        if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            Log::info('Skipping webhook creation for localhost', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
                'note' => 'Webhooks require HTTPS. Use ngrok or deploy to production for real-time sync.',
            ]);

            return self::RESULT_SKIPPED_LOCALHOST;
        }

        // Check if subscription already exists
        $existing = WebhookSubscription::where('calendar_connection_id', $connection->id)
            ->where('calendar_id', $calendarId)
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->first();

        if ($existing) {
            return self::RESULT_EXISTS;
        }

        try {
            $webhookUrl = config('app.url')."/webhooks/{$connection->provider}/{$connection->id}";

            if ($connection->provider === 'google') {
                $service = app(GoogleCalendarService::class);
            } else {
                $service = app(MicrosoftCalendarService::class);
            }

            $service->initializeWithConnection($connection);
            $subscriptionData = $service->createWebhook($calendarId, $webhookUrl);

            WebhookSubscription::create([
                'calendar_connection_id' => $connection->id,
                'provider_subscription_id' => $subscriptionData['subscription_id'],
                'resource_id' => $subscriptionData['resource_id'],
                'calendar_id' => $calendarId,
                'expires_at' => $subscriptionData['expires_at'],
                'status' => 'active',
            ]);

            Log::info('Webhook subscription created', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
            ]);

            return self::RESULT_CREATED;
        } catch (\Exception $e) {
            Log::warning('Failed to create webhook subscription', [
                'connection_id' => $connection->id,
                'calendar_id' => $calendarId,
                'error' => $e->getMessage(),
                'note' => 'Sync will work via polling instead of real-time webhooks',
            ]);

            return self::RESULT_FAILED;
        }
    }
}
