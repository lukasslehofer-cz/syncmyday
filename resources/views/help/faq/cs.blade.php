@extends('layouts.public')

@section('title', 'Často kladené otázky')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<h1>Často kladené otázky</h1>

<p class="text-xl text-gray-600 mb-8">Rychlé odpovědi na běžné otázky o SyncMyDay.</p>

<div class="p-6 bg-yellow-50 border border-yellow-200 rounded-xl mb-8">
    <p class="text-yellow-800">
        <strong>Poznámka:</strong> Detailní český překlad FAQ je v přípravě. Prozatím prosím použijte <a href="{{ route('help.faq') }}?lang=en" class="text-indigo-600 hover:text-indigo-700 font-semibold">anglickou verzi</a> nebo nás <a href="{{ route('contact') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">kontaktujte</a> s vašimi otázkami.
    </p>
</div>

<h2>Základní otázky</h2>

<div class="space-y-4">
    <div class="p-6 bg-white border border-gray-200 rounded-xl">
        <h3>Co je SyncMyDay?</h3>
        <p>SyncMyDay je služba pro synchronizaci kalendářů, která automaticky vytváří blokující události mezi vašimi různými kalendáři, aby všichni viděli, kdy jste zaneprázdněni.</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 rounded-xl">
        <h3>Je moje data v bezpečí?</h3>
        <p>Ano, vaše data jsou chráněna pomocí šifrování a ukládána v Evropské unii. Nikdy nesdílíme vaše kalendářní data s třetími stranami.</p>
    </div>

    <div class="p-6 bg-white border border-gray-200 rounded-xl">
        <h3>Jaké kalendáře mohu připojit?</h3>
        <p>Podporujeme Google Calendar, Microsoft 365/Outlook, Apple iCloud, CalDAV a e-mailové kalendáře.</p>
    </div>
</div>

<div class="mt-12 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Máte další otázky?</h3>
    <p class="text-gray-700 mb-4">Jsme tu, abychom vám pomohli! Náš tým podpory obvykle odpovídá do 24 hodin.</p>
    <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition no-underline" style="text-decoration: none !important;">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        Kontaktujte podporu
    </a>
</div>
</div>
@endsection

