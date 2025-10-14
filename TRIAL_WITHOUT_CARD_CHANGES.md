# Trial Without Card - Změny Platebního Systému

## 📋 Přehled změn

Tento dokument popisuje změny v platebním systému SyncMyDay z **povinné karty při registraci** na **volný trial bez karty** s možností měsíčního nebo ročního předplatného.

## 🎯 Co se změnilo

### 1. **Registrace BEZ karty**

- ✅ Uživatel se registruje bez zadání platební karty
- ✅ Okamžitě dostane Pro tier přístup na 15 dní (konfigurovatelné v .env)
- ✅ Redirect přímo na onboarding místo Stripe checkout

### 2. **Měsíční + Roční předplatné**

- ✅ Dva platební intervaly: monthly a yearly
- ✅ Dynamické ceny podle locale (CZK, EUR, PLN...)
- ✅ Zobrazení úspory u ročního plánu

### 3. **Soft-lock místo deaktivace**

- ✅ Po expiraci trialu zůstávají data zachována
- ✅ Synchronizace se zastaví (žádná nová data)
- ✅ User vidí všechna svá data, ale nemůže synchronizovat
- ✅ Po zaplacení se vše obnoví

---

## 🔧 Technické změny

### Backend

#### 1. **AuthController** (`app/Http/Controllers/Auth/AuthController.php`)

```php
// Odstraněno: Stripe checkout redirect po registraci
// Nově: Přímý redirect na onboarding s trialem
return redirect()->route('onboarding.start')
    ->with('success', __('messages.registration_success'));
```

#### 2. **BillingController** (`app/Http/Controllers/BillingController.php`)

- Odstraněna metoda `createTrialCheckoutSession()`
- Upravena metoda `createCheckoutSession()` pro monthly/yearly intervaly
- Nově přijímá `interval` parametr (monthly nebo yearly)

#### 3. **PricingHelper** (`app/Helpers/PricingHelper.php`)

```php
// Nové metody
getPriceId($locale, $interval)         // Vrátí Stripe price ID
getCurrency($locale, $interval)        // Vrátí cenu a měnu
formatPrice($locale, $interval)        // Formátuje cenu
getYearlySavings($locale)             // Vypočítá úsporu yearly vs monthly
```

#### 4. **Config** (`config/services.php`)

```php
'stripe' => [
    'trial_period_days' => env('TRIAL_PERIOD_DAYS', 15),

    'prices_monthly' => [
        'cs' => env('STRIPE_PRICE_CZK_MONTHLY'),
        'en' => env('STRIPE_PRICE_EUR_MONTHLY'),
        // ...
    ],

    'prices_yearly' => [
        'cs' => env('STRIPE_PRICE_CZK_YEARLY'),
        // ...
    ],

    'currencies' => [
        'cs' => [
            'code' => 'CZK',
            'symbol' => 'Kč',
            'amount_monthly' => env('PRICE_AMOUNT_CZK_MONTHLY', 29),
            'amount_yearly' => env('PRICE_AMOUNT_CZK_YEARLY', 249),
        ],
        // ...
    ],
]
```

#### 5. **Soft-lock Implementation**

**SyncEngine** (`app/Services/Sync/SyncEngine.php`):

```php
public function syncRule(SyncRule $rule, CalendarConnection $sourceConnection): void
{
    // Check if user has active subscription (soft-lock for expired trials)
    if (!$rule->user->hasActiveSubscription()) {
        Log::warning('Sync skipped - user subscription expired');
        return;
    }
    // ... continue sync
}
```

**SyncCalendarsCommand** (`app/Console/Commands/SyncCalendarsCommand.php`):

```php
// Přidána kontrola před synchronizací
if (!$rule->user->hasActiveSubscription()) {
    $this->warn("⚠️  User subscription expired. Skipping sync.");
    continue;
}
```

**ExpireTrialsCommand** (`app/Console/Commands/ExpireTrialsCommand.php`):

```php
// Změněno: Soft-lock místo downgrade na 'free'
// Uživatel zůstává na 'pro' tier, ale hasActiveSubscription() vrací false
Log::info('Trial expired - user soft-locked');
```

### Frontend

#### 1. **Billing View** (`resources/views/billing/index.blade.php`)

- Přepracováno na pricing cards (monthly vs yearly)
- Zobrazení úspory u ročního plánu
- Dynamické ceny z PricingHelper

#### 2. **Trial Banner** (`resources/views/layouts/app.blade.php`)

- Nový design s gradientem
- "Upgrade Now" CTA
- Zobrazení zbývajících dní trialu

#### 3. **Email Templates**

