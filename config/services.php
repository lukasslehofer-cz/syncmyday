<?php

return [
    // Google OAuth & Calendar API
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
        'scopes' => [
            'https://www.googleapis.com/auth/calendar',
            'https://www.googleapis.com/auth/calendar.events',
        ],
    ],

    // Microsoft OAuth & Graph API
    'microsoft' => [
        'client_id' => env('MICROSOFT_CLIENT_ID'),
        'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
        'redirect' => env('MICROSOFT_REDIRECT_URI'),
        'tenant' => env('MICROSOFT_TENANT', 'common'),
        'scopes' => [
            'Calendars.ReadWrite',
            'User.Read',
            'offline_access',
        ],
    ],

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

