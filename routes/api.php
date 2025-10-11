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
    
    // Store in session for OAuth callback
    session(['detected_timezone' => $timezone]);
    
    return response()->json(['success' => true, 'timezone' => $timezone]);
})->name('api.set-timezone');

