<h1>Zásady ochrany osobních údajů</h1>
<p><strong>Provozovatel:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Praha 5, DIČ: CZ7912150191</p>
<p><strong>Datum aktualizace:</strong> {{ date('Y-m-d') }}</p>

<h2>Rozsah a účel</h2>
<p>Tento dokument vysvětluje, jak zpracováváme osobní údaje v souvislosti s poskytováním služby {{ config('app.name') }}. Postupujeme dle nařízení EU 2016/679 (GDPR) a zákona č. 110/2019 Sb., o zpracování osobních údajů. Tyto zásady se řídí českým právem.</p>

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

<h2>Právní základy zpracování</h2>
<p>Vaše osobní údaje zpracováváme v souladu s GDPR (nařízení EU 2016/679) a zákonem č. 110/2019 Sb., o zpracování osobních údajů. Právními základy našeho zpracování údajů jsou:</p>
<ul>
    <li><strong>Plnění smlouvy (čl. 6 odst. 1 písm. b) GDPR, § 5 odst. 1 písm. b) zákona č. 110/2019 Sb.):</strong> Zpracování nezbytné pro poskytování služeb {{ config('app.name') }}, včetně synchronizace kalendářů, správy účtu a dodání služby</li>
    <li><strong>Právní povinnost (čl. 6 odst. 1 písm. c) GDPR, § 5 odst. 1 písm. c) zákona č. 110/2019 Sb.):</strong> Zpracování vyžadované českým právem, včetně:
        <ul>
            <li>Povinností v oblasti účetnictví a daní (zákon č. 563/1991 Sb., o účetnictví)</li>
            <li>Požadavků na uchovávání záznamů (zákon č. 235/2004 Sb., o dani z přidané hodnoty)</li>
            <li>Povinností v oblasti boje proti praní peněz, je‑li to relevantní</li>
        </ul>
    </li>
    <li><strong>Oprávněné zájmy (čl. 6 odst. 1 písm. f) GDPR, § 5 odst. 1 písm. f) zákona č. 110/2019 Sb.):</strong> Zpracování nezbytné pro naše oprávněné zájmy nebo zájmy třetích stran, včetně:
        <ul>
            <li>Zabezpečení a prevence podvodů</li>
            <li>Zlepšování služby a technické diagnostiky</li>
            <li>Ochrana našich právních nároků</li>
            <li>Agregovaná analytika pro optimalizaci služby</li>
        </ul>
        Provedli jsme posouzení oprávněných zájmů potvrzující, že tyto zájmy nepřevažují nad vašimi právy a svobodami.
    </li>
    <li><strong>Souhlas (čl. 6 odst. 1 písm. a) GDPR, § 5 odst. 1 písm. a) zákona č. 110/2019 Sb.):</strong> Pokud je to vyžadováno, získáváme váš výslovný souhlas pro:
        <ul>
            <li>Určité cookies (podle zákona č. 127/2005 Sb., o elektronických komunikacích, § 89)</li>
            <li>Marketingovou komunikaci (pokud se přihlásíte)</li>
        </ul>
        Souhlas můžete kdykoli odvolat, aniž by to ovlivnilo zákonnost zpracování před odvoláním.
    </li>
</ul>
<p><strong>Rozhodné právo:</strong> Tyto zásady ochrany osobních údajů a veškeré zpracování údajů se řídí českým právem, konkrétně GDPR jako přímo použitelným nařízením EU a zákonem č. 110/2019 Sb., o zpracování osobních údajů. Naším dozorným orgánem je Úřad pro ochranu osobních údajů (ÚOOÚ).</p>
    


<h2>Doby uchovávání údajů</h2>
<p>Osobní údaje uchováváme pouze po dobu nezbytnou pro účely popsané v těchto zásadách:</p>
<ul>
    <li><strong>Údaje aktivního účtu:</strong> Po dobu existence vašeho aktivního účtu</li>
    <li><strong>Připojení kalendářů:</strong> OAuth tokeny, stav synchronizace a data kalendáře jsou smazány do 30 dnů po odpojení kalendáře nebo smazání účtu</li>
    <li><strong>Technické logy:</strong> Systémové záznamy pro zabezpečení a sledování chyb jsou uchovávány až 90 dnů. Tyto záznamy neobsahují obsah událostí kalendáře, pouze technická metadata</li>
    <li><strong>Účetní doklady:</strong> Faktury a platební záznamy jsou uchovávány 10 let v souladu s požadavky českého účetního a daňového práva</li>
    <li><strong>Komunikace s podporou:</strong> Uchovávána až 3 roky pro účely zajištění kvality a právní ochrany</li>
    <li><strong>Anonymizovaná analytika:</strong> Agregované anonymizované statistiky užívání mohou být uchovávány na dobu neurčitou, protože nemohou identifikovat jednotlivce</li>
</ul>

