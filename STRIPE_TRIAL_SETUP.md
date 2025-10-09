# Stripe Trial & Subscription Setup Guide

Tento dokument obsahuje kompletní návod k nastavení 30denního trialu a ročního předplatného za 249 Kč v SyncMyDay.

## 📋 Co bylo implementováno

### 1. **Trial Flow**

- Po registraci má uživatel automaticky 30 dní trial s plným přístupem (Pro tier)
- Uživatel zadá platební kartu při registraci, ale žádné peníze se nestrhnou během trialu
- Po 30 dnech se automaticky strhne 249 Kč/rok
- Pokud uživatel nezadá platební kartu nebo ji zruší před koncem trialu, služba se deaktivuje

### 2. **Implementované komponenty**

#### **User Model** (`app/Models/User.php`)

- `isInTrial()` - kontrola, zda je uživatel v trialu
- `getRemainingTrialDays()` - počet zbývajících dní trialu
- `isTrialExpiringSoon()` - kontrola, zda trial končí do 3 dnů
- `expireTrial()` - deaktivace účtu po expiraci trialu

#### **AuthController** (`app/Http/Controllers/Auth/AuthController.php`)

- Po registraci se uživatel nastaví na Pro tier s `subscription_ends_at = now() + 30 days`
- Redirect na Stripe Checkout pro zadání platební karty s trialem

#### **BillingController** (`app/Http/Controllers/BillingController.php`)

- `createTrialCheckoutSession()` - Stripe Checkout s 30denním trialem
- `createCheckoutSession()` - Stripe Checkout bez trialu (pro expirované účty)
- Webhook handling pro `checkout.session.completed`, `customer.subscription.updated`, `invoice.payment_succeeded`

#### **CheckSubscription Middleware** (`app/Http/Middleware/CheckSubscription.php`)

- Kontroluje, zda má uživatel aktivní předplatné (včetně trialu)
- Pokud ne, přesměruje na billing stránku

#### **Trial Banner** (`resources/views/layouts/app.blade.php`)

- Zobrazuje zbývající dny trialu
- Upozornění, pokud trial brzy končí
- CTA pro nastavení platební karty

#### **Billing View** (`resources/views/billing/index.blade.php`)

- Různé stavy: aktivní trial, expirovaný trial, aktivní předplatné
- Správné texty o platební kartě a automatickém strhnutí

#### **Commands**

- `trial:send-ending-notifications` - posílá upozornění 3 a 1 den před koncem trialu
- `trial:expire` - automaticky deaktivuje účty s expirovaným trialem (spouští se denně)

#### **Cron Jobs** (`app/Console/Kernel.php`)

- `trial:send-ending-notifications` - denně v 9:00
- `trial:expire` - denně o půlnoci

---

## 🔧 Nastavení Stripe

### Krok 1: Získání Price ID

