<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OAuthProxyRedirect
{
    /**
     * Handle OAuth callback proxy redirect
     * 
     * Redirects OAuth callbacks from national domains to the primary .cz domain
     * while preserving all query parameters and state.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Primary domain where OAuth apps are configured
        $primaryDomain = config('app.oauth_primary_domain', 'syncmyday.cz');
        $currentHost = $request->getHost();
        
        // If we're already on primary domain, continue normally
        if (str_contains($currentHost, $primaryDomain)) {
            return $next($request);
        }
        
        // Check if this is an OAuth callback route
        $isOAuthCallback = $request->is('oauth/*/callback') || $request->is('auth/*/callback');
        
        if ($isOAuthCallback) {
            // Build redirect URL to primary domain
            $protocol = $request->secure() ? 'https' : 'http';
            $path = $request->path();
            $queryString = $request->getQueryString();
            
            $redirectUrl = "{$protocol}://{$primaryDomain}/{$path}";
            if ($queryString) {
                $redirectUrl .= "?{$queryString}";
            }
            
            // Store the original domain in session for post-auth redirect
            session(['oauth_original_domain' => $currentHost]);
            
            return redirect($redirectUrl);
        }
        
        return $next($request);
    }
}

