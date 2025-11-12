<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\ClientManager;
use Carbon\Carbon;

/**
 * Clean old processed inbound emails
 * 
 * This command:
 * 1. Connects to IMAP mailbox
 * 2. Opens "Processed" folder
 * 3. Deletes emails older than configured retention period (default 7 days)
 * 
 * Runs automatically via Laravel scheduler (daily).
 * Configured in app/Console/Kernel.php
 */
class CleanOldInboundEmailsCommand extends Command
{
    protected $signature = 'app:clean-old-inbound-emails
                            {--dry-run : Preview without deleting}
                            {--days= : Override retention days from config}';

    protected $description = 'Delete processed inbound emails older than retention period';

    public function handle(): int
    {
        if (!config('inbound_email.enabled')) {
            $this->warn('Inbound email processing is disabled. Set INBOUND_EMAIL_ENABLED=true in .env');
            return self::FAILURE;
        }

        $processedFolder = config('inbound_email.imap.processed_folder');
        
        if (!$processedFolder) {
            $this->warn('No processed folder configured. Set INBOUND_EMAIL_PROCESSED_FOLDER in .env');
            return self::FAILURE;
        }

        $retentionDays = $this->option('days') ?? config('inbound_email.imap.retention_days', 7);
        $cutoffDate = Carbon::now()->subDays($retentionDays);

        $this->info("Cleaning emails older than {$retentionDays} days (before {$cutoffDate->format('Y-m-d H:i:s')})");

        try {
            // Connect to IMAP
            $client = $this->connectImap();
            
            if (!$client) {
                $this->error('Failed to connect to IMAP server');
                return self::FAILURE;
            }

            // Get processed folder
            $folder = $client->getFolder($processedFolder);
            
            if (!$folder) {
                $this->error("Folder '{$processedFolder}' not found");
                return self::FAILURE;
            }

            $this->info("Connected to folder: {$processedFolder}");

            // Get all messages in processed folder
            $messages = $folder->query()->all()->get();
            
            if ($messages->count() === 0) {
                $this->info('No emails in processed folder');
                return self::SUCCESS;
            }

            $this->info("Found {$messages->count()} emails in processed folder");

            $deleted = 0;
            $kept = 0;
            $failed = 0;

            foreach ($messages as $message) {
                try {
                    $messageDate = $message->getDate();
                    
                    if ($messageDate < $cutoffDate) {
                        $subject = $message->getSubject();
                        $dateStr = $messageDate->format('Y-m-d H:i:s');
                        
                        if ($this->option('dry-run')) {
                            $this->info("Would delete: [{$dateStr}] {$subject}");
                            $deleted++;
                        } else {
                            $message->delete();
                            $this->info("Deleted: [{$dateStr}] {$subject}");
                            $deleted++;
                        }
                    } else {
                        $kept++;
                    }
                } catch (\Exception $e) {
                    $failed++;
                    $this->error("Failed to process email: " . $e->getMessage());
                    Log::error('Failed to delete old inbound email', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Expunge deleted messages (permanently remove them)
            if (!$this->option('dry-run') && $deleted > 0) {
                try {
                    $folder->expunge();
                    $this->info("Expunged deleted messages");
                } catch (\Exception $e) {
                    $this->warn("Could not expunge: " . $e->getMessage());
                }
            }

            $this->newLine();
            $this->info("Summary:");
            $this->info("  Deleted: {$deleted}");
            $this->info("  Kept: {$kept}");
            if ($failed > 0) {
                $this->warn("  Failed: {$failed}");
            }
            
            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('IMAP processing error: ' . $e->getMessage());
            Log::error('IMAP cleanup error', [
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
}

