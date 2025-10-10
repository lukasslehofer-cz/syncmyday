@extends('emails.layout')

@section('title', __('emails.trial_ending_7days_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_7days_intro') }}
    </p>
    
    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #eff6ff; border-left: 4px solid #667eea; padding: 20px; border-radius: 4px;">
                <p style="margin: 0; color: #1e40af; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    <strong>{{ __('emails.trial_ending_7days_reminder', ['days' => 7, 'date' => $user->subscription_ends_at->isoFormat('LL')]) }}</strong>
                </p>
            </td>
        </tr>
    </table>
    
    <p style="margin: 20px 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_benefits_intro') }}
    </p>
    
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_benefit_1') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_benefit_2') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_benefit_3') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_benefit_4') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_benefit_5') }}</td></tr>
    </table>
    
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
                    {{ __('emails.trial_pricing_note') }}
                </p>
            </td>
        </tr>
    </table>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <a href="{{ route('billing') }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.trial_setup_payment_button') }}
                </a>
            </td>
        </tr>
    </table>
    
    <p style="margin: 30px 0 10px 0; font-size: 14px; color: #6b7280; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_footer_1') }}
    </p>
    <p style="margin: 0; font-size: 14px; color: #6b7280; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_ending_footer_2') }}
    </p>
@endsection
