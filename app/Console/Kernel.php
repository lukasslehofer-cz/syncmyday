<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * 
     * NOTE: This scheduler is NOT used on shared hosting (SyncMyDay.cz).
     * Shared hosting uses standalone PHP cron files in /public/cron-*.php instead.
     * 
     * This scheduler is only for:
     * - Local development
     * - VPS/dedicated servers with Laravel scheduler support
     * 
     * For production shared hosting cron setup, see: documentation/SHARED_HOSTING_SETUP.md
     */
    protected function schedule(Schedule $schedule): void
    {
        // NOTE: Calendar sync is handled by /public/cron-calendars-sync.php on shared hosting
        // This is only for local/VPS environments
        $schedule->command('calendars:sync')->everyFiveMinutes();

        // NOTE: Webhook renewal is handled by /public/cron-webhooks-renew.php on shared hosting
        $schedule->command('webhooks:renew')->everySixHours();

        // NOTE: Log cleanup and other maintenance handled by /public/cron-*.php files
        $schedule->command('logs:clean')->daily();
        $schedule->command('connections:check')->hourly();
        $schedule->command('trial:send-ending-notifications')->dailyAt('09:00');
        $schedule->command('trial:expire')->dailyAt('00:00');
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

