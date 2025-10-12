<?php

return [
    // Google OAuth & Calendar API
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        // Use current domain for redirect URIs (simpler approach)
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL') . '/oauth/google/callback'),
        'redirect_login' => env('APP_URL') . '/auth/google/callback',
        'scopes' => [
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ],
    ],

    // Microsoft OAuth & Graph API
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        // Use current domain for redirect URIs (simpler approach)
        'redirect' => env('MICROSOFT_REDIRECT_URI', env('APP_URL') . '/oauth/microsoft/callback'),
        'redirect_login' => env('APP_URL') . '/auth/microsoft/callback',
        'tenant' => env('MICROSOFT_TENANT', 'common'),
        'scopes' => [
            'Calendars.ReadWrite',
            'User.Read',
            'offline_access',
        ],
    ],

    // Note: For OAuth login, we use the same credentials but different callback routes:
    // - Login/Register: /auth/{provider}/callback
    // - Calendar Connect (authenticated): /oauth/{provider}/callback

    // Stripe
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'pro_price_id' => env('STRIPE_PRO_PRICE_ID'), // Default fallback
        'trial_period_days' => env('TRIAL_PERIOD_DAYS', 31), // Trial length in days
        'prices' => [
            'cs' => env('STRIPE_PRICE_CZK'), // 249 CZK
            'en' => env('STRIPE_PRICE_EUR'), // 10.99 USD
            'de' => env('STRIPE_PRICE_EUR'), // 9.99 EUR
            'pl' => env('STRIPE_PRICE_PLN'), // 39.99 PLN
            'sk' => env('STRIPE_PRICE_EUR'), // 9.99 EUR (SK používá EUR)
        ],
        'currencies' => [
            'cs' => ['code' => 'CZK', 'symbol' => 'Kč', 'amount' => env('PRICE_AMOUNT_CZK', 249)],
            'en' => ['code' => 'EUR', 'symbol' => '€', 'amount' => env('PRICE_AMOUNT_EUR', 9.90)],
            'de' => ['code' => 'EUR', 'symbol' => '€', 'amount' => env('PRICE_AMOUNT_EUR', 9.90)],
            'pl' => ['code' => 'PLN', 'symbol' => 'zł', 'amount' => env('PRICE_AMOUNT_PLN', 49)],
            'sk' => ['code' => 'EUR', 'symbol' => '€', 'amount' => env('PRICE_AMOUNT_EUR', 9.90)],
        ],
    ],

    // Token encryption key (separate from app key for added security)
    'token_encryption_key' => env('TOKEN_ENCRYPTION_KEY'),
];

