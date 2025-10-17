@extends('errors::layout')

@section('code', '429')
@section('title', __('Too Many Requests'))

@section('image')
<div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
    <svg class="w-16 h-16 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
    </svg>
</div>
<div class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-2xl">
    🏃
</div>
@endsection

@section('message')
<h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
    Pomaleji, prosím!
</h2>

<p class="text-lg text-gray-600 mb-4 max-w-lg mx-auto">
    Posíláte příliš mnoho požadavků najednou. 
    Dejte nám chvilku na vydech! 💨
</p>

<p class="text-base text-gray-500 mb-8 max-w-md mx-auto">
    Počkejte prosím několik sekund a zkuste to znovu. Děkujeme za pochopení!
</p>
@endsection

