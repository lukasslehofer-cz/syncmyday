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
        // Sync calendars every 5 minutes (for localhost without webhooks)
        // In production with webhooks, this serves as a backup sync
        $schedule->command('calendars:sync')->everyFiveMinutes();

        // Renew webhook subscriptions before they expire (every 6 hours)
        $schedule->command('webhooks:renew')->everySixHours();

        // Clean up old sync logs (keep last 30 days)
        $schedule->command('logs:clean')->daily();

        // Check for stale connections and notify users
        $schedule->command('connections:check')->hourly();
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

