<?php

namespace App\Jobs;

use App\Models\WebhookSubscription;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Renew Webhook Subscriptions Job
 * 
 * Automatically renews webhook subscriptions before they expire.
 * Runs periodically via scheduler.
 */
class RenewWebhookSubscriptionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    /**
     * Execute the job.
     */
    public function handle(
        GoogleCalendarService $googleService,
        MicrosoftCalendarService $microsoftService
    ): void {
        Log::info('Renewing webhook subscriptions');

        // Find subscriptions expiring soon (within 24 hours)
        $subscriptions = WebhookSubscription::expiringSoon()->get();

        Log::info("Found {$subscriptions->count()} subscriptions to renew");

        foreach ($subscriptions as $subscription) {
            try {
                $connection = $subscription->calendarConnection;
                
                if (!$connection->isHealthy()) {
                    Log::warning('Skipping renewal for unhealthy connection', [
                        'subscription_id' => $subscription->id,
                        'connection_status' => $connection->status,
                    ]);
                    continue;
                }

                // Initialize service
                if ($connection->provider === 'google') {
                    $service = $googleService;
                } else {
                    $service = $microsoftService;
                }
                
                $service->initializeWithConnection($connection);

                // For Google, we need to stop and recreate. For Microsoft, we can renew.
                if ($connection->provider === 'google') {
                    // Stop old subscription
                    $service->stopWebhook(
                        $subscription->provider_subscription_id,
                        $subscription->resource_id
                    );

                    // Create new subscription
                    $webhookUrl = config('app.url') . "/webhooks/{$connection->provider}/{$connection->id}";
                    $newSubscription = $service->createWebhook($subscription->calendar_id, $webhookUrl);

                    $subscription->update([
                        'provider_subscription_id' => $newSubscription['subscription_id'],
                        'resource_id' => $newSubscription['resource_id'],
                        'expires_at' => $newSubscription['expires_at'],
                        'renewed_at' => now(),
                    ]);
                } else {
                    // Microsoft: just update expiration
                    $newExpiration = $service->renewWebhook($subscription->provider_subscription_id);
                    
                    $subscription->update([
                        'expires_at' => $newExpiration,
                        'renewed_at' => now(),
                    ]);
                }

                Log::info('Webhook subscription renewed', [
                    'subscription_id' => $subscription->id,
                    'new_expires_at' => $subscription->expires_at,
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to renew webhook subscription', [
                    'subscription_id' => $subscription->id,
                    'error' => $e->getMessage(),
                ]);

                $subscription->update(['status' => 'failed']);
            }
        }
    }
}

