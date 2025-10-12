<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact', [
            'formattedPrice' => \App\Helpers\PricingHelper::formatPrice(),
        ]);
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        try {
            // Determine support email based on locale
            $locale = app()->getLocale();
            $supportEmail = $locale === 'cs' ? 'support@syncmyday.cz' : 'support@syncmyday.eu';

            // Send email
            Mail::send('emails.contact', [
                'contactName' => $validated['name'],
                'contactEmail' => $validated['email'],
                'contactSubject' => $validated['subject'],
                'contactMessage' => $validated['message'],
            ], function ($message) use ($validated, $supportEmail) {
                $message->to($supportEmail)
                    ->replyTo($validated['email'], $validated['name'])
                    ->subject('Contact Form: ' . $validated['subject']);
            });

            return redirect()->route('contact')->with('success', __('messages.contact_success'));
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            return redirect()->route('contact')->with('error', __('messages.contact_error'));
        }
    }
}
