# Quick Deploy Guide

## 🚀 Rychlý návod pro nasazení na VPS

### 1. Nastavte .env na VPS

```bash
cd /path/to/syncmyday
nano .env
```

**Vložte tuto konfiguraci (upravte DB credentials a API keys):**

```env
APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_URL=https://syncmyday.cz

# Czech Hosting SMTP
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

# Queue
QUEUE_CONNECTION=database

# Ostatní konfigurace...
```

### 2. Spusťte deployment příkazy

```bash
# Vygenerujte APP_KEY
php artisan key:generate

# Migrace
php artisan migrate --force

# Vyčistěte cache
php artisan config:clear
php artisan cache:clear

# Vytvořte production cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Nastavte oprávnění
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Nastavte Queue Worker (Supervisor)

```bash
# Nainstalujte Supervisor
sudo apt-get install supervisor

# Vytvořte config
sudo nano /etc/supervisor/conf.d/syncmyday-worker.conf
```

**Obsah:**

```ini
[program:syncmyday-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/syncmyday/artisan queue:work database --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/syncmyday/storage/logs/worker.log
```

**Aktivujte:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start syncmyday-worker:*
```

### 4. Nastavte Cron Job

```bash
crontab -e
```

**Přidejte:**

```cron
* * * * * cd /path/to/syncmyday && php artisan schedule:run >> /dev/null 2>&1
```

### 5. Test Email

```bash
php artisan email:test vas@email.cz --type=payment
```

---

## ✅ Hotovo!

Emaily se nyní budou posílat z `info@syncmyday.cz` přes Czech Hosting SMTP.

Scheduled tasky (trial ending emails) poběží automaticky každý den v 9:00.

---

## 🔍 Kontrola

```bash
# Zkontrolujte queue worker
sudo supervisorctl status

# Zkontrolujte logy
tail -f storage/logs/laravel.log

# Zkontrolujte failed jobs
php artisan queue:failed
```
