# Setup Emailových Kalendářů - Konkrétní Kroky

## ✅ Co je již hotové

1. **IMAP Polling Command**: `php artisan app:process-inbound-emails`
2. **Cron URL Script**: `public/cron-inbound-emails.php`
3. **Webhook Endpointy**:
   - `/webhooks/email/mailgun`
   - `/webhooks/email/sendgrid`
   - `/webhooks/email/postmark`
4. **Konfigurace**: `config/inbound_email.php`

## 📋 Co je třeba udělat na produkčním serveru

### 1. Nastavit EMAIL_DOMAIN v .env

V produkčním `.env` změnit:

```bash
# BYLO:
EMAIL_DOMAIN=syncmyday.local

# MÁ BÝT:
EMAIL_DOMAIN=syncmyday.cz
```

### 2. Ověřit IMAP konfiguraci

V produkčním `.env` už máte nastaveno:

```bash
INBOUND_EMAIL_ENABLED=true
INBOUND_EMAIL_HOST=imap.cesky-hosting.cz
INBOUND_EMAIL_PORT=993
INBOUND_EMAIL_USERNAME=events@syncmyday.cz
INBOUND_EMAIL_PASSWORD=hT6De901pX
INBOUND_EMAIL_ENCRYPTION=ssl
INBOUND_EMAIL_MAILBOX=INBOX
INBOUND_EMAIL_PROCESSED_FOLDER=Processed
```

✅ To vypadá dobře!

### 3. Nastavit Catch-all forwarding

V administraci cesky-hosting.cz:

1. Najděte nastavení emailů pro doménu `syncmyday.cz`
2. Nastavte catch-all forwarding:
   - Všechny emaily na `*@syncmyday.cz` přeposílat na `events@syncmyday.cz`
   - Nebo nastavte alias `*@syncmyday.cz` → `events@syncmyday.cz`

**Proč**: Když někdo pošle email na `abc12345@syncmyday.cz`, server ho přepošle do schránky `events@syncmyday.cz`, odkud ho aplikace zpracuje.

### 4. Nastavit Cron Job

V administraci webhostingu (nebo crontabu):

**URL pro cron:**

```
https://syncmyday.cz/cron-inbound-emails.php?token=VÁŠ_CRON_SECRET
```

**Nebo curl příkaz:**

```bash
* * * * * curl -s "https://syncmyday.cz/cron-inbound-emails.php?token=VÁŠ_CRON_SECRET" > /dev/null 2>&1
```

**Nebo wget příkaz:**

```bash
* * * * * wget -q -O - "https://syncmyday.cz/cron-inbound-emails.php?token=VÁŠ_CRON_SECRET" > /dev/null 2>&1
```

**Nahraďte `VÁŠ_CRON_SECRET`** hodnotou z `.env` proměnné `CRON_SECRET`.

**Poznámka**: Script je optimalizován pro shared hosting (nepotřebuje `proc_open`), takže funguje i s omezeními hostingu.

### 5. Clear Cache na serveru

Po změnách v `.env`:

```bash
php artisan config:cache
```

### 6. Otestovat

#### Test 1: Vytvoření emailového kalendáře

1. Přihlaste se do aplikace
2. Jděte na `/connections`
3. Klikněte na "Email Calendar"
4. Vytvořte nový emailový kalendář
5. Zkopírujte vygenerovanou email adresu (např. `abc12345@syncmyday.cz`)

#### Test 2: Poslání testovacího .ics souboru

1. Vytvořte jednoduchý .ics soubor:

```ics
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Test//Test//EN
METHOD:REQUEST
BEGIN:VEVENT
UID:test-event-123@syncmyday.cz
DTSTAMP:20251010T120000Z
DTSTART:20251015T140000Z
DTEND:20251015T150000Z
SUMMARY:Test Meeting
DESCRIPTION:Test event for calendar sync
STATUS:CONFIRMED
END:VEVENT
END:VCALENDAR
```

2. Pošlete tento soubor jako přílohu na email na vygenerovanou adresu
3. Počkejte 1 minutu (až se spustí cron)
4. Zkontrolujte logy:
   ```bash
   tail -f storage/logs/laravel.log
   tail -f storage/logs/sync.log
   ```

#### Test 3: Ověření sync rules

