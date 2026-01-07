# 🌐 Nasazení na sdílený hosting (bez SSH/root přístupu)

Průvodce pro nasazení SyncMyDay na komerční sdílený hosting jako Wedos, Forpsi, Endora, WebSupport, atd.

## ⚠️ DŮLEŽITÉ UPOZORNĚNÍ

Aplikace SyncMyDay byla primárně navržena pro VPS s plným přístupem. Na sdíleném hostingu bude mít **omezené funkce**:

### Co NEBUDE fungovat na sdíleném hostingu:
- ❌ Real-time webhooky (okamžitá synchronizace)
- ❌ Redis cache (použije se databáze místo toho)
- ❌ Na pozadí běžící queue worker

### Co BUDE fungovat:
- ✅ Základní synchronizace kalendářů (manuální trigger nebo cron)
- ✅ OAuth připojení k Google/Microsoft
- ✅ Správa pravidel synchronizace
- ✅ Stripe platby
- ✅ Email notifikace

### Doporučení:
Pokud potřebujete real-time synchronizaci, zvažte **VPS za ~200 Kč/měsíc** (viz NASAZENI_PRODUKCE.md).

Pro základní použití můžete pokračovat s tímto sdíleným hostingem.

---

## 📋 Požadavky na hosting

### Minimální požadavky:

```
✅ PHP 8.2 nebo novější
✅ MySQL/MariaDB databáze
✅ Composer (nebo možnost nahrát vendor složku)
✅ Cron job (alespoň každých 5 minut)
✅ HTTPS/SSL certifikát
✅ Min. 512 MB PHP memory_limit
✅ Min. 1 GB diskového prostoru
✅ Možnost nastavit document root na /public
```

### Doporučení hostingů pro ČR/SK:

| Hosting | Cena | PHP 8.2+ | Cron | SSH | Hodnocení |
|---------|------|----------|------|-----|-----------|
| **Wedos** WebHosting M | 49 Kč/měsíc | ✅ | ✅ | ✅ | ⭐⭐⭐⭐⭐ Nejlepší |
| **WebSupport** Standard | 2.99 €/měsíc | ✅ | ✅ | ✅ | ⭐⭐⭐⭐ |
| **Forpsi** WebHosting M | 59 Kč/měsíc | ✅ | ✅ | ❌ | ⭐⭐⭐ |
| **Endora** Hosting M | 69 Kč/měsíc | ✅ | ✅ | ❌ | ⭐⭐⭐ |

**Doporučení**: **Wedos WebHosting M** - má PHP 8.2, SSH přístup a je nejlevnější.

---

## 🚀 Postup nasazení

### KROK 1: Příprava lokálního projektu

#### 1.1 Upravte konfiguraci pro sdílený hosting

Vytvořte soubor `config/shared-hosting.php`:

```php
<?php
// Konfigurace pro sdílený hosting bez Redis
return [
    'queue_connection' => 'database',
    'cache_driver' => 'database',
    'session_driver' => 'database',
];
```

#### 1.2 Upravte `.env` pro produkci

```env
APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vase-domena.cz

# Databáze (dostanete od hostingu)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=vase_databaze
DB_USERNAME=vase_uzivatel
DB_PASSWORD=vase_heslo

# CACHE A FRONTY - DŮLEŽITÉ PRO SDÍLENÝ HOSTING!
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# REDIS ZAKÁZAT (není k dispozici)
REDIS_CLIENT=null

# Šifrování tokenů
TOKEN_ENCRYPTION_KEY=base64:VYGENERUJTE_PODLE_INSTRUKCI

# Google OAuth
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://vase-domena.cz/oauth/google/callback

# Microsoft OAuth
MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_REDIRECT_URI=https://vase-domena.cz/oauth/microsoft/callback
MICROSOFT_TENANT=common

# Stripe
STRIPE_KEY=pk_live_
STRIPE_SECRET=sk_live_
STRIPE_WEBHOOK_SECRET=whsec_
STRIPE_PRO_PRICE_ID=

# Email (použijte SMTP od hostingu nebo SendGrid)
MAIL_MAILER=smtp
MAIL_HOST=smtp.vase-domena.cz
MAIL_PORT=587
MAIL_USERNAME=noreply@vase-domena.cz
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vase-domena.cz
MAIL_FROM_NAME=SyncMyDay

# Webhooky
WEBHOOK_BASE_URL=https://vase-domena.cz/webhooks

# Bezpečnost
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120
LOG_LEVEL=error
```

#### 1.3 Vygenerujte klíče

