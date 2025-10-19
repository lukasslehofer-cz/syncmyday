<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// API routes can be added here for mobile apps or external integrations
// For MVP, we don't need API routes as we're using web routes

// Timezone detection for OAuth login
Route::post('/set-timezone', function(\Illuminate\Http\Request $request) {
    $timezone = $request->input('timezone', 'UTC');
    
    // Validate timezone
    if (!in_array($timezone, timezone_identifiers_list())) {
        $timezone = 'UTC';
    }
    
    // Generate unique key and store in cache (valid for 15 minutes)
    $timezoneKey = \Illuminate\Support\Str::random(40);
    \Illuminate\Support\Facades\Cache::put("timezone_{$timezoneKey}", $timezone, now()->addMinutes(15));
    
    \Illuminate\Support\Facades\Log::info('Timezone stored in cache for OAuth', [
        'timezone' => $timezone,
        'key' => substr($timezoneKey, 0, 8) . '...',
    ]);
    
    return response()->json([
        'success' => true,
        'timezone' => $timezone,
        'timezone_key' => $timezoneKey
    ]);
})->name('api.set-timezone');

