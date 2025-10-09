@extends('emails.layout')

@section('title', __('emails.trial_ending_7days_subject'))

@section('content')
    <h2>{{ __('emails.trial_ending_greeting', ['name' => $user->name]) }}</h2>
    
    <p>{{ __('emails.trial_ending_7days_intro') }}</p>
    
    <div class="highlight-box">
        <p><strong>{{ __('emails.trial_ending_7days_reminder', ['days' => 7, 'date' => $user->trial_ends_at->isoFormat('LL')]) }}</strong></p>
    </div>
    
    <p>{{ __('emails.trial_ending_benefits_intro') }}</p>
    
    <ul class="feature-list">
        <li>{{ __('emails.trial_benefit_1') }}</li>
        <li>{{ __('emails.trial_benefit_2') }}</li>
        <li>{{ __('emails.trial_benefit_3') }}</li>
        <li>{{ __('emails.trial_benefit_4') }}</li>
        <li>{{ __('emails.trial_benefit_5') }}</li>
    </ul>
    
    <div style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 8px; padding: 20px; margin: 30px 0; text-align: center;">
        <p style="font-size: 18px; color: #065f46; margin: 0 0 10px 0;"><strong>{{ __('emails.trial_pricing_title') }}</strong></p>
        <p style="font-size: 32px; color: #059669; margin: 0; font-weight: bold;">€29 / {{ __('emails.per_year') }}</p>
        <p style="font-size: 14px; color: #047857; margin: 10px 0 0 0;">{{ __('emails.trial_pricing_note') }}</p>
    </div>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('billing') }}" class="btn">
            {{ __('emails.trial_setup_payment_button') }}
        </a>
    </div>
    
    <p style="font-size: 14px; color: #6b7280; margin-top: 30px;">{{ __('emails.trial_ending_footer_1') }}</p>
    <p style="font-size: 14px; color: #6b7280;">{{ __('emails.trial_ending_footer_2') }}</p>
@endsection

