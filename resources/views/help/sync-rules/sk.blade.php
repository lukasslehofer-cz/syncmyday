@extends('layouts.public')

@section('title', 'Vytváranie pravidiel synchronizacie')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl gradient-bg flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Vytváranie pravidiel synchronizacie</h1>
        <p class="text-lg text-gray-600 !mb-0">Nastavte automaticku synchronizaciu kalendarov</p>
    </div>
</div>

<div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-indigo-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-indigo-900 mb-2">Co je pravidlo synchronizacie?</h3>
            <p class="text-indigo-800 mb-2"><strong>Pravidlo synchronizacie</strong> definuje, ako maju byt udalosti z jedneho kalendara (<em>zdroja</em>) synchronizovane do ineho kalendara (<em>ciela</em>) ako blokovacie udalosti.</p>
            <p class="text-indigo-800 mb-0"><strong>Priklad:</strong> "Synchronizovat vsetky zaneprazdnene udalosti z mojho osobneho kalendara Google do pracovneho kalendara Outlook ako blokatory 'Zaneprazdneny'."</p>
        </div>
    </div>
</div>

<h2>Skor nez zacnete</h2>

<div class="p-6 bg-yellow-50 border border-yellow-200 rounded-xl mb-8">
    <p class="font-semibold text-yellow-900 mb-2">Uistite sa, ze mate:</p>
    <ul class="text-yellow-800 space-y-1 mb-0">
        <li><strong>Aspon 2 pripojene kalendare</strong> - Potrebujete zdrojovy kalendar a cielovy kalendar</li>
        <li><strong>Oba kalendare zobrazuju stav "Aktivny"</strong> - Skontrolujte stranku Pripojenia kalendarov</li>
        <li><strong>Udalosti v zdrojovom kalendari</strong> - Na otestovanie synchronizacie</li>
    </ul>
</div>

