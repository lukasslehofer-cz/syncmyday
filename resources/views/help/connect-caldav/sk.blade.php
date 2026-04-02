@extends('layouts.public')

@section('title', 'Pripojenie CalDAV kalendara')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mr-4 shadow-lg">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Pripojenie CalDAV kalendara</h1>
        <p class="text-lg text-gray-600 !mb-0">Pre Fastmail, Nextcloud, SOGo a dalsich poskytovatelov CalDAV</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2 !mt-0">Co je CalDAV?</h3>
            <p class="text-blue-800 mb-2"><strong>CalDAV</strong> je otvoreny standardny protokol na pristup ku kalendarovym datam cez internet. Mnoho kalendarovych sluzieb podporuje CalDAV, co z neho robi flexibilnu moznost na pripojenie kalendarov.</p>
            <p class="text-blue-800 mb-0"><strong>Oblubeni poskytovatelia CalDAV zahrnuju:</strong> Fastmail, Nextcloud, SOGo, Radicale, Baikal, Synology Calendar a mnoho dalsich.</p>
        </div>
    </div>
</div>

<h2>Co budete potrebovat</h2>

<div class="grid md:grid-cols-3 gap-4 mb-8">
    <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-indigo-900 mb-2">1. URL servera</h3>
        <p class="text-indigo-800 text-sm mb-0">Adresa CalDAV servera od vasho poskytovatela (napr. <code>caldav.fastmail.com</code>)</p>
    </div>

    <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-purple-900 mb-2">2. Pouzivatelske meno</h3>
        <p class="text-purple-800 text-sm mb-0">Obvykle vasa e-mailova adresa alebo pouzivatelske meno uctu</p>
    </div>

    <div class="p-4 bg-gradient-to-br from-pink-50 to-red-50 border border-pink-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-pink-900 mb-2">3. Heslo</h3>
        <p class="text-pink-800 text-sm mb-0">Heslo vasho uctu alebo heslo pre aplikaciu</p>
    </div>
</div>

<h2>Oblubeni poskytovatelia CalDAV</h2>

<div class="space-y-4 mb-8">
    <!-- Fastmail -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-blue-500 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">F</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">Fastmail</h3>
                    <p class="text-sm text-gray-600 !mb-0">Popularna e-mailova a kalendarova sluzba</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL servera:</strong> <code>https://caldav.fastmail.com</code></li>
                <li><strong>Pouzivatelske meno:</strong> Vasa e-mailova adresa Fastmail</li>
                <li><strong>Heslo:</strong> Vase heslo Fastmail (alebo heslo pre aplikaciu, ak je povolene 2FA)</li>
            </ul>
            <p class="mt-4 text-sm text-gray-600 mb-0"><a href="https://www.fastmail.help/hc/en-us/articles/1500000278342" target="_blank">Dokumentacia Fastmail CalDAV</a></p>
        </div>
    </div>

    <!-- Nextcloud -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-blue-600 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">N</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">Nextcloud</h3>
                    <p class="text-sm text-gray-600 !mb-0">Self-hosted alebo spravovany Nextcloud</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL servera:</strong> <code>https://vas-nextcloud.com/remote.php/dav</code></li>
                <li><strong>Pouzivatelske meno:</strong> Vase pouzivatelske meno Nextcloud</li>
                <li><strong>Heslo:</strong> Vase heslo Nextcloud alebo heslo pre aplikaciu</li>
            </ul>
            <p class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800 mb-0">
                <strong>Tip:</strong> Pre lepsiu bezpecnost vygenerujte heslo pre aplikaciu v Nextcloud: Nastavenia -> Zabezpecenie -> Zariadenia a relacie -> Vytvorit nove heslo pre aplikaciu
            </p>
        </div>
    </div>

    <!-- SOGo -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-green-600 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">SOGo</h3>
                    <p class="text-sm text-gray-600 !mb-0">Open-source groupware server</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL servera:</strong> <code>https://vas-sogo-server.com/SOGo/dav</code></li>
                <li><strong>Pouzivatelske meno:</strong> Vase pouzivatelske meno SOGo (casto email@domena.sk)</li>
                <li><strong>Heslo:</strong> Vase heslo SOGo</li>
            </ul>
        </div>
    </div>

    <!-- Synology -->
    <div class="border-2 border-gray-200 rounded-xl p-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex items-center justify-between text-left">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-lg bg-orange-500 flex items-center justify-center mr-4">
                    <span class="text-white font-bold text-xl">S</span>
                </div>
                <div>
                    <h3 class="!mb-0 !mt-0 text-xl font-bold text-gray-900">Synology Calendar</h3>
                    <p class="text-sm text-gray-600 !mb-0">Balicek Kalendar pre Synology NAS</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL servera:</strong> <code>https://vase-nas-adresa.com:5001/calendar</code></li>
                <li><strong>Pouzivatelske meno:</strong> Vase pouzivatelske meno Synology DSM</li>
                <li><strong>Heslo:</strong> Vase heslo Synology DSM</li>
            </ul>
            <p class="mt-4 text-sm text-gray-600 mb-0">Uistite sa, ze je nainstalovany balicek Kalendar a CalDAV je povoleny v nastaveniach Kalendara.</p>
        </div>
    </div>
