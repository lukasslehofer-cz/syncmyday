@extends('layouts.public')

@section('title', 'Vytváření pravidel synchronizace')

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
        <h1 class="!mb-0">Vytváření pravidel synchronizace</h1>
        <p class="text-lg text-gray-600 !mb-0">Nastavte automatickou synchronizaci kalendářů</p>
    </div>
</div>

<div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-indigo-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-indigo-900 mb-2">Co je pravidlo synchronizace?</h3>
            <p class="text-indigo-800 mb-2"><strong>Pravidlo synchronizace</strong> definuje, jak mají být události z jednoho kalendáře (<em>zdroje</em>) synchronizovány do jiného kalendáře (<em>cíle</em>) jako blokovací události.</p>
            <p class="text-indigo-800 mb-0"><strong>Příklad:</strong> "Synchronizovat všechny zaneprázdněné události z mého osobního kalendáře Google do pracovního kalendáře Outlook jako blokátory 'Zaneprázdněn'."</p>
        </div>
    </div>
</div>

<h2>Než začnete</h2>

<div class="p-6 bg-yellow-50 border border-yellow-200 rounded-xl mb-8">
    <p class="font-semibold text-yellow-900 mb-2">✅ Ujistěte se, že máte:</p>
    <ul class="text-yellow-800 space-y-1 mb-0">
        <li><strong>Alespoň 2 připojené kalendáře</strong> - Potřebujete zdrojový kalendář a cílový kalendář</li>
        <li><strong>Oba kalendáře zobrazují stav "Aktivní"</strong> - Zkontrolujte stránku Připojení kalendářů</li>
        <li><strong>Události ve zdrojovém kalendáři</strong> - Pro otestování synchronizace</li>
    </ul>
</div>