```bash
# Lokálně ve vašem projektu:

# APP_KEY
php artisan key:generate --show

# TOKEN_ENCRYPTION_KEY
php -r "echo 'TOKEN_ENCRYPTION_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

Uložte si tyto klíče a vložte do `.env` souboru.

#### 1.4 Nainstalujte závislosti lokálně

```bash
cd /Users/lukas/SyncMyDay

# Nainstalujte produkční závislosti
composer install --no-dev --optimize-autoloader

# Optimalizace
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### KROK 2: Příprava souborů pro upload

#### 2.1 Vytvořte archiv projektu

```bash
# Vytvořte archiv BEZ .git, node_modules, tests
tar -czf syncmyday-production.tar.gz \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='.env' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  .
```

Nebo vytvořte ZIP soubor ručně s těmito složkami:
```
✅ app/
✅ bootstrap/
✅ config/
✅ database/
✅ lang/
✅ public/
✅ resources/
✅ routes/
✅ storage/ (prázdné podsložky)
✅ vendor/
✅ artisan
✅ composer.json
✅ composer.lock
❌ .git/
❌ node_modules/
❌ tests/
❌ .env (nahrajete zvlášť)
```

---

### KROK 3: Nahrání na hosting

#### Varianta A: Přes SSH (pokud je dostupné - např. Wedos)

```bash
# Připojte se na hosting
ssh uzivatel@ssh.vase-domena.cz

# Vytvořte adresář pro projekt
mkdir -p ~/aplikace/syncmyday
cd ~/aplikace/syncmyday

# Nahrajte archiv (z druhého terminálu na vašem počítači)
scp syncmyday-production.tar.gz uzivatel@ssh.vase-domena.cz:~/aplikace/syncmyday/

# Zpět na serveru - rozbalte archiv
cd ~/aplikace/syncmyday
tar -xzf syncmyday-production.tar.gz
rm syncmyday-production.tar.gz

# Vytvořte .env soubor
nano .env
# (vložte obsah z kroku 1.2)

# Nastavte oprávnění
chmod 600 .env
chmod -R 775 storage bootstrap/cache
```

#### Varianta B: Přes FTP/cPanel File Manager (bez SSH)

1. **Přihlaste se do cPanel** vašeho hostingu
2. **File Manager** → Přejděte do složky `public_html` nebo `www`
3. **Vytvořte složku** `syncmyday` (nebo jiný název)
4. **Nahrajte** `syncmyday-production.tar.gz` nebo ZIP soubor
5. **Rozbalte** archiv přes cPanel File Manager (pravý klik → Extract)
6. **Vytvořte soubor** `.env` a zkopírujte do něj konfiguraci z kroku 1.2
7. **Nastavte oprávnění**:
   - `.env` → 600 (pouze vlastník může číst/zapisovat)
   - `storage/` → 775 (rekurzivně pro všechny podsložky)
   - `bootstrap/cache/` → 775

---

### KROK 4: Nastavení databáze

#### 4.1 Vytvořte databázi v cPanel

1. **MySQL Databases** v cPanel
2. **Create New Database**: `vase_jmeno_syncmyday`
3. **Create New User**: `vase_jmeno_sync`
4. **Add User to Database** - vyberte uživatele a databázi
5. **Přidělte oprávnění**: ALL PRIVILEGES
6. Poznamenejte si:
   - Database name: `vase_jmeno_syncmyday`
   - Username: `vase_jmeno_sync`
   - Password: (vaše zvolené heslo)
   - Host: `localhost` (obvykle)

#### 4.2 Aktualizujte .env soubor

Upravte v `.env` sekci databáze:

```env
DB_HOST=localhost
DB_DATABASE=vase_jmeno_syncmyday
DB_USERNAME=vase_jmeno_sync
DB_PASSWORD=vase_heslo
```

#### 4.3 Spusťte migrace

**Přes SSH:**
```bash
cd ~/aplikace/syncmyday
php artisan migrate --force
```

**Bez SSH** (vytvořte dočasný skript):

1. Vytvořte soubor `install.php` v root složce projektu:

```php
<?php
// install.php - jednorázový instalační skript
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Spustit migrace
$status = $kernel->call('migrate', ['--force' => true]);

echo "Migrace dokončeny!\n";
echo "Status: " . $status . "\n";

// DŮLEŽITÉ: Po spuštění tento soubor SMAŽTE!
echo "\n⚠️  NYNÍ SMAŽTE tento soubor install.php ze serveru!\n";
```

2. Otevřete v prohlížeči: `https://vase-domena.cz/install.php`
3. **IHNED po dokončení smažte soubor** `install.php`!

---

### KROK 5: Nastavení Document Root

Aplikace musí běžet ze složky `public/`, ne z kořenové složky projektu.

