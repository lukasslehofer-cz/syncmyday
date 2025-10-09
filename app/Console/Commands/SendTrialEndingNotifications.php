<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingInSevenDaysMail;
use App\Mail\TrialEndingTomorrowMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTrialEndingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trial:send-ending-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email notifications to users whose trial period is ending soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for users with trial ending soon...');

        // Users with trial ending in 3 days (warning notification)
        $usersEndingIn3Days = User::where('subscription_tier', 'pro')
            ->whereNotNull('subscription_ends_at')
            ->whereDate('subscription_ends_at', now()->addDays(3)->toDateString())
            ->whereNull('stripe_subscription_id') // Haven't set up payment yet
            ->get();

        $this->info("Found {$usersEndingIn3Days->count()} users with trial ending in 3 days");

        foreach ($usersEndingIn3Days as $user) {
            try {
                Mail::to($user->email)->send(new TrialEndingInSevenDaysMail($user));
                $this->line("✓ Sent 3-day notification to: {$user->email}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$user->email}: {$e->getMessage()}");
            }
        }

        // Users with trial ending in 1 day (urgent notification)
        $usersEndingIn1Day = User::where('subscription_tier', 'pro')
            ->whereNotNull('subscription_ends_at')
            ->whereDate('subscription_ends_at', now()->addDay()->toDateString())
            ->whereNull('stripe_subscription_id') // Haven't set up payment yet
            ->get();

        $this->info("Found {$usersEndingIn1Day->count()} users with trial ending in 1 day");

        foreach ($usersEndingIn1Day as $user) {
            try {
                Mail::to($user->email)->send(new TrialEndingTomorrowMail($user));
                $this->line("✓ Sent 1-day notification to: {$user->email}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to send to {$user->email}: {$e->getMessage()}");
            }
        }

        $totalSent = $usersEndingIn3Days->count() + $usersEndingIn1Day->count();
        $this->info("Finished! Total notifications sent: {$totalSent}");

        return Command::SUCCESS;
    }
}