<h2>Průvodce krok za krokem</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Přejděte na Pravidla synchronizace</h3>
            <p>Přejděte na <strong>Pravidla synchronizace</strong> v hlavním menu, nebo jděte přímo na stránku Pravidla synchronizace z vašeho dashboardu.</p>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-1.jpg') }}" 
                         alt="Dashboard se zvýrazněným menu Pravidla synchronizace" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Přejděte na stránku Synchronizační pravidla</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Klikněte na "Vytvořit nové pravidlo synchronizace"</h3>
            <p>Na stránce Pravidla synchronizace klikněte na tlačítko <strong>"Vytvořit nové pravidlo synchronizace"</strong> nebo <strong>"+ Nové pravidlo"</strong>.</p>
            
        </div>
    </div>
    
    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte zdrojový kalendář</h3>
            <p>Vyberte, události z jakého kalendáře chcete synchronizovat <strong>Z</strong>.</p>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <p class="text-blue-900 text-sm mb-2"><strong>Co je zdrojový kalendář?</strong></p>
                <p class="text-blue-800 text-sm mb-0">Zdrojový kalendář je místo, kde jsou vaše skutečné události. Když v tomto kalendáři vytvoříte, aktualizujete nebo smažete události, SyncMyDay automaticky vytvoří nebo aktualizuje blokovací události ve vašich cílových kalendářích.</p>
            </div>
            
            <p><strong>Běžné příklady:</strong></p>
            <ul>
                <li><strong>Osobní kalendář</strong> (zdroj) → Pracovní kalendář (cíl): Blokovat pracovní čas, když máte osobní schůzky</li>
                <li><strong>Pracovní kalendář</strong> (zdroj) → Osobní kalendář (cíl): Blokovat osobní čas, když máte pracovní schůzky</li>
            </ul>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-3.jpg') }}" 
                         alt="Rozbalovací menu zdrojového kalendáře zobrazující připojené kalendáře" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte zdrojový kalendář (odkud kopírovat události)</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte cílový kalendář(e)</h3>
            <p>Vyberte jeden nebo více kalendářů, kde mají být vytvořeny blokovací události.</p>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 text-sm mb-2"><strong>Tip: Více cílů</strong></p>
                <p class="text-purple-800 text-sm mb-0">Můžete vybrat více cílových kalendářů! Například synchronizujte osobní události současně do pracovního kalendáře Google A pracovního kalendáře Outlook.</p>
            </div>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-4.jpg') }}" 
                         alt="Výběr cílového kalendáře se zaškrtávacími políčky" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vyberte cílové kalendáře (kam kopírovat události)</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Nakonfigurujte název blokovací události</h3>
            <p>Zadejte text, který se zobrazí jako název pro všechny blokovací události vytvořené tímto pravidlem.</p>
            
            <p><strong>Oblíbené názvy:</strong></p>
            <ul>
                <li><code>Zaneprázdněn</code> - Jednoduché a univerzální</li>
                <li><code>Osobní čas</code> - Indikuje soukromý čas</li>
                <li><code>Není k dispozici</code> - Jasná nedostupnost</li>
                <li><code>Schůzka</code> - Obecný zástupný symbol</li>
                <li><code>🔒 Soukromé</code> - S emoji pro vizuální odlišení</li>
            </ul>
            
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                <p class="text-green-900 text-sm mb-1"><strong>Pamatujte:</strong></p>
                <p class="text-green-800 text-sm mb-0">Název blokátoru je to, co ostatní uvidí ve vašem kalendáři. Vyberte něco vhodného pro váš kontext (práce, osobní atd.).</p>
            </div>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-5.jpg') }}" 
                         alt="Vstupní pole názvu blokátoru" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Zadejte název pro blokující události</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Nastavte filtry (Volitelné, ale doporučené)</h3>
            <p>Filtry kontrolují, <strong>které události</strong> se synchronizují. Zde můžete doladit svou synchronizaci.</p>
            
            <h4 class="text-lg font-semibold text-gray-800 mt-6 mb-3">Dostupné filtry:</h4>
            
            <!-- Pouze zaneprázdněné události -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-gray-900">Synchronizovat pouze zaneprázdněné události</h4>
                        <p class="text-gray-700 text-sm mb-2">Synchronizovat pouze události označené jako "Zaneprázdněn". Přeskočit události označené jako "Volný" nebo "Předběžně".</p>
                        <p class="text-gray-600 text-xs mb-0"><strong>Použití:</strong> Zabránit předběžným schůzkám v blokování ostatních kalendářů, dokud nejsou potvrzeny.</p>
                    </div>
                </div>
            </div>
            
            <!-- Ignorovat celodenní události -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-gray-900">Ignorovat celodenní události</h4>
                        <p class="text-gray-700 text-sm mb-2">Nesynchronizovat celodenní události jako jsou svátky, narozeniny nebo dny mimo kancelář.</p>
                        <p class="text-gray-600 text-xs mb-0"><strong>Použití:</strong> Celodenní události často nemusí blokovat ostatní kalendáře (např. veřejné svátky).</p>
                    </div>
                </div>
            </div>
            
            <!-- Pouze pracovní doba -->
            <div class="mb-6 p-4 border-2 border-gray-200 rounded-lg">
                <div class="flex items-start mb-2">
                    <input type="checkbox" class="mt-1 mr-3" disabled checked>
                    <div>
                        <h4 class="!mt-0 !mb-1 font-semibold text-indigo-900">Zapnout časový a denní filtr</h4>
                        <p class="text-indigo-800 text-sm mb-3">Synchronizovat pouze události, které spadají do specifických hodin a dnů.</p>                                                
                        
                        <p class="text-indigo-700 text-xs mt-3 mb-0"><strong>Použití:</strong> Blokovat pouze pracovní kalendář během pracovní doby. Osobní události večer nebo o víkendech se nebudou synchronizovat.</p>
                    </div>
                </div>
            </div>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-6.jpg') }}" 
                         alt="Možnosti filtrů se zaškrtávacími políčky a selektory času" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Nastavte filtry (volitelné) pro specifické případy</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Zkontrolujte a uložte</h3>
            <p>Zkontrolujte nastavení pravidla synchronizace a klikněte na <strong>"Vytvořit pravidlo synchronizace"</strong> nebo <strong>"Uložit"</strong>.</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ Pravidlo synchronizace vytvořeno!</h4>
                <p class="text-green-800 mb-2">Vaše kalendáře se nyní automaticky synchronizují. Co se stane dál:</p>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li><strong>Počáteční synchronizace:</strong> Všechny existující události z vašeho zdrojového kalendáře budou synchronizovány během minut</li>
                    <li><strong>Aktualizace v reálném čase:</strong> Nové, aktualizované nebo smazané události se budou synchronizovat automaticky</li>
                    <li><strong>Můžete pozastavit nebo upravit:</strong> Pravidlo synchronizace kdykoli ze stránky Pravidla synchronizace</li>
                </ul>
            </div>
            
            <div class="my-6">
                <div class="rounded-xl overflow-hidden border-2 border-gray-200 shadow-lg hover:shadow-xl transition-shadow duration-300">
                    <img src="{{ asset('images/help/cs/sync-7.jpg') }}" 
                         alt="Potvrzovací stránka zobrazující aktivní pravidlo synchronizace" 
                         class="w-full h-auto"
                         loading="lazy">
                </div>
                <p class="text-center text-sm text-gray-600 mt-3 italic">Vaše pravidlo synchronizace je aktivní a funkční!</p>
            </div>
        </div>
    </div>
