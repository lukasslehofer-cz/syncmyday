<h1>Zásady ochrany osobných údajov</h1>
<p><strong>Prevádzkovateľ:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Praha 5, IČ DPH: CZ7912150191</p>
<p><strong>Dátum aktualizácie:</strong> {{ date('Y-m-d') }}</p>

<h2>Rozsah a účel</h2>
<p>Tento dokument vysvetľuje, ako spracúvame osobné údaje v súvislosti s poskytovaním služby {{ config('app.name') }}. Postupujeme podľa GDPR a slovenských/príslušných českých predpisov.</p>

<h2>Kategórie spracúvaných údajov</h2>
<ul>
    <li>Identifikačné a kontaktné údaje (napr. e‑mail, meno)</li>
    <li>Údaje o účte a autentifikácii (OAuth identifikátory, tokeny)</li>
    <li>Fakturačné údaje pri nákupe Pro (cez Stripe)</li>
    <li>Technické a prevádzkové údaje (logy, metaúdaje zariadenia/prehliadača)</li>
    <li>Metaúdaje synchronizácie kalendárov (časy začiatku/konca, stav). Neuchovávame názvy, popisy ani účastníkov udalostí.</li>
    <li>Metaúdaje spracovania e‑mailov pre e‑mailové kalendáre</li>
    <li>Komunikácia s podporou</li>
    <li>Identifikátory súborov cookie, ak je to relevantné</li>
    <li>Minimálne IP adresy pre bezpečnosť a prevenciu zneužitia</li>
    <li>Identifikátory platobných transakcií u poskytovateľa platieb</li>
    <li>Identifikátory odberov webhook</li>
    <li>Šifrovacie kľúče a identifikátory potrebné pre prevádzku služby</li>
    <li>Údaje nevyhnutné na splnenie zákonných povinností</li>
    <li>Akékoľvek ďalšie údaje, ktoré nám dobrovoľne poskytnete</li>
    <li>Osobitné kategórie údajov vedome nespracúvame</li>
</ul>

<h2>Spracovanie údajov z Google Calendar</h2>
<p>Používanie a prenos informácií prijatých z Google Calendar službou {{ config('app.name') }} je v súlade so <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">zásadami Google pre používateľské údaje služieb API</a>, vrátane požiadaviek na obmedzené používanie.</p>

<h3>Údaje získavané z Google</h3>
<p>Pri pripojení vášho Google Calendar žiadame o nasledujúce oprávnenia:</p>
<ul>
    <li><strong>https://www.googleapis.com/auth/calendar</strong> — Plný prístup k vašim kalendárom, umožňujúci nám čítať, vytvárať a upravovať metadáta kalendárov</li>
    <li><strong>https://www.googleapis.com/auth/calendar.events</strong> — Prístup k udalostiam v kalendári, umožňujúci nám čítať, vytvárať, upravovať a mazať udalosti</li>
</ul>
<p>Z vášho Google Calendar pristupujeme k nasledujúcim údajom:</p>
<ul>
    <li>Metadáta kalendárov (názvy kalendárov, ID, časové pásma, farby)</li>
    <li>Podrobnosti udalostí (názvy, popisy, časy začiatku/konca, miesta, účastníci, pravidlá opakovania, pripomienky, stav)</li>
    <li>Identifikátory udalostí a časové značky zmien pre sledovanie synchronizácie</li>
</ul>

<h3>Ako používame údaje z Google Calendar</h3>
<p>Údaje z Google Calendar používame výlučne na poskytovanie základnej služby synchronizácie kalendárov:</p>
<ul>
    <li>Synchronizácia udalostí medzi vaším Google Calendar a ostatnými pripojenými kalendármi (Microsoft Outlook, CalDAV alebo e‑mailovými kalendármi)</li>
    <li>Vytváranie, aktualizácia a mazanie udalostí v rámci vašich pripojených kalendárov podľa vašich pravidiel synchronizácie</li>
    <li>Udržiavanie stavu synchronizácie na prevenciu duplikátov a zabezpečenie správnej obojsmernej synchronizácie</li>
    <li>Zobrazenie vašich kalendárov a udalostí v našom webovom rozhraní na účely konfigurácie a správy</li>
</ul>
<p><strong>Údaje z Google Calendar nepoužívame na žiadne iné účely, vrátane reklamy, trénovania AI alebo analytiky nad rámec toho, co je striktne nevyhnutné pre synchronizáciu.</strong></p>

