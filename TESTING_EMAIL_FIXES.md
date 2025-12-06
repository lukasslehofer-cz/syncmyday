# Testovací Scénáře - Email System Fixes

## Před testováním

### 1. Spusť migraci

```bash
php artisan migrate
```

nebo v produkci:

```bash
php artisan migrate --force
```

### 2. Ověř scheduler

Ujisti se, že cron běží:

```bash
php artisan schedule:list
```

## Test Scénáře

### ✅ Scénář 1: Registrace přes Email/Password

**Kroky:**

1. Jdi na `/register`
2. Vyplň formulář s email/heslem
3. Odešli registraci

**Očekávaný výsledek:**

- ✓ User je vytvořen s `email_verified_at = NOW()` (auto-verify pro lepší UX)
- ✓ Welcome email byl odeslán (zkontroluj logy)
- ✓ User je přesměrován na `/onboarding/start`
- ✓ User může okamžitě začít používat aplikaci bez čekání

**Ověření v DB:**

```sql
SELECT email, email_verified_at, created_at FROM users WHERE email = 'test@example.com';
-- email_verified_at by měl být nastaven okamžitě
```

---

### ✅ Scénář 2: Registrace přes OAuth (Google/Microsoft)

**Kroky:**

1. Jdi na `/login`
2. Klikni na "Continue with Google"
3. Autorizuj aplikaci

**Očekávaný výsledek:**

- ✓ User je vytvořen s `email_verified_at = NOW()` (auto-verify)
- ✓ Žádný verification email není odeslán
- ✓ User je přesměrován přímo na onboarding
- ✓ Stejné chování jako email/password registrace

**Ověření v DB:**

```sql
SELECT email, email_verified_at, oauth_provider FROM users WHERE email = 'oauth@example.com';
```

---

### ⚠️ Scénář 3: Email Verification - VYPNUTO

**Poznámka:** Email verification není aktivní. Všichni uživatelé mají email automaticky ověřený pro lepší UX trial aplikace.

**Verification routes (připraveny pro budoucí použití):**

- `GET /email/verify` - verification notice page
- `GET /email/verify/{id}/{hash}` - verification link
- `POST /email/verification-notification` - resend email

**Tyto routes existují v kódu, ale nejsou aktivně používány.**

---

### ✅ Scénář 4: Trial končí dnes - První email

**Příprava:**

```sql
-- Vytvoř testovacího usera s trialem končícím dnes
UPDATE users
SET subscription_ends_at = NOW(),
    stripe_subscription_id = NULL,
    trial_expired_email_sent_at = NULL
WHERE email = 'test@example.com';
```

**Kroky:**

1. Spusť command: `php artisan trial:expire`

**Očekávaný výsledek:**

- ✓ Trial expired email byl odeslán (zkontroluj logy)
- ✓ `trial_expired_email_sent_at` je nastaven
- ✓ `trial_expired_reminder_sent_at` je NULL

**Ověření v DB:**

```sql
SELECT email, trial_expired_email_sent_at, trial_expired_reminder_sent_at
FROM users WHERE email = 'test@example.com';
```

**Důležité:**

- ✓ Pokud spustíš command znovu DRUHÝ DEN, email se NEPOŠLE (již byl poslán)

---

### ✅ Scénář 5: Trial skončil před 5 dny - Reminder email

**Příprava:**

```sql
-- Nastavit initial email jako odeslaný před 5 dny
UPDATE users
SET subscription_ends_at = NOW() - INTERVAL 5 DAY,
    trial_expired_email_sent_at = NOW() - INTERVAL 5 DAY,
    trial_expired_reminder_sent_at = NULL,
    stripe_subscription_id = NULL
WHERE email = 'test@example.com';
```

**Kroky:**

1. Spusť command: `php artisan trial:expire`

**Očekávaný výsledek:**

- ✓ Reminder email byl odeslán (zkontroluj logy)
- ✓ `trial_expired_reminder_sent_at` je nastaven

**Ověření v DB:**

```sql
SELECT email, trial_expired_email_sent_at, trial_expired_reminder_sent_at
FROM users WHERE email = 'test@example.com';
```

**Důležité:**

- ✓ Pokud spustíš command znovu, reminder se NEPOŠLE (již byl poslán)

---

### ✅ Scénář 6: Trial skončil před 10 dny - Žádný email

**Příprava:**

```sql
-- Nastavit oba emaily jako odeslané
UPDATE users
SET subscription_ends_at = NOW() - INTERVAL 10 DAY,
    trial_expired_email_sent_at = NOW() - INTERVAL 10 DAY,
    trial_expired_reminder_sent_at = NOW() - INTERVAL 5 DAY,
    stripe_subscription_id = NULL
WHERE email = 'test@example.com';
```

**Kroky:**

