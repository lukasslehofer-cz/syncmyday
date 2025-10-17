<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
    /**
     * Show feedback form (for deleted account feedback)
     * Requires signed URL with email and name parameters
     */
    public function show(Request $request)
    {
        // Validate signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired feedback link.');
        }

        // Get user info from signed URL
        $userEmail = $request->query('email');
        $userName = $request->query('name');

        if (!$userEmail || !$userName) {
            abort(400, 'Missing required parameters.');
        }

        return view('feedback', [
            'userEmail' => $userEmail,
            'userName' => $userName,
        ]);
    }

    /**
     * Send feedback email
     * Validates signed URL to prevent spam
     */
    public function send(Request $request)
    {
        // Validate signed URL
        if (!$request->hasValidSignature()) {
            return redirect()->route('home')->with('error', __('messages.invalid_feedback_link'));
        }

        // Get user info from signed URL (not from form input)
        $userEmail = $request->query('email');
        $userName = $request->query('name');

        if (!$userEmail || !$userName) {
            abort(400, 'Missing required parameters.');
        }

        $validated = $request->validate([
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
                'userName' => $userName,
                'userEmail' => $userEmail,
                'reason' => $validated['reason'],
                'reasonLabel' => $reasonLabels[$validated['reason']] ?? $validated['reason'],
                'userMessage' => $validated['message'] ?? null,
            ], function ($message) use ($userEmail, $userName, $validated, $supportEmail) {
                $message->to($supportEmail)
                    ->replyTo($userEmail, $userName)
                    ->subject('Account Deletion Feedback: ' . $validated['reason']);
            });

            Log::info('Feedback received', [
                'email' => $userEmail,
                'reason' => $validated['reason'],
            ]);

            // Preserve signed URL parameters in redirect
            return redirect()->route('feedback', [
                'email' => $userEmail,
                'name' => $userName,
                'signature' => $request->query('signature'),
                'expires' => $request->query('expires'),
            ])->with('success', __('messages.feedback_success'));
        } catch (\Exception $e) {
            Log::error('Feedback form error: ' . $e->getMessage());
            
            // Preserve signed URL parameters in redirect
            return redirect()->route('feedback', [
                'email' => $userEmail,
                'name' => $userName,
                'signature' => $request->query('signature'),
                'expires' => $request->query('expires'),
            ])->with('error', __('messages.feedback_error'));
        }
    }
}

