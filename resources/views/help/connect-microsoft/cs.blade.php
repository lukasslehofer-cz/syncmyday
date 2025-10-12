@extends('layouts.public')

@section('title', 'Připojení Microsoft 365')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<div class="flex items-center mb-6">
    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center mr-4">
        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M11.4 24H0V12.6h11.4V24zM24 24H12.6V12.6H24V24zM11.4 11.4H0V0h11.4v11.4zm12.6 0H12.6V0H24v11.4z"/>
        </svg>
    </div>
    <div>
        <h1 class="!mb-0">Připojení Microsoft 365</h1>
        <p class="text-lg text-gray-600 !mb-0">Outlook, Office 365 a Exchange Online</p>
    </div>
</div>

<div class="p-6 bg-blue-50 border border-blue-200 rounded-xl mb-8">
    <div class="flex items-start">
        <svg class="w-6 h-6 text-blue-600 mt-1 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>
            <h3 class="text-lg font-semibold text-blue-900 mb-2">Co je zahrnuto</h3>
            <p class="text-blue-800 mb-2">Tento průvodce pokrývá všechny Microsoft kalendářové služby:</p>
            <ul class="text-blue-800 space-y-1 mb-0">
                <li><strong>Outlook.com</strong> - Osobní Microsoft účty (@outlook.com, @hotmail.com, @live.com)</li>
                <li><strong>Microsoft 365</strong> - Pracovní nebo školní účty</li>
                <li><strong>Office 365</strong> - Firemní předplatné</li>
                <li><strong>Exchange Online</strong> - Podnikové e-maily a kalendáře</li>
            </ul>
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
            <p>Z vašeho SyncMyDay dashboardu přejděte na <strong>Kalendáře</strong> v hlavním menu, nebo jděte přímo na <a href="{{ route('connections.index') }}">stránku Připojení kalendářů</a>.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Dashboard se zvýrazněným menu "Kalendáře"</p>
                <p class="text-sm">Navigační lišta zobrazující možnost Kalendáře</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 2 -->
    <div class="flex items-start">
        <span class="step-number">2</span>
        <div class="flex-1">
            <h3 class="!mt-0">Klikněte na "Připojit Microsoft 365"</h3>
            <p>Najděte a klikněte na tlačítko <strong>Microsoft 365</strong> s logem Microsoft.</p>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Možnosti poskytovatelů kalendářů s tlačítkem Microsoft 365</p>
                <p class="text-sm">Zobrazuje rozhraní pro připojení se zvýrazněnou možností Microsoft 365</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 3 -->
    <div class="flex items-start">
        <span class="step-number">3</span>
        <div class="flex-1">
            <h3 class="!mt-0">Přihlaste se pomocí Microsoft</h3>
            <p>Budete přesměrováni na bezpečnou přihlašovací stránku Microsoft. Zadejte svou Microsoft e-mailovou adresu:</p>
            <ul>
                <li><strong>Osobní:</strong> @outlook.com, @hotmail.com, @live.com</li>
                <li><strong>Pracovní/Školní:</strong> E-mail vaší organizace (např. vy@firma.cz)</li>
            </ul>
            
            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-yellow-900 mb-1">Pracovní/Školní účet?</p>
                        <p class="text-yellow-800 text-sm mb-0">Vaše organizace může potřebovat schválit SyncMyDay. Kontaktujte svého IT administrátora, pokud uvidíte zprávu o žádosti o schválení.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Přihlašovací stránka Microsoft</p>
                <p class="text-sm">Oficiální přihlašovací obrazovka Microsoft vyžadující e-mailovou adresu</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 4 -->
    <div class="flex items-start">
        <span class="step-number">4</span>
        <div class="flex-1">
            <h3 class="!mt-0">Zadejte své heslo</h3>
            <p>Po zadání e-mailu budete vyzváni k zadání hesla. Zadejte heslo svého Microsoft účtu.</p>
            
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <div>
                        <p class="text-blue-900 font-semibold mb-1">Vaše heslo je v bezpečí</p>
                        <p class="text-blue-800 text-sm mb-0">Zadáváte heslo přímo na webu Microsoft. SyncMyDay nikdy nevidí ani neukládá vaše heslo.</p>
                    </div>
                </div>
            </div>
            
            <p class="text-sm text-gray-600">Pokud máte povolenu vícefaktorovou autentizaci (MFA), budete muset schválit přihlášení na telefonu nebo v aplikaci autentizátoru.</p>
        </div>
    </div>
    
    <!-- Krok 5 -->
    <div class="flex items-start">
        <span class="step-number">5</span>
        <div class="flex-1">
            <h3 class="!mt-0">Udělte oprávnění</h3>
            <p>Microsoft zobrazí obrazovku s oprávněními a zeptá se, zda chcete povolit SyncMyDay přístup k vašemu kalendáři. Klikněte na <strong>Přijmout</strong> pro pokračování.</p>
            
            <p><strong>Jaká oprávnění SyncMyDay potřebuje?</strong></p>
            <ul>
                <li><strong>Číst vaše kalendáře:</strong> Pro detekci, kdy máte naplánované události</li>
                <li><strong>Vytvářet a upravovat kalendářové události:</strong> Pro vytváření blokujících událostí</li>
                <li><strong>Mazat kalendářové události:</strong> Pro odstranění blokujících událostí, když je to potřeba</li>
                <li><strong>Udržovat přístup k datům:</strong> Pro nepřetržitou synchronizaci</li>
            </ul>
            
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg mb-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-green-900 font-semibold mb-1">Soukromí na prvním místě</p>
                        <p class="text-green-800 text-sm mb-0">Čteme pouze načasování událostí (začátek/konec). Nikdy nepřistupujeme k názvům událostí, popisům, místům nebo informacím o účastnících.</p>
                    </div>
                </div>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Obrazovka souhlasu s oprávněními Microsoft</p>
                <p class="text-sm">Zobrazuje seznam požadovaných oprávnění s tlačítkem "Přijmout"</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 6 -->
    <div class="flex items-start">
        <span class="step-number">6</span>
        <div class="flex-1">
            <h3 class="!mt-0">Vyberte kalendáře k synchronizaci</h3>
            <p>Po udělení oprávnění se vrátíte do SyncMyDay, kde můžete vybrat, které Microsoft kalendáře chcete použít pro synchronizaci.</p>
            
            <p>Většina účtů bude mít alespoň:</p>
            <ul>
                <li><strong>Kalendář</strong> - Váš hlavní kalendář</li>
                <li><strong>Narozeniny</strong> - Narozeniny kontaktů (můžete přeskočit)</li>
            </ul>
            
            <p>Můžete také vidět:</p>
            <ul>
                <li>Sdílené týmové kalendáře</li>
                <li>Kalendáře zdrojů (jednací místnosti, vybavení)</li>
                <li>Kalendáře sdílené s vámi kolegy</li>
            </ul>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Rozhraní výběru kalendáře</p>
                <p class="text-sm">Zaškrtávací políčka zobrazující dostupné kalendáře z Microsoft účtu</p>
            </div>
        </div>
    </div>
    
    <!-- Krok 7 -->
    <div class="flex items-start">
        <span class="step-number">7</span>
        <div class="flex-1">
            <h3 class="!mt-0">Připojení dokončeno!</h3>
            <p>Váš Microsoft 365 kalendář je nyní připojen a připraven k použití. Uvidíte ho ve vašem seznamu Připojení kalendářů se stavem "Aktivní".</p>
            
            <div class="p-6 bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl">
                <h4 class="text-lg font-semibold text-purple-900 mb-2">✅ Vše je nastaveno!</h4>
                <ul class="text-purple-800 space-y-1 mb-0">
                    <li>Synchronizace v reálném čase je povolena prostřednictvím webhooků</li>
                    <li>Změny ve vašem kalendáři jsou detekovány během několika sekund</li>
                    <li>Připraveno vytvořit pravidla synchronizace a začít synchronizovat!</li>
                </ul>
            </div>
            
            <div class="img-placeholder">
                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="font-semibold">Screenshot: Úspěšně připojený Microsoft 365 kalendář</p>
                <p class="text-sm">Stránka připojení kalendářů zobrazující nový Microsoft kalendář se zeleným odznáčkem "Aktivní"</p>
            </div>
        </div>
    </div>
