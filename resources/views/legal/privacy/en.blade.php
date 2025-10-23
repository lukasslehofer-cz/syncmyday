<h1>Privacy Policy</h1>
<p><strong>Operator:</strong> SyncMyDay — Lukas Slehofer, Kurzova 2222/16, 155 00 Prague 5, VAT ID: CZ7912150191</p>
<p><strong>Last updated:</strong> {{ date('Y-m-d') }}</p>

<h2>Scope and Purpose</h2>
<p>This Privacy Policy explains how we process personal data in connection with the provision of {{ config('app.name') }}. We follow Regulation (EU) 2016/679 (GDPR) and Czech Act No. 110/2019 Coll., on Personal Data Processing. This policy is governed by Czech law.</p>

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

<h2>Legal Bases for Data Processing</h2>
<p>We process your personal data in accordance with GDPR (Regulation (EU) 2016/679) and Czech Act No. 110/2019 Coll., on Personal Data Processing. The legal bases for our data processing activities are:</p>
<ul>
    <li><strong>Performance of contract (Article 6(1)(b) GDPR, Section 5(1)(b) of Czech Act No. 110/2019 Coll.):</strong> Processing necessary to provide {{ config('app.name') }} services, including calendar synchronization, account management, and service delivery</li>
    <li><strong>Legal obligation (Article 6(1)(c) GDPR, Section 5(1)(c) of Czech Act No. 110/2019 Coll.):</strong> Processing required by Czech law, including:
        <ul>
            <li>Accounting and tax obligations (Act No. 563/1991 Coll., Accounting Act)</li>
            <li>Record retention requirements (Act No. 235/2004 Coll., VAT Act)</li>
            <li>Anti-money laundering obligations where applicable</li>
        </ul>
    </li>
    <li><strong>Legitimate interests (Article 6(1)(f) GDPR, Section 5(1)(f) of Czech Act No. 110/2019 Coll.):</strong> Processing necessary for our legitimate interests or those of third parties, including:
        <ul>
            <li>Security and fraud prevention</li>
            <li>Service improvement and technical diagnostics</li>
            <li>Protection of our legal rights</li>
            <li>Aggregate analytics for service optimization</li>
        </ul>
        We have conducted a legitimate interest assessment confirming that these interests are not overridden by your rights and freedoms.
    </li>
    <li><strong>Consent (Article 6(1)(a) GDPR, Section 5(1)(a) of Czech Act No. 110/2019 Coll.):</strong> Where required, we obtain your explicit consent for:
        <ul>
            <li>Certain cookies (pursuant to Czech Act No. 127/2005 Coll., Electronic Communications Act, Section 89)</li>
            <li>Marketing communications (if you opt in)</li>
        </ul>
        You may withdraw consent at any time without affecting the lawfulness of processing before withdrawal.
    </li>
</ul>
<p><strong>Applicable law:</strong> This Privacy Policy and all data processing is governed by Czech law, specifically GDPR as directly applicable EU regulation and Czech Act No. 110/2019 Coll., on Personal Data Processing. Our supervisory authority is the Office for Personal Data Protection (Úřad pro ochranu osobních údajů, ÚOOÚ).</p>

<h2>Data Retention Periods</h2>
<p>We retain personal data only as long as necessary for the purposes described in this policy:</p>
<ul>
    <li><strong>Active account data:</strong> Retained for the duration of your active account</li>
    <li><strong>Calendar connections:</strong> OAuth tokens, sync state, and calendar data are deleted within 30 days after you disconnect a calendar or delete your account</li>
    <li><strong>Technical logs:</strong> System logs for security and error tracking are retained for up to 90 days. These logs do not include calendar event content, only technical metadata</li>
    <li><strong>Billing records:</strong> Invoices and payment records are retained for 10 years to comply with Czech accounting and tax law requirements</li>
    <li><strong>Support communications:</strong> Retained for up to 3 years for quality assurance and legal protection purposes</li>
    <li><strong>Anonymized analytics:</strong> Aggregate anonymized usage statistics may be retained indefinitely as they cannot identify individuals</li>
</ul>

<h2>Third-Party Service Providers and Data Processors</h2>
<p>We work with carefully selected third-party service providers who process data on our behalf under strict data processing agreements (DPAs). We do not sell your data to third parties. Our processors include:</p>
<ul>
    <li><strong>Hosting providers:</strong> Infrastructure providers that host our application and databases in secure data centers. They have access to encrypted data at rest but cannot decrypt calendar content without our encryption keys</li>
    <li><strong>Payment processor (Stripe):</strong> Handles payment processing and subscription management. Stripe processes your payment card details directly; we never store full card numbers</li>
    <li><strong>Email service providers:</strong> Transactional email delivery for account notifications, password resets, and support communications</li>
    <li><strong>Monitoring and error tracking:</strong> Services that help us monitor application performance and diagnose technical issues. These receive minimal technical metadata only</li>
    <li><strong>Calendar service providers:</strong> Google, Microsoft, Apple, and CalDAV server operators process your calendar data when you explicitly connect these services for synchronization</li>
</ul>
<p>All third-party processors:</p>
<ul>
    <li>Are bound by data processing agreements ensuring GDPR compliance</li>
    <li>Process data only on our instructions and for specified purposes</li>
    <li>Implement appropriate technical and organizational security measures</li>
    <li>Do not use your data for their own purposes</li>
    <li>Must notify us of any data breaches</li>
</ul>

