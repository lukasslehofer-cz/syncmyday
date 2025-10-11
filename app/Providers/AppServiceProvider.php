<?php

namespace App\Providers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
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
        \App\Models\EmailCalendarConnection::observe(\App\Observers\EmailCalendarConnectionObserver::class);

        // Register event listeners
        Event::listen(
            Verified::class,
            \App\Listeners\SendWelcomeEmail::class
        );
    }
}

