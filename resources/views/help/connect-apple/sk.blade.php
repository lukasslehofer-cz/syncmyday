@extends('layouts.public')

@section('title', 'Pripojenie Apple iCloud Calendar')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-gray-800 to-black flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Pripojenie Apple iCloud Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Použitie CalDAV s heslom pre aplikaciu</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2 !mt-0">Dolezite: Vyzadovane heslo pre aplikaciu</h3>
            <p class="text-blue-800 mb-2">Apple vyzaduje <strong>Heslo pre aplikaciu</strong> pre aplikacie tretich stran, ak mate povolene dvojfaktorove overenie (ktore je vyzadovane pre vsetky Apple ucty).</p>
            <p class="text-blue-800 mb-0"><strong>Nebojte sa!</strong> Tento sprievodca vas prevedie jeho vygenerovanim. Zaberie to asi 5 minut.</p>
        </div>
    </div>
</div>

<h2>Predpoklady</h2>

<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl mb-8">
    <p class="mb-3">Skor nez zacnete, uistite sa, ze mate:</p>
    <ul class="space-y-2 mb-0">
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>iCloud ucet</strong> (Apple ID) s kalendarmi</span>
        </li>
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Povolene dvojfaktorove overenie</strong> (povolene v predvolenom nastaveni pre vsetky ucty)</span>
        </li>
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Pristup na appleid.apple.com</strong> pre generovanie hesla pre aplikaciu</span>
        </li>
    </ul>
</div>

<h2>Sprievodca krok za krokom</h2>

<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
    <p class="font-semibold text-yellow-900 mb-2">Tento sprievodca ma 2 casti:</p>
    <ol class="text-yellow-800 mb-0 space-y-1">
        <li><strong>Cast A:</strong> Vygenerujte heslo pre aplikaciu od Apple (5 minut)</li>
        <li><strong>Cast B:</strong> Pripojte svoj iCloud kalendar v SyncMyDay (2 minuty)</li>
    </ol>
</div>

<h3 class="text-2xl font-bold text-indigo-600 mb-4">Cast A: Generovanie hesla pre aplikaciu</h3>

<div class="space-y-8 mb-12">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h4 class="!mt-0">Prejdite do nastaveni Apple ID</h4>
            <p>Otvorte prehliadac a prejdite na <a href="https://appleid.apple.com" target="_blank" class="font-semibold">appleid.apple.com</a></p>
            <p>Prihlaste sa pomocou e-mailu a hesla vasho Apple ID.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-1.jpg') }}"
                         alt="Prihlasovacia stranka Apple ID"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Prihlaste sa pomocou vasho Apple ID</p>
            </div>
        </div>
    </div>

    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h4 class="!mt-0">Autentifikujte sa dvojfaktorovo</h4>
            <p>Apple posle overovaci kod na vase doveryhodne zariadenia (iPhone, iPad, Mac). Zadajte 6-miestny kod, ked budete vyzvania.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-2.jpg') }}"
                         alt="Zadanie kodu dvojfaktorovej autentifikacie"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadajte 6-miestny overovaci kod z vasho zariadenia</p>
            </div>
        </div>
    </div>

    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h4 class="!mt-0">Prejdite do sekcie Zabezpecenie</h4>
            <p>Po prihlaseni najdite a kliknite na sekciu <strong>"Prihlasenie a zabezpecenie"</strong>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-3.jpg') }}"
                         alt="Stranka uctu Apple ID so sekciou Prihlasenie a zabezpecenie"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Kliknite na sekciu "Prihlasenie a zabezpecenie"</p>
            </div>
        </div>
    </div>

    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h4 class="!mt-0">Kliknite na "Hesla pre aplikacie"</h4>
            <p>V sekcii Zabezpecenie posunte nadol, kym nenajdete <strong>"Hesla pre aplikacie"</strong> a kliknite na ne.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-4.jpg') }}"
                         alt="Nastavenia zabezpecenia s moznostou Hesla pre aplikacie"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Najdite a kliknite na "Hesla pre aplikacie"</p>
            </div>
        </div>
    </div>

    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h4 class="!mt-0">Vygenerujte nove heslo</h4>
            <p>Kliknite na tlacidlo <strong>"Vygenerovat heslo pre aplikaciu"</strong> (alebo ikonu +).</p>
            <p>Ked budete vyzvania k zadaniu nazvu, zadajte nieco popisne ako:</p>
            <ul class="mb-4">
                <li><code>SyncMyDay</code></li>
                <li><code>SyncMyDay Synchronizacia kalendara</code></li>
            </ul>

            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 text-sm mb-0"><strong>Tip:</strong> Nazov vam pomoze zapametat si, na co je toto heslo, najma ak ho budete neskor potrebovat odvolat.</p>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-5.jpg') }}"
                         alt="Dialog pre zadanie nazvu hesla pre aplikaciu"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadajte nazov ako "SyncMyDay"</p>
            </div>
        </div>
    </div>

    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h4 class="!mt-0">Skopirujte heslo</h4>
            <p>Apple vygeneruje heslo, ktore vypada takto: <code>abcd-efgh-ijkl-mnop</code></p>

            <div class="p-4 bg-red-50 border-2 border-red-300 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-900 mb-1">DOLEZITE: Skopirujte toto heslo TERAZ!</p>
                        <p class="text-red-800 text-sm mb-0">Apple ukaze toto heslo iba raz. Ak ho stratite, budete musiet vygenerovat nove. Skopirujte ho do schranky alebo ho vlozte priamo do SyncMyDay v dalsom kroku.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-6.jpg') }}"
                         alt="Zobrazene vygenerovane heslo pre aplikaciu"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Skopirujte vygenerovane heslo - zobrazi sa iba raz!</p>
            </div>
        </div>
    </div>
