@extends('layouts.public')

@section('title', 'Pripojenie Microsoft 365')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Pripojenie Microsoft 365</h1>
        <p class="text-lg text-gray-600 !mb-0">Outlook, Office 365 a Exchange Online</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2 !mt-0">Co je zahrnute</h3>
            <p class="text-blue-800 mb-2">Tento sprievodca pokryva vsetky Microsoft kalendarove sluzby:</p>
            <ul class="text-blue-800 space-y-1 mb-0">
                <li><strong>Outlook.com</strong> - Osobne Microsoft ucty (@outlook.com, @hotmail.com, @live.com)</li>
                <li><strong>Microsoft 365</strong> - Pracovne alebo skolske ucty</li>
                <li><strong>Office 365</strong> - Firemne predplatne</li>
                <li><strong>Exchange Online</strong> - Podnikove e-maily a kalendare</li>
            </ul>
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
            <p>Z vasho SyncMyDay dashboardu prejdite na <strong>Kalendare</strong> v hlavnom menu, alebo chodte priamo na <a href="{{ route('connections.index') }}">stranku Pripojenia kalendarov</a>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/microsoft-1.jpg') }}"
                         alt="Stranka Pripojenia kalendarov s moznostami poskytovatelov"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Navigacna lista zobrazujuca moznost Kalendare</p>
            </div>
        </div>
    </div>

    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Kliknite na "Pripojit Microsoft 365"</h3>
            <p>Najdite a kliknite na tlacidlo <strong>Microsoft 365</strong> s logom Microsoft.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/microsoft-2.jpg') }}"
                         alt="Tlacidlo Microsoft 365 na stranke Pripojenia kalendarov"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Kliknite na fialove tlacidlo "Microsoft 365" pre zahajenie pripojenia</p>
            </div>
        </div>
    </div>

    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Prihlaste sa pomocou Microsoft</h3>
            <p>Budete presmerovani na bezpecnu prihlasovaciu stranku Microsoft. Zadajte svoju Microsoft e-mailovu adresu:</p>
            <ul>
                <li><strong>Osobna:</strong> @outlook.com, @hotmail.com, @live.com</li>
                <li><strong>Pracovna/Skolska:</strong> E-mail vasej organizacie (napr. vy@firma.sk)</li>
            </ul>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Pracovny/Skolsky ucet?</p>
                        <p class="text-yellow-800 text-sm mb-0">Vasa organizacia moze potrebovat schvalit SyncMyDay. Kontaktujte svojho IT administratora, ak uvidite spravu o ziadosti o schvalenie.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/microsoft-3.jpg') }}"
                         alt="Prihlasovacia obrazovka Microsoft"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadajte svoju Microsoft e-mailovu adresu</p>
            </div>
        </div>
    </div>

    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Zadajte svoje heslo</h3>
            <p>Po zadani e-mailu budete vyzvania k zadaniu hesla. Zadajte heslo svojho Microsoft uctu.</p>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Vase heslo je v bezpeci</p>
                        <p class="text-blue-800 text-sm mb-0">Zadavate heslo priamo na webe Microsoft. SyncMyDay nikdy nevidi ani neuklada vase heslo.</p>
                    </div>
                </div>
            </div>

            <p class="text-sm text-gray-600">Ak mate povolenu viacfaktorovu autentifikaciu (MFA), budete musiet schvalit prihlasenie na telefone alebo v aplikacii autentifikatora.</p>
        </div>
    </div>

    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Udelite opravnenia</h3>
            <p>Microsoft zobrazi obrazovku s opravneniami a opyta sa, ci chcete povolit SyncMyDay pristup k vasmu kalendaru. Kliknite na <strong>Prijat</strong> pre pokracovanie.</p>

            <p><strong>Ake opravnenia SyncMyDay potrebuje?</strong></p>
            <ul>
                <li><strong>Citat vase kalendare:</strong> Na detekciu, kedy mate naplanovane udalosti</li>
                <li><strong>Vytvarat a upravovat kalendarove udalosti:</strong> Na vytváranie blokujucich udalosti</li>
                <li><strong>Mazat kalendarove udalosti:</strong> Na odstranenie blokujucich udalosti, ked je to potrebne</li>
                <li><strong>Udrzovat pristup k datam:</strong> Na nepretrzitu synchronizaciu</li>
            </ul>

            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-green-900 font-semibold mb-1">Sukromie na prvom mieste</p>
                        <p class="text-green-800 text-sm mb-0">Citame iba casovanie udalosti (zaciatok/koniec). Nikdy nepristupujeme k nazvom udalosti, popisom, miestam alebo informaciam o ucastnikoch.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/microsoft-4.jpg') }}"
                         alt="Obrazovka suhlasu s opravneniami Microsoft"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Udelite SyncMyDay opravnenia na pristup k vasmu kalendaru</p>
            </div>
        </div>
    </div>

    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte kalendare na synchronizaciu</h3>
            <p>Po udeleni opravneni sa vratite do SyncMyDay, kde mozete vybrat, ktore Microsoft kalendare chcete pouzit pre synchronizaciu.</p>

            <p>Vacsina uctov bude mat aspon:</p>
            <ul>
                <li><strong>Kalendar</strong> - Vas hlavny kalendar</li>
                <li><strong>Narodeniny</strong> - Narodeniny kontaktov (mozete preskocit)</li>
            </ul>

            <p>Mozete tiez vidiet:</p>
            <ul>
                <li>Zdielane timove kalendare</li>
                <li>Kalendare zdrojov (zasadacie miestnosti, vybavenie)</li>
                <li>Kalendare zdielane s vami kolegami</li>
            </ul>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/microsoft-5.jpg') }}"
                         alt="Dialog vyberu kalendarov z Microsoft uctu"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte kalendare, ktore chcete synchronizovat</p>
            </div>
        </div>
    </div>

    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Pripojenie dokoncene!</h3>
            <p>Vas Microsoft 365 kalendar je teraz pripojeny a pripraveny na pouzitie. Uvidite ho vo vasom zozname Pripojeni kalendarov so stavom "Aktivny".</p>

            <div class="p-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
                <h4 class="text-lg font-semibold text-purple-900 mb-2">Vsetko je nastavene!</h4>
                <ul class="text-purple-800 space-y-1 mb-0">
                    <li>Synchronizacia v realnom case je povolena prostrednictvom webhookov</li>
                    <li>Zmeny vo vasom kalendari su detekovane pocas niekolkych sekund</li>
                    <li>Pripravene vytvorit pravidla synchronizacie a zacat synchronizovat!</li>
                </ul>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/microsoft-6.jpg') }}"
                         alt="Zoznam pripojenych kalendarov s Microsoft 365 so stavom Aktivny"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vas Microsoft 365 kalendar je uspesne pripojeny a aktivny</p>
            </div>
        </div>
    </div>
