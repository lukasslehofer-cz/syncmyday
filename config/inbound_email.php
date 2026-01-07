<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Inbound Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the application receives emails sent to calendar
    | email addresses (e.g., abc12345@syncmyday.com)
    |
    */

    // Enable IMAP polling
    'enabled' => env('INBOUND_EMAIL_ENABLED', false),

    // IMAP server configuration
    'imap' => [
        'host' => env('INBOUND_EMAIL_HOST', 'bunny.mxroute.com'),
        'port' => env('INBOUND_EMAIL_PORT', 993),
        'username' => env('INBOUND_EMAIL_USERNAME'),
        'password' => env('INBOUND_EMAIL_PASSWORD'),
        'encryption' => env('INBOUND_EMAIL_ENCRYPTION', 'ssl'),
        'validate_cert' => env('INBOUND_EMAIL_VALIDATE_CERT', true),
        
        // Mailbox to monitor
        'mailbox' => env('INBOUND_EMAIL_MAILBOX', 'INBOX'),
        
        // After processing, move emails to this folder
        'processed_folder' => env('INBOUND_EMAIL_PROCESSED_FOLDER', 'Processed'),
        
        // Days to keep processed emails before deletion
        'retention_days' => env('INBOUND_EMAIL_RETENTION_DAYS', 7),
    ],

    // Webhook configuration (alternative to IMAP)
    'webhook' => [
        'secret' => env('INBOUND_EMAIL_WEBHOOK_SECRET'),
    ],
];

