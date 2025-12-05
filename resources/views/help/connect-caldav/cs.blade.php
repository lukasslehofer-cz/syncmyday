@extends('layouts.public')

@section('title', 'Připojení CalDAV kalendáře')

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
        <h1 class="!mb-0">Připojení CalDAV kalendáře</h1>
        <p class="text-lg text-gray-600 !mb-0">Pro Fastmail, Nextcloud, SOGo a další poskytovatele CalDAV</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2 !mt-0">Co je CalDAV?</h3>
            <p class="text-blue-800 mb-2"><strong>CalDAV</strong> je otevřený standardní protokol pro přístup ke kalendářovým datům přes internet. Mnoho kalendářových služeb podporuje CalDAV, což z něj činí flexibilní možnost pro připojení kalendářů.</p>
            <p class="text-blue-800 mb-0"><strong>Oblíbení poskytovatelé CalDAV zahrnují:</strong> Fastmail, Nextcloud, SOGo, Radicale, Baikal, Synology Calendar a mnoho dalších.</p>
        </div>
    </div>
</div>

<h2>Co budete potřebovat</h2>

<div class="grid md:grid-cols-3 gap-4 mb-8">
    <div class="p-4 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-indigo-900 mb-2">1. URL serveru</h3>
        <p class="text-indigo-800 text-sm mb-0">Adresa CalDAV serveru od vašeho poskytovatele (např. <code>caldav.fastmail.com</code>)</p>
    </div>
    
    <div class="p-4 bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-purple-900 mb-2">2. Uživatelské jméno</h3>
        <p class="text-purple-800 text-sm mb-0">Obvykle vaše e-mailová adresa nebo uživatelské jméno účtu</p>
    </div>
    
    <div class="p-4 bg-gradient-to-br from-pink-50 to-red-50 border border-pink-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-semibold text-pink-900 mb-2">3. Heslo</h3>
        <p class="text-pink-800 text-sm mb-0">Heslo vašeho účtu nebo heslo pro aplikaci</p>
    </div>
</div>

<h2>Oblíbení poskytovatelé CalDAV</h2>

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
                    <p class="text-sm text-gray-600 !mb-0">Populární e-mailová a kalendářová služba</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL serveru:</strong> <code>https://caldav.fastmail.com</code></li>
                <li><strong>Uživatelské jméno:</strong> Vaše e-mailová adresa Fastmail</li>
                <li><strong>Heslo:</strong> Vaše heslo Fastmail (nebo heslo pro aplikaci, pokud je povoleno 2FA)</li>
            </ul>
            <p class="mt-4 text-sm text-gray-600 mb-0">📚 <a href="https://www.fastmail.help/hc/en-us/articles/1500000278342" target="_blank">Dokumentace Fastmail CalDAV</a></p>
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
                    <p class="text-sm text-gray-600 !mb-0">Self-hosted nebo spravovaný Nextcloud</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL serveru:</strong> <code>https://vas-nextcloud.com/remote.php/dav</code></li>
                <li><strong>Uživatelské jméno:</strong> Vaše uživatelské jméno Nextcloud</li>
                <li><strong>Heslo:</strong> Vaše heslo Nextcloud nebo heslo pro aplikaci</li>
            </ul>
            <p class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800 mb-0">
                <strong>Tip:</strong> Pro lepší bezpečnost vygenerujte heslo pro aplikaci v Nextcloud: Nastavení → Zabezpečení → Zařízení a relace → Vytvořit nové heslo pro aplikaci
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
                <li><strong>URL serveru:</strong> <code>https://vas-sogo-server.com/SOGo/dav</code></li>
                <li><strong>Uživatelské jméno:</strong> Vaše uživatelské jméno SOGo (často email@domena.cz)</li>
                <li><strong>Heslo:</strong> Vaše heslo SOGo</li>
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
                    <p class="text-sm text-gray-600 !mb-0">Balíček Kalendář pro Synology NAS</p>
                </div>
            </div>
            <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        
        <div x-show="open" x-collapse class="mt-4 pt-4 border-t border-gray-200">
            <ul class="space-y-2 mb-0">
                <li><strong>URL serveru:</strong> <code>https://vase-nas-adresa.com:5001/calendar</code></li>
                <li><strong>Uživatelské jméno:</strong> Vaše uživatelské jméno Synology DSM</li>
                <li><strong>Heslo:</strong> Vaše heslo Synology DSM</li>
            </ul>
            <p class="mt-4 text-sm text-gray-600 mb-0">Ujistěte se, že je nainstalován balíček Kalendář a CalDAV je povolen v nastavení Kalendáře.</p>
        </div>
    </div>
