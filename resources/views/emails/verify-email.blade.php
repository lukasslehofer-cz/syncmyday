@extends('emails.layout')

@section('title', __('emails.verify_email_subject'))

@section('content')
    <h2>{{ __('emails.verify_email_greeting', ['name' => $user->name]) }}</h2>
    
    <p>{{ __('emails.verify_email_intro') }}</p>
    
    <div style="text-align: center;">
        <a href="{{ $verificationUrl }}" class="btn">
            {{ __('emails.verify_email_button') }}
        </a>
    </div>
    
    <p style="margin-top: 30px;">{{ __('emails.verify_email_alternative') }}</p>
    <p style="word-break: break-all; color: #667eea;">{{ $verificationUrl }}</p>
    
    <div class="divider"></div>
    
    <p style="font-size: 14px; color: #6b7280;">{{ __('emails.verify_email_footer') }}</p>
@endsection

