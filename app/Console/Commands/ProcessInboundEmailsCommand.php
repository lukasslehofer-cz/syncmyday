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
 * 
 * Should be run via cron every minute:
 * * * * * * php artisan app:process-inbound-emails
 * 
 * Or via HTTP: https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_SECRET
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
        foreach ($message->getTo() as $to) {
            $toAddresses[] = strtolower($to->mail);
        }
        
        // Get CC recipients
        foreach ($message->getCc() as $cc) {
            $toAddresses[] = strtolower($cc->mail);
        }

        $emailDomain = config('app.email_domain');
        
        // Find matching email calendar token
        $token = null;
        foreach ($toAddresses as $address) {
            if (str_ends_with($address, '@' . $emailDomain)) {
                $token = explode('@', $address)[0];
                break;
            }
        }

        if (!$token) {
            $this->warn("No valid recipient found in email: {$message->getSubject()}");
            $message->setFlag('Seen');
            return;
        }

        // Find email calendar connection
        $connection = EmailCalendarConnection::findByToken($token);
        
        if (!$connection) {
            $this->warn("Email calendar not found for token: {$token}");
            $message->setFlag('Seen');
            return;
        }

        // Get raw email
        $rawEmail = $message->getRawBody();

        if ($this->option('dry-run')) {
            $this->info("Would process email for: {$connection->name} ({$connection->email_address})");
            $this->info("  Subject: {$message->getSubject()}");
            $this->info("  From: {$message->getFrom()[0]->mail}");
            return;
        }

        // Process email
        $this->info("Processing email for: {$connection->name}");
        $this->info("  Subject: {$message->getSubject()}");
        
        $result = $this->syncService->processIncomingEmail($token, $rawEmail);

        $this->info("  ✓ Processed {$result['ics_count']} .ics attachments, {$result['events_processed']} events");

        // Mark as read
        $message->setFlag('Seen');
        
        // Optionally move to processed folder
        $processedFolder = config('inbound_email.imap.processed_folder');
        if ($processedFolder) {
            try {
                $message->move($processedFolder);
            } catch (\Exception $e) {
                $this->warn("Could not move email to '{$processedFolder}': " . $e->getMessage());
            }
        }
    }
}

