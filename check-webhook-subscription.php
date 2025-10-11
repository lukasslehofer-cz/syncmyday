<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Webhook Subscriptions ===\n\n";

// Get connection ID 5 (Microsoft)
$connection = \App\Models\CalendarConnection::find(5);
if (!$connection) {
    echo "❌ Connection 5 not found\n";
    exit(1);
}

echo "Connection #5:\n";
echo "  Provider: {$connection->provider}\n";
echo "  Email: {$connection->provider_email}\n";
echo "  Status: {$connection->status}\n\n";

// Get all webhooks for this connection
$webhooks = \App\Models\WebhookSubscription::where('calendar_connection_id', 5)->get();

echo "Webhook Subscriptions for Connection #5: {$webhooks->count()}\n\n";

foreach ($webhooks as $webhook) {
    echo "Webhook ID: {$webhook->id}\n";
    echo "  Provider Subscription ID: {$webhook->provider_subscription_id}\n";
    echo "  Calendar ID: {$webhook->calendar_id}\n";
    echo "  Status: {$webhook->status}\n";
    echo "  Expires: {$webhook->expires_at}\n";
    echo "  Resource ID: {$webhook->resource_id}\n";
    echo "\n";
}

echo "\n=== Looking for subscription: ee4cb8db-eaf1-46cd-8c4a-e253328f893e ===\n";
$specific = \App\Models\WebhookSubscription::where('provider_subscription_id', 'ee4cb8db-eaf1-46cd-8c4a-e253328f893e')->first();
if ($specific) {
    echo "✓ Found!\n";
    echo "  Connection ID: {$specific->calendar_connection_id}\n";
    echo "  Calendar ID: {$specific->calendar_id}\n";
    echo "  Status: {$specific->status}\n";
} else {
    echo "❌ Not found in database\n";
}

echo "\n=== All Webhooks in DB ===\n";
$all = \App\Models\WebhookSubscription::with('calendarConnection')->get();
echo "Total: {$all->count()}\n\n";
foreach ($all as $w) {
    echo "ID: {$w->id} | Conn: {$w->calendar_connection_id} | Provider Sub ID: {$w->provider_subscription_id}\n";
}