</div>

<h3 class="text-2xl font-bold text-purple-600 mb-4">Cast B: Pripojenie v SyncMyDay</h3>

<div class="space-y-8">
    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h4 class="!mt-0">Prejdite na Pripojenia kalendarov</h4>
            <p>Vratte sa do SyncMyDay a prejdite na <strong>Kalendare</strong> v menu, alebo chodte priamo na <a href="{{ route('connections.index') }}">stranku Pripojenia kalendarov</a>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-7.jpg') }}"
                         alt="Dashboard SyncMyDay s menu Kalendare"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Prejdite na stranku Pripojenia kalendarov</p>
            </div>
        </div>
    </div>

    <!-- Krok 8 -->
    <div class="flex items-start">
        <span class="step-number">8</span>
        <div class="flex-1">
            <h4 class="!mt-0">Kliknite na "Pripojit Apple iCloud"</h4>
            <p>Najdite a kliknite na tlacidlo <strong>Apple iCloud</strong> s logom Apple.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-8.jpg') }}"
                         alt="Poskytovatelia kalendarov s moznostou Apple iCloud"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Kliknite na tlacidlo "Apple / CalDAV"</p>
            </div>
        </div>
    </div>

    <!-- Krok 9 -->
    <div class="flex items-start">
        <span class="step-number">9</span>
        <div class="flex-1">
            <h4 class="!mt-0">Zadajte svoje prihlasovacie udaje</h4>
            <p>Vyplnte pripojovaci formular:</p>
            <ul>
                <li><strong>E-mail:</strong> Vas cely Apple ID e-mail (napr. vas.email@icloud.com)</li>
                <li><strong>Heslo:</strong> Vlozte heslo pre aplikaciu, ktore ste skopirovali od Apple (vratane pomlciek alebo bez - oboje funguje)</li>
            </ul>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Pouzite heslo pre aplikaciu</p>
                        <p class="text-blue-800 text-sm mb-0">NEPOUZIVAJTE svoje bezne heslo Apple ID. Pouzite heslo pre aplikaciu, ktore ste prave vygenerovali. Vase bezne heslo nebude fungovat.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-9.jpg') }}"
                         alt="Pripojovaci formular iCloud s polami pre e-mail a heslo"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadajte svoj Apple ID email a heslo pre aplikaciu</p>
            </div>
        </div>
    </div>

    <!-- Krok 10 -->
    <div class="flex items-start">
        <span class="step-number">10</span>
        <div class="flex-1">
            <h4 class="!mt-0">Vyberte kalendare</h4>
            <p>Po pripojeni SyncMyDay nacita vase iCloud kalendare. Vyberte, ktore chcete synchronizovat.</p>
            <p>Bezne iCloud kalendare zahrnuju:</p>
            <ul>
                <li><strong>Domov</strong> - Vas predvoleny osobny kalendar</li>
                <li><strong>Praca</strong> - Ak ste vytvorili pracovny kalendar</li>
                <li><strong>Rodina</strong> - Zdielany rodinny kalendar</li>
                <li>Akekolvek vlastne kalendare, ktore ste vytvorili</li>
            </ul>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-10.jpg') }}"
                         alt="Vyber kalendara s iCloud kalendarmi"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte kalendare, ktore chcete synchronizovat</p>
            </div>
        </div>
    </div>

    <!-- Krok 11 -->
    <div class="flex items-start">
        <span class="step-number">11</span>
        <div class="flex-1">
            <h4 class="!mt-0">Vsetko hotovo!</h4>
            <p>Vas Apple iCloud kalendar je teraz pripojeny! Uvidite ho vo vasom zozname pripojeni kalendarov.</p>

            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">Co dalej?</h4>
                <ul class="text-green-800 space-y-1 mb-2">
                    <li>Vas iCloud kalendar je pripraveny na pouzitie v pravidlach synchronizacie</li>
                    <li>Udalosti sa budu synchronizovat kazdych 15 minut (obmedzenie CalDAV)</li>
                    <li>Teraz mozete vytvarat pravidla synchronizacie pre udrzanie kalendarov synchronizovanych</li>
                </ul>
                <p class="text-green-800 text-sm mb-0"><strong>Poznamka:</strong> iCloud pouziva protokol CalDAV, ktory nepodporuje webhooky v realnom case. Kontrolujeme zmeny kazdych 15 minut, aby sme boli aktualni.</p>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/apple-11.jpg') }}"
                         alt="Uspesne pripojeny iCloud kalendar"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vas iCloud kalendar je uspesne pripojeny a aktivny</p>
            </div>
        </div>
    </div>
