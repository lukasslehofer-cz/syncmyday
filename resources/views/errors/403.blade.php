@extends('errors::layout')

@section('code', '403')
@section('title', __('Forbidden'))

@section('image')
<div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
    <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
</div>
<div class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-2xl">
    🔒
</div>
@endsection

@section('message')
<h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
    {{ __('messages.error_403_title') }}
</h2>

<p class="text-lg text-gray-600 mb-4 max-w-lg mx-auto">
    {{ __('messages.error_403_message') }} 🔐
</p>

<p class="text-base text-gray-500 mb-8 max-w-md mx-auto">
    {{ __('messages.error_403_hint') }}
</p>
@endsection

