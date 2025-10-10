#!/usr/bin/env php
<?php

/**
 * Cron Job Runner for Inbound Email Processing (Shared Hosting Compatible)
 * 
 * This script processes inbound calendar emails via IMAP polling.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-inbound-emails.php
 * 
 * Security: Requires CRON_SECRET token to prevent unauthorized access
 */

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader (one level up from public/)
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel application (one level up from public/)
$app = require_once __DIR__.'/../bootstrap/app.php';

// Boot the application
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Security check - require CRON_SECRET token
$cronSecret = config('app.cron_secret');

// If accessed via HTTP, verify token
if (php_sapi_name() !== 'cli') {
    $providedToken = $_GET['token'] ?? '';
    
    if (empty($cronSecret)) {
        http_response_code(503);
        echo json_encode([
            'error' => 'CRON_SECRET not configured',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
    
    if (!hash_equals($cronSecret, $providedToken)) {
        http_response_code(401);
        echo json_encode([
            'error' => 'Unauthorized - invalid token',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit(1);
    }
    
    // Set content type for HTTP response
    header('Content-Type: application/json');
}

// Direct execution without artisan command (proc_open not needed)
try {
    $startTime = microtime(true);
    $output = [];
    
    // Check if enabled
    if (!config('inbound_email.enabled')) {
        $output[] = 'Inbound email processing is disabled. Set INBOUND_EMAIL_ENABLED=true in .env';
        
        $response = [
            'status' => 'disabled',
            'output' => implode("\n", $output),
            'time' => date('Y-m-d H:i:s'),
        ];
        
        if (php_sapi_name() === 'cli') {
            echo implode("\n", $output) . "\n";
        } else {
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
        exit(0);
    }
    
    $output[] = 'Starting inbound email processing...';
    
    // Get IMAP configuration
    $config = config('inbound_email.imap');
    
    $output[] = "Connecting to IMAP: {$config['host']}:{$config['port']}";
    
    // Connect to IMAP using webklex/php-imap
    $cm = new \Webklex\PHPIMAP\ClientManager();
    $client = $cm->make([
        'host' => $config['host'],
        'port' => $config['port'],
        'encryption' => $config['encryption'],
        'validate_cert' => $config['validate_cert'],
        'username' => $config['username'],
        'password' => $config['password'],
        'protocol' => 'imap'
    ]);
    
    $client->connect();
    $output[] = 'Connected successfully';
    
    // Get mailbox
    $folder = $client->getFolder($config['mailbox'] ?? 'INBOX');
    
    // Get unread emails
    $messages = $folder->query()->unseen()->get();
    
    if ($messages->count() === 0) {
        $output[] = 'No new emails to process';
        
        $response = [
            'status' => 'success',
            'processed' => 0,
            'failed' => 0,
            'output' => implode("\n", $output),
            'duration' => round(microtime(true) - $startTime, 2) . 's',
            'time' => date('Y-m-d H:i:s'),
        ];
        
        if (php_sapi_name() === 'cli') {
            echo implode("\n", $output) . "\n";
        } else {
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
        exit(0);
    }
    
    $limit = 10; // Process max 10 emails per run
    $processed = 0;
    $failed = 0;
    
    // Get sync service
    $syncService = app(\App\Services\Email\EmailCalendarSyncService::class);
    
    foreach ($messages->take($limit) as $message) {
        try {
            // Extract recipient addresses from multiple sources
            $toAddresses = [];
            
            // Standard To field
            foreach ($message->getTo() as $to) {
                $toAddresses[] = strtolower($to->mail);
            }
            
            // CC field
            foreach ($message->getCc() as $cc) {
                $toAddresses[] = strtolower($cc->mail);
            }
            
            // Fallback: Parse To header directly if getTo() returns empty
            if (empty($toAddresses)) {
                $rawTo = $message->getHeader()->get('to');
                if ($rawTo) {
                    $rawToString = is_array($rawTo) ? implode(', ', $rawTo) : $rawTo;
                    if (preg_match_all('/([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $rawToString, $matches)) {
                        foreach ($matches[1] as $addr) {
                            $toAddresses[] = strtolower($addr);
                        }
                    }
                }
            }
            
            // For forwarded emails, check Delivered-To, X-Original-To, Envelope-To headers
            $deliveredTo = $message->getHeader()->get('delivered-to');
            if ($deliveredTo) {
                $addresses = is_array($deliveredTo) ? $deliveredTo : [$deliveredTo];
                foreach ($addresses as $addr) {
                    if (preg_match('/([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $addr, $matches)) {
                        $toAddresses[] = strtolower($matches[1]);
                    }
                }
            }
            
            $originalTo = $message->getHeader()->get('x-original-to');
            if ($originalTo) {
                $addresses = is_array($originalTo) ? $originalTo : [$originalTo];
                foreach ($addresses as $addr) {
                    if (preg_match('/([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $addr, $matches)) {
                        $toAddresses[] = strtolower($matches[1]);
                    }
                }
            }
            
            $envelopeTo = $message->getHeader()->get('envelope-to');
            if ($envelopeTo) {
                $addresses = is_array($envelopeTo) ? $envelopeTo : [$envelopeTo];
                foreach ($addresses as $addr) {
                    if (preg_match('/([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})/i', $addr, $matches)) {
                        $toAddresses[] = strtolower($matches[1]);
                    }
                }
            }
            
            // Remove duplicates
            $toAddresses = array_unique($toAddresses);
            
            $emailDomain = config('app.email_domain');
            
            // DEBUG: Log all recipients
            $output[] = "Email: {$message->getSubject()}";
            $output[] = "  From: " . ($message->getFrom()[0]->mail ?? 'unknown');
            $output[] = "  Recipients (To/CC): " . implode(', ', array_slice($toAddresses, 0, 5));
            $output[] = "  Looking for domain: @{$emailDomain}";
            
            // Find matching email calendar token
            $token = null;
            foreach ($toAddresses as $address) {
                if (str_ends_with($address, '@' . $emailDomain)) {
                    $token = explode('@', $address)[0];
                    break;
                }
            }
            
            if (!$token) {
                $output[] = "  ✗ No valid recipient found (no @{$emailDomain} address)";
                $message->setFlag('Seen');
                continue;
            }
            
            $output[] = "  ✓ Found token: {$token}";
            
            // Find email calendar connection
            $connection = \App\Models\EmailCalendarConnection::findByToken($token);
            
            if (!$connection) {
                $output[] = "Email calendar not found for token: {$token}";
                $message->setFlag('Seen');
                continue;
            }
            
            // Get raw email
            $rawEmail = $message->getRawBody();
            
            // Process email
            $output[] = "Processing email for: {$connection->name}";
            $output[] = "  Subject: {$message->getSubject()}";
            
            $result = $syncService->processIncomingEmail($token, $rawEmail);
            
            $output[] = "  ✓ Processed {$result['ics_count']} .ics attachments, {$result['events_processed']} events";
            
            // Mark as read
            $message->setFlag('Seen');
            
            // Optionally move to processed folder
            $processedFolder = $config['processed_folder'] ?? null;
            if ($processedFolder) {
                try {
                    $message->move($processedFolder);
                } catch (\Exception $e) {
                    $output[] = "  Could not move email to '{$processedFolder}'";
                }
            }
            
            $processed++;
            
        } catch (\Exception $e) {
            $failed++;
            $output[] = "Failed to process email: " . $e->getMessage();
            \Illuminate\Support\Facades\Log::error('Inbound email processing failed', [
                'subject' => $message->getSubject(),
                'error' => $e->getMessage(),
            ]);
        }
    }
    
    $output[] = "Processed: {$processed}, Failed: {$failed}";
    
    $response = [
        'status' => 'success',
        'processed' => $processed,
        'failed' => $failed,
        'output' => implode("\n", $output),
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo "[" . date('Y-m-d H:i:s') . "] Inbound email processing completed\n";
        echo implode("\n", $output) . "\n";
    } else {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit(0);
    
} catch (\Exception $e) {
    $error = 'IMAP processing error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error('IMAP processing error', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    $response = [
        'status' => 'error',
        'error' => $error,
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit(1);
}
