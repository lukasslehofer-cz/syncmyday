@extends('errors::layout')

@section('code', '503')
@section('title', __('Service Unavailable'))

@section('image')
<div class="w-24 h-24 rounded-2xl bg-gradient-to-br from-yellow-100 to-orange-100 flex items-center justify-center">
    <svg class="w-16 h-16 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
    </svg>
</div>
<div class="absolute -top-2 -right-2 w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center text-2xl">
    🔧
</div>
@endsection

@section('message')
<h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-4">
    Momentálně probíhá údržba
</h2>

<p class="text-lg text-gray-600 mb-4 max-w-lg mx-auto">
    Pracujeme na vylepšeních, abychom vám mohli poskytovat ještě lepší služby. 
    Brzy budeme zpět! ⚡
</p>

<p class="text-base text-gray-500 mb-8 max-w-md mx-auto">
    Zkuste to prosím za pár minut. Děkujeme za trpělivost!
</p>
@endsection

