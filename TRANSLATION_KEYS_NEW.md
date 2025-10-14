# Nové Translation Keys - Trial Without Card

Tento dokument obsahuje všechny **nové** translation keys, které je potřeba přidat do language souborů.

---

## 📁 Soubory k úpravě

- `lang/cs/messages.php`
- `lang/en/messages.php`
- `lang/de/messages.php`
- `lang/pl/messages.php`
- `lang/sk/messages.php`

A také:

- `lang/cs/emails.php` (pokud existuje, jinak přidat do messages.php)
- `lang/en/emails.php`
- atd.

---

## 🆕 NOVÉ KLÍČE - messages.php

Přidej tyto klíče do `lang/*/messages.php`:

```php
// ============================================
// BILLING PAGE - Pricing Cards
// ============================================
'choose_your_plan' => 'Vyber si svůj plán',
'monthly_plan' => 'Měsíční plán',
'yearly_plan' => 'Roční plán',
'per_month' => 'měsíčně',
'per_year' => 'ročně',
'or' => 'nebo',
'best_value' => 'Nejlepší hodnota',
'save_percent' => 'Ušetři :percent%',
'flexible_cancel_anytime' => 'Flexibilní, zrušitelné kdykoliv',
'choose_monthly' => 'Vybrat měsíční',
'choose_yearly' => 'Vybrat roční',
'trial_days_remaining' => 'Zbývá :days dní trialu',
'no_charge_during_trial' => 'Během trialu se nic nestrhne',

// ============================================
// TRIAL BANNER (layouts/app.blade.php)
// ============================================
'trial_ending_soon' => 'Trial brzy končí',
'trial_active' => 'Trial aktivní',
'days_remaining' => '{0} Žádné dny|{1} 1 den|[2,4] :count dny|[5,*] :count dní',
'upgrade_now_save' => 'Upgraduj teď a získej slevu',
'upgrade_now' => 'Upgraduj nyní',

// ============================================
// HOMEPAGE (welcome.blade.php)
// ============================================
'start_free_trial' => 'Začni zdarma',
'choose_plan_after_trial' => 'Po trialu si vyber plán:',
'save_with_yearly' => 'Ušetři :percent% s ročním plánem',
'start_free_trial_now' => 'Začít zdarma (bez karty)',
'no_commitment' => 'Žádné závazky',

// ============================================
// EXISTING KEYS (ověř že máš)
// ============================================
'no_credit_card_required' => 'Není potřeba platební karta',
'cancel_anytime' => 'Zruš kdykoliv',
'unlimited_sync_rules' => 'Neomezená pravidla synchronizace',
'unlimited_calendars' => 'Neomezené kalendáře',
'realtime_sync_webhooks' => 'Synchronizace v reálném čase',
'priority_support' => 'Prioritní podpora',
'full_features_no_limits' => 'Všechny funkce bez omezení',

```

---

## 📧 NOVÉ KLÍČE - emails.php (nebo messages.php)

Přidej do `lang/*/emails.php` (nebo do messages.php pokud emails.php neexistuje):

```php
// ============================================
// EMAIL - Trial Ending
// ============================================
'or' => 'nebo',
'per_month' => 'měsíčně',
'per_year' => 'ročně',
'choose_your_plan' => 'Vyber si svůj plán',
'trial_pricing_title' => 'Jednoduchá cena',
'trial_pricing_note' => 'Bez závazků, zruš kdykoliv',
'trial_pricing_details' => 'Automaticky obnovitelné, zruš kdykoliv',
```

---

## 📝 KOMPLETNÍ PŘÍKLAD - Čeština (CS)

### lang/cs/messages.php

```php
<?php

return [
    // ... existující klíče ...

    // ============================================
    // NOVÉ - Billing & Pricing
    // ============================================
    'choose_your_plan' => 'Vyber si svůj plán',
    'monthly_plan' => 'Měsíční plán',
    'yearly_plan' => 'Roční plán',
    'per_month' => 'měsíčně',
    'per_year' => 'ročně',
    'or' => 'nebo',
    'best_value' => 'Nejlepší hodnota',
    'save_percent' => 'Ušetři :percent%',
    'flexible_cancel_anytime' => 'Flexibilní, zrušitelné kdykoliv',
    'choose_monthly' => 'Vybrat měsíční',
    'choose_yearly' => 'Vybrat roční',
    'trial_days_remaining' => 'Zbývá :days dní trialu',
    'no_charge_during_trial' => 'Během trialu se nic nestrhne',

    // Trial Banner
    'trial_ending_soon' => 'Trial brzy končí',
    'trial_active' => 'Trial aktivní',
    'days_remaining' => '{0} Žádné dny|{1} 1 den|[2,4] :count dny|[5,*] :count dní',
    'upgrade_now_save' => 'Upgraduj teď a ušetři',
    'upgrade_now' => 'Upgraduj nyní',

    // Homepage
    'start_free_trial' => 'Začni zdarma',
    'choose_plan_after_trial' => 'Po trialu si vyber plán:',
    'save_with_yearly' => 'Ušetři :percent% s ročním plánem',
    'start_free_trial_now' => 'Začít zdarma (bez karty)',
    'no_commitment' => 'Žádné závazky',

    // Already existing (verify)
    'no_credit_card_required' => 'Není potřeba platební karta',
    'cancel_anytime' => 'Zruš kdykoliv',
];
```

