<h1>Privacy Policy</h1>
<p><strong>Operator:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Prague 5, VAT ID: CZ7912150191</p>
<p><strong>Last updated:</strong> {{ date('Y-m-d') }}</p>

<h2>Scope and Purpose</h2>
<p>This Privacy Policy explains how we process personal data in connection with the provision of {{ config('app.name') }}. We follow Regulation (EU) 2016/679 (GDPR) and applicable Czech law.</p>

<h2>Categories of Data Processed</h2>
<ul>
    <li>Identification and contact data (e.g., email, name)</li>
    <li>Account and authentication data (OAuth identifiers, tokens)</li>
    <li>Billing data if you purchase Pro (handled via Stripe)</li>
    <li>Technical and usage data (logs, device/browser metadata)</li>
    <li>Calendar synchronization metadata (start/end times, status). We do not store event titles, descriptions or attendees.</li>
    <li>Inbound email processing metadata for email calendars</li>
    <li>Support communications</li>
    <li>Cookie identifiers where applicable</li>
    <li>Minimal IP addresses for security and anti‑abuse</li>
    <li>Payment transaction identifiers via our payment provider</li>
    <li>Webhook subscription identifiers</li>
    <li>Encryption keys and key identifiers required for service operation</li>
    <li>Data strictly necessary to comply with legal obligations</li>
    <li>Any additional data you voluntarily provide</li>
    <li>We do not process special categories of personal data intentionally</li>
</ul>

<h2>Google Calendar Data Processing</h2>
<p>{{ config('app.name') }}'s use and transfer of information received from Google Calendar will adhere to <a href="https://developers.google.com/terms/api-services-user-data-policy" target="_blank" rel="noopener">Google API Services User Data Policy</a>, including the Limited Use requirements.</p>

<h3>Data Accessed from Google</h3>
<p>When you connect your Google Calendar, we request the following permissions:</p>
<ul>
    <li><strong>https://www.googleapis.com/auth/calendar</strong> — Full access to your calendars, allowing us to read, create, and modify calendar metadata</li>
    <li><strong>https://www.googleapis.com/auth/calendar.events</strong> — Access to calendar events, allowing us to read, create, modify, and delete events</li>
</ul>
<p>We access the following data from your Google Calendar:</p>
<ul>
    <li>Calendar metadata (calendar names, IDs, time zones, colors)</li>
    <li>Event details (titles, descriptions, start/end times, locations, attendees, recurrence rules, reminders, status)</li>
    <li>Event identifiers and modification timestamps for synchronization tracking</li>
</ul>

<h3>How We Use Google Calendar Data</h3>
<p>We use Google Calendar data exclusively to provide the core calendar synchronization service:</p>
<ul>
    <li>Synchronizing events between your Google Calendar and other connected calendars (Microsoft Outlook, CalDAV, or email calendars)</li>
    <li>Creating, updating, and deleting events across your connected calendars according to your sync rules</li>
    <li>Maintaining synchronization state to prevent duplicates and ensure bidirectional sync works correctly</li>
    <li>Displaying your calendars and events in our web interface for configuration and management purposes</li>
</ul>
<p><strong>We do not use Google Calendar data for any other purposes, including advertising, AI training, or analytics beyond what is strictly necessary for synchronization.</strong></p>

<h3>Sharing of Google Calendar Data</h3>
<p>We do not sell, rent, or share your Google Calendar data with third parties for their own purposes. Your calendar data is shared only in the following limited circumstances:</p>
<ul>
    <li><strong>With other calendar services you connect:</strong> When you set up a sync rule, event data is transmitted to the destination calendar service (e.g., Microsoft Outlook, CalDAV server) to create or update synchronized events</li>
    <li><strong>With technical service providers:</strong> We use secure hosting infrastructure to store and process your data. These providers act as data processors under strict contractual obligations and do not have independent access to your calendar content</li>
    <li><strong>As required by law:</strong> We may disclose data if legally required (e.g., court order), but only to the minimum extent necessary</li>
</ul>
<p>Data sharing between your calendars happens only when you explicitly configure sync rules. You have full control over which calendars sync and which data flows between them.</p>

