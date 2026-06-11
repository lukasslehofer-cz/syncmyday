<?php

namespace App\Console\Commands;

use App\Models\SentEmail;
use Illuminate\Console\Command;

class CleanSentEmailsCommand extends Command
{
    protected $signature = 'emails:clean {--days=90 : Number of days to keep}';

    protected $description = 'Clean up old sent email records';

    public function handle()
    {
        $days = $this->option('days');

        $this->info("Cleaning up sent emails older than {$days} days...");

        $deleted = SentEmail::where('sent_at', '<', now()->subDays($days))->delete();

        $this->info("Deleted {$deleted} sent email records.");

        return 0;
    }
}
