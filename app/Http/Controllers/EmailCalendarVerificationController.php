<?php

namespace App\Http\Controllers;

use App\Models\EmailCalendarConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EmailCalendarVerificationController extends Controller
{
    /**
     * Display the email verification notice.
     */
    public function notice(EmailCalendarConnection $emailCalendar)
    {
        // Check if user owns this email calendar
        if ($emailCalendar->user_id !== auth()->id()) {
            abort(403);
        }

        return $emailCalendar->hasVerifiedTargetEmail()
            ? redirect()->route('email-calendars.show', $emailCalendar)
            : view('email-calendars.verify-notice', compact('emailCalendar'));
    }

    /**
     * Mark the email calendar's target email address as verified.
     */
    public function verify(Request $request, $id)
    {
        $emailCalendar = EmailCalendarConnection::findOrFail($id);

        // Verify the hash
        if (!hash_equals(
            (string) $request->route('hash'),
            sha1($emailCalendar->target_email)
        )) {
            Log::warning('Email calendar verification failed - invalid hash', [
                'email_calendar_id' => $id,
                'ip' => $request->ip(),
            ]);
            
            abort(403, 'Invalid verification link');
        }

        // Verify the signature
        if (!$request->hasValidSignature()) {
            Log::warning('Email calendar verification failed - invalid signature', [
                'email_calendar_id' => $id,
                'ip' => $request->ip(),
            ]);
            
            abort(403, 'Verification link has expired');
        }

        if ($emailCalendar->hasVerifiedTargetEmail()) {
            return redirect()->route('email-calendars.show', $emailCalendar)
                ->with('info', __('messages.email_calendar_already_verified'));
        }

        if ($emailCalendar->markTargetEmailAsVerified()) {
            Log::info('Email calendar target email verified', [
                'email_calendar_id' => $emailCalendar->id,
                'target_email' => $emailCalendar->target_email,
                'user_id' => $emailCalendar->user_id,
            ]);
        }

        return redirect()->route('email-calendars.verification.success', $emailCalendar)
            ->with('verified', true);
    }

    /**
     * Display the email verification success page.
     */
    public function success(EmailCalendarConnection $emailCalendar)
    {
        // Check if user owns this email calendar
        if ($emailCalendar->user_id !== auth()->id()) {
            abort(403);
        }

        return view('email-calendars.verify-success', compact('emailCalendar'));
    }

    /**
     * Resend the email verification notification.
     */
    public function resend(Request $request, EmailCalendarConnection $emailCalendar)
    {
        // Check if user owns this email calendar
        if ($emailCalendar->user_id !== auth()->id()) {
            abort(403);
        }

        if ($emailCalendar->hasVerifiedTargetEmail()) {
            return redirect()->route('email-calendars.show', $emailCalendar)
                ->with('info', __('messages.email_calendar_already_verified'));
        }

        $emailCalendar->sendTargetEmailVerificationNotification();

        Log::info('Email calendar verification email resent', [
            'email_calendar_id' => $emailCalendar->id,
            'target_email' => $emailCalendar->target_email,
            'user_id' => auth()->id(),
        ]);

        return back()->with('status', __('emails.verification_link_sent'));
    }
}

