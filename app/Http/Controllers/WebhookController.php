<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCalendarWebhookJob;
use App\Models\CalendarConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Google Calendar webhook
     */
    public function google(Request $request, string $connectionId)
    {
        // Google sends both GET (verification) and POST (notification) requests
        
        // Verification request
        if ($request->isMethod('get')) {
            return response('OK', 200);
        }

        // Get resource state from header
        $resourceState = $request->header('X-Goog-Resource-State');
        $channelId = $request->header('X-Goog-Channel-ID');
        $resourceId = $request->header('X-Goog-Resource-ID');

        // Ignore sync state (only process changes)
        if ($resourceState === 'sync') {
            return response('OK', 200);
        }

        // Verify connection exists
        $connection = CalendarConnection::find($connectionId);
        if (!$connection) {
            // ORPHANED WEBHOOK: Connection was deleted but webhook wasn't stopped
            // Log only ONCE per connection to avoid spam
            $cacheKey = "orphaned-webhook-logged-google-{$connectionId}";
            
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                // First time seeing this orphaned webhook - log it
                \App\Models\WebhookSubscription::where('calendar_connection_id', $connectionId)
                    ->delete();
                
                Log::channel('webhook')->warning('Orphaned webhook detected - Google will send until expiration', [
                    'connection_id' => $connectionId,
                    'channel_id' => $channelId,
                    'resource_id' => $resourceId,
                    'note' => 'Further webhooks for this connection will be silently ignored',
                ]);
                
                // Cache for 7 days (webhooks typically expire within 7 days)
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addDays(7));
            }
            
            // Return 200 to prevent Google from retrying
            return response('OK', 200);
        }

        // Find the webhook subscription to get calendar ID
        $subscription = $connection->webhookSubscriptions()
            ->where('provider_subscription_id', $channelId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            // Log all subscriptions for this connection to help debug
            $allSubscriptions = $connection->webhookSubscriptions()->get();
            
            Log::channel('webhook')->warning('Subscription not found for channel', [
                'connection_id' => $connectionId,
                'channel_id' => $channelId,
                'resource_id' => $resourceId,
                'all_subscriptions_count' => $allSubscriptions->count(),
                'all_subscriptions' => $allSubscriptions->map(function($sub) {
                    return [
                        'id' => $sub->id,
                        'channel_id' => $sub->provider_subscription_id,
                        'calendar_id' => $sub->calendar_id,
                        'status' => $sub->status,
                        'expires_at' => $sub->expires_at,
                    ];
                })->toArray(),
            ]);
            return response('OK', 200); // Still return 200 to avoid retries
        }

        // Single consolidated log for successful webhook processing
        Log::channel('webhook')->info('Webhook processed', [
            'provider' => 'google',
            'connection_id' => $connectionId,
            'calendar_id' => $subscription->calendar_id,
            'channel_id' => $channelId,
        ]);

        // Dispatch job with 3-second delay (debouncing)
        // This allows multiple rapid webhooks to be batched together
        // The rate limiting in the job will skip duplicates
        ProcessCalendarWebhookJob::dispatch($connection->id, $subscription->calendar_id)
            ->delay(now()->addSeconds(3));

        return response('OK', 200);
    }

    /**
     * Handle Microsoft Graph webhook
     */
    public function microsoft(Request $request, string $connectionId)
    {
        // Validation token check (subscription validation)
        if ($request->has('validationToken')) {
            return response($request->validationToken, 200)
                ->header('Content-Type', 'text/plain');
        }

        // Verify connection exists
        $connection = CalendarConnection::find($connectionId);
        if (!$connection) {
            // ORPHANED WEBHOOK: Connection was deleted but webhook wasn't stopped
            // Log only ONCE per connection to avoid spam
            $cacheKey = "orphaned-webhook-logged-microsoft-{$connectionId}";
            
            if (!\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                // First time seeing this orphaned webhook - log it
                \App\Models\WebhookSubscription::where('calendar_connection_id', $connectionId)
                    ->delete();
                
                Log::channel('webhook')->warning('Orphaned webhook detected - Microsoft will send until expiration', [
                    'connection_id' => $connectionId,
                    'note' => 'Further webhooks for this connection will be silently ignored',
                ]);
                
                // Cache for 30 days (Microsoft subscriptions can last up to 30 days)
                \Illuminate\Support\Facades\Cache::put($cacheKey, true, now()->addDays(30));
            }
            
            // Return 200 instead of 404 (Microsoft might retry on 404)
            return response('OK', 200);
        }

        // Parse notification payload
        $notifications = $request->input('value', []);
        $processedCount = 0;

        foreach ($notifications as $notification) {
            $subscriptionId = $notification['subscriptionId'] ?? null;
            $changeType = $notification['changeType'] ?? null;
            
            if (!$subscriptionId) {
                continue;
            }

            // Find subscription
            $subscription = $connection->webhookSubscriptions()
                ->where('provider_subscription_id', $subscriptionId)
                ->where('status', 'active')
                ->first();

            if (!$subscription) {
                Log::channel('webhook')->warning('Subscription not found', [
                    'connection_id' => $connectionId,
                    'subscription_id' => $subscriptionId,
                ]);
                continue;
            }

            // Dispatch job with 3-second delay (debouncing)
            // This allows multiple rapid webhooks to be batched together
            // The rate limiting in the job will skip duplicates
            ProcessCalendarWebhookJob::dispatch($connection->id, $subscription->calendar_id)
                ->delay(now()->addSeconds(3));
            $processedCount++;
        }

        // Single consolidated log for successful webhook processing
        if ($processedCount > 0) {
            Log::channel('webhook')->info('Webhook processed', [
                'provider' => 'microsoft',
                'connection_id' => $connectionId,
                'notifications_processed' => $processedCount,
            ]);
        }

        return response('Accepted', 202);
    }
}

