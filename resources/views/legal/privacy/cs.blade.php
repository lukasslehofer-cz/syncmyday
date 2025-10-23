<h1>Zásady ochrany osobních údajů</h1>
<p><strong>Provozovatel:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Praha 5, DIČ: CZ7912150191</p>
<p><strong>Datum aktualizace:</strong> {{ date('Y-m-d') }}</p>

<h2>Rozsah a účel</h2>
<p>Tento dokument vysvětluje, jak zpracováváme osobní údaje v souvislosti s poskytováním služby {{ config('app.name') }}. Postupujeme dle GDPR a českých právních předpisů.</p>

<h2>Kategorie zpracovávaných údajů</h2>
<ul>
    <li>Identifikační a kontaktní údaje (např. e‑mail, jméno)</li>
    <li>Údaje o účtu a autentizaci (OAuth identifikátory, tokeny)</li>
    <li>Fakturační údaje v případě nákupu Pro (přes Stripe)</li>
    <li>Technické a provozní údaje (logy, metadata zařízení/prohlížeče)</li>
    <li>Metadata synchronizace kalendářů (časy začátku/konce, stav). Neuložujeme názvy, popisy ani účastníky událostí.</li>
    <li>Metadata pro zpracování e‑mailů u e‑mailových kalendářů</li>
    <li>Komunikace s podporou</li>
    <li>Identifikátory cookies, je‑li to relevantní</li>
    <li>Minimální IP adresy pro bezpečnost a prevenci zneužití</li>
    <li>Identifikátory platebních transakcí přes poskytovatele plateb</li>
    <li>Identifikátory webhook odběrů</li>
    <li>Šifrovací klíče a identifikátory nutné pro provoz služby</li>
    <li>Údaje nezbytné pro splnění právních povinností</li>
    <li>Jakékoli další údaje, které nám dobrovolně poskytnete</li>
    <li>Zvláštní kategorie osobních údajů vědomě nezpracováváme</li>
    
</ul>

<h2>Zpracování dat z Google Calendar</h2>
<p>Používání a přenos informací přijatých z Google Calendar služby {{ config('app.name') }} dodržuje <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">zásady Google pro uživatelská data API služeb</a>, včetně požadavků na omezené použití.</p>

<h3>Data získávaná z Google</h3>
<p>Při připojení vašeho Google Calendar žádáme o následující oprávnění:</p>
<ul>
    <li><strong>https://www.googleapis.com/auth/calendar</strong> — Plný přístup k vašim kalendářům, umožňující nám číst, vytvářet a upravovat metadata kalendářů</li>
    <li><strong>https://www.googleapis.com/auth/calendar.events</strong> — Přístup k událostem v kalendáři, umožňující nám číst, vytvářet, upravovat a mazat události</li>
</ul>
<p>Z vašeho Google Calendar přistupujeme k následujícím datům:</p>
<ul>
    <li>Metadata kalendářů (názvy kalendářů, ID, časové pásma, barvy)</li>
    <li>Podrobnosti událostí (názvy, popisy, časy začátku/konce, místa, účastníci, pravidla opakování, připomínky, stav)</li>
    <li>Identifikátory událostí a časové značky změn pro sledování synchronizace</li>
</ul>

<h3>Jak používáme data z Google Calendar</h3>
<p>Data z Google Calendar používáme výhradně k poskytování základní služby synchronizace kalendářů:</p>
<ul>
    <li>Synchronizace událostí mezi vaším Google Calendar a ostatními připojenými kalendáři (Microsoft Outlook, CalDAV nebo e‑mailovými kalendáři)</li>
    <li>Vytváření, aktualizace a mazání událostí napříč vašimi připojenými kalendáři podle vašich pravidel synchronizace</li>
    <li>Udržování stavu synchronizace pro prevenci duplikátů a zajištění správné obousměrné synchronizace</li>
    <li>Zobrazení vašich kalendářů a událostí v našem webovém rozhraní pro účely konfigurace a správy</li>
</ul>
<p><strong>Data z Google Calendar nepoužíváme k žádným jiným účelům, včetně reklamy, trénování AI nebo analytiky nad rámec toho, co je striktně nutné pro synchronizaci.</strong></p>

<h3>Sdílení dat z Google Calendar</h3>
<p>Vaše data z Google Calendar neprodáváme, nepronajímáme ani nesdílíme s třetími stranami pro jejich vlastní účely. Vaše kalendářová data jsou sdílena pouze v těchto omezených případech:</p>
<ul>
    <li><strong>S ostatními kalendářovými službami, které připojíte:</strong> Když nastavíte pravidlo synchronizace, data událostí jsou přenášena do cílové kalendářové služby (např. Microsoft Outlook, CalDAV server) pro vytvoření nebo aktualizaci synchronizovaných událostí</li>
    <li><strong>S technickými poskytovateli služeb:</strong> Používáme zabezpečenou hostingovou infrastrukturu pro ukládání a zpracování vašich dat. Tito poskytovatelé jednají jako zpracovatelé dat na základě přísných smluvních závazků a nemají nezávislý přístup k obsahu vašeho kalendáře</li>
    <li><strong>Dle požadavků zákona:</strong> Data můžeme sdělit v případě zákonné povinnosti (např. soudní příkaz), ale pouze v minimálně nutném rozsahu</li>
</ul>
<p>Sdílení dat mezi vašimi kalendáři probíhá pouze tehdy, když výslovně nakonfigurujete pravidla synchronizace. Máte plnou kontrolu nad tím, které kalendáře se synchronizují a která data mezi nimi proudí.</p>

<h3>Ukládání a ochrana dat z Google Calendar</h3>
<p>Implementujeme komplexní bezpečnostní opatření pro ochranu vašich dat z Google Calendar:</p>
<ul>
    <li><strong>Šifrování:</strong> Všechna data jsou šifrována při přenosu pomocí TLS/SSL a v klidu v naší databázi pomocí AES-256 šifrování</li>
    <li><strong>Přístupové tokeny:</strong> Google OAuth tokeny jsou šifrovány samostatně s dodatečným šifrováním klíčů pro prevenci neoprávněného přístupu</li>
    <li><strong>Řízení přístupu:</strong> Přísné kontroly přístupu zajišťují, že pouze autorizované systémové komponenty mohou přistupovat k vašim kalendářovým datům</li>
    <li><strong>Princip nejmenších oprávnění:</strong> Naše systémy žádají pouze minimálně nutná oprávnění a přístup</li>
    <li><strong>Pravidelné bezpečnostní aktualizace:</strong> Udržujeme aktuální bezpečnostní záplaty a monitoring</li>
    <li><strong>Zabezpečená infrastruktura:</strong> Data jsou uložena v profesionálně spravovaných datových centrech s fyzickými i digitálními bezpečnostními opatřeními</li>
</ul>
<p>Vaše data z Google Calendar jsou uložena pouze po dobu nezbytnou pro poskytování služby synchronizace. Synchronizovaná metadata událostí jsou uložena v naší databázi pro sledování stavu synchronizace, ale obsah událostí není trvale ukládán nad rámec toho, co je potřeba pro aktivní synchronizaci.</p>

<h3>Uchovávání a mazání dat z Google Calendar</h3>
<p>Máte plnou kontrolu nad svými daty z Google Calendar:</p>
<ul>
    <li><strong>Odpojení kalendáře:</strong> Svůj Google Calendar můžete kdykoli odpojit z nastavení účtu. Po odpojení přestaneme přistupovat k vašemu Google Calendar a smažeme související OAuth tokeny a stav synchronizace do 30 dnů</li>
    <li><strong>Smazání účtu:</strong> Smazání vašeho účtu {{ config('app.name') }} okamžitě zastaví veškerou synchronizaci kalendářů. Všechna vaše kalendářová data, pravidla synchronizace a OAuth tokeny jsou trvale smazány do 30 dnů</li>
    <li><strong>Odvolání přístupu:</strong> Přístup aplikace {{ config('app.name') }} k vašemu Google Calendar můžete kdykoli odvolat na stránce <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">oprávnění Google účtu</a></li>
    <li><strong>Uchovávání dat:</strong> Data z Google Calendar uchováváme pouze po dobu, kdy je váš kalendář připojen a aktivní. Metadata událostí potřebná pro synchronizaci jsou uchovávána po dobu trvání aktivního synchronizačního vztahu</li>
    <li><strong>Zákonné uchovávání:</strong> Některé minimální technické logy mohou být uchovávány pro oprávněné zájmy (bezpečnost, prevence podvodů) až 90 dnů, ale tyto neobsahují obsah kalendářových událostí</li>
</ul>

<h2>Zpracování dat z Microsoft Outlook/365 kalendáře</h2>
<p>Používání Microsoft Graph API službou {{ config('app.name') }} je v souladu s <a href="https://learn.microsoft.com/en-us/legal/microsoft-apis/terms-of-use" target="_blank" rel="noopener">podmínkami používání API Microsoftu</a> a respektuje soukromí uživatelských dat.</p>

<h3>Data získávaná z Microsoftu</h3>
<p>Při připojení vašeho Microsoft Outlook nebo Microsoft 365 kalendáře žádáme o následující oprávnění:</p>
<ul>
    <li><strong>Calendars.Read</strong> — Přístup pro čtení k vašim kalendářům a událostem</li>
    <li><strong>Calendars.ReadWrite</strong> — Plný přístup k čtení, vytváření, úpravám a mazání událostí v kalendáři</li>
</ul>
<p>Z vašeho Microsoft kalendáře přistupujeme k následujícím datům:</p>
<ul>
    <li>Metadata kalendářů (názvy kalendářů, ID, časová pásma, barvy)</li>
    <li>Podrobnosti událostí (názvy, popisy, časy začátku/konce, místa, účastníci, pravidla opakování, připomínky, stav, citlivost)</li>
    <li>Identifikátory událostí a časové značky změn pro sledování synchronizace</li>
</ul>

<h3>Jak používáme data z Microsoft kalendáře</h3>
<p>Data z Microsoft kalendáře používáme výhradně pro synchronizaci kalendářů:</p>
<ul>
    <li>Synchronizace událostí mezi vaším Microsoft kalendářem a ostatními připojenými kalendáři (Google, CalDAV nebo e‑mailovými kalendáři)</li>
    <li>Vytváření, aktualizace a mazání událostí podle vašich pravidel synchronizace</li>
    <li>Udržování stavu synchronizace pro prevenci duplikátů</li>
    <li>Zobrazení vašich kalendářů a událostí v našem rozhraní pro správu</li>
</ul>
<p><strong>Data z Microsoft kalendáře nepoužíváme k reklamě, marketingu, trénování AI ani k žádnému jinému účelu než k synchronizaci kalendářů.</strong></p>

<h3>Sdílení dat z Microsoft kalendáře</h3>
<p>Vaše data z Microsoft kalendáře jsou sdílena pouze tehdy, když výslovně nakonfigurujete pravidla synchronizace s ostatními kalendářovými službami, které připojíte. Vaše data neprodáváme ani nesdílíme s třetími stranami pro jejich vlastní účely. Techničtí poskytovatelé služeb mají přístup pouze na základě přísných zpracovatelských smluv.</p>

<h3>Ukládání a ochrana dat z Microsoft kalendáře</h3>
<p>Microsoft OAuth tokeny a data kalendáře jsou šifrovány pomocí AES-256 šifrování v klidu a TLS/SSL při přenosu. Kontroly přístupu zajišťují, že pouze autorizované systémové komponenty mohou zpracovávat vaše data.</p>

<h3>Uchovávání a mazání dat z Microsoft kalendáře</h3>
<p>Svůj Microsoft kalendář můžete kdykoli odpojit, smazat účet nebo odvolat přístup prostřednictvím <a href="https://account.microsoft.com/privacy/app-access" target="_blank" rel="noopener">oprávnění aplikací v účtu Microsoft</a>. Po odpojení nebo smazání účtu jsou všechna související data smazána do 30 dnů.</p>

<h2>Zpracování dat z Apple iCloud a CalDAV kalendářů</h2>
<p>{{ config('app.name') }} podporuje Apple iCloud kalendáře a další kalendářové služby založené na CalDAV. Respektujeme soukromí uživatelů v souladu s průmyslovými standardy a GDPR.</p>

<h3>Data získávaná z CalDAV služeb</h3>
<p>Když připojíte CalDAV kalendář (včetně Apple iCloud), poskytnete přihlašovací údaje k serveru, které nám umožňují přístup k:</p>
<ul>
    <li>Metadata kalendářů (názvy kalendářů, ID, časová pásma, barvy)</li>
    <li>Podrobnosti událostí (názvy, popisy, časy začátku/konce, místa, účastníci, pravidla opakování, alarmy, stav)</li>
    <li>Identifikátory událostí (UID) a časové značky změn pro synchronizaci</li>
</ul>

<h3>Jak používáme data z CalDAV kalendářů</h3>
<p>Data z CalDAV kalendářů používáme výhradně pro synchronizaci s ostatními kalendáři, které připojíte. Data jsou zpracovávána pouze pro:</p>
<ul>
    <li>Obousměrnou synchronizaci událostí mezi vaším CalDAV kalendářem a ostatními službami</li>
    <li>Vytváření, aktualizaci a mazání událostí podle vašich pravidel synchronizace</li>
    <li>Sledování stavu synchronizace pro prevenci duplikátů</li>
    <li>Zobrazení kalendářů a událostí v našem rozhraní</li>
</ul>
<p><strong>Data z CalDAV nejsou nikdy používána k reklamě, analytice ani k žádnému jinému účelu než k synchronizaci.</strong></p>

<h3>Sdílení dat z CalDAV kalendářů</h3>
<p>Data z CalDAV kalendářů jsou sdílena pouze s ostatními kalendářovými službami, které výslovně připojíte prostřednictvím pravidel synchronizace. Vaše CalDAV přihlašovací údaje a data nejsou sdílena s třetími stranami pro jejich vlastní účely.</p>

<h3>Ukládání a ochrana dat z CalDAV</h3>
<p>CalDAV přihlašovací údaje (uživatelská jména, hesla, hesla specifická pro aplikace) jsou šifrovány samostatně silným šifrováním. Všechna data kalendáře jsou šifrována v klidu (AES-256) a při přenosu (TLS/SSL). Používáme zabezpečená připojení k CalDAV serverům.</p>

<h3>Uchovávání a mazání dat z CalDAV</h3>
<p>Svůj CalDAV kalendář můžete kdykoli odpojit nebo smazat účet {{ config('app.name') }}. Všechny CalDAV přihlašovací údaje, tokeny a synchronizovaná data jsou trvale smazána do 30 dnů. U Apple iCloud můžete také odvolat hesla specifická pro aplikace prostřednictvím vašeho <a href="https://appleid.apple.com/account/manage" target="_blank" rel="noopener">účtu Apple ID</a>.</p>

<h2>Právní základy</h2>
<ul>
    <li>Plnění smlouvy (čl. 6 odst. 1 písm. b) GDPR)</li>
    <li>Právní povinnost (čl. 6 odst. 1 písm. c) GDPR)</li>
    <li>Oprávněný zájem (čl. 6 odst. 1 písm. f) GDPR) – bezpečnost, prevence podvodů, analytika</li>
    <li>Souhlas, je‑li vyžadován (čl. 6 odst. 1 písm. a) GDPR)</li>
    
</ul>

<h2>Doba uchování</h2>
<p>Údaje uchováváme po dobu nezbytnou pro uvedené účely, typicky po dobu existence účtu. Po smazání účtu údaje vymažeme nebo anonymizujeme, nevyžaduje‑li jejich uchování právní předpis (např. účetnictví).</p>

<h2>Příjemci a zpracovatelé</h2>
<p>Využíváme pečlivě vybrané zpracovatele pro infrastrukturu, platby, doručování e‑mailů a analytiku chyb. Příklady: hosting, Stripe (platby), transakční e‑mail, logování/monitoring. S relevantními partnery máme uzavřeny zpracovatelské smlouvy.</p>

<h2>Předání do třetích zemí</h2>
<p>Pokud dochází k předání mimo EU/EHP, uplatňujeme odpovídající záruky dle kapitoly V GDPR (rozhodnutí o přiměřenosti nebo standardní smluvní doložky).</p>

<h2>Vaše práva</h2>
<ul>
    <li>Právo na přístup, opravu, výmaz a omezení zpracování</li>
    <li>Právo na přenositelnost a námitku proti zpracování na základě oprávněných zájmů</li>
    <li>Právo odvolat souhlas kdykoli (bez vlivu na zákonnost předchozího zpracování)</li>
    <li>Právo podat stížnost u dozorového úřadu (v ČR: ÚOOÚ)</li>
    <li>Právo nebýt předmětem automatizovaného individuálního rozhodování, pokud je relevantní</li>
    <li>Právo být informován o porušení zabezpečení, pokud to vyžaduje zákon</li>
</ul>

<h2>Bezpečnost</h2>
<p>Uplatňujeme přiměřená technická a organizační opatření, včetně šifrování při přenosu i v klidu, řízení přístupu, zásady nejmenších oprávnění a pravidelných aktualizací.</p>

<h2>Kontakt</h2>
<p>Pro záležitosti ochrany osobních údajů: <a href="mailto:support@syncmyday.cz">support@syncmyday.cz</a></p>

<h2>Změny</h2>
<p>Tento dokument můžeme aktualizovat. O podstatných změnách vás vhodně informujeme v rámci služby. Pokračováním v užívání po účinnosti změn vyslovujete souhlas.</p>

