@extends('emails.layout')

@section('title', __('emails.payment_success_subject'))

@section('content')
    <h2>{{ __('emails.payment_success_greeting', ['name' => $user->name]) }}</h2>
    
    <p>{{ __('emails.payment_success_intro') }}</p>
    
    <div style="background-color: #f0fdf4; border: 2px solid #10b981; border-radius: 12px; padding: 24px; margin: 30px 0; text-align: center;">
        <div style="font-size: 48px; margin-bottom: 12px;">✅</div>
        <p style="font-size: 20px; color: #065f46; margin: 0 0 8px 0; font-weight: bold;">{{ __('emails.payment_success_confirmed') }}</p>
        @if($amount)
            <p style="font-size: 32px; color: #059669; margin: 12px 0; font-weight: bold;">€{{ number_format($amount, 2) }}</p>
        @endif
        <p style="font-size: 14px; color: #047857; margin: 8px 0 0 0;">{{ __('emails.payment_success_receipt') }}</p>
    </div>
    
    <h3 style="color: #1f2937; margin-top: 30px;">{{ __('emails.payment_success_whats_next') }}</h3>
    
    <div class="highlight-box">
        <p style="margin: 0;"><strong>🎉 {{ __('emails.payment_success_full_access') }}</strong></p>
        <p style="margin: 8px 0 0 0;">{{ __('emails.payment_success_full_access_text') }}</p>
    </div>
    
    <ul class="feature-list" style="margin-top: 20px;">
        <li>{{ __('emails.payment_benefit_1') }}</li>
        <li>{{ __('emails.payment_benefit_2') }}</li>
        <li>{{ __('emails.payment_benefit_3') }}</li>
        <li>{{ __('emails.payment_benefit_4') }}</li>
        <li>{{ __('emails.payment_benefit_5') }}</li>
    </ul>
    
    <div class="divider"></div>
    
    <h3 style="color: #1f2937;">{{ __('emails.payment_success_renewal_title') }}</h3>
    
    <div style="background-color: #eff6ff; border-left: 4px solid #3b82f6; padding: 20px; margin: 20px 0; border-radius: 4px;">
        <p style="margin: 0; color: #1e40af;"><strong>📅 {{ __('emails.payment_success_next_payment') }}</strong></p>
        <p style="margin: 8px 0; color: #1e3a8a;">
            @if($nextBillingDate)
                {{ __('emails.payment_success_next_payment_date', ['date' => $nextBillingDate]) }}
            @else
                {{ __('emails.payment_success_next_payment_year') }}
            @endif
        </p>
        <p style="margin: 8px 0 0 0; color: #1e40af; font-size: 14px;">{{ __('emails.payment_success_reminder_promise') }}</p>
    </div>
    
    <p>{{ __('emails.payment_success_manage_info') }}</p>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('billing') }}" class="btn">
            {{ __('emails.payment_success_manage_subscription') }}
        </a>
    </div>
    
    <div class="divider"></div>
    
    <h3 style="color: #1f2937;">{{ __('emails.payment_success_need_help') }}</h3>
    <p>{{ __('emails.payment_success_help_text') }}</p>
    
    <ul style="list-style: none; padding: 0;">
        <li style="margin: 10px 0;">
            <a href="{{ route('dashboard') }}" style="color: #667eea; text-decoration: none;">
                🏠 {{ __('emails.payment_success_dashboard') }}
            </a>
        </li>
        <li style="margin: 10px 0;">
            <a href="{{ route('billing') }}" style="color: #667eea; text-decoration: none;">
                💳 {{ __('emails.payment_success_billing') }}
            </a>
        </li>
        <li style="margin: 10px 0;">
            <a href="{{ config('app.url') }}/docs" style="color: #667eea; text-decoration: none;">
                📚 {{ __('emails.welcome_documentation') }}
            </a>
        </li>
    </ul>
    
    <p style="margin-top: 30px;">{{ __('emails.payment_success_thank_you') }}</p>
    <p><strong>{{ __('emails.welcome_team_name') }}</strong></p>
@endsection

