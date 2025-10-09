<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExpireTrialsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire trial periods for users without active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired trials...');

        // Find users in trial (pro tier) without Stripe subscription and expired subscription_ends_at
        $expiredTrials = User::where('subscription_tier', 'pro')
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', now())
            ->whereNull('stripe_subscription_id') // No active Stripe subscription
            ->get();

        $this->info("Found {$expiredTrials->count()} expired trials");

        $expired = 0;
        foreach ($expiredTrials as $user) {
            try {
                $this->line("Processing user: {$user->email} (trial ended: {$user->subscription_ends_at->format('Y-m-d')})");
                
                $user->expireTrial();
                
                Log::info('Trial expired and user downgraded', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'trial_ended_at' => $user->subscription_ends_at,
                ]);

                $this->line("✓ Expired trial for: {$user->email}");
                $expired++;

                // TODO: Send trial expired notification email
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to expire trial for {$user->email}: {$e->getMessage()}");
                Log::error('Failed to expire trial', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Finished! Total trials expired: {$expired}");

        return Command::SUCCESS;
    }
}