- `resources/views/emails/trial-ending-7days.blade.php` - Dynamické ceny monthly/yearly
- `resources/views/emails/trial-ending-1day.blade.php` - Dynamické ceny monthly/yearly

---

## ⚙️ Konfigurace .env

### Nové proměnné

```env
# Trial Configuration
TRIAL_PERIOD_DAYS=15

# Monthly Price IDs
STRIPE_PRICE_CZK_MONTHLY=price_xxx_czk_monthly
STRIPE_PRICE_EUR_MONTHLY=price_xxx_eur_monthly
STRIPE_PRICE_PLN_MONTHLY=price_xxx_pln_monthly

# Yearly Price IDs (už existovaly, jen přejmenovat)
STRIPE_PRICE_CZK_YEARLY=price_xxx_czk_yearly
STRIPE_PRICE_EUR_YEARLY=price_xxx_eur_yearly
STRIPE_PRICE_PLN_YEARLY=price_xxx_pln_yearly

# Price Amounts (pro zobrazení, ne pro Stripe)
PRICE_AMOUNT_CZK_MONTHLY=29
PRICE_AMOUNT_CZK_YEARLY=249
PRICE_AMOUNT_EUR_MONTHLY=1.99
PRICE_AMOUNT_EUR_YEARLY=19.99
PRICE_AMOUNT_PLN_MONTHLY=9.99
PRICE_AMOUNT_PLN_YEARLY=99
```

---

## 🚀 Nasazení

### 1. Stripe Setup

1. Vytvoř **monthly price** produkty pro všechny měny (CZK, EUR, PLN...)
2. Vytvoř **yearly price** produkty (nebo použij stávající)
3. Zkopíruj Price IDs do `.env`

### 2. Database

- Žádné migrace nejsou potřeba
- Stávající struktura funguje bez změn

### 3. Deployment

```bash
# Pull změny
git pull

# Update dependencies (pokud potřeba)
composer install --no-dev

# Clear cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🧪 Testování

### Trial Flow (bez karty)

1. ✅ Registruj se jako nový uživatel
2. ✅ Měl bys být přesměrován na onboarding (ne na Stripe)
3. ✅ Trial banner by se měl zobrazit
4. ✅ Zkontroluj DB: `subscription_tier = 'pro'`, `subscription_ends_at = +15 days`

### Pricing Cards

1. ✅ Jdi na `/billing`
2. ✅ Měly by se zobrazit 2 karty (Monthly, Yearly)
3. ✅ Klikni na "Choose Monthly" → redirect na Stripe checkout
4. ✅ Po platbě → subscription aktivní

### Soft-lock

1. ✅ Nastav `subscription_ends_at` v minulosti v DB
2. ✅ Spusť `php artisan trial:expire`
3. ✅ User zůstane na 'pro' tier
4. ✅ Sync se nezapne: `php artisan calendars:sync` → přeskočí pravidla
5. ✅ Jdi na `/billing` → měla by se zobrazit "Trial expired" hláška

---

## 📊 Monitoring

### Queries pro kontrolu

**Aktivní trialy:**

```sql
SELECT * FROM users
WHERE subscription_tier = 'pro'
  AND subscription_ends_at > NOW()
  AND stripe_subscription_id IS NULL;
```

**Expired trialy (soft-locked):**

```sql
SELECT * FROM users
WHERE subscription_tier = 'pro'
  AND subscription_ends_at <= NOW()
  AND stripe_subscription_id IS NULL;
```

**Aktivní platící:**

```sql
SELECT * FROM users
WHERE subscription_tier = 'pro'
  AND stripe_subscription_id IS NOT NULL;
```

---

## 🔍 Troubleshooting

### Problém: Sync stále běží pro expired usery

**Řešení:** Zkontroluj, že `hasActiveSubscription()` v User modelu správně vrací false

### Problém: Pricing cards neukazují správné ceny

**Řešení:**

1. Zkontroluj `.env` - jsou tam všechny price amounts?
2. Clear config: `php artisan config:clear`
3. Zkontroluj `config/services.php` - jsou tam fallback hodnoty?

### Problém: Stripe Price ID not found

**Řešení:**

1. Zkontroluj, že price IDs jsou ve Stripe dashboard
2. Zkontroluj `.env` - správné hodnoty?
3. Zkontroluj interval (monthly/yearly) ve formuláři

---

## 📝 TODO pro produkci

- [ ] Vytvořit monthly + yearly price objekty ve Stripe (všechny měny)
- [ ] Přidat všechny price IDs do `.env`
- [ ] Otestovat celý flow na staging
- [ ] Vytvořit trial expired notification email
- [ ] Případně přidat translation keys pro nové texty

---

**Verze:** 2.0  
**Datum:** 2025-10-13  
**Autor:** AI Assistant