<h2>International Data Transfers</h2>
<p>Some of our service providers may process data outside the European Economic Area (EEA). When data is transferred to third countries, we ensure adequate protection through:</p>
<ul>
    <li><strong>Adequacy decisions:</strong> We prioritize providers in countries recognized by the European Commission as providing adequate data protection (e.g., UK, Switzerland, countries covered by Privacy Shield successor frameworks)</li>
    <li><strong>Standard Contractual Clauses (SCCs):</strong> For transfers to other countries, we use EU-approved Standard Contractual Clauses ensuring the same level of protection as within the EU</li>
    <li><strong>Additional safeguards:</strong> We implement technical measures such as encryption in transit and at rest, access controls, and regular security audits</li>
</ul>
<p>Our main data processing occurs within the EU. Specific international transfers include:</p>
<ul>
    <li><strong>Hosting infrastructure:</strong> Primary servers located in the EU; backup systems may be in countries with adequacy decisions</li>
    <li><strong>Calendar services:</strong> When you connect Google, Microsoft, or Apple calendars, your calendar data is synchronized with servers operated by these providers in their respective jurisdictions according to their privacy policies</li>
    <li><strong>Payment processing:</strong> Stripe processes payments globally but maintains GDPR compliance through appropriate safeguards</li>
</ul>

<h2>Your Privacy Rights</h2>
<p>Under GDPR, you have the following rights regarding your personal data:</p>
<ul>
    <li><strong>Right of access:</strong> Request a copy of all personal data we hold about you</li>
    <li><strong>Right to rectification:</strong> Correct inaccurate or incomplete personal data</li>
    <li><strong>Right to erasure ("right to be forgotten"):</strong> Request deletion of your personal data</li>
    <li><strong>Right to restriction:</strong> Limit how we process your data in certain circumstances</li>
    <li><strong>Right to data portability:</strong> Receive your data in a structured, machine-readable format and transfer it to another service</li>
    <li><strong>Right to object:</strong> Object to processing based on legitimate interests or for direct marketing</li>
    <li><strong>Right to withdraw consent:</strong> Withdraw consent at any time where processing is based on consent (does not affect prior processing)</li>
    <li><strong>Right to lodge a complaint:</strong> File a complaint with a supervisory authority (in the Czech Republic: <a href="https://www.uoou.cz" target="_blank" rel="noopener">ÚOOÚ</a>)</li>
    <li><strong>Right to information about automated decision-making:</strong> We do not make decisions based solely on automated processing that significantly affects you</li>
    <li><strong>Right to be informed about data breaches:</strong> We will notify you if a data breach affects your rights and freedoms</li>
</ul>

<h2>How to Exercise Your Rights</h2>
<p>You can exercise your rights through the following methods:</p>

<h3>Self-Service Options</h3>
<ul>
    <li><strong>Delete your account:</strong> Go to Account Settings → Delete Account. This will permanently delete all your data within 30 days</li>
    <li><strong>Disconnect calendars:</strong> Go to Calendar Connections → Disconnect. OAuth tokens and sync data will be deleted within 30 days</li>
    <li><strong>Export your data:</strong> Go to Account Settings → Export Data to download your sync rules and configuration</li>
    <li><strong>Update your information:</strong> Edit your profile and account settings directly in the application</li>
</ul>

<h3>Contact Us for Assistance</h3>
<p>For other privacy requests or questions, contact us at:</p>
<ul>
    <li><strong>Email:</strong> <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></li>
    <li><strong>Subject line:</strong> "Privacy Request - [Your Request Type]"</li>
    <li><strong>Include:</strong> Your registered email address and a description of your request</li>
</ul>
<p>We will respond to your request within 30 days as required by GDPR. For complex requests, we may extend this period by an additional 60 days and will inform you of the extension.</p>

<h2>Security Measures</h2>
<p>We implement comprehensive technical and organizational security measures to protect your personal data:</p>
<ul>
    <li><strong>Encryption:</strong> AES-256 encryption for data at rest; TLS/SSL for data in transit</li>
    <li><strong>Access controls:</strong> Role-based access restrictions; principle of least privilege</li>
    <li><strong>Authentication security:</strong> Secure password hashing; OAuth 2.0 for calendar connections</li>
    <li><strong>Infrastructure security:</strong> Professionally managed data centers with physical security; regular security updates and patches</li>
    <li><strong>Monitoring:</strong> 24/7 security monitoring and intrusion detection</li>
    <li><strong>Backup and recovery:</strong> Regular encrypted backups with secure retention</li>
    <li><strong>Incident response:</strong> Documented procedures for security incident handling</li>
</ul>

<h2>Contact Information</h2>
<p><strong>Data Controller:</strong> Lukas Slehofer, Kurzova 2222/16, 155 00 Prague 5, Czech Republic, VAT ID: CZ7912150191</p>
<p><strong>Privacy inquiries:</strong> <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></p>
<p><strong>General support:</strong> <a href="mailto:support@syncmyday.eu">support@syncmyday.eu</a></p>

<h2>Changes to This Privacy Policy</h2>
<p>We may update this Privacy Policy from time to time to reflect changes in our practices, technology, legal requirements, or other factors.</p>
<p><strong>How we notify you of changes:</strong></p>
<ul>
    <li><strong>Material changes:</strong> We will notify you via email and/or a prominent notice in the application at least 30 days before the changes take effect</li>
    <li><strong>Minor changes:</strong> We will update the "Last updated" date at the top of this policy</li>
    <li><strong>Your acceptance:</strong> Continued use of {{ config('app.name') }} after the effective date constitutes acceptance of the updated policy</li>
    <li><strong>Review recommended:</strong> We encourage you to review this policy periodically</li>
</ul>
<p>If you do not agree with changes to this policy, you may delete your account before the changes take effect.</p>