</div>

<h2>Průvodce krok za krokem</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Shromážděte své CalDAV údaje</h3>
            <p>Před připojením musíte najít informace o vašem CalDAV serveru. Ty se obvykle nachází v:</p>
            <ul>
                <li>Dokumentaci pomoci vašeho poskytovatele</li>
                <li>Stránce nastavení účtu</li>
                <li>E-mailu od vašeho poskytovatele při registraci</li>
            </ul>
            
            <p>Budete potřebovat:</p>
            <ol>
                <li><strong>URL CalDAV serveru</strong> - např. <code>caldav.example.com</code> nebo <code>https://example.com/dav</code></li>
                <li><strong>Uživatelské jméno</strong> - Obvykle vaše e-mailová adresa</li>
                <li><strong>Heslo</strong> - Heslo vašeho účtu nebo heslo pro aplikaci</li>
            </ol>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <p class="text-blue-900 text-sm mb-0"><strong>Nemůžete najít své CalDAV údaje?</strong> Kontaktujte podporu vašeho poskytovatele kalendáře nebo hledejte v jejich dokumentaci "CalDAV" nebo "přístup k kalendáři třetích stran".</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Přejděte na Připojení kalendářů</h3>
            <p>V SyncMyDay přejděte na <strong>Kalendáře</strong> v menu, nebo jděte na <a href="{{ route('connections.index') }}">stránku Připojení kalendářů</a>.</p>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-1.jpg') }}" 
                         alt="Dashboard s menu Kalendáře" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Přejděte na stránku Připojení kalendářů</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Klikněte na "Připojit CalDAV"</h3>
            <p>Najděte a klikněte na tlačítko <strong>CalDAV (Generic)</strong>.</p>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-2.jpg') }}" 
                         alt="Možnosti poskytovatelů kalendářů s tlačítkem CalDAV" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Klikněte na tlačítko "Apple / CalDAV"</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Zadejte své CalDAV přihlašovací údaje</h3>
            <p>Vyplňte připojovací formulář s údaji, které jste shromáždili:</p>
            
            <div class="space-y-4 mb-4">
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">URL CalDAV serveru</h4>
                    <p class="text-sm text-gray-700 mb-2">Zadejte úplnou adresu CalDAV serveru. Příklady:</p>
                    <ul class="text-sm text-gray-600 space-y-1 mb-0">
                        <li><code>https://caldav.fastmail.com</code></li>
                        <li><code>https://nextcloud.example.com/remote.php/dav</code></li>
                        <li><code>caldav.example.com</code> (https:// přidáme automaticky)</li>
                    </ul>
                </div>
                
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">Uživatelské jméno</h4>
                    <p class="text-sm text-gray-600 mb-0">Obvykle vaše e-mailová adresa (např. <code>vy@example.com</code>) nebo uživatelské jméno</p>
                </div>
                
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg">
                    <h4 class="!mt-0 text-sm font-semibold text-gray-900 mb-2">Heslo</h4>
                    <p class="text-sm text-gray-600 mb-0">Heslo vašeho účtu nebo heslo pro aplikaci (pokud to váš poskytovatel vyžaduje)</p>
                </div>
            </div>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Hesla pro aplikace</p>
                        <p class="text-yellow-800 text-sm mb-0">Někteří poskytovatelé (jako Fastmail s 2FA) vyžadují hesla pro aplikace místo běžného hesla. Zkontrolujte dokumentaci vašeho poskytovatele.</p>
                    </div>
                </div>
            </div>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-3.jpg') }}" 
                         alt="Připojovací formulář CalDAV" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadejte údaje vašeho CalDAV serveru</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Otestujte připojení</h3>
            <p>Klikněte na <strong>"Připojit"</strong> nebo <strong>"Testovat připojení"</strong>. SyncMyDay bude:</p>
            <ol>
                <li>Ověřovat, že je URL serveru dosažitelná</li>
                <li>Autentizovat pomocí vašich přihlašovacích údajů</li>
                <li>Objevovat dostupné kalendáře</li>
            </ol>
            
            <p>To obvykle trvá 5-10 sekund.</p>
        </div>
    </div>
    
    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte kalendáře</h3>
            <p>Po připojení uvidíte seznam všech kalendářů dostupných na vašem CalDAV serveru. Vyberte, které chcete synchronizovat.</p>
            
            <p>Typické kalendáře, které můžete vidět:</p>
            <ul>
                <li><strong>Osobní</strong> - Váš hlavní kalendář</li>
                <li><strong>Práce</strong> - Pracovní události</li>
                <li><strong>Rodina</strong> - Sdílený rodinný kalendář</li>
                <li>Jakékoliv vlastní kalendáře, které jste vytvořili</li>
            </ul>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-4.jpg') }}" 
                         alt="Výběr kalendáře s CalDAV kalendáři" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte kalendáře, které chcete synchronizovat</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Připojení dokončeno!</h3>
            <p>Váš CalDAV kalendář je nyní připojen a připraven k použití!</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ Co dál?</h4>
                <ul class="text-green-800 space-y-1 mb-2">
                    <li>Váš CalDAV kalendář je připraven pro pravidla synchronizace</li>
                    <li>Události se budou synchronizovat každých 15 minut</li>
                    <li>Nyní můžete vytvářet pravidla synchronizace!</li>
                </ul>
                <p class="text-green-800 text-sm mb-0"><strong>Poznámka:</strong> CalDAV nepodporuje webhooky v reálném čase, takže kontrolujeme změny každých 15 minut.</p>
            </div>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/caldav-5.jpg') }}" 
                         alt="Úspěšně připojený CalDAV kalendář" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Váš CalDAV kalendář je úspěšně připojen a aktivní</p>
            </div>
        </div>
    </div>
</div>

<h2>Řešení problémů</h2>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            "Připojení se nezdařilo" nebo "Nelze se připojit"
        </h3>
        <p><strong>Zkontrolujte tyto časté problémy:</strong></p>
        <ol>
            <li><strong>Formát URL serveru:</strong> Ujistěte se, že obsahuje <code>https://</code> nebo nám to dovolte přidat automaticky</li>
            <li><strong>Lomítka na konci:</strong> Zkuste s lomítkem (<code>/</code>) na konci i bez něj</li>
            <li><strong>Číslo portu:</strong> Některé servery potřebují explicitní port (např. <code>:8443</code>)</li>
            <li><strong>Self-signed certifikáty:</strong> Pokud používáte self-hosted, ujistěte se, že váš SSL certifikát je platný</li>
            <li><strong>Firewall:</strong> Ujistěte se, že váš CalDAV server je přístupný z internetu</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            "Autentizace se nezdařila" nebo "Neplatné přihlašovací údaje"
        </h3>
        <p><strong>Časté příčiny:</strong></p>
        <ul>
            <li>Nesprávné uživatelské jméno nebo heslo</li>
            <li>Potřeba použít heslo pro aplikaci (pokud je povoleno 2FA)</li>
            <li>Špatný formát uživatelského jména (zkuste s @domena.cz i bez)</li>
            <li>Účet uzamčen nebo deaktivován</li>
        </ul>
        <p><strong>Řešení:</strong> Zkontrolujte přihlašovací údaje, vygenerujte heslo pro aplikaci, pokud je potřeba, nebo kontaktujte svého poskytovatele.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Nebyly nalezeny žádné kalendáře
        </h3>
        <p>Pokud připojení uspěje, ale neobjeví se žádné kalendáře:</p>
        <ul>
            <li>Ujistěte se, že máte alespoň jeden kalendář ve vašem účtu</li>
            <li>Zkontrolujte, že kalendáře nejsou skryté nebo archivované</li>
            <li>Zkuste vytvořit testovací kalendář ve webovém rozhraní vašeho poskytovatele</li>
            <li>Některé CalDAV servery vyžadují specifické principal URL (kontaktujte podporu)</li>
        </ul>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Synchronizace je pomalá
        </h3>
        <p>CalDAV kalendáře se synchronizují každých 15 minut, což je pomalejší než Google/Microsoft:</p>
        <ul>
            <li>To je normální kvůli omezením protokolu CalDAV</li>
            <li>Push notifikace v reálném čase nejsou dostupné</li>
            <li>Frekvence pollingu vyvažuje odezvu se zatížením serveru</li>
        </ul>
        <p class="mb-0"><strong>Potřebujete rychlejší synchronizaci?</strong> Zvažte použití Google Calendar nebo Microsoft 365, které podporují webhooky v reálném čase.</p>
    </div>
</div>

<h2>Další kroky</h2>

<div class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('connections.index') }}" class="block p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Připojte další kalendář</h3>
        </div>
        <p class="mb-0">Připojte druhý kalendář pro zahájení synchronizace událostí.</p>
    </a>
    
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Vytvořte pravidlo synchronizace</h3>
        </div>
        <p class="mb-0">Nastavte synchronizaci mezi vašimi kalendáři.</p>
    </a>
