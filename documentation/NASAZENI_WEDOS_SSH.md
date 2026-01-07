# 🌐 Nasazení na WebHosting s omezeným SSH (Wedos, WebSupport)

Průvodce pro nasazení na webhostingu s SSH přístupem, ale bez možnosti instalace balíčků.

**Typické pro:** Wedos WebHosting M/L, WebSupport s SSH, podobné managed hostingy

---

## 📋 Co máte k dispozici

### ✅ Funguje:

- PHP přes SSH (s parametrem `-c domena.cz`)
- Composer
- Git (klonování, pull)
- MySQL přes admin panel
- Cron přes admin panel
- SCP/rsync pro upload

### ❌ Nefunguje:

- Docker
- Redis (použijete database cache)
- Root přístup
- Instalace balíčků (apt/yum)
- Supervisor (queue workers přes cron)

---

## 🚀 Nasazení krok za krokem

### KROK 1: Příprava lokálně (5 minut)

```bash
cd /Users/lukas/SyncMyDay

# Spusťte pomocný skript
./prepare-shared-hosting.sh
```

Skript vytvoří `.env.shared-hosting` - otevřete ho a vyplňte:

- Databázové údaje (dostanete v kroku 3)
- Google OAuth klíče
- Microsoft OAuth klíče
- Stripe LIVE klíče
- Email SMTP

---

### KROK 2: Připojení přes SSH

```bash
# Připojte se na server
ssh uzivatel@ssh.vase-domena.cz

# Ověřte PHP verzi (musí být 8.2+)
php -c vase-domena.cz -v

# Zjistěte, kde jste
pwd
# Typicky: /data/web/virtuals/12345/virtual

# Přejděte do složky pro vaši doménu
cd ~/vase-domena.cz
# nebo
cd ~/domains/vase-domena.cz
# nebo podle struktury vašeho hostingu

# Seznam složek
ls -la
```

**💡 Tip:** Na Wedos je struktura obvykle:

```
~/
├── domains/
│   └── vase-domena.cz/
│       ├── html/          ← veřejný web (document root)
│       └── subdomains/
```

---

### KROK 3: Vytvoření databáze (přes admin panel)

1. **Přihlaste se do admin panelu** (cPanel/Webhosting admin)
2. **MySQL databáze** → Vytvořit novou
   - Název: `vase_jmeno_syncmyday`
   - Uživatel: `vase_jmeno_sync`
   - Heslo: (silné heslo)
3. **Poznamenejte si údaje** pro .env

---

### KROK 4: Upload projektu přes Git

#### Varianta A: Git clone (pokud máte repo)

```bash
# Na serveru:
cd ~/domains/vase-domena.cz
mkdir app
cd app

# Klonování (pokud máte repo)
git clone https://github.com/vas-ucet/SyncMyDay.git .

# Nebo pokud nemáte Git repo, použijte variantu B (SCP)
```

#### Varianta B: Upload přes SCP (z lokálního počítače)

```bash
# Vytvořte archiv lokálně (bez .git, tests)
cd /Users/lukas/SyncMyDay
tar -czf syncmyday.tar.gz \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='.env' \
  --exclude='storage/logs/*' \
  --exclude='storage/framework/cache/*' \
  .

# Nahrajte na server
scp syncmyday.tar.gz uzivatel@ssh.vase-domena.cz:~/domains/vase-domena.cz/app/

# Na serveru:
cd ~/domains/vase-domena.cz/app
tar -xzf syncmyday.tar.gz
rm syncmyday.tar.gz
```

---

### KROK 5: Konfigurace .env

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

# Zkopírujte .env soubor (lokálně jste ho připravili)
nano .env
```

Vložte obsah z `.env.shared-hosting` a upravte:

```env
APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:VYGENEROVANÝ_KLÍČ
APP_URL=https://vase-domena.cz

