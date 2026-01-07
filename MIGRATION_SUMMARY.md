# Migrace Mailgun → MXroute - Souhrn

**Datum migrace:** 2026-01-07  
**Provedené změny:** Kompletní migrace z Mailgun na MXroute s rate limitingem

## ✅ Co bylo provedeno

### 1. Záloha Mailgun konfigurace
- ✅ Vytvořen backup: `env-configs/MAILGUN_BACKUP.md`
- ✅ Git tag: `mailgun-config-backup`
- ✅ Mailgun kód zachován (zakomentován) pro snadný rollback

### 2. Migrace na MXroute
- ✅ `app/Helpers/EmailHelper.php` - events@ používá MXroute
- ✅ `config/inbound_email.php` - IMAP host změněn na MXroute
- ✅ `config/mail.php` - Mailgun mailer zakomentován
- ✅ `config/services.php` - Mailgun service zakomentován
- ✅ `routes/api.php` + `routes/web.php` - Mailgun webhooks zakomentovány

### 3. Rate Limiting Implementace
- ✅ `app/Jobs/SendCalendarBlockerEmail.php` - Queue job s rate limitingem
- ✅ `app/Services/Email/ImipEmailService.php` - Používá queue místo přímého odesílání
- ✅ `config/mail.php` - Rate limit config (300/hodinu)
- ✅ Rate limiting per mailbox (events@domain má vlastní limit)
- ✅ Automatický retry s backoff strategií

### 4. Dokumentace
- ✅ `env-configs/mxroute-only.txt` - Kompletní env konfigurace
- ✅ `env-configs/RATE_LIMITING.md` - Detailní rate limiting dokumentace
- ✅ `MIGRATION_SUMMARY.md` - Tento souhrn

## 🎯 Klíčové změny

### Před (Mailgun)
```
Odchozí: events@ → Mailgun SMTP (smtp.mailgun.org)
Příchozí: Mailgun webhooks (real-time)
Limit: 100,000/měsíc free tier
Náklady: $35+/měsíc po překročení
```

### Po (MXroute + Queue)
```
Odchozí: events@ → MXroute SMTP (bunny.mxroute.com)
Příchozí: MXroute IMAP polling (5 min interval)
Limit: 300/hodinu per mailbox (rate limited)
Náklady: $0 (používáme MXroute stejně)
```

## 📋 Co musíte udělat

### 1. Aktualizovat .env

```bash
# Odstranit nebo zakomentovat všechny MAILGUN_*
# MAILGUN_SMTP_HOST=...
# MAILGUN_SMTP_PORT=...
# ...atd

# Přidat:
QUEUE_CONNECTION=database
MAIL_RATE_LIMIT_PER_HOUR=300

INBOUND_EMAIL_ENABLED=true
INBOUND_EMAIL_HOST=bunny.mxroute.com
INBOUND_EMAIL_PORT=993
INBOUND_EMAIL_USERNAME=events@syncmyday.cz
INBOUND_EMAIL_PASSWORD="vaše_heslo"
INBOUND_EMAIL_ENCRYPTION=ssl
```

### 2. Vyčistit config cache

```bash
php artisan config:clear
```

### 3. Spustit queue worker

**Development:**
```bash
php artisan queue:work --tries=5 --timeout=60
```

**Production (Supervisor - doporučeno):**
```bash
# Vytvořit /etc/supervisor/conf.d/syncmyday-queue.conf
# (viz env-configs/RATE_LIMITING.md)

sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start syncmyday-queue:*
```

### 4. Testování

```bash
# Test odchozích emailů
php artisan app:test-email

# Test IMAP
php artisan app:process-inbound-emails --dry-run

# Monitor queue
php artisan queue:monitor
```

### 5. V MXroute DirectAdmin

Ověřit mailboxy:
- ✅ `events@syncmyday.cz` (a další domény)
- ✅ IMAP přístup povolen
- ✅ Hesla správně nastavená

### 6. V Mailgun (volitelné)

Můžete deaktivovat:
- ❌ Inbound Routes pro events@
- ❌ Webhook endpoints
- ❌ SMTP credentials (pokud už nepoužíváte)

## 🔍 Jak ověřit že to funguje

### Odchozí emaily
```bash
# V logách byste měli vidět:
tail -f storage/logs/laravel.log | grep "Calendar blocker email"

# Výstup:
# [INFO] Calendar blocker email sent via queue
#   from: events@syncmyday.cz
#   to: user@example.com
#   mailer: mxroute
```

### Rate limiting
```bash
# Počet emailů ve frontě
php artisan queue:monitor

# Rate limiter status
php artisan tinker
>>> RateLimiter::remaining('send-email:events@syncmyday.cz', 300);
# => 287  (zbývá 287 z 300)
```

### IMAP polling
```bash
# Scheduler by měl běžet každých 5 minut
php artisan schedule:list

# Output:
# 0 */5 * * * php artisan app:process-inbound-emails
```

## 📊 Rate Limiting Details

### Limity
- **MXroute maximum:** 400 emailů/hodinu per mailbox
- **Naše nastavení:** 300 emailů/hodinu (bezpečná rezerva)
- **Per mailbox:** Každý mailbox má vlastní počítadlo

### Multi-domain kapacita
```
events@syncmyday.cz  → 300/h
events@syncmyday.sk  → 300/h
events@syncmyday.pl  → 300/h
events@syncmyday.de  → 300/h
events@syncmyday.eu  → 300/h
────────────────────────────
CELKEM:              1500/h
```

### Co se stane při překročení?
1. Job se vrátí do fronty (není ztracen)
2. Retry s backoff: 5min, 10min, 15min, 20min, 25min
3. Po 5 pokusech → failed_jobs (manuální retry)

## ⚠️ Důležité poznámky

### Systémové vs. Calendar emaily

**Systémové emaily** (welcome, password reset, platby):
- ✅ Odesílají se **okamžitě** (synchronně)
- ✅ **Bez** rate limitingu (nízký objem)
- ✅ Z `info@domain` mailboxu

**Calendar blocker emaily**:
- ⏱️ Odesílají se **přes queue** (asynchronně)
- 🚦 **S** rate limitingem (300/h)
- 📧 Z `events@domain` mailboxu

### Queue worker

**MUSÍ BĚŽET** na produkci! Bez něj se calendar emaily neodešlou.

```bash
# Zkontrolovat
ps aux | grep "queue:work"

# Spustit
php artisan queue:work --tries=5
```

### Monitoring

Pravidelně kontrolujte:
```bash
# Failed jobs (mělo by být 0)
php artisan queue:failed

# Queue size (neměla by růst do nekonečna)
php artisan queue:monitor
```

## 🔄 Jak vrátit Mailgun?

Pokud by bylo potřeba:

1. Přečíst `env-configs/MAILGUN_BACKUP.md`
2. Nebo checkout git tag:
   ```bash
   git checkout mailgun-config-backup
   ```
3. Odkomentovat Mailgun config v `config/mail.php`, `config/services.php`
4. Odkomentovat routes v `routes/api.php`, `routes/web.php`
5. Změnit `EmailHelper.php` řádek 54: `$mailer = 'mailgun'`
6. Přidat MAILGUN_* proměnné do .env
7. Config clear + restart

## 📚 Další dokumentace

- **Kompletní rate limiting info:** `env-configs/RATE_LIMITING.md`
- **MXroute setup:** `env-configs/mxroute-only.txt`
- **Mailgun backup:** `env-configs/MAILGUN_BACKUP.md`

## ✨ Výhody migrace

✅ **Ušetřené náklady:** $35+/měsíc  
✅ **Vendor independence:** Neváže nás Mailgun  
✅ **Rate limiting:** Nikdy nepřekročíme MXroute limit  
✅ **Graceful degradation:** Emaily se zpozdí, ale neztratí  
✅ **Multi-domain scaling:** Automatické rozdělení zátěže  

## 🎉 Hotovo!

Migrace je kompletní. Stačí už jen:
1. Aktualizovat .env
2. Spustit queue worker
3. Testovat

---

**Poznámka:** Pokud máte jakékoliv problémy, vše je detailně zdokumentováno v `env-configs/RATE_LIMITING.md`.

