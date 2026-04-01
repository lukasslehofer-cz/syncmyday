<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Run scheduled tasks via HTTP request
     * 
     * This endpoint allows running cron jobs via HTTP request
     * Useful for shared hosting without CLI access
     * 
     * Security: Protected by CRON_SECRET token
     */
    public function run(Request $request)
    {
        // Security check - verify secret token
        $cronSecret = config('app.cron_secret');

        if (empty($cronSecret) || $request->input('token') !== $cronSecret) {
            Log::warning('Cron endpoint unauthorized access attempt', [
                'ip' => $request->ip(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        // Run scheduled tasks
        Artisan::call('schedule:run');
        $output = Artisan::output();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Scheduled tasks executed',
            'output' => $output,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
