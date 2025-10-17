@extends('emails.layout')

@section('title', __('emails.account_deleted_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_intro') }}
    </p>
    
    <!-- Confirmation Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0;">
        <tr>
            <td align="center" style="background-color: #f3f4f6; border: 2px solid #9ca3af; border-radius: 12px; padding: 24px;">
                <div style="font-size: 48px; margin-bottom: 12px;">✓</div>
                <p style="margin: 0 0 8px 0; font-size: 20px; color: #374151; font-weight: bold; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.account_deleted_title') }}
                </p>
                <p style="margin: 0; font-size: 16px; color: #6b7280; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.account_deleted_date', ['date' => $deletedAt]) }}
                </p>
            </td>
        </tr>
    </table>
    
    <h3 style="margin: 30px 0 20px 0; padding: 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_what_happened') }}
    </h3>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_explanation') }}
    </p>
    
    <!-- What was deleted -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-bottom: 30px;">
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #9ca3af; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.account_deleted_item_1') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #9ca3af; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.account_deleted_item_2') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #9ca3af; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.account_deleted_item_3') }}</td></tr>
        <tr><td style="padding: 8px 0; padding-left: 30px; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;"><span style="color: #9ca3af; font-weight: bold; margin-right: 10px;">•</span>{{ __('emails.account_deleted_item_4') }}</td></tr>
    </table>
    
    <!-- Info Box -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; border-radius: 4px;">
                <p style="margin: 0 0 8px 0; color: #1e40af; font-size: 16px; font-weight: bold; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    ℹ️ {{ __('emails.account_deleted_data_retention_title') }}
                </p>
                <p style="margin: 0; color: #1e40af; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                    {{ __('emails.account_deleted_data_retention_text') }}
                </p>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_feedback_title') }}
    </h3>
    
    <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_feedback_text') }}
    </p>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <a href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('feedback', now()->addDays(30), ['email' => $user->email, 'name' => $user->name]) }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 250px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.account_deleted_feedback_button') }}
                </a>
            </td>
        </tr>
    </table>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr><td style="padding: 30px 0;"><div style="border-top: 1px solid #e5e7eb;"></div></td></tr>
    </table>
    
    <h3 style="margin: 0 0 16px 0; color: #1f2937; font-size: 20px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_changed_mind_title') }}
    </h3>
    
    <p style="margin: 0 0 20px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_changed_mind_text') }}
    </p>
    
    <p style="margin: 20px 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.account_deleted_thank_you') }}
    </p>
    <p style="margin: 0; color: #1f2937; font-size: 16px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.welcome_team_name') }}
    </p>
@endsection

