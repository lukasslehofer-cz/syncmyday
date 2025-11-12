<?php

namespace App\Console\Commands;

use App\Models\EmailCalendarConnection;
use App\Services\Email\EmailCalendarSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;

/**
 * Process inbound emails via IMAP polling
 * 
 * This command:
 * 1. Connects to IMAP mailbox
 * 2. Fetches unread emails
 * 3. Extracts recipient token (e.g., abc12345@syncmyday.com -> abc12345)
 * 4. Processes .ics attachments
 * 5. Creates/updates blockers in target calendars
 * 6. Moves processed emails to "Processed" folder
 * 
 * Processed emails are kept for 7 days then deleted by CleanOldInboundEmailsCommand.
 * 
 * Runs automatically via Laravel scheduler (every 5 minutes).
 * Configured in app/Console/Kernel.php
 */
class ProcessInboundEmailsCommand extends Command
{
    protected $signature = 'app:process-inbound-emails
                            {--dry-run : Preview without processing}
                            {--limit=10 : Maximum emails to process per run}';

    protected $description = 'Process inbound calendar emails via IMAP';

    private EmailCalendarSyncService $syncService;

    public function __construct(EmailCalendarSyncService $syncService)
    {
        parent::__construct();
        $this->syncService = $syncService;
    }

