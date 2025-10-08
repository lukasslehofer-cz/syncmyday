<?php

namespace App\Console\Commands;

use App\Jobs\RenewWebhookSubscriptionsJob;
use Illuminate\Console\Command;

class WebhooksRenewCommand extends Command
{
    protected $signature = 'webhooks:renew';
    protected $description = 'Renew expiring webhook subscriptions';

    public function handle()
    {
        $this->info('Dispatching webhook renewal job...');
        
        RenewWebhookSubscriptionsJob::dispatch();
        
        $this->info('Webhook renewal job dispatched.');
        return 0;
    }
}

