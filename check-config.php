<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_URL: " . config('app.url') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "\nChecking webhook creation logic...\n";

$appUrl = config('app.url');
if (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
    echo "❌ SKIPPING webhooks (localhost detected)\n";
} else {
    echo "✓ Webhooks would be created\n";
}
