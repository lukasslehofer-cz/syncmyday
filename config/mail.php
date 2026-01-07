<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send any email
    | messages sent by your application. Alternative mailers may be setup
    | and used as needed; however, this mailer will be used by default.
    |
    */

    'default' => env('MAIL_MAILER', 'smtp'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers to be used while
    | sending an e-mail. You will specify which one you are using for your
    | mailers below. You are free to add additional mailers as required.
    |
    | Supported: "smtp", "sendmail", "mailgun", "ses", "ses-v2",
    |            "postmark", "log", "array", "failover"
    |
    */

    'mailers' => [
        // MXroute - for system emails (info@)
        // Critical emails: welcome, trial, payments, password reset
        'mxroute' => [
            'transport' => 'smtp',
            'host' => env('MXROUTE_HOST', 'bunny.mxroute.com'),
            'port' => env('MXROUTE_PORT', 587),
            'encryption' => env('MXROUTE_ENCRYPTION', 'tls'),
            'username' => env('MXROUTE_USERNAME'),
            'password' => env('MXROUTE_PASSWORD'),
            'timeout' => null,
            'verify_peer' => true,
        ],

        // Mailgun - DISABLED (migrated to MXroute for all emails)
        // Keep configuration for easy rollback if needed
        // 'mailgun' => [
        //     'transport' => 'smtp',
        //     'host' => env('MAILGUN_SMTP_HOST', 'smtp.mailgun.org'),
        //     'port' => env('MAILGUN_SMTP_PORT', 587),
        //     'encryption' => env('MAILGUN_SMTP_ENCRYPTION', 'tls'),
        //     'username' => env('MAILGUN_SMTP_USERNAME'),
        //     'password' => env('MAILGUN_SMTP_PASSWORD'),
        //     'timeout' => null,
        //     'verify_peer' => true,
        // ],

        // Fallback - legacy config (keep for backward compatibility)
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'bunny.mxroute.com'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],

        'ses' => [
            'transport' => 'ses',
        ],

        'postmark' => [
            'transport' => 'postmark',
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],

        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all e-mails sent by your application to be sent from
    | the same address. Here, you may specify a name and address that is
    | used globally for all e-mails that are sent by your application.
    |
    */

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'events@syncmyday.eu'),
        'name' => env('MAIL_FROM_NAME', 'Example'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Rate limit for outgoing emails to comply with MXroute limits.
    | MXroute allows 400 emails/hour per mailbox. We set a safe limit of 300.
    | Rate limiting is enforced per FROM address (mailbox).
    |
    */

    'rate_limit_per_hour' => env('MAIL_RATE_LIMIT_PER_HOUR', 300),

    /*
    |--------------------------------------------------------------------------
    | Markdown Mail Settings
    |--------------------------------------------------------------------------
    |
    | If you are using Markdown based email rendering, you may configure your
    | theme and component paths here, allowing you to customize the design
    | of the emails. Or, you may simply stick with the Laravel defaults!
    |
    */

    'markdown' => [
        'theme' => 'default',

        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];

