@extends('layouts.public')

@section('title', 'Pripojenie e-mailoveho kalendara')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-green-500 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Pripojenie e-mailoveho kalendara</h1>
        <p class="text-lg text-gray-600 !mb-0">Prijimajte pozvanky do kalendara e-mailom</p>
    </div>
</div>

<div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2 !mt-0">Co je e-mailovy kalendar?</h3>
            <p class="text-blue-800 mb-2"><strong>E-mailovy kalendar</strong> je unikatny sposob synchronizacie kalendarov prostrednictvom preposielania pozvanok do kalendara (subory .ics) e-mailom. To je idealne pre kalendare, ktore nemaju API pristup, alebo ked chcete urcite kalendare udrzat uplne oddelene.</p>
            <p class="text-blue-800 mb-0"><strong>Ako to funguje:</strong> Ked su v kalendari zdroja vytvorene udalosti, SyncMyDay odesle e-mailove pozvanky na specialnu adresu. Tieto pozvanky sa automaticky zobrazia ako blokovacie udalosti.</p>
        </div>
    </div>
</div>

<h2>Kedy pouzivat e-mailove kalendare</h2>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-green-900 mb-3">Skvele pre</h3>
        <ul class="text-green-800 space-y-2 mb-0">
            <li>Kalendare bez podpory API</li>
            <li>Starsie e-mailove klienty (Thunderbird, Lotus Notes)</li>
            <li>Prijimanie blokovacich pozvanok do e-mailovej schranky</li>
            <li>Jednoduchu jednosmernu synchronizaciu</li>
            <li>Maximalne sukromie (udalosti iba cez zabezpeceny e-mail)</li>
        </ul>
    </div>

    <div class="p-6 bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-yellow-900 mb-3">Zvazte alternativy, ak</h3>
        <ul class="text-yellow-800 space-y-2 mb-0">
            <li>Potrebujete synchronizaciu v realnom case (e-mail ma oneskorenie)</li>
            <li>Vas kalendar podporuje API pristup (Google, Microsoft)</li>
            <li>Potrebujete obojsmernu synchronizaciu</li>
            <li>Chcete automaticke prijatie (e-mailove kalendare vyzaduju manualne akcie)</li>
        </ul>
    </div>
</div>

<h2>Dva sposoby pouzitia e-mailovych kalendarov</h2>

<div class="space-y-6 mb-8">
    <div class="border-2 border-indigo-200 rounded-xl p-6 bg-indigo-50">
        <div class="flex items-start">
            <div class="w-12 h-12 rounded-lg bg-indigo-600 flex items-center justify-center mr-4 flex-shrink-0">
                <span class="text-white font-bold text-2xl">1</span>
            </div>
            <div>
                <h3 class="!mt-0 !mb-2 text-xl font-bold text-indigo-900">Prijimanie blokatorov e-mailom</h3>
                <p class="text-indigo-800 mb-0">Ked mate udalosti v kalendari Google/Microsoft, SyncMyDay odesle e-mailove pozvanky na lubovolnu vami zadanu e-mailovu adresu. Tieto pozvanky mozete prijat vo svojom e-mailovom klientovi (Outlook, Thunderbird atd.) a objavia sa vo vasom kalendari.</p>
            </div>
        </div>
    </div>

    <div class="border-2 border-purple-200 rounded-xl p-6 bg-purple-50">
        <div class="flex items-start">
            <div class="w-12 h-12 rounded-lg bg-purple-600 flex items-center justify-center mr-4 flex-shrink-0">
                <span class="text-white font-bold text-2xl">2</span>
            </div>
            <div>
                <h3 class="!mt-0 !mb-2 text-xl font-bold text-purple-900">Preposielanie pozvanok do SyncMyDay</h3>
                <p class="text-purple-800 mb-0">Ziskajte jedinecnu e-mailovu adresu od SyncMyDay (napr. <code>abc123@syncmyday.com</code>). Ked obdrzite pozvanky do kalendara na tuto adresu, SyncMyDay automaticky vytvori blokovacie udalosti v ostatnych pripojenych kalendaroch.</p>
            </div>
        </div>
    </div>
</div>