<h3>Storage and Protection of Google Calendar Data</h3>
<p>We implement comprehensive security measures to protect your Google Calendar data:</p>
<ul>
    <li><strong>Encryption:</strong> All data is encrypted in transit using TLS/SSL and at rest in our database using AES-256 encryption</li>
    <li><strong>Access tokens:</strong> Google OAuth tokens are encrypted separately with additional key encryption to prevent unauthorized access</li>
    <li><strong>Access control:</strong> Strict access controls ensure only authorized system components can access your calendar data</li>
    <li><strong>Principle of least privilege:</strong> Our systems request only the minimum necessary permissions and access</li>
    <li><strong>Regular security updates:</strong> We maintain up-to-date security patches and monitoring</li>
    <li><strong>Secure infrastructure:</strong> Data is stored in professionally managed data centers with physical and digital security measures</li>
</ul>
<p>Your Google Calendar data is stored only for as long as necessary to provide the synchronization service. Synchronized event metadata is stored in our database to track sync state, but event content is not permanently stored beyond what's needed for active synchronization.</p>

<h3>Retention and Deletion of Google Calendar Data</h3>
<p>You have full control over your Google Calendar data:</p>
<ul>
    <li><strong>Disconnect calendar:</strong> You can disconnect your Google Calendar at any time from your account settings. When disconnected, we stop accessing your Google Calendar and delete associated OAuth tokens and sync state within 30 days</li>
    <li><strong>Delete account:</strong> Deleting your {{ config('app.name') }} account immediately stops all calendar synchronization. All your calendar data, sync rules, and OAuth tokens are permanently deleted within 30 days</li>
    <li><strong>Revoke access:</strong> You can revoke {{ config('app.name') }}'s access to your Google Calendar at any time through your <a href="https://myaccount.google.com/permissions" target="_blank" rel="noopener">Google Account permissions</a> page</li>
    <li><strong>Data retention:</strong> We retain Google Calendar data only while your calendar remains connected and active. Event metadata needed for synchronization is kept for the duration of the active sync relationship</li>
    <li><strong>Legal retention:</strong> Some minimal technical logs may be retained for legitimate interests (security, fraud prevention) for up to 90 days, but these do not include calendar event content</li>
</ul>

<h2>Microsoft Outlook/365 Calendar Data Processing</h2>
<p>{{ config('app.name') }}'s use of Microsoft Graph API complies with <a href="https://learn.microsoft.com/en-us/legal/microsoft-apis/terms-of-use" target="_blank" rel="noopener">Microsoft API Terms of Use</a> and respects user data privacy.</p>

<h3>Data Accessed from Microsoft</h3>
<p>When you connect your Microsoft Outlook or Microsoft 365 Calendar, we request the following permissions:</p>
<ul>
    <li><strong>Calendars.Read</strong> — Read access to your calendars and events</li>
    <li><strong>Calendars.ReadWrite</strong> — Full access to read, create, modify, and delete calendar events</li>
</ul>
<p>We access the following data from your Microsoft Calendar:</p>
<ul>
    <li>Calendar metadata (calendar names, IDs, time zones, colors)</li>
    <li>Event details (titles, descriptions, start/end times, locations, attendees, recurrence rules, reminders, status, sensitivity)</li>
    <li>Event identifiers and modification timestamps for synchronization tracking</li>
</ul>

<h3>How We Use Microsoft Calendar Data</h3>
<p>We use Microsoft Calendar data exclusively for calendar synchronization:</p>
<ul>
    <li>Synchronizing events between your Microsoft Calendar and other connected calendars (Google, CalDAV, or email calendars)</li>
    <li>Creating, updating, and deleting events according to your sync rules</li>
    <li>Maintaining synchronization state to prevent duplicates</li>
    <li>Displaying your calendars and events in our interface for management</li>
</ul>
<p><strong>We do not use Microsoft Calendar data for advertising, marketing, AI training, or any purpose beyond calendar synchronization.</strong></p>

<h3>Sharing of Microsoft Calendar Data</h3>
<p>Your Microsoft Calendar data is shared only when you explicitly configure sync rules to other calendar services you connect. We do not sell or share your data with third parties for their own purposes. Technical service providers have access only under strict data processing agreements.</p>

<h3>Storage and Protection of Microsoft Calendar Data</h3>
<p>Microsoft OAuth tokens and calendar data are encrypted with AES-256 encryption at rest and TLS/SSL in transit. Access controls ensure only authorized system components can process your data.</p>

<h3>Retention and Deletion of Microsoft Calendar Data</h3>
<p>You can disconnect your Microsoft Calendar anytime, delete your account, or revoke access through your <a href="https://account.microsoft.com/privacy/app-access" target="_blank" rel="noopener">Microsoft Account App permissions</a>. Upon disconnection or account deletion, all associated data is deleted within 30 days.</p>

