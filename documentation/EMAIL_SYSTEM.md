# Email System Documentation

## Přehled

Projekt SyncMyDay nyní obsahuje komplexní systém pro odesílání systémových emailů uživatelům. Systém pokrývá celý životní cyklus uživatele od registrace přes verifikaci až po připomínky před koncem trial období.

## Implementované funkce

### 1. Ověřovací email při registraci

**Kdy se posílá**: Automaticky při registraci nového uživatele

**Účel**: Ověření e-mailové adresy uživatele

**Implementace**:

- Třída: `App\Notifications\VerifyEmailNotification`
- View: `resources/views/emails/verify-email.blade.php`
- Route: `/email/verify/{id}/{hash}` (signed URL s expiračním časem)

**Funkce**:

- Uživatel po kliknutí na link v emailu je přesměrován na potvrzovací stránku
- Link je podepsaný a má časovou expirace (60 minut)
- Po úspěšné verifikaci se automaticky posílá uvítací email

### 2. Uvítací email po verifikaci

**Kdy se posílá**: Automaticky po úspěšné verifikaci emailu

**Účel**: Přivítání uživatele a poskytnutí základních informací o službě

**Implementace**:

- Třída: `App\Mail\WelcomeMail`
- View: `resources/views/emails/welcome.blade.php`
- Listener: `App\Listeners\SendWelcomeEmail` (reaguje na `Verified` event)

**Obsah emailu**:

- Potvrzení aktivace účtu
- Informace o 30denním trial období zdarma
- Seznam hlavních funkcí služby
- Odkazy na dokumentaci a help center
- CTA tlačítko "Get Started"

### 3. Email 7 dní před koncem trial období

**Kdy se posílá**: Každý den v 9:00 (scheduled command)

**Účel**: Připomenout uživateli blížící se konec trial období a možnost předplatného

**Implementace**:

- Třída: `App\Mail\TrialEndingInSevenDaysMail`
- View: `resources/views/emails/trial-ending-7days.blade.php`
- Command: `php artisan trial:send-ending-notifications`

**Podmínky zasílání**:

- `trial_ends_at` je přesně za 7 dní
- Uživatel ještě nemá aktivní předplatné (`stripe_subscription_id` je NULL)

**Obsah emailu**:

- Připomínka konce trial období
- Seznam výhod Pro verze
- Cena předplatného (€29/rok)
- CTA tlačítko "Set Up Payment"

### 4. Email 1 den před koncem trial období

**Kdy se posílá**: Každý den v 9:00 (scheduled command)

**Účel**: Poslední připomínka před koncem trial období

**Implementace**:

- Třída: `App\Mail\TrialEndingTomorrowMail`
- View: `resources/views/emails/trial-ending-1day.blade.php`
- Command: `php artisan trial:send-ending-notifications`

**Podmínky zasílání**:

- `trial_ends_at` je přesně za 1 den
- Uživatel ještě nemá aktivní předplatné (`stripe_subscription_id` je NULL)

**Obsah emailu**:

- Urgentní připomínka konce trial období
- Vysvětlení co se stane po konci trialu
- Informace o možnosti pokračovat s free verzí (1 pravidlo)
- CTA tlačítko "Activate Pro Now"

## Databázové změny

### Nové sloupce v tabulce `users`:

```sql
email_verified_at TIMESTAMP NULL     -- Časová značka ověření emailu
trial_ends_at TIMESTAMP NULL          -- Konec 30denního trial období
```

### Nastavení při registraci:

```php
'trial_ends_at' => now()->addDays(30)  // 30 dní od registrace
```

## Routy

```php
// Email Verification
GET  /email/verify                            - Zobrazení stránky s upozorněním
GET  /email/verify/{id}/{hash}               - Verifikační endpoint (signed)
GET  /email/verified                          - Potvrzovací stránka po verifikaci
POST /email/verification-notification         - Resend verifikačního emailu
```

## Scheduled Command

### Nastavení cron job:

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Command spouštěný každý den:

```php
$schedule->command('trial:send-ending-notifications')->dailyAt('09:00');
```

### Ruční spuštění pro testování:

```bash
php artisan trial:send-ending-notifications
```

## Email Šablony