<h2>Sprievodca nastavenim</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Prejdite na Pripojenia kalendarov</h3>
            <p>Prejdite na <strong>Kalendare</strong> v menu, alebo chodte na <a href="{{ route('connections.index') }}">stranku Pripojenia kalendarov</a>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/email-1.jpg') }}"
                         alt="Dashboard so zvyraznenym menu Kalendare"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Prejdite na stranku Pripojenia kalendarov</p>
            </div>
        </div>
    </div>

    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Kliknite na "Pripojit e-mailovy kalendar"</h3>
            <p>Najdite a kliknite na tlacidlo <strong>E-mailovy kalendar</strong> s ikonou obalky.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/email-2.jpg') }}"
                         alt="Poskytovatelia kalendarov s moznostou E-mailovy kalendar"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Kliknite na zelene tlacidlo "Emailovy kalendar"</p>
            </div>
        </div>
    </div>

    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte metodu nastavenia</h3>
            <p>Uvidite dve moznosti:</p>

            <div class="space-y-4">
                <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4 class="!mt-0 text-lg font-semibold text-blue-900 mb-2">Moznost A: Prijimanie pozvanok</h4>
                    <p class="text-blue-800 text-sm mb-2">Zadajte e-mailovu adresu, na ktorej chcete prijimat pozvanky do kalendara. Tento e-mail by mal byt pripojeny k aplikacii kalendara (Outlook, Thunderbird, Apple Mail atd.).</p>
                    <p class="text-blue-800 text-sm font-semibold mb-0">Priklad: <code>moja-praca@firma.sk</code></p>
                </div>

                <div class="p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4 class="!mt-0 text-lg font-semibold text-purple-900 mb-2">Moznost B: Ziskanie jedinecnej adresy</h4>
                    <p class="text-purple-800 text-sm mb-2">SyncMyDay vygeneruje pre vas jedinecnu e-mailovu adresu (napr. <code>abc123@syncmyday.com</code>). Preposielajte pozvanky do kalendara na tuto adresu a my ich automaticky spracujeme.</p>
                    <p class="text-purple-800 text-sm font-semibold mb-0">Nie je potrebne zadavat e-mail—staci kliknut na "Vygenerovat adresu"</p>
                </div>
            </div>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mt-4">
                <p class="text-yellow-900 text-sm mb-0"><strong>Mozete pouzit obe metody!</strong> Vytvorte jeden e-mailovy kalendar pre prijimanie pozvanok a dalsi pre odosielanie pozvanok do SyncMyDay.</p>
            </div>
        </div>
    </div>

    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Dajte mu nazov</h3>
            <p>Zadajte popisny nazov pre tento e-mailovy kalendar, napriklad:</p>
            <ul>
                <li><code>Pracovny e-mailovy kalendar</code></li>
                <li><code>Thunderbird kalendar</code></li>
                <li><code>Outlook Desktop</code></li>
            </ul>
            <p>To vam pomoze identifikovat, ktory e-mailovy kalendar je ktory, ak vytvorite viac.</p>
        </div>
    </div>

    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Ulozit a pripojit</h3>
            <p>Kliknite na <strong>"Pripojit"</strong> alebo <strong>"Ulozit"</strong>. Vas e-mailovy kalendar sa objavi v zozname pripojeni.</p>

            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">E-mailovy kalendar pripojeny!</h4>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li>Ak ste zvolili <strong>Moznost A</strong>: Budete dostavat e-mailove pozvanky na zadanu adresu, ked budu synchronizovane udalosti</li>
                    <li>Ak ste zvolili <strong>Moznost B</strong>: Skopirujte jedinecnu adresu a nastavte preposielanie e-mailov (dalsi krok)</li>
                </ul>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/email-3.jpg') }}"
                         alt="Uspesne pripojeny e-mailovy kalendar"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vas e-mailovy kalendar je uspesne pripojeny a aktivny</p>
            </div>
        </div>
    </div>
</div>

<h2>Nastavenie preposielania e-mailov (Moznost B)</h2>

