<?php

namespace App\Providers;

use App\Listeners\LogSentEmail;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        \App\Models\User::observe(\App\Observers\UserObserver::class);
        \App\Models\CalendarConnection::observe(\App\Observers\CalendarConnectionObserver::class);
        \App\Models\SyncRule::observe(\App\Observers\SyncRuleObserver::class);
        \App\Models\EmailCalendarConnection::observe(\App\Observers\EmailCalendarConnectionObserver::class);

        // Record sent system/transactional emails for the admin overview
        Event::listen(MessageSent::class, [LogSentEmail::class, 'handle']);

        // Note: SendWelcomeEmail listener removed - welcome email now sent immediately upon registration
    }
}