1. Vytvořte sync rule:
   - Source: Email kalendář
   - Target: Váš Google nebo Microsoft kalendář
2. Pošlete .ics na email adresu
3. Zkontrolujte, zda se v cílovém kalendáři objevil blocker

### 7. Monitoring

**Důležité logy na kontrolu:**

```bash
# Zobrazit poslední zpracované emaily
grep "Inbound email processing" storage/logs/laravel.log | tail -20

# Zobrazit chyby při zpracování
grep "IMAP" storage/logs/laravel.log | grep "error"

# Sync logy
tail -50 storage/logs/sync.log
```

**Manuální spuštění pro debug:**

```bash
# SSH na server, pak:
php artisan app:process-inbound-emails --dry-run
php artisan app:process-inbound-emails --limit=5
```

## 🔧 Možné problémy a řešení

### Problém: "proc_open not available"

**Příčina:** Shared hosting má zakázanou funkci `proc_open` z bezpečnostních důvodů.

**Řešení:** ✅ **VYŘEŠENO** - `public/cron-inbound-emails.php` byl přepsán tak, aby **nepotřeboval** `proc_open`. Spouští IMAP polling přímo bez artisan commandu.

Stačí použít URL nebo curl/wget v cronu:

```
https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_SECRET
```

### Problém: "IMAP connection failed"

**Možné příčiny:**

- Špatné heslo
- Firewall blokuje port 993
- SSL certifikát problém

**Řešení:**

1. Ověřte credentials v `.env`
2. Zkuste `INBOUND_EMAIL_VALIDATE_CERT=false` (ne doporučeno pro produkci)
3. Zkontrolujte, zda server umožňuje odchozí spojení na port 993

### Problém: "No valid recipient found"

**Příčina:** Email nebyl poslán na adresu `*@syncmyday.cz`, nebo catch-all není správně nastaven.

**Řešení:**

1. Ověřte catch-all nastavení
2. Zkuste poslat email přímo na `events@syncmyday.cz` a zkontrolujte, zda dorazí

### Problém: "No valid recipient found" nebo "Email calendar not found for token"

**Příčiny:**
1. Token v email adrese neodpovídá žádnému emailovému kalendáři v databázi
2. ~~Case sensitivity problém s velkými/malými písmeny~~ ✅ **VYŘEŠENO** (od verze 3ec50743)

**Řešení:**

- Zkontrolujte databázovou tabulku `email_calendar_connections`
- Ověřte, že `EMAIL_DOMAIN` je správně nastaveno (`syncmyday.cz`)
- Od října 2025 jsou všechny tokeny generovány jako lowercase (např. `abc12345@syncmyday.cz`)
- Staré tokeny s velkými písmeny budou automaticky převedeny migrací na lowercase

### Problém: Emaily se zpracovávají, ale blockery se nevytvářejí

**Možné příčiny:**

- Není vytvořeno sync rule
- Target kalendář není správně připojený
- .ics soubor je nevalidní

**Řešení:**

1. Zkontrolujte sync rules v databázi
2. Ověřte logy: `grep "iMIP\|blocker" storage/logs/sync.log`

## 📊 Statistiky

Po úspěšném nastavení můžete sledovat:

1. **V aplikaci**: `/email-calendars` → Detail kalendáře

   - Kolik emailů bylo přijato
   - Kolik eventů bylo zpracováno
   - Poslední email

2. **V logách**:

   ```bash
   # Kolikrát za den se spouští cron
   grep "Inbound email processing completed" storage/logs/laravel.log | grep "$(date +%Y-%m-%d)" | wc -l

   # Úspěšnost
   grep "Processed:" storage/logs/laravel.log | tail -20
   ```

## 🎯 Shrnutí

1. ✅ Změnit `EMAIL_DOMAIN=syncmyday.cz`
2. ✅ Nastavit catch-all forwarding na `events@syncmyday.cz`
3. ✅ Přidat cron job s URL: `https://syncmyday.cz/cron-inbound-emails.php?token=SECRET`
4. ✅ Spustit `php artisan config:cache`
5. ✅ Otestovat odesláním .ics na vygenerovanou adresu
6. ✅ Sledovat logy

**Hotovo!** 🎉

Emailové kalendáře by teď měly fungovat. Pokud něco nefunguje, zkontrolujte logy a dejte vědět.