1. Spusť command: `php artisan trial:expire`

**Očekávaný výsledek:**

- ✓ ŽÁDNÝ email není odeslán
- ✓ V logu je: "Found 0 users needing initial trial expired email"
- ✓ V logu je: "Found 0 users needing 5-day reminder email"

---

### ✅ Scénář 7: Grace Period končí - Subscription Suspended

**Příprava:**

```sql
-- Nastavit grace period končící dnes
UPDATE users
SET grace_period_ends_at = NOW(),
    subscription_tier = 'pro'
WHERE email = 'test@example.com';
```

**Kroky:**

1. Spusť command: `php artisan grace-period:check`

**Očekávaný výsledek:**

- ✓ Subscription suspended email byl odeslán
- ✓ `grace_period_ends_at` je nastaven na NULL
- ✓ User má stále `subscription_tier = 'pro'` (soft-lock)

**Ověření:**

```sql
SELECT email, grace_period_ends_at, subscription_tier
FROM users WHERE email = 'test@example.com';
```

---

### ✅ Scénář 8: Email Calendar s ověřeným emailem

**Příprava:**

- User má ověřený email (`email_verified_at` is NOT NULL)

**Kroky:**

1. Jdi na email calendars
2. Přidej nový email calendar s STEJNÝM emailem jako má user registrovaný
3. Odešli formulář

**Očekávaný výsledek:**

- ✓ Email calendar je vytvořen
- ✓ `target_email_verified_at` je OKAMŽITĚ nastaven (auto-verify)
- ✓ ŽÁDNÝ verification email není odeslán
- ✓ V logu je: "Email calendar target email auto-verified (matches user email)"

---

## Ověření Scheduleru

Zkontroluj, že všechny commands jsou správně naplánované:

```bash
php artisan schedule:list
```

**Očekávaný výstup by měl obsahovat:**

- `trial:send-ending-notifications` - Daily at 09:00
- `trial:expire` - Daily at 00:00
- `grace-period:check` - Daily at 01:00
- `onboarding:send-emails` - Daily at 10:00

---

## Manuální Testování Emailů

Pro manuální testování všech emailů použij:

```bash
# Test konkrétního emailu
php artisan test:email your-email@example.com --type=trial-expired

# Test všech emailů najednou
php artisan test:email your-email@example.com --all
```

**Dostupné typy:**

- `welcome`
- `trial-7` (3 days before end)
- `trial-1` (1 day before end)
- `trial-expired`
- `payment-success`
- `renewal-reminder`
- `subscription-suspended`
- `account-deleted`
- `onboarding-calendar`
- `onboarding-rules`
- `onboarding-upgrade`
- `password-reset`

---

## Checklist - Kompletní Ověření

### Trial Expired Email Fix

- [ ] Initial email se pošle jen jednou
- [ ] Reminder se pošle po 5 dnech
- [ ] Po reminderu se už žádný email nepošle
- [ ] Tracking sloupce jsou správně nastaveny v DB

### Email Auto-Verify (Email Verification je vypnuto)

- [ ] Email/password registrace má email okamžitě ověřený (email_verified_at nastaven)
- [ ] OAuth registrace má email okamžitě ověřený (email_verified_at nastaven)
- [ ] Uživatelé jsou přesměrováni na onboarding, ne na verification notice
- [ ] Uživatelé mohou okamžitě začít používat aplikaci
- [ ] Email calendar auto-verify funguje (protože user email je vždy ověřený)

### Scheduler

- [ ] `grace-period:check` je v scheduleru
- [ ] Všechny commands běží ve správný čas
- [ ] Cron běží (produkce)

---

## Řešení Problémů

### Email se neposílá

1. Zkontroluj `.env` mail nastavení
2. Zkontroluj logy: `tail -f storage/logs/laravel.log`
3. Zkontroluj mail queue (pokud používáš queues)

### Migrace nelze spustit (production)

```bash
php artisan migrate --force
```

### Command nenajde uživatele

Zkontroluj query v DB manuálně:

```sql
-- Pro initial email
SELECT * FROM users
WHERE subscription_tier = 'pro'
  AND subscription_ends_at <= NOW()
  AND stripe_subscription_id IS NULL
  AND trial_expired_email_sent_at IS NULL;

-- Pro reminder
SELECT * FROM users
WHERE trial_expired_email_sent_at BETWEEN
  DATE_SUB(NOW(), INTERVAL 5 DAY + INTERVAL 12 HOUR) AND
  DATE_SUB(NOW(), INTERVAL 5 DAY - INTERVAL 12 HOUR)
  AND trial_expired_reminder_sent_at IS NULL
  AND stripe_subscription_id IS NULL;
```

---

## Výsledek

Po úspěšném otestování všech scénářů můžeš označit implementaci jako **COMPLETE** ✅
