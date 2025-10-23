<h1>Polityka prywatności</h1>
<p><strong>Administrator:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Praga 5, NIP (VAT): CZ7912150191</p>
<p><strong>Data aktualizacji:</strong> {{ date('Y-m-d') }}</p>

<h2>Zakres i cel</h2>
<p>Niniejszy dokument wyjaśnia, w jaki sposób przetwarzamy dane osobowe w związku ze świadczeniem usługi {{ config('app.name') }}. Stosujemy RODO oraz właściwe prawo czeskie.</p>

<h2>Kategorie przetwarzanych danych</h2>
<ul>
    <li>Dane identyfikacyjne i kontaktowe (np. e‑mail, imię i nazwisko)</li>
    <li>Dane konta i uwierzytelniania (identyfikatory OAuth, tokeny)</li>
    <li>Dane rozliczeniowe w przypadku zakupu Pro (przez Stripe)</li>
    <li>Dane techniczne i eksploatacyjne (logi, metadane urządzenia/przeglądarki)</li>
    <li>Metadane synchronizacji kalendarzy (czasy rozpoczęcia/zakończenia, status). Nie przechowujemy tytułów, opisów ani uczestników wydarzeń.</li>
    <li>Metadane przetwarzania e‑maili dla kalendarzy e‑mail</li>
    <li>Komunikacja z pomocą techniczną</li>
    <li>Identyfikatory plików cookie (jeśli dotyczy)</li>
    <li>Minimalne adresy IP na potrzeby bezpieczeństwa i przeciwdziałania nadużyciom</li>
    <li>Identyfikatory transakcji płatniczych u dostawcy płatności</li>
    <li>Identyfikatory subskrypcji webhook</li>
    <li>Klucze szyfrowania i identyfikatory niezbędne do działania usługi</li>
    <li>Dane niezbędne do spełnienia obowiązków prawnych</li>
    <li>Wszelkie inne dane przekazane dobrowolnie</li>
    <li>Nie przetwarzamy świadomie szczególnych kategorii danych</li>
</ul>

<h2>Przetwarzanie danych z Google Calendar</h2>
<p>Korzystanie i przekazywanie informacji otrzymanych z Google Calendar przez {{ config('app.name') }} jest zgodne z <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Polityką danych użytkowników usług API Google</a>, w tym z wymogami ograniczonego użytkowania.</p>

<h3>Dane pobierane z Google</h3>
<p>Podczas łączenia z Google Calendar prosimy o następujące uprawnienia:</p>
<ul>
    <li><strong>https://www.googleapis.com/auth/calendar</strong> — Pełny dostęp do kalendarzy, umożliwiający odczyt, tworzenie i modyfikację metadanych kalendarzy</li>
    <li><strong>https://www.googleapis.com/auth/calendar.events</strong> — Dostęp do wydarzeń w kalendarzu, umożliwiający odczyt, tworzenie, modyfikację i usuwanie wydarzeń</li>
</ul>
<p>Uzyskujemy dostęp do następujących danych z Google Calendar:</p>
<ul>
    <li>Metadane kalendarzy (nazwy kalendarzy, identyfikatory, strefy czasowe, kolory)</li>
    <li>Szczegóły wydarzeń (tytuły, opisy, czasy rozpoczęcia/zakończenia, lokalizacje, uczestników, reguły powtarzania, przypomnienia, status)</li>
    <li>Identyfikatory wydarzeń i znaczniki czasowe modyfikacji do śledzenia synchronizacji</li>
</ul>

<h3>Jak wykorzystujemy dane z Google Calendar</h3>
<p>Dane z Google Calendar wykorzystujemy wyłącznie do świadczenia podstawowej usługi synchronizacji kalendarzy:</p>
<ul>
    <li>Synchronizacja wydarzeń między Google Calendar a innymi podłączonymi kalendarzami (Microsoft Outlook, CalDAV lub kalendarze e‑mail)</li>
    <li>Tworzenie, aktualizowanie i usuwanie wydarzeń w podłączonych kalendarzach zgodnie z regułami synchronizacji</li>
    <li>Utrzymanie stanu synchronizacji w celu zapobiegania duplikatom i zapewnienia prawidłowej synchronizacji dwukierunkowej</li>
    <li>Wyświetlanie kalendarzy i wydarzeń w interfejsie webowym do celów konfiguracji i zarządzania</li>
