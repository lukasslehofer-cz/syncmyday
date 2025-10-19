@extends('emails.layout')

@section('title', __('emails.renewal_reminder_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_intro') }}
    </p>
    
    <!-- Reminder Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #eff6ff; border: 2px solid #3b82f6; border-radius: 12px; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">📅</div>
                <p style="margin: 0 0 8px 0; font-size: 20px; color: #1e40af; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.renewal_reminder_title') }}
                </p>
                <p style="margin: 12px 0 4px 0; font-size: 14px; color: #1e40af; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.renewal_reminder_date') }}
                </p>
                @if($renewalDate)
                    <p style="margin: 0 0 12px 0; font-size: 24px; color: #1e3a8a; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                        {{ $renewalDate }}
                    </p>
                @endif
                @if($amount && $currency)
                    <p style="margin: 12px 0 4px 0; font-size: 14px; color: #1e40af; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                        {{ __('emails.renewal_reminder_amount') }}
                    </p>
                    <p style="margin: 0; font-size: 32px; color: #2563eb; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                        {{ $currency }} {{ number_format($amount, 2) }}
                    </p>
                @endif
            </td>
        </tr>
    </table>
    
    <h3 style="margin: 30px 0 20px 0; padding: 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_what_happens') }}
    </h3>
    
    <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_auto_renewal', ['date' => $renewalDate]) }}
    </p>
    
    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #f0fdf4; border-left: 4px solid #10b981; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 12px 0; color: #065f46; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.renewal_reminder_what_you_keep') }}
                </p>
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr><td style="padding: 6px 0; padding-left: 10px; color: #047857; font-size: 15px; line-height: 22px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #10b981; font-weight: bold; margin-right: 8px;">✓</span>{{ __('emails.renewal_reminder_benefit_1') }}</td></tr>
                    <tr><td style="padding: 6px 0; padding-left: 10px; color: #047857; font-size: 15px; line-height: 22px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #10b981; font-weight: bold; margin-right: 8px;">✓</span>{{ __('emails.renewal_reminder_benefit_2') }}</td></tr>
                    <tr><td style="padding: 6px 0; padding-left: 10px; color: #047857; font-size: 15px; line-height: 22px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #10b981; font-weight: bold; margin-right: 8px;">✓</span>{{ __('emails.renewal_reminder_benefit_3') }}</td></tr>
                    <tr><td style="padding: 6px 0; padding-left: 10px; color: #047857; font-size: 15px; line-height: 22px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #10b981; font-weight: bold; margin-right: 8px;">✓</span>{{ __('emails.renewal_reminder_benefit_4') }}</td></tr>
                    <tr><td style="padding: 6px 0; padding-left: 10px; color: #047857; font-size: 15px; line-height: 22px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #10b981; font-weight: bold; margin-right: 8px;">✓</span>{{ __('emails.renewal_reminder_benefit_5') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <!-- No Action Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; color: #92400e; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    ✨ {{ __('emails.renewal_reminder_no_action_title') }}
                </p>
                <p style="margin: 0; color: #92400e; font-size: 15px; line-height: 22px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.renewal_reminder_no_action_text') }}
                </p>
            </td>
        </tr>
    </table>
    
    <h3 style="margin: 30px 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_manage_title') }}
    </h3>
    
    <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_manage_text') }}
    </p>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 10px 0 30px 0;">
                <a href="{{ route('billing') }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.renewal_reminder_manage_button') }}
                </a>
            </td>
        </tr>
    </table>
    
    <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px; line-height: 20px; font-style: italic; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        💡 {{ __('emails.renewal_reminder_cancel_info', ['date' => $renewalDate]) }}
    </p>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <p style="margin: 0 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.renewal_reminder_thank_you') }}
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.welcome_team_name') }}
    </p>
@endsection