<h3>Zdieľanie údajov z Google Calendar</h3>
<p>Vaše údaje z Google Calendar nepredávame, neprenajímame ani nezdieľame s tretími stranami na ich vlastné účely. Vaše kalendárové údaje sú zdieľané len v týchto obmedzených prípadoch:</p>
<ul>
    <li><strong>S ostatnými kalendárovými službami, ktoré pripojíte:</strong> Keď nastavíte pravidlo synchronizácie, údaje o udalostiach sú prenášané do cieľovej kalendárovej služby (napr. Microsoft Outlook, CalDAV server) na vytvorenie alebo aktualizáciu synchronizovaných udalostí</li>
    <li><strong>S technickými poskytovateľmi služieb:</strong> Používame zabezpečenú hostingovú infraštruktúru na ukladanie a spracovanie vašich údajov. Títo poskytovatelia konajú ako sprostredkovatelia údajov na základe prísnych zmluvných záväzkov a nemajú nezávislý prístup k obsahu vášho kalendára</li>
    <li><strong>Podľa požiadaviek zákona:</strong> Údaje môžeme zverejniť v prípade zákonnej povinnosti (napr. súdny príkaz), ale len v minimálne nevyhnutnom rozsahu</li>
</ul>
<p>Zdieľanie údajov medzi vašimi kalendármi prebieha len vtedy, keď výslovne nakonfigurujete pravidlá synchronizácie. Máte plnú kontrolu nad tým, ktoré kalendáre sa synchronizujú a ktoré údaje medzi nimi tečú.</p>

<h3>Ukladanie a ochrana údajov z Google Calendar</h3>
<p>Implementujeme komplexné bezpečnostné opatrenia na ochranu vašich údajov z Google Calendar:</p>
<ul>
    <li><strong>Šifrovanie:</strong> Všetky údaje sú šifrované pri prenose pomocou TLS/SSL a v pokoji v našej databáze pomocou AES‑256 šifrovania</li>
    <li><strong>Prístupové tokeny:</strong> Google OAuth tokeny sú šifrované samostatne s dodatočným šifrovaním kľúčov na prevenciu neoprávneného prístupu</li>
    <li><strong>Riadenie prístupu:</strong> Prísne kontroly prístupu zabezpečujú, že len autorizované systémové komponenty môžu pristupovať k vašim kalendárovým údajom</li>
    <li><strong>Princíp najmenších oprávnení:</strong> Naše systémy žiadajú len minimálne nevyhnutné oprávnenia a prístup</li>
    <li><strong>Pravidelné bezpečnostné aktualizácie:</strong> Udržiavame aktuálne bezpečnostné záplaty a monitoring</li>
    <li><strong>Zabezpečená infraštruktúra:</strong> Údaje sú uložené v profesionálne spravovaných dátových centrách s fyzickými aj digitálnymi bezpečnostnými opatreniami</li>
</ul>
<p>Vaše údaje z Google Calendar sú uložené len po dobu nevyhnutnú na poskytovanie služby synchronizácie. Synchronizované metadáta udalostí sú uložené v našej databáze na sledovanie stavu synchronizácie, ale obsah udalostí nie je trvalo ukladaný nad rámec toho, čo je potrebné pre aktívnu synchronizáciu.</p>

<h3>Uchovávanie a mazanie údajov z Google Calendar</h3>
<p>Máte plnú kontrolu nad svojimi údajmi z Google Calendar:</p>
<ul>
    <li><strong>Odpojenie kalendára:</strong> Svoj Google Calendar môžete kedykoľvek odpojiť z nastavení účtu. Po odpojení prestaneme pristupovať k vášmu Google Calendar a zmažeme súvisiace OAuth tokeny a stav synchronizácie do 30 dní</li>
    <li><strong>Zmazanie účtu:</strong> Zmazanie vášho účtu {{ config('app.name') }} okamžite zastaví všetku synchronizáciu kalendárov. Všetky vaše kalendárové údaje, pravidlá synchronizácie a OAuth tokeny sú trvalo zmazané do 30 dní</li>
    <li><strong>Odvolanie prístupu:</strong> Prístup aplikácie {{ config('app.name') }} k vášmu Google Calendar môžete kedykoľvek odvolať na stránke <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">oprávnení účtu Google</a></li>
    <li><strong>Uchovávanie údajov:</strong> Údaje z Google Calendar uchovávame len po dobu, kým je váš kalendár pripojený a aktívny. Metadáta udalostí potrebné na synchronizáciu sú uchovávané po dobu trvania aktívneho synchronizačného vzťahu</li>
    <li><strong>Zákonné uchovávanie:</strong> Niektoré minimálne technické záznamy môžu byť uchovávané pre oprávnené záujmy (bezpečnosť, prevencia podvodov) až 90 dní, ale tieto neobsahujú obsah kalendárových udalostí</li>
</ul>

<h2>Spracovanie údajov z kalendára Microsoft Outlook/365</h2>
<p>Používanie Microsoft Graph API službou {{ config('app.name') }} je v súlade s <a href="https://learn.microsoft.com/en-us/legal/microsoft-apis/terms-of-use" target="_blank" rel="noopener">podmienkami používania API Microsoftu</a> a rešpektuje súkromie používateľských údajov.</p>

