# Stripe Environment Variables Setup

## Přidej do .env tyto proměnné:

```env
# ==============================================
# TRIAL CONFIGURATION
# ==============================================
TRIAL_PERIOD_DAYS=15

# ==============================================
# STRIPE API KEYS
# ==============================================
STRIPE_KEY=pk_test_your_publishable_key
STRIPE_SECRET=sk_test_your_secret_key
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret

# ==============================================
# MONTHLY PRICE IDs
# ==============================================
STRIPE_PRICE_CZK_MONTHLY=price_xxx_czk_monthly
STRIPE_PRICE_EUR_MONTHLY=price_xxx_eur_monthly
STRIPE_PRICE_PLN_MONTHLY=price_xxx_pln_monthly

# ==============================================
# YEARLY PRICE IDs
# ==============================================
STRIPE_PRICE_CZK_YEARLY=price_xxx_czk_yearly
STRIPE_PRICE_EUR_YEARLY=price_xxx_eur_yearly
STRIPE_PRICE_PLN_YEARLY=price_xxx_pln_yearly

# ==============================================
# PRICE AMOUNTS (pro zobrazení v UI)
# ==============================================
PRICE_AMOUNT_CZK_MONTHLY=99
PRICE_AMOUNT_CZK_YEARLY=999

PRICE_AMOUNT_EUR_MONTHLY=1.99
PRICE_AMOUNT_EUR_YEARLY=19.99

PRICE_AMOUNT_PLN_MONTHLY=9.99
PRICE_AMOUNT_PLN_YEARLY=99
```

## Jak nastavit Stripe Products & Prices

### 1. Přihlaš se do Stripe Dashboard

https://dashboard.stripe.com/

### 2. Vytvoř Product

- Products → Create product
- Name: "SyncMyDay Pro"
- Description: "Professional calendar synchronization"

### 3. Vytvoř Price objekty

Pro **KAŽDOU měnu** vytvoř **DVA** price objekty (monthly + yearly):

#### CZK (Czech Republic)

**Monthly:**

- Price: 29 CZK
- Billing period: Monthly
- Zkopíruj Price ID → `STRIPE_PRICE_CZK_MONTHLY`

**Yearly:**

- Price: 249 CZK
- Billing period: Yearly
- Zkopíruj Price ID → `STRIPE_PRICE_CZK_YEARLY`

#### EUR (Europe - EN, DE, SK)

**Monthly:**

- Price: 1.99 EUR
- Billing period: Monthly
- Zkopíruj Price ID → `STRIPE_PRICE_EUR_MONTHLY`

**Yearly:**

- Price: 19.99 EUR
- Billing period: Yearly
- Zkopíruj Price ID → `STRIPE_PRICE_EUR_YEARLY`

#### PLN (Poland)

**Monthly:**

- Price: 9.99 PLN
- Billing period: Monthly
- Zkopíruj Price ID → `STRIPE_PRICE_PLN_MONTHLY`

**Yearly:**

- Price: 99 PLN
- Billing period: Yearly
- Zkopíruj Price ID → `STRIPE_PRICE_PLN_YEARLY`

### 4. Nastav Webhook

**URL:** `https://vasedomena.cz/webhooks/stripe`

**Events to listen:**

- `checkout.session.completed`
- `customer.subscription.created`
- `customer.subscription.updated`
- `customer.subscription.deleted`
- `invoice.payment_succeeded`
- `invoice.payment_failed`

**Po vytvoření:**
Zkopíruj **Signing Secret** → `STRIPE_WEBHOOK_SECRET`

---

## Test Mode vs Live Mode

### Test Mode (Development)

```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

- Použij testovací kartu: `4242 4242 4242 4242`
- CVC: jakékoliv 3 číslice
- Expiry: jakékoliv budoucí datum

### Live Mode (Production)

```env
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
```

- ⚠️ Vytvoř nový webhook endpoint v live mode
- ⚠️ Vytvoř nové price objekty v live mode
- ⚠️ Zkopíruj nové Price IDs a Webhook Secret

---

## Příklad kompletní konfigurace

```env
TRIAL_PERIOD_DAYS=15

STRIPE_KEY=pk_test_YOUR_STRIPE_PUBLISHABLE_KEY
STRIPE_SECRET=sk_test_YOUR_STRIPE_SECRET_KEY
STRIPE_WEBHOOK_SECRET=whsec_YOUR_WEBHOOK_SECRET

STRIPE_PRICE_CZK_MONTHLY=price_1QRgHzK7USwXxSbNabcd1234
STRIPE_PRICE_CZK_YEARLY=price_1QRgI5K7USwXxSbNefgh5678

STRIPE_PRICE_EUR_MONTHLY=price_1QRgIBK7USwXxSbNijkl9012
STRIPE_PRICE_EUR_YEARLY=price_1QRgIHK7USwXxSbNmnop3456

STRIPE_PRICE_PLN_MONTHLY=price_1QRgINK7USwXxSbNqrst7890
STRIPE_PRICE_PLN_YEARLY=price_1QRgITK7USwXxSbNuvwx1234

PRICE_AMOUNT_CZK_MONTHLY=29
PRICE_AMOUNT_CZK_YEARLY=249
PRICE_AMOUNT_EUR_MONTHLY=1.99
PRICE_AMOUNT_EUR_YEARLY=19.99
PRICE_AMOUNT_PLN_MONTHLY=9.99
PRICE_AMOUNT_PLN_YEARLY=99
```

---

## Ověření konfigurace

Po nastavení spusť:

```bash
php artisan config:clear
php artisan tinker
```

V tinkeru:

```php
config('services.stripe.trial_period_days');           // Mělo by vrátit 15
config('services.stripe.prices_monthly.cs');            // Mělo by vrátit price_id
\App\Helpers\PricingHelper::formatPrice('cs', 'monthly');  // Mělo by vrátit "29 Kč"
\App\Helpers\PricingHelper::getYearlySavings('cs');        // Mělo by vrátit procenta
```
