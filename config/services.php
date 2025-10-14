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
        'trial_period_days' => env('TRIAL_PERIOD_DAYS', 15), // Trial length in days
        
        // Monthly price IDs per locale
        'prices_monthly' => [
            'cs' => env('STRIPE_PRICE_CZK_MONTHLY'), // e.g. 29 CZK/month
            'en' => env('STRIPE_PRICE_EUR_MONTHLY'), // e.g. 1.99 EUR/month
            'de' => env('STRIPE_PRICE_EUR_MONTHLY'), // e.g. 1.99 EUR/month
            'pl' => env('STRIPE_PRICE_PLN_MONTHLY'), // e.g. 9.99 PLN/month
            'sk' => env('STRIPE_PRICE_EUR_MONTHLY'), // e.g. 1.99 EUR/month
        ],
        
        // Yearly price IDs per locale
        'prices_yearly' => [
            'cs' => env('STRIPE_PRICE_CZK_YEARLY'), // e.g. 249 CZK/year
            'en' => env('STRIPE_PRICE_EUR_YEARLY'), // e.g. 19.99 EUR/year
            'de' => env('STRIPE_PRICE_EUR_YEARLY'), // e.g. 19.99 EUR/year
            'pl' => env('STRIPE_PRICE_PLN_YEARLY'), // e.g. 99 PLN/year
            'sk' => env('STRIPE_PRICE_EUR_YEARLY'), // e.g. 19.99 EUR/year
        ],
        
        // Currency information and amounts
        'currencies' => [
            'cs' => [
                'code' => 'CZK',
                'symbol' => 'Kč',
                'amount_monthly' => env('PRICE_AMOUNT_CZK_MONTHLY', 29),
                'amount_yearly' => env('PRICE_AMOUNT_CZK_YEARLY', 249),
            ],
            'en' => [
                'code' => 'EUR',
                'symbol' => '€',
                'amount_monthly' => env('PRICE_AMOUNT_EUR_MONTHLY', 1.99),
                'amount_yearly' => env('PRICE_AMOUNT_EUR_YEARLY', 19.99),
            ],
            'de' => [
                'code' => 'EUR',
                'symbol' => '€',
                'amount_monthly' => env('PRICE_AMOUNT_EUR_MONTHLY', 1.99),
                'amount_yearly' => env('PRICE_AMOUNT_EUR_YEARLY', 19.99),
            ],
            'pl' => [
                'code' => 'PLN',
                'symbol' => 'zł',
                'amount_monthly' => env('PRICE_AMOUNT_PLN_MONTHLY', 9.99),
                'amount_yearly' => env('PRICE_AMOUNT_PLN_YEARLY', 99),
            ],
            'sk' => [
                'code' => 'EUR',
                'symbol' => '€',
                'amount_monthly' => env('PRICE_AMOUNT_EUR_MONTHLY', 1.99),
                'amount_yearly' => env('PRICE_AMOUNT_EUR_YEARLY', 19.99),
            ],
        ],
    ],

    // Token encryption key (separate from app key for added security)
    'token_encryption_key' => env('TOKEN_ENCRYPTION_KEY'),
];

