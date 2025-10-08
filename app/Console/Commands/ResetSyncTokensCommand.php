<?php

namespace App\Console\Commands;

use App\Models\WebhookSubscription;
use Illuminate\Console\Command;

class ResetSyncTokensCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:reset-tokens 
                            {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset sync tokens to force a full sync on next run';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will reset all sync tokens and force a full re-sync. Continue?')) {
                $this->info('Cancelled.');
                return Command::SUCCESS;
            }
        }
        
        $this->info('🔄 Resetting sync tokens...');
        
        $count = WebhookSubscription::whereNotNull('sync_token')
            ->update(['sync_token' => null]);
        
        $this->info("✅ Reset {$count} sync token(s)");
        $this->info('Next sync will be a full sync with the new time range.');
        
        return Command::SUCCESS;
    }
}