# Databáze (z kroku 3)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=vase_jmeno_syncmyday
DB_USERNAME=vase_jmeno_sync
DB_PASSWORD=vase_heslo

# DŮLEŽITÉ pro WebHosting!
CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
REDIS_CLIENT=null

# Token encryption
TOKEN_ENCRYPTION_KEY=base64:VYGENEROVANÝ_KLÍČ

# Další konfigurace...
```

Uložte (Ctrl+O, Enter, Ctrl+X).

```bash
# Nastavte oprávnění
chmod 600 .env
```

---

### KROK 6: Instalace závislostí

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

# Composer install (může trvat pár minut)
composer install --no-dev --optimize-autoloader --no-interaction

# Ověřte
ls -la vendor/
```

---

### KROK 7: Vygenerování APP_KEY

```bash
# Na serveru (DŮLEŽITÉ: použijte -c s vaší doménou!)
cd ~/domains/vase-domena.cz/app

php -c vase-domena.cz artisan key:generate

# Ověřte, že se klíč uložil do .env
grep APP_KEY .env
```

---

### KROK 8: Nastavení oprávnění

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

chmod -R 775 storage bootstrap/cache

# Vytvořte potřebné složky, pokud neexistují
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
```

---

### KROK 9: Spuštění migrací

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

# Spusťte migrace
php -c vase-domena.cz artisan migrate --force

# Měli byste vidět:
# Migration table created successfully.
# Migrating: ...
# Migrated: ...
```

Pokud migrace selžou, zkontrolujte:

1. Údaje k databázi v .env
2. Že databáze existuje (admin panel)
3. Že uživatel má oprávnění

---

### KROK 10: Optimalizace cache

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

php -c vase-domena.cz artisan config:cache
php -c vase-domena.cz artisan route:cache
php -c vase-domena.cz artisan view:cache
```

---

### KROK 11: Nastavení Document Root

V admin panelu hostingu:

1. **Nastavení domény** → najděte `vase-domena.cz`
2. **Document Root** změňte na: `/app/public`

   Celá cesta bude například:

   ```
   /data/web/virtuals/12345/virtual/domains/vase-domena.cz/app/public
   ```

   Nebo relativně:

   ```
   ~/domains/vase-domena.cz/app/public
   ```

3. **Uložte změny**

**💡 Tip:** Na Wedos je to v: Webhosting → Domény → Upravit → Hlavní složka webu

---

### KROK 12: Nastavení Cron jobů (KRITICKÉ!)

V admin panelu hostingu:

1. **Cron / Plánované úlohy**
2. **Přidat nový cron**

#### Cron 1: Laravel Scheduler (každou minutu)

```bash
* * * * * cd ~/domains/vase-domena.cz/app && php -c vase-domena.cz artisan schedule:run >> /dev/null 2>&1
```

#### Cron 2: Queue Worker (každých 5 minut)

```bash
*/5 * * * * cd ~/domains/vase-domena.cz/app && php -c vase-domena.cz artisan queue:work --stop-when-empty --max-time=240 >> /dev/null 2>&1
```

**Vysvětlení:**

- `--stop-when-empty` = ukončí se, když fronta je prázdná
- `--max-time=240` = běží max 4 minuty (bezpečně před dalším cronem)
- `-c vase-domena.cz` = důležité pro správnou konfiguraci PHP

---

### KROK 13: SSL certifikát

Většina hostingů nabízí **Let's Encrypt zdarma**:

1. Admin panel → **SSL certifikáty**
2. **Let's Encrypt** → Vygenerovat pro `vase-domena.cz`
3. Počkejte pár minut na aktivaci
4. **Vynutit HTTPS** (redirect z HTTP na HTTPS)

---

### KROK 14: Test aplikace 🎉

```bash
# Otevřete v prohlížeči:
https://vase-domena.cz

