<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredGracePeriods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grace-period:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired grace periods and suspend subscriptions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired grace periods...');

        // Find users with expired grace period who haven't been notified yet
        $expiredGracePeriods = User::where('subscription_tier', 'pro')
            ->whereNotNull('grace_period_ends_at')
            ->where('grace_period_ends_at', '<=', now())
            ->where(function($query) {
                // Only process if subscription has actually ended
                $query->whereNull('subscription_ends_at')
                      ->orWhere('subscription_ends_at', '<=', now());
            })
            ->get();

        $this->info("Found {$expiredGracePeriods->count()} expired grace periods");

        $suspended = 0;
        foreach ($expiredGracePeriods as $user) {
            try {
                $this->line("Processing user: {$user->email} (grace period ended: {$user->grace_period_ends_at->format('Y-m-d H:i')})");
                
                // Clear grace period (it's expired now)
                $user->update([
                    'grace_period_ends_at' => null,
                ]);
                
                Log::warning('Grace period expired - subscription suspended', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'grace_period_ended_at' => $user->grace_period_ends_at,
                ]);

                $this->line("✓ Suspended user (grace period expired): {$user->email}");
                $suspended++;

                // Send subscription suspended notification email
                try {
                    \Mail::to($user->email)->send(new \App\Mail\SubscriptionSuspendedMail($user));
                    $this->line("  ✉️  Subscription suspended email sent");
                    
                    Log::info('Subscription suspended email sent', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);
                } catch (\Exception $emailError) {
                    $this->error("  ✗ Failed to send subscription suspended email: {$emailError->getMessage()}");
                    Log::error('Failed to send subscription suspended email', [
                        'user_id' => $user->id,
                        'error' => $emailError->getMessage(),
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to process expired grace period for {$user->email}: {$e->getMessage()}");
                Log::error('Failed to process expired grace period', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("Finished! Total grace periods expired: {$suspended}");

        return Command::SUCCESS;
    }
}
