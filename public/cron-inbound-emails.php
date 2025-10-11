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
            // Extract recipient addresses and from address
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
            
            // Get sender email
            $fromEmail = strtolower($message->getFrom()[0]->mail ?? 'unknown');
            
            $emailDomain = config('app.email_domain');
            
            // DEBUG: Log all recipients
            $output[] = "Email: {$message->getSubject()}";
            $output[] = "  From: {$fromEmail}";
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
                $output[] = "  ✗ Email calendar not found for token: {$token}";
                $message->setFlag('Seen');
                continue;
            }
            
            // SECURITY: Check that the sender matches the target_email (source address)
            if ($fromEmail !== strtolower($connection->target_email)) {
                $output[] = "  ✗ Security check failed: sender '{$fromEmail}' does not match source email '{$connection->target_email}'";
                $message->setFlag('Seen');
                continue;
            }
            
            $output[] = "  ✓ Security check passed: sender matches source email";
            
            // Process email
            $output[] = "Processing email for: {$connection->name}";
            $output[] = "  Subject: {$message->getSubject()}";
            
            // Extract .ics attachments using webklex parser (better than raw parsing)
            $icsAttachments = [];
            foreach ($message->getAttachments() as $attachment) {
                $contentType = strtolower($attachment->content_type ?? '');
                $filename = strtolower($attachment->name ?? '');
                
                // Check if it's a calendar file
                if (str_contains($contentType, 'calendar') || 
                    str_contains($contentType, 'ics') || 
                    str_ends_with($filename, '.ics')) {
                    
                    $content = $attachment->getContent();
                    if (str_contains($content, 'BEGIN:VCALENDAR')) {
                        $icsAttachments[] = $content;
                        $output[] = "  Found .ics: {$attachment->name} ({$contentType}, " . strlen($content) . " bytes)";
                    }
                }
            }
            
            if (empty($icsAttachments)) {
                $output[] = "  No .ics attachments found";
                $message->setFlag('Seen');
                continue;
            }
            
            // Process .ics attachments directly
            $connection->incrementEmailReceived();
            
            $icsParser = app(\App\Services\Email\IcsParserService::class);
            $syncEngine = app(\App\Services\Sync\SyncEngine::class);
            
            $totalEventsProcessed = 0;
            
            foreach ($icsAttachments as $icsContent) {
                try {
                    $events = $icsParser->parseIcsFile($icsContent);
                    $output[] = "  Parsed " . count($events) . " event(s) from .ics";
                    
                    // Get sync rules for this email calendar
                    $syncRules = $connection->getAllSyncRules();
                    
                    if ($syncRules->isEmpty()) {
                        $output[] = "  ⚠ No sync rules configured for this email calendar";
                        break;
                    }
                    
                    // Generate unique transaction ID for this sync operation
                    $transactionId = \Illuminate\Support\Str::uuid()->toString();
                    
                    foreach ($events as $eventData) {
                        // Check if this is a cancellation
                        $isCancellation = isset($eventData['method']) && strtoupper($eventData['method']) === 'CANCEL';
                        
                        if ($isCancellation) {
                            // Handle event cancellation - delete existing blockers
                            $output[] = "  📧 Event cancelled: {$eventData['uid']}";
                            
                            $mappings = \App\Models\SyncEventMapping::where('email_connection_id', $connection->id)
                                ->where('original_event_uid', $eventData['uid'])
                                ->get();
                            
                            foreach ($mappings as $mapping) {
                                try {
                                    // Delete blocker from target calendar
                                    if ($mapping->target_connection_id) {
                                        $targetConnection = $mapping->targetConnection;
                                        
                                        if ($targetConnection && $targetConnection->status === 'active') {
                                            $service = $targetConnection->provider === 'google'
                                                ? app(\App\Services\Calendar\GoogleCalendarService::class)
                                                : app(\App\Services\Calendar\MicrosoftCalendarService::class);
                                            
                                            $service->initializeWithConnection($targetConnection);
                                            $service->deleteBlocker(
                                                $mapping->target_calendar_id,
                                                $mapping->target_event_id
                                            );
                                            
                                            $output[] = "  ✓ Deleted blocker from {$targetConnection->provider}";
                                        }
                                    } elseif ($mapping->target_email_connection_id) {
                                        // Send CANCEL to email target
                                        $targetEmail = $mapping->targetEmailConnection;
                                        if ($targetEmail && $targetEmail->target_email) {
                                            $imipService = app(\App\Services\Email\ImipEmailService::class);
                                            $imipService->sendBlockerInvitation(
                                                $targetEmail,
                                                $targetEmail->target_email,
                                                $mapping->target_event_id,
                                                'Cancelled',
                                                $eventData['start'],
                                                $eventData['end'],
                                                'CANCEL',
                                                $mapping->sequence ?? 0
                                            );
                                            
                                            $output[] = "  ✓ Sent cancellation to email";
                                        }
                                    }
                                    
                                    // Delete the mapping
                                    $mapping->delete();
                                    
                                    // Log the deletion
                                    \App\Models\SyncLog::create([
                                        'user_id' => $connection->user_id,
                                        'sync_rule_id' => $mapping->sync_rule_id,
                                        'action' => 'deleted',
                                        'source_event_id' => $eventData['uid'],
                                        'target_event_id' => $mapping->target_event_id,
                                        'transaction_id' => $transactionId,
                                    ]);
                                    
                                } catch (\Exception $e) {
                                    $output[] = "  ✗ Error deleting blocker: " . $e->getMessage();
                                    
                                    \App\Models\SyncLog::create([
                                        'user_id' => $connection->user_id,
                                        'sync_rule_id' => $mapping->sync_rule_id,
                                        'action' => 'error',
                                        'source_event_id' => $eventData['uid'],
                                        'error_message' => $e->getMessage(),
                                        'transaction_id' => $transactionId,
                                    ]);
                                }
                            }
                            
                            continue; // Skip to next event
                        }
                        
                        // Process through each sync rule
                        foreach ($syncRules as $rule) {
                            if (!$rule->is_active) {
                                continue;
                            }
                            
                            // Apply rule filters
                            if (!$rule->shouldSyncEvent($eventData)) {
                                $output[] = "  Skipped event (filtered out by sync rule)";
                                continue;
                            }
                            
                            // ANTI-LOOP PROTECTION: Skip events with our own UID format
                            // This prevents processing our own blockers sent via API → Email sync
                            if (str_starts_with($eventData['uid'], 'syncmyday-')) {
                                $output[] = "  ⊘ Skipped SyncMyDay blocker (loop prevention): {$eventData['uid']}";
                                
                                \App\Models\SyncLog::create([
                                    'user_id' => $connection->user_id,
                                    'sync_rule_id' => $rule->id,
                                    'action' => 'skipped',
                                    'source_event_id' => $eventData['uid'],
                                    'event_start' => $eventData['start'],
                                    'event_end' => $eventData['end'],
                                    'error_message' => 'Loop prevention: this is a SyncMyDay blocker (recognized by UID format)',
                                    'transaction_id' => $transactionId,
                                ]);
                                
                                continue; // Skip to next event
                            }
                            
                            // Create blockers in target calendars
                            foreach ($rule->targets as $target) {
                                try {
                                    
                                    // Check if mapping already exists (to prevent duplicates)
                                    $existingMapping = \App\Models\SyncEventMapping::where('sync_rule_id', $rule->id)
                                        ->where('email_connection_id', $connection->id)
                                        ->where('original_event_uid', $eventData['uid'])
                                        ->where('target_connection_id', $target->target_connection_id)
                                        ->where('target_calendar_id', $target->target_calendar_id)
                                        ->first();
                                    
                                    $blockerId = null;
                                    $action = 'created';
                                    
                                    if ($target->isEmailTarget()) {
                                        // Send iMIP to email target
                                        $targetEmail = $target->targetEmailConnection;
                                        if ($targetEmail && $targetEmail->target_email) {
                                            $imipService = app(\App\Services\Email\ImipEmailService::class);
                                            $blockerId = 'syncmyday-' . $rule->id . '-' . md5($eventData['uid']);
                                            
                                            if ($existingMapping) {
                                                // Update existing blocker
                                                $imipService->sendBlockerInvitation(
                                                    $targetEmail,
                                                    $targetEmail->target_email,
                                                    $blockerId,
                                                    $rule->blocker_title,
                                                    $eventData['start'],
                                                    $eventData['end'],
                                                    'REQUEST',
                                                    ($existingMapping->sequence ?? 0) + 1
                                                );
                                                $existingMapping->update(['sequence' => ($existingMapping->sequence ?? 0) + 1]);
                                                $action = 'updated';
                                            } else {
                                                // Create new blocker
                                                $imipService->sendBlockerInvitation(
                                                    $targetEmail,
                                                    $targetEmail->target_email,
                                                    $blockerId,
                                                    $rule->blocker_title,
                                                    $eventData['start'],
                                                    $eventData['end']
                                                );
                                            }
                                            
                                            $output[] = "  ✓ " . ucfirst($action) . " blocker to email: {$targetEmail->target_email}";
                                        }
                                    } else {
                                        // Create/update blocker in API calendar (Google/Microsoft)
                                        $targetConnection = $target->targetConnection;
                                        if ($targetConnection && $targetConnection->status === 'active') {
                                            $service = $targetConnection->provider === 'google'
                                                ? app(\App\Services\Calendar\GoogleCalendarService::class)
                                                : app(\App\Services\Calendar\MicrosoftCalendarService::class);
                                            
                                            $service->initializeWithConnection($targetConnection);
                                            
                                            if ($existingMapping && $existingMapping->target_event_id) {
                                                // Update existing blocker
                                                try {
                                                    $service->updateBlocker(
                                                        $target->target_calendar_id,
                                                        $existingMapping->target_event_id,
                                                        $rule->blocker_title,
                                                        $eventData['start'],
                                                        $eventData['end']
                                                    );
                                                    $blockerId = $existingMapping->target_event_id;
                                                    $action = 'updated';
                                                } catch (\Exception $e) {
                                                    // If update fails, create new blocker
                                                    $blockerId = $service->createBlocker(
                                                        $target->target_calendar_id,
                                                        $rule->blocker_title,
                                                        $eventData['start'],
                                                        $eventData['end'],
                                                        \Illuminate\Support\Str::uuid()->toString()
                                                    );
                                                    $action = 'recreated';
                                                }
                                            } else {
                                                // Create new blocker
                                                $blockerId = $service->createBlocker(
                                                    $target->target_calendar_id,
                                                    $rule->blocker_title,
                                                    $eventData['start'],
                                                    $eventData['end'],
                                                    \Illuminate\Support\Str::uuid()->toString()
                                                );
                                            }
                                            
                                            $output[] = "  ✓ " . ucfirst($action) . " blocker in {$targetConnection->provider}: {$targetConnection->account_email}";
                                        }
                                    }
                                    
                                    // Create or update SyncEventMapping for tracking
                                    if ($blockerId) {
                                        if ($existingMapping) {
                                            $existingMapping->update([
                                                'target_event_id' => $blockerId,
                                            ]);
                                        } else {
                                            \App\Models\SyncEventMapping::create([
                                                'sync_rule_id' => $rule->id,
                                                'source_type' => 'email',
                                                'source_connection_id' => null, // Not applicable for email calendars
                                                'source_calendar_id' => $connection->target_email, // The email address is the calendar ID
                                                'source_event_id' => $eventData['uid'], // Original .ics UID
                                                'email_connection_id' => $connection->id,
                                                'original_event_uid' => $eventData['uid'], // For cancellation lookups
                                                'target_connection_id' => $target->target_connection_id,
                                                'target_email_connection_id' => $target->target_email_connection_id,
                                                'target_calendar_id' => $target->target_calendar_id,
                                                'target_event_id' => $blockerId,
                                                'sequence' => 0,
                                            ]);
                                        }
                                        
                                        // Create SyncLog entry
                                        \App\Models\SyncLog::create([
                                            'user_id' => $connection->user_id,
                                            'sync_rule_id' => $rule->id,
                                            'action' => $action,
                                            'source_event_id' => $eventData['uid'],
                                            'target_event_id' => $blockerId,
                                            'event_start' => $eventData['start'],
                                            'event_end' => $eventData['end'],
                                            'transaction_id' => $transactionId,
                                        ]);
                                    }
                                    
                                    $totalEventsProcessed++;
                                    
                                } catch (\Exception $e) {
                                    $output[] = "  ✗ Error creating blocker: " . $e->getMessage();
                                    
                                    // Log the error
                                    \App\Models\SyncLog::create([
                                        'user_id' => $connection->user_id,
                                        'sync_rule_id' => $rule->id,
                                        'action' => 'error',
                                        'source_event_id' => $eventData['uid'] ?? 'unknown',
                                        'error_message' => $e->getMessage(),
                                        'transaction_id' => $transactionId,
                                    ]);
                                }
                            }
                        }
                    }
                    
                } catch (\Exception $e) {
                    $output[] = "  ✗ Error parsing .ics: " . $e->getMessage();
                }
            }
            
            if ($totalEventsProcessed > 0) {
                $connection->incrementEventProcessed();
            }
            
            $output[] = "  ✓ Processed " . count($icsAttachments) . " .ics attachment(s), {$totalEventsProcessed} blocker(s) created";
            
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
