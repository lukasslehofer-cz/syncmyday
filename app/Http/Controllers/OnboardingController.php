<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    /**
     * Start onboarding wizard
     */
    public function start()
    {
        // Step 1: Welcome
        return view('onboarding.welcome');
    }

    /**
     * Step 2: Connect calendars
     */
    public function connectCalendars()
    {
        $user = auth()->user();
        $connections = $user->calendarConnections;
        $emailConnections = $user->emailCalendarConnections;
        
        return view('onboarding.connect-calendars', compact('connections', 'emailConnections'));
    }

    /**
     * Step 3: Create first rule
     */
    public function createRule()
    {
        $user = auth()->user();
        
        // Get both API connections and email connections
        $apiConnections = $user->calendarConnections()
            ->where('status', 'active')
            ->get();
        $emailConnections = $user->emailCalendarConnections()->get();
        
        $totalConnections = $apiConnections->count() + $emailConnections->count();

        if ($totalConnections < 2) {
            return redirect()->route('onboarding.connect-calendars')
                ->with('warning', __('messages.need_two_calendars'));
        }

        // Pass API connections for rule creation
        $connections = $apiConnections;
        return view('onboarding.create-rule', compact('connections'));
    }

    /**
     * Complete onboarding
     */
    public function complete()
    {
        auth()->user()->update(['onboarding_completed' => true]);
        
        return redirect()->route('dashboard')
            ->with('success', __('messages.onboarding_complete'));
    }
    
    /**
     * Dismiss onboarding progress bar (permanently)
     */
    public function dismissProgress(Request $request)
    {
        // Mark onboarding as completed with timestamp (won't show again)
        auth()->user()->update(['onboarding_completed_at' => now()]);
        
        // Also dismiss for current session
        $request->session()->put('onboarding_progress_dismissed', true);
        
        return redirect()->back();
    }
}