<h3>Údaje získavané z Microsoftu</h3>
<p>Pri pripojení vášho kalendára Microsoft Outlook alebo Microsoft 365 žiadame o nasledujúce oprávnenia:</p>
<ul>
    <li><strong>Calendars.Read</strong> — Prístup na čítanie k vašim kalendárom a udalostiam</li>
    <li><strong>Calendars.ReadWrite</strong> — Plný prístup na čítanie, vytváranie, úpravy a mazanie udalostí v kalendári</li>
</ul>
<p>Z vášho kalendára Microsoft pristupujeme k nasledujúcim údajom:</p>
<ul>
    <li>Metadáta kalendárov (názvy kalendárov, ID, časové pásma, farby)</li>
    <li>Podrobnosti udalostí (názvy, popisy, časy začiatku/konca, miesta, účastníci, pravidlá opakovania, pripomienky, stav, citlivosť)</li>
    <li>Identifikátory udalostí a časové značky zmien pre sledovanie synchronizácie</li>
</ul>

<h3>Ako používame údaje z kalendára Microsoft</h3>
<p>Údaje z kalendára Microsoft používame výlučne na synchronizáciu kalendárov:</p>
<ul>
    <li>Synchronizácia udalostí medzi vaším kalendárom Microsoft a ostatnými pripojenými kalendármi (Google, CalDAV alebo e‑mailovými kalendármi)</li>
    <li>Vytváranie, aktualizácia a mazanie udalostí podľa vašich pravidiel synchronizácie</li>
    <li>Udržiavanie stavu synchronizácie na prevenciu duplikátov</li>
    <li>Zobrazenie vašich kalendárov a udalostí v našom rozhraní na správu</li>
</ul>
<p><strong>Údaje z kalendára Microsoft nepoužívame na reklamu, marketing, trénovanie AI ani na žiadny iný účel okrem synchronizácie kalendárov.</strong></p>

<h3>Zdieľanie údajov z kalendára Microsoft</h3>
<p>Vaše údaje z kalendára Microsoft sú zdieľané len vtedy, keď výslovne nakonfigurujete pravidlá synchronizácie s ostatnými kalendárovými službami, ktoré pripojíte. Vaše údaje nepredávame ani nezdieľame s tretími stranami na ich vlastné účely. Techničtí poskytovatelia služieb majú prístup len na základe prísnych zmlúv o spracovaní údajov.</p>

<h3>Ukladanie a ochrana údajov z kalendára Microsoft</h3>
<p>Microsoft OAuth tokeny a údaje kalendára sú šifrované pomocou AES-256 šifrovania v pokoji a TLS/SSL pri prenose. Kontroly prístupu zabezpečujú, že len autorizované systémové komponenty môžu spracovávať vaše údaje.</p>

<h3>Uchovávanie a mazanie údajov z kalendára Microsoft</h3>
<p>Svoj kalendár Microsoft môžete kedykoľvek odpojiť, zmazať účet alebo odvolať prístup prostredníctvom <a href="https://account.microsoft.com/privacy/app-access" target="_blank" rel="noopener">oprávnení aplikácií v účte Microsoft</a>. Po odpojení alebo zmazaní účtu sú všetky súvisiace údaje zmazané do 30 dní.</p>

<h2>Spracovanie údajov z Apple iCloud a CalDAV kalendárov</h2>
<p>{{ config('app.name') }} podporuje Apple iCloud kalendáre a ďalšie kalendárové služby založené na CalDAV. Rešpektujeme súkromie používateľov v súlade s priemyselnými štandardmi a GDPR.</p>

<h3>Údaje získavané z CalDAV služieb</h3>
<p>Keď pripojíte CalDAV kalendár (vrátane Apple iCloud), poskytnete prihlasovacie údaje k serveru, ktoré nám umožňujú prístup k:</p>
<ul>
    <li>Metadáta kalendárov (názvy kalendárov, ID, časové pásma, farby)</li>
    <li>Podrobnosti udalostí (názvy, popisy, časy začiatku/konca, miesta, účastníci, pravidlá opakovania, alarmy, stav)</li>
    <li>Identifikátory udalostí (UID) a časové značky zmien pre synchronizáciu</li>
</ul>

<h3>Ako používame údaje z CalDAV kalendárov</h3>
<p>Údaje z CalDAV kalendárov používame výlučne na synchronizáciu s ostatnými kalendármi, ktoré pripojíte. Údaje sú spracovávané len na:</p>
<ul>
    <li>Obojsmernú synchronizáciu udalostí medzi vaším CalDAV kalendárom a ostatnými službami</li>
    <li>Vytváranie, aktualizáciu a mazanie udalostí podľa vašich pravidiel synchronizácie</li>
    <li>Sledovanie stavu synchronizácie na prevenciu duplikátov</li>
    <li>Zobrazenie kalendárov a udalostí v našom rozhraní</li>
</ul>
<p><strong>Údaje CalDAV nikdy nie sú používané na reklamu, analytiku ani na žiadny iný účel okrem synchronizácie.</strong></p>

<h3>Zdieľanie údajov z CalDAV kalendárov</h3>
<p>Údaje z CalDAV kalendárov sú zdieľané len s ostatnými kalendárovými službami, ktoré výslovne pripojíte prostredníctvom pravidiel synchronizácie. Vaše prihlasovacie údaje CalDAV a údaje nie sú zdieľané s tretími stranami na ich vlastné účely.</p>

<h3>Ukladanie a ochrana údajov CalDAV</h3>
<p>Prihlasovacie údaje CalDAV (používateľské mená, heslá, heslá špecifické pre aplikácie) sú šifrované samostatne pomocou silného šifrovania. Všetky údaje kalendára sú šifrované v pokoji (AES-256) a pri prenose (TLS/SSL). Používame zabezpečené pripojenia k CalDAV serverom.</p>

<h3>Uchovávanie a mazanie údajov CalDAV</h3>
<p>Svoj CalDAV kalendár môžete kedykoľvek odpojiť alebo zmazať účet {{ config('app.name') }}. Všetky prihlasovacie údaje CalDAV, tokeny a synchronizované údaje sú trvalo zmazané do 30 dní. Pri Apple iCloud môžete tiež odvolať heslá špecifické pre aplikácie prostredníctvom vášho <a href="https://appleid.apple.com/account/manage" target="_blank" rel="noopener">účtu Apple ID</a>.</p>

<h2>Právne základy</h2>
<ul>
    <li>Plnenie zmluvy (čl. 6 ods. 1 písm. b) GDPR)</li>
    <li>Právna povinnosť (čl. 6 ods. 1 písm. c) GDPR)</li>
    <li>Oprávnený záujem (čl. 6 ods. 1 písm. f) GDPR) – bezpečnosť, prevencia podvodov, analytika</li>
    <li>Súhlas, ak je vyžadovaný (čl. 6 ods. 1 písm. a) GDPR)</li>
</ul>

<h2>Doba uchovávania</h2>
<p>Údaje uchovávame len po dobu nevyhnutnú na vyššie uvedené účely, zvyčajne počas existencie vášho účtu. Po zmazaní účtu údaje zmažeme alebo anonymizujeme, pokiaľ právne predpisy nevyžadujú ich uchovanie (napr. fakturačné údaje).</p>

<h2>Príjemcovia a sprostredkovatelia</h2>
<p>Využívame starostlivo vybraných sprostredkovateľov pre infraštruktúru, platby, doručovanie e‑mailov a analytiku chýb. Príklady: hosting, Stripe (platby), transakčný e‑mail, logovanie/monitoring. Uzatvárame zmluvy o spracúvaní údajov, kde je to potrebné.</p>

<h2>Prenosy do tretích krajín</h2>
<p>Ak dochádza k prenosu údajov mimo EÚ/EHP, spoliehame sa na primerané záruky podľa kapitoly V GDPR (rozhodnutia o primeranosti alebo štandardné zmluvné doložky).</p>

<h2>Vaše práva</h2>
<ul>
    <li>Právo na prístup, opravu, vymazanie a obmedzenie spracúvania</li>
    <li>Právo na prenositeľnosť a námietku proti spracúvaniu na základe oprávnených záujmov</li>
    <li>Právo odvolať súhlas kedykoľvek (bez vplyvu na zákonnosť predchádzajúceho spracúvania)</li>
    <li>Právo podať sťažnosť dozornému orgánu (v ČR: ÚOOÚ)</li>
    <li>Právo nebyť predmetom výlučne automatizovaného rozhodovania, ak je to relevantné</li>
    <li>Povinnosť informovania o porušeniach ochrany údajov, ak to vyžaduje zákon</li>
</ul>

<h2>Bezpečnosť</h2>
<p>Uplatňujeme primerané technické a organizačné opatrenia vrátane šifrovania počas prenosu aj ukladania, riadenia prístupu, zásady najmenších oprávnení a pravidelných aktualizácií.</p>

<h2>Kontakt</h2>
<p>V otázkach ochrany osobných údajov: <a href="mailto:support@syncmyday.com">support@syncmyday.com</a></p>

<h2>Zmeny</h2>
<p>Tento dokument môžeme aktualizovať. O podstatných zmenách vás primerane informujeme v rámci služby. Pokračovaním v používaní po dátume účinnosti vyjadrujete súhlas.</p>

