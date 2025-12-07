<?php

namespace App\Console\Commands;

use App\Mail\RenewalReminderMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:send-renewal-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send renewal reminder emails to users whose subscription renews in 3 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for users with subscription renewal in 3 days...');

        // Find users with active Stripe subscription that renews in 3 days
        $targetDate = now()->addDays(3)->toDateString();

        $users = User::where('subscription_tier', 'pro')
            ->whereNotNull('stripe_subscription_id') // Has active subscription
            ->whereNotNull('subscription_ends_at')
            ->whereDate('subscription_ends_at', $targetDate)
            ->whereNotNull('email_verified_at')
            ->get();

        $this->info("Found {$users->count()} users with subscription renewal in 3 days");

        $sent = 0;
        foreach ($users as $user) {
            try {
                // Get subscription details from Stripe
                $amount = null;
                $currency = null;
                $renewalDate = $user->subscription_ends_at->isoFormat('LL');

                if ($user->stripe_subscription_id) {
                    try {
                        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
                        $subscription = $stripe->subscriptions->retrieve($user->stripe_subscription_id);
                        
                        if ($subscription && isset($subscription->items->data[0])) {
                            $item = $subscription->items->data[0];
                            $amount = $item->price->unit_amount / 100; // Convert from cents
                            $currency = strtoupper($subscription->currency);
                        }
                    } catch (\Exception $stripeError) {
                        Log::warning('Could not fetch Stripe subscription details for renewal reminder', [
                            'user_id' => $user->id,
                            'error' => $stripeError->getMessage(),
                        ]);
                        // Continue with email anyway, just without amount
                    }
                }

                Mail::to($user->email)->send(new RenewalReminderMail(
                    $user, 
                    $amount, 
                    $currency, 
                    $renewalDate
                ));
                
                $sent++;
                
                Log::info('Sent renewal reminder email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'renewal_date' => $renewalDate,
                    'amount' => $amount,
                    'currency' => $currency,
                ]);

                $this->line("✓ Sent renewal reminder to: {$user->email}");
                
            } catch (\Exception $e) {
                Log::error('Failed to send renewal reminder email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
                
                $this->error("✗ Failed to send to {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Finished! Total renewal reminders sent: {$sent}");

        return Command::SUCCESS;
    }
}