</ul>
<p><strong>Nie wykorzystujemy danych z Google Calendar do innych celów, w tym reklamy, trenowania AI czy analityki wykraczającej poza to, co jest ściśle niezbędne do synchronizacji.</strong></p>

<h3>Udostępnianie danych z Google Calendar</h3>
<p>Nie sprzedajemy, nie wynajmujemy ani nie udostępniamy danych z Google Calendar osobom trzecim do ich własnych celów. Dane kalendarza są udostępniane tylko w następujących ograniczonych okolicznościach:</p>
<ul>
    <li><strong>Z innymi podłączonymi usługami kalendarzowymi:</strong> Podczas konfiguracji reguły synchronizacji dane wydarzeń są przesyłane do docelowej usługi kalendarza (np. Microsoft Outlook, serwer CalDAV) w celu utworzenia lub zaktualizowania zsynchronizowanych wydarzeń</li>
    <li><strong>Z dostawcami usług technicznych:</strong> Korzystamy z bezpiecznej infrastruktury hostingowej do przechowywania i przetwarzania danych. Ci dostawcy działają jako podmioty przetwarzające na podstawie ścisłych zobowiązań umownych i nie mają niezależnego dostępu do treści kalendarza</li>
    <li><strong>Zgodnie z wymogami prawa:</strong> Możemy ujawniać dane, jeśli jest to wymagane prawnie (np. nakaz sądowy), ale tylko w minimalnym niezbędnym zakresie</li>
</ul>
<p>Udostępnianie danych między kalendarzami następuje tylko wtedy, gdy wyraźnie skonfigurujesz reguły synchronizacji. Masz pełną kontrolę nad tym, które kalendarze są synchronizowane i jakie dane między nimi przepływają.</p>

<h3>Przechowywanie i ochrona danych z Google Calendar</h3>
<p>Wdrażamy kompleksowe środki bezpieczeństwa w celu ochrony danych z Google Calendar:</p>
<ul>
    <li><strong>Szyfrowanie:</strong> Wszystkie dane są szyfrowane podczas transmisji za pomocą TLS/SSL oraz w stanie spoczynku w naszej bazie danych przy użyciu szyfrowania AES‑256</li>
    <li><strong>Tokeny dostępu:</strong> Tokeny Google OAuth są szyfrowane oddzielnie z dodatkowym szyfrowaniem klucza, aby zapobiec nieuprawnionemu dostępowi</li>
    <li><strong>Kontrola dostępu:</strong> Ścisłe kontrole dostępu zapewniają, że tylko autoryzowane komponenty systemu mogą uzyskać dostęp do danych kalendarza</li>
    <li><strong>Zasada najmniejszych uprawnień:</strong> Nasze systemy żądają tylko minimalnie niezbędnych uprawnień i dostępu</li>
    <li><strong>Regularne aktualizacje zabezpieczeń:</strong> Utrzymujemy aktualne poprawki zabezpieczeń i monitoring</li>
    <li><strong>Bezpieczna infrastruktura:</strong> Dane są przechowywane w profesjonalnie zarządzanych centrach danych z fizycznymi i cyfrowymi środkami bezpieczeństwa</li>
</ul>
<p>Dane z Google Calendar są przechowywane tylko tak długo, jak jest to konieczne do świadczenia usługi synchronizacji. Zsynchronizowane metadane wydarzeń są przechowywane w naszej bazie danych w celu śledzenia stanu synchronizacji, ale treść wydarzeń nie jest przechowywana na stałe ponad to, co jest potrzebne do aktywnej synchronizacji.</p>