</div>

<h2>Sprievodca krok za krokom</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Zhromazdite svoje CalDAV udaje</h3>
            <p>Pred pripojenim musite najst informacie o vasom CalDAV serveri. Tie sa obvykle nachadzaju v:</p>
            <ul>
                <li>Dokumentacii pomoci vasho poskytovatela</li>
                <li>Stranke nastaveni uctu</li>
                <li>E-maile od vasho poskytovatela pri registracii</li>
            </ul>

            <p>Budete potrebovat:</p>
            <ol>
                <li><strong>URL CalDAV servera</strong> - napr. <code>caldav.example.com</code> alebo <code>https://example.com/dav</code></li>
                <li><strong>Pouzivatelske meno</strong> - Obvykle vasa e-mailova adresa</li>
                <li><strong>Heslo</strong> - Heslo vasho uctu alebo heslo pre aplikaciu</li>
            </ol>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <p class="text-blue-900 text-sm mb-0"><strong>Nemozete najst svoje CalDAV udaje?</strong> Kontaktujte podporu vasho poskytovatela kalendara alebo hladajte v ich dokumentacii "CalDAV" alebo "pristup ku kalendaru tretich stran".</p>
            </div>
        </div>
    </div>

    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Prejdite na Pripojenia kalendarov</h3>
            <p>V SyncMyDay prejdite na <strong>Kalendare</strong> v menu, alebo chodte na <a href="{{ route('connections.index') }}">stranku Pripojenia kalendarov</a>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-1.jpg') }}"
                         alt="Dashboard s menu Kalendare"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Prejdite na stranku Pripojenia kalendarov</p>
            </div>
        </div>
    </div>

    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Kliknite na "Pripojit CalDAV"</h3>
            <p>Najdite a kliknite na tlacidlo <strong>CalDAV (Generic)</strong>.</p>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-2.jpg') }}"
                         alt="Moznosti poskytovatelov kalendarov s tlacidlom CalDAV"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Kliknite na tlacidlo "Apple / CalDAV"</p>
            </div>
        </div>
    </div>

    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Zadajte svoje CalDAV prihlasovacie udaje</h3>
            <p>Vyplnte pripojovaci formular s udajmi, ktore ste zhromazdili:</p>

            <div class="space-y-4 mb-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">URL CalDAV servera</h4>
                    <p class="text-sm text-gray-700 mb-2">Zadajte uplnu adresu CalDAV servera. Priklady:</p>
                    <ul class="text-sm text-gray-600 space-y-1 mb-0">
                        <li><code>https://caldav.fastmail.com</code></li>
                        <li><code>https://nextcloud.example.com/remote.php/dav</code></li>
                        <li><code>caldav.example.com</code> (https:// pridame automaticky)</li>
                    </ul>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">Pouzivatelske meno</h4>
                    <p class="text-sm text-gray-600 mb-0">Obvykle vasa e-mailova adresa (napr. <code>vy@example.com</code>) alebo pouzivatelske meno</p>
                </div>

                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">Heslo</h4>
                    <p class="text-sm text-gray-600 mb-0">Heslo vasho uctu alebo heslo pre aplikaciu (ak to vas poskytovatel vyzaduje)</p>
                </div>
            </div>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Hesla pre aplikacie</p>
                        <p class="text-yellow-800 text-sm mb-0">Niektori poskytovatelia (ako Fastmail s 2FA) vyzaduju hesla pre aplikacie namiesto bezneho hesla. Skontrolujte dokumentaciu vasho poskytovatela.</p>
                    </div>
                </div>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-3.jpg') }}"
                         alt="Pripojovaci formular CalDAV"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadajte udaje vasho CalDAV servera</p>
            </div>
        </div>
    </div>

    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Otestujte pripojenie</h3>
            <p>Kliknite na <strong>"Pripojit"</strong> alebo <strong>"Testovat pripojenie"</strong>. SyncMyDay bude:</p>
            <ol>
                <li>Overovat, ze je URL servera dosiahnutelna</li>
                <li>Autentifikovat pomocou vasich prihlasovacich udajov</li>
                <li>Objavovat dostupne kalendare</li>
            </ol>

            <p>To obvykle trva 5-10 sekund.</p>
        </div>
    </div>

    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte kalendare</h3>
            <p>Po pripojeni uvidite zoznam vsetkych kalendarov dostupnych na vasom CalDAV serveri. Vyberte, ktore chcete synchronizovat.</p>

            <p>Typicke kalendare, ktore mozete vidiet:</p>
            <ul>
                <li><strong>Osobny</strong> - Vas hlavny kalendar</li>
                <li><strong>Praca</strong> - Pracovne udalosti</li>
                <li><strong>Rodina</strong> - Zdielany rodinny kalendar</li>
                <li>Akekolvek vlastne kalendare, ktore ste vytvorili</li>
            </ul>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-4.jpg') }}"
                         alt="Vyber kalendara s CalDAV kalendarmi"
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
            <p>Vas CalDAV kalendar je teraz pripojeny a pripraveny na pouzitie!</p>

            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">Co dalej?</h4>
                <ul class="text-green-800 space-y-1 mb-2">
                    <li>Vas CalDAV kalendar je pripraveny pre pravidla synchronizacie</li>
                    <li>Udalosti sa budu synchronizovat kazdych 15 minut</li>
                    <li>Teraz mozete vytvarat pravidla synchronizacie!</li>
                </ul>
                <p class="text-green-800 text-sm mb-0"><strong>Poznamka:</strong> CalDAV nepodporuje webhooky v realnom case, takze kontrolujeme zmeny kazdych 15 minut.</p>
            </div>

            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-5.jpg') }}"
                         alt="Uspesne pripojeny CalDAV kalendar"
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vas CalDAV kalendar je uspesne pripojeny a aktivny</p>
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
            "Pripojenie sa nepodarilo" alebo "Nie je mozne sa pripojit"
        </h3>
        <p><strong>Skontrolujte tieto caste problemy:</strong></p>
        <ol>
            <li><strong>Format URL servera:</strong> Uistite sa, ze obsahuje <code>https://</code> alebo nam to dovolte pridat automaticky</li>
            <li><strong>Lomitka na konci:</strong> Skuste s lomitkom (<code>/</code>) na konci aj bez neho</li>
            <li><strong>Cislo portu:</strong> Niektore servery potrebuju explicitny port (napr. <code>:8443</code>)</li>
            <li><strong>Self-signed certifikaty:</strong> Ak pouzivate self-hosted, uistite sa, ze vas SSL certifikat je platny</li>
            <li><strong>Firewall:</strong> Uistite sa, ze vas CalDAV server je pristupny z internetu</li>
        </ol>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            "Autentifikacia sa nepodarila" alebo "Neplatne prihlasovacie udaje"
        </h3>
        <p><strong>Caste priciny:</strong></p>
        <ul>
            <li>Nespravne pouzivatelske meno alebo heslo</li>
            <li>Potreba pouzit heslo pre aplikaciu (ak je povolene 2FA)</li>
            <li>Zly format pouzivatelskeho mena (skuste s @domena.sk aj bez)</li>
            <li>Ucet uzamknuty alebo deaktivovany</li>
        </ul>
        <p><strong>Riesenie:</strong> Skontrolujte prihlasovacie udaje, vygenerujte heslo pre aplikaciu, ak je potrebne, alebo kontaktujte svojho poskytovatela.</p>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Neboli najdene ziadne kalendare
        </h3>
        <p>Ak pripojenie uspeje, ale neobjavia sa ziadne kalendare:</p>
        <ul>
            <li>Uistite sa, ze mate aspon jeden kalendar vo vasom ucte</li>
            <li>Skontrolujte, ze kalendare nie su skryte alebo archivovane</li>
            <li>Skuste vytvorit testovaci kalendar vo webovom rozhrani vasho poskytovatela</li>
            <li>Niektore CalDAV servery vyzaduju specificke principal URL (kontaktujte podporu)</li>
        </ul>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Synchronizacia je pomala
        </h3>
        <p>CalDAV kalendare sa synchronizuju kazdych 15 minut, co je pomalsie ako Google/Microsoft:</p>
        <ul>
            <li>To je normalne kvoli obmedzeniam protokolu CalDAV</li>
            <li>Push notifikacie v realnom case nie su dostupne</li>
            <li>Frekvencia pollingu vyvazuje odozvu so zatazenim servera</li>
        </ul>
        <p class="mb-0"><strong>Potrebujete rychlejsiu synchronizaciu?</strong> Zvazte pouzitie Google Calendar alebo Microsoft 365, ktore podporuju webhooky v realnom case.</p>
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
        <p class="mb-0">Pripojte druhy kalendar pre zahajenie synchronizacie udalosti.</p>
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
        <p class="mb-0">Nastavte synchronizaciu medzi vasimi kalendarmi.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Pre vyvojarov a systemovych administratorov</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Protokol CalDAV (RFC 4791)</h4>
        <p>SyncMyDay implementuje CalDAV standard pomocou:</p>
        <ul>
            <li><strong>PROPFIND:</strong> Objavovanie kalendarov a kalendarovych kolekcii</li>
            <li><strong>REPORT:</strong> Dotazovanie kalendarovych dat (calendar-query)</li>
            <li><strong>GET:</strong> Nacitanie jednotlivych kalendarovych objektov (iCalendar format)</li>
            <li><strong>PUT:</strong> Vytváranie a aktualizacia udalosti</li>
            <li><strong>DELETE:</strong> Odstranovanie udalosti</li>
        </ul>

        <h4>Objavovanie sluzby</h4>
        <p>Pouzivame WebDAV service discovery na najdenie kalendarovych kolekcii:</p>
        <ol>
            <li>Vykoname PROPFIND na poskytnutej URL</li>
            <li>Hladame vlastnost <code>calendar-home-set</code></li>
            <li>Dotazujeme home set na kalendarove kolekcie</li>
            <li>Prezentujeme dostupne kalendare pouzivatelovi</li>
        </ol>

        <h4>Autentifikacia</h4>
        <ul>
            <li><strong>Basic Auth:</strong> Standardna HTTP Basic Authentication cez HTTPS</li>
            <li><strong>Digest Auth:</strong> Podporovane, ak to server vyzaduje</li>
            <li>Prihlasovacie udaje su sifrovane v pokoji pomocou AES-256</li>
        </ul>

        <h4>Strategia pollingu</h4>
        <p>Pretoze CalDAV nepodporuje push notifikacie:</p>
        <ul>
            <li>Pollujeme kazdych 15 minut pre zmeny</li>
            <li>Pouzivame <code>getctag</code> (collection tag) pre efektivnu detekciu zmien</li>
            <li>Nacitavame iba zmenene udalosti pomocou <code>getetag</code></li>
            <li>Minimalizujeme sirku pasma a zatazenie servera</li>
        </ul>

        <h4>Format iCalendar</h4>
        <p>Udalosti su vymienane vo formate RFC 5545 iCalendar:</p>
        <ul>
            <li>Parsujeme komponenty <code>VEVENT</code></li>
            <li>Extrahujeme <code>DTSTART</code>, <code>DTEND</code>, <code>STATUS</code></li>
            <li>Spracovavame pravidla opakovania (<code>RRULE</code>)</li>
            <li>Podporujeme konverziu casovych pasiem (<code>VTIMEZONE</code>)</li>
        </ul>

        <h4>Zname obmedzenia</h4>
        <ul>
            <li><strong>Ziadna synchronizacia v realnom case:</strong> 15-minutovy interval pollingu</li>
            <li><strong>Zavislosti servera:</strong> Vyzaduje spravnu implementaciu CalDAV</li>
            <li><strong>Obmedzenia firewallu:</strong> Server musi byt pristupny z internetu</li>
        </ul>
    </div>
</div>
</div>
@endsection