</div>

<h2>Běžné příklady pravidel synchronizace</h2>

<div class="grid md:grid-cols-2 gap-6 mb-8">
    <div class="p-6 border-2 border-blue-200 bg-blue-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-blue-900 mb-3">🏠 Osobní → Práce</h3>
        <ul class="text-blue-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Osobní kalendář Google</li>
            <li><strong>Cíl:</strong> Pracovní kalendář Outlook</li>
            <li><strong>Název:</strong> "Osobní čas"</li>
            <li><strong>Filtry:</strong> Pouze pracovní doba (9-17, Po-Pá), Pouze zaneprázdněné události</li>
            <li><strong>Výsledek:</strong> Kolegové vidí, že jste zaneprázdněni během osobních schůzek, ale pouze během pracovní doby</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-purple-200 bg-purple-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-purple-900 mb-3">💼 Práce → Osobní</h3>
        <ul class="text-purple-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Pracovní kalendář Outlook</li>
            <li><strong>Cíl:</strong> Osobní kalendář Google</li>
            <li><strong>Název:</strong> "Pracovní schůzka"</li>
            <li><strong>Filtry:</strong> Ignorovat celodenní události, Pouze zaneprázdněné události</li>
            <li><strong>Výsledek:</strong> Váš osobní kalendář ukazuje, kdy máte pracovní schůzky (užitečné pro rodinné plánování)</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-green-200 bg-green-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-green-900 mb-3">👨‍👩‍👧 Rodinný kalendář → Práce</h3>
        <ul class="text-green-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Sdílený rodinný kalendář Google</li>
            <li><strong>Cíl:</strong> Pracovní kalendář</li>
            <li><strong>Název:</strong> "Rodinný závazek"</li>
            <li><strong>Filtry:</strong> Pouze pracovní doba</li>
            <li><strong>Výsledek:</strong> Tým ví, že nejste k dispozici kvůli rodinným událostem jako je vyzvednutí dětí ze školy</li>
        </ul>
    </div>
    
    <div class="p-6 border-2 border-orange-200 bg-orange-50 rounded-xl">
        <h3 class="!mt-0 text-lg font-bold text-orange-900 mb-3">📅 Více osobních → Práce</h3>
        <ul class="text-orange-800 text-sm space-y-2 mb-0">
            <li><strong>Zdroj:</strong> Osobní kalendář</li>
            <li><strong>Cíle:</strong> Pracovní Google + Pracovní Outlook + Pracovní iCloud</li>
            <li><strong>Název:</strong> "Zaneprázdněn"</li>
            <li><strong>Filtry:</strong> Pracovní doba, Pouze zaneprázdněné</li>
            <li><strong>Výsledek:</strong> Zablokujte všechny vaše pracovní kalendáře najednou</li>
        </ul>
    </div>