<h3>Przechowywanie i usuwanie danych z Google Calendar</h3>
<p>Masz pełną kontrolę nad swoimi danymi z Google Calendar:</p>
<ul>
    <li><strong>Odłączenie kalendarza:</strong> Możesz odłączyć Google Calendar w dowolnym momencie z ustawień konta. Po odłączeniu przestajemy uzyskiwać dostęp do Google Calendar i usuwamy powiązane tokeny OAuth oraz stan synchronizacji w ciągu 30 dni</li>
    <li><strong>Usunięcie konta:</strong> Usunięcie konta {{ config('app.name') }} natychmiast zatrzymuje całą synchronizację kalendarza. Wszystkie dane kalendarza, reguły synchronizacji i tokeny OAuth są trwale usuwane w ciągu 30 dni</li>
    <li><strong>Cofnięcie dostępu:</strong> Możesz cofnąć dostęp {{ config('app.name') }} do Google Calendar w dowolnym momencie poprzez stronę <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">uprawnień konta Google</a></li>
    <li><strong>Przechowywanie danych:</strong> Przechowujemy dane z Google Calendar tylko wtedy, gdy kalendarz pozostaje podłączony i aktywny. Metadane wydarzeń potrzebne do synchronizacji są przechowywane przez czas trwania aktywnej relacji synchronizacji</li>
    <li><strong>Przechowywanie prawne:</strong> Niektóre minimalne dzienniki techniczne mogą być przechowywane dla prawnie uzasadnionych interesów (bezpieczeństwo, zapobieganie oszustwom) przez maksymalnie 90 dni, ale nie zawierają one treści wydarzeń kalendarzowych</li>
</ul>

<h2>Przetwarzanie danych z kalendarza Microsoft Outlook/365</h2>
<p>Korzystanie z Microsoft Graph API przez {{ config('app.name') }} jest zgodne z <a href="https://learn.microsoft.com/en-us/legal/microsoft-apis/terms-of-use" target="_blank" rel="noopener">Warunkami użytkowania API Microsoftu</a> i respektuje prywatność danych użytkowników.</p>

<h3>Dane pobierane z Microsoftu</h3>
<p>Podczas łączenia z kalendarzem Microsoft Outlook lub Microsoft 365 prosimy o następujące uprawnienia:</p>
<ul>
    <li><strong>Calendars.Read</strong> — Dostęp do odczytu kalendarzy i wydarzeń</li>
    <li><strong>Calendars.ReadWrite</strong> — Pełny dostęp do odczytu, tworzenia, modyfikacji i usuwania wydarzeń w kalendarzu</li>
</ul>
<p>Uzyskujemy dostęp do następujących danych z kalendarza Microsoft:</p>
<ul>
    <li>Metadane kalendarzy (nazwy kalendarzy, identyfikatory, strefy czasowe, kolory)</li>
    <li>Szczegóły wydarzeń (tytuły, opisy, czasy rozpoczęcia/zakończenia, lokalizacje, uczestnicy, reguły powtarzania, przypomnienia, status, poufność)</li>
    <li>Identyfikatory wydarzeń i znaczniki czasowe modyfikacji do śledzenia synchronizacji</li>
</ul>

<h3>Jak wykorzystujemy dane z kalendarza Microsoft</h3>
<p>Dane z kalendarza Microsoft wykorzystujemy wyłącznie do synchronizacji kalendarzy:</p>
<ul>
    <li>Synchronizacja wydarzeń między kalendarzem Microsoft a innymi podłączonymi kalendarzami (Google, CalDAV lub kalendarze e‑mail)</li>
    <li>Tworzenie, aktualizowanie i usuwanie wydarzeń zgodnie z regułami synchronizacji</li>
    <li>Utrzymanie stanu synchronizacji w celu zapobiegania duplikatom</li>
    <li>Wyświetlanie kalendarzy i wydarzeń w interfejsie do zarządzania</li>
</ul>
<p><strong>Nie wykorzystujemy danych z kalendarza Microsoft do reklamy, marketingu, trenowania AI ani do żadnego innego celu niż synchronizacja kalendarzy.</strong></p>

<h3>Udostępnianie danych z kalendarza Microsoft</h3>
<p>Dane z kalendarza Microsoft są udostępniane tylko wtedy, gdy wyraźnie skonfigurujesz reguły synchronizacji z innymi usługami kalendarzowymi, które podłączysz. Nie sprzedajemy ani nie udostępniamy danych osobom trzecim do ich własnych celów. Dostawcy usług technicznych mają dostęp tylko na podstawie ścisłych umów o przetwarzanie danych.</p>

<h3>Przechowywanie i ochrona danych z kalendarza Microsoft</h3>
<p>Tokeny Microsoft OAuth i dane kalendarza są szyfrowane przy użyciu szyfrowania AES-256 w stanie spoczynku i TLS/SSL podczas transmisji. Kontrole dostępu zapewniają, że tylko autoryzowane komponenty systemu mogą przetwarzać dane.</p>

<h3>Przechowywanie i usuwanie danych z kalendarza Microsoft</h3>
<p>Możesz w dowolnym momencie odłączyć kalendarz Microsoft, usunąć konto lub cofnąć dostęp poprzez <a href="https://account.microsoft.com/privacy/app-access" target="_blank" rel="noopener">uprawnienia aplikacji w koncie Microsoft</a>. Po odłączeniu lub usunięciu konta wszystkie powiązane dane są usuwane w ciągu 30 dni.</p>

<h2>Przetwarzanie danych z Apple iCloud i kalendarzy CalDAV</h2>
<p>{{ config('app.name') }} obsługuje kalendarze Apple iCloud i inne usługi kalendarzowe oparte na CalDAV. Respektujemy prywatność użytkowników zgodnie ze standardami branżowymi i RODO.</p>

<h3>Dane pobierane z usług CalDAV</h3>
<p>Podczas łączenia kalendarza CalDAV (w tym Apple iCloud) podajesz dane uwierzytelniające serwera, które dają nam dostęp do:</p>
<ul>
    <li>Metadane kalendarzy (nazwy kalendarzy, identyfikatory, strefy czasowe, kolory)</li>
    <li>Szczegóły wydarzeń (tytuły, opisy, czasy rozpoczęcia/zakończenia, lokalizacje, uczestnicy, reguły powtarzania, alarmy, status)</li>
    <li>Identyfikatory wydarzeń (UID) i znaczniki czasowe modyfikacji do synchronizacji</li>
</ul>

<h3>Jak wykorzystujemy dane z kalendarzy CalDAV</h3>
<p>Dane z kalendarzy CalDAV wykorzystujemy wyłącznie do synchronizacji z innymi kalendarzami, które podłączysz. Dane są przetwarzane tylko w celu:</p>
<ul>
    <li>Synchronizacji dwukierunkowej wydarzeń między kalendarzem CalDAV a innymi usługami</li>
    <li>Tworzenia, aktualizowania i usuwania wydarzeń zgodnie z regułami synchronizacji</li>
    <li>Śledzenia stanu synchronizacji w celu zapobiegania duplikatom</li>
    <li>Wyświetlania kalendarzy i wydarzeń w interfejsie</li>
</ul>
<p><strong>Dane CalDAV nigdy nie są wykorzystywane do reklamy, analityki ani do żadnego innego celu niż synchronizacja.</strong></p>

<h3>Udostępnianie danych z kalendarzy CalDAV</h3>
<p>Dane z kalendarzy CalDAV są udostępniane tylko innym usługom kalendarzowym, które wyraźnie podłączysz poprzez reguły synchronizacji. Dane uwierzytelniające CalDAV i dane nie są udostępniane osobom trzecim do ich własnych celów.</p>

<h3>Przechowywanie i ochrona danych CalDAV</h3>
<p>Dane uwierzytelniające CalDAV (nazwy użytkowników, hasła, hasła specyficzne dla aplikacji) są szyfrowane oddzielnie przy użyciu silnego szyfrowania. Wszystkie dane kalendarza są szyfrowane w stanie spoczynku (AES-256) i podczas transmisji (TLS/SSL). Używamy bezpiecznych połączeń z serwerami CalDAV.</p>

<h3>Przechowywanie i usuwanie danych CalDAV</h3>
<p>Możesz w dowolnym momencie odłączyć kalendarz CalDAV lub usunąć konto {{ config('app.name') }}. Wszystkie dane uwierzytelniające CalDAV, tokeny i zsynchronizowane dane są trwale usuwane w ciągu 30 dni. W przypadku Apple iCloud możesz również cofnąć hasła specyficzne dla aplikacji poprzez <a href="https://appleid.apple.com/account/manage" target="_blank" rel="noopener">konto Apple ID</a>.</p>

<h2>Podstawy prawne</h2>
<ul>
    <li>Wykonanie umowy (art. 6 ust. 1 lit. b RODO)</li>
    <li>Obowiązek prawny (art. 6 ust. 1 lit. c RODO)</li>
    <li>Prawnie uzasadniony interes (art. 6 ust. 1 lit. f RODO) – bezpieczeństwo, zapobieganie nadużyciom, analityka</li>
    <li>Zgoda, jeśli jest wymagana (art. 6 ust. 1 lit. a RODO)</li>
</ul>

<h2>Okres przechowywania</h2>
<p>Dane przechowujemy przez okres niezbędny do określonych celów, zwykle przez czas trwania konta. Po usunięciu konta dane usuwamy lub anonimizujemy, chyba że prawo wymaga ich zachowania (np. dane księgowe).</p>

<h2>Odbiorcy i podmioty przetwarzające</h2>
<p>Korzystamy z wybranych podmiotów przetwarzających do infrastruktury, płatności, wysyłki e‑maili i analityki błędów. Przykłady: dostawcy hostingu, Stripe (płatności), e‑mail transakcyjny, narzędzia logujące/monitorujące. Zawieramy umowy powierzenia przetwarzania tam, gdzie to wymagane.</p>

<h2>Przekazywanie do państw trzecich</h2>
<p>W przypadku przekazywania danych poza UE/EOG stosujemy odpowiednie zabezpieczenia zgodnie z rozdziałem V RODO (decyzje o adekwatności lub standardowe klauzule umowne).</p>

<h2>Twoje prawa</h2>
<ul>
    <li>Dostęp, sprostowanie, usunięcie, ograniczenie przetwarzania</li>
    <li>Przenoszenie danych i sprzeciw wobec przetwarzania na podstawie prawnie uzasadnionego interesu</li>
    <li>Wycofanie zgody w dowolnym momencie (bez wpływu na zgodność wcześniejszego przetwarzania z prawem)</li>
    <li>Skarga do organu nadzorczego (w Czechach: ÚOOÚ)</li>
    <li>Brak podlegania decyzjom opartym wyłącznie na zautomatyzowanym przetwarzaniu, jeśli dotyczy</li>
    <li>Obowiązek informowania o naruszeniach ochrony danych, jeśli wymagają tego przepisy</li>
}</ul>

<h2>Bezpieczeństwo</h2>
<p>Stosujemy odpowiednie środki techniczne i organizacyjne, w tym szyfrowanie w trakcie przesyłu i spoczynku, kontrolę dostępu, zasadę minimalnych uprawnień i regularne aktualizacje.</p>

<h2>Kontakt</h2>
<p>W sprawach prywatności: <a href="mailto:support@syncmyday.com">support@syncmyday.com</a></p>

<h2>Zmiany</h2>
<p>Możemy aktualizować niniejszą politykę. O istotnych zmianach poinformujemy w ramach usługi. Dalsze korzystanie po dacie wejścia w życie oznacza akceptację.</p>

