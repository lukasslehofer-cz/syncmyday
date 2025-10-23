<?php

namespace App\Console\Commands;

use App\Mail\OnboardingCalendarSetupMail;
use App\Mail\OnboardingRulesGuideMail;
use App\Mail\OnboardingUpgradeGuideMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOnboardingEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onboarding:send-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send onboarding emails to users in trial period';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting onboarding email campaign...');

        // Day 2: Calendar Setup Email
        $this->sendCalendarSetupEmails();

        // Day 7: Rules Guide Email
        $this->sendRulesGuideEmails();

        // Day 14: Upgrade Guide Email
        $this->sendUpgradeGuideEmails();

        $this->info('Onboarding email campaign completed!');

        return 0;
    }

    /**
     * Send calendar setup email to users on day 2 of trial
     * Only send to users with less than 2 calendar connections
     */
    private function sendCalendarSetupEmails()
    {
        $targetDate = now()->subDays(2)->startOfDay();

        $users = User::where('subscription_tier', 'pro')
            ->whereNull('stripe_subscription_id') // Only trial users
            ->whereDate('created_at', $targetDate)
            ->whereNotNull('email_verified_at')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            // Check if user has less than 2 calendars
            $calendarCount = $user->calendarConnections()->count() + 
                           $user->emailCalendarConnections()->count();
            
            if ($calendarCount >= 2) {
                continue; // Skip users who already have 2+ calendars
            }
            
            try {
                Mail::to($user->email)->send(new OnboardingCalendarSetupMail($user));
                $count++;
                
                Log::info('Sent calendar setup onboarding email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'calendar_count' => $calendarCount,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send calendar setup onboarding email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("  - Sent calendar setup emails: {$count}");
    }

    /**
     * Send rules guide email to users on day 7 of trial
     */
    private function sendRulesGuideEmails()
    {
        $targetDate = now()->subDays(7)->startOfDay();

        $users = User::where('subscription_tier', 'pro')
            ->whereNull('stripe_subscription_id') // Only trial users
            ->whereDate('created_at', $targetDate)
            ->whereNotNull('email_verified_at')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            try {
                Mail::to($user->email)->send(new OnboardingRulesGuideMail($user));
                $count++;
                
                Log::info('Sent rules guide onboarding email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send rules guide onboarding email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("  - Sent rules guide emails: {$count}");
    }

    /**
     * Send upgrade guide email to users on day 14 of trial
     * Only send to users without payment method
     */
    private function sendUpgradeGuideEmails()
    {
        $targetDate = now()->subDays(14)->startOfDay();

        $users = User::where('subscription_tier', 'pro')
            ->whereNull('stripe_subscription_id') // Only trial users without active subscription
            ->whereDate('created_at', $targetDate)
            ->whereNotNull('email_verified_at')
            ->get();

        $count = 0;
        foreach ($users as $user) {
            // Skip users who already have payment method set up
            // (they would have stripe_subscription_id if they completed payment)
            if ($user->stripe_subscription_id) {
                continue;
            }
            
            try {
                Mail::to($user->email)->send(new OnboardingUpgradeGuideMail($user));
                $count++;
                
                Log::info('Sent upgrade guide onboarding email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send upgrade guide onboarding email', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("  - Sent upgrade guide emails: {$count}");
    }
}

