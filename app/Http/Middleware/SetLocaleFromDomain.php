<?php

namespace App\Http\Middleware;

use App\Helpers\LocaleHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromDomain
{
    /**
     * Handle an incoming request.
     * Sets locale based on:
     * 1. User's saved preference (highest priority)
     * 2. Domain default (e.g., .cz -> cs, .sk -> sk)
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start with domain default
        $locale = LocaleHelper::getDefaultLocale();
        App::setLocale($locale);
        
        // Override with user's preference if authenticated and available for this domain
        if ($request->user()) {
            // Refresh user from database to get latest locale value
            $user = $request->user()->fresh();
            
            if ($user && $user->locale) {
                $userLocale = $user->locale;
                
                // Only use user locale if it's available for current domain
                if (LocaleHelper::isLocaleAvailable($userLocale)) {
                    App::setLocale($userLocale);
                } else {
                    // User's locale not available for this domain, reset to domain default
                    // This happens when user switches domains
                    $user->update(['locale' => $locale]);
                }
            }
        }

        return $next($request);
    }
}

