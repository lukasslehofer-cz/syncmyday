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

        Log::channel('webhook')->info('Google webhook received', [
            'connection_id' => $connectionId,
            'headers' => $request->headers->all(),
        ]);

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
            Log::channel('webhook')->warning('Connection not found - orphaned webhook channel detected', [
                'connection_id' => $connectionId,
                'channel_id' => $channelId,
                'resource_id' => $resourceId,
                'resource_state' => $resourceState,
                'message' => 'This webhook channel was not properly stopped when connection was deleted. ' .
                            'Google will continue sending notifications until channel expires or is manually stopped.',
            ]);
            
            // Return 200 instead of 404 to prevent Google from retrying
            // Note: Google will eventually stop sending to channels that consistently return errors,
            // but 200 is safer and prevents unnecessary retries
            return response('OK', 200);
        }

        Log::channel('webhook')->debug('Connection found, looking for subscription', [
            'connection_id' => $connectionId,
            'channel_id' => $channelId,
        ]);

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

        Log::channel('webhook')->info('Processing webhook', [
            'connection_id' => $connectionId,
            'subscription_id' => $subscription->id,
            'calendar_id' => $subscription->calendar_id,
            'channel_id' => $channelId,
        ]);

        // Dispatch job to process changes
        ProcessCalendarWebhookJob::dispatch($connection->id, $subscription->calendar_id);

        return response('OK', 200);
    }

    /**
     * Handle Microsoft Graph webhook
     */
    public function microsoft(Request $request, string $connectionId)
    {
        Log::channel('webhook')->info('Microsoft webhook received', [
            'connection_id' => $connectionId,
            'query' => $request->query->all(),
        ]);

        // Validation token check (subscription validation)
        if ($request->has('validationToken')) {
            return response($request->validationToken, 200)
                ->header('Content-Type', 'text/plain');
        }

        // Verify connection exists
        $connection = CalendarConnection::find($connectionId);
        if (!$connection) {
            Log::channel('webhook')->warning('Connection not found', [
                'connection_id' => $connectionId,
            ]);
            return response('Not Found', 404);
        }

        // Parse notification payload
        $notifications = $request->input('value', []);

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

            // Dispatch job to process changes
            ProcessCalendarWebhookJob::dispatch($connection->id, $subscription->calendar_id);
        }

        return response('Accepted', 202);
    }
}

