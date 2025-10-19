@extends('layouts.public')

@section('title', 'Často kladené otázky')

@section('sidebar')
    @include('help.partials.sidebar')
@endsection

@section('content')
<div class="help-content">
<h1>Často kladené otázky</h1>

<p class="text-xl text-gray-600 mb-8">Rychlé odpovědi na běžné otázky o SyncMyDay.</p>

<div class="space-y-6" x-data="{ open: null }">
    <!-- Bezpečnost & Soukromí -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">🔒 Bezpečnost & Soukromí</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-1' ? open = null : open = 'security-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Jsou moje kalendářová data v bezpečí?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-1'" x-collapse class="px-6 pb-4">
                    <p><strong>Ano, rozhodně.</strong> Bezpečnost bereme vážně:</p>
                    <ul>
                        <li><strong>Minimální ukládání dat:</strong> Ukládáme pouze začátek/konec události a stav (zaneprázdněn/volný). Nikdy neukládáme názvy událostí, popisy nebo účastníky.</li>
                        <li><strong>Šifrování v databázi:</strong> Všechna data jsou v databázi šifrována.</li>
                        <li><strong>Šifrování při přenosu:</strong> Všechna připojení používají HTTPS/TLS.</li>
                        <li><strong>OAuth autentizace:</strong> Pro Google a Microsoft používáme standardní OAuth, což znamená, že nikdy nevidíme vaše heslo.</li>
                        <li><strong>Přístupové tokeny jsou šifrovány:</strong> Jakékoliv přihlašovací údaje jsou šifrovány silným šifrováním.</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-2' ? open = null : open = 'security-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Jaké informace vlastně ukládáte?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-2'" x-collapse class="px-6 pb-4">
                    <p>Pro každou synchronizovanou událost ukládáme pouze:</p>
                    <ul>
                        <li>Datum a čas začátku</li>
                        <li>Datum a čas konce</li>
                        <li>Stav (zaneprázdněn/volný/předběžně)</li>
                        <li>Ze kterého kalendáře pochází a ve kterých kalendářích jsme vytvořili blokování</li>
                        <li>Jedinečné ID pro sledování události</li>
                    </ul>
                    <p><strong>Nikdy neukládáme:</strong> Názvy událostí, popisy, místa, účastníky, poznámky ani žádné další detaily o vašich událostech.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-3' ? open = null : open = 'security-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Můžete vidět moje kalendářové události?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-3'" x-collapse class="px-6 pb-4">
                    <p><strong>Ne.</strong> Záměrně nikdy neobdržíme ani neukládáme názvy nebo detaily vašich událostí. Při synchronizaci čteme pouze časové informace (kdy událost začíná a končí) a vytváříme jednoduché "Zaneprázdněn" blokující události ve vašich dalších kalendářích.</p>
                    <p>Vaše osobní kalendářové události zůstávají soukromé ve vaší kalendářové službě (Google, Microsoft atd.).</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'security-4' ? open = null : open = 'security-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Jak odvolám přístup?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'security-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'security-4'" x-collapse class="px-6 pb-4">
                    <p>Můžete kdykoli odpojit jakýkoliv kalendář ze stránky <strong>Připojení kalendářů</strong>. Tímto:</p>
                    <ul>
                        <li>Odstraníte všechny blokující události vytvořené SyncMyDay v daném kalendáři</li>
                        <li>Smažete všechna pravidla synchronizace používající tento kalendář</li>
                        <li>Odvoláte náš přístup k tomuto kalendáři</li>
                    </ul>
                    <p>Přístup můžete také odvolat přímo u vašeho poskytovatele kalendáře (Google, Microsoft atd.) v jejich nastavení bezpečnosti.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Zkušební období & Platby -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">💳 Zkušební období & Platby</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-1' ? open = null : open = 'payment-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Jak funguje zkušební období zdarma?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-1'" x-collapse class="px-6 pb-4">
                    <p>Dostanete <strong>14 dní plného přístupu zdarma</strong> bez nutnosti zadávat platební kartu. Můžete připojit neomezený počet kalendářů a vytvořit libovolný počet pravidel synchronizace.</p>
                    <p>Po skončení zkušebního období:</p>
                    <ul>
                        <li><strong>Přidáte platební metodu:</strong> SyncMyDay bude pokračovat v plném rozsahu.</li>
                        <li><strong>Nepřidáte platební metodu:</strong> Účet se zamkne a již se neprovedou žádné synchronizace.</li>
                    </ul>
                    <p>Žádné automatické poplatky. Žádné překvapení.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-2' ? open = null : open = 'payment-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Budu automaticky účtován po zkušebním období?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-2'" x-collapse class="px-6 pb-4">
                    <p><strong>Ne!</strong> Nevyžadujeme platební kartu pro zkušební období, takže vás nemůžeme účtovat. Máte 14 dní na vyzkoušení všech funkcí. A pokud si během tohoto období připojíte vaši platební kartu, stále dokončíme celých 14 dní zdarma a první platba proběhne až poté.</p>                   
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-3' ? open = null : open = 'payment-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Můžu službu kdykoli zrušit?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-3'" x-collapse class="px-6 pb-4">
                    <p><strong>Ano.</strong> Můžete kdykoli zrušit svůj plán ze stránky Platby. Vaše předplatné zůstane aktivní do konce fakturačního období, poté bude váš účet pozastaven a již se neprovedou žádné synchronizace.</p>
                    <p>Žádné další poplatky. Žádné otázky.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'payment-4' ? open = null : open = 'payment-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Jaké platební metody přijímáte?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'payment-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'payment-4'" x-collapse class="px-6 pb-4">
                    <p>Přijímáme všechny hlavní platební karty (Visa, Mastercard, American Express) prostřednictvím Stripe, což je přední platební procesor.</p>
                    <p>Vaše platební údaje jsou zpracovávány bezpečně přes službu Stripe a nikdy se nedostanou na naše servery.</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jak to funguje -->
    <div class="border-b border-gray-200 pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">⚙️ Jak to funguje</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-1' ? open = null : open = 'how-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Co je to "blocker"?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-1'" x-collapse class="px-6 pb-4">
                    <p>Blocker je jednoduchá událost, kterou vytvoříme ve vašich cílových kalendářích, aby ukázala, že jste zaneprázdněni. Obvykle se zobrazuje jako:</p>
                    <ul>
                        <li><strong>Název:</strong> "Zaneprázdněn" (nebo vlastní text, který si nastavíte)</li>
                        <li><strong>Čas:</strong> Přesně stejný čas jako původní událost</li>
                        <li><strong>Stav:</strong> Označeno jako "Zaneprázdněn" aby vás nikdo nepřerušoval</li>
                    </ul>
                    <p>Je to jako rezervace místa ve vašem kalendáři bez odhalení jakýchkoliv detailů o skutečné události.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-2' ? open = null : open = 'how-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Jak rychle se události synchronizují?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-2'" x-collapse class="px-6 pb-4">
                    <p>Rychlost synchronizace závisí na typu kalendáře:</p>
                    <ul>
                        <li><strong>Google Calendar:</strong> Okamžitě (cca do 1 minuty)</li>
                        <li><strong>Microsoft 365:</strong> Okamžitě (cca do 1 minuty)</li>
                        <li><strong>Apple iCloud:</strong> Každých ~5 minut</li>
                        <li><strong>CalDAV:</strong> Každých ~5 minut</li>
                        <li><strong>E-mailová synchronizace:</strong> Okamžitě při obdržení e-mailu</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-3' ? open = null : open = 'how-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Co se stane, když smažu událost?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-3'" x-collapse class="px-6 pb-4">
                    <p>Když smažete událost ze zdrojového kalendáře, automaticky smažeme odpovídající blokující událost ze všech cílových kalendářů.</p>
                    <p>Pokud smažete blokující událost přímo, vytvoříme ji znovu při další synchronizaci (protože původní událost stále existuje). Chcete-li ji trvale odstranit, buď:</p>
                    <ul>
                        <li>Smažte původní událost, nebo</li>
                        <li>Upravte své pravidlo synchronizace tak, aby vyloučilo tento typ události</li>
                    </ul>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'how-4' ? open = null : open = 'how-4'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Můžu synchronizovat víc než 2 kalendáře?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'how-4' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'how-4'" x-collapse class="px-6 pb-4">
                    <p><strong>Ano!</strong> Můžete připojit neomezený počet kalendářů.</p>
                    <p>Také můžete vytvořit více pravidel synchronizace s různými směry:</p>
                    <ul>
                        <li>Osobní → Pracovní</li>
                        <li>Pracovní → Osobní</li>
                        <li>Osobní → Rodinný</li>
                        <li>Atd...</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Řešení problémů -->
    <div class="pb-6">
        <h2 class="!mt-0 !border-t-0 !pt-0">🔧 Řešení problémů</h2>
        
        <div class="space-y-4">
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-1' ? open = null : open = 'trouble-1'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Moje události se nesynchronizují. Co mám dělat?</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-1' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-1'" x-collapse class="px-6 pb-4">
                    <p>Zkuste tyto kroky řešení problémů:</p>
                    <ol>
                        <li><strong>Zkontrolujte pravidlo synchronizace:</strong> Ujistěte se, že máte aktivní pravidlo synchronizace mezi správnými kalendáři</li>
                        <li><strong>Zkontrolujte filtry:</strong> Událost může být vyfiltrována (např. celodenní událost s povoleným "ignorovat celodenní")</li>
                        <li><strong>Zkontrolujte stav události:</strong> Pokud máte filtr "pouze zaneprázdněn", předběžné události se nebudou synchronizovat</li>
                        <li><strong>Počkejte pár minut:</strong> CalDAV kalendáře se aktualizují každých 15 minut</li>
                        <li><strong>Obnovte připojení:</strong> Použijte tlačítko "Obnovit" na stránce Připojení</li>
                    </ol>
                    <p>Pokud problémy přetrvávají, <a href="{{ route('contact') }}">kontaktujte náš tým podpory</a></p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-2' ? open = null : open = 'trouble-2'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Vidím chybovou zprávu "Vypršela platnost tokenu"</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-2' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-2'" x-collapse class="px-6 pb-4">
                    <p>To znamená, že váš přístupový token kalendáře vypršel nebo byl odvolán. Obvykle se to stává, když:</p>
                    <ul>
                        <li>Změnili jste heslo ve vašem kalendáři</li>
                        <li>Odvolali jste přístup v nastavení bezpečnosti vašeho kalendáře</li>
                        <li>U Apple: Heslo pro aplikaci přestalo fungovat</li>
                    </ul>
                    <p><strong>Řešení:</strong> Přejděte na stránku Připojení kalendářů a znovu připojte dotčený kalendář. Všechna vaše pravidla synchronizace zůstanou zachována.</p>
                </div>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button @click="open === 'trouble-3' ? open = null : open = 'trouble-3'" class="w-full px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition flex items-center justify-between">
                    <span>Vidím duplicitní události</span>
                    <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': open === 'trouble-3' }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === 'trouble-3'" x-collapse class="px-6 pb-4">
                    <p>Pokud vidíte duplicitní blokující události, obvykle je to způsobeno:</p>
                    <ul>
                        <li><strong>Více pravidel synchronizace:</strong> Máte 2+ pravidla, která vytváří blokování ve stejném kalendáři</li>
                        <li><strong>Cirkulární synchronizace:</strong> Kalendář A → B a B → A zároveň</li>
                    </ul>
                    <p><strong>Řešení:</strong> Zkontrolujte svá pravidla synchronizace a ujistěte se, že nemáte konfliktní nebo cirkulární pravidla. Každý kalendář by měl být buď zdrojem nebo cílem, ne obojím ve stejném páru.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-12 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl">
    <h3 class="text-lg font-semibold text-gray-900 mb-2">Máte další otázky?</h3>
    <p class="text-gray-700 mb-4">Jsme tu, abychom vám pomohli! Náš tým podpory obvykle odpovídá do 24 hodin.</p>
    <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition no-underline" style="text-decoration: none !important;">
        <svg class="w-5 h-5 mr-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <span class="text-white">Kontaktujte podporu</span>
    </a>
</div>
</div>
@endsection
