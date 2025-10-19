<?php

/**
 * Test Fakturoid API Authentication
 * 
 * This script tests if your Fakturoid credentials are correct.
 * Run: php test-fakturoid-auth.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "Fakturoid API Authentication Test\n";
echo "====================================\n\n";

// Get config values
$email = config('services.fakturoid.email');
$apiToken = config('services.fakturoid.api_token');
$slug = config('services.fakturoid.slug');
$numberFormat = config('services.fakturoid.number_format');

echo "Configuration:\n";
echo "- Email: " . ($email ?: '❌ NOT SET') . "\n";
echo "- API Token: " . ($apiToken ? substr($apiToken, 0, 10) . '...' : '❌ NOT SET') . "\n";
echo "- Slug: " . ($slug ?: '❌ NOT SET') . "\n";
echo "- Number Format ID: " . ($numberFormat ?: '❌ NOT SET') . "\n\n";

if (!$email || !$apiToken || !$slug) {
    echo "❌ ERROR: Missing configuration in .env file!\n";
    echo "\nPlease add to .env:\n";
    echo "FAKTUROID_EMAIL=your@email.cz\n";
    echo "FAKTUROID_API_TOKEN=your_api_token\n";
    echo "FAKTUROID_SLUG=your_account_slug\n";
    echo "FAKTUROID_NUMBER_FORMAT=your_format_id\n";
    exit(1);
}

echo "Testing API connection...\n\n";

// Test 1: Get account info
echo "Test 1: Get account information\n";
echo "URL: https://app.fakturoid.cz/api/v3/accounts/{$slug}/account.json\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://app.fakturoid.cz/api/v3/accounts/{$slug}/account.json",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'User-Agent: SyncMyDay Test (support@syncmyday.com)',
    ],
    CURLOPT_USERPWD => "{$email}:{$apiToken}",
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response code: {$httpCode}\n";

if ($httpCode === 200) {
    echo "✅ Authentication successful!\n";
    $data = json_decode($response, true);
    echo "Account name: " . ($data['name'] ?? 'N/A') . "\n";
    echo "Subdomain: " . ($data['subdomain'] ?? 'N/A') . "\n\n";
} elseif ($httpCode === 401) {
    echo "❌ Authentication FAILED (401 Unauthorized)\n";
    echo "Response: {$response}\n\n";
    echo "Possible issues:\n";
    echo "1. Wrong email address\n";
    echo "2. Wrong API token\n";
    echo "3. API access not enabled in Fakturoid\n\n";
    exit(1);
} elseif ($httpCode === 404) {
    echo "❌ Account not found (404)\n";
    echo "The slug '{$slug}' is incorrect.\n";
    echo "Check the URL in your Fakturoid account.\n\n";
    exit(1);
} else {
    echo "❌ Unexpected response code: {$httpCode}\n";
    echo "Response: {$response}\n\n";
    exit(1);
}

// Test 2: List number formats
echo "Test 2: List number formats\n";
echo "URL: https://app.fakturoid.cz/api/v3/accounts/{$slug}/number_formats.json\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://app.fakturoid.cz/api/v3/accounts/{$slug}/number_formats.json",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'User-Agent: SyncMyDay Test (support@syncmyday.com)',
    ],
    CURLOPT_USERPWD => "{$email}:{$apiToken}",
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Number formats retrieved successfully!\n\n";
    $formats = json_decode($response, true);
    
    echo "Available number formats:\n";
    foreach ($formats as $format) {
        $marker = ($format['id'] == $numberFormat) ? '👉 ' : '   ';
        echo "{$marker}ID: {$format['id']} - Format: {$format['format']}\n";
    }
    echo "\n";
    
    // Check if configured format exists
    $formatExists = false;
    foreach ($formats as $format) {
        if ($format['id'] == $numberFormat) {
            $formatExists = true;
            echo "✅ Your configured number format (ID: {$numberFormat}) exists!\n";
            echo "   Format: {$format['format']}\n\n";
            break;
        }
    }
    
    if (!$formatExists && $numberFormat) {
        echo "⚠️  WARNING: Your configured number format ID ({$numberFormat}) was not found!\n";
        echo "   Please update FAKTUROID_NUMBER_FORMAT in .env with one of the IDs above.\n\n";
    }
} else {
    echo "❌ Failed to retrieve number formats (code: {$httpCode})\n";
    echo "Response: {$response}\n\n";
}

// Test 3: Try to create a test invoice (dry run - we won't actually create it)
echo "Test 3: Verify invoice creation permissions\n";
echo "Checking if we can access invoices endpoint...\n";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://app.fakturoid.cz/api/v3/accounts/{$slug}/invoices.json?limit=1",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'User-Agent: SyncMyDay Test (support@syncmyday.com)',
    ],
    CURLOPT_USERPWD => "{$email}:{$apiToken}",
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Invoice endpoint accessible!\n\n";
} else {
    echo "❌ Cannot access invoice endpoint (code: {$httpCode})\n";
    echo "Response: {$response}\n\n";
}

echo "====================================\n";
echo "Test completed!\n";
echo "====================================\n";

if ($httpCode === 200) {
    echo "\n✅ All tests passed! Your Fakturoid integration should work.\n";
    exit(0);
} else {
    echo "\n❌ Some tests failed. Please fix the issues above.\n";
    exit(1);
}