<h2>Poskytovatelé služeb třetích stran a zpracovatelé</h2>
<p>Spolupracujeme s pečlivě vybranými poskytovateli služeb třetích stran, kteří zpracovávají údaje naším jménem na základě přísných zpracovatelských smluv (DPA). Vaše data neprodáváme třetím stranám. Naši zpracovatelé zahrnují:</p>
<ul>
    <li><strong>Poskytovatelé hostingu:</strong> Poskytovatelé infrastruktury, kteří hostují naši aplikaci a databáze v zabezpečených datových centrech. Mají přístup k zašifrovaným datům v klidu, ale nemohou dešifrovat obsah kalendáře bez našich šifrovacích klíčů</li>
    <li><strong>Platební procesor (Stripe):</strong> Zpracovává platby a správu předplatného. Stripe zpracovává údaje o vaší platební kartě přímo; nikdy neukládáme plná čísla karet</li>
    <li><strong>Poskytovatelé e‑mailových služeb:</strong> Doručování transakčních e‑mailů pro oznámení o účtu, obnovení hesla a komunikaci s podporou</li>
    <li><strong>Monitoring a sledování chyb:</strong> Služby, které nám pomáhají monitorovat výkon aplikace a diagnostikovat technické problémy. Tyto dostávají pouze minimální technická metadata</li>
    <li><strong>Poskytovatelé kalendářových služeb:</strong> Google, Microsoft, Apple a operátoři CalDAV serverů zpracovávají vaše kalendářová data, když tyto služby výslovně připojíte pro synchronizaci</li>
</ul>
<p>Všichni zpracovatelé třetích stran:</p>
<ul>
    <li>Jsou vázáni zpracovatelskými smlouvami zajišťujícími soulad s GDPR</li>
    <li>Zpracovávají data pouze na naše pokyny a pro stanovené účely</li>
    <li>Implementují přiměřená technická a organizační bezpečnostní opatření</li>
    <li>Nepoužívají vaše data pro své vlastní účely</li>
    <li>Musí nás informovat o jakémkoliv narušení zabezpečení údajů</li>
</ul>

<h2>Mezinárodní předávání údajů</h2>
<p>Někteří z našich poskytovatelů služeb mohou zpracovávat údaje mimo Evropský hospodářský prostor (EHP). Když jsou údaje předávány do třetích zemí, zajišťujeme adekvátní ochranu prostřednictvím:</p>
<ul>
    <li><strong>Rozhodnutí o přiměřenosti:</strong> Upřednostňujeme poskytovatele v zemích uznaných Evropskou komisí jako poskytující přiměřenou ochranu údajů (např. Spojené království, Švýcarsko, země pokryté nástupnickými rámci Privacy Shield)</li>
    <li><strong>Standardní smluvní doložky (SCC):</strong> Pro předání do jiných zemí používáme standardní smluvní doložky schválené EU zajišťující stejnou úroveň ochrany jako v rámci EU</li>
    <li><strong>Dodatečné záruky:</strong> Implementujeme technická opatření jako šifrování při přenosu i v klidu, kontroly přístupu a pravidelné bezpečnostní audity</li>
</ul>
<p>Naše hlavní zpracování dat probíhá v rámci EU. Konkrétní mezinárodní předávání zahrnuje:</p>
<ul>
    <li><strong>Hostingová infrastruktura:</strong> Primární servery umístěné v EU; záložní systémy mohou být v zemích s rozhodnutím o přiměřenosti</li>
    <li><strong>Kalendářové služby:</strong> Když připojíte kalendáře Google, Microsoft nebo Apple, vaše kalendářová data jsou synchronizována se servery provozovanými těmito poskytovateli v jejich příslušných jurisdikcích podle jejich zásad ochrany osobních údajů</li>
    <li><strong>Zpracování plateb:</strong> Stripe zpracovává platby globálně, ale udržuje soulad s GDPR prostřednictvím odpovídajících záruk</li>
</ul>

<h2>Vaše práva na ochranu osobních údajů</h2>
<p>Podle GDPR máte následující práva týkající se vašich osobních údajů:</p>
<ul>
    <li><strong>Právo na přístup:</strong> Vyžádat si kopii všech osobních údajů, které o vás uchováváme</li>
    <li><strong>Právo na opravu:</strong> Opravit nepřesné nebo neúplné osobní údaje</li>
    <li><strong>Právo na výmaz („právo být zapomenut"):</strong> Požádat o smazání vašich osobních údajů</li>
    <li><strong>Právo na omezení:</strong> Omezit způsob, jakým zpracováváme vaše údaje za určitých okolností</li>
    <li><strong>Právo na přenositelnost údajů:</strong> Obdržet vaše údaje ve strukturovaném, strojově čitelném formátu a předat je jinému poskytovateli</li>
    <li><strong>Právo na námitku:</strong> Vznést námitku proti zpracování založenému na oprávněných zájmech nebo pro účely přímého marketingu</li>
    <li><strong>Právo odvolat souhlas:</strong> Kdykoli odvolat souhlas, pokud je zpracování založeno na souhlasu (nemá vliv na zákonnost předchozího zpracování)</li>
    <li><strong>Právo podat stížnost:</strong> Podat stížnost u dozorového orgánu (v České republice: <a href="https://www.uoou.cz" target="_blank" rel="noopener">ÚOOÚ</a>)</li>
    <li><strong>Právo na informace o automatizovaném rozhodování:</strong> Neprovádíme rozhodování založené výhradně na automatizovaném zpracování, které by vás významně ovlivňovalo</li>
    <li><strong>Právo být informován o narušení zabezpečení:</strong> Budeme vás informovat, pokud narušení zabezpečení ovlivní vaše práva a svobody</li>
</ul>

<h2>Jak uplatnit svá práva</h2>
<p>Svá práva můžete uplatnit následujícími způsoby:</p>

<h3>Samoobslužné možnosti</h3>
<ul>
    <li><strong>Smazat účet:</strong> Přejděte do Nastavení účtu → Smazat účet. Tím trvale smažete všechna svá data do 30 dnů</li>
    <li><strong>Odpojit kalendáře:</strong> Přejděte do Připojení kalendářů → Odpojit. OAuth tokeny a data synchronizace budou smazána do 30 dnů</li>
    <li><strong>Exportovat data:</strong> Přejděte do Nastavení účtu → Exportovat data a stáhněte si pravidla synchronizace a konfiguraci</li>
    <li><strong>Aktualizovat informace:</strong> Upravte svůj profil a nastavení účtu přímo v aplikaci</li>
</ul>

<h3>Kontaktujte nás pro pomoc</h3>
<p>Pro další žádosti nebo dotazy týkající se ochrany osobních údajů nás kontaktujte na:</p>
<ul>
    <li><strong>E‑mail:</strong> <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></li>
    <li><strong>Předmět:</strong> "Žádost o ochranu osobních údajů - [Typ vaší žádosti]"</li>
    <li><strong>Uveďte:</strong> Vaši registrovanou e‑mailovou adresu a popis vaší žádosti</li>
</ul>
<p>Na vaši žádost odpovíme do 30 dnů, jak vyžaduje GDPR. U složitých žádostí můžeme tuto lhůtu prodloužit o dalších 60 dnů a budeme vás o prodloužení informovat.</p>

<h2>Bezpečnostní opatření</h2>
<p>Implementujeme komplexní technická a organizační bezpečnostní opatření pro ochranu vašich osobních údajů:</p>
<ul>
    <li><strong>Šifrování:</strong> AES-256 šifrování pro data v klidu; TLS/SSL pro data při přenosu</li>
    <li><strong>Kontroly přístupu:</strong> Omezení přístupu založená na rolích; princip nejmenších oprávnění</li>
    <li><strong>Zabezpečení autentizace:</strong> Bezpečné hashování hesel; OAuth 2.0 pro připojení kalendářů</li>
    <li><strong>Zabezpečení infrastruktury:</strong> Profesionálně spravovaná datová centra s fyzickým zabezpečením; pravidelné bezpečnostní aktualizace a záplaty</li>
    <li><strong>Monitoring:</strong> 24/7 bezpečnostní monitoring a detekce narušení</li>
    <li><strong>Zálohování a obnova:</strong> Pravidelné šifrované zálohy s bezpečným uchováním</li>
    <li><strong>Reakce na incidenty:</strong> Zdokumentované postupy pro řešení bezpečnostních incidentů</li>
</ul>

<h2>Kontaktní informace</h2>
<p><strong>Správce údajů:</strong> Lukas Slehofer, Kurzova 2222/16, 155 00 Praha 5, Česká republika, DIČ: CZ7912150191</p>
<p><strong>Dotazy k ochraně osobních údajů:</strong> <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></p>
<p><strong>Obecná podpora:</strong> <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></p>

<h2>Změny těchto zásad ochrany osobních údajů</h2>
<p>Tyto zásady ochrany osobních údajů můžeme čas od času aktualizovat, aby odrážely změny v našich postupech, technologiích, právních požadavcích nebo jiných faktorech.</p>
<p><strong>Jak vás informujeme o změnách:</strong></p>
<ul>
    <li><strong>Podstatné změny:</strong> Budeme vás informovat e‑mailem a/nebo výrazným oznámením v aplikaci nejméně 30 dní před nabytím účinnosti změn</li>
    <li><strong>Drobné změny:</strong> Aktualizujeme datum „Datum aktualizace" v horní části těchto zásad</li>
    <li><strong>Vaše přijetí:</strong> Pokračování v používání {{ config('app.name') }} po datu účinnosti znamená přijetí aktualizovaných zásad</li>
    <li><strong>Doporučené přezkoumání:</strong> Doporučujeme vám pravidelně kontrolovat tyto zásady</li>
</ul>
<p>Pokud nesouhlasíte se změnami těchto zásad, můžete si smazat účet před tím, než změny nabudou účinnosti.</p>

