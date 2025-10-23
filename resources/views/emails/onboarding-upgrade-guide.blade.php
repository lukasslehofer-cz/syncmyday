@extends('emails.layout')

@section('content')
    <tr>
        <td style="padding: 0;">
            <table role="presentation" style="width: 100%; border-collapse: collapse; border: 0; border-spacing: 0;">
                <tr>
                    <td style="padding: 40px 30px; text-align: left;">
                        <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.onboarding_upgrade_guide_greeting', ['name' => $user->name]) }}
                        </p>
                        
                        <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.onboarding_upgrade_guide_intro') }}
                        </p>

                        <!-- Highlight box -->
                        <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 25px 0; background-color: #f3f4f6; border-left: 4px solid #8b5cf6;">
                            <tr>
                                <td style="padding: 20px;">
                                    <p style="margin: 0; font-size: 15px; line-height: 22px; color: #374151; font-weight: 600;">
                                        {{ __('emails.onboarding_upgrade_guide_trial_notice') }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 30px 0;">
                            <tr>
                                <td style="padding: 0;">
                                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1f2937; font-weight: 600;">
                                        {{ __('emails.onboarding_upgrade_guide_benefits_title') }}
                                    </h3>
                                    <p style="margin: 0 0 5px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        ✓ {{ __('emails.onboarding_upgrade_guide_benefit1') }}
                                    </p>
                                    <p style="margin: 0 0 5px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        ✓ {{ __('emails.onboarding_upgrade_guide_benefit2') }}
                                    </p>
                                    <p style="margin: 0 0 5px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        ✓ {{ __('emails.onboarding_upgrade_guide_benefit3') }}
                                    </p>
                                    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        ✓ {{ __('emails.onboarding_upgrade_guide_benefit4') }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 0;">
                                    <h3 style="margin: 0 0 15px 0; font-size: 18px; color: #1f2937; font-weight: 600;">
                                        {{ __('emails.onboarding_upgrade_guide_pricing_title') }}
                                    </h3>
                                    <p style="margin: 0 0 20px 0; font-size: 15px; line-height: 22px; color: #6b7280;">
                                        {{ __('emails.onboarding_upgrade_guide_pricing_text') }}
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- CTA Button -->
                        <table role="presentation" style="width: 100%; border-collapse: collapse; margin: 30px 0;">
                            <tr>
                                <td style="padding: 0; text-align: center;">
                                    <!--[if mso]>
                                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ route('billing.index') }}" style="height:50px;v-text-anchor:middle;width:250px;" arcsize="10%" stroke="f" fillcolor="#8b5cf6">
                                        <w:anchorlock/>
                                        <center style="color:#ffffff;font-family:sans-serif;font-size:16px;font-weight:bold;">{{ __('emails.onboarding_upgrade_guide_button') }}</center>
                                    </v:roundrect>
                                    <![endif]-->
                                    <!--[if !mso]><!-->
                                    <a href="{{ route('billing.index') }}" style="display: inline-block; width: 250px; padding: 15px 0; background-color: #8b5cf6; color: #ffffff; text-align: center; text-decoration: none; font-size: 16px; font-weight: bold; border-radius: 5px; line-height: 20px;">
                                        {{ __('emails.onboarding_upgrade_guide_button') }}
                                    </a>
                                    <!--<![endif]-->
                                </td>
                            </tr>
                        </table>

                        <p style="margin: 30px 0 20px 0; font-size: 16px; line-height: 24px; color: #4b5563;">
                            {{ __('emails.onboarding_upgrade_guide_outro') }}
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