<h2>Sprievodca krok za krokom</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Prejdite na Pravidla synchronizacie</h3>
            <p>Prejdite na <strong>Pravidla synchronizacie</strong> v hlavnom menu, alebo chodte priamo na stranku Pravidla synchronizacie z vasho dashboardu.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-1.jpg') }}"
                         alt="Dashboard so zvyraznenym menu Pravidla synchronizacie"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Prejdite na stranku Synchronizacne pravidla</p>
            </div>
        </div>
    </div>

    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Kliknite na "Vytvorit nove pravidlo synchronizacie"</h3>
            <p>Na stranke Pravidla synchronizacie kliknite na tlacidlo <strong>"Vytvorit nove pravidlo synchronizacie"</strong> alebo <strong>"+ Nove pravidlo"</strong>.</p>

        </div>
    </div>

    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte zdrojovy kalendar</h3>
            <p>Vyberte, udalosti z akeho kalendara chcete synchronizovat <strong>Z</strong>.</p>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <p class="text-blue-900 text-sm mb-2"><strong>Co je zdrojovy kalendar?</strong></p>
                <p class="text-blue-800 text-sm mb-0">Zdrojovy kalendar je miesto, kde su vase skutocne udalosti. Ked v tomto kalendari vytvorite, aktualizujete alebo zmazete udalosti, SyncMyDay automaticky vytvori alebo aktualizuje blokovacie udalosti vo vasich cielovych kalendaroch.</p>
            </div>

            <p><strong>Bezne priklady:</strong></p>
            <ul>
                <li><strong>Osobny kalendar</strong> (zdroj) -> Pracovny kalendar (ciel): Blokovat pracovny cas, ked mate osobne schodzky</li>
                <li><strong>Pracovny kalendar</strong> (zdroj) -> Osobny kalendar (ciel): Blokovat osobny cas, ked mate pracovne schodzky</li>
            </ul>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-3.jpg') }}"
                         alt="Rozbalovacie menu zdrojoveho kalendara zobrazujuce pripojene kalendare"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte zdrojovy kalendar (odkial kopirovat udalosti)</p>
            </div>
        </div>
    </div>

    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte cielovy kalendar(e)</h3>
            <p>Vyberte jeden alebo viac kalendarov, kde maju byt vytvorene blokovacie udalosti.</p>

            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 text-sm mb-2"><strong>Tip: Viac cielov</strong></p>
                <p class="text-purple-800 text-sm mb-0">Mozete vybrat viac cielovych kalendarov! Napriklad synchronizujte osobne udalosti sucasne do pracovneho kalendara Google A pracovneho kalendara Outlook.</p>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-4.jpg') }}"
                         alt="Vyber cieloveho kalendara so zaskrtavacimi polickami"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte cielove kalendare (kam kopirovat udalosti)</p>
            </div>
        </div>
    </div>

    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Nakonfigurujte nazov blokovacej udalosti</h3>
            <p>Zadajte text, ktory sa zobrazi ako nazov pre vsetky blokovacie udalosti vytvorene tymto pravidlom.</p>

            <p><strong>Oblubene nazvy:</strong></p>
            <ul>
                <li><code>Zaneprazdneny</code> - Jednoduche a univerzalne</li>
                <li><code>Osobny cas</code> - Indikuje sukromny cas</li>
                <li><code>Nie je k dispozicii</code> - Jasna nedostupnost</li>
                <li><code>Schodzka</code> - Obecny zastupny symbol</li>
                <li><code>Sukromne</code> - Pre vizualne odlisenie</li>
            </ul>

            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                <p class="text-green-900 text-sm mb-1"><strong>Pamatajte:</strong></p>
                <p class="text-green-800 text-sm mb-0">Nazov blokatora je to, co ostatni uvidia vo vasom kalendari. Vyberte nieco vhodne pre vas kontext (praca, osobne atd.).</p>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-5.jpg') }}"
                         alt="Vstupne pole nazvu blokatora"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadajte nazov pre blokujuce udalosti</p>
            </div>
        </div>
    </div>

    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Nastavte filtre (Volitelne, ale odporucane)</h3>
            <p>Filtre kontroluju, <strong>ktore udalosti</strong> sa synchronizuju. Tu mozete doladit svoju synchronizaciu.</p>

            <h4 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Dostupne filtre:</h4>

            <!-- Iba zaneprazdnene udalosti -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-gray-900">Synchronizovat iba zaneprazdnene udalosti</h4>
                        <p class="text-gray-700 text-sm mb-2">Synchronizovat iba udalosti oznacene ako "Zaneprazdneny". Preskocit udalosti oznacene ako "Volny" alebo "Predbezne".</p>
                        <p class="text-gray-600 text-xs mb-0"><strong>Pouzitie:</strong> Zabranit predbeznym schodzam v blokovani ostatnych kalendarov, kym nie su potvrdene.</p>
                    </div>
                </div>
            </div>

            <!-- Ignorovat celodenne udalosti -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-gray-900">Ignorovat celodenne udalosti</h4>
                        <p class="text-gray-700 text-sm mb-2">Nesynchronizovat celodenne udalosti ako su sviatky, narodeniny alebo dni mimo kancelarie.</p>
                        <p class="text-gray-600 text-xs mb-0"><strong>Pouzitie:</strong> Celodenne udalosti casto nemusia blokovat ostatne kalendare (napr. verejne sviatky).</p>
                    </div>
                </div>
            </div>

            <!-- Iba pracovna doba -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-indigo-900">Zapnut casovy a denny filter</h4>
                        <p class="text-indigo-800 text-sm mb-3">Synchronizovat iba udalosti, ktore spadaju do specifickych hodin a dni.</p>

                        <p class="text-indigo-700 text-xs mt-3 mb-0"><strong>Pouzitie:</strong> Blokovat iba pracovny kalendar pocas pracovnej doby. Osobne udalosti vecer alebo cez vikendy sa nebudu synchronizovat.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-6.jpg') }}"
                         alt="Moznosti filtrov so zaskrtavacimi polickami a selektormi casu"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Nastavte filtre (volitelne) pre specificke pripady</p>
            </div>
        </div>
    </div>

    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Skontrolujte a ulozte</h3>
            <p>Skontrolujte nastavenia pravidla synchronizacie a kliknite na <strong>"Vytvorit pravidlo synchronizacie"</strong> alebo <strong>"Ulozit"</strong>.</p>

            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">Pravidlo synchronizacie vytvorene!</h4>
                <p class="text-green-800 mb-2">Vase kalendare sa teraz automaticky synchronizuju. Co sa stane dalej:</p>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li><strong>Pociatocna synchronizacia:</strong> Vsetky existujuce udalosti z vasho zdrojoveho kalendara budu synchronizovane pocas minut</li>
                    <li><strong>Aktualizacie v realnom case:</strong> Nove, aktualizovane alebo zmazane udalosti sa budu synchronizovat automaticky</li>
                    <li><strong>Mozete pozastavit alebo upravit:</strong> Pravidlo synchronizacie kedykolvek zo stranky Pravidla synchronizacie</li>
                </ul>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-7.jpg') }}"
                         alt="Potvrdovacia stranka zobrazujuca aktivne pravidlo synchronizacie"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vase pravidlo synchronizacie je aktivne a funkcne!</p>
            </div>
        </div>
    </div>