#### V cPanel:

1. **Domains** → najděte vaši doménu
2. **Document Root** → změňte na: `/aplikace/syncmyday/public`
3. Uložte

#### Nebo přes .htaccess (starší hostingy):

Pokud nemůžete změnit document root, vytvořte `.htaccess` v kořenu domény:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

---

### KROK 6: Nastavení Cron Job (KRITICKÉ!)

Protože nemáte queue worker běžící na pozadí, musíte spouštět cron každých **5 minut**.

#### V cPanel:

1. **Cron Jobs**
2. **Add New Cron Job**:
   - **Common Setting**: Every 5 Minutes (*/5 * * * *)
   - **Command**:
   ```bash
   cd /home/vase_uzivatel/aplikace/syncmyday && php artisan schedule:run >> /dev/null 2>&1
   ```
   
3. **Přidejte další cron pro zpracování fronty**:
   - **Common Setting**: Every 5 Minutes (*/5 * * * *)
   - **Command**:
   ```bash
   cd /home/vase_uzivatel/aplikace/syncmyday && php artisan queue:work --stop-when-empty --max-time=240 >> /dev/null 2>&1
   ```

#### Bez cPanel (starší hostingy):

Kontaktujte podporu hostingu a požádejte je o nastavení těchto dvou cron jobů.

---

### KROK 7: Optimalizace a zabezpečení

```bash
# Přes SSH:
cd ~/aplikace/syncmyday

# Cache konfigurace
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Nastavte oprávnění
chmod -R 775 storage bootstrap/cache
chmod 600 .env

# Vytvořte symbolic link pro storage (pokud potřebujete)
php artisan storage:link
```

---

### KROK 8: Konfigurace OAuth (Google + Microsoft)

#### Google Calendar API

1. Jděte na https://console.cloud.google.com/
2. Vytvořte projekt nebo vyberte existující
3. **APIs & Services** → **Library** → **Google Calendar API** → **Enable**
4. **Credentials** → **Create Credentials** → **OAuth 2.0 Client ID**
5. **Authorized redirect URIs**:
   ```
   https://vase-domena.cz/oauth/google/callback
   ```
6. Zkopírujte Client ID a Secret do `.env`

#### Microsoft Graph API

1. Jděte na https://portal.azure.com/
2. **App registrations** → **New registration**
3. **Redirect URI**: `https://vase-domena.cz/oauth/microsoft/callback`
4. **Certificates & secrets** → New client secret
5. **API permissions** → Microsoft Graph:
   - Calendars.ReadWrite
   - User.Read
   - offline_access
6. Zkopírujte Client ID a Secret do `.env`

#### Stripe (platby)

1. https://dashboard.stripe.com/
2. Přepněte na **Live mode**
3. **Developers** → **API keys**
4. Zkopírujte klíče do `.env`
5. **Webhooks** → Add endpoint:
   - URL: `https://vase-domena.cz/webhooks/stripe`
   - Events: `customer.subscription.*`

---

### KROK 9: Test aplikace

1. **Otevřete** `https://vase-domena.cz`
2. **Registrujte se** jako první uživatel
3. **Připojte testovací kalendář**
4. **Vytvořte sync rule**
5. **Ručně spusťte synchronizaci** (v admin panelu nebo přes cron)

---

## 🔧 Speciální konfigurace pro sdílený hosting

### Vypnutí Redis

V `config/cache.php` ověřte:

```php
'default' => env('CACHE_DRIVER', 'database'),
```

V `config/queue.php`:

```php
'default' => env('QUEUE_CONNECTION', 'database'),
```

### Použití database queue

V `config/queue.php` ověřte, že máte:

```php
'database' => [
    'driver' => 'database',
    'table' => 'jobs',
    'queue' => 'default',
    'retry_after' => 90,
],
```

### Zpracování fronty přes cron

Protože nemáte stále běžícího queue workera, fronta se zpracovává přes cron každých 5 minut:

```bash
*/5 * * * * cd /cesta/k/projektu && php artisan queue:work --stop-when-empty --max-time=240
```

`--stop-when-empty` = ukončí se, když fronta je prázdná
`--max-time=240` = maximálně 4 minuty (bezpečně před dalším cronem)

---

## ⚠️ Omezení sdíleného hostingu

### 1. Synchronizace není real-time

- **Na VPS**: Událost se synchronizuje během 1-2 minut (webhooky)
- **Na sdíleném hostingu**: Synchronizace každých 5 minut (cron)

### 2. Webhooky nebudou fungovat optimálně

Webhooky od Google/Microsoft sice přijdou, ale zpracují se až při dalším spuštění cronu.