</div>

<h2>Caste problemy</h2>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            "Vasa organizacia potrebuje schvalit tuto aplikaciu"
        </h3>
        <p><strong>Preco sa to stava:</strong> Vase IT oddelenie obmedzilo, ktore aplikacie mozu pristupovat k firemnym datam.</p>
        <p><strong>Riesenie:</strong></p>
        <ol>
            <li>Kontaktujte svojho IT administratora alebo helpdesk</li>
            <li>Poziadajte ich o schvalenie "SyncMyDay" v centre spravy Microsoft 365</li>
            <li>Alebo poziadajte o vynimku pre vas ucet</li>
        </ol>
        <p class="text-sm text-gray-600 mb-0">To je bezne vo vacsich organizaciach a je to osvedceny bezpecnostny postup.</p>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Pripojenie zobrazuje stav "Chyba"
        </h3>
        <p><strong>Caste priciny:</strong></p>
        <ul>
            <li>Vase heslo bolo zmenene</li>
            <li>Nastavenia viacfaktorovej autentifikacie sa zmenili</li>
            <li>Organizacia odvolala pristup</li>
        </ul>
        <p><strong>Riesenie:</strong> Kliknite na tlacidlo "Obnovit" pre opätovnu autentifikaciu, alebo odpojte a znovu pripojte kalendar.</p>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Nevidim zdielany kalendar
        </h3>
        <p>Zdielane kalendare by sa mali objavit, ak su pridane do vasho Outlooku. Ak chybaju:</p>
        <ol>
            <li>Uistite sa, ze kalendar je viditelny v Outlook webe alebo aplikacii</li>
            <li>Odpojte a znovu pripojte svoj Microsoft ucet</li>
            <li>Uistite sa, ze mate aspon opravnenie "Moze zobrazit vsetky detaily"</li>
        </ol>
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
        <p class="mb-0">Pripojte osobny kalendar (Google, Apple) pre synchronizaciu s vasim pracovnym kalendarom.</p>
    </a>

    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Vytvorte pravidlo synchronizacie</h3>
        </div>
        <p class="mb-0">Nastavte svoju prvu synchronizaciu medzi kalendarmi.</p>
    </a>
