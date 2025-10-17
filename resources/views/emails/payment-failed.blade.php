@extends('emails.layout')

@section('title', __('emails.payment_failed_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_intro') }}
    </p>
    
    <!-- Warning Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">⚠️</div>
                <p style="margin: 0 0 8px 0; font-size: 20px; color: #991b1b; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_failed_title') }}
                </p>
                @if($amount)
                    <p style="margin: 12px 0; font-size: 28px; color: #dc2626; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                        €{{ number_format($amount, 2) }}
                    </p>
                @endif
                <p style="margin: 8px 0 0 0; font-size: 14px; color: #991b1b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_failed_amount_due') }}
                </p>
            </td>
        </tr>
    </table>
    
    <h3 style="margin: 30px 0 20px 0; padding: 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_why') }}
    </h3>
    
    <!-- Common Reasons -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 30px;">
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #ef4444; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.payment_failed_reason_1') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #ef4444; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.payment_failed_reason_2') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #ef4444; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.payment_failed_reason_3') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #ef4444; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.payment_failed_reason_4') }}</td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_action_required') }}
    </h3>
    
    <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_action_text') }}
    </p>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <a href="{{ $invoiceUrl }}" style="background-color: #ef4444; border: 2px solid #ef4444; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.payment_failed_update_payment') }}
                </a>
            </td>
        </tr>
    </table>
    
    <!-- Info Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; color: #92400e; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    ⏰ {{ __('emails.payment_failed_deadline_title') }}
                </p>
                <p style="margin: 0; color: #92400e; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.payment_failed_deadline_text') }}
                </p>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_need_help') }}
    </h3>
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_help_text') }}
    </p>
    
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="padding: 10px 0;">
                <a href="{{ route('billing') }}" style="color: #667eea; text-decoration: none; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    💳 {{ __('emails.payment_failed_billing') }}
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 0;">
                <a href="{{ route('contact') }}" style="color: #667eea; text-decoration: none; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    ✉️ {{ __('emails.payment_failed_contact') }}
                </a>
            </td>
        </tr>
    </table>
    
    <p style="margin: 30px 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.payment_failed_thank_you') }}
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.welcome_team_name') }}
    </p>
@endsection

