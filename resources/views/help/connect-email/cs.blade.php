@extends('layouts.public')

@section('title', 'Připojení e-mailového kalendáře')

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
        <h1 class="!mb-0">Připojení e-mailového kalendáře</h1>
        <p class="text-lg text-gray-600 !mb-0">Přijímejte pozvánky do kalendáře e-mailem</p>
    </div>
</div>

<div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Co je e-mailový kalendář?</h3>
            <p class="text-blue-800 mb-2"><strong>E-mailový kalendář</strong> je unikátní způsob synchronizace kalendářů prostřednictvím přeposílání pozvánek do kalendáře (soubory .ics) e-mailem. To je ideální pro kalendáře, které nemají API přístup, nebo když chcete určité kalendáře udržet zcela oddělené.</p>
            <p class="text-blue-800 mb-0"><strong>Jak to funguje:</strong> Když jsou v kalendáři zdroje vytvořeny události, SyncMyDay odešle e-mailové pozvánky na speciální adresu. Tyto pozvánky se automaticky zobrazí jako blokovací události.</p>
        </div>
    </div>
</div>

<h2>Kdy používat e-mailové kalendáře</h2>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-green-900 mb-3">✅ Skvělé pro</h3>
        <ul class="text-green-800 space-y-2 mb-0">
            <li>Kalendáře bez podpory API</li>
            <li>Starší e-mailové klienty (Thunderbird, Lotus Notes)</li>
            <li>Přijímání blokovacích pozvánek do e-mailové schránky</li>
            <li>Jednoduchou jednosměrnou synchronizaci</li>
            <li>Maximální soukromí (události pouze přes zabezpečený e-mail)</li>
        </ul>
    </div>
    
    <div class="p-6 bg-gradient-to-br from-yellow-50 to-orange-50 border border-yellow-200 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-yellow-900 mb-3">⚠️ Zvažte alternativy, pokud</h3>
        <ul class="text-yellow-800 space-y-2 mb-0">
            <li>Potřebujete synchronizaci v reálném čase (e-mail má zpoždění)</li>
            <li>Váš kalendář podporuje API přístup (Google, Microsoft)</li>
            <li>Potřebujete obousměrnou synchronizaci</li>
            <li>Chcete automatické přijetí (e-mailové kalendáře vyžadují manuální akce)</li>
        </ul>
    </div>
</div>

<h2>Dva způsoby použití e-mailových kalendářů</h2>

<div class="space-y-6 mb-8">
    <div class="border-2 border-indigo-200 rounded-xl p-6 bg-indigo-50">
        <div class="flex items-start">
            <div class="w-12 h-12 rounded-lg bg-indigo-600 flex items-center justify-center mr-4 flex-shrink-0">
                <span class="text-white font-bold text-2xl">1</span>
            </div>
            <div>
                <h3 class="!mt-0 !mb-2 text-xl font-bold text-indigo-900">Přijímání blokátorů e-mailem</h3>
                <p class="text-indigo-800 mb-0">Když máte události v kalendáři Google/Microsoft, SyncMyDay odešle e-mailové pozvánky na libovolnou vámi zadanou e-mailovou adresu. Tyto pozvánky můžete přijmout ve svém e-mailovém klientovi (Outlook, Thunderbird atd.) a objeví se ve vašem kalendáři.</p>
            </div>
        </div>
    </div>
    
    <div class="border-2 border-purple-200 rounded-xl p-6 bg-purple-50">
        <div class="flex items-start">
            <div class="w-12 h-12 rounded-lg bg-purple-600 flex items-center justify-center mr-4 flex-shrink-0">
                <span class="text-white font-bold text-2xl">2</span>
            </div>
            <div>
                <h3 class="!mt-0 !mb-2 text-xl font-bold text-purple-900">Přeposílání pozvánek do SyncMyDay</h3>
                <p class="text-purple-800 mb-0">Získejte jedinečnou e-mailovou adresu od SyncMyDay (např. <code>abc123@syncmyday.com</code>). Když obdržíte pozvánky do kalendáře na tuto adresu, SyncMyDay automaticky vytvoří blokovací události v ostatních připojených kalendářích.</p>
            </div>
        </div>
    </div>
</div>

