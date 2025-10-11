<?php
/**
 * Debug Webhooks - Check webhook subscriptions in database
 * 
 * Usage: https://syncmyday.cz/debug-webhooks.php?token=YOUR_CRON_SECRET
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Security check
$cronSecret = config('app.cron_secret');
$providedToken = $_GET['token'] ?? '';

if (empty($cronSecret) || !hash_equals($cronSecret, $providedToken)) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit(1);
}

header('Content-Type: application/json');

$output = [];

// Get connection ID from query or default to 5
$connectionId = $_GET['connection_id'] ?? 5;

$output['connection_id'] = $connectionId;

// Get connection
$connection = \App\Models\CalendarConnection::find($connectionId);
if (!$connection) {
    $output['error'] = "Connection #{$connectionId} not found";
    $output['all_connections'] = \App\Models\CalendarConnection::select('id', 'provider', 'provider_email', 'status')->get();
    echo json_encode($output, JSON_PRETTY_PRINT);
    exit(0);
}

$output['connection'] = [
    'id' => $connection->id,
    'provider' => $connection->provider,
    'email' => $connection->provider_email,
    'status' => $connection->status,
];

// Get all webhooks for this connection
$webhooks = \App\Models\WebhookSubscription::where('calendar_connection_id', $connectionId)->get();

$output['webhook_count'] = $webhooks->count();
$output['webhooks'] = [];

foreach ($webhooks as $webhook) {
    $output['webhooks'][] = [
        'id' => $webhook->id,
        'provider_subscription_id' => $webhook->provider_subscription_id,
        'calendar_id' => $webhook->calendar_id,
        'status' => $webhook->status,
        'expires_at' => $webhook->expires_at?->toDateTimeString(),
        'resource_id' => $webhook->resource_id,
    ];
}

// Check for specific subscription ID from query
$searchSubId = $_GET['subscription_id'] ?? null;
if ($searchSubId) {
    $output['search_subscription_id'] = $searchSubId;
    $specific = \App\Models\WebhookSubscription::where('provider_subscription_id', $searchSubId)->first();
    if ($specific) {
        $output['found'] = true;
        $output['found_webhook'] = [
            'id' => $specific->id,
            'connection_id' => $specific->calendar_connection_id,
            'calendar_id' => $specific->calendar_id,
            'status' => $specific->status,
        ];
    } else {
        $output['found'] = false;
    }
}

// All webhooks summary
$allWebhooks = \App\Models\WebhookSubscription::with('calendarConnection')->get();
$output['total_webhooks_in_db'] = $allWebhooks->count();
$output['all_webhooks'] = [];

foreach ($allWebhooks as $w) {
    $output['all_webhooks'][] = [
        'id' => $w->id,
        'connection_id' => $w->calendar_connection_id,
        'provider' => $w->calendarConnection->provider ?? 'unknown',
        'provider_subscription_id' => $w->provider_subscription_id,
        'calendar_id' => $w->calendar_id,
        'status' => $w->status,
    ];
}

// Get sync rules for this connection
$rules = \App\Models\SyncRule::where('source_connection_id', $connectionId)
    ->orWhereHas('targets', function($q) use ($connectionId) {
        $q->where('target_connection_id', $connectionId);
    })
    ->with(['sourceConnection', 'targets'])
    ->get();

$output['sync_rules_count'] = $rules->count();
$output['sync_rules'] = [];

foreach ($rules as $rule) {
    $output['sync_rules'][] = [
        'id' => $rule->id,
        'direction' => $rule->direction,
        'source_calendar_id' => $rule->source_calendar_id,
        'is_active' => $rule->is_active,
        'targets_count' => $rule->targets->count(),
    ];
}

echo json_encode($output, JSON_PRETTY_PRINT);

