# Nastavení emailových kalendářů

## Problémy, které je třeba vyřešit

### 1. Emailová doména (.local na produkci)

**Problém**: Emailová adresa generovaná pro uživatele má doménu `syncmyday.local` i na produkčním serveru, protože v `.env` není nastavena proměnná `EMAIL_DOMAIN`.

**Řešení**: Do `.env` na produkčním serveru přidat:

```bash
EMAIL_DOMAIN=syncmyday.com
```

Po změně restartovat aplikaci:

```bash
php artisan config:cache
php artisan queue:restart  # pokud používáte queue
```

### 2. Příjem emailů na vygenerované adresy

**Problém**: Vygenerované adresy typu `abc12345@syncmyday.com` musí být schopné přijímat emaily a předávat je aplikaci.

**Možná řešení**:

#### A) Catch-all email forwarding + IMAP polling ✅ IMPLEMENTOVÁNO

1. Na mail serveru nastavit catch-all rule pro doménu (např. `*@syncmyday.com`)
2. Všechny příchozí emaily přeposílat na jediný mailbox
3. Laravel Command `php artisan app:process-inbound-emails`:
   - Pravidelně (cron každou minutu) kontroluje mailbox přes IMAP
   - Pro každý email parsuje příjemce (např. `abc12345@syncmyday.com`)
   - Extrahuje token (`abc12345`)
   - Volá `EmailCalendarSyncService::processIncomingEmail($token, $rawEmail)`

**Nastavení v `.env`**:

```bash
INBOUND_EMAIL_ENABLED=true
INBOUND_EMAIL_HOST=imap.mailgun.org
INBOUND_EMAIL_PORT=993
INBOUND_EMAIL_USERNAME=your-mailbox@syncmyday.com
INBOUND_EMAIL_PASSWORD=your-password
INBOUND_EMAIL_ENCRYPTION=ssl
INBOUND_EMAIL_MAILBOX=INBOX
INBOUND_EMAIL_PROCESSED_FOLDER=Processed
```

**Přidat do crontabu**:

```bash
* * * * * cd /path/to/syncmyday && php artisan app:process-inbound-emails >> /dev/null 2>&1
```

**Test příkazu**:

```bash
php artisan app:process-inbound-emails --dry-run
php artisan app:process-inbound-emails --limit=5
```

#### B) Mail webhook (pokud to poskytovatel podporuje) ✅ IMPLEMENTOVÁNO

Poskytovatelé jako Mailgun, SendGrid, Postmark umožňují webhook, který pošle POST request s obsahem emailu.

**Dostupné webhooky**:

- `POST /webhooks/email/mailgun` - pro Mailgun
- `POST /webhooks/email/sendgrid` - pro SendGrid
- `POST /webhooks/email/postmark` - pro Postmark

**Nastavení v Mailgun**:

1. V Mailgun dashboardu jděte do: Sending → Domains → Routes
2. Vytvořte novou route:
   - Priority: 10
   - Expression: `match_recipient(".*@syncmyday.com")`
   - Actions: `forward("https://syncmyday.com/webhooks/email/mailgun")`
   - Description: "SyncMyDay Calendar Emails"
3. Uložte

**Nastavení v SendGrid**:

1. Settings → Inbound Parse
2. Add Host & URL:
   - Subdomain: `calendar`
   - Domain: `syncmyday.com`
   - Destination URL: `https://syncmyday.com/webhooks/email/sendgrid`
   - Check spam: No

**Nastavení v Postmark**:

1. Servers → Inbound
2. Add Inbound Stream
3. Set webhook URL: `https://syncmyday.com/webhooks/email/postmark`

#### C) Email pipes (pokud máte vlastní server)

Nastavit `.forward` nebo mail alias, který přepošle emaily přímo do PHP scriptu.

### 3. Odesílání emailů (iMIP blockery)

**Problém**: Aplikace odesílá iMIP invitations přes `ImipEmailService`, ale emaily se možná neposílají.

**Co zkontrolovat v `.env`**:

```bash
# Mail driver
MAIL_MAILER=smtp  # nebo mailgun, sendgrid, postmark, ses

# SMTP nastavení (pokud používáte SMTP)
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=noreply@syncmyday.com
MAIL_FROM_NAME="SyncMyDay"
```

**Testování odchozích emailů**:

```bash
php artisan tinker
```

```php
use Illuminate\Support\Facades\Mail;

Mail::raw('Test email', function ($message) {
    $message->to('your-email@example.com')
        ->subject('Test from SyncMyDay');
});
```

### 4. Kdy se posílají iMIP emaily

Emaily se posílají v těchto situacích:

