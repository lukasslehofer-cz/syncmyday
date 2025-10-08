<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register calendar service interfaces
        $this->app->singleton(\App\Services\Calendar\GoogleCalendarService::class);
        $this->app->singleton(\App\Services\Calendar\MicrosoftCalendarService::class);
        $this->app->singleton(\App\Services\Encryption\TokenEncryptionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Force HTTPS in production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register model observers
        \App\Models\CalendarConnection::observe(\App\Observers\CalendarConnectionObserver::class);
        \App\Models\SyncRule::observe(\App\Observers\SyncRuleObserver::class);
    }
}

