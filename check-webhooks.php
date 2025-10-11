<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Webhooks and Sync Rules ===\n\n";

// Get all sync rules
$rules = \App\Models\SyncRule::with(['sourceConnection', 'sourceEmailConnection', 'targets'])->get();

echo "Total Sync Rules: " . $rules->count() . "\n\n";

foreach ($rules as $rule) {
    echo "Rule #{$rule->id}:\n";
    echo "  Direction: {$rule->direction}\n";
    
    if ($rule->sourceConnection) {
        echo "  Source: {$rule->sourceConnection->provider} - {$rule->sourceConnection->provider_email}\n";
        echo "  Source Calendar ID: {$rule->source_calendar_id}\n";
        
        // Check webhooks for this source
        $webhooks = \App\Models\WebhookSubscription::where('calendar_connection_id', $rule->sourceConnection->id)
            ->where('calendar_id', $rule->source_calendar_id)
            ->get();
        
        echo "  Webhooks for source: {$webhooks->count()}\n";
        foreach ($webhooks as $webhook) {
            echo "    - Status: {$webhook->status}, Expires: {$webhook->expires_at}\n";
        }
    } elseif ($rule->sourceEmailConnection) {
        echo "  Source: Email - {$rule->sourceEmailConnection->name}\n";
    }
    
    echo "  Targets: {$rule->targets->count()}\n";
    foreach ($rule->targets as $target) {
        if ($target->targetConnection) {
            echo "    - {$target->targetConnection->provider} - {$target->targetConnection->provider_email} (ID: {$target->target_calendar_id})\n";
        } elseif ($target->targetEmailConnection) {
            echo "    - Email - {$target->targetEmailConnection->name}\n";
        }
    }
    
    echo "\n";
}

echo "\n=== All Webhook Subscriptions ===\n";
$allWebhooks = \App\Models\WebhookSubscription::with('calendarConnection')->get();
echo "Total: {$allWebhooks->count()}\n\n";

foreach ($allWebhooks as $webhook) {
    echo "Webhook ID: {$webhook->id}\n";
    echo "  Connection: {$webhook->calendarConnection->provider} - {$webhook->calendarConnection->provider_email}\n";
    echo "  Calendar ID: {$webhook->calendar_id}\n";
    echo "  Status: {$webhook->status}\n";
    echo "  Expires: {$webhook->expires_at}\n";
    echo "\n";
}
