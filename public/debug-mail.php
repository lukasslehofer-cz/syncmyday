<?php
/**
 * Debug Mail Configuration
 * 
 * Shows current mail configuration and attempts to send test email.
 * 
 * Usage: https://syncmyday.cz/debug-mail.php?token=YOUR_CRON_SECRET
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

// Get mail configuration
$output['mail_config'] = [
    'driver' => config('mail.default'),
    'from_address' => config('mail.from.address'),
    'from_name' => config('mail.from.name'),
];

// Get SMTP configuration if using SMTP
if (config('mail.default') === 'smtp') {
    $output['smtp_config'] = [
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'encryption' => config('mail.mailers.smtp.encryption'),
        'username' => config('mail.mailers.smtp.username'),
        'password' => config('mail.mailers.smtp.password') ? '***SET***' : 'NOT SET',
    ];
}

// Check if queue is being used
$output['queue_config'] = [
    'driver' => config('queue.default'),
    'connection' => config('queue.connections.' . config('queue.default')),
];

// Try to send test email
$testEmail = $_GET['test_email'] ?? null;
if ($testEmail) {
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from SyncMyDay', function ($message) use ($testEmail) {
            $message->to($testEmail)
                ->subject('SyncMyDay Test Email - ' . date('Y-m-d H:i:s'))
                ->from(config('mail.from.address'), config('mail.from.name'));
        });
        
        $output['test_result'] = 'SUCCESS - Email sent to ' . $testEmail;
    } catch (\Exception $e) {
        $output['test_result'] = 'FAILED - ' . $e->getMessage();
    }
}

// Check recent sync logs for email targets
$recentLogs = \App\Models\SyncLog::where('created_at', '>', now()->subHours(1))
    ->whereHas('syncRule.targets', function($q) {
        $q->whereNotNull('target_email_connection_id');
    })
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get(['id', 'sync_rule_id', 'action', 'created_at', 'error_message']);

$output['recent_email_syncs'] = $recentLogs->map(function($log) {
    return [
        'id' => $log->id,
        'sync_rule_id' => $log->sync_rule_id,
        'action' => $log->action,
        'created_at' => $log->created_at->toDateTimeString(),
        'error' => $log->error_message,
    ];
});

echo json_encode($output, JSON_PRETTY_PRINT);