<h2>Apple iCloud & CalDAV Calendar Data Processing</h2>
<p>{{ config('app.name') }} supports Apple iCloud calendars and other CalDAV-based calendar services. We respect user privacy in accordance with industry standards and GDPR.</p>

<h3>Data Accessed from CalDAV Services</h3>
<p>When you connect a CalDAV calendar (including Apple iCloud), you provide server credentials that allow us to access:</p>
<ul>
    <li>Calendar metadata (calendar names, IDs, time zones, colors)</li>
    <li>Event details (titles, descriptions, start/end times, locations, attendees, recurrence rules, alarms, status)</li>
    <li>Event identifiers (UIDs) and modification timestamps for synchronization</li>
</ul>

<h3>How We Use CalDAV Calendar Data</h3>
<p>We use CalDAV calendar data exclusively for synchronization with other calendars you connect. Data is processed only to:</p>
<ul>
    <li>Synchronize events bidirectionally between your CalDAV calendar and other services</li>
    <li>Create, update, and delete events according to your sync rules</li>
    <li>Track synchronization state to prevent duplicates</li>
    <li>Display calendars and events in our interface</li>
</ul>
<p><strong>CalDAV data is never used for advertising, analytics, or any purpose beyond synchronization.</strong></p>

<h3>Sharing of CalDAV Calendar Data</h3>
<p>CalDAV calendar data is shared only with other calendar services you explicitly connect through sync rules. Your CalDAV credentials and data are not shared with third parties for their own purposes.</p>

<h3>Storage and Protection of CalDAV Data</h3>
<p>CalDAV credentials (usernames, passwords, app-specific passwords) are encrypted separately with strong encryption. All calendar data is encrypted at rest (AES-256) and in transit (TLS/SSL). We use secure connections to CalDAV servers.</p>

<h3>Retention and Deletion of CalDAV Data</h3>
<p>You can disconnect your CalDAV calendar or delete your {{ config('app.name') }} account anytime. All CalDAV credentials, tokens, and synchronized data are permanently deleted within 30 days. For Apple iCloud, you can also revoke app-specific passwords through your <a href="https://appleid.apple.com/account/manage" target="_blank" rel="noopener">Apple ID account</a>.</p>

<h2>Legal Bases</h2>
<ul>
    <li>Performance of contract (Article 6(1)(b) GDPR) to run {{ config('app.name') }}</li>
    <li>Legal obligation (Article 6(1)(c) GDPR), e.g., accounting/tax</li>
    <li>Legitimate interests (Article 6(1)(f) GDPR), e.g., security, fraud prevention, service analytics</li>
    <li>Consent where required (Article 6(1)(a) GDPR), e.g., certain cookies or marketing</li>
</ul>

<h2>Retention</h2>
<p>We keep data only for as long as necessary for the above purposes, typically for the duration of your account. Upon deletion of your account, we erase or anonymize data unless we must retain some records to comply with law (e.g., invoicing data).</p>

<h2>Recipients and Processors</h2>
<p>We use carefully selected processors to provide infrastructure, payments, email delivery and error analytics. Examples include hosting providers, Stripe (payments), transactional email, log/monitoring tools. We have data processing agreements with all processors where required.</p>

<h2>Transfers to Third Countries</h2>
<p>Where data is transferred outside the EU/EEA, we rely on adequate safeguards under GDPR Chapter V (adequacy decisions or Standard Contractual Clauses).</p>

<h2>Your Rights</h2>
<ul>
    <li>Access, rectification, erasure, restriction</li>
    <li>Portability and objection to processing based on legitimate interests</li>
    <li>Withdraw consent at any time (does not affect prior processing)</li>
    <li>Lodge a complaint with a supervisory authority (in the Czech Republic: ÚOOÚ)</li>
    <li>Right not to be subject to decisions based solely on automated processing where applicable</li>
    <li>Right to be informed about data breaches where legally required</li>
</ul>

<h2>Security</h2>
<p>We implement appropriate technical and organizational measures, including encryption at rest and in transit, access controls, least privilege, and regular security updates.</p>

<h2>Contact</h2>
<p>For privacy matters contact: <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></p>

<h2>Changes</h2>
<p>We may update this policy. Material changes will be notified within the service. Continued use of the service after the effective date constitutes acceptance.</p>