<p>Ak ste si zvolili ziskanie jedinecnej adresy SyncMyDay, musite na nu nastavit preposielanie pozvanok do kalendara:</p>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-blue-500 flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
            </div>
            Gmail
        </h3>
        <ol class="space-y-2 mb-0">
            <li>Prejdite do Nastaveni Gmailu (-> Zobrazit vsetky nastavenia)</li>
            <li>Kliknite na zalozku <strong>"Preposielanie a POP/IMAP"</strong></li>
            <li>Kliknite na <strong>"Pridat adresu pre preposielanie"</strong></li>
            <li>Zadajte vasu adresu SyncMyDay (napr. <code>abc123@syncmyday.com</code>)</li>
            <li>Gmail odesle potvrdovaci kod na tuto adresu (skontrolujte s nami!)</li>
            <li>Po potvrdeni nastavte filter pre preposielanie iba pozvanok do kalendara</li>
        </ol>
    </div>

    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
                </svg>
            </div>
            Outlook / Microsoft 365
        </h3>
        <ol class="space-y-2 mb-0">
            <li>Prejdite do Nastaveni Outlooku (-> Zobrazit vsetky nastavenia Outlooku)</li>
            <li>Prejdite na <strong>Posta -> Preposielanie</strong></li>
            <li>Povolte preposielanie a zadajte vasu adresu SyncMyDay</li>
            <li>Ulozte zmeny</li>
            <li>Volitelne vytvorte pravidlo pre preposielanie iba e-mailov s prilohou <code>.ics</code></li>
        </ol>
    </div>

    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center mr-3">
                <span class="text-white font-bold">@</span>
            </div>
            Ostatni e-mailovi klienti
        </h3>
        <p class="mb-2">Vacsina e-mailovych klientov podporuje pravidla preposielania. Hladajte:</p>
        <ul class="mb-0">
            <li><strong>Filtre</strong> alebo <strong>Pravidla</strong> v nastaveniach</li>
            <li>Vytvorte pravidlo: "Ked sprava ma prilohu s priponou <code>.ics</code>"</li>
            <li>Akcia: "Preposlat na <code>vase-syncmyday-adresa@syncmyday.com</code>"</li>
        </ul>
    </div>
</div>

<h2>Vytváranie pravidiel synchronizacie s e-mailovymi kalendarmi</h2>

<p>Akmile je vas e-mailovy kalendar pripojeny, mozete ho pouzit v pravidlach synchronizacie:</p>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-indigo-900 mb-3">Ako ciel (prijimanie pozvanok)</h3>
        <p class="text-indigo-800 mb-3"><strong>Priklad:</strong> Google Calendar -> E-mailovy kalendar</p>
        <ul class="text-indigo-700 space-y-1 mb-0 text-sm">
            <li>Zdroj: Vas pracovny kalendar Google</li>
            <li>Ciel: E-mailovy kalendar s <code>osobne@example.com</code></li>
            <li>Vysledok: Budete dostavat e-mailove pozvanky na vsetky pracovne udalosti na osobny e-mail</li>
        </ul>
    </div>

    <div class="p-6 border-2 border-purple-200 bg-purple-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-purple-900 mb-3">Ako zdroj (preposielanie pozvanok)</h3>
        <p class="text-purple-800 mb-3"><strong>Priklad:</strong> E-mailovy kalendar -> Google Calendar</p>
        <ul class="text-purple-700 space-y-1 mb-0 text-sm">
            <li>Zdroj: E-mailovy kalendar s jedinecnou adresou</li>
            <li>Ciel: Vas pracovny kalendar Google</li>
            <li>Vysledok: Pozvanky do kalendara zaslane na vasu jedinecnu adresu sa zobrazia ako blokatory v Google</li>
        </ul>
    </div>
</div>

<h2>Ako funguje synchronizacia e-mailoveho kalendara</h2>

<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl mb-8">
    <h3 class="!mt-0 text-lg font-semibold text-gray-900 mb-4">Proces</h3>

    <div class="space-y-4">
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">1</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Udalost vytvorena v zdrojovom kalendari</p>
                <p class="text-gray-700 text-sm mb-0">Vo vasom zdrojovom kalendari (napr. Google Calendar) je vytvorena udalost</p>
            </div>
        </div>

        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">2</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">SyncMyDay detekuje zmenu</p>
                <p class="text-gray-700 text-sm mb-0">Obdrzime webhook notifikaciu (pre API kalendare) alebo kontrolujeme zmeny (CalDAV/E-mail)</p>
            </div>
        </div>

        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">3</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Odoslana e-mailova pozvanka</p>
                <p class="text-gray-700 text-sm mb-0">E-mail s prilohou <code>.ics</code> je odoslany na adresu vasho e-mailoveho kalendara</p>
            </div>
        </div>

        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">4</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Udalost sa zobrazi v e-mailovom klientovi</p>
                <p class="text-gray-700 text-sm mb-0">Vas e-mailovy klient (Outlook, Thunderbird atd.) obdrzi pozvanku a zobrazi ju vo vasom kalendari</p>
            </div>
        </div>
    </div>
</div>

<h2>Caste otazky</h2>

