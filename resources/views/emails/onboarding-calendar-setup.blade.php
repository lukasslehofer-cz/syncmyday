@extends('emails.layout')

@section('content')
    <tr>
        <td style="padding: 0;">
            <table role="presentation" style="width: 100%; border-collapse: collapse; border: 0; border-spacing: 0;">
                <tr>
                    <td style="padding: 40px 30px; text-align: left;">
                        <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.onboarding_calendar_setup_greeting', ['name' => $user->name]) }}
                        </p>
                        
                        <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.onboarding_calendar_setup_intro') }}
                        </p>

                        <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 30px 0;">
                            <tr>
                                <td style="padding: 0;">
                                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1f2937; font-weight: 600;">
                                        {{ __('emails.onboarding_calendar_setup_step1_title') }}
                                    </h3>
                                    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        {{ __('emails.onboarding_calendar_setup_step1_text') }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0;">
                                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1f2937; font-weight: 600;">
                                        {{ __('emails.onboarding_calendar_setup_step2_title') }}
                                    </h3>
                                    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        {{ __('emails.onboarding_calendar_setup_step2_text') }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0;">
                                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1f2937; font-weight: 600;">
                                        {{ __('emails.onboarding_calendar_setup_step3_title') }}
                                    </h3>
                                    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        {{ __('emails.onboarding_calendar_setup_step3_text') }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- CTA Button -->
                        <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 30px 0;">
                            <tr>
                                <td style="padding: 0; text-align: center;">
                                    <!--[if mso]>
                                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ route('connections.index') }}" style="height:50px;v-text-anchor:middle;width:250px;" arcsize="10%" stroke="f" fillcolor="#8b5cf6">
                                        <w:anchorlock/>
                                        <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:bold;">{{ __('emails.onboarding_calendar_setup_button') }}</center>
                                    </v:roundrect>
                                    <![endif]-->
                                    <!--[if !mso]><!-->
                                    <a href="{{ route('connections.index') }}" style="display: inline-block; width: 250px; padding: 15px 0; background-color: #8b5cf6; color: #ffffff; text-align: center; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; line-height: 20px;">
                                        {{ __('emails.onboarding_calendar_setup_button') }}
                                    </a>
                                    <!--<![endif]-->
                                </td>
                            </tr>
                        </table>

                        <p style="margin: 30px 0 20px 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.onboarding_calendar_setup_outro') }}
                        </p>

                        <p style="margin: 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.closing') }}<br>
                            {{ __('emails.team_name') }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection

