<h1>Datenschutzrichtlinie</h1>
<p><strong>Betreiber:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Prag 5, USt-IdNr.: CZ7912150191</p>
<p><strong>Zuletzt aktualisiert:</strong> {{ date('Y-m-d') }}</p>

<h2>Umfang und Zweck</h2>
<p>Diese Datenschutzrichtlinie erläutert, wie wir personenbezogene Daten im Zusammenhang mit {{ config('app.name') }} verarbeiten. Wir befolgen die DSGVO (Verordnung (EU) 2016/679) und das tschechische Gesetz Nr. 110/2019 Slg. über die Verarbeitung personenbezogener Daten. Diese Richtlinie unterliegt dem tschechischen Recht.</p>

<h2>Kategorien verarbeiteter Daten</h2>
<ul>
    <li>Identifikations- und Kontaktdaten (z. B. E‑Mail, Name)</li>
    <li>Konto- und Authentifizierungsdaten (OAuth‑Kennungen, Token)</li>
    <li>Abrechnungsdaten bei Pro‑Kauf (über Stripe)</li>
    <li>Technische und Nutzungsdaten (Logs, Geräte-/Browser‑Metadaten)</li>
    <li>Kalendersynchronisations‑Metadaten (Start-/Endzeiten, Status). Wir speichern keine Titel, Beschreibungen oder Teilnehmer.</li>
    <li>Metadaten der E‑Mail‑Verarbeitung für E‑Mail‑Kalender</li>
    <li>Support‑Kommunikation</li>
    <li>Cookie‑Kennungen, sofern relevant</li>
    <li>Minimale IP‑Adressen für Sicherheit und Missbrauchsprävention</li>
    <li>Zahlungstransaktions‑IDs über den Zahlungsanbieter</li>
    <li>Webhook‑Abonnement‑Kennungen</li>
    <li>Verschlüsselungs‑Schlüssel und Kennungen, die für den Betrieb notwendig sind</li>
    <li>Daten, die zur Erfüllung gesetzlicher Pflichten erforderlich sind</li>
    <li>Alle weiteren Daten, die Sie freiwillig bereitstellen</li>
    <li>Besondere Kategorien personenbezogener Daten verarbeiten wir nicht bewusst</li>
</ul>

<h2>Google Calendar Datenverarbeitung</h2>
<p>Die Nutzung und Weitergabe von Informationen aus Google Calendar durch {{ config('app.name') }} erfolgt gemäß der <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Google API Services User Data Policy</a>, einschließlich der Limited Use-Anforderungen.</p>

<h3>Von Google abgerufene Daten</h3>
<p>Wenn Sie Ihren Google Calendar verbinden, fordern wir folgende Berechtigungen an:</p>
<ul>
    <li><strong>https://www.googleapis.com/auth/calendar</strong> — Vollzugriff auf Ihre Kalender, um Kalendermetadaten zu lesen, zu erstellen und zu ändern</li>
    <li><strong>https://www.googleapis.com/auth/calendar.events</strong> — Zugriff auf Kalenderereignisse, um Ereignisse zu lesen, zu erstellen, zu ändern und zu löschen</li>
</ul>
<p>Wir greifen auf folgende Daten aus Ihrem Google Calendar zu:</p>
<ul>
    <li>Kalendermetadaten (Kalendernamen, IDs, Zeitzonen, Farben)</li>
    <li>Ereignisdetails (Titel, Beschreibungen, Start-/Endzeiten, Orte, Teilnehmer, Wiederholungsregeln, Erinnerungen, Status)</li>
    <li>Ereigniskennungen und Änderungszeitstempel für die Synchronisationsverfolgung</li>
</ul>

<h3>Wie wir Google Calendar-Daten verwenden</h3>
<p>Wir verwenden Google Calendar-Daten ausschließlich zur Bereitstellung des Kalendersynchronisationsdienstes:</p>
<ul>
    <li>Synchronisierung von Ereignissen zwischen Ihrem Google Calendar und anderen verbundenen Kalendern (Microsoft Outlook, CalDAV oder E‑Mail‑Kalender)</li>
    <li>Erstellen, Aktualisieren und Löschen von Ereignissen über Ihre verbundenen Kalender gemäß Ihren Synchronisationsregeln</li>
    <li>Aufrechterhaltung des Synchronisationsstatus zur Vermeidung von Duplikaten und zur korrekten bidirektionalen Synchronisation</li>
    <li>Anzeige Ihrer Kalender und Ereignisse in unserer Weboberfläche zu Konfigurations- und Verwaltungszwecken</li>
</ul>
<p><strong>Wir verwenden Google Calendar-Daten nicht für andere Zwecke, einschließlich Werbung, KI‑Training oder Analytik über das hinaus, was für die Synchronisation unbedingt erforderlich ist.</strong></p>

<h3>Weitergabe von Google Calendar-Daten</h3>
<p>Wir verkaufen, vermieten oder teilen Ihre Google Calendar-Daten nicht mit Dritten für deren eigene Zwecke. Ihre Kalenderdaten werden nur unter folgenden begrenzten Umständen weitergegeben:</p>
<ul>
    <li><strong>Mit anderen von Ihnen verbundenen Kalenderdiensten:</strong> Wenn Sie eine Synchronisationsregel einrichten, werden Ereignisdaten an den Zielkalenderdienst (z. B. Microsoft Outlook, CalDAV‑Server) übertragen, um synchronisierte Ereignisse zu erstellen oder zu aktualisieren</li>
    <li><strong>Mit technischen Dienstleistern:</strong> Wir nutzen sichere Hosting-Infrastruktur zum Speichern und Verarbeiten Ihrer Daten. Diese Anbieter handeln als Auftragsverarbeiter unter strikten vertraglichen Verpflichtungen und haben keinen unabhängigen Zugriff auf Ihre Kalenderinhalte</li>
    <li><strong>Gesetzlich vorgeschrieben:</strong> Wir können Daten offenlegen, wenn gesetzlich erforderlich (z. B. gerichtliche Anordnung), jedoch nur im minimal erforderlichen Umfang</li>
</ul>
<p>Die Datenfreigabe zwischen Ihren Kalendern erfolgt nur, wenn Sie explizit Synchronisationsregeln konfigurieren. Sie haben die volle Kontrolle darüber, welche Kalender synchronisiert werden und welche Daten zwischen ihnen fließen.</p>

<h3>Speicherung und Schutz von Google Calendar-Daten</h3>
<p>Wir implementieren umfassende Sicherheitsmaßnahmen zum Schutz Ihrer Google Calendar-Daten:</p>
<ul>
    <li><strong>Verschlüsselung:</strong> Alle Daten werden bei der Übertragung mit TLS/SSL und im Ruhezustand in unserer Datenbank mit AES‑256‑Verschlüsselung verschlüsselt</li>
    <li><strong>Zugriffstoken:</strong> Google OAuth-Token werden separat mit zusätzlicher Schlüsselverschlüsselung verschlüsselt, um unbefugten Zugriff zu verhindern</li>
    <li><strong>Zugriffskontrolle:</strong> Strenge Zugriffskontrollen stellen sicher, dass nur autorisierte Systemkomponenten auf Ihre Kalenderdaten zugreifen können</li>
    <li><strong>Prinzip der geringsten Berechtigung:</strong> Unsere Systeme fordern nur die minimal erforderlichen Berechtigungen und Zugriffe an</li>
    <li><strong>Regelmäßige Sicherheitsupdates:</strong> Wir pflegen aktuelle Sicherheitspatches und Monitoring</li>
    <li><strong>Sichere Infrastruktur:</strong> Daten werden in professionell verwalteten Rechenzentren mit physischen und digitalen Sicherheitsmaßnahmen gespeichert</li>
</ul>
<p>Ihre Google Calendar-Daten werden nur so lange gespeichert, wie es zur Bereitstellung des Synchronisationsdienstes erforderlich ist. Synchronisierte Ereignismetadaten werden in unserer Datenbank gespeichert, um den Synchronisationsstatus zu verfolgen, aber Ereignisinhalte werden nicht dauerhaft über das hinaus gespeichert, was für die aktive Synchronisation erforderlich ist.</p>

<h3>Aufbewahrung und Löschung von Google Calendar-Daten</h3>
<p>Sie haben die volle Kontrolle über Ihre Google Calendar-Daten:</p>
<ul>
    <li><strong>Kalender trennen:</strong> Sie können Ihren Google Calendar jederzeit in Ihren Kontoeinstellungen trennen. Nach der Trennung greifen wir nicht mehr auf Ihren Google Calendar zu und löschen die zugehörigen OAuth-Token und den Synchronisationsstatus innerhalb von 30 Tagen</li>
    <li><strong>Konto löschen:</strong> Das Löschen Ihres {{ config('app.name') }}-Kontos stoppt sofort alle Kalendersynchronisationen. Alle Ihre Kalenderdaten, Synchronisationsregeln und OAuth-Token werden innerhalb von 30 Tagen dauerhaft gelöscht</li>
    <li><strong>Zugriff widerrufen:</strong> Sie können den Zugriff von {{ config('app.name') }} auf Ihren Google Calendar jederzeit über die Seite <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">Google-Konto-Berechtigungen</a> widerrufen</li>
    <li><strong>Datenaufbewahrung:</strong> Wir bewahren Google Calendar-Daten nur auf, solange Ihr Kalender verbunden und aktiv bleibt. Ereignismetadaten, die für die Synchronisation benötigt werden, werden für die Dauer der aktiven Synchronisationsbeziehung aufbewahrt</li>
    <li><strong>Gesetzliche Aufbewahrung:</strong> Einige minimale technische Protokolle können für berechtigte Interessen (Sicherheit, Betrugsprävention) bis zu 90 Tage aufbewahrt werden, enthalten jedoch keine Kalenderereignisinhalte</li>
</ul>

<h2>Microsoft Outlook/365 Kalender Datenverarbeitung</h2>
<p>Die Nutzung der Microsoft Graph API durch {{ config('app.name') }} entspricht den <a href="https://learn.microsoft.com/en-us/legal/microsoft-apis/terms-of-use" target="_blank" rel="noopener">Microsoft API-Nutzungsbedingungen</a> und respektiert den Datenschutz der Benutzer.</p>

<h3>Von Microsoft abgerufene Daten</h3>
<p>Wenn Sie Ihren Microsoft Outlook- oder Microsoft 365-Kalender verbinden, fordern wir folgende Berechtigungen an:</p>
<ul>
    <li><strong>Calendars.Read</strong> — Lesezugriff auf Ihre Kalender und Ereignisse</li>
    <li><strong>Calendars.ReadWrite</strong> — Vollzugriff zum Lesen, Erstellen, Ändern und Löschen von Kalenderereignissen</li>
</ul>
<p>Wir greifen auf folgende Daten aus Ihrem Microsoft-Kalender zu:</p>
<ul>
    <li>Kalendermetadaten (Kalendernamen, IDs, Zeitzonen, Farben)</li>
    <li>Ereignisdetails (Titel, Beschreibungen, Start-/Endzeiten, Orte, Teilnehmer, Wiederholungsregeln, Erinnerungen, Status, Vertraulichkeit)</li>
    <li>Ereigniskennungen und Änderungszeitstempel für die Synchronisationsverfolgung</li>
</ul>

<h3>Wie wir Microsoft-Kalenderdaten verwenden</h3>
<p>Wir verwenden Microsoft-Kalenderdaten ausschließlich zur Kalendersynchronisation:</p>
<ul>
    <li>Synchronisierung von Ereignissen zwischen Ihrem Microsoft-Kalender und anderen verbundenen Kalendern (Google, CalDAV oder E‑Mail‑Kalender)</li>
    <li>Erstellen, Aktualisieren und Löschen von Ereignissen gemäß Ihren Synchronisationsregeln</li>
    <li>Aufrechterhaltung des Synchronisationsstatus zur Vermeidung von Duplikaten</li>
    <li>Anzeige Ihrer Kalender und Ereignisse in unserer Benutzeroberfläche zur Verwaltung</li>
</ul>
<p><strong>Wir verwenden Microsoft-Kalenderdaten nicht für Werbung, Marketing, KI‑Training oder andere Zwecke außer der Kalendersynchronisation.</strong></p>

<h3>Weitergabe von Microsoft-Kalenderdaten</h3>
<p>Ihre Microsoft-Kalenderdaten werden nur weitergegeben, wenn Sie explizit Synchronisationsregeln mit anderen Kalenderdiensten konfigurieren, die Sie verbinden. Wir verkaufen oder teilen Ihre Daten nicht mit Dritten für deren eigene Zwecke. Technische Dienstleister haben nur im Rahmen strikter Datenverarbeitungsverträge Zugriff.</p>

<h3>Speicherung und Schutz von Microsoft-Kalenderdaten</h3>
<p>Microsoft OAuth-Token und Kalenderdaten werden mit AES-256-Verschlüsselung im Ruhezustand und TLS/SSL während der Übertragung verschlüsselt. Zugriffskontrollen stellen sicher, dass nur autorisierte Systemkomponenten Ihre Daten verarbeiten können.</p>

<h3>Aufbewahrung und Löschung von Microsoft-Kalenderdaten</h3>
<p>Sie können Ihren Microsoft-Kalender jederzeit trennen, Ihr Konto löschen oder den Zugriff über Ihre <a href="https://account.microsoft.com/privacy/app-access" target="_blank" rel="noopener">Microsoft-Konto-App-Berechtigungen</a> widerrufen. Bei Trennung oder Kontolöschung werden alle zugehörigen Daten innerhalb von 30 Tagen gelöscht.</p>

<h2>Apple iCloud & CalDAV Kalender Datenverarbeitung</h2>
<p>{{ config('app.name') }} unterstützt Apple iCloud-Kalender und andere CalDAV-basierte Kalenderdienste. Wir respektieren die Privatsphäre der Benutzer gemäß Industriestandards und DSGVO.</p>

<h3>Von CalDAV-Diensten abgerufene Daten</h3>
<p>Wenn Sie einen CalDAV-Kalender (einschließlich Apple iCloud) verbinden, geben Sie Serverzugangsdaten an, die uns Zugriff gewähren auf:</p>
<ul>
    <li>Kalendermetadaten (Kalendernamen, IDs, Zeitzonen, Farben)</li>
    <li>Ereignisdetails (Titel, Beschreibungen, Start-/Endzeiten, Orte, Teilnehmer, Wiederholungsregeln, Alarme, Status)</li>
    <li>Ereigniskennungen (UIDs) und Änderungszeitstempel für die Synchronisation</li>
</ul>

<h3>Wie wir CalDAV-Kalenderdaten verwenden</h3>
<p>Wir verwenden CalDAV-Kalenderdaten ausschließlich zur Synchronisation mit anderen Kalendern, die Sie verbinden. Daten werden nur verarbeitet, um:</p>
<ul>
    <li>Ereignisse bidirektional zwischen Ihrem CalDAV-Kalender und anderen Diensten zu synchronisieren</li>
    <li>Ereignisse gemäß Ihren Synchronisationsregeln zu erstellen, aktualisieren und löschen</li>
    <li>Den Synchronisationsstatus zur Vermeidung von Duplikaten zu verfolgen</li>
    <li>Kalender und Ereignisse in unserer Benutzeroberfläche anzuzeigen</li>
</ul>
<p><strong>CalDAV-Daten werden niemals für Werbung, Analytik oder andere Zwecke außer der Synchronisation verwendet.</strong></p>

<h3>Weitergabe von CalDAV-Kalenderdaten</h3>
<p>CalDAV-Kalenderdaten werden nur mit anderen Kalenderdiensten geteilt, die Sie explizit über Synchronisationsregeln verbinden. Ihre CalDAV-Zugangsdaten und -Daten werden nicht mit Dritten für deren eigene Zwecke geteilt.</p>

<h3>Speicherung und Schutz von CalDAV-Daten</h3>
<p>CalDAV-Zugangsdaten (Benutzernamen, Passwörter, App-spezifische Passwörter) werden separat mit starker Verschlüsselung verschlüsselt. Alle Kalenderdaten werden im Ruhezustand (AES-256) und während der Übertragung (TLS/SSL) verschlüsselt. Wir verwenden sichere Verbindungen zu CalDAV-Servern.</p>

<h3>Aufbewahrung und Löschung von CalDAV-Daten</h3>
<p>Sie können Ihren CalDAV-Kalender jederzeit trennen oder Ihr {{ config('app.name') }}-Konto löschen. Alle CalDAV-Zugangsdaten, Token und synchronisierten Daten werden innerhalb von 30 Tagen dauerhaft gelöscht. Für Apple iCloud können Sie auch App-spezifische Passwörter über Ihr <a href="https://appleid.apple.com/account/manage" target="_blank" rel="noopener">Apple-ID-Konto</a> widerrufen.</p>

<h2>Rechtsgrundlagen der Datenverarbeitung</h2>
<p>Wir verarbeiten Ihre personenbezogenen Daten gemäß DSGVO (Verordnung (EU) 2016/679) und dem tschechischen Gesetz Nr. 110/2019 Slg. über die Verarbeitung personenbezogener Daten. Die Rechtsgrundlagen für unsere Datenverarbeitung sind:</p>
<ul>
    <li><strong>Vertragserfüllung (Art. 6 Abs. 1 lit. b DSGVO, § 5 Abs. 1 lit. b des tschechischen Gesetzes Nr. 110/2019 Slg.):</strong> Verarbeitung zur Bereitstellung der {{ config('app.name') }}-Dienste, einschließlich Kalendersynchronisation, Kontoverwaltung und Servicebereitstellung</li>
    <li><strong>Rechtliche Verpflichtung (Art. 6 Abs. 1 lit. c DSGVO, § 5 Abs. 1 lit. c des tschechischen Gesetzes Nr. 110/2019 Slg.):</strong> Verarbeitung gemäß tschechischem Recht, einschließlich:
        <ul>
            <li>Buchhaltungs- und Steuerpflichten (Gesetz Nr. 563/1991 Slg., Rechnungslegungsgesetz)</li>
            <li>Aufbewahrungsanforderungen (Gesetz Nr. 235/2004 Slg., Mehrwertsteuergesetz)</li>
            <li>Geldwäschepflichten, soweit anwendbar</li>
        </ul>
    </li>
    <li><strong>Berechtigte Interessen (Art. 6 Abs. 1 lit. f DSGVO, § 5 Abs. 1 lit. f des tschechischen Gesetzes Nr. 110/2019 Slg.):</strong> Verarbeitung für unsere berechtigten Interessen oder die Dritter, einschließlich:
        <ul>
            <li>Sicherheit und Betrugsprävention</li>
            <li>Serviceverbesserung und technische Diagnose</li>
            <li>Schutz unserer Rechtsansprüche</li>
            <li>Aggregierte Analytik zur Serviceoptimierung</li>
        </ul>
        Wir haben eine Interessenabwägung durchgeführt, die bestätigt, dass diese Interessen nicht durch Ihre Rechte und Freiheiten überwiegen.
    </li>
    <li><strong>Einwilligung (Art. 6 Abs. 1 lit. a DSGVO, § 5 Abs. 1 lit. a des tschechischen Gesetzes Nr. 110/2019 Slg.):</strong> Soweit erforderlich, holen wir Ihre ausdrückliche Einwilligung ein für:
        <ul>
            <li>Bestimmte Cookies (gemäß tschechischem Gesetz Nr. 127/2005 Slg., Gesetz über elektronische Kommunikation, § 89)</li>
            <li>Marketing-Kommunikation (bei Opt-in)</li>
        </ul>
        Sie können die Einwilligung jederzeit widerrufen, ohne dass die Rechtmäßigkeit der vorherigen Verarbeitung berührt wird.
    </li>
</ul>
<p><strong>Anwendbares Recht:</strong> Diese Datenschutzrichtlinie und alle Datenverarbeitung unterliegen dem tschechischen Recht, insbesondere der DSGVO als direkt anwendbarer EU-Verordnung und dem tschechischen Gesetz Nr. 110/2019 Slg. über die Verarbeitung personenbezogener Daten. Unsere Aufsichtsbehörde ist das Amt für den Schutz personenbezogener Daten (Úřad pro ochranu osobních údajů, ÚOOÚ).</p>
</ul>

<h2>Aufbewahrung</h2>
<p>Wir speichern Daten nur so lange, wie es für die oben genannten Zwecke erforderlich ist, in der Regel für die Dauer Ihres Kontos. Nach Löschung des Kontos löschen oder anonymisieren wir Daten, sofern keine gesetzliche Aufbewahrungspflicht besteht (z. B. Abrechnungsdaten).</p>

<h2>Empfänger und Auftragsverarbeiter</h2>
<p>Wir setzen sorgfältig ausgewählte Auftragsverarbeiter für Infrastruktur, Zahlungen, E‑Mail‑Zustellung und Fehler‑Analytik ein. Beispiele: Hosting, Stripe (Zahlungen), Transaktions‑E‑Mail, Logging/Monitoring. Es bestehen Auftragsverarbeitungsverträge, wo erforderlich.</p>

<h2>Übermittlungen in Drittländer</h2>
<p>Erfolgen Übermittlungen außerhalb der EU/des EWR, stützen wir uns auf geeignete Garantien gemäß Kapitel V DSGVO (Angemessenheitsbeschlüsse oder Standardvertragsklauseln).</p>

<h2>Ihre Rechte</h2>
<ul>
    <li>Auskunft, Berichtigung, Löschung, Einschränkung</li>
    <li>Datenübertragbarkeit und Widerspruch gegen die Verarbeitung auf Basis berechtigter Interessen</li>
    <li>Widerruf der Einwilligung jederzeit (ohne Auswirkung auf die Rechtmäßigkeit der bisherigen Verarbeitung)</li>
    <li>Beschwerde bei einer Aufsichtsbehörde (in Tschechien: ÚOOÚ)</li>
    <li>Kein ausschließlich automatisiertes Entscheiden, sofern anwendbar</li>
    <li>Benachrichtigung über Datenschutzverletzungen, soweit gesetzlich vorgeschrieben</li>
</ul>

<h2>Sicherheit</h2>
<p>Wir implementieren geeignete technische und organisatorische Maßnahmen, einschließlich Verschlüsselung bei Übertragung und Speicherung, Zugriffskontrollen, Least‑Privilege‑Prinzip und regelmäßige Sicherheitsupdates.</p>

<h2>Kontakt</h2>
<p>Für Datenschutz‑Anfragen: <a href="mailto:support@syncmyday.com">support@syncmyday.com</a></p>

<h2>Änderungen</h2>
<p>Wir können diese Richtlinie aktualisieren. Über wesentliche Änderungen informieren wir innerhalb des Dienstes. Die fortgesetzte Nutzung nach Wirksamwerden gilt als Zustimmung.</p>