</div>

<!-- Technické detaily -->
<div class="mt-12" x-data="{ open: false }">
    <button @click="open = !open" class="w-full p-6 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-xl text-left transition flex items-center justify-between">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
            </svg>
            <div>
                <h3 class="!mb-0 !mt-0 text-lg font-semibold text-gray-900">Technické detaily</h3>
                <p class="text-sm text-gray-600 !mb-0">Pro vývojáře a systémové administrátory</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Protokol CalDAV (RFC 4791)</h4>
        <p>SyncMyDay implementuje CalDAV standard pomocí:</p>
        <ul>
            <li><strong>PROPFIND:</strong> Objevování kalendářů a kalendářových kolekcí</li>
            <li><strong>REPORT:</strong> Dotazování kalendářových dat (calendar-query)</li>
            <li><strong>GET:</strong> Načítání jednotlivých kalendářových objektů (iCalendar formát)</li>
            <li><strong>PUT:</strong> Vytváření a aktualizace událostí</li>
            <li><strong>DELETE:</strong> Odstraňování událostí</li>
        </ul>
        
        <h4>Objevování služby</h4>
        <p>Používáme WebDAV service discovery pro nalezení kalendářových kolekcí:</p>
        <ol>
            <li>Provedeme PROPFIND na poskytnuté URL</li>
            <li>Hledáme vlastnost <code>calendar-home-set</code></li>
            <li>Dotazujeme home set na kalendářové kolekce</li>
            <li>Prezentujeme dostupné kalendáře uživateli</li>
        </ol>
        
        <h4>Autentizace</h4>
        <ul>
            <li><strong>Basic Auth:</strong> Standardní HTTP Basic Authentication přes HTTPS</li>
            <li><strong>Digest Auth:</strong> Podporováno, pokud to server vyžaduje</li>
            <li>Přihlašovací údaje jsou šifrovány v klidu pomocí AES-256</li>
        </ul>
        
        <h4>Strategie pollingu</h4>
        <p>Protože CalDAV nepodporuje push notifikace:</p>
        <ul>
            <li>Pollujeme každých 15 minut pro změny</li>
            <li>Používáme <code>getctag</code> (collection tag) pro efektivní detekci změn</li>
            <li>Načítáme pouze změněné události pomocí <code>getetag</code></li>
            <li>Minimalizujeme šířku pásma a zatížení serveru</li>
        </ul>
        
        <h4>Formát iCalendar</h4>
        <p>Události jsou vyměňovány ve formátu RFC 5545 iCalendar:</p>
        <ul>
            <li>Parsujeme komponenty <code>VEVENT</code></li>
            <li>Extrahujeme <code>DTSTART</code>, <code>DTEND</code>, <code>STATUS</code></li>
            <li>Zpracováváme pravidla opakování (<code>RRULE</code>)</li>
            <li>Podporujeme konverzi časových pásem (<code>VTIMEZONE</code>)</li>
        </ul>
        
        <h4>Známá omezení</h4>
        <ul>
            <li><strong>Žádná synchronizace v reálném čase:</strong> 15minutový interval pollingu</li>
            <li><strong>Závislosti serveru:</strong> Vyžaduje správnou implementaci CalDAV</li>
            <li><strong>Omezení firewallu:</strong> Server musí být přístupný z internetu</li>
        </ul>
    </div>
</div>
</div>
@endsection

