<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanTestStripeDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:clean-test-data {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean test Stripe customer IDs from database (for migration from test to live mode)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for users with Stripe test data...');

        // Find users with stripe_customer_id set
        $usersWithStripeData = User::whereNotNull('stripe_customer_id')->get();

        if ($usersWithStripeData->isEmpty()) {
            $this->info('No users with Stripe data found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$usersWithStripeData->count()} users with Stripe customer IDs:");
        
        foreach ($usersWithStripeData as $user) {
            $this->line("  - User #{$user->id} ({$user->email}): {$user->stripe_customer_id}");
        }

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to remove these Stripe IDs from the database?')) {
                $this->info('Cancelled.');
                return Command::SUCCESS;
            }
        }

        $cleaned = 0;
        foreach ($usersWithStripeData as $user) {
            $user->update([
                'stripe_customer_id' => null,
                'stripe_subscription_id' => null,
            ]);
            $cleaned++;
            $this->line("  ✓ Cleaned user #{$user->id} ({$user->email})");
        }

        $this->info("Successfully cleaned {$cleaned} users.");
        $this->info('Users can now register their payment methods with live Stripe keys.');

        return Command::SUCCESS;
    }
}

