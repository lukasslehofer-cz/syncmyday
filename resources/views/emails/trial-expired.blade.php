@extends('emails.layout')

@section('title', __('emails.trial_expired_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_intro') }}
    </p>
    
    <!-- Warning Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #fef2f2; border: 2px solid #f59e0b; border-radius: 12px; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">⏰</div>
                <p style="margin: 0 0 8px 0; font-size: 20px; color: #92400e; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.trial_expired_title') }}
                </p>
                <p style="margin: 0; font-size: 16px; color: #92400e; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.trial_expired_subtitle') }}
                </p>
            </td>
        </tr>
    </table>
    
    <h3 style="margin: 30px 0 20px 0; padding: 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_what_happens') }}
    </h3>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_limited_access') }}
    </p>
    
    <!-- Limited Access Info -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 12px 0; color: #92400e; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    ℹ️ {{ __('emails.trial_expired_limitations_title') }}
                </p>
                <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                    <tr><td style="padding: 4px 0; padding-left: 20px; color: #92400e; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #f59e0b; font-weight: bold; margin-right: 8px;">•</span>{{ __('emails.trial_expired_limit_1') }}</td></tr>
                    <tr><td style="padding: 4px 0; padding-left: 20px; color: #92400e; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #f59e0b; font-weight: bold; margin-right: 8px;">•</span>{{ __('emails.trial_expired_limit_2') }}</td></tr>
                    <tr><td style="padding: 4px 0; padding-left: 20px; color: #92400e; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #f59e0b; font-weight: bold; margin-right: 8px;">•</span>{{ __('emails.trial_expired_limit_3') }}</td></tr>
                </table>
            </td>
        </tr>
    </table>
    
    <p style="margin: 30px 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_good_news') }}
    </p>
    
    <!-- Pricing Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 8px; padding: 24px;">
                <p style="margin: 0 0 10px 0; font-size: 18px; color: #065f46; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.trial_expired_pricing_title') }}
                </p>
                <p style="margin: 0 0 5px 0; font-size: 28px; color: #059669; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ \App\Helpers\PricingHelper::formatPrice($user->locale, 'monthly') }} / {{ __('emails.per_month') }}
                </p>
                <p style="margin: 0; font-size: 18px; color: #065f46; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.or') }} {{ \App\Helpers\PricingHelper::formatPrice($user->locale, 'yearly') }} / {{ __('emails.per_year') }}
                </p>
                <p style="margin: 10px 0 0 0; font-size: 14px; color: #047857; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.trial_expired_pricing_note') }}
                </p>
            </td>
        </tr>
    </table>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 30px 0;">
                <a href="{{ route('billing') }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.trial_expired_subscribe_button') }}
                </a>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_benefits_title') }}
    </h3>
    
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 20px;">
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_expired_benefit_1') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_expired_benefit_2') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_expired_benefit_3') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_expired_benefit_4') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #667eea; font-weight: bold; margin-right: 10px;">✓</span>{{ __('emails.trial_expired_benefit_5') }}</td></tr>
    </table>
    
    <p style="margin: 20px 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.trial_expired_thank_you') }}
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.welcome_team_name') }}
    </p>
@endsection

