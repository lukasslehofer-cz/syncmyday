@extends('layouts.public')

@section('title', 'Pripojenie Google Calendar')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-blue-500 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Pripojenie Google Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Rychle a bezpecne pripojenie</p>
    </div>
</div>

<div class="p-6 bg-green-50 border border-green-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-green-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-green-900 mb-2 !mt-0">Preco Google Calendar?</h3>
            <p class="text-green-800 mb-0"><strong>Google Calendar je najjednoduchsi kalendar na pripojenie!</strong> Pouziva bezpecnu OAuth autentifikaciu, takze s nami nikdy nezdielate svoje heslo. Nastavenie trva menej ako 2 minuty a synchronizacia je okamzita vdaka webhookom v realnom case.</p>
        </div>
    </div>
</div>

<h2>Sprievodca krok za krokom</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Prejdite na Pripojenia kalendarov</h3>
            <p>Z vasho SyncMyDay dashboardu kliknite na <strong>Kalendare</strong> v hlavnom menu, alebo prejdite priamo na <a href="{{ route('connections.index') }}">stranku Pripojenia kalendarov</a>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/google-1.jpg') }}"
                         alt="Stranka Pripojenia kalendarov s moznostami Google Calendar, Microsoft 365, Apple/CalDAV a Emailovy kalendar"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Stranka "Pripojenia kalendarov" zobrazujuca vsetky dostupne moznosti pripojenia</p>
            </div>
        </div>
    </div>

    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Kliknite na "Pripojit Google Calendar"</h3>
            <p>Na stranke Pripojenia kalendarov najdite tlacidlo <strong>Google Calendar</strong> s logom Google a kliknite nan.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/google-2.jpg') }}"
                         alt="Tlacidlo Google Calendar na stranke Pripojenia kalendarov"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Kliknite na modre tlacidlo "Google Calendar" pre zahajenie pripojenia</p>
            </div>
        </div>
    </div>

    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Prihlaste sa pomocou Google</h3>
            <p>Budete presmerovani na bezpecnu prihlasovaciu stranku Google. Prihlaste sa pomocou Google uctu, ktory ma kalendar, ktory chcete pripojit.</p>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Viac Google uctov?</p>
                        <p class="text-yellow-800 text-sm mb-0">Uistite sa, ze sa prihlasujete spravnym uctom. Viac Google uctov mozete pripojit neskor opakovanim tohto procesu.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/google-3.jpg') }}"
                         alt="Prihlasovacia obrazovka Google"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Prihlaste sa pomocou svojho Google uctu</p>
            </div>
        </div>
    </div>

    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Udelite opravnenia</h3>
            <p>Google vas poziada o povolenie pre SyncMyDay na pristup k vasmu kalendaru. Skontrolujte opravnenia a kliknite na <strong>Povolit</strong>.</p>

            <p><strong>Ake opravnenia SyncMyDay potrebuje?</strong></p>
            <ul>
                <li><strong>Zobrazit udalosti vo vsetkych vasich kalendaroch:</strong> Na citanie casov udalosti (nie nazvov/detailov)</li>
                <li><strong>Pridavat a upravovat udalosti:</strong> Na vytváranie blokujucich udalosti</li>
                <li><strong>Mazat udalosti:</strong> Na odstranenie blokujucich udalosti, ked su zdrojove udalosti zmazane</li>
            </ul>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Nebojte sa o sukromie!</p>
                        <p class="text-blue-800 text-sm mb-0">Aj ked pozadujeme opravnenie na "zobrazenie udalosti", citame iba casy zaciatku/konca a stav. Nikdy nepristupujeme ani neukladame nazvy udalosti, popisy alebo dalsie detaily.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/google-4.jpg') }}"
                         alt="Obrazovka suhlasu s opravneniami Google OAuth"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Udelite SyncMyDay opravnenia na pristup k vasmu kalendaru</p>
            </div>
        </div>
    </div>

    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte, ktore kalendare synchronizovat</h3>
            <p>Po udeleni opravneni budete presmerovani spat do SyncMyDay. Uvidite zoznam vsetkych kalendarov vo vasom Google ucte. Vyberte, ktore chcete sprístupnit pre synchronizaciu.</p>

            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 mb-1"><strong>Pro tip:</strong> Mozete vybrat viac kalendarov z toho isteho Google uctu! To je uzitocne, ak mate oddelene kalendare pre:</p>
                <ul class="text-purple-800 text-sm mb-0">
                    <li>Osobne udalosti</li>
                    <li>Rodinne udalosti</li>
                    <li>Zdielane timove kalendare</li>
                    <li>Projektovo specificke kalendare</li>
                </ul>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/google-5.jpg') }}"
                         alt="Dialog vyberu kalendarov z Google uctu"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte kalendare, ktore chcete synchronizovat</p>
            </div>
        </div>
    </div>

    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Hotovo! Kalendar pripojeny</h3>
            <p>Vas Google Calendar je teraz pripojeny a objavi sa vo vasom zozname pripojeni kalendarov so zelenym stavovym odznackom "Aktivny".</p>

            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">Co sa stane dalej?</h4>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li>Vas kalendar je pripraveny na pouzitie v pravidlach synchronizacie</li>
                    <li>SyncMyDay bude dostavat oznamenia v realnom case, ked sa udalosti zmenia</li>
                    <li>Teraz mozete vytvorit pravidla synchronizacie a zacat synchronizovat!</li>
                </ul>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/google-6.jpg') }}"
                         alt="Zoznam pripojenych kalendarov s Google Calendar so stavom Aktivny"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vas Google Calendar je uspesne pripojeny a aktivny</p>
            </div>
        </div>
    </div>
