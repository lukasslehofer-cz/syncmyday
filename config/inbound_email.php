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
        'host' => env('INBOUND_EMAIL_HOST', 'imap.mailgun.org'),
        'port' => env('INBOUND_EMAIL_PORT', 993),
        'username' => env('INBOUND_EMAIL_USERNAME'),
        'password' => env('INBOUND_EMAIL_PASSWORD'),
        'encryption' => env('INBOUND_EMAIL_ENCRYPTION', 'ssl'),
        'validate_cert' => env('INBOUND_EMAIL_VALIDATE_CERT', true),
        
        // Mailbox to monitor
        'mailbox' => env('INBOUND_EMAIL_MAILBOX', 'INBOX'),
        
        // After processing, move to this folder (null = delete)
        'processed_folder' => env('INBOUND_EMAIL_PROCESSED_FOLDER', 'Processed'),
    ],

    // Webhook configuration (alternative to IMAP)
    'webhook' => [
        'secret' => env('INBOUND_EMAIL_WEBHOOK_SECRET'),
    ],
];