</div>

<h2>Správa pravidel synchronizace</h2>

<div class="space-y-4">
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Pozastavit pravidlo synchronizace
        </h3>
        <p class="text-gray-700 mb-0">Potřebujete dočasně zastavit synchronizaci? Klikněte na tlačítko "Pozastavit" u jakéhokoli pravidla synchronizace. Blokovací události zůstanou, ale nové nebudou vytvářeny, dokud ji neobnovíte. Skvělé pro dovolenou nebo změny projektu.</p>
    </div>
    
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Upravit pravidlo synchronizace
        </h3>
        <p class="text-gray-700 mb-0">Klikněte na "Upravit" pro změnu jakéhokoli nastavení—filtry, název blokátoru, cílové kalendáře atd. Změny se vztahují na nové blokovací události. Existující blokátory zůstávají beze změny, pokud se nezmění zdrojová událost.</p>
    </div>
    
    <div class="p-6 border border-gray-200 rounded-xl">
        <h3 class="!mt-0 flex items-center text-lg font-semibold text-gray-900 mb-3">
            <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Smazat pravidlo synchronizace
        </h3>
        <p class="text-gray-700 mb-0">Klikněte na "Smazat" pro trvalé odstranění pravidla synchronizace. <strong>Všechny blokovací události</strong> vytvořené tímto pravidlem budou automaticky smazány z vašich cílových kalendářů. Tato akce nelze vrátit zpět.</p>
    </div>
</div>

<h2>Řešení problémů</h2>