</div>

<h2>Časté problémy</h2>

<div class="space-y-4">
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            "Vaše organizace potřebuje schválit tuto aplikaci"
        </h3>
        <p><strong>Proč se to stává:</strong> Vaše IT oddělení omezilo, které aplikace mohou přistupovat k firemním datům.</p>
        <p><strong>Řešení:</strong></p>
        <ol>
            <li>Kontaktujte svého IT administrátora nebo helpdesk</li>
            <li>Požádejte je o schválení "SyncMyDay" v centru správy Microsoft 365</li>
            <li>Nebo požádejte o výjimku pro váš účet</li>
        </ol>
        <p class="text-sm text-gray-600 mb-0">To je běžné ve větších organizacích a je to osvědčený bezpečnostní postup.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Připojení zobrazuje stav "Chyba"
        </h3>
        <p><strong>Časté příčiny:</strong></p>
        <ul>
            <li>Vaše heslo bylo změněno</li>
            <li>Nastavení vícefaktorové autentizace se změnilo</li>
            <li>Organizace odvolala přístup</li>
        </ul>
        <p><strong>Řešení:</strong> Klikněte na tlačítko "Obnovit" pro opětovnou autentizaci, nebo odpojte a znovu připojte kalendář.</p>
    </div>
    
    <div class="border border-gray-200 rounded-lg p-6">
        <h3 class="!mt-0 flex items-center">
            <svg class="w-6 h-6 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Nevidím sdílený kalendář
        </h3>
        <p>Sdílené kalendáře by se měly objevit, pokud jsou přidány do vašeho Outlooku. Pokud chybí:</p>
        <ol>
            <li>Ujistěte se, že kalendář je viditelný v Outlook webu nebo aplikaci</li>
            <li>Odpojte a znovu připojte svůj Microsoft účet</li>
            <li>Ujistěte se, že máte alespoň oprávnění "Může zobrazit všechny detaily"</li>
        </ol>
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
        <p class="mb-0">Připojte osobní kalendář (Google, Apple) pro synchronizaci s vaším pracovním kalendářem.</p>
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
        <p class="mb-0">Nastavte svou první synchronizaci mezi kalendáři.</p>
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
                <p class="text-sm text-gray-600 !mb-0">Pro vývojáře a IT administrátory</p>
            </div>
        </div>
        <svg class="w-6 h-6 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>
    
    <div x-show="open" x-collapse class="mt-4 p-6 bg-white border border-gray-200 rounded-xl">
        <h4>Microsoft Graph API</h4>
        <p>SyncMyDay používá Microsoft Graph API s těmito oprávněními:</p>
        <ul>
            <li><code>Calendars.ReadWrite</code> - Čtení a zápis kalendářových událostí</li>
            <li><code>offline_access</code> - Udržování přístupu, když je uživatel offline</li>
        </ul>
        
        <h4>OAuth 2.0 autentizace</h4>
        <p>Používáme standardní OAuth 2.0 authorization code flow:</p>
        <ul>
            <li>Podporuje osobní Microsoft účty i Azure AD účty</li>
            <li>Tokeny jsou automaticky obnovovány každých 60 minut</li>
            <li>Obnovovací tokeny jsou platné 90 dní (automaticky obnovovány)</li>
        </ul>
        
        <h4>Synchronizace v reálném čase</h4>
        <p>Microsoft Graph change notifications (webhooky) umožňují okamžitou synchronizaci:</p>
        <ul>
            <li>Předplatná jsou vytvořena pro každý připojený kalendář</li>
            <li>Oznámení jsou přijímána během 2-3 minut po změnách</li>
            <li>Předplatná jsou automaticky obnovována každé 3 dny</li>
            <li>Záložní polling probíhá každých 15 minut, pokud webhooky selžou</li>
        </ul>
        
        <h4>Podnikový souhlas administrátora</h4>
        <p>IT administrátoři mohou předem schválit SyncMyDay pro všechny uživatele:</p>
        <ol>
            <li>Přejděte na Portál Azure AD → Podnikové aplikace</li>
            <li>Vyhledejte "SyncMyDay" nebo přidejte přes App ID</li>
            <li>Udělte souhlas administrátora pro organizaci</li>
            <li>Uživatelé se pak mohou připojit bez výzev ke schválení</li>
        </ol>
        
        <h4>API omezení</h4>
        <p>Microsoft Graph má následující limity:</p>
        <ul>
            <li><strong>Na aplikaci:</strong> 10,000 požadavků za 10 minut</li>
            <li><strong>Na uživatele:</strong> 2,000 požadavků za sekundu</li>
        </ul>
        <p>Architektura SyncMyDay založená na webhookách minimalizuje API volání a zůstává dobře v rámci limitů.</p>
        
        <h4>Rezidence dat</h4>
        <p>Vaše kalendářová data zůstávají v datových centrech Microsoft. SyncMyDay ukládá pouze:</p>
        <ul>
            <li>ID kalendářů a názvy</li>
            <li>Časy začátku/konce událostí (žádné názvy nebo popisy)</li>
            <li>Šifrované OAuth tokeny</li>
        </ul>
    </div>
</div>
</div>
@endsection