# Měli byste vidět úvodní stránku SyncMyDay!
```

**Pokud vidíte chybu 500:**

```bash
# Zkontrolujte logy na serveru:
cd ~/domains/vase-domena.cz/app
tail -50 storage/logs/laravel.log
```

---

## 🔧 Konfigurace OAuth a API

### Google Calendar API

1. https://console.cloud.google.com/
2. Vytvořte projekt
3. APIs & Services → Library → Google Calendar API → Enable
4. Credentials → OAuth 2.0 Client ID
5. Authorized redirect URI: `https://vase-domena.cz/oauth/google/callback`
6. Zkopírujte Client ID a Secret do `.env`

### Microsoft Graph API

1. https://portal.azure.com/
2. App registrations → New
3. Redirect URI: `https://vase-domena.cz/oauth/microsoft/callback`
4. Certificates & secrets → New client secret
5. API permissions → Calendars.ReadWrite, offline_access
6. Zkopírujte do `.env`

### Stripe

1. https://dashboard.stripe.com/
2. Přepněte na **LIVE mode**
3. Developers → API keys
4. Zkopírujte pk*live* a sk*live* do `.env`
5. Webhooks → Add endpoint: `https://vase-domena.cz/webhooks/stripe`

Po změně .env:

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app
php -c vase-domena.cz artisan config:clear
php -c vase-domena.cz artisan config:cache
```

---

## 🔄 Aktualizace aplikace (budoucí)

### Přes Git:

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

# Stáhnout změny
git pull origin master

# Nainstalovat nové závislosti
composer install --no-dev --optimize-autoloader

# Spustit migrace
php -c vase-domena.cz artisan migrate --force

# Vyčistit a znovu vytvořit cache
php -c vase-domena.cz artisan config:clear
php -c vase-domena.cz artisan config:cache
php -c vase-domena.cz artisan route:cache
php -c vase-domena.cz artisan view:cache

# Hotovo!
```

### Přes SCP (bez Git):

```bash
# Lokálně vytvořte nový archiv
cd /Users/lukas/SyncMyDay
tar -czf update.tar.gz <soubory>

# Nahrajte
scp update.tar.gz uzivatel@ssh.vase-domena.cz:~/

# Na serveru rozbalte
cd ~/domains/vase-domena.cz/app
tar -xzf ~/update.tar.gz
rm ~/update.tar.gz

# Cache
php -c vase-domena.cz artisan config:cache
```

---

## 🆘 Řešení problémů

### "Class not found" chyba

```bash
cd ~/domains/vase-domena.cz/app
composer dump-autoload --optimize
php -c vase-domena.cz artisan clear-compiled
php -c vase-domena.cz artisan config:clear
```

### "Permission denied" chyba

```bash
chmod -R 775 storage bootstrap/cache
chmod 600 .env
```

### Synchronizace nefunguje

```bash
# Zkontrolujte, že cron běží (admin panel)
# Ručně spusťte queue:
php -c vase-domena.cz artisan queue:work --once

# Zkontrolujte failed jobs:
php -c vase-domena.cz artisan queue:failed
```

### Logy nejsou vidět

```bash
# Zobrazit poslední chyby:
tail -100 storage/logs/laravel.log

# Nebo stáhnout lokálně:
# Z vašeho počítače:
scp uzivatel@ssh.vase-domena.cz:~/domains/vase-domena.cz/app/storage/logs/laravel.log ~/Desktop/
```

---

## 📊 Monitoring

### Kontrola stavu

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

# Zdraví aplikace
php -c vase-domena.cz artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Fronta
php -c vase-domena.cz artisan queue:monitor

# Failed jobs
php -c vase-domena.cz artisan queue:failed
```

### Uptime monitoring (zdarma)

1. Registrace na https://uptimerobot.com/
2. Add Monitor → HTTP(s)
3. URL: `https://vase-domena.cz/health`
4. Interval: 5 minut

---

## 🔒 Bezpečnost

### Checklist:

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app