    public function handle(): int
    {
        if (!config('inbound_email.enabled')) {
            $this->warn('Inbound email processing is disabled. Set INBOUND_EMAIL_ENABLED=true in .env');
            return self::FAILURE;
        }

        $this->info('Starting inbound email processing...');

        try {
            // Connect to IMAP
            $client = $this->connectImap();
            
            if (!$client) {
                $this->error('Failed to connect to IMAP server');
                return self::FAILURE;
            }

            // Get mailbox
            $folder = $client->getFolder(config('inbound_email.imap.mailbox', 'INBOX'));
            
            // Get unread emails
            $messages = $folder->query()->unseen()->get();
            
            if ($messages->count() === 0) {
                $this->info('No new emails to process');
                return self::SUCCESS;
            }

            $limit = (int) $this->option('limit');
            $processed = 0;
            $failed = 0;

            foreach ($messages->take($limit) as $message) {
                try {
                    $this->processEmail($message);
                    $processed++;
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("Failed to process email: " . $e->getMessage());
                    Log::error('Inbound email processing failed', [
                        'subject' => $message->getSubject(),
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->info("Processed: {$processed}, Failed: {$failed}");
            
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('IMAP processing error: ' . $e->getMessage());
            Log::error('IMAP processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return self::FAILURE;
        }
    }

    private function connectImap()
    {
        $config = config('inbound_email.imap');
        
        $this->info("Connecting to IMAP: {$config['host']}:{$config['port']}");

        try {
            $cm = new ClientManager();
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
            
            $this->info("Connected successfully");
            
            return $client;

        } catch (\Exception $e) {
            $this->error("IMAP connection failed: " . $e->getMessage());
            Log::error('IMAP connection failed', [
                'host' => $config['host'],
                'port' => $config['port'],
                'username' => $config['username'],
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function processEmail($message): void
    {
        // Extract recipient addresses
        $toAddresses = [];
        
        // Get To recipients
        try {
            $toRecipients = $message->getTo();
            if ($toRecipients && $toRecipients->count() > 0) {
                foreach ($toRecipients as $to) {
                    if (isset($to->mail)) {
                        $toAddresses[] = strtolower($to->mail);
                    }
                }
            }
        } catch (\Exception $e) {
            // Continue
        }
        
        // Get CC recipients
        try {
            $ccRecipients = $message->getCc();
            if ($ccRecipients && $ccRecipients->count() > 0) {
                foreach ($ccRecipients as $cc) {
                    if (isset($cc->mail)) {
                        $toAddresses[] = strtolower($cc->mail);
                    }
                }
            }
        } catch (\Exception $e) {
            // Continue
        }
        
        // IMPORTANT: When using catch-all forwarding, the original recipient
        // is in the Envelope-to header. Use getHeader()->raw to get raw headers.
        
        try {
            $header = $message->getHeader();
            if ($header && isset($header->raw)) {
                $rawHeaders = $header->raw;
                
                // Extract email addresses from special headers
                $headerPatterns = [
                    '/^Envelope-to:\s*<?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>?/im',
                    '/^X-Original-To:\s*<?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>?/im',
                    '/^Delivered-To:\s*<?([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})>?/im',
                ];
                
                foreach ($headerPatterns as $pattern) {
                    if (preg_match_all($pattern, $rawHeaders, $matches)) {
                        foreach ($matches[1] as $email) {
                            $email = strtolower(trim($email));
                            if (filter_var($email, FILTER_VALIDATE_EMAIL) && !in_array($email, $toAddresses)) {
                                $toAddresses[] = $email;
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            // If raw parsing fails, continue with what we have
        }

        // Find matching email calendar connection by checking all recipient addresses
        // Support for multiple domains (syncmyday.cz, .sk, .pl, .de, .eu)
        $validDomains = ['syncmyday.cz', 'syncmyday.sk', 'syncmyday.pl', 'syncmyday.de', 'syncmyday.eu'];
        
        $token = null;
        $matchedAddress = null;
        
        foreach ($toAddresses as $address) {
            foreach ($validDomains as $domain) {
                if (str_ends_with($address, '@' . $domain)) {
                    $token = explode('@', $address)[0];
                    $matchedAddress = $address;
                    break 2; // Break both loops
                }
            }
        }

        if (!$token) {
            $this->warn("No valid recipient found in email: {$message->getSubject()}");
            $this->warn("  Checked addresses: " . (empty($toAddresses) ? '(none found)' : implode(', ', $toAddresses)));
            
            // DEBUG: Show actual email headers
            $this->warn("  DEBUG - Using getHeader()->raw...");
            try {
                $header = $message->getHeader();
                if ($header && isset($header->raw)) {
                    $rawHeaders = $header->raw;
                    $this->warn("  Raw headers length: " . strlen($rawHeaders) . " bytes");
                    
                    // Show relevant headers
                    $lines = explode("\n", $rawHeaders);
                    $headerCount = 0;
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (preg_match('/^(Return-Path|Delivered-To|Envelope-to|X-Original-To|To|Cc|From|Subject):/i', $line)) {
                            $this->warn("    " . $line);
                            $headerCount++;
                        }
                    }
                    
                    if ($headerCount === 0) {
                        $this->warn("    No relevant headers found!");
                        $this->warn("    First 500 chars: " . substr($rawHeaders, 0, 500));
                    }
                } else {
                    $this->warn("    getHeader() returned null or no raw property!");
                }
            } catch (\Exception $e) {
                $this->warn("    Error: " . $e->getMessage());
            }
            
            $message->setFlag('Seen');
            return;
        }
        
        $this->info("Found recipient: {$matchedAddress} (token: {$token})");

        // Find email calendar connection
        $connection = EmailCalendarConnection::findByToken($token);
        
        if (!$connection) {
            $this->warn("Email calendar not found for token: {$token}");
            $message->setFlag('Seen');
            return;
        }

        // Get raw email (headers + body) - EmailParserService needs full raw email
        $header = $message->getHeader();
        $rawHeaders = $header && isset($header->raw) ? $header->raw : '';
        $rawBody = $message->getRawBody();
        
        // Combine headers + body to create full raw email
        $rawEmail = $rawHeaders . "\r\n\r\n" . $rawBody;

        if ($this->option('dry-run')) {
            $this->info("Would process email for: {$connection->name} ({$connection->email_address})");
            $this->info("  Subject: {$message->getSubject()}");
            $this->info("  From: " . ($message->getFrom()[0]->mail ?? 'unknown'));
            $this->info("  Raw email length: " . strlen($rawEmail) . " bytes");
            $this->info("  Headers length: " . strlen($rawHeaders) . " bytes");
            $this->info("  Body length: " . strlen($rawBody) . " bytes");
            return;
        }

        // Process email
        $this->info("Processing email for: {$connection->name}");
        $this->info("  Subject: {$message->getSubject()}");
        
        $result = $this->syncService->processIncomingEmail($token, $rawEmail);

        $this->info("  ✓ Processed {$result['ics_count']} .ics attachments, {$result['events_processed']} events");

        // Mark as read
        $message->setFlag('Seen');
        
        // Move to processed folder for 7-day archival
        $processedFolder = config('inbound_email.imap.processed_folder');
        if ($processedFolder) {
            try {
                $message->move($processedFolder);
                $this->info("  ✓ Email moved to '{$processedFolder}'");
            } catch (\Exception $e) {
                $this->warn("Could not move email to '{$processedFolder}': " . $e->getMessage());
            }
        }
    }
}

