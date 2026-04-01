<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Core synchronization and maintenance tasks
        $schedule->command('calendars:sync')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('webhooks:renew')->everySixHours()->withoutOverlapping();
        $schedule->command('logs:clean')->daily();
        $schedule->command('connections:check')->hourly()->withoutOverlapping();
        $schedule->command('sync:clean-orphaned-rules --force')->dailyAt('02:30');

        // Inbound email processing
        $schedule->command('app:process-inbound-emails')->everyFiveMinutes()->withoutOverlapping();
        $schedule->command('app:clean-old-inbound-emails')->dailyAt('03:00'); // Clean old emails at 3 AM
        
        // Trial management
        $schedule->command('trial:send-ending-notifications')->dailyAt('09:00');
        $schedule->command('trial:expire')->dailyAt('00:00');
        
        // Subscription renewal reminders
        $schedule->command('subscription:send-renewal-reminders')->dailyAt('09:30');
        
        // Grace period management (for failed payments)
        $schedule->command('grace-period:check')->dailyAt('01:00');
        
        // Onboarding emails
        $schedule->command('onboarding:send-emails')->dailyAt('10:00');
        
        // Fakturoid integration
        $schedule->command('fakturoid:retry-failed')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