</div>

<h2>Bezne priklady pravidiel synchronizacie</h2>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 border-2 border-blue-200 bg-blue-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-blue-900 mb-3">Osobny -> Praca</h3>
        <ul class="text-blue-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Osobny kalendar Google</li>
            <li><strong>Ciel:</strong> Pracovny kalendar Outlook</li>
            <li><strong>Nazov:</strong> "Osobny cas"</li>
            <li><strong>Filtre:</strong> Iba pracovna doba (9-17, Po-Pia), Iba zaneprazdnene udalosti</li>
            <li><strong>Vysledok:</strong> Kolegovia vidia, ze ste zaneprazdneni pocas osobnych schodzok, ale iba pocas pracovnej doby</li>
        </ul>
    </div>

    <div class="p-6 border-2 border-purple-200 bg-purple-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-purple-900 mb-3">Praca -> Osobny</h3>
        <ul class="text-purple-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Pracovny kalendar Outlook</li>
            <li><strong>Ciel:</strong> Osobny kalendar Google</li>
            <li><strong>Nazov:</strong> "Pracovna schodzka"</li>
            <li><strong>Filtre:</strong> Ignorovat celodenne udalosti, Iba zaneprazdnene udalosti</li>
            <li><strong>Vysledok:</strong> Vas osobny kalendar ukazuje, kedy mate pracovne schodzky (uzitocne pre rodinne planovanie)</li>
        </ul>
    </div>

    <div class="p-6 border-2 border-green-200 bg-green-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-green-900 mb-3">Rodinny kalendar -> Praca</h3>
        <ul class="text-green-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Zdielany rodinny kalendar Google</li>
            <li><strong>Ciel:</strong> Pracovny kalendar</li>
            <li><strong>Nazov:</strong> "Rodinny zavazok"</li>
            <li><strong>Filtre:</strong> Iba pracovna doba</li>
            <li><strong>Vysledok:</strong> Tim vie, ze nie ste k dispozicii kvoli rodinnym udalostiam ako je vyzdvihnutie deti zo skoly</li>
        </ul>
    </div>

    <div class="p-6 border-2 border-orange-200 bg-orange-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-orange-900 mb-3">Viac osobnych -> Praca</h3>
        <ul class="text-orange-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Osobny kalendar</li>
            <li><strong>Ciele:</strong> Pracovny Google + Pracovny Outlook + Pracovny iCloud</li>
            <li><strong>Nazov:</strong> "Zaneprazdneny"</li>
            <li><strong>Filtre:</strong> Pracovna doba, Iba zaneprazdnene</li>
            <li><strong>Vysledok:</strong> Zablokujte vsetky vase pracovne kalendare naraz</li>
        </ul>
    </div>
