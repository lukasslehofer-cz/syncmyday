<?php

return [
    // Google OAuth & Calendar API
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        // Always use primary OAuth domain for redirect URIs
        'redirect' => 'https://' . (config('app.oauth_primary_domain') ?? 'syncmyday.cz') . '/oauth/google/callback',
        'redirect_login' => 'https://' . (config('app.oauth_primary_domain') ?? 'syncmyday.cz') . '/auth/google/callback',
        'scopes' => [
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ],
    ],

    // Microsoft OAuth & Graph API
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        // Always use primary OAuth domain for redirect URIs
        'redirect' => 'https://' . (config('app.oauth_primary_domain') ?? 'syncmyday.cz') . '/oauth/microsoft/callback',
        'redirect_login' => 'https://' . (config('app.oauth_primary_domain') ?? 'syncmyday.cz') . '/auth/microsoft/callback',
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
        'pro_price_id' => env('STRIPE_PRO_PRICE_ID'),
    ],

    // Token encryption key (separate from app key for added security)
    'token_encryption_key' => env('TOKEN_ENCRYPTION_KEY'),
];

