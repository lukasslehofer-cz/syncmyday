<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', config('app.name'))</title>
    <!--[if mso]>
    <style type="text/css">
        table {border-collapse: collapse; border-spacing: 0; border: none; margin: 0;}
        div, td {padding: 0;}
        div {margin: 0 !important;}
    </style>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; line-height: 1.6; color: #333333; background-color: #f4f4f5;">
    <!-- Main Container Table -->
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f4f5;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <!-- Email Container Table -->
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; width: 100%; background-color: #ffffff;">
                    
                    <!-- Header with gradient background -->
                    <tr>
                        <td align="center" style="background-color: #667eea; padding: 40px 30px;">
                            <!--[if gte mso 9]>
                            <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:180px;">
                                <v:fill type="gradient" color="#667eea" color2="#764ba2" angle="135" />
                                <v:textbox inset="0,0,0,0">
                            <![endif]-->
                            <div>
                                <!-- Icon -->
                                <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 0 auto 16px;">
                                    <tr>
                                        <td align="center" style="width: 48px; height: 48px; background-color: rgba(255, 255, 255, 0.2); border-radius: 12px;">
                                            <div style="font-size: 28px; line-height: 48px; color: #ffffff;">📅</div>
                                        </td>
                                    </tr>
                                </table>
                                <!-- Title -->
                                <h1 style="margin: 0; padding: 0; color: #ffffff; font-size: 28px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{{ config('app.name') }}</h1>
                            </div>
                            <!--[if gte mso 9]>
                                </v:textbox>
                            </v:rect>
                            <![endif]-->
                        </td>
                    </tr>
                    
                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            @yield('content')
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #e5e7eb;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                                            {{ __('emails.footer_message') }}
                                        </p>
                                        <p style="margin: 0 0 10px 0; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                                            <a href="{{ route('home') }}" style="color: #667eea; text-decoration: none;">{{ __('emails.visit_website') }}</a>
                                            <span style="color: #6b7280;"> | </span>
                                            <a href="{{ route('home') }}" style="color: #667eea; text-decoration: none;">{{ __('emails.help_center') }}</a>
                                        </p>
                                        <p style="margin: 20px 0 0 0; color: #6b7280; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                                            &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('messages.all_rights_reserved') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