<div class="space-y-4" x-data="{ open: null }">
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble1' ? open = null : open = 'trouble1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Blokovací události se neobjevují</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble1'" x-collapse class="px-6 pb-4">
            <p class="mb-2"><strong>Zkontrolujte tyto věci:</strong></p>
            <ol class="space-y-2 mb-0">
                <li>Stav pravidla synchronizace je "Aktivní" (není pozastaveno)</li>
                <li>Zdrojový a cílový kalendář zobrazují stav "Aktivní"</li>
                <li>Událost splňuje kritéria filtru (zkontrolujte stav zaneprázdněnosti, celodenní, pracovní dobu)</li>
                <li>Počkejte několik minut (CalDAV kalendáře kontrolují každých 15 minut)</li>
                <li>Zkontrolujte, zda byla dokončena počáteční synchronizace (hledejte časové razítko synchronizace)</li>
            </ol>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble2' ? open = null : open = 'trouble2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Příliš mnoho/málo událostí se synchronizuje</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble2'" x-collapse class="px-6 pb-4">
            <p class="mb-2">Upravte své filtry:</p>
            <ul class="mb-0">
                <li><strong>Příliš mnoho?</strong> Povolte "Ignorovat celodenní události" nebo "Pouze zaneprázdněné události" nebo omezit na pracovní dobu</li>
                <li><strong>Příliš málo?</strong> Zakažte filtry pro synchronizaci všech událostí, nebo upravte pracovní dobu pro zahrnutí více času</li>
                <li><strong>Tip:</strong> Upravte pravidlo synchronizace a zkuste různé kombinace filtrů, dokud nebude fungovat pro vaše potřeby</li>
            </ul>
        </div>
    </div>
    
    <div class="border border-gray-200 rounded-lg overflow-hidden">
        <button @click="open === 'trouble3' ? open = null : open = 'trouble3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
            <span>Blokovací události zobrazují špatné časy</span>
            <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open === 'trouble3'" x-collapse class="px-6 pb-4">
            <p class="mb-2">To je obvykle problém s časovým pásmem:</p>
            <ul class="mb-0">
                <li>Zkontrolujte časové pásmo účtu v Nastavení</li>
                <li>Ověřte nastavení časového pásma zdrojového kalendáře</li>
                <li>Zkontrolujte nastavení časového pásma cílového kalendáře</li>
                <li>Pokud používáte CalDAV, ujistěte se, že je časové pásmo správně nakonfigurováno v kalendářové službě</li>
            </ul>
        </div>
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
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Připojte více kalendářů</h3>
        </div>
        <p class="mb-0">Přidejte další připojení kalendářů pro vytvoření dalších pravidel synchronizace.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Jak fungují pravidla synchronizace pod pokličkou</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Architektura synchronizačního enginu</h4>
        <p>Když vytvoříte pravidlo synchronizace, stane se toto:</p>
        <ol>
            <li><strong>Počáteční synchronizace:</strong> Všechny události ze zdrojového kalendáře v časovém rozmezí (výchozí: minulých 7 dní, budoucích 90 dní) jsou synchronizovány</li>
            <li><strong>Registrace webhooků:</strong> Pro Google/Microsoft jsou registrovány webhooky pro příjem notifikací v reálném čase</li>
            <li><strong>Zpracování událostí:</strong> Každá událost je zkontrolována proti filtrům před vytvořením blokátoru</li>
            <li><strong>Vytvoření blokátoru:</strong> Nová událost je vytvořena v cílových kalendářích s vaším vlastním názvem</li>
            <li><strong>Sledování:</strong> Databázový záznam propojuje zdrojovou událost s blokovacími událostmi pro budoucí aktualizace/mazání</li>
        </ol>
        
        <h4>Reálný čas vs. Polling</h4>
        <ul>
            <li><strong>Google & Microsoft:</strong> Reálný čas přes webhooky (latence 1-2 minuty)</li>
            <li><strong>CalDAV & E-mail:</strong> Polling každých 15 minut</li>
            <li><strong>Obnovení webhooků:</strong> Automatické každé 3-7 dny (liší se podle poskytovatele)</li>
        </ul>
        
        <h4>Zpracování filtrů</h4>
        <p>Filtry jsou aplikovány v tomto pořadí:</p>
        <ol>
            <li>Kontrola, zda je událost celodenní (pokud je povoleno "Ignorovat celodenní události")</li>
            <li>Kontrola stavu události (pokud je povoleno "Pouze zaneprázdněné události")</li>
            <li>Kontrola, zda čas události spadá do pracovní doby (pokud je nakonfigurováno)</li>
            <li>Kontrola, zda den události je zahrnut ve vybraných dnech (pokud je povolena pracovní doba)</li>
        </ol>
        <p>Událost musí projít VŠEMI povolenými filtry, aby byla synchronizována.</p>
        
        <h4>Prevence duplicit</h4>
        <p>SyncMyDay zabraňuje duplicitním blokovacím událostem pomocí:</p>
        <ul>
            <li>Jedinečných identifikátorů propojujících zdrojové události s blokátory</li>
            <li>Hash-based detekce existujících blokátorů</li>
            <li>Čištění osiřelých blokátorů při smazání pravidel</li>
        </ul>
        
        <h4>Výkon</h4>
        <ul>
            <li><strong>Databáze:</strong> Indexováno podle uživatele, kalendáře a pravidla synchronizace pro rychlé vyhledávání</li>
            <li><strong>Caching:</strong> Tokeny připojení a metadata jsou uloženy v cache v Redis (pokud je k dispozici)</li>
            <li><strong>Fronty:</strong> Velké operace synchronizace jsou zpracovány v pozadí</li>
            <li><strong>Omezení rychlosti:</strong> API volání jsou throttlována pro respektování limitů poskytovatelů</li>
        </ul>
    </div>
</div>
</div>
@endsection


