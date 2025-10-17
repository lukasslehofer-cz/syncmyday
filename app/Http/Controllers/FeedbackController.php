<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Show feedback form (for deleted account feedback)
     */
    public function show()
    {
        return view('feedback');
    }

    /**
     * Send feedback email
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'reason' => 'required|string|in:not_using,too_expensive,missing_features,technical_issues,found_alternative,other',
            'message' => 'nullable|string|max:5000',
        ]);

        try {
            // Determine support email based on locale
            $locale = app()->getLocale();
            $supportEmail = $locale === 'cs' ? 'support@syncmyday.cz' : 'support@syncmyday.eu';

            // Map reason to human-readable text
            $reasonLabels = [
                'not_using' => __('messages.feedback_reason_not_using'),
                'too_expensive' => __('messages.feedback_reason_too_expensive'),
                'missing_features' => __('messages.feedback_reason_missing_features'),
                'technical_issues' => __('messages.feedback_reason_technical_issues'),
                'found_alternative' => __('messages.feedback_reason_found_alternative'),
                'other' => __('messages.feedback_reason_other'),
            ];

            // Send email
            Mail::send('emails.feedback', [
                'userName' => $validated['name'],
                'userEmail' => $validated['email'],
                'reason' => $validated['reason'],
                'reasonLabel' => $reasonLabels[$validated['reason']] ?? $validated['reason'],
                'userMessage' => $validated['message'] ?? null,
            ], function ($message) use ($validated, $supportEmail) {
                $message->to($supportEmail)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('Account Deletion Feedback: ' . $validated['reason']);
            });

            Log::info('Feedback received', [
                'email' => $validated['email'],
                'reason' => $validated['reason'],
            ]);

            return redirect()->route('feedback')->with('success', __('messages.feedback_success'));
        } catch (\Exception $e) {
            Log::error('Feedback form error: ' . $e->getMessage());
            return redirect()->route('feedback')->with('error', __('messages.feedback_error'));
        }
    }
}