</div>

<h2>Sprava pravidiel synchronizacie</h2>

<div class="space-y-4">
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pozastavit pravidlo synchronizacie
        </h3>
        <p class="text-gray-700 mb-0">Potrebujete docasne zastavit synchronizaciu? Kliknite na tlacidlo "Pozastavit" pri akokolvek pravidle synchronizacie. Blokovacie udalosti zostanu, ale nove nebudu vytvarane, kym ju neobnovite. Skvele pre dovolenku alebo zmeny projektu.</p>
    </div>

    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Upravit pravidlo synchronizacie
        </h3>
        <p class="text-gray-700 mb-0">Kliknite na "Upravit" pre zmenu akehokolvek nastavenia—filtre, nazov blokatora, cielove kalendare atd. Zmeny sa vztahuju na nove blokovacie udalosti. Existujuce blokatory zostavaju bez zmeny, ak sa nezmeni zdrojova udalost.</p>
    </div>

    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Zmazat pravidlo synchronizacie
        </h3>
        <p class="text-gray-700 mb-0">Kliknite na "Zmazat" pre trvale odstranenie pravidla synchronizacie. <strong>Vsetky blokovacie udalosti</strong> vytvorene tymto pravidlom budu automaticky zmazane z vasich cielovych kalendarov. Tato akcia sa neda vratit spat.</p>
    </div>
</div>

<h2>Riesenie problemov</h2>