Všechny emaily používají společný layout: `resources/views/emails/layout.blade.php`

**Design features**:

- Moderní gradient design (purple/indigo)
- Responzivní pro všechna zařízení
- Brand consistency
- Clear CTA buttons
- Footer s odkazy a copyright

## Vícejazyčná podpora

Systém podporuje 5 jazyků:

- **en** - Angličtina (English)
- **cs** - Čeština (Czech)
- **de** - Němčina (German)
- **pl** - Polština (Polish)
- **sk** - Slovenština (Slovak)

### Překladové soubory:

```
lang/en/emails.php
lang/cs/emails.php
lang/de/emails.php
lang/pl/emails.php
lang/sk/emails.php
```

Emaily se automaticky posílají v jazyce uživatele (nastaveno v `users.locale`).

## User Model - Nové metody

```php
// Kontrola, zda je uživatel v trial období
public function isInTrialPeriod(): bool

// Počet dní zbývajících v trial období
public function trialDaysRemaining(): int

// Odeslání ověřovacího emailu (přepsání Laravel metody)
public function sendEmailVerificationNotification()
```

## Konfigurace

### MAIL nastavení v .env:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@syncmyday.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Queue konfigurace (doporučeno pro produkci):

Všechny Mailable třídy implementují `ShouldQueue`, takže se automaticky zpracovávají přes queue.

```env
QUEUE_CONNECTION=redis  # nebo database
```

Pro spuštění queue workera:

```bash
php artisan queue:work
```

## Testování

### Test verifikačního emailu:

1. Zaregistrujte nového uživatele
2. Zkontrolujte email v mailtrap/mailhog
3. Klikněte na verifikační link
4. Měli byste vidět potvrzovací stránku
5. Zkontrolujte uvítací email

### Test trial ending emailů:

```bash
# Ruční test commandu
php artisan trial:send-ending-notifications

# Pro testování vytvořte testovacího uživatele s trial končícím za 7 dní
php artisan tinker
> $user = User::first();
> $user->trial_ends_at = now()->addDays(7);
> $user->save();
```

## Bezpečnost

- ✅ Signed URLs pro verifikační linky (zabezpečeno proti manipulaci)
- ✅ Expirační čas pro verifikační linky (60 minut)
- ✅ Throttling na resend endpoint (max 6 pokusů za minutu)
- ✅ Throttling na verifikační endpoint (max 6 pokusů za minutu)
- ✅ Middleware authentication pro všechny verifikační endpointy

## Monitorování

### Logy:

```bash
# Sledování emailových logů
tail -f storage/logs/laravel.log | grep "Mail"

# Sledování queue jobů
php artisan queue:failed
```

### Metriky k sledování:

- Počet odeslaných verifikačních emailů
- Conversion rate verifikace emailů
- Počet odeslaných trial ending emailů
- Conversion rate z trialu na placené předplatné

## Budoucí vylepšení (návrhy)

1. **Email analytics**: Tracking otevření a kliknutí v emailech
2. **A/B testování**: Různé varianty emailů pro optimalizaci konverze
3. **Personalizace**: Více personalizovaný obsah na základě chování uživatele
4. **Follow-up sekvence**: Další emaily během trial období (např. tips & tricks)
5. **Re-engagement**: Emaily pro neaktivní uživatele
6. **Win-back campaign**: Emaily pro uživatele, jejichž trial skončil bez konverze

## Řešení problémů

### Email se neposílá:

1. Zkontrolujte `.env` konfiguraci
2. Zkontrolujte logs: `storage/logs/laravel.log`
3. Ověřte queue worker běží: `ps aux | grep "queue:work"`
4. Zkontrolujte failed jobs: `php artisan queue:failed`

### Verifikační link nefunguje:

1. Zkontrolujte `APP_URL` v `.env`
2. Ověřte, že link není expirovaný (60 minut)
3. Zkontrolujte signed URL middleware v routách

### Scheduled command se nespouští:

1. Ověřte cron job je nakonfigurován: `crontab -l`
2. Zkontrolujte logs: `storage/logs/laravel.log`
3. Ruční test: `php artisan trial:send-ending-notifications`

## Kontakt

Pro dotazy ohledně email systému kontaktujte vývojový tým.
