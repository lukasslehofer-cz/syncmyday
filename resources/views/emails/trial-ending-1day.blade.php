@extends('emails.layout')

@section('title', __('emails.trial_ending_1day_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_intro') }}
    </p>
    
    <!-- Warning Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 4px;">
                <p style="margin: 0; color: #92400e; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    <strong>⏰ {{ __('emails.trial_ending_1day_reminder', ['date' => $user->subscription_ends_at->isoFormat('LL')]) }}</strong>
                </p>
            </td>
        </tr>
    </table>
    
    <p style="margin: 20px 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_action_needed') }}
    </p>
    
    <!-- Pricing Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 8px; padding: 24px;">
                <p style="margin: 0 0 10px 0; font-size: 18px; color: #065f46; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.trial_pricing_title') }}
                </p>
                <p style="margin: 0; font-size: 32px; color: #059669; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    €29 / {{ __('emails.per_year') }}
                </p>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #047857; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.trial_pricing_details') }}
                </p>
            </td>
        </tr>
    </table>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <a href="{{ route('billing') }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.trial_activate_now_button') }}
                </a>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_what_happens') }}
    </h3>
    
    <p style="margin: 0 0 8px 0; color: #1f2937; font-size: 16px; font-weight: 600; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_if_subscribe') }}
    </p>
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_if_subscribe_text') }}
    </p>
    
    <p style="margin: 20px 0 8px 0; color: #1f2937; font-size: 16px; font-weight: 600; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_if_not_subscribe') }}
    </p>
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_if_not_subscribe_text') }}
    </p>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_1day_thanks') }}
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.welcome_team_name') }}
    </p>
@endsection