</div>

<!-- Technicke detaily -->
<div class="mt-12" x-data="{ open: false }">
    <button @click="open = !open" class="w-full p-6 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl text-left transition flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            <div>
                <h3 class="!mb-0 !mt-0 text-lg font-semibold text-gray-900">Technicke detaily</h3>
                <p class="text-sm text-gray-600 !mb-0">Pre vyvojarov a IT administratorov</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Microsoft Graph API</h4>
        <p>SyncMyDay pouziva Microsoft Graph API s tymito opravneniami:</p>
        <ul>
            <li><code>Calendars.ReadWrite</code> - Citanie a zapis kalendarovych udalosti</li>
            <li><code>offline_access</code> - Udrzovanie pristupu, ked je pouzivatel offline</li>
        </ul>

        <h4>OAuth 2.0 autentifikacia</h4>
        <p>Pouzivame standardny OAuth 2.0 authorization code flow:</p>
        <ul>
            <li>Podporuje osobne Microsoft ucty aj Azure AD ucty</li>
            <li>Tokeny su automaticky obnovovane kazdych 60 minut</li>
            <li>Obnovovacie tokeny su platne 90 dni (automaticky obnovovane)</li>
        </ul>

        <h4>Synchronizacia v realnom case</h4>
        <p>Microsoft Graph change notifications (webhooky) umoznuju okamzitu synchronizaciu:</p>
        <ul>
            <li>Predplatne su vytvorene pre kazdy pripojeny kalendar</li>
            <li>Oznamenia su prijimane pocas 2-3 minut po zmenach</li>
            <li>Predplatne su automaticky obnovovane kazde 3 dni</li>
            <li>Zalozny polling prebieha kazdych 15 minut, ak webhooky zlyhaju</li>
        </ul>

        <h4>Podnikovy suhlas administratora</h4>
        <p>IT administratori mozu vopred schvalit SyncMyDay pre vsetkych pouzivatelov:</p>
        <ol>
            <li>Prejdite na Portal Azure AD -> Podnikove aplikacie</li>
            <li>Vyhladajte "SyncMyDay" alebo pridajte cez App ID</li>
            <li>Udelite suhlas administratora pre organizaciu</li>
            <li>Pouzivatelia sa potom mozu pripojit bez vyziev ku schvaleniu</li>
        </ol>

        <h4>API obmedzenia</h4>
        <p>Microsoft Graph ma nasledujuce limity:</p>
        <ul>
            <li><strong>Na aplikaciu:</strong> 10,000 poziadavkov za 10 minut</li>
            <li><strong>Na pouzivatela:</strong> 2,000 poziadavkov za sekundu</li>
        </ul>
        <p>Architektura SyncMyDay zalozena na webhookoch minimalizuje API volania a zostava dobre v ramci limitov.</p>

        <h4>Rezidencia dat</h4>
        <p>Vase kalendarove data zostavaju v datovych centrach Microsoft. SyncMyDay uklada iba:</p>
        <ul>
            <li>ID kalendarov a nazvy</li>
            <li>Casy zaciatku/konca udalosti (ziadne nazvy alebo popisy)</li>
            <li>Sifrovane OAuth tokeny</li>
        </ul>
    </div>
</div>
</div>
@endsection
