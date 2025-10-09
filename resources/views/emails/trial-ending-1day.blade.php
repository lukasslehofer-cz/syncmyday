@extends('emails.layout')

@section('title', __('emails.trial_ending_1day_subject'))

@section('content')
    <h2>{{ __('emails.trial_ending_greeting', ['name' => $user->name]) }}</h2>
    
    <p>{{ __('emails.trial_ending_1day_intro') }}</p>
    
    <div class="highlight-box" style="background-color: #fef3c7; border-left-color: #f59e0b;">
        <p style="color: #92400e;"><strong>⏰ {{ __('emails.trial_ending_1day_reminder', ['date' => $user->trial_ends_at->isoFormat('LL')]) }}</strong></p>
    </div>
    
    <p>{{ __('emails.trial_ending_1day_action_needed') }}</p>
    
    <div style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 8px; padding: 20px; margin: 30px 0; text-align: center;">
        <p style="font-size: 18px; color: #065f46; margin: 0 0 10px 0;"><strong>{{ __('emails.trial_pricing_title') }}</strong></p>
        <p style="font-size: 32px; color: #059669; margin: 0; font-weight: bold;">€29 / {{ __('emails.per_year') }}</p>
        <p style="font-size: 14px; color: #047857; margin: 10px 0 0 0;">{{ __('emails.trial_pricing_details') }}</p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('billing') }}" class="btn">
            {{ __('emails.trial_activate_now_button') }}
        </a>
    </div>
    
    <div class="divider"></div>
    
    <h3 style="color: #1f2937;">{{ __('emails.trial_ending_1day_what_happens') }}</h3>
    
    <p><strong>{{ __('emails.trial_ending_1day_if_subscribe') }}</strong></p>
    <p>{{ __('emails.trial_ending_1day_if_subscribe_text') }}</p>
    
    <p style="margin-top: 20px;"><strong>{{ __('emails.trial_ending_1day_if_not_subscribe') }}</strong></p>
    <p>{{ __('emails.trial_ending_1day_if_not_subscribe_text') }}</p>
    
    <div class="divider"></div>
    
    <p>{{ __('emails.trial_ending_1day_thanks') }}</p>
    <p><strong>{{ __('emails.welcome_team_name') }}</strong></p>
@endsection