<h2>Průvodce nastavením</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Přejděte na Připojení kalendářů</h3>
            <p>Přejděte na <strong>Kalendáře</strong> v menu, nebo jděte na <a href="{{ route('connections.index') }}">stránku Připojení kalendářů</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard se zvýrazněným menu Kalendáře</p>
                <p class="text-sm">Navigační lišta zobrazující možnost Kalendáře</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Klikněte na "Připojit e-mailový kalendář"</h3>
            <p>Najděte a klikněte na tlačítko <strong>E-mailový kalendář</strong> s ikonou obálky.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Poskytovatelé kalendářů s možností E-mailový kalendář</p>
                <p class="text-sm">Zobrazuje rozhraní pro připojení se zvýrazněným E-mailovým kalendářem</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte metodu nastavení</h3>
            <p>Uvidíte dvě možnosti:</p>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                    <h4 class="!mt-0 text-lg font-semibold text-blue-900 mb-2">Možnost A: Přijímání pozvánek</h4>
                    <p class="text-blue-800 text-sm mb-2">Zadejte e-mailovou adresu, na které chcete přijímat pozvánky do kalendáře. Tento e-mail by měl být připojen k aplikaci kalendáře (Outlook, Thunderbird, Apple Mail atd.).</p>
                    <p class="text-blue-800 text-sm font-semibold mb-0">Příklad: <code>moje-prace@firma.cz</code></p>
                </div>
                
                <div class="p-4 bg-purple-50 border-2 border-purple-200 rounded-lg">
                    <h4 class="!mt-0 text-lg font-semibold text-purple-900 mb-2">Možnost B: Získání jedinečné adresy</h4>
                    <p class="text-purple-800 text-sm mb-2">SyncMyDay vygeneruje pro vás jedinečnou e-mailovou adresu (např. <code>abc123@syncmyday.com</code>). Přeposílejte pozvánky do kalendáře na tuto adresu a my je automaticky zpracujeme.</p>
                    <p class="text-purple-800 text-sm font-semibold mb-0">Není potřeba zadávat e-mail—stačí kliknout na "Vygenerovat adresu"</p>
                </div>
            </div>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mt-4">
                <p class="text-yellow-900 text-sm mb-0"><strong>Můžete použít obě metody!</strong> Vytvořte jeden e-mailový kalendář pro přijímání pozvánek a další pro odesílání pozvánek do SyncMyDay.</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Dejte mu název</h3>
            <p>Zadejte popisný název pro tento e-mailový kalendář, například:</p>
            <ul>
                <li><code>Pracovní e-mailový kalendář</code></li>
                <li><code>Thunderbird kalendář</code></li>
                <li><code>Outlook Desktop</code></li>
            </ul>
            <p>To vám pomůže identifikovat, který e-mailový kalendář je který, pokud vytvoříte více.</p>
        </div>
    </div>
    
    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Uložit a připojit</h3>
            <p>Klikněte na <strong>"Připojit"</strong> nebo <strong>"Uložit"</strong>. Váš e-mailový kalendář se objeví v seznamu připojení.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ E-mailový kalendář připojen!</h4>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li>Pokud jste zvolili <strong>Možnost A</strong>: Budete dostávat e-mailové pozvánky na zadanou adresu, když budou synchronizovány události</li>
                    <li>Pokud jste zvolili <strong>Možnost B</strong>: Zkopírujte jedinečnou adresu a nastavte přeposílání e-mailů (další krok)</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Úspěšně připojený e-mailový kalendář</p>
                <p class="text-sm">Zobrazuje e-mailový kalendář v seznamu připojení se stavem</p>
            </div>
        </div>
    </div>
</div>

<h2>Nastavení přeposílání e-mailů (Možnost B)</h2>

<p>Pokud jste si zvolili získání jedinečné adresy SyncMyDay, musíte na ni nastavit přeposílání pozvánek do kalendáře:</p>

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
            <li>Přejděte do Nastavení Gmailu (⚙️ → Zobrazit všechna nastavení)</li>
            <li>Klikněte na záložku <strong>"Přeposílání a POP/IMAP"</strong></li>
            <li>Klikněte na <strong>"Přidat adresu pro přeposílání"</strong></li>
            <li>Zadejte vaši adresu SyncMyDay (např. <code>abc123@syncmyday.com</code>)</li>
            <li>Gmail odešle potvrzovací kód na tuto adresu (zkontrolujte s námi!)</li>
            <li>Po potvrzení nastavte filtr pro přeposílání pouze pozvánek do kalendáře</li>
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
            <li>Přejděte do Nastavení Outlooku (⚙️ → Zobrazit všechna nastavení Outlooku)</li>
            <li>Přejděte na <strong>Pošta → Přeposílání</strong></li>
            <li>Povolte přeposílání a zadejte vaši adresu SyncMyDay</li>
            <li>Uložte změny</li>
            <li>Volitelně vytvořte pravidlo pro přeposílání pouze e-mailů s přílohou <code>.ics</code></li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-xl p-6">
        <h3 class="!mt-0 flex items-center">
            <div class="w-10 h-10 rounded-lg bg-gray-700 flex items-center justify-center mr-3">
                <span class="text-white font-bold">📧</span>
            </div>
            Ostatní e-mailoví klienti
        </h3>
        <p class="mb-2">Většina e-mailových klientů podporuje pravidla přeposílání. Hledejte:</p>
        <ul class="mb-0">
            <li><strong>Filtry</strong> nebo <strong>Pravidla</strong> v nastavení</li>
            <li>Vytvořte pravidlo: "Když zpráva má přílohu s příponou <code>.ics</code>"</li>
            <li>Akce: "Přeposlat na <code>vase-syncmyday-adresa@syncmyday.com</code>"</li>
        </ul>
    </div>
