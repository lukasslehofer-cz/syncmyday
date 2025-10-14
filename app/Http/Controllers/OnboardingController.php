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
        $connections = auth()->user()->calendarConnections;
        
        return view('onboarding.connect-calendars', compact('connections'));
    }

    /**
     * Step 3: Create first rule
     */
    public function createRule()
    {
        $connections = auth()->user()
            ->calendarConnections()
            ->where('status', 'active')
            ->get();

        if ($connections->count() < 2) {
            return redirect()->route('onboarding.connect-calendars')
                ->with('warning', __('messages.need_two_calendars'));
        }

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
     * Dismiss onboarding progress bar (for current session)
     */
    public function dismissProgress(Request $request)
    {
        $request->session()->put('onboarding_progress_dismissed', true);
        
        return redirect()->back();
    }
}

