@extends('layouts.public')

@section('title', 'Připojení Google Calendar')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-blue-500 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Připojení Google Calendar</h1>
        <p class="text-lg text-gray-600 !mb-0">Rychlé a bezpečné OAuth připojení</p>
    </div>
</div>

<div class="p-6 bg-green-50 border border-green-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-green-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-green-900 mb-2">Proč Google Calendar?</h3>
            <p class="text-green-800 mb-0"><strong>Google Calendar je nejjednodušší kalendář na připojení!</strong> Používá bezpečnou OAuth autentizaci, takže s námi nikdy nesdílíte své heslo. Nastavení trvá méně než 2 minuty a synchronizace je okamžitá díky webhookům v reálném čase.</p>
        </div>
    </div>
</div>

<h2>Průvodce krok za krokem</h2>

<div class="space-y-8">
    <!-- Krok 1 -->
    <div class="flex items-start">
        <span class="step-number">1</span>
        <div class="flex-1">
            <h3 class="!mt-0">Přejděte na Připojení kalendářů</h3>
            <p>Z vašeho SyncMyDay dashboardu klikněte na <strong>Kalendáře</strong> v hlavním menu, nebo přejděte přímo na <a href="{{ route('connections.index') }}">stránku Připojení kalendářů</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard se zvýrazněnou položkou menu "Kalendáře"</p>
                <p class="text-sm">Zobrazuje hlavní navigaci s jasně viditelným odkazem Kalendáře</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Klikněte na "Připojit Google Calendar"</h3>
            <p>Na stránce Připojení kalendářů najděte tlačítko <strong>Google Calendar</strong> s logem Google a klikněte na něj.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Stránka Připojení kalendářů zobrazující tlačítko "Připojit Google Calendar"</p>
                <p class="text-sm">Zobrazuje mřížku poskytovatelů kalendářů s prominentně zobrazeným Google Calendar</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Přihlaste se pomocí Google</h3>
            <p>Budete přesměrováni na bezpečnou přihlašovací stránku Google. Přihlaste se pomocí Google účtu, který má kalendář, který chcete připojit.</p>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Více Google účtů?</p>
                        <p class="text-yellow-800 text-sm mb-0">Ujistěte se, že se přihlašujete správným účtem. Více Google účtů můžete připojit později opakováním tohoto procesu.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Přihlašovací stránka Google</p>
                <p class="text-sm">Zobrazuje oficiální přihlašovací obrazovku Google vyžadující e-mail/heslo</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Udělte oprávnění</h3>
            <p>Google vás požádá o povolení pro SyncMyDay k přístupu k vašemu kalendáři. Zkontrolujte oprávnění a klikněte na <strong>Povolit</strong>.</p>
            
            <p><strong>Jaká oprávnění SyncMyDay potřebuje?</strong></p>
            <ul>
                <li><strong>Zobrazit události ve všech vašich kalendářích:</strong> Pro čtení časů událostí (ne názvů/detailů)</li>
                <li><strong>Přidávat a upravovat události:</strong> Pro vytváření blokujících událostí</li>
                <li><strong>Mazat události:</strong> Pro odstranění blokujících událostí, když jsou zdrojové události smazány</li>
            </ul>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Nebojte se o soukromí!</p>
                        <p class="text-blue-800 text-sm mb-0">I když požadujeme oprávnění k "zobrazení událostí", čteme pouze časy začátku/konce a stav. Nikdy nepřistupujeme ani neukládáme názvy událostí, popisy nebo další detaily.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Obrazovka souhlasu Google OAuth</p>
                <p class="text-sm">Zobrazuje dialog s požadavkem na oprávnění s tlačítkem "Povolit"</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte, které kalendáře synchronizovat</h3>
            <p>Po udělení oprávnění budete přesměrováni zpět do SyncMyDay. Uvidíte seznam všech kalendářů ve vašem Google účtu. Vyberte, které chcete zpřístupnit pro synchronizaci.</p>
            
            <div class="p-4 bg-purple-50 border border-purple-200 rounded-lg mb-4">
                <p class="text-purple-900 mb-1"><strong>Pro tip:</strong> Můžete vybrat více kalendářů ze stejného Google účtu! To je užitečné, pokud máte oddělené kalendáře pro:</p>
                <ul class="text-purple-800 text-sm mb-0">
                    <li>Osobní události</li>
                    <li>Rodinné události</li>
                    <li>Sdílené týmové kalendáře</li>
                    <li>Projektově specifické kalendáře</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dialog výběru kalendáře</p>
                <p class="text-sm">Zobrazuje zaškrtávací políčka pro každý kalendář dostupný v Google účtu</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Hotovo! Kalendář připojen</h3>
            <p>Váš Google Calendar je nyní připojen a objeví se ve vašem seznamu připojení kalendářů se zeleným stavovým odznáčkem "Aktivní".</p>
            
            <div class="p-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl">
                <h4 class="text-lg font-semibold text-green-900 mb-2">✅ Co se stane dál?</h4>
                <ul class="text-green-800 space-y-1 mb-0">
                    <li>Váš kalendář je připraven k použití v pravidlech synchronizace</li>
                    <li>SyncMyDay bude dostávat oznámení v reálném čase, když se události změní</li>
                    <li>Nyní můžete vytvořit pravidla synchronizace a začít synchronizovat!</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Seznam připojených kalendářů zobrazující Google Calendar se stavem "Aktivní"</p>
                <p class="text-sm">Zobrazuje stránku připojení kalendářů s nově připojeným Google Calendar</p>
            </div>
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
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-indigo-700">Připojte další kalendář</h3>
        </div>
        <p class="mb-0">Potřebujete alespoň 2 kalendáře pro vytvoření pravidla synchronizace. Připojte pracovní kalendář, osobní kalendář nebo jinou službu.</p>
    </a>
    
    <a href="{{ route('help.sync-rules') }}" class="block p-6 border-2 border-purple-200 bg-purple-50 rounded-xl hover:shadow-lg transition group">
        <div class="flex items-center mb-3">
            <div class="w-12 h-12 rounded-lg gradient-bg flex items-center justify-center mr-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <h3 class="!mb-0 !mt-0 text-xl group-hover:text-purple-700">Vytvořte své první pravidlo synchronizace</h3>
        </div>
        <p class="mb-0">Naučte se nastavit synchronizaci mezi vašimi kalendáři s filtry a vlastními možnostmi.</p>
    </a>
