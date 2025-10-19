<?php

/**
 * Test Fakturoid OAuth 2.0 Authentication
 * 
 * Tests the OAuth 2.0 Client Credentials Flow
 * Run: php test-fakturoid-oauth.php
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "====================================\n";
echo "Fakturoid OAuth 2.0 Test\n";
echo "====================================\n\n";

// Get config values
$clientId = config('services.fakturoid.client_id');
$clientSecret = config('services.fakturoid.client_secret');
$slug = config('services.fakturoid.slug');

echo "Configuration:\n";
echo "- Client ID: " . ($clientId ? substr($clientId, 0, 10) . '...' : '❌ NOT SET') . "\n";
echo "- Client Secret: " . ($clientSecret ? substr($clientSecret, 0, 10) . '...' : '❌ NOT SET') . "\n";
echo "- Slug: " . ($slug ?: '❌ NOT SET') . "\n\n";

if (!$clientId || !$clientSecret || !$slug) {
    echo "❌ ERROR: Missing configuration in .env file!\n";
    echo "\nPlease add to .env:\n";
    echo "FAKTUROID_CLIENT_ID=your_client_id\n";
    echo "FAKTUROID_CLIENT_SECRET=your_client_secret\n";
    echo "FAKTUROID_SLUG=your_account_slug\n";
    exit(1);
}

echo "Step 1: Obtaining OAuth 2.0 access token...\n";
echo "URL: https://app.fakturoid.cz/api/v3/oauth/token\n";

// Prepare Basic Auth header
$authHeader = base64_encode("{$clientId}:{$clientSecret}");

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://app.fakturoid.cz/api/v3/oauth/token",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'User-Agent: SyncMyDay Test (support@syncmyday.com)',
        'Content-Type: application/json',
        'Accept: application/json',
        "Authorization: Basic {$authHeader}",
    ],
    CURLOPT_POSTFIELDS => json_encode([
        'grant_type' => 'client_credentials',
    ]),
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Response code: {$httpCode}\n";

if ($httpCode === 200) {
    echo "✅ OAuth token obtained successfully!\n\n";
    $data = json_decode($response, true);
    $accessToken = $data['access_token'] ?? null;
    $expiresIn = $data['expires_in'] ?? 0;
    
    echo "Access token: " . substr($accessToken, 0, 20) . "...\n";
    echo "Expires in: {$expiresIn} seconds (" . ($expiresIn / 3600) . " hours)\n\n";
    
    if (!$accessToken) {
        echo "❌ No access token in response!\n";
        exit(1);
    }
    
    // Test 2: Use the access token to get account info
    echo "Step 2: Testing access token with account info...\n";
    echo "URL: https://app.fakturoid.cz/api/v3/accounts/{$slug}/account.json\n";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://app.fakturoid.cz/api/v3/accounts/{$slug}/account.json",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'User-Agent: SyncMyDay Test (support@syncmyday.com)',
            "Authorization: Bearer {$accessToken}",
        ],
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Response code: {$httpCode}\n";
    
    if ($httpCode === 200) {
        echo "✅ Account access successful!\n";
        $accountData = json_decode($response, true);
        echo "Account name: " . ($accountData['name'] ?? 'N/A') . "\n";
        echo "Subdomain: " . ($accountData['subdomain'] ?? 'N/A') . "\n\n";
    } else {
        echo "❌ Failed to access account (code: {$httpCode})\n";
        echo "Response: {$response}\n\n";
        exit(1);
    }
    
    // Test 3: Test with FakturoidService
    echo "Step 3: Testing FakturoidService class...\n";
    try {
        $service = new \App\Services\FakturoidService();
        
        // This will trigger getAccessToken() internally
        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('getAccessToken');
        $method->setAccessible(true);
        $token = $method->invoke($service);
        
        if ($token) {
            echo "✅ FakturoidService can obtain access token!\n";
            echo "Token: " . substr($token, 0, 20) . "...\n\n";
        } else {
            echo "❌ FakturoidService failed to obtain token\n";
            exit(1);
        }
    } catch (\Exception $e) {
        echo "❌ Exception: {$e->getMessage()}\n";
        exit(1);
    }
    
    echo "====================================\n";
    echo "✅ ALL TESTS PASSED!\n";
    echo "====================================\n";
    echo "\nYour Fakturoid integration is ready to use.\n";
    echo "Access tokens are automatically managed and cached for 2 hours.\n";
    
    exit(0);
    
} elseif ($httpCode === 401) {
    echo "❌ Authentication FAILED (401 Unauthorized)\n";
    echo "Response: {$response}\n\n";
    echo "Possible issues:\n";
    echo "1. Wrong Client ID\n";
    echo "2. Wrong Client Secret\n";
    echo "3. Credentials not from 'User account' section\n";
    echo "   (They must be from Settings → User account → API access)\n";
    echo "   (NOT from OAuth applications!)\n\n";
    exit(1);
} else {
    echo "❌ Unexpected response code: {$httpCode}\n";
    echo "Response: {$response}\n\n";
    exit(1);
}

