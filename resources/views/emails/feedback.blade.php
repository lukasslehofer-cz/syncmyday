<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: bold;">
                                Account Deletion Feedback
                            </h1>
                        </td>
                    </tr>
                    
                    <!-- Body -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #333333; font-size: 18px; margin-top: 0; margin-bottom: 20px;">
                                Feedback received from deleted account
                            </h2>
                            
                            <table width="100%" cellpadding="8" cellspacing="0" style="margin-bottom: 20px;">
                                <tr>
                                    <td style="background-color: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 10px;">
                                        <strong style="color: #495057;">Name:</strong><br>
                                        <span style="color: #212529;">{{ $userName }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #f8f9fa; padding: 12px; border-radius: 4px; margin-bottom: 10px;">
                                        <strong style="color: #495057;">Email:</strong><br>
                                        <span style="color: #212529;">{{ $userEmail }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color: #fff3cd; padding: 12px; border-radius: 4px; border-left: 4px solid #ffc107;">
                                        <strong style="color: #856404;">Deletion Reason:</strong><br>
                                        <span style="color: #856404; font-size: 16px;">{{ $reasonLabel }}</span>
                                    </td>
                                </tr>
                            </table>
                            
                            @if($userMessage)
                            <div style="background-color: #e9ecef; padding: 15px; border-radius: 4px; margin-top: 20px;">
                                <strong style="color: #495057;">Additional Comments:</strong>
                                <p style="color: #212529; margin: 10px 0 0 0; line-height: 1.6;">
                                    {{ $userMessage }}
                                </p>
                            </div>
                            @else
                            <p style="color: #6c757d; font-style: italic; margin-top: 20px;">
                                No additional comments provided.
                            </p>
                            @endif
                            
                            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                                <p style="color: #6c757d; font-size: 14px; margin: 0;">
                                    This feedback was submitted via the account deletion feedback form.
                                </p>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
                            <p style="color: #6c757d; font-size: 12px; margin: 0;">
                                &copy; {{ date('Y') }} SyncMyDay. All rights reserved.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

