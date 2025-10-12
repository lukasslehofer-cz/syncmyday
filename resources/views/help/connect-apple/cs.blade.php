@extends('layouts.public')

@section('title', 'Připojení Apple iCloud Calendar')

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
        <h1 class="!mb-0">Připojení Apple iCloud Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Použití CalDAV s heslem pro aplikaci</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Důležité: Vyžadováno heslo pro aplikaci</h3>
            <p class="text-blue-800 mb-2">Apple vyžaduje <strong>Heslo pro aplikaci</strong> pro aplikace třetích stran, pokud máte povoleno dvoufaktorové ověření (které je vyžadováno pro všechny Apple účty).</p>
            <p class="text-blue-800 mb-0"><strong>Nebojte se!</strong> Tento průvodce vás provede jeho generováním. Zabere to asi 5 minut.</p>
        </div>
    </div>
</div>

<h2>Předpoklady</h2>

<div class="p-6 bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-300 rounded-xl mb-8">
    <p class="mb-3">Než začnete, ujistěte se, že máte:</p>
    <ul class="space-y-2 mb-0">
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>iCloud účet</strong> (Apple ID) s kalendáři</span>
        </li>
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Povolené dvoufaktorové ověření</strong> (povoleno ve výchozím nastavení pro všechny účty)</span>
        </li>
        <li class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span><strong>Přístup na appleid.apple.com</strong> pro generování hesla pro aplikaci</span>
        </li>
    </ul>
</div>

<h2>Průvodce krok za krokem</h2>

<div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
    <p class="font-semibold text-yellow-900 mb-2">Tento průvodce má 2 části:</p>
    <ol class="text-yellow-800 mb-0 space-y-1">
        <li><strong>Část A:</strong> Vygenerujte heslo pro aplikaci z Apple (5 minut)</li>
        <li><strong>Část B:</strong> Připojte svůj iCloud kalendář v SyncMyDay (2 minuty)</li>
    </ol>
</div>

<h3 class="text-2xl font-bold text-indigo-600 mb-4">Část A: Generování hesla pro aplikaci</h3>

<div class="space-y-8 mb-12">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h4 class="!mt-0">Přejděte do nastavení Apple ID</h4>
            <p>Otevřete prohlížeč a přejděte na <a href="https://appleid.apple.com" target="_blank" class="font-semibold">appleid.apple.com</a></p>
            <p>Přihlaste se pomocí e-mailu a hesla vašeho Apple ID.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Přihlašovací stránka Apple ID na appleid.apple.com</p>
                <p class="text-sm">Zobrazuje přihlašovací formulář Apple ID</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h4 class="!mt-0">Autentizujte se dvoufaktorově</h4>
            <p>Apple pošle ověřovací kód na vaše důvěryhodná zařízení (iPhone, iPad, Mac). Zadejte 6místný kód, když budete vyzváni.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Zadání kódu dvoufaktorové autentizace</p>
                <p class="text-sm">Zobrazuje vstup pro 6místný ověřovací kód</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h4 class="!mt-0">Přejděte do sekce Zabezpečení</h4>
            <p>Po přihlášení najděte a klikněte na sekci <strong>"Přihlášení a zabezpečení"</strong>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Stránka účtu Apple ID se zvýrazněnou sekcí "Přihlášení a zabezpečení"</p>
                <p class="text-sm">Zobrazuje hlavní dashboard Apple ID</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h4 class="!mt-0">Klikněte na "Hesla pro aplikace"</h4>
            <p>V sekci Zabezpečení posuňte dolů, dokud nenajdete <strong>"Hesla pro aplikace"</strong> a klikněte na ně.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Nastavení zabezpečení s možností "Hesla pro aplikace"</p>
                <p class="text-sm">Zobrazuje položku menu Hesla pro aplikace</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h4 class="!mt-0">Vygenerujte nové heslo</h4>
            <p>Klikněte na tlačítko <strong>"Vygenerovat heslo pro aplikaci"</strong> (nebo ikonu +).</p>
            <p>Když budete vyzváni k zadání názvu, zadejte něco popisného jako:</p>
            <ul class="mb-4">
                <li><code>SyncMyDay</code></li>
                <li><code>SyncMyDay Synchronizace kalendáře</code></li>
            </ul>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 text-sm mb-0"><strong>Tip:</strong> Název vám pomůže zapamatovat si, k čemu je toto heslo, zejména pokud ho budete později potřebovat odvolat.</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dialog pro zadání názvu hesla pro aplikaci</p>
                <p class="text-sm">Zobrazuje vstupní pole s "SyncMyDay" zadaným</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h4 class="!mt-0">Zkopírujte heslo</h4>
            <p>Apple vygeneruje heslo, které vypadá takto: <code>abcd-efgh-ijkl-mnop</code></p>
            
            <div class="p-4 bg-red-50 border-2 border-red-300 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-red-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-red-900 mb-1">⚠️ DŮLEŽITÉ: Zkopírujte toto heslo TEĎ!</p>
                        <p class="text-red-800 text-sm mb-0">Apple ukáže toto heslo pouze jednou. Pokud ho ztratíte, budete muset vygenerovat nové. Zkopírujte ho do schránky nebo ho vložte přímo do SyncMyDay v dalším kroku.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Zobrazené vygenerované heslo pro aplikaci</p>
                <p class="text-sm">Zobrazuje heslo ve skupinách 4 znaků s tlačítkem kopírovat</p>
            </div>
        </div>
    </div>
