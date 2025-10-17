@extends('errors::layout')

@section('code', '500')
@section('title', __('Server Error'))

@section('image')
<div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-red-100 to-orange-100 flex items-center justify-center">
    <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
</div>
<div class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-2xl">
    😱
</div>
@endsection

@section('message')
<h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
    Jejda! Něco se pokazilo
</h2>

<p class="text-lg text-gray-600 mb-4 max-w-lg mx-auto">
    Naše servery momentálně řeší neočekávaný problém. 
    Už na tom pracujeme! 🔧
</p>

<p class="text-base text-gray-500 mb-8 max-w-md mx-auto">
    Zkuste prosím stránku obnovit za chvíli. Pokud problém přetrvává, 
    dejte nám vědět a my to co nejrychleji opravíme.
</p>
@endsection

