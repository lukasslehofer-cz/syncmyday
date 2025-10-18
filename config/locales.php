<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domain-Specific Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Configure available locales and default locale for each domain.
    | Users can only select from locales available for their domain.
    |
    */

    'domains' => [
        'syncmyday.cz' => [
            'default' => 'cs',
            'available' => ['cs', 'en'],
        ],
        'syncmyday.sk' => [
            'default' => 'sk',
            'available' => ['sk', 'en'],
        ],
        'syncmyday.pl' => [
            'default' => 'pl',
            'available' => ['pl', 'en'],
        ],
        'syncmyday.de' => [
            'default' => 'de',
            'available' => ['de', 'en'],
        ],
        'syncmyday.eu' => [
            'default' => 'en',
            'available' => ['en', 'de', 'cs', 'sk', 'pl'],
        ],
        // Development/testing
        'localhost' => [
            'default' => 'en',
            'available' => ['en', 'cs', 'sk', 'de', 'pl'],
        ],
        '127.0.0.1' => [
            'default' => 'en',
            'available' => ['en', 'cs', 'sk', 'de', 'pl'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Fallback Configuration
    |--------------------------------------------------------------------------
    |
    | Used when domain is not found in the configuration above.
    |
    */

    'fallback' => [
        'default' => 'en',
        'available' => ['en', 'cs', 'sk', 'de', 'pl'],
    ],

    /*
    |--------------------------------------------------------------------------
    | All Supported Locales
    |--------------------------------------------------------------------------
    |
    | All locales that have translation files in lang/ directory.
    | Used for display names in language selector.
    |
    */

    'supported' => [
        'en' => 'English',
        'cs' => 'Čeština',
        'sk' => 'Slovenčina',
        'de' => 'Deutsch',
        'pl' => 'Polski',
    ],
];