<div class="space-y-4" x-data="{ open: null }">
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble1' ? open = null : open = 'trouble1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Blokovacie udalosti sa neobjavuju</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble1'" x-collapse class="px-6 pb-4">
            <p class="mb-2"><strong>Skontrolujte tieto veci:</strong></p>
            <ol class="space-y-2 mb-0">
                <li>Stav pravidla synchronizacie je "Aktivny" (nie je pozastavene)</li>
                <li>Zdrojovy a cielovy kalendar zobrazuju stav "Aktivny"</li>
                <li>Udalost splna kriteria filtra (skontrolujte stav zaneprazdnenosti, celodenne, pracovnu dobu)</li>
                <li>Pockajte niekolko minut (CalDAV kalendare kontroluju kazdych 15 minut)</li>
                <li>Skontrolujte, ci bola dokoncena pociatocna synchronizacia (hladajte casove razitko synchronizacie)</li>
            </ol>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble2' ? open = null : open = 'trouble2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Prilis vela/malo udalosti sa synchronizuje</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble2'" x-collapse class="px-6 pb-4">
            <p class="mb-2">Upravte svoje filtre:</p>
            <ul class="mb-0">
                <li><strong>Prilis vela?</strong> Povolte "Ignorovat celodenne udalosti" alebo "Iba zaneprazdnene udalosti" alebo obmedzit na pracovnu dobu</li>
                <li><strong>Prilis malo?</strong> Zakazte filtre pre synchronizaciu vsetkych udalosti, alebo upravte pracovnu dobu pre zahrnutie viac casu</li>
                <li><strong>Tip:</strong> Upravte pravidlo synchronizacie a skuste rozne kombinacie filtrov, kym nebude fungovat pre vase potreby</li>
            </ul>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble3' ? open = null : open = 'trouble3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Blokovacie udalosti zobrazuju nespravne casy</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble3'" x-collapse class="px-6 pb-4">
            <p class="mb-2">To je obvykle problem s casovym pasmom:</p>
            <ul class="mb-0">
                <li>Skontrolujte casove pasmo uctu v Nastaveniach</li>
                <li>Overte nastavenie casoveho pasma zdrojoveho kalendara</li>
                <li>Skontrolujte nastavenie casoveho pasma cieloveho kalendara</li>
                <li>Ak pouzivate CalDAV, uistite sa, ze je casove pasmo spravne naconfigurovane v kalendarovej sluzbe</li>
            </ul>
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
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Pripojte viac kalendarov</h3>
        </div>
        <p class="mb-0">Pridajte dalsie pripojenia kalendarov pre vytvorenie dalsich pravidiel synchronizacie.</p>
    </a>

    <a href="{{ route('help.faq') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Pozrite sa na FAQ</h3>
        </div>
        <p class="mb-0">Dalsie odpovede na caste otazky o SyncMyDay.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Ako funguju pravidla synchronizacie pod poklickou</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Architektura synchronizacneho enginu</h4>
        <p>Ked vytvorite pravidlo synchronizacie, stane sa toto:</p>
        <ol>
            <li><strong>Pociatocna synchronizacia:</strong> Vsetky udalosti zo zdrojoveho kalendara v casovom rozmedzí (predvolene: minulych 7 dni, buducich 90 dni) su synchronizovane</li>
            <li><strong>Registracia webhookov:</strong> Pre Google/Microsoft su registrovane webhooky na prijem notifikacii v realnom case</li>
            <li><strong>Spracovanie udalosti:</strong> Kazda udalost je skontrolovana proti filtrom pred vytvorenim blokatora</li>
            <li><strong>Vytvorenie blokatora:</strong> Nova udalost je vytvorena v cielovych kalendaroch s vasim vlastnym nazvom</li>
            <li><strong>Sledovanie:</strong> Databazovy zaznam prepaja zdrojovu udalost s blokovacimi udalostami pre buduce aktualizacie/mazanie</li>
        </ol>

        <h4>Realny cas vs. Polling</h4>
        <ul>
            <li><strong>Google & Microsoft:</strong> Realny cas cez webhooky (latencia 1-2 minuty)</li>
            <li><strong>CalDAV & E-mail:</strong> Polling kazdych 15 minut</li>
            <li><strong>Obnovenie webhookov:</strong> Automaticke kazde 3-7 dni (lisi sa podla poskytovatela)</li>
        </ul>

        <h4>Spracovanie filtrov</h4>
        <p>Filtre su aplikovane v tomto poradi:</p>
        <ol>
            <li>Kontrola, ci je udalost celodenna (ak je povolene "Ignorovat celodenne udalosti")</li>
            <li>Kontrola stavu udalosti (ak je povolene "Iba zaneprazdnene udalosti")</li>
            <li>Kontrola, ci cas udalosti spada do pracovnej doby (ak je naconfigurovane)</li>
            <li>Kontrola, ci den udalosti je zahrnuty vo vybranych dnoch (ak je povolena pracovna doba)</li>
        </ol>
        <p>Udalost musi prejst VSETKYMI povolenymi filtrami, aby bola synchronizovana.</p>

        <h4>Prevencia duplicit</h4>
        <p>SyncMyDay zabranuje duplicitnym blokovacim udalostiam pomocou:</p>
        <ul>
            <li>Jedinecnych identifikatorov prepajajucich zdrojove udalosti s blokatormi</li>
            <li>Hash-based detekcie existujucich blokatorov</li>
            <li>Cistenia osirenych blokatorov pri zmazani pravidiel</li>
        </ul>

        <h4>Vykon</h4>
        <ul>
            <li><strong>Databaza:</strong> Indexovane podla pouzivatela, kalendara a pravidla synchronizacie pre rychle vyhladavanie</li>
            <li><strong>Caching:</strong> Tokeny pripojenia a metadata su ulozene v cache v Redis (ak je k dispozicii)</li>
            <li><strong>Fronty:</strong> Velke operacie synchronizacie su spracovane v pozadi</li>
            <li><strong>Obmedzenie rychlosti:</strong> API volania su throttlovane pre respektovanie limitov poskytovatelov</li>
        </ul>
    </div>
</div>
</div>
@endsection