</div>

<h2>Riesenie problemov</h2>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            "Autentifikacia sa nepodarila" alebo "Neplatne prihlasovacie udaje"
        </h3>
        <p><strong>Caste priciny:</strong></p>
        <ul>
            <li>Pouzili ste svoje bezne heslo Apple ID namiesto hesla pre aplikaciu</li>
            <li>Preklep v e-mailovej adrese alebo hesle</li>
            <li>Heslo pre aplikaciu bolo odvolane</li>
        </ul>
        <p><strong>Riesenie:</strong></p>
        <ol>
            <li>Skontrolujte, ze pouzivate heslo pre aplikaciu, nie bezne heslo</li>
            <li>Vygenerujte nove heslo pre aplikaciu a skuste to znovu</li>
            <li>Uistite sa, ze vas e-mail je spravny (vratane @icloud.com alebo @me.com)</li>
        </ol>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Nevidim moznost "Hesla pre aplikacie"
        </h3>
        <p>To obvykle znamena, ze na vasom ucte nie je povolene dvojfaktorove overenie.</p>
        <p><strong>Riesenie:</strong></p>
        <ol>
            <li>Prejdite do nastaveni Apple ID na appleid.apple.com</li>
            <li>Prejdite na Prihlasenie a zabezpecenie</li>
            <li>Povolte dvojfaktorove overenie</li>
            <li>Po povoleni sa objavi moznost Hesla pre aplikacie</li>
        </ol>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Synchronizacia je pomala (15-minutove oneskorenie)
        </h3>
        <p>To je normalne pre iCloud kalendare. Protokol CalDAV od Apple nepodporuje webhooky v realnom case ako Google alebo Microsoft.</p>
        <p><strong>Preco?</strong> Kontrolujeme iCloud kazdych 15 minut na detekciu zmien. To je standardny pristup pre poskytovatelov CalDAV a vyvazuje odozvu so zatazenim servera.</p>
        <p><strong>Alternativa:</strong> Ak potrebujete okamzitu synchronizaciu, zvazte pouzitie Google Calendar alebo Microsoft 365, ktore oba podporuju webhooky v realnom case.</p>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Ako odvolam heslo pre aplikaciu?
        </h3>
        <p>Ak potrebujete odvolat pristup:</p>
        <ol>
            <li>Prejdite na appleid.apple.com</li>
            <li>Prihlaste sa a prejdite na Prihlasenie a zabezpecenie</li>
            <li>Kliknite na Hesla pre aplikacie</li>
            <li>Najdite "SyncMyDay" v zozname</li>
            <li>Kliknite na "Odvolat" vedla neho</li>
        </ol>
        <p class="mb-0">Mozete tiez odpojit kalendar zo stranky Pripojenia kalendarov v SyncMyDay a prestaneme pouzivat prihlasovacie udaje.</p>
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
        <p class="mb-0">Pripojte pracovny kalendar (Google, Microsoft) pre synchronizaciu s vasim osobnym iCloud kalendarom.</p>
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
        <p class="mb-0">Zacnite synchronizovat udalosti medzi vasimi kalendarmi.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Pre vyvojarov a technickych pouzivatelov</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Protokol CalDAV</h4>
        <p>Apple iCloud pouziva protokol CalDAV (RFC 4791):</p>
        <ul>
            <li><strong>URL servera:</strong> <code>https://caldav.icloud.com</code></li>
            <li><strong>Principal URL:</strong> Automaticky zistene pomocou DAV service discovery</li>
            <li><strong>Autentifikacia:</strong> Basic Auth s Apple ID + heslom pre aplikaciu</li>
        </ul>

        <h4>Interval pollingu</h4>
        <p>Pretoze CalDAV nepodporuje push notifikacie, pollujeme kazdych 15 minut:</p>
        <ul>
            <li>Pouziva PROPFIND poziadavky na kontrolu metadat kalendara</li>
            <li>Stahuje iba zmenene udalosti (pomocou ETags)</li>
            <li>Minimalizuje sirku pasma a respektuje limity Apple</li>
        </ul>

        <h4>Ukladanie prihlasovacich udajov</h4>
        <ul>
            <li>Hesla pre aplikacie su sifrovane pomocou AES-256</li>
            <li>Bezpecne ulozene v nasej databaze</li>
            <li>Nikdy neprenesene v cistom texte (vzdy HTTPS)</li>
            <li>Okamzite zmazane pri odpojeni kalendara</li>
        </ul>

        <h4>Kompatibilita</h4>
        <p>Tato metoda pripojenia funguje s:</p>
        <ul>
            <li>Kalendarmi iCloud.com</li>
            <li>Kalendarmi synchronizovanymi do iCloud z iOS zariadeni</li>
            <li>Kalendarmi synchronizovanymi z aplikacie Kalendar macOS</li>
            <li>Zdielanymi iCloud kalendarmi (ak mate opravnenie na zapis)</li>
        </ul>

        <h4>Obmedzenia</h4>
        <ul>
            <li><strong>Ziadna synchronizacia v realnom case:</strong> 15-minutovy interval pollingu</li>
            <li><strong>Vyzadovane heslo pre aplikaciu:</strong> Nemozno pouzit bezne heslo</li>
            <li><strong>Vyzadovane dvojfaktorove overenie:</strong> Vsetky iCloud ucty teraz vyzaduju 2FA</li>
        </ul>
    </div>
</div>
</div>
@endsection
