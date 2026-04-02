@extends('layouts.public')

@section('title', 'Casto kladene otazky')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<h1>Casto kladene otazky</h1>

<p class="text-xl text-gray-600 mb-8">Rychle odpovede na bezne otazky o SyncMyDay.</p>

<div class="space-y-6" x-data="{ open: null }">
    <!-- Bezpecnost & Sukromie -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">Bezpecnost & Sukromie</h2>

        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-1' ? open = null : open = 'security-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Su moje kalendarove data v bezpeci?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-1'" x-collapse class="px-6 pb-4">
                    <p><strong>Ano, rozhodne.</strong> Bezpecnost berieme vazne:</p>
                    <ul>
                        <li><strong>Minimalne ukladanie dat:</strong> Ukladame iba zaciatok/koniec udalosti a stav (zaneprazdneny/volny). Nikdy neukladame nazvy udalosti, popisy alebo ucastnikov.</li>
                        <li><strong>Sifrovanie v databaze:</strong> Vsetky data su v databaze sifrovane.</li>
                        <li><strong>Sifrovanie pri prenose:</strong> Vsetky pripojenia pouzivaju HTTPS/TLS.</li>
                        <li><strong>OAuth autentifikacia:</strong> Pre Google a Microsoft pouzivame standardny OAuth, co znamena, ze nikdy nevidime vase heslo.</li>
                        <li><strong>Pristupove tokeny su sifrovane:</strong> Akekolvek prihlasovacie udaje su sifrovane silnym sifrovanim.</li>
                    </ul>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-2' ? open = null : open = 'security-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Ake informacie vlastne ukladate?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-2'" x-collapse class="px-6 pb-4">
                    <p>Pre kazdu synchronizovanu udalost ukladame iba:</p>
                    <ul>
                        <li>Datum a cas zaciatku</li>
                        <li>Datum a cas konca</li>
                        <li>Stav (zaneprazdneny/volny/predbezne)</li>
                        <li>Z ktoreho kalendara pochadza a v ktorych kalendaroch sme vytvorili blokovanie</li>
                        <li>Jedinecne ID pre sledovanie udalosti</li>
                    </ul>
                    <p><strong>Nikdy neukladame:</strong> Nazvy udalosti, popisy, miesta, ucastnikov, poznamky ani ziadne dalsie detaily o vasich udalostiach.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-3' ? open = null : open = 'security-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Mozete vidiet moje kalendarove udalosti?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-3'" x-collapse class="px-6 pb-4">
                    <p><strong>Nie.</strong> Zamerne nikdy neobdrzime ani neukladame nazvy alebo detaily vasich udalosti. Pri synchronizacii citame iba casove informacie (kedy udalost zacina a konci) a vytvarame jednoduche "Zaneprazdneny" blokujuce udalosti vo vasich dalsich kalendaroch.</p>
                    <p>Vase osobne kalendarove udalosti zostavaju sukromne vo vasej kalendarovej sluzbe (Google, Microsoft atd.).</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-4' ? open = null : open = 'security-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Ako odvolam pristup?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-4'" x-collapse class="px-6 pb-4">
                    <p>Mozete kedykolvek odpojit akykolvek kalendar zo stranky <strong>Pripojenia kalendarov</strong>. Tymto:</p>
                    <ul>
                        <li>Odstranite vsetky blokujuce udalosti vytvorene SyncMyDay v danom kalendari</li>
                        <li>Zmazete vsetky pravidla synchronizacie pouzivajuce tento kalendar</li>
                        <li>Odvolate nas pristup k tomuto kalendaru</li>
                    </ul>
                    <p>Pristup mozete tiez odvolat priamo u vasho poskytovatela kalendara (Google, Microsoft atd.) v ich nastaveniach bezpecnosti.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Skusobne obdobie & Platby -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">Skusobne obdobie & Platby</h2>

        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-1' ? open = null : open = 'payment-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Ako funguje skusobne obdobie zadarmo?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-1'" x-collapse class="px-6 pb-4">
                    <p>Dostanete <strong>14 dni plneho pristupu zadarmo</strong> bez nutnosti zadavat platobnu kartu. Mozete pripojit neobmedzeny pocet kalendarov a vytvorit lubovolny pocet pravidiel synchronizacie.</p>
                    <p>Po skonceni skusobneho obdobia:</p>
                    <ul>
                        <li><strong>Pridate platobnu metodu:</strong> SyncMyDay bude pokracovat v plnom rozsahu.</li>
                        <li><strong>Nepridate platobnu metodu:</strong> Ucet sa zamkne a uz sa nevykonaju ziadne synchronizacie.</li>
                    </ul>
                    <p>Ziadne automaticke poplatky. Ziadne prekvapenia.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-2' ? open = null : open = 'payment-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Budem automaticky uctovany po skusobnom obdobi?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-2'" x-collapse class="px-6 pb-4">
                    <p><strong>Nie!</strong> Nevyzadujeme platobnu kartu pre skusobne obdobie, takze vas nemozeme uctovat. Mate 14 dni na vyskusanie vsetkych funkcii. A ak si pocas tohto obdobia pripojite vasu platobnu kartu, stale dokoncime celych 14 dni zadarmo a prva platba prebehne az potom.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-3' ? open = null : open = 'payment-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Mozem sluzbu kedykolvek zrusit?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-3'" x-collapse class="px-6 pb-4">
                    <p><strong>Ano.</strong> Mozete kedykolvek zrusit svoj plan zo stranky Platby. Vase predplatne zostane aktivne do konca fakturacneho obdobia, potom bude vas ucet pozastaveny a uz sa nevykonaju ziadne synchronizacie.</p>
                    <p>Ziadne dalsie poplatky. Ziadne otazky.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-4' ? open = null : open = 'payment-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Ake platobne metody prijimate?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-4'" x-collapse class="px-6 pb-4">
                    <p>Prijimame vsetky hlavne platobne karty (Visa, Mastercard, American Express) prostrednictvom Stripe, co je predny platobny procesor.</p>
                    <p>Vase platobne udaje su spracovavane bezpecne cez sluzbu Stripe a nikdy sa nedostanu na nase servery.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Ako to funguje -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">Ako to funguje</h2>

        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-1' ? open = null : open = 'how-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Co je to "blocker"?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-1'" x-collapse class="px-6 pb-4">
                    <p>Blocker je jednoducha udalost, ktoru vytvorime vo vasich cielovych kalendaroch, aby ukazala, ze ste zaneprazdneni. Obvykle sa zobrazuje ako:</p>
                    <ul>
                        <li><strong>Nazov:</strong> "Zaneprazdneny" (alebo vlastny text, ktory si nastavite)</li>
                        <li><strong>Cas:</strong> Presne ten isty cas ako povodna udalost</li>
                        <li><strong>Stav:</strong> Oznacene ako "Zaneprazdneny" aby vas nikto nerusil</li>
                    </ul>
                    <p>Je to ako rezervacia miesta vo vasom kalendari bez odhalenia akychkolvek detailov o skutocnej udalosti.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-2' ? open = null : open = 'how-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Ako rychlo sa udalosti synchronizuju?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-2'" x-collapse class="px-6 pb-4">
                    <p>Rychlost synchronizacie zavisi od typu kalendara:</p>
                    <ul>
                        <li><strong>Google Calendar:</strong> Okamzite (cca do 1 minuty)</li>
                        <li><strong>Microsoft 365:</strong> Okamzite (cca do 1 minuty)</li>
                        <li><strong>Apple iCloud:</strong> Kazdych ~5 minut</li>
                        <li><strong>CalDAV:</strong> Kazdych ~5 minut</li>
                        <li><strong>E-mailova synchronizacia:</strong> Okamzite pri obdrzani e-mailu</li>
                    </ul>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-3' ? open = null : open = 'how-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Co sa stane, ked zmazem udalost?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-3'" x-collapse class="px-6 pb-4">
                    <p>Ked zmazete udalost zo zdrojoveho kalendara, automaticky zmazeme zodpovedajucu blokujucu udalost zo vsetkych cielovych kalendarov.</p>
                    <p>Ak zmazete blokujucu udalost priamo, vytvorime ju znova pri dalsej synchronizacii (pretoze povodna udalost stale existuje). Ak ju chcete trvalo odstranit, bud:</p>
                    <ul>
                        <li>Zmazte povodnu udalost, alebo</li>
                        <li>Upravte svoje pravidlo synchronizacie tak, aby vylucovalo tento typ udalosti</li>
                    </ul>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-4' ? open = null : open = 'how-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Mozem synchronizovat viac nez 2 kalendare?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-4'" x-collapse class="px-6 pb-4">
                    <p><strong>Ano!</strong> Mozete pripojit neobmedzeny pocet kalendarov.</p>
                    <p>Tiez mozete vytvorit viac pravidiel synchronizacie s roznymi smermi:</p>
                    <ul>
                        <li>Osobny -> Pracovny</li>
                        <li>Pracovny -> Osobny</li>
                        <li>Osobny -> Rodinny</li>
                        <li>Atd...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Riesenie problemov -->
    <div class="pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">Riesenie problemov</h2>

        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-1' ? open = null : open = 'trouble-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Moje udalosti sa nesynchronizuju. Co mam robit?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-1'" x-collapse class="px-6 pb-4">
                    <p>Skuste tieto kroky riesenia problemov:</p>
                    <ol>
                        <li><strong>Skontrolujte pravidlo synchronizacie:</strong> Uistite sa, ze mate aktivne pravidlo synchronizacie medzi spravnymi kalendarmi</li>
                        <li><strong>Skontrolujte filtre:</strong> Udalost moze byt vyfiltrovana (napr. celodenna udalost s povolenym "ignorovat celodenne")</li>
                        <li><strong>Skontrolujte stav udalosti:</strong> Ak mate filter "iba zaneprazdneny", predbezne udalosti sa nebudu synchronizovat</li>
                        <li><strong>Pockajte par minut:</strong> CalDAV kalendare sa aktualizuju kazdych 15 minut</li>
                        <li><strong>Obnovte pripojenie:</strong> Pouzite tlacidlo "Obnovit" na stranke Pripojenia</li>
                    </ol>
                    <p>Ak problemy pretrvavaju, <a href="{{ route('contact') }}">kontaktujte nas tim podpory</a></p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-2' ? open = null : open = 'trouble-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Vidim chybovu spravu "Vyprsala platnost tokenu"</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-2'" x-collapse class="px-6 pb-4">
                    <p>To znamena, ze vas pristupovy token kalendara vyprsal alebo bol odvolany. Obvykle sa to stava, ked:</p>
                    <ul>
                        <li>Zmenili ste heslo vo vasom kalendari</li>
                        <li>Odvolali ste pristup v nastaveniach bezpecnosti vasho kalendara</li>
                        <li>U Apple: Heslo pre aplikaciu prestalo fungovat</li>
                    </ul>
                    <p><strong>Riesenie:</strong> Prejdite na stranku Pripojenia kalendarov a znovu pripojte dotceny kalendar. Vsetky vase pravidla synchronizacie zostanu zachovane.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-3' ? open = null : open = 'trouble-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Vidim duplicitne udalosti</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-3'" x-collapse class="px-6 pb-4">
                    <p>Ak vidite duplicitne blokujuce udalosti, obvykle je to sposobene:</p>
                    <ul>
                        <li><strong>Viac pravidiel synchronizacie:</strong> Mate 2+ pravidla, ktore vytvaraju blokovanie v tom istom kalendari</li>
                        <li><strong>Cirkularna synchronizacia:</strong> Kalendar A -> B a B -> A zaroven</li>
                    </ul>
                    <p><strong>Riesenie:</strong> Skontrolujte svoje pravidla synchronizacie a uistite sa, ze nemate konfliktne alebo cirkularne pravidla. Kazdy kalendar by mal byt bud zdrojom alebo cielom, nie obojim v tom istom pare.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-12 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Mate dalsie otazky?</h3>
    <p class="text-gray-700 mb-4">Sme tu, aby sme vam pomohli! Nas tim podpory obvykle odpoveda do 24 hodin.</p>
    <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition no-underline" style="text-decoration: none !important;">
        <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <span class="text-white">Kontaktujte podporu</span>
    </a>
</div>
</div>
@endsection