</div>

<h2>Dalsie kroky</h2>

<div class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('connections.index') }}" class="block p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Pripojte dalsi kalendar</h3>
        </div>
        <p class="mb-0">Potrebujete aspon 2 kalendare na vytvorenie pravidla synchronizacie. Pripojte pracovny kalendar, osobny kalendar alebo inu sluzbu.</p>
    </a>

    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Vytvorte svoje prve pravidlo synchronizacie</h3>
        </div>
        <p class="mb-0">Naucte sa nastavit synchronizaciu medzi vasimi kalendarmi s filtrami a vlastnymi moznostami.</p>
    </a>
</div>

<!-- Technicke detaily (Rozbalovaci) -->
<div class="mt-12" x-data="{ open: false }">
    <button @click="open = !open" class="w-full p-6 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl text-left transition flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            <div>
                <h3 class="!mb-0 !mt-0 text-lg font-semibold text-gray-900">Technicke detaily</h3>
                <p class="text-sm text-gray-600 !mb-0">Pre vyvojarov a technickych pouzivatelov</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>OAuth 2.0 Flow</h4>
        <p>SyncMyDay pouziva Google OAuth 2.0 autentifikaciu s nasledujucimi scope:</p>
        <ul>
            <li><code>https://www.googleapis.com/auth/calendar.readonly</code> - Citanie kalendarovych dat</li>
            <li><code>https://www.googleapis.com/auth/calendar.events</code> - Vytváranie/uprava/mazanie udalosti</li>
        </ul>

        <h4>Synchronizacia v realnom case</h4>
        <p>Pouzivame Google Calendar Push Notifications (webhooky) pre okamzite prijimanie aktualizacii:</p>
        <ul>
            <li>Pre kazdy pripojeny kalendar je registrovany webhook</li>
            <li>Google odosiela oznamenia pocas niekolkych sekund po akychkolvek zmenach udalosti</li>
            <li>Webhooky su automaticky obnovene kazdych 7 dni</li>
            <li>Ak dorucenie webhooku zlyha, prepneme na polling kazdych 15 minut</li>
        </ul>

        <h4>API kvoty</h4>
        <p>Google Calendar API ma nasledujuce kvoty:</p>
        <ul>
            <li><strong>Poziadavkov za den:</strong> 1,000,000 (zdielane medzi vsetkymi pouzivatelmi SyncMyDay)</li>
            <li><strong>Poziadavkov za 100 sekund na pouzivatela:</strong> 500</li>
        </ul>
        <p>Architektura SyncMyDay je optimalizovana, aby zostala dobre v ramci tychto limitov pre bezne pouzitie.</p>

        <h4>Ukladanie tokenov</h4>
        <p>OAuth pristupove tokeny a obnovovacie tokeny su:</p>
        <ul>
            <li>Sifrovane v pokoji pomocou AES-256</li>
            <li>Bezpecne ulozene v nasej databaze</li>
            <li>Automaticky obnovovane, ked vyprsia (kazdych 60 minut)</li>
            <li>Okamzite zmazane, ked odpojite kalendar</li>
        </ul>

        <h4>Odvolanie pristupu</h4>
        <p>Pristup SyncMyDay mozete kedykolvek odvolat:</p>
        <ul>
            <li><strong>Zo SyncMyDay:</strong> Kliknite na "Odpojit" na stranke Pripojenia kalendarov</li>
            <li><strong>Z Google:</strong> Navstivte <a href="https://myaccount.google.com/permissions" target="_blank">myaccount.google.com/permissions</a> a odoberte SyncMyDay</li>
        </ul>
    </div>
</div>
</div>
@endsection
