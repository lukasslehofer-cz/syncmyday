#!/usr/bin/env php
<?php

/**
 * Cron Job Runner for SyncMyDay (public/ folder version)
 * 
 * This file can be called directly from cron without artisan command.
 * This version is designed to be placed in the public/ folder.
 * 
 * Usage in cron: /usr/bin/php /path/to/syncmyday/public/cron-email.php
 */

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader (one level up from public/)
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel application (one level up from public/)
$app = require_once __DIR__.'/../bootstrap/app.php';

// Make kernel instance
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Define the artisan command
$status = $kernel->call('schedule:run');

// Output for logging
echo "[" . date('Y-m-d H:i:s') . "] Schedule:run executed with status: {$status}\n";

// Exit with status code
exit($status);

