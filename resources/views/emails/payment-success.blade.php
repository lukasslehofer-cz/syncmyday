@extends('emails.layout')

@section('title', __('emails.payment_success_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_intro') }}
    </p>
    
    <!-- Success Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 12px; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">✅</div>
                <p style="margin: 0 0 8px 0; font-size: 20px; color: #065f46; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_success_confirmed') }}
                </p>
                @if($amount)
                    <p style="margin: 12px 0; font-size: 32px; color: #059669; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                        €{{ number_format($amount, 2) }}
                    </p>
                @endif
                <p style="margin: 8px 0 0 0; font-size: 14px; color: #047857; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_success_receipt') }}
                </p>
            </td>
        </tr>
    </table>
    
    <h3 style="margin: 30px 0 20px 0; padding: 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_whats_next') }}
    </h3>
    
    <!-- Highlight Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #eff6ff; border-left: 4px solid #667eea; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; color: #1e40af; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    🎉 {{ __('emails.payment_success_full_access') }}
                </p>
                <p style="margin: 0; color: #1e40af; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_success_full_access_text') }}
                </p>
            </td>
        </tr>
    </table>
    
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top: 20px;">
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.payment_benefit_1') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.payment_benefit_2') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.payment_benefit_3') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.payment_benefit_4') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.payment_benefit_5') }}</td></tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_renewal_title') }}
    </h3>
    
    <!-- Renewal Info Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; color: #1e40af; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    📅 {{ __('emails.payment_success_next_payment') }}
                </p>
                <p style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    @if($nextBillingDate)
                        {{ __('emails.payment_success_next_payment_date', ['date' => $nextBillingDate]) }}
                    @else
                        {{ __('emails.payment_success_next_payment_year') }}
                    @endif
                </p>
                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_success_reminder_promise') }}
                </p>
            </td>
        </tr>
    </table>
    
    <p style="margin: 20px 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_manage_info') }}
    </p>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <a href="{{ route('billing') }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.payment_success_manage_subscription') }}
                </a>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_need_help') }}
    </h3>
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_help_text') }}
    </p>
    
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="padding: 10px 0;">
                <a href="{{ route('dashboard') }}" style="color: #667eea; text-decoration: none; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    🏠 {{ __('emails.payment_success_dashboard') }}
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0;">
                <a href="{{ route('billing') }}" style="color: #667eea; text-decoration: none; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    💳 {{ __('emails.payment_success_billing') }}
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0;">
                <a href="{{ config('app.url') }}/docs" style="color: #667eea; text-decoration: none; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    📚 {{ __('emails.welcome_documentation') }}
                </a>
            </td>
        </tr>
    </table>
    
    <p style="margin: 30px 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_success_thank_you') }}
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.welcome_team_name') }}
    </p>
@endsection