1. **API kalendář → Email target**:

   - Když se vytvoří/změní/smaže událost v Google/Microsoft kalendáři
   - A existuje sync rule, kde je tento kalendář jako source
   - A target je email kalendář s nastaveným `target_email`
   - Proces: Webhook → SyncEngine → ImipEmailService

2. **Email kalendář → API target**:
   - Když příjde email s .ics attachmentem na vygenerovanou adresu
   - EmailCalendarSyncService parsuje .ics a vytváří blockery v target kalendářích

## Kontrolní seznam pro nasazení

### Na lokálním vývojovém prostředí:

- [x] ✅ Implementovat příjem emailů (catch-all + IMAP polling nebo webhook)
- [x] ✅ Implementovat odesílání blockerů do email targetů (iMIP)
- [x] ✅ Vytvořit webhook endpointy pro Mailgun/SendGrid/Postmark
- [x] ✅ Nainstalovat `webklex/php-imap` balíček (nepotřebuje PHP IMAP extension)
- [x] ✅ Vytvořit `public/cron-inbound-emails.php` pro spuštění přes URL
- [x] ✅ Vytvořit podrobný setup guide: `SETUP_EMAILOVE_KALENDARE.md`

### Na produkčním serveru:

- [ ] Nastavit `EMAIL_DOMAIN=syncmyday.com` v `.env` (aktuálně: `syncmyday.local`)
- [ ] Nakonfigurovat SMTP nebo mail service v `.env`:
  ```bash
  MAIL_MAILER=mailgun  # nebo smtp, sendgrid, postmark
  MAILGUN_DOMAIN=syncmyday.com
  MAILGUN_SECRET=...
  MAIL_FROM_ADDRESS=noreply@syncmyday.com
  ```
- [ ] Vybrat metodu příjmu emailů:
  - [ ] **Option A**: IMAP polling (jednodušší setup)
    - Nastavit catch-all forwarding v mail serveru
    - Nakonfigurovat IMAP credentials v `.env`
    - Přidat cron job: `* * * * * php artisan app:process-inbound-emails`
  - [ ] **Option B**: Webhook (rychlejší, real-time)
    - Nastavit route v Mailgun/SendGrid/Postmark
    - URL: `https://syncmyday.com/webhooks/email/mailgun`
- [ ] Otestovat odchozí emaily:
  ```bash
  php artisan tinker
  Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));
  ```
- [ ] Otestovat příchozí emaily:
  - Vytvořit email kalendář v aplikaci → získat adresu (např. `abc12345@syncmyday.com`)
  - Poslat .ics soubor na tuto adresu
  - Zkontrolovat logy a ověřit vytvoření blockeru
- [ ] Spustit `php artisan config:cache` po změnách v `.env`
- [ ] Zkontrolovat logy: `storage/logs/laravel.log` a `storage/logs/sync.log`

## Doporučený mail provider pro produkci

### Option 1: Mailgun (nejlepší pro transactional + inbound)

- ✅ Podporuje inbound email webhooks
- ✅ Dobrá cena pro transactional emaily
- ✅ Laravel má built-in podporu
- ✅ Catch-all routing

Nastavení v `.env`:

```bash
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=syncmyday.com
MAILGUN_SECRET=your-api-key
MAILGUN_ENDPOINT=https://api.mailgun.net
```

### Option 2: Postmark (nejlepší deliverability)

- ✅ Výborná deliverability
- ✅ Podporuje inbound webhooks
- ❌ Dražší než Mailgun

### Option 3: SendGrid

- ✅ Populární, široce používaný
- ✅ Podporuje inbound parse
- ⚠️ Laravel nemá built-in driver (potřeba package)

## Debug příkazy

```bash
# Zkontrolovat konfiguraci
php artisan config:show app | grep email_domain
php artisan config:show mail

# Test emailového parseru (lokálně)
php artisan app:process-test-email <token> <path-to-eml-file>

# Zobrazit logy
tail -f storage/logs/laravel.log
tail -f storage/logs/sync.log

# Manuálně spustit sync (pokud webhooks nefungují)
php artisan app:sync-calendars
```

## Poznámky k implementaci

1. **ImipEmailService** (`app/Services/Email/ImipEmailService.php`) - odesílá iMIP invitations
2. **EmailCalendarSyncService** (`app/Services/Email/EmailCalendarSyncService.php`) - zpracovává příchozí emaily
3. **SyncEngine** (`app/Services/Sync/SyncEngine.php`) - koordinuje synchronizaci včetně email targetů

Kód je připravený a funkční - chybí pouze:

1. Správná konfigurace `.env` proměnných
2. Implementace příjmu emailů (IMAP polling nebo webhook)