</div>

<h3 class="text-2xl font-bold text-purple-600 mb-4">Část B: Připojení v SyncMyDay</h3>

<div class="space-y-8">
    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h4 class="!mt-0">Přejděte na Připojení kalendářů</h4>
            <p>Vraťte se do SyncMyDay a přejděte na <strong>Kalendáře</strong> v menu, nebo jděte přímo na <a href="{{ route('connections.index') }}">stránku Připojení kalendářů</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard SyncMyDay s menu Kalendáře</p>
                <p class="text-sm">Navigace zobrazující možnost Kalendáře</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 8 -->
    <div class="flex items-start">
        <span class="step-number">8</span>
        <div class="flex-1">
            <h4 class="!mt-0">Klikněte na "Připojit Apple iCloud"</h4>
            <p>Najděte a klikněte na tlačítko <strong>Apple iCloud</strong> s logem Apple.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Poskytovatelé kalendářů s možností Apple iCloud</p>
                <p class="text-sm">Zobrazuje tlačítko pro připojení Apple iCloud</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 9 -->
    <div class="flex items-start">
        <span class="step-number">9</span>
        <div class="flex-1">
            <h4 class="!mt-0">Zadejte své přihlašovací údaje</h4>
            <p>Vyplňte připojovací formulář:</p>
            <ul>
                <li><strong>E-mail:</strong> Váš celý Apple ID e-mail (např. vas.email@icloud.com)</li>
                <li><strong>Heslo:</strong> Vložte heslo pro aplikaci, které jste zkopírovali z Apple (včetně pomlček nebo bez - obojí funguje)</li>
            </ul>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Použijte heslo pro aplikaci</p>
                        <p class="text-blue-800 text-sm mb-0">NEPOUŽÍVEJTE své běžné heslo Apple ID. Použijte heslo pro aplikaci, které jste právě vygenerovali. Vaše běžné heslo nebude fungovat.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Připojovací formulář iCloud s poli pro e-mail a heslo</p>
                <p class="text-sm">Zobrazuje formulář pro zadání přihlašovacích údajů</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 10 -->
    <div class="flex items-start">
        <span class="step-number">10</span>
        <div class="flex-1">
            <h4 class="!mt-0">Vyberte kalendáře</h4>
            <p>Po připojení SyncMyDay načte vaše iCloud kalendáře. Vyberte, které chcete synchronizovat.</p>
            <p>Běžné iCloud kalendáře zahrnují:</p>
            <ul>
                <li><strong>Domů</strong> - Váš výchozí osobní kalendář</li>
                <li><strong>Práce</strong> - Pokud jste vytvořili pracovní kalendář</li>
                <li><strong>Rodina</strong> - Sdílený rodinný kalendář</li>
                <li>Jakékoliv vlastní kalendáře, které jste vytvořili</li>
            </ul>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Výběr kalendáře s iCloud kalendáři</p>
                <p class="text-sm">Zobrazuje zaškrtávací políčka pro každý dostupný iCloud kalendář</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 11 -->
    <div class="flex items-start">
        <span class="step-number">11</span>
        <div class="flex-1">
            <h4 class="!mt-0">Vše hotovo!</h4>
            <p>Váš Apple iCloud kalendář je nyní připojen! Uvidíte ho ve vašem seznamu připojení kalendářů.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ Co dál?</h4>
                <ul class="text-green-800 space-y-1 mb-2">
                    <li>Váš iCloud kalendář je připraven k použití v pravidlech synchronizace</li>
                    <li>Události se budou synchronizovat každých 15 minut (omezení CalDAV)</li>
                    <li>Nyní můžete vytvářet pravidla synchronizace pro udržení kalendářů synchronizovaných</li>
                </ul>
                <p class="text-green-800 text-sm mb-0"><strong>Poznámka:</strong> iCloud používá protokol CalDAV, který nepodporuje webhooky v reálném čase. Kontrolujeme změny každých 15 minut, abychom byli aktuální.</p>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Úspěšně připojený iCloud kalendář</p>
                <p class="text-sm">Zobrazuje kalendář v seznamu připojení se stavem "Aktivní"</p>
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
            "Autentizace se nezdařila" nebo "Neplatné přihlašovací údaje"
        </h3>
        <p><strong>Časté příčiny:</strong></p>
        <ul>
            <li>Použili jste své běžné heslo Apple ID místo hesla pro aplikaci</li>
            <li>Překlep v e-mailové adrese nebo hesle</li>
            <li>Heslo pro aplikaci bylo odvoláno</li>
        </ul>
        <p><strong>Řešení:</strong></p>
        <ol>
            <li>Zkontrolujte, že používáte heslo pro aplikaci, ne běžné heslo</li>
            <li>Vygenerujte nové heslo pro aplikaci a zkuste to znovu</li>
            <li>Ujistěte se, že váš e-mail je správný (včetně @icloud.com nebo @me.com)</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Nevidím možnost "Hesla pro aplikace"
        </h3>
        <p>To obvykle znamená, že na vašem účtu není povoleno dvoufaktorové ověření.</p>
        <p><strong>Řešení:</strong></p>
        <ol>
            <li>Přejděte do nastavení Apple ID na appleid.apple.com</li>
            <li>Přejděte na Přihlášení a zabezpečení</li>
            <li>Povolte dvoufaktorové ověření</li>
            <li>Po povolení se objeví možnost Hesla pro aplikace</li>
        </ol>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Synchronizace je pomalá (15minutové zpoždění)
        </h3>
        <p>To je normální pro iCloud kalendáře. Protokol CalDAV od Apple nepodporuje webhooky v reálném čase jako Google nebo Microsoft.</p>
        <p><strong>Proč?</strong> Kontrolujeme iCloud každých 15 minut pro detekci změn. To je standardní přístup pro poskytovatele CalDAV a vyvažuje odezvu se zatížením serveru.</p>
        <p><strong>Alternativa:</strong> Pokud potřebujete okamžitou synchronizaci, zvažte použití Google Calendar nebo Microsoft 365, které oba podporují webhooky v reálném čase.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Jak odvolám heslo pro aplikaci?
        </h3>
        <p>Pokud potřebujete odvolat přístup:</p>
        <ol>
            <li>Přejděte na appleid.apple.com</li>
            <li>Přihlaste se a přejděte na Přihlášení a zabezpečení</li>
            <li>Klikněte na Hesla pro aplikace</li>
            <li>Najděte "SyncMyDay" v seznamu</li>
            <li>Klikněte na "Odvolat" vedle něj</li>
        </ol>
        <p class="mb-0">Můžete také odpojit kalendář ze stránky Připojení kalendářů v SyncMyDay a přestaneme používat přihlašovací údaje.</p>
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
        <p class="mb-0">Připojte pracovní kalendář (Google, Microsoft) pro synchronizaci s vaším osobním iCloud kalendářem.</p>
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
        <p class="mb-0">Začněte synchronizovat události mezi vašimi kalendáři.</p>
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
        <h4>Protokol CalDAV</h4>
        <p>Apple iCloud používá protokol CalDAV (RFC 4791):</p>
        <ul>
            <li><strong>URL serveru:</strong> <code>https://caldav.icloud.com</code></li>
            <li><strong>Principal URL:</strong> Automaticky zjištěno pomocí DAV service discovery</li>
            <li><strong>Autentizace:</strong> Basic Auth s Apple ID + heslem pro aplikaci</li>
        </ul>
        
        <h4>Interval pollingu</h4>
        <p>Protože CalDAV nepodporuje push notifikace, pollujeme každých 15 minut:</p>
        <ul>
            <li>Používá PROPFIND požadavky pro kontrolu metadat kalendáře</li>
            <li>Stahuje pouze změněné události (pomocí ETags)</li>
            <li>Minimalizuje šířku pásma a respektuje limity Apple</li>
        </ul>
        
        <h4>Ukládání přihlašovacích údajů</h4>
        <ul>
            <li>Hesla pro aplikace jsou šifrována pomocí AES-256</li>
            <li>Bezpečně uložena v naší databázi</li>
            <li>Nikdy nepřenesena v čistém textu (vždy HTTPS)</li>
            <li>Okamžitě smazána při odpojení kalendáře</li>
        </ul>
        
        <h4>Kompatibilita</h4>
        <p>Tato metoda připojení funguje s:</p>
        <ul>
            <li>Kalendáři iCloud.com</li>
            <li>Kalendáři synchronizovanými do iCloud z iOS zařízení</li>
            <li>Kalendáři synchronizovanými z aplikace Kalendář macOS</li>
            <li>Sdílenými iCloud kalendáři (pokud máte oprávnění k zápisu)</li>
        </ul>
        
        <h4>Omezení</h4>
        <ul>
            <li><strong>Žádná synchronizace v reálném čase:</strong> 15minutový interval pollingu</li>
            <li><strong>Vyžadováno heslo pro aplikaci:</strong> Nelze použít běžné heslo</li>
            <li><strong>Vyžadováno dvoufaktorové ověření:</strong> Všechny iCloud účty nyní vyžadují 2FA</li>
        </ul>
    </div>
</div>
</div>
@endsection