---

## 📝 KOMPLETNÍ PŘÍKLAD - Angličtina (EN)

### lang/en/messages.php

```php
<?php

return [
    // ... existing keys ...

    // ============================================
    // NEW - Billing & Pricing
    // ============================================
    'choose_your_plan' => 'Choose Your Plan',
    'monthly_plan' => 'Monthly Plan',
    'yearly_plan' => 'Yearly Plan',
    'per_month' => 'per month',
    'per_year' => 'per year',
    'or' => 'or',
    'best_value' => 'Best Value',
    'save_percent' => 'Save :percent%',
    'flexible_cancel_anytime' => 'Flexible, cancel anytime',
    'choose_monthly' => 'Choose Monthly',
    'choose_yearly' => 'Choose Yearly',
    'trial_days_remaining' => ':days days remaining in trial',
    'no_charge_during_trial' => 'No charge during trial',

    // Trial Banner
    'trial_ending_soon' => 'Trial Ending Soon',
    'trial_active' => 'Trial Active',
    'days_remaining' => '{0} No days|{1} 1 day|[2,*] :count days remaining',
    'upgrade_now_save' => 'Upgrade now and save',
    'upgrade_now' => 'Upgrade Now',

    // Homepage
    'start_free_trial' => 'Start Free Trial',
    'choose_plan_after_trial' => 'Choose your plan after trial:',
    'save_with_yearly' => 'Save :percent% with yearly',
    'start_free_trial_now' => 'Start Free Trial (No Card)',
    'no_commitment' => 'No commitment',

    // Already existing (verify)
    'no_credit_card_required' => 'No credit card required',
    'cancel_anytime' => 'Cancel anytime',
];
```

---

## 📝 KOMPLETNÍ PŘÍKLAD - Němčina (DE)

### lang/de/messages.php

```php
<?php

return [
    // ... bestehende Schlüssel ...

    // ============================================
    // NEU - Abrechnung & Preise
    // ============================================
    'choose_your_plan' => 'Wählen Sie Ihren Plan',
    'monthly_plan' => 'Monatlicher Plan',
    'yearly_plan' => 'Jährlicher Plan',
    'per_month' => 'pro Monat',
    'per_year' => 'pro Jahr',
    'or' => 'oder',
    'best_value' => 'Bester Wert',
    'save_percent' => 'Sparen Sie :percent%',
    'flexible_cancel_anytime' => 'Flexibel, jederzeit kündbar',
    'choose_monthly' => 'Monatlich wählen',
    'choose_yearly' => 'Jährlich wählen',
    'trial_days_remaining' => ':days Tage Testversion verbleibend',
    'no_charge_during_trial' => 'Keine Gebühr während der Testversion',

    // Trial-Banner
    'trial_ending_soon' => 'Testversion endet bald',
    'trial_active' => 'Testversion aktiv',
    'days_remaining' => '{0} Keine Tage|{1} 1 Tag|[2,*] :count Tage verbleibend',
    'upgrade_now_save' => 'Jetzt upgraden und sparen',
    'upgrade_now' => 'Jetzt upgraden',

    // Startseite
    'start_free_trial' => 'Kostenlos testen',
    'choose_plan_after_trial' => 'Wählen Sie nach der Testversion Ihren Plan:',
    'save_with_yearly' => 'Sparen Sie :percent% mit Jahresplan',
    'start_free_trial_now' => 'Kostenlos starten (ohne Karte)',
    'no_commitment' => 'Keine Verpflichtung',

    // Bereits vorhanden (überprüfen)
    'no_credit_card_required' => 'Keine Kreditkarte erforderlich',
    'cancel_anytime' => 'Jederzeit kündbar',
];
```

---

## 📝 KOMPLETNÍ PŘÍKLAD - Polština (PL)

### lang/pl/messages.php