**Řešení**: V `app/Http/Controllers/WebhookController.php` můžete webhooky zpracovat okamžitě místo přes frontu:

```php
// Místo:
ProcessCalendarWebhookJob::dispatch($connection);

// Použijte:
ProcessCalendarWebhookJob::dispatchSync($connection);
```

### 3. Vyšší zátěž databáze

Cache a sessions v databázi místo Redis = více dotazů na DB.

**Řešení**: Pravidelně čistit staré sessions:

```bash
# Přidejte do cronu (denně)
0 3 * * * cd /cesta/k/projektu && php artisan session:gc
```

---

## 📊 Monitoring a údržba

### Kontrola logů

**Přes SSH:**
```bash
tail -f storage/logs/laravel.log
```

**Přes cPanel File Manager:**
1. Otevřete `storage/logs/laravel.log`
2. Zkontrolujte poslední řádky

### Kontrola fronty

**Přes SSH:**
```bash
php artisan queue:monitor
php artisan queue:failed
```

**Přes databázi (cPanel phpMyAdmin):**
```sql
-- Zkontrolovat čekající joby
SELECT * FROM jobs;

-- Zkontrolovat selhané joby
SELECT * FROM failed_jobs;

-- Vymazat staré záznamy (starší než 7 dní)
DELETE FROM jobs WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
```

### Automatické čištění (přidejte do App\Console\Kernel.php)

```php
protected function schedule(Schedule $schedule)
{
    // Existující tasky...
    
    // Čištění pro sdílený hosting
    $schedule->command('queue:prune-failed --hours=168')->daily(); // 7 dní
    $schedule->command('cache:prune-stale-tags')->hourly();
}
```

---

## 🔒 Bezpečnost

### .htaccess ochrana

Vytvořte `.htaccess` v kořenu projektu (mimo `public/`):

```apache
# Zakázat přístup k celému projektu kromě public/
<FilesMatch "\.">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

### Ochrana .env souboru

V `public/.htaccess` (už by mělo být):

```apache
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### Zakázat directory listing

```apache
Options -Indexes
```

---

## 🆘 Časté problémy

### "500 Internal Server Error"

**Řešení:**
1. Zkontrolujte `storage/logs/laravel.log`
2. Ověřte oprávnění složek:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```
3. Smažte cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

### "Class not found"

**Řešení:**
```bash
composer dump-autoload --optimize
php artisan clear-compiled
php artisan optimize
```

### Synchronizace nefunguje

**Řešení:**
1. Zkontrolujte, že cron běží:
   - cPanel → Cron Jobs → zkontrolujte "Current Cron Jobs"
2. Ručně spusťte frontu:
   ```bash
   php artisan queue:work --once
   ```
3. Zkontrolujte failed jobs:
   ```bash
   php artisan queue:failed
   ```

### Databázové chyby

**Řešení:**
1. Ověřte přihlašovací údaje v `.env`
2. Zkontrolujte, že databázový uživatel má všechna oprávnění
3. Test připojení:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPdo();
   ```

---

## 📞 Doporučení pro upgrade

Pokud vás omezení sdíleného hostingu obtěžují, zvažte upgrade na:

1. **Wedos VPS Start** (199 Kč/měsíc)
   - Plný přístup, Redis, supervisor
   - Real-time synchronizace
   
2. **Hetzner CPX11** (4.15 €/měsíc = ~100 Kč)
   - Nejlepší poměr cena/výkon
   - S Dockerem jednoduchý deployment

Postupujte pak podle `NASAZENI_PRODUKCE.md`.

---

## ✅ Checklist pro sdílený hosting

```
□ PHP 8.2+ ověřeno
□ Databáze vytvořena v cPanel
□ Projekt nahrán na hosting
□ .env soubor vytvořen a nakonfigurován
□ APP_KEY vygenerován
□ TOKEN_ENCRYPTION_KEY vygenerován
□ Document root nastaven na /public
□ Migrace spuštěny (install.php nebo SSH)
□ Oprávnění složek nastavena (775 storage/)
□ Cron joby nastaveny (každých 5 minut)
□ Google OAuth produkční credentials
□ Microsoft OAuth produkční credentials
□ Stripe LIVE klíče
□ Email SMTP nakonfigurováno
□ SSL certifikát aktivní (HTTPS)
□ Testovací registrace funguje
□ Testovací sync rule vytvořena a funguje
```

---

**Hodně štěstí s nasazením! I na sdíleném hostingu to půjde, jen s mírnými omezeními. 🚀**

Pro real-time funkčnost doporučuji upgrade na VPS (~200 Kč/měsíc).


