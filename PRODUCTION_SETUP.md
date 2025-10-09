# Production Setup Guide

## 📧 Email Configuration

### Development (Localhost) - Mailtrap

Pro development prostředí (localhost) použijte Mailtrap pro testování emailů:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=a36fa5039c4baf
MAIL_PASSWORD=7093378e706aff
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@syncmyday.app"
MAIL_FROM_NAME="${APP_NAME}"
```

### Production (VPS) - Czech Hosting SMTP

Pro produkční prostředí na VPS serveru:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.cesky-hosting.cz
MAIL_PORT=625
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD=Login2025-
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@syncmyday.cz"
MAIL_FROM_NAME="${APP_NAME}"
```

**Poznámky:**

- Port 625 s TLS zabezpečením je doporučený
- Alternativně lze použít port 25 bez zabezpečení (nedoporučeno)
- Login a heslo jsou stejné jako pro příjem pošty

---

## 🚀 Deployment na Production

### 1. Připravte .env soubor na VPS

Na vašem VPS serveru vytvořte `.env` soubor:

```bash
# Na VPS serveru
cd /path/to/syncmyday
nano .env
```

Vložte tuto konfiguraci:

```env
# Application
APP_NAME=SyncMyDay
APP_ENV=production
APP_KEY=base64:your-app-key-here
APP_DEBUG=false
APP_URL=https://syncmyday.cz

# Mail Configuration - Czech Hosting SMTP
MAIL_MAILER=smtp
MAIL_HOST=smtp.cesky-hosting.cz
MAIL_PORT=625
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD=Login2025-
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@syncmyday.cz"
MAIL_FROM_NAME="${APP_NAME}"

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=syncmyday
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

# Queue Configuration
QUEUE_CONNECTION=database

# Session & Cache
SESSION_DRIVER=database
CACHE_DRIVER=file

# Google OAuth
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/oauth/google/callback"

# Microsoft OAuth
MICROSOFT_CLIENT_ID=your-microsoft-client-id
MICROSOFT_CLIENT_SECRET=your-microsoft-client-secret
MICROSOFT_REDIRECT_URI="${APP_URL}/oauth/microsoft/callback"

# Stripe
STRIPE_KEY=your-stripe-publishable-key
STRIPE_SECRET=your-stripe-secret-key
STRIPE_WEBHOOK_SECRET=your-stripe-webhook-secret
STRIPE_PRO_PRICE_ID=your-stripe-price-id

# Email Domain (for email calendars)
EMAIL_DOMAIN=syncmyday.cz

# Locale
DEFAULT_LOCALE=cs
```

### 2. Vygenerujte APP_KEY

```bash
php artisan key:generate
```

### 3. Spusťte migrace

```bash
php artisan migrate --force
```

### 4. Vyčistěte a cache konfiguraci

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Cachování pro produkci (zrychlí aplikaci)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Nastavte oprávnění

```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 📬 Queue Worker pro odesílání emailů

Protože všechny emaily používají queue (`implements ShouldQueue`), musíte mít spuštěný queue worker.

### Ruční spuštění (pro testování)

```bash
php artisan queue:work --tries=3
```

### Automatické spuštění přes Supervisor (doporučeno pro produkci)

**1. Nainstalujte Supervisor:**

```bash
sudo apt-get install supervisor
```

**2. Vytvořte konfiguraci:**

```bash
sudo nano /etc/supervisor/conf.d/syncmyday-worker.conf
```

**3. Vložte tuto konfiguraci:**

```ini
[program:syncmyday-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/syncmyday/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/syncmyday/storage/logs/worker.log
stopwaitsecs=3600
```

**4. Aktivujte a spusťte:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start syncmyday-worker:*
```

**5. Kontrola stavu:**

```bash
sudo supervisorctl status syncmyday-worker:*
```

---

## ⏰ Cron Job pro Scheduled Tasks

Pro automatické odesílání trial ending emailů a jiné scheduled tasky:

```bash
crontab -e
```

Přidejte:

```cron
* * * * * cd /path/to/syncmyday && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Testování Email Konfigurace

### Test na localhostu (Mailtrap)

```bash
php artisan email:test test@example.com --type=payment
```

### Test na produkci (Czech Hosting SMTP)

Po nasazení na VPS otestujte:

```bash
php artisan email:test vas@email.cz --type=payment
```

Email by měl dorazit z `info@syncmyday.cz` na váš email.

---

## 🔧 Troubleshooting

### Email se neposílá

**1. Zkontrolujte queue:**

```bash
php artisan queue:work --once -vvv
```

**2. Zkontrolujte failed jobs:**

```bash
php artisan queue:failed
```

**3. Zkontrolujte logy:**

```bash
tail -f storage/logs/laravel.log
```

### SMTP chyby

**Pokud dostanete SMTP chybu 535 (Authentication failed):**

- Zkontrolujte správnost přihlašovacích údajů
- Ověřte, že Czech Hosting povoluje SMTP spojení z vaší IP

**Pokud dostanete Connection timeout:**

- Zkontrolujte, že port 625 není blokovaný firewallem
- Zkuste alternativní port 25

**Test SMTP připojení:**

```bash
php artisan tinker

# Zkuste poslat testovací email
Mail::raw('Test email', function($message) {
    $message->to('vas@email.cz')->subject('Test');
});
```

---

## 🔒 Bezpečnost

### Ochrana .env souboru

Ujistěte se, že `.env` není veřejně přístupný:

```bash
# V .htaccess nebo nginx config
# Apache:
<Files .env>
    Order allow,deny
    Deny from all
</Files>

# Nginx (v config):
location ~ /\.env {
    deny all;
    return 404;
}
```

### Ochrana credentials

- **NIKDY** necommitujte `.env` do gitu
- Použijte silná hesla pro databázi
- Rotujte API keys pravidelně

---

## 📊 Monitoring

### Email Delivery Rate

Sledujte, kolik emailů se posílá:

```bash
# Počet úspěšně odeslaných jobů
php artisan queue:monitor

# Failed jobs
php artisan queue:failed
```

### Logy

```bash
# Email logy
tail -f storage/logs/laravel.log | grep "Mail"

# Worker logy
tail -f storage/logs/worker.log
```

---

## 🎯 Checklist pro Production

- [ ] .env soubor nakonfigurován s Czech Hosting SMTP
- [ ] APP_KEY vygenerován
- [ ] Database migrace spuštěny
- [ ] Queue worker běží (Supervisor)
- [ ] Cron job nakonfigurován
- [ ] Test email odeslán a doručen
- [ ] Logy monitorovány
- [ ] .env soubor zabezpečen
- [ ] Oprávnění na storage a cache nastavena
- [ ] Config cache vytvořena

---

## 💡 Tipy

1. **Queue monitoring**: Nastavte alerting pro failed jobs
2. **Email rate limiting**: Czech Hosting může mít limit na počet emailů/hodinu
3. **Backup**: Pravidelně zálohujte databázi i .env
4. **Updates**: Sledujte Laravel security updates

---

Máte-li jakékoliv problémy s nastavením, kontaktujte Czech Hosting support pro ověření SMTP nastavení.
