<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     * Checks if user has an active subscription for pro features.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Check if user has active subscription (including trial)
        if (!$user->hasActiveSubscription()) {
            return redirect()
                ->route('billing')
                ->with('error', __('messages.subscription_required'));
        }

        return $next($request);
    }
}