</div>

<h2>Vytváření pravidel synchronizace s e-mailovými kalendáři</h2>

<p>Jakmile je váš e-mailový kalendář připojen, můžete jej použít v pravidlech synchronizace:</p>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-indigo-900 mb-3">Jako cíl (přijímání pozvánek)</h3>
        <p class="text-indigo-800 mb-3"><strong>Příklad:</strong> Google Calendar → E-mailový kalendář</p>
        <ul class="text-indigo-700 space-y-1 mb-0 text-sm">
            <li>Zdroj: Váš pracovní kalendář Google</li>
            <li>Cíl: E-mailový kalendář s <code>osobni@example.com</code></li>
            <li>Výsledek: Budete dostávat e-mailové pozvánky na všechny pracovní události na osobní e-mail</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-purple-200 bg-purple-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-purple-900 mb-3">Jako zdroj (přeposílání pozvánek)</h3>
        <p class="text-purple-800 mb-3"><strong>Příklad:</strong> E-mailový kalendář → Google Calendar</p>
        <ul class="text-purple-700 space-y-1 mb-0 text-sm">
            <li>Zdroj: E-mailový kalendář s jedinečnou adresou</li>
            <li>Cíl: Váš pracovní kalendář Google</li>
            <li>Výsledek: Pozvánky do kalendáře zaslané na vaši jedinečnou adresu se zobrazí jako blokátory v Google</li>
        </ul>
    </div>
</div>

<h2>Jak funguje synchronizace e-mailového kalendáře</h2>

<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl mb-8">
    <h3 class="!mt-0 text-lg font-semibold text-gray-900 mb-4">Proces</h3>
    
    <div class="space-y-4">
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">1</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Událost vytvořena ve zdrojovém kalendáři</p>
                <p class="text-gray-700 text-sm mb-0">Ve vašem zdrojovém kalendáři (např. Google Calendar) je vytvořena událost</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">2</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">SyncMyDay detekuje změnu</p>
                <p class="text-gray-700 text-sm mb-0">Obdržíme webhook notifikaci (pro API kalendáře) nebo kontrolujeme změny (CalDAV/E-mail)</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">3</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Odeslaná e-mailová pozvánka</p>
                <p class="text-gray-700 text-sm mb-0">E-mail s přílohou <code>.ics</code> je odeslán na adresu vašeho e-mailového kalendáře</p>
            </div>
        </div>
        
        <div class="flex items-start">
            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center mr-3 flex-shrink-0 font-bold">4</div>
            <div>
                <p class="font-semibold text-gray-900 mb-1">Událost se zobrazí v e-mailovém klientovi</p>
                <p class="text-gray-700 text-sm mb-0">Váš e-mailový klient (Outlook, Thunderbird atd.) obdrží pozvánku a zobrazí ji ve vašem kalendáři</p>
            </div>
        </div>
    </div>
</div>

<h2>Časté otázky</h2>

