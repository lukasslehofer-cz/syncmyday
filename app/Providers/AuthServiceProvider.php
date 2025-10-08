<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Admin gate
        Gate::define('admin', function ($user) {
            return $user->is_admin;
        });

        // Check if user can create more sync rules based on subscription
        Gate::define('create-sync-rule', function ($user) {
            if ($user->subscription_tier === 'pro') {
                return true;
            }
            
            // Free tier: max 1 rule
            return $user->syncRules()->count() < 1;
        });
    }
}