1. Přihlaste se do [Stripe Dashboard](https://dashboard.stripe.com/)
2. Jděte na **Products** → váš produkt
3. V sekci **Pricing** najděte cenu (např. "249 CZK per year")
4. Klikněte na cenu a zkopírujte **Price ID** (začíná `price_xxxx`)

**DŮLEŽITÉ:** Potřebujete Price ID, ne Product ID! Product ID začíná `prod_`, Price ID začíná `price_`.

### Krok 2: Nastavení Webhook

1. V Stripe Dashboard jděte na **Developers** → **Webhooks**
2. Klikněte **Add endpoint**
3. **Endpoint URL:** `https://vasedomena.cz/webhooks/stripe`
4. **Select events to listen to:**
   - `checkout.session.completed`
   - `customer.subscription.created`
   - `customer.subscription.updated`
   - `customer.subscription.deleted`
   - `invoice.payment_succeeded`
   - `invoice.payment_failed`
5. Klikněte **Add endpoint**
6. Po vytvoření klikněte na endpoint a odkryjte **Signing secret** (začíná `whsec_xxxx`)

### Krok 3: Nastavení .env souboru

Přidejte do `.env`:

```env
# Stripe Keys
STRIPE_KEY=pk_test_51SG7FUK7USwXxSbNCpts6z1ZTnVZ08QkFYRQ6dO7HaCgAVdIcjuVGRRcXTek6431rHUC4mxg5uLQ3QsGI0FIho3c00542qTQcd
STRIPE_SECRET=sk_test_YOUR_STRIPE_TEST_SECRET_KEY_HERE
STRIPE_WEBHOOK_SECRET=whsec_VÁŠE_WEBHOOK_SECRET_TADY
STRIPE_PRO_PRICE_ID=price_VÁŠE_PRICE_ID_TADY
```

**Pro production (live keys):**

- Změňte `pk_test_` → `pk_live_`
- Změňte `sk_test_` → `sk_live_`
- Vytvořte nový webhook endpoint v live mode a získejte nový webhook secret

---

## 🚀 Testování

### 1. Lokální testování

```bash
# Spusťte aplikaci
php artisan serve

# V druhém terminálu spusťte Stripe CLI pro webhooky
stripe listen --forward-to http://localhost:8000/webhooks/stripe

# Zkopírujte webhook signing secret z CLI outputu do .env
```

### 2. Test Flow

1. **Registrace:**
   - Zaregistrujte nový účet
   - Měli byste být přesměrováni na Stripe Checkout
2. **Stripe Checkout:**
   - Pro testování použijte testovací kartu: `4242 4242 4242 4242`
   - CVC: jakékoliv 3 číslice
   - Datum: jakékoliv budoucí datum
3. **Po úspěšné platbě:**

   - Měli byste být přesměrováni na onboarding
   - V dashboardu by se měl zobrazit trial banner
   - Zkontrolujte databázi: uživatel by měl mít:
     - `subscription_tier = 'pro'`
     - `subscription_ends_at = now() + 30 days`
     - `stripe_subscription_id = 'sub_xxxx'`

4. **Webhook testy:**

   ```bash
   # Test subscription.updated
   stripe trigger customer.subscription.updated

   # Test invoice.payment_succeeded
   stripe trigger invoice.payment_succeeded
   ```

### 3. Test expirace trialu

```bash
# Manuálně nastavte subscription_ends_at v minulosti
php artisan tinker
>>> $user = User::find(1);
>>> $user->update(['subscription_ends_at' => now()->subDay()]);

# Spusťte expire command
php artisan trial:expire

# Zkontrolujte, že uživatel je downgraded na 'free'
>>> $user->fresh()->subscription_tier
```

---

## 📊 Monitoring

### Logy ke kontrole:

```bash
# Stripe webhooks
tail -f storage/logs/laravel.log | grep "Stripe"

# Trial expiry
tail -f storage/logs/laravel.log | grep "Trial expired"

# Subscription updates
tail -f storage/logs/laravel.log | grep "Subscription"
```

### Důležité metriky:

1. **Active Trials:** Uživatelé v trial období

   ```sql
   SELECT COUNT(*) FROM users
   WHERE subscription_tier = 'pro'
   AND subscription_ends_at > NOW()
   AND stripe_subscription_id IS NULL;
   ```

2. **Expiring Soon:** Trialy končící do 3 dnů

   ```sql
   SELECT COUNT(*) FROM users
   WHERE subscription_tier = 'pro'
   AND subscription_ends_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 3 DAY)
   AND stripe_subscription_id IS NULL;
   ```

3. **Paid Subscribers:** Aktivní platící uživatelé
   ```sql
   SELECT COUNT(*) FROM users
   WHERE subscription_tier = 'pro'
   AND stripe_subscription_id IS NOT NULL;
   ```

---

## 🔄 Cron Setup (Production)

Přidejte do crontab:

```bash
* * * * * cd /path/to/syncmyday && php artisan schedule:run >> /dev/null 2>&1
```

Nebo v Dockeru přes `docker-compose.yml`:

```yaml
services:
  scheduler:
    image: your-app-image
    command: php artisan schedule:work
    depends_on:
      - db
      - redis
```

---

## 🐛 Troubleshooting

### Problém: Webhook signature verification failed

**Řešení:**

1. Zkontrolujte, že máte správný `STRIPE_WEBHOOK_SECRET` v `.env`
2. Pro lokální vývoj použijte `stripe listen --forward-to`
3. Pro production vytvořte nový webhook endpoint

### Problém: Trial není vytvořen po registraci

**Řešení:**

1. Zkontrolujte, že Price ID je správně nastaveno v `.env`
2. Zkontrolujte logy: `tail -f storage/logs/laravel.log`
3. Ověřte, že cena v Stripe má správnou měnu (CZK) a interval (yearly)

### Problém: Po expiraci trialu uživatel stále má přístup

**Řešení:**

1. Zkontrolujte, že command `trial:expire` běží v cronu
2. Manuálně spusťte: `php artisan trial:expire`
3. Zkontrolujte middleware `CheckSubscription`

---

## 📝 Poznámky

- **Měna:** Ujistěte se, že Price v Stripe je v CZK (ne EUR nebo USD)
- **Interval:** Price musí být yearly (roční)
- **Test vs Live:** Nezapomeňte změnit klíče při přechodu do production
- **Webhook URL:** Musí být veřejně dostupná (ne localhost) pro production
- **Cron:** Schedule musí běžet pro automatickou expiraci trials

---

## ✅ Checklist před spuštěním

- [ ] Price ID získáno ze Stripe
- [ ] Webhook endpoint vytvořen a testován
- [ ] Všechny Stripe klíče v `.env`
- [ ] Webhook secret správně nakonfigurován
- [ ] Cron job nastaven a běží
- [ ] Testovací registrace proběhla úspěšně
- [ ] Webhook eventy se zpracovávají
- [ ] Trial expiry funguje správně
- [ ] Notifikace o konci trialu fungují

---

**Vytvořeno:** 2025-10-09
**Verze:** 1.0