# Ověřte .env
grep APP_DEBUG .env  # mělo by být "false"
grep APP_ENV .env    # mělo by být "production"

# Oprávnění
ls -la .env          # mělo by být -rw------- (600)
ls -la storage       # mělo by být drwxrwxr-x (775)

# SSL
curl -I https://vase-domena.cz  # mělo by vrátit 200 OK přes HTTPS
```

---

## 💡 Tipy a triky

### 1. Zkrácení příkazu php

Vytvořte alias:

```bash
# Na serveru v ~/.bashrc nebo ~/.bash_profile
echo 'alias artisan="php -c vase-domena.cz artisan"' >> ~/.bashrc
source ~/.bashrc

# Pak můžete použít:
artisan migrate
artisan queue:work
```

### 2. Rychlý deployment skript

```bash
# Na serveru vytvořte ~/deploy.sh
nano ~/deploy.sh
```

```bash
#!/bin/bash
cd ~/domains/vase-domena.cz/app
git pull origin master
composer install --no-dev --optimize-autoloader
php -c vase-domena.cz artisan migrate --force
php -c vase-domena.cz artisan config:cache
php -c vase-domena.cz artisan route:cache
php -c vase-domena.cz artisan view:cache
echo "✅ Deployment dokončen!"
```

```bash
chmod +x ~/deploy.sh

# Použití:
bash ~/deploy.sh
```

### 3. Zálohy databáze

Většina hostingů má automatické zálohy v admin panelu:

- Admin panel → Zálohy → Databáze
- Doporučeno: denní zálohy, uchovávat 7 dní

---

## ✅ Finální checklist

```
PŘED NASAZENÍM:
□ .env.shared-hosting připraven lokálně
□ API klíče získány (Google, Microsoft, Stripe)
□ Databáze vytvořena v admin panelu
□ SSH přístup funguje

NASAZENÍ:
□ Soubory nahrány (Git nebo SCP)
□ Composer install dokončen
□ .env soubor vytvořen a konfigurován
□ APP_KEY vygenerován
□ Oprávnění nastavena (775 storage, 600 .env)
□ Migrace spuštěny
□ Cache optimalizována
□ Document root nastaven na /app/public
□ Cron joby nastaveny (2x)
□ SSL certifikát aktivní

PO NASAZENÍ:
□ Aplikace běží na https://domena.cz
□ Registrace funguje
□ OAuth připojení fungují
□ Sync rule vytvořena
□ Synchronizace probíhá (každých 5 min)
□ Monitoring nastaven (UptimeRobot)
□ První admin uživatel vytvořen
```

---

## 📞 Wedos specifika

### Struktura složek:

```
~/
├── domains/
│   └── vase-domena.cz/
│       └── app/              ← sem nahrajete projekt
│           ├── public/       ← document root
│           ├── app/
│           ├── config/
│           └── ...
```

### PHP příkaz:

```bash
php -c vase-domena.cz artisan ...
```

### Cron v admin panelu:

- Webhosting → Úlohy
- Přidat úlohu
- Každou minutu: `* * * * *`
- Příkaz: `cd ~/domains/vase-domena.cz/app && php -c vase-domena.cz artisan schedule:run`

### Logs:

```bash
tail -f ~/domains/vase-domena.cz/app/storage/logs/laravel.log
```

---

## 🎉 Hotovo!

Aplikace by měla běžet na `https://vase-domena.cz`!

**Další kroky:**

1. Zaregistrujte se jako první uživatel
2. Nastavte se jako admin (viz níže)
3. Připojte testovací kalendáře
4. Ověřte synchronizaci

### Nastavit se jako admin:

```bash
# Na serveru:
cd ~/domains/vase-domena.cz/app
php -c vase-domena.cz artisan tinker

>>> $user = User::where('email', 'vas@email.cz')->first();
>>> $user->is_admin = true;
>>> $user->save();
>>> exit
```

---

**Hodně štěstí! 🚀**
