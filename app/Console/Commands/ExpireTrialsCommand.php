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

        // Process initial trial expired emails (send once when trial expires)
        $this->sendInitialTrialExpiredEmails();
        
        // Process 5-day reminder emails
        $this->sendTrialExpiredReminderEmails();

        return Command::SUCCESS;
    }
    
    /**
     * Send initial trial expired email (once when trial expires)
     */
    private function sendInitialTrialExpiredEmails(): void
    {
        // Find users in trial (pro tier) without Stripe subscription, expired subscription_ends_at
        // and who haven't received the initial email yet
        $expiredTrials = User::where('subscription_tier', 'pro')
            ->whereNotNull('subscription_ends_at')
            ->where('subscription_ends_at', '<=', now())
            ->whereNull('stripe_subscription_id') // No active Stripe subscription
            ->whereNull('trial_expired_email_sent_at') // Haven't sent initial email yet
            ->get();

        $this->info("Found {$expiredTrials->count()} users needing initial trial expired email");

        $sent = 0;
        foreach ($expiredTrials as $user) {
            try {
                $this->line("Processing user: {$user->email} (trial ended: {$user->subscription_ends_at->format('Y-m-d')})");
                
                Log::info('Trial expired - sending initial email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'trial_ended_at' => $user->subscription_ends_at,
                ]);

                // Send trial expired notification email
                try {
                    \Mail::to($user->email)->send(new \App\Mail\TrialExpiredMail($user));
                    
                    // Mark email as sent
                    $user->update(['trial_expired_email_sent_at' => now()]);
                    
                    $this->line("  ✉️  Trial expired email sent");
                    $sent++;
                    
                    Log::info('Trial expired email sent', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);
                } catch (\Exception $emailError) {
                    $this->error("  ✗ Failed to send trial expired email: {$emailError->getMessage()}");
                    Log::error('Failed to send trial expired email', [
                        'user_id' => $user->id,
                        'error' => $emailError->getMessage(),
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to process expired trial for {$user->email}: {$e->getMessage()}");
                Log::error('Failed to process expired trial', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Initial trial expired emails sent: {$sent}");
    }
    
    /**
     * Send 5-day reminder email to users who received initial email 5 days ago
     */
    private function sendTrialExpiredReminderEmails(): void
    {
        // Find users who:
        // 1. Received initial email exactly 5 days ago (give or take a few hours for cron timing)
        // 2. Haven't received reminder yet
        // 3. Still don't have active subscription
        $reminderCandidates = User::where('subscription_tier', 'pro')
            ->whereNotNull('trial_expired_email_sent_at')
            ->whereNull('trial_expired_reminder_sent_at') // Haven't sent reminder yet
            ->whereNull('stripe_subscription_id') // Still no subscription
            ->whereBetween('trial_expired_email_sent_at', [
                now()->subDays(5)->subHours(12), // 5 days ago +/- 12 hours for cron timing
                now()->subDays(5)->addHours(12),
            ])
            ->get();

        $this->info("Found {$reminderCandidates->count()} users needing 5-day reminder email");

        $sent = 0;
        foreach ($reminderCandidates as $user) {
            try {
                $this->line("Sending reminder to: {$user->email}");
                
                Log::info('Sending 5-day trial expired reminder', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'initial_email_sent_at' => $user->trial_expired_email_sent_at,
                ]);

                // Send reminder email (using same mail class for now)
                try {
                    \Mail::to($user->email)->send(new \App\Mail\TrialExpiredMail($user));
                    
                    // Mark reminder as sent
                    $user->update(['trial_expired_reminder_sent_at' => now()]);
                    
                    $this->line("  ✉️  5-day reminder sent");
                    $sent++;
                    
                    Log::info('Trial expired reminder email sent', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);
                } catch (\Exception $emailError) {
                    $this->error("  ✗ Failed to send reminder email: {$emailError->getMessage()}");
                    Log::error('Failed to send trial expired reminder email', [
                        'user_id' => $user->id,
                        'error' => $emailError->getMessage(),
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder for {$user->email}: {$e->getMessage()}");
                Log::error('Failed to send trial expired reminder', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("5-day reminder emails sent: {$sent}");
    }
}

