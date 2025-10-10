@extends('emails.layout')

@section('title', __('emails.verify_email_subject'))

@section('content')
    <h2 style="margin: 0 0 20px 0; padding: 0; color: #1f2937; font-size: 24px; font-weight: 600; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.verify_email_greeting', ['name' => $user->name]) }}
    </h2>
    
    <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.verify_email_intro') }}
    </p>
    
    <!-- Button -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!--[if mso]>
                <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $verificationUrl }}" style="height:48px;v-text-anchor:middle;width:200px;" arcsize="17%" strokecolor="#667eea" fillcolor="#667eea">
                    <w:anchorlock/>
                    <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:600;">{{ __('emails.verify_email_button') }}</center>
                </v:roundrect>
                <![endif]-->
                <a href="{{ $verificationUrl }}" style="background-color: #667eea; border: 2px solid #667eea; border-radius: 8px; color: #ffffff; display: inline-block; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; font-size: 16px; font-weight: 600; line-height: 48px; text-align: center; text-decoration: none; width: 200px; -webkit-text-size-adjust: none; mso-hide: all;">
                    {{ __('emails.verify_email_button') }}
                </a>
            </td>
        </tr>
    </table>
    
    <p style="margin: 30px 0 10px 0; color: #4b5563; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.verify_email_alternative') }}
    </p>
    <p style="margin: 0 0 16px 0; word-break: break-all; color: #667eea; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ $verificationUrl }}
    </p>
    
    <!-- Divider -->
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
            <td style="padding: 30px 0;">
                <div style="border-top: 1px solid #e5e7eb;"></div>
            </td>
        </tr>
    </table>
    
    <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
        {{ __('emails.verify_email_footer') }}
    </p>
@endsection