</div>

<!-- Technické detaily (Rozbalovací) -->
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
        <h4>OAuth 2.0 Flow</h4>
        <p>SyncMyDay používá Google OAuth 2.0 autentizaci s následujícími scope:</p>
        <ul>
            <li><code>https://www.googleapis.com/auth/calendar.readonly</code> - Čtení kalendářových dat</li>
            <li><code>https://www.googleapis.com/auth/calendar.events</code> - Vytváření/úprava/mazání událostí</li>
        </ul>
        
        <h4>Synchronizace v reálném čase</h4>
        <p>Používáme Google Calendar Push Notifications (webhooky) pro okamžité přijímání aktualizací:</p>
        <ul>
            <li>Pro každý připojený kalendář je registrován webhook</li>
            <li>Google odesílá oznámení během několika sekund po jakýchkoliv změnách událostí</li>
            <li>Webhooky jsou automaticky obnoveny každých 7 dní</li>
            <li>Pokud doručení webhooku selže, přepneme na polling každých 15 minut</li>
        </ul>
        
        <h4>API kvóty</h4>
        <p>Google Calendar API má následující kvóty:</p>
        <ul>
            <li><strong>Dotazů za den:</strong> 1,000,000 (sdíleno mezi všemi uživateli SyncMyDay)</li>
            <li><strong>Dotazů za 100 sekund na uživatele:</strong> 500</li>
        </ul>
        <p>Architektura SyncMyDay je optimalizována, aby zůstala dobře v rámci těchto limitů pro běžné použití.</p>
        
        <h4>Ukládání tokenů</h4>
        <p>OAuth přístupové tokeny a obnovovací tokeny jsou:</p>
        <ul>
            <li>Šifrovány v klidu pomocí AES-256</li>
            <li>Bezpečně uloženy v naší databázi</li>
            <li>Automaticky obnovovány, když vyprší (každých 60 minut)</li>
            <li>Okamžitě smazány, když odpojíte kalendář</li>
        </ul>
        
        <h4>Odvolání přístupu</h4>
        <p>Přístup SyncMyDay můžete kdykoli odvolat:</p>
        <ul>
            <li><strong>Ze SyncMyDay:</strong> Klikněte na "Odpojit" na stránce Připojení kalendářů</li>
            <li><strong>Z Google:</strong> Navštivte <a href="https://myaccount.google.com/permissions" target="_blank">myaccount.google.com/permissions</a> a odeberte SyncMyDay</li>
        </ul>
    </div>
</div>
</div>
@endsection

