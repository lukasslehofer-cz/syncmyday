# 🚀 Průvodce nasazením SyncMyDay na produkční hosting

Tento průvodce vám pomůže s přesunem aplikace SyncMyDay na veřejný hosting.

## 📋 Obsah

1. [Výběr hostingu](#výběr-hostingu)
2. [Požadavky serveru](#požadavky-serveru)
3. [Příprava projektu](#příprava-projektu)
4. [Nasazení krok za krokem](#nasazení-krok-za-krokem)
5. [Konfigurace služeb třetích stran](#konfigurace-služeb-třetích-stran)
6. [Bezpečnost](#bezpečnost)
7. [Monitoring a údržba](#monitoring-a-údržba)
8. [Řešení problémů](#řešení-problémů)

---

## 🏢 Výběr hostingu

### Doporučené možnosti pro české uživatele:

#### **A) VPS/Dedicated Server (doporučeno pro plnou kontrolu)**

| Poskytovatel        | Cena                  | Výhody                                | Nevýhody            |
| ------------------- | --------------------- | ------------------------------------- | ------------------- |
| **Hetzner** (DE)    | Od 300 Kč/měsíc       | Nejlepší poměr cena/výkon, rychlé, EU | Německý server      |
| **Wedos VPS** (CZ)  | Od 199 Kč/měsíc       | Český support, GDPR friendly          | Dražší než Hetzner  |
| **Forpsi VPS** (CZ) | Od 249 Kč/měsíc       | Český poskytovatel                    | Starší technologie  |
| **DigitalOcean**    | Od $6/měsíc (~140 Kč) | Skvělá dokumentace, globální          | Platba kartou v USD |

**Doporučení**: **Hetzner Cloud** - CPX11 (2 vCPU, 2GB RAM, 40GB SSD) za 4.15€/měsíc

#### **B) Managed Laravel Hosting**

| Poskytovatel                     | Cena           | Výhody                          | Nevýhody         |
| -------------------------------- | -------------- | ------------------------------- | ---------------- |
| **Laravel Forge + DigitalOcean** | $12 + $6/měsíc | Automatizované nasazení, backup | Dražší, anglicky |
| **Ploi.io**                      | Od $10/měsíc   | Jednodušší než Forge            | Anglicky         |

#### **C) Sdílený hosting (NEpovinné - nedoporučeno)**

Aplikace vyžaduje:

- Queue workers (na pozadí běžící procesy)
- Cron job každou minutu
- Redis server
- Přístup k SSH

**Většina sdílených hostingů tohle nepodporuje!**

---

## 🖥️ Požadavky serveru

### Minimální požadavky:

```
✅ Ubuntu 20.04+ / Debian 11+ / CentOS 8+
✅ PHP 8.2 nebo novější
✅ MySQL 8.0+ nebo MariaDB 10.6+
✅ Redis 6.0+
✅ Composer 2.x
✅ Node.js 18+ (volitelné, pro asset building)
✅ Nginx nebo Apache
✅ SSL certifikát (Let's Encrypt zdarma)
✅ 2GB RAM (minimum), 4GB+ doporučeno
✅ 10GB+ diskový prostor
```

### PHP rozšíření:

```bash
# Potřebná PHP rozšíření:
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO MySQL
- Tokenizer
- XML
- cURL
- GD nebo Imagick
- Sodium (pro šifrování tokenů)
- Redis extension
```

---

## 🎯 Příprava projektu

### 1. Vytvořte .env soubor

```bash
# Ve vašem lokálním projektu:
cp .env.example .env.production
```

Upravte `.env.production` pro produkci:

```env
# KRITICKÉ ZMĚNY PRO PRODUKCI!

APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_URL=https://vase-domena.cz

# Databáze
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=syncmyday_prod
DB_USERNAME=syncmyday_prod
DB_PASSWORD=VYGENERUJTE_SILNE_HESLO

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=VYGENERUJTE_SILNE_HESLO
REDIS_PORT=6379

# TOKEN ENCRYPTION KEY - NIKDY nesdílejte!
# Vygenerujte: php -r "echo 'TOKEN_ENCRYPTION_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
TOKEN_ENCRYPTION_KEY=base64:XXXXX

# Google OAuth (produkční)
GOOGLE_CLIENT_ID=your-prod-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-prod-secret
GOOGLE_REDIRECT_URI=https://vase-domena.cz/oauth/google/callback

# Microsoft OAuth (produkční)
MICROSOFT_CLIENT_ID=your-prod-client-id
MICROSOFT_CLIENT_SECRET=your-prod-secret
MICROSOFT_REDIRECT_URI=https://vase-domena.cz/oauth/microsoft/callback
MICROSOFT_TENANT=common

# Stripe (LIVE klíče!)
STRIPE_KEY=pk_live_XXXXX
STRIPE_SECRET=sk_live_XXXXX
STRIPE_WEBHOOK_SECRET=whsec_XXXXX
STRIPE_PRO_PRICE_ID=price_XXXXX

# Email (příklad SendGrid)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=SG.XXXXX
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@vase-domena.cz
MAIL_FROM_NAME=SyncMyDay

# Webhooky
WEBHOOK_BASE_URL=https://vase-domena.cz/webhooks

# Bezpečnost
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Logování
LOG_LEVEL=error
LOG_CHANNEL=daily
```

### 2. Vygenerujte bezpečnostní klíče

```bash
# APP_KEY
php artisan key:generate --show

# TOKEN_ENCRYPTION_KEY
php -r "echo 'TOKEN_ENCRYPTION_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

**⚠️ DŮLEŽITÉ**: Tyto klíče si bezpečně uložte! Po nasazení je NIKDY neměňte!

### 3. Commitněte aktuální stav (volitelné)

```bash
git add .
git commit -m "Příprava pro produkční nasazení"
git push origin master
```

---

## 🚀 Nasazení krok za krokem

### Varianta A: S Dockerem (jednodušší)

#### 1. Připojte se na server

```bash
ssh root@ip-adresa-serveru
```

#### 2. Nainstalujte Docker

```bash
# Instalace Docker & Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Ověření
docker --version
docker-compose --version
```

#### 3. Naklonujte projekt

```bash
cd /var/www
git clone https://github.com/vase-uzivatel/SyncMyDay.git syncmyday
cd syncmyday
```

#### 4. Nakonfigurujte prostředí

```bash
# Vytvořte .env soubor
nano .env
```

Vložte obsah vašeho `.env.production` souboru.

```bash
# Nastavte oprávnění
chmod 600 .env
chown www-data:www-data .env
```

#### 5. Vygenerujte APP_KEY

```bash
# Nejprve spusťte kontejnery
docker-compose up -d

# Vygenerujte klíč
docker-compose exec app php artisan key:generate

# Ověřte, že se klíč uložil do .env
```

#### 6. Spusťte migrace

```bash
docker-compose exec app php artisan migrate --force
```

#### 7. Optimalizujte pro produkci

```bash
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
docker-compose exec app php artisan optimize
```

#### 8. Nastavte oprávnění

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

### Varianta B: Bez Dockeru (pokročilé)

#### 1. Nainstalujte závislosti

```bash
# Aktualizace systému
sudo apt update && sudo apt upgrade -y

# PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-redis php8.2-xml \
    php8.2-mbstring php8.2-curl php8.2-bcmath php8.2-gd php8.2-zip \
    php8.2-cli php8.2-sodium

# Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# MySQL
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Redis
sudo apt install -y redis-server
sudo systemctl enable redis-server

# Nginx
sudo apt install -y nginx
```

#### 2. Vytvořte databázi

```bash
sudo mysql
```

```sql
CREATE DATABASE syncmyday_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'syncmyday_prod'@'localhost' IDENTIFIED BY 'SILNE_HESLO';
GRANT ALL PRIVILEGES ON syncmyday_prod.* TO 'syncmyday_prod'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 3. Nastavte Redis heslo

```bash
sudo nano /etc/redis/redis.conf
```

Najděte řádek `# requirepass foobared` a změňte na:

```
requirepass VASE_REDIS_HESLO
```

```bash
sudo systemctl restart redis-server
```

#### 4. Naklonujte a nainstalujte projekt

```bash
cd /var/www
sudo git clone https://github.com/vase-uzivatel/SyncMyDay.git syncmyday
cd syncmyday

# Nainstalujte závislosti
sudo composer install --no-dev --optimize-autoloader

# Zkopírujte .env
sudo cp .env.production .env

# Vygenerujte klíč
sudo php artisan key:generate

# Migrace
sudo php artisan migrate --force

# Cache
sudo php artisan config:cache
sudo php artisan route:cache
sudo php artisan view:cache

# Oprávnění
sudo chown -R www-data:www-data /var/www/syncmyday
sudo chmod -R 775 storage bootstrap/cache
```

#### 5. Nastavte Nginx

```bash
sudo nano /etc/nginx/sites-available/syncmyday
```

```nginx
server {
    listen 80;
    server_name vase-domena.cz www.vase-domena.cz;
    root /var/www/syncmyday/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 20M;
}
```

```bash
# Aktivujte konfiguraci
sudo ln -s /etc/nginx/sites-available/syncmyday /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 6. Nastavte SSL (Let's Encrypt)

```bash
# Instalace Certbot
sudo apt install -y certbot python3-certbot-nginx

# Získání certifikátu
sudo certbot --nginx -d vase-domena.cz -d www.vase-domena.cz

# Auto-renewal je nastaven automaticky
sudo certbot renew --dry-run
```

#### 7. Nastavte Queue Worker (Supervisor)

```bash
sudo apt install -y supervisor

sudo nano /etc/supervisor/conf.d/syncmyday-worker.conf
```

```ini
[program:syncmyday-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/syncmyday/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/syncmyday/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start syncmyday-worker:*
```

#### 8. Nastavte Scheduler (Cron)

```bash
sudo crontab -e -u www-data
```

Přidejte:

```cron
* * * * * cd /var/www/syncmyday && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 Konfigurace služeb třetích stran

### 1. Google Calendar API

```bash
1. Jděte na https://console.cloud.google.com/
2. Vyberte projekt nebo vytvořte nový
3. APIs & Services → Library → Google Calendar API → Enable
4. APIs & Services → Credentials → Create Credentials → OAuth 2.0 Client ID
5. Application type: Web application
6. Authorized redirect URIs:
   - https://vase-domena.cz/oauth/google/callback
   - https://www.vase-domena.cz/oauth/google/callback
7. Zkopírujte Client ID a Client Secret do .env
```

### 2. Microsoft Graph API (Office 365)

```bash
1. Jděte na https://portal.azure.com/
2. Azure Active Directory → App registrations → New registration
3. Name: SyncMyDay Production
4. Supported account types: Accounts in any organizational directory and personal Microsoft accounts
5. Redirect URI: https://vase-domena.cz/oauth/microsoft/callback
6. Klikněte Register

Po vytvoření:
7. Overview → zkopírujte Application (client) ID do .env jako MICROSOFT_CLIENT_ID
8. Certificates & secrets → New client secret → zkopírujte do .env jako MICROSOFT_CLIENT_SECRET
9. API permissions → Add a permission → Microsoft Graph → Delegated permissions:
   - Calendars.ReadWrite
   - User.Read
   - offline_access
10. Klikněte Grant admin consent (pokud máte oprávnění)
```

### 3. Stripe (platby)

```bash
1. Jděte na https://dashboard.stripe.com/
2. Developers → API keys
3. Přepněte z Test mode na Live mode (IMPORTANT!)
4. Zkopírujte:
   - Publishable key → STRIPE_KEY
   - Secret key → STRIPE_SECRET
5. Products → vytvořte produkt "Pro Plan"
6. Zkopírujte Price ID → STRIPE_PRO_PRICE_ID
7. Developers → Webhooks → Add endpoint
   - URL: https://vase-domena.cz/webhooks/stripe
   - Events: customer.subscription.created, customer.subscription.updated, customer.subscription.deleted
8. Zkopírujte Signing secret → STRIPE_WEBHOOK_SECRET
```

### 4. Email služba (SendGrid příklad)

```bash
1. Registrace na https://sendgrid.com/ (100 emailů/den zdarma)
2. Settings → API Keys → Create API Key
3. Full Access → Create & View
4. Zkopírujte API key do .env:
   MAIL_HOST=smtp.sendgrid.net
   MAIL_PORT=587
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=SG.xxxxx

5. Sender Authentication → Domain Authentication → nastavte DNS záznamy
6. Nebo Single Sender Verification pro rychlý start
```

---

## 🔒 Bezpečnost

### Checklist před spuštěním:

```bash
✅ APP_DEBUG=false
✅ APP_ENV=production
✅ SESSION_SECURE_COOKIE=true
✅ Silná hesla pro DB, Redis
✅ SSL certifikát nastaven
✅ Firewall aktivní (pouze porty 80, 443, 22)
✅ .env soubor má oprávnění 600
✅ TOKEN_ENCRYPTION_KEY je unikátní a bezpečný
✅ Všechny API klíče jsou produkční (ne testovací)
```

### Nastavení firewallu (UFW)

```bash
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
sudo ufw status
```

### Ochrana SSH

```bash
# Zakažte root login
sudo nano /etc/ssh/sshd_config
```

Změňte:

```
PermitRootLogin no
PasswordAuthentication no  # Pouze pokud máte SSH klíče!
```

```bash
sudo systemctl restart sshd
```

### Fail2ban (ochrana proti brute force)

```bash
sudo apt install -y fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

## 📊 Monitoring a údržba

### Kontrola stavu aplikace

```bash
# Zkontrolujte health endpoint
curl https://vase-domena.cz/health

# Sledujte logy
tail -f storage/logs/laravel.log
tail -f storage/logs/sync-$(date +%Y-%m-%d).log

# Zkontrolujte queue workers
sudo supervisorctl status syncmyday-worker:*

# Zkontrolujte frontu
php artisan queue:monitor
php artisan queue:failed
```

### Automatické zálohy databáze

```bash
# Vytvořte backup skript
sudo nano /usr/local/bin/syncmyday-backup.sh
```

```bash
#!/bin/bash
BACKUP_DIR="/backups/syncmyday"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Zálohování databáze
mysqldump -u syncmyday_prod -p'VASE_HESLO' syncmyday_prod | gzip > $BACKUP_DIR/db_$DATE.sql.gz

# Zálohování .env a storage
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/syncmyday/.env /var/www/syncmyday/storage

# Smazat zálohy starší než 30 dní
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Záloha dokončena: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/syncmyday-backup.sh

# Přidejte do cronu (každý den ve 2:00)
sudo crontab -e
```

```cron
0 2 * * * /usr/local/bin/syncmyday-backup.sh >> /var/log/syncmyday-backup.log 2>&1
```

### Monitoring s Uptime Robot

1. Registrace na https://uptimerobot.com/ (zdarma)
2. Add New Monitor:
   - Type: HTTP(s)
   - URL: https://vase-domena.cz/health
   - Interval: 5 minut
3. Nastavte email notifikace

---

## 🔧 Řešení problémů

### Queue worker neběží

```bash
# Zkontrolujte status
sudo supervisorctl status syncmyday-worker:*

# Restartujte
sudo supervisorctl restart syncmyday-worker:*

# Zkontrolujte logy
tail -f /var/www/syncmyday/storage/logs/worker.log
```

### Webhooky nefungují

```bash
# 1. Zkontrolujte, že URL je přístupná
curl https://vase-domena.cz/webhooks/google/test

# 2. Zkontrolujte SSL certifikát
curl -I https://vase-domena.cz

# 3. Obnovte webhook subscriptions
php artisan webhooks:renew
```

### Chyba 500 (Internal Server Error)

```bash
# Zkontrolujte logy
tail -50 storage/logs/laravel.log

# Zkontrolujte nginx error log
sudo tail -50 /var/log/nginx/error.log

# Vymažte cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Problémy s šifrováním tokenů

```bash
# Ověřte, že máte TOKEN_ENCRYPTION_KEY v .env
grep TOKEN_ENCRYPTION_KEY .env

# Ověřte, že PHP má sodium extension
php -m | grep sodium

# Test šifrování
php artisan tinker
>>> app('encryptor')->encrypt('test')
```

### Vysoká zátěž serveru

```bash
# Zkontrolujte procesy
htop

# Zkontrolujte MySQL
mysqladmin -u root -p processlist

# Optimalizujte cache
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Potřebujete pomoc?

### Časté dotazy

**Q: Kolik stojí provoz aplikace?**
A: S Hetzner CPX11 (4.15€) + SendGrid Free (100 emailů/den) = ~100 Kč/měsíc

**Q: Je nutné mít doménu?**
A: Ano, pro OAuth redirecty a SSL certifikát. Doména .cz stojí ~200 Kč/rok.

**Q: Můžu použít sdílený hosting?**
A: Ne, potřebujete VPS s SSH přístupem a možností běžících procesů na pozadí.

**Q: Jak aktualizovat aplikaci?**
A:

```bash
cd /var/www/syncmyday
git pull origin master
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan cache:clear
php artisan config:cache
sudo supervisorctl restart syncmyday-worker:*
```

**Q: Co když zapomenu TOKEN_ENCRYPTION_KEY?**
A: Všichni uživatelé musí znovu připojit své kalendáře. NIKDY klíč neměňte!

---

## ✅ Checklist před spuštěním

```
□ Server připraven (VPS s Ubuntu 20.04+)
□ Doména nakoupena a DNS ukazuje na server
□ Docker nainstalován NEBO PHP/MySQL/Redis/Nginx nainstalováno
□ .env soubor nakonfigurován s produkčními hodnotami
□ APP_KEY vygenerován
□ TOKEN_ENCRYPTION_KEY vygenerován a uložen
□ Databáze vytvořena
□ Migrace spuštěny
□ SSL certifikát nastaven (Let's Encrypt)
□ Google OAuth produkční credentials nastaveny
□ Microsoft OAuth produkční credentials nastaveny
□ Stripe LIVE API klíče nastaveny
□ Email služba (SendGrid/Mailgun) nakonfigurována
□ Queue worker běží (Supervisor nebo Docker)
□ Scheduler nastaven (Cron)
□ Firewall aktivní (porty 80, 443, 22)
□ Zálohy nastaveny
□ Monitoring nastaven (UptimeRobot)
□ Testovací registrace a sync rule fungují
□ Webhooky od Google/Microsoft přichází
```

---

## 🎉 Úspěšné nasazení!

Po dokončení všech kroků byste měli mít funkční produkční aplikaci na adrese `https://vase-domena.cz`.

### První kroky po nasazení:

1. **Zaregistrujte se** jako první uživatel
2. **Nastavte se jako admin** v databázi:
   ```bash
   php artisan tinker
   >>> $user = User::first();
   >>> $user->is_admin = true;
   >>> $user->save();
   ```
3. **Připojte testovací kalendáře** a ověřte funkčnost
4. **Sledujte logy** prvních pár hodin pro případné chyby

---

**Hodně štěstí s vaším projektem! 🚀**
