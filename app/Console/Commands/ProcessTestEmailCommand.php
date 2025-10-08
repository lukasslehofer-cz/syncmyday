<?php

namespace App\Console\Commands;

use App\Services\Email\EmailCalendarSyncService;
use Illuminate\Console\Command;

class ProcessTestEmailCommand extends Command
{
    protected $signature = 'email:process-test {email_token} {--file= : Path to email file}';
    protected $description = 'Process a test email for email-based calendar sync (for local development)';

    public function __construct(
        private EmailCalendarSyncService $emailSync
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $emailToken = $this->argument('email_token');
        $filePath = $this->option('file');

        if (!$filePath) {
            $this->error('Please provide --file option with path to email file');
            $this->info('Example: php artisan email:process-test abc123 --file=test-email.txt');
            return Command::FAILURE;
        }

        // Check if file exists
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        // Read email content
        $emailContent = file_get_contents($filePath);
        
        if (empty($emailContent)) {
            $this->error('Email file is empty');
            return Command::FAILURE;
        }

        $this->info("Processing email from: {$filePath}");
        $this->info("Email token: {$emailToken}");
        $this->newLine();

        try {
            // Process the email
            $result = $this->emailSync->processIncomingEmail($emailToken, $emailContent);

            if ($result['success']) {
                $this->info('✅ Email processed successfully!');
                $this->info("Events processed: {$result['events_processed']}");
                
                if (isset($result['transaction_id'])) {
                    $this->info("Transaction ID: {$result['transaction_id']}");
                }
                
                return Command::SUCCESS;
            } else {
                $this->error('❌ Email processing failed');
                $this->error("Error: {$result['error']}");
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error('❌ Exception occurred');
            $this->error($e->getMessage());
            
            if ($this->option('verbose')) {
                $this->error($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }
}

