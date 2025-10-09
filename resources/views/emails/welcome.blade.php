@extends('emails.layout')

@section('title', __('emails.welcome_subject'))

@section('content')
    <h2>{{ __('emails.welcome_greeting', ['name' => $user->name]) }}</h2>
    
    <p>{{ __('emails.welcome_intro') }}</p>
    
    <div class="highlight-box">
        <p><strong>{{ __('emails.welcome_trial_info') }}</strong></p>
    </div>
    
    <h3 style="color: #1f2937; margin-top: 30px;">{{ __('emails.welcome_what_you_can_do') }}</h3>
    
    <ul class="feature-list">
        <li>{{ __('emails.welcome_feature_1') }}</li>
        <li>{{ __('emails.welcome_feature_2') }}</li>
        <li>{{ __('emails.welcome_feature_3') }}</li>
        <li>{{ __('emails.welcome_feature_4') }}</li>
        <li>{{ __('emails.welcome_feature_5') }}</li>
    </ul>
    
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ route('dashboard') }}" class="btn">
            {{ __('emails.welcome_get_started_button') }}
        </a>
    </div>
    
    <div class="divider"></div>
    
    <h3 style="color: #1f2937;">{{ __('emails.welcome_need_help') }}</h3>
    <p>{{ __('emails.welcome_help_intro') }}</p>
    
    <ul style="list-style: none; padding: 0;">
        <li style="margin: 10px 0;">
            <a href="{{ config('app.url') }}/docs" style="color: #667eea; text-decoration: none;">
                📚 {{ __('emails.welcome_documentation') }}
            </a>
        </li>
        <li style="margin: 10px 0;">
            <a href="{{ route('help_center') ?? route('home') }}" style="color: #667eea; text-decoration: none;">
                💬 {{ __('emails.welcome_help_center') }}
            </a>
        </li>
    </ul>
    
    <p style="margin-top: 30px;">{{ __('emails.welcome_signature') }}</p>
    <p><strong>{{ __('emails.welcome_team_name') }}</strong></p>
@endsection