```php
<?php

return [
    // ... istniejące klucze ...

    // ============================================
    // NOWE - Rozliczenia i Ceny
    // ============================================
    'choose_your_plan' => 'Wybierz swój plan',
    'monthly_plan' => 'Plan miesięczny',
    'yearly_plan' => 'Plan roczny',
    'per_month' => 'miesięcznie',
    'per_year' => 'rocznie',
    'or' => 'lub',
    'best_value' => 'Najlepsza wartość',
    'save_percent' => 'Oszczędź :percent%',
    'flexible_cancel_anytime' => 'Elastyczny, anuluj w dowolnym momencie',
    'choose_monthly' => 'Wybierz miesięczny',
    'choose_yearly' => 'Wybierz roczny',
    'trial_days_remaining' => 'Pozostało :days dni próbnych',
    'no_charge_during_trial' => 'Brak opłat podczas okresu próbnego',

    // Banner próbny
    'trial_ending_soon' => 'Okres próbny wkrótce się kończy',
    'trial_active' => 'Okres próbny aktywny',
    'days_remaining' => '{0} Brak dni|{1} 1 dzień|[2,4] :count dni|[5,*] :count dni pozostało',
    'upgrade_now_save' => 'Ulepsz teraz i oszczędzaj',
    'upgrade_now' => 'Ulepsz teraz',

    // Strona główna
    'start_free_trial' => 'Rozpocznij darmowy okres próbny',
    'choose_plan_after_trial' => 'Wybierz plan po okresie próbnym:',
    'save_with_yearly' => 'Oszczędź :percent% z planem rocznym',
    'start_free_trial_now' => 'Rozpocznij za darmo (bez karty)',
    'no_commitment' => 'Bez zobowiązań',

    // Już istniejące (sprawdź)
    'no_credit_card_required' => 'Karta kredytowa nie jest wymagana',
    'cancel_anytime' => 'Anuluj w dowolnym momencie',
];
```

---

## 📝 KOMPLETNÍ PŘÍKLAD - Slovenština (SK)

### lang/sk/messages.php

```php
<?php

return [
    // ... existujúce kľúče ...

    // ============================================
    // NOVÉ - Fakturácia a Ceny
    // ============================================
    'choose_your_plan' => 'Vyberte si svoj plán',
    'monthly_plan' => 'Mesačný plán',
    'yearly_plan' => 'Ročný plán',
    'per_month' => 'mesačne',
    'per_year' => 'ročne',
    'or' => 'alebo',
    'best_value' => 'Najlepšia hodnota',
    'save_percent' => 'Ušetrite :percent%',
    'flexible_cancel_anytime' => 'Flexibilné, zrušiteľné kedykoľvek',
    'choose_monthly' => 'Vybrať mesačný',
    'choose_yearly' => 'Vybrať ročný',
    'trial_days_remaining' => 'Zostáva :days dní skúšobného obdobia',
    'no_charge_during_trial' => 'Počas skúšobného obdobia sa nič nestiahne',

    // Trial banner
    'trial_ending_soon' => 'Skúšobné obdobie čoskoro končí',
    'trial_active' => 'Skúšobné obdobie aktívne',
    'days_remaining' => '{0} Žiadne dni|{1} 1 deň|[2,4] :count dni|[5,*] :count dní zostáva',
    'upgrade_now_save' => 'Upgradujte teraz a ušetrite',
    'upgrade_now' => 'Upgradujte teraz',

    // Domovská stránka
    'start_free_trial' => 'Začať zadarmo',
    'choose_plan_after_trial' => 'Po skúšobnom období si vyberte plán:',
    'save_with_yearly' => 'Ušetrite :percent% s ročným plánom',
    'start_free_trial_now' => 'Začať zadarmo (bez karty)',
    'no_commitment' => 'Žiadne záväzky',

    // Už existujúce (overte)
    'no_credit_card_required' => 'Nie je potrebná platobná karta',
    'cancel_anytime' => 'Zrušiť kedykoľvek',
];
```

---

## ✅ CHECKLIST

- [ ] Přidat všechny nové klíče do `lang/cs/messages.php`
- [ ] Přidat všechny nové klíče do `lang/en/messages.php`
- [ ] Přidat všechny nové klíče do `lang/de/messages.php`
- [ ] Přidat všechny nové klíče do `lang/pl/messages.php`
- [ ] Přidat všechny nové klíče do `lang/sk/messages.php`
- [ ] Přidat email klíče do `lang/*/emails.php` (pokud existují)
- [ ] Otestovat všechny jazyky v prohlížeči
- [ ] Clear cache: `php artisan config:clear && php artisan view:clear`

---

## 🧪 TESTOVÁNÍ

Po přidání klíčů otestuj:

```bash
# Clear cache
php artisan config:clear
php artisan view:clear

# Zkontroluj, že všechny klíče fungují
php artisan tinker
```

V tinkeru:

```php
app()->setLocale('cs');
__('messages.choose_your_plan');  // Mělo by vrátit: "Vyber si svůj plán"
__('messages.save_percent', ['percent' => 30]);  // "Ušetři 30%"
```

Pro každý jazyk změň locale a ověř.

---

**Vytvořeno:** 2025-10-14  
**Verze:** 1.0