<div class="space-y-4" x-data="{ open: null }">
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q1' ? open = null : open = 'q1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Musim manualne prijimat e-mailove pozvanky?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q1'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Zalezi na nastaveni vasho e-mailoveho klienta. Vacsina e-mailovych klientov moze byt nakonfigurovana pre automaticke prijatie pozvanok do kalendara. Skontrolujte nastavenia kalendara pre "Automaticky prijimat ziadosti o schodzky" alebo podobne moznosti.</p>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q2' ? open = null : open = 'q2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Ako rychla je synchronizacia e-mailoveho kalendara?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q2'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Dorucenie e-mailu je obvykle rychle (pocas minut), ale zavisi od oneskoreni e-mailoveho servera. Ak potrebujete okamzitu synchronizaciu, zvazte pouzitie Google Calendar alebo Microsoft 365, ktore podporuju webhooky v realnom case.</p>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q3' ? open = null : open = 'q3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Mozem pouzit tu istu e-mailovu adresu pre viac e-mailovych kalendarov?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q3'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Ano! Mozete vytvorit viac e-mailovych kalendarov, ktore vsetky odosielaju na tu istu e-mailovu adresu. To je uzitocne, ak chcete prijimat blokatory z roznych zdrojovych kalendarov na jednom mieste.</p>
        </div>
    </div>

    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q4' ? open = null : open = 'q4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Co ked prestanem dostavat e-maily?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q4'" x-collapse class="px-6 pb-4">
            <p class="mb-2">Skontrolujte tieto mozne problemy:</p>
            <ul class="mb-0">
                <li>E-mail zachyteny v zlozke spam</li>
                <li>Pravidlo preposielania e-mailov zakazane alebo nefunkcne</li>
                <li>Pripojenie e-mailoveho kalendara neaktivne (skontrolujte stranku Pripojenia)</li>
                <li>Pravidlo synchronizacie pozastavene alebo zmazane</li>
            </ul>
        </div>
    </div>
</div>

<h2>Dalsie kroky</h2>

<div class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Vytvorte pravidlo synchronizacie</h3>
        </div>
        <p class="mb-0">Nastavte svoju prvu synchronizaciu pomocou e-mailoveho kalendara.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Pre vyvojarov a technickych pouzivatelov</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Format iCalendar (RFC 5545)</h4>
        <p>E-mailove pozvanky pouzivaju format iCalendar (<code>.ics</code>):</p>
        <ul>
            <li>Standardny MIME typ: <code>text/calendar</code></li>
            <li>Obsahuje komponenty <code>VEVENT</code> s datami udalosti</li>
            <li>Zahrnuje <code>VTIMEZONE</code> pre informacie o casovom pasme</li>
            <li>Pouziva <code>METHOD:REQUEST</code> pre pozvanky</li>
        </ul>

        <h4>Odosielanie e-mailov</h4>
        <p>Odchadzajuce e-mailove pozvanky:</p>
        <ul>
            <li>Odosielane cez system Laravel Mail (SMTP, Mailgun, SendGrid atd.)</li>
            <li>Adresa odosielatela: Naconfigurovane v <code>.env</code> (<code>MAIL_FROM_ADDRESS</code>)</li>
            <li>Odpoved na: <code>noreply@syncmyday.com</code></li>
            <li>Priloha: subor <code>event.ics</code></li>
        </ul>

        <h4>Prijimanie e-mailov (Inbound)</h4>
        <p>Pre jedinecne adresy SyncMyDay:</p>
        <ul>
            <li>IMAP polling: Kontroluje schranku kazdu minutu</li>
            <li>Podpora webhookov: Mailgun, SendGrid, Postmark</li>
            <li>Parsuje prilohy <code>.ics</code></li>
            <li>Extrahuje token z adresy prijemcu (napr. <code>abc123</code> z <code>abc123@syncmyday.com</code>)</li>
        </ul>

        <h4>Spracovanie udalosti</h4>
        <ol>
            <li>Parsovanie suboru <code>.ics</code> pre komponenty <code>VEVENT</code></li>
            <li>Extrahovanie <code>DTSTART</code>, <code>DTEND</code>, <code>SUMMARY</code>, <code>STATUS</code></li>
            <li>Konverzia do interneho formatu udalosti</li>
            <li>Kontrola pravidiel synchronizacie a vytvorenie blokovacich udalosti</li>
            <li>Oznacenie e-mailu ako spracovaneho (presun do zlozky "Spracovane" alebo zmazanie)</li>
        </ol>

        <h4>Bezpecnost</h4>
        <ul>
            <li>Jedinecne adresy su kryptograficky generovane tokeny</li>
            <li>Validacia tokenu zabranuje neopravnenemu pristupu</li>
            <li>Obsah e-mailu je pred spracovanim sanitizovany</li>
            <li>Spracovavane su iba prilohy <code>.ics</code></li>
        </ul>
    </div>
</div>
</div>
@endsection
