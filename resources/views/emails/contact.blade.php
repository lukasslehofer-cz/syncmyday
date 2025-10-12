@extends('emails.layout')

@section('content')
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
        <td style="padding: 40px 30px; text-align: center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h1 style="margin: 0; color: #ffffff; font-size: 28px; font-weight: 700; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                Contact Form Submission
            </h1>
        </td>
    </tr>
</table>

<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="padding: 40px 30px;">
    <tr>
        <td>
            <p style="margin: 0 0 20px 0; color: #4a5568; font-size: 16px; line-height: 24px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                You have received a new message from the contact form:
            </p>

            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 30px 0; background-color: #f7fafc; border-radius: 8px; overflow: hidden;">
                <tr>
                    <td style="padding: 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <td style="padding: 10px 0;">
                                    <strong style="color: #2d3748; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Name:</strong><br>
                                    <span style="color: #4a5568; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{{ $contactName }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0;">
                                    <strong style="color: #2d3748; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Email:</strong><br>
                                    <a href="mailto:{{ $contactEmail }}" style="color: #667eea; text-decoration: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{{ $contactEmail }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0;">
                                    <strong style="color: #2d3748; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Subject:</strong><br>
                                    <span style="color: #4a5568; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">{{ $contactSubject }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 0; border-top: 1px solid #e2e8f0;">
                                    <strong style="color: #2d3748; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">Message:</strong><br>
                                    <div style="color: #4a5568; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; white-space: pre-line; margin-top: 10px;">{{ $contactMessage }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <p style="margin: 20px 0 0 0; color: #718096; font-size: 14px; line-height: 20px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
                <strong>Note:</strong> You can reply directly to this email to respond to {{ $contactName }}.
            </p>
        </td>
    </tr>
</table>
@endsection

