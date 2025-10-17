@extends('errors::layout')

@section('code', '419')
@section('title', __('Page Expired'))

@section('image')
<div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
    <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
</div>
<div class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-2xl">
    ⏰
</div>
@endsection

@section('message')
<h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
    Stránka vypršela
</h2>

<p class="text-lg text-gray-600 mb-4 max-w-lg mx-auto">
    Vaše relace vypršela. To se stává, když je stránka otevřená příliš dlouho. ⏱️
</p>

<p class="text-base text-gray-500 mb-8 max-w-md mx-auto">
    Prosím, obnovte stránku a zkuste to znovu. Vaše data jsou v bezpečí!
</p>
@endsection

