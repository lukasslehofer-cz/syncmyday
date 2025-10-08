<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromDomain
{
    /**
     * Handle an incoming request.
     * Sets locale based on domain (e.g., .cz -> cs, .sk -> sk)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $domainLocales = config('app.domain_locales', []);
        
        // Try to match full host or domain
        foreach ($domainLocales as $domain => $locale) {
            if (str_contains($host, $domain)) {
                App::setLocale($locale);
                break;
            }
        }
        
        // Or use user's saved preference if authenticated
        if ($request->user() && $request->user()->locale) {
            App::setLocale($request->user()->locale);
        }

        return $next($request);
    }
}