<div class="space-y-4" x-data="{ open: null }">
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q1' ? open = null : open = 'q1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Musím manuálně přijímat e-mailové pozvánky?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q1'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Záleží na nastavení vašeho e-mailového klienta. Většina e-mailových klientů může být nakonfigurována pro automatické přijetí pozvánek do kalendáře. Zkontrolujte nastavení kalendáře pro "Automaticky přijímat žádosti o schůzky" nebo podobné možnosti.</p>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q2' ? open = null : open = 'q2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Jak rychlá je synchronizace e-mailového kalendáře?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q2'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Doručení e-mailu je obvykle rychlé (během minut), ale závisí na zpožděních e-mailového serveru. Pokud potřebujete okamžitou synchronizaci, zvažte použití Google Calendar nebo Microsoft 365, které podporují webhooky v reálném čase.</p>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q3' ? open = null : open = 'q3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Mohu použít stejnou e-mailovou adresu pro více e-mailových kalendářů?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q3'" x-collapse class="px-6 pb-4">
            <p class="mb-0">Ano! Můžete vytvořit více e-mailových kalendářů, které všechny odesílají na stejnou e-mailovou adresu. To je užitečné, pokud chcete přijímat blokátory z různých zdrojových kalendářů na jednom místě.</p>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'q4' ? open = null : open = 'q4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Co když přestanu dostávat e-maily?</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'q4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'q4'" x-collapse class="px-6 pb-4">
            <p class="mb-2">Zkontrolujte tyto možné problémy:</p>
            <ul class="mb-0">
                <li>E-mail zachycen ve složce spam</li>
                <li>Pravidlo přeposílání e-mailů zakázáno nebo nefunkční</li>
                <li>Připojení e-mailového kalendáře neaktivní (zkontrolujte stránku Připojení)</li>
                <li>Pravidlo synchronizace pozastaveno nebo smazáno</li>
            </ul>
        </div>
    </div>
</div>

<h2>Další kroky</h2>

<div class="grid md:grid-cols-2 gap-6">
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-indigo-200 bg-indigo-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Vytvořte pravidlo synchronizace</h3>
        </div>
        <p class="mb-0">Nastavte svou první synchronizaci pomocí e-mailového kalendáře.</p>
    </a>
    
    <a href="{{ route('help.faq') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Podívejte se na FAQ</h3>
        </div>
        <p class="mb-0">Další odpovědi na časté otázky o SyncMyDay.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Pro vývojáře a technické uživatele</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Formát iCalendar (RFC 5545)</h4>
        <p>E-mailové pozvánky používají formát iCalendar (<code>.ics</code>):</p>
        <ul>
            <li>Standardní MIME typ: <code>text/calendar</code></li>
            <li>Obsahuje komponenty <code>VEVENT</code> s daty události</li>
            <li>Zahrnuje <code>VTIMEZONE</code> pro informace o časovém pásmu</li>
            <li>Používá <code>METHOD:REQUEST</code> pro pozvánky</li>
        </ul>
        
        <h4>Odesílání e-mailů</h4>
        <p>Odchozí e-mailové pozvánky:</p>
        <ul>
            <li>Odesílány přes systém Laravel Mail (SMTP, Mailgun, SendGrid atd.)</li>
            <li>Adresa odesílatele: Nakonfigurováno v <code>.env</code> (<code>MAIL_FROM_ADDRESS</code>)</li>
            <li>Odpověď na: <code>noreply@syncmyday.com</code></li>
            <li>Příloha: soubor <code>event.ics</code></li>
        </ul>
        
        <h4>Přijímání e-mailů (Inbound)</h4>
        <p>Pro jedinečné adresy SyncMyDay:</p>
        <ul>
            <li>IMAP polling: Kontroluje schránku každou minutu</li>
            <li>Podpora webhooků: Mailgun, SendGrid, Postmark</li>
            <li>Parsuje přílohy <code>.ics</code></li>
            <li>Extrahuje token z adresy příjemce (např. <code>abc123</code> z <code>abc123@syncmyday.com</code>)</li>
        </ul>
        
        <h4>Zpracování událostí</h4>
        <ol>
            <li>Parsování souboru <code>.ics</code> pro komponenty <code>VEVENT</code></li>
            <li>Extrahování <code>DTSTART</code>, <code>DTEND</code>, <code>SUMMARY</code>, <code>STATUS</code></li>
            <li>Konverze do interního formátu události</li>
            <li>Kontrola pravidel synchronizace a vytvoření blokovacích událostí</li>
            <li>Označení e-mailu jako zpracovaného (přesun do složky "Zpracováno" nebo smazání)</li>
        </ol>
        
        <h4>Bezpečnost</h4>
        <ul>
            <li>Jedinečné adresy jsou kryptograficky generované tokeny</li>
            <li>Validace tokenu zabraňuje neoprávněnému přístupu</li>
            <li>Obsah e-mailu je před zpracováním sanitizován</li>
            <li>Zpracovávány jsou pouze přílohy <code>.ics</code></li>
        </ul>
    </div>
</div>
</div>
@endsection


