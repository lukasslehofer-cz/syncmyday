# Email System - Rychlý start

## ⚡ Quick Setup

### 1. Spusťte migraci (již provedeno)

```bash
php artisan migrate
```

### 2. Nakonfigurujte email v `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io  # Pro testování
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@syncmyday.com
MAIL_FROM_NAME="SyncMyDay"
```

### 3. Spusťte queue worker (pro produkci)

```bash
php artisan queue:work
```

### 4. Nakonfigurujte cron (pro produkci)

```bash
crontab -e
```

Přidejte:

```
* * * * * cd /path/to/syncmyday && php artisan schedule:run >> /dev/null 2>&1
```

## 📧 Typy emailů

| Email            | Trigger        | Kdy se posílá                        |
| ---------------- | -------------- | ------------------------------------ |
| **Verification** | Registrace     | Okamžitě po registraci               |
| **Welcome**      | Email verified | Po kliknutí na verifikační link      |
| **Trial 7 days** | Scheduled      | Každý den v 9:00 (7 dní před koncem) |
| **Trial 1 day**  | Scheduled      | Každý den v 9:00 (1 den před koncem) |

## 🧪 Testování

### Test verifikace:

```bash
# 1. Zaregistrujte nového uživatele
# 2. Zkontrolujte email
# 3. Klikněte na verifikační link
# 4. Měl by přijít uvítací email
```

### Test trial emailů:

```bash
php artisan trial:send-ending-notifications
```

### Vytvoření testovacího uživatele:

```bash
php artisan tinker
```

```php
$user = new App\Models\User;
$user->name = 'Test User';
$user->email = 'test@example.com';
$user->password = bcrypt('password');
$user->locale = 'cs';
$user->trial_ends_at = now()->addDays(7);  // nebo addDay() pro 1 den
$user->save();
```

## 🌍 Podporované jazyky

- `en` - English
- `cs` - Čeština
- `de` - Deutsch
- `pl` - Polski
- `sk` - Slovenčina

Všechny překlady jsou v: `lang/{locale}/emails.php`

## 🔧 Důležité soubory

### Mailable třídy:

- `app/Mail/WelcomeMail.php`
- `app/Mail/TrialEndingInSevenDaysMail.php`
- `app/Mail/TrialEndingTomorrowMail.php`

### Email views:

- `resources/views/emails/layout.blade.php` (základní šablona)
- `resources/views/emails/verify-email.blade.php`
- `resources/views/emails/welcome.blade.php`
- `resources/views/emails/trial-ending-7days.blade.php`
- `resources/views/emails/trial-ending-1day.blade.php`

### Potvrzovací stránky:

- `resources/views/auth/verify-email.blade.php`
- `resources/views/auth/verify-success.blade.php`

### Commands:

- `app/Console/Commands/SendTrialEndingNotifications.php`

## 📝 Co bylo změněno

### Database:

- ✅ Přidán sloupec `email_verified_at` do `users` tabulky
- ✅ Přidán sloupec `trial_ends_at` do `users` tabulky

### Models:

- ✅ `User` model implementuje `MustVerifyEmail`
- ✅ Přidány metody: `isInTrialPeriod()`, `trialDaysRemaining()`

### Controllers:

- ✅ `AuthController` - upravena registrace (posílá verification email, nastavuje trial)
- ✅ Nový `EmailVerificationController` - obsluha verifikace

### Routes:

- ✅ Přidány routy pro email verifikaci
- ✅ Všechny verifikační routy jsou zabezpečeny (signed URLs, throttling)

### Scheduled Tasks:

- ✅ Nový command `trial:send-ending-notifications`
- ✅ Scheduled běží každý den v 9:00

## 🚨 Checklist pro produkci

- [ ] Nastavit správný SMTP server v `.env`
- [ ] Nastavit správný `APP_URL` v `.env`
- [ ] Nakonfigurovat Redis/Database queue
- [ ] Spustit queue worker jako daemon (supervisor)
- [ ] Nakonfigurovat cron job
- [ ] Otestovat všechny typy emailů
- [ ] Zkontrolovat spam score emailů
- [ ] Nastavit monitoring failed jobs
- [ ] Nastavit alert pro failed emails

## 💡 Tipy

### Pro development (MailTrap):

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

### Pro lokální testování (MailHog):

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### Disable email verification pro testování:

V `User` modelu dočasně odeberte `implements MustVerifyEmail`

## 📖 Plná dokumentace

Pro detailní informace viz: `EMAIL_SYSTEM.md`
