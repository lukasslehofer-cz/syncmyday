#!/bin/bash

###############################################################################
# SyncMyDay - Pomocný skript pro nasazení na produkci
# Tento skript vám pomůže s přípravou .env souboru a deployment checklist
###############################################################################

set -e

# Barvy pro output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Banner
echo -e "${BLUE}"
cat << "EOF"
╔═══════════════════════════════════════════════════════╗
║                                                       ║
║           SyncMyDay Deployment Helper                ║
║         Pomocník pro nasazení na produkci            ║
║                                                       ║
╚═══════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

# Menu
echo -e "${YELLOW}Co chcete udělat?${NC}"
echo "1) Vygenerovat šifrovací klíče (APP_KEY, TOKEN_ENCRYPTION_KEY)"
echo "2) Vytvořit šablonu .env souboru pro produkci"
echo "3) Otestovat připojení k databázi"
echo "4) Zkontrolovat PHP požadavky"
echo "5) Zkontrolovat deployment checklist"
echo "6) Všechno najednou (doporučeno pro první nasazení)"
echo "0) Konec"
echo ""
read -p "Vyberte možnost (0-6): " choice

case $choice in
    1)
        echo -e "\n${GREEN}=== Generování šifrovacích klíčů ===${NC}\n"
        
        echo -e "${YELLOW}APP_KEY:${NC}"
        if command -v php &> /dev/null; then
            APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
            echo "$APP_KEY"
            echo ""
            echo -e "${GREEN}✓ APP_KEY vygenerován${NC}"
        else
            echo -e "${RED}✗ PHP není nainstalováno${NC}"
        fi
        
        echo -e "\n${YELLOW}TOKEN_ENCRYPTION_KEY:${NC}"
        if command -v php &> /dev/null; then
            TOKEN_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
            echo "TOKEN_ENCRYPTION_KEY=$TOKEN_KEY"
            echo ""
            echo -e "${GREEN}✓ TOKEN_ENCRYPTION_KEY vygenerován${NC}"
        else
            echo -e "${RED}✗ PHP není nainstalováno${NC}"
        fi
        
        echo -e "\n${RED}⚠️  DŮLEŽITÉ: Tyto klíče si bezpečně uložte!${NC}"
        echo "Po nasazení je NIKDY neměňte, jinak přijdete o přístup k šifrovaným datům!"
        ;;
        
    2)
        echo -e "\n${GREEN}=== Vytvoření .env souboru pro produkci ===${NC}\n"
        
        if [ -f ".env.production" ]; then
            read -p ".env.production již existuje. Přepsat? (y/N): " overwrite
            if [ "$overwrite" != "y" ] && [ "$overwrite" != "Y" ]; then
                echo "Operace zrušena."
                exit 0
            fi
        fi
        
        read -p "Zadejte vaši doménu (např. syncmyday.cz): " domain
        read -p "Název databáze [syncmyday_prod]: " db_name
        db_name=${db_name:-syncmyday_prod}
        read -p "Uživatel databáze [syncmyday_prod]: " db_user
        db_user=${db_user:-syncmyday_prod}
        read -sp "Heslo databáze: " db_pass
        echo ""
        
        # Generování klíčů
        if command -v php &> /dev/null; then
            APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
            TOKEN_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
        else
            APP_KEY="base64:$(openssl rand -base64 32)"
            TOKEN_KEY="base64:$(openssl rand -base64 32)"
        fi
        
        # Vytvoření souboru
        cat > .env.production << EOF
# ==============================================
# PRODUKČNÍ KONFIGURACE - $domain
# Vygenerováno: $(date)
# ==============================================

APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_KEY=$APP_KEY
APP_URL=https://$domain

# ==============================================
# DATABÁZE
# ==============================================

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=$db_name
DB_USERNAME=$db_user
DB_PASSWORD=$db_pass

# ==============================================
# REDIS
# ==============================================

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=
REDIS_PORT=6379
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_PREFIX=syncmyday_

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# ==============================================
# ŠIFROVÁNÍ TOKENŮ (KRITICKÉ!)
# ==============================================

TOKEN_ENCRYPTION_KEY=$TOKEN_KEY

# ==============================================
# GOOGLE CALENDAR API
# ==============================================

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=https://$domain/oauth/google/callback

# ==============================================
# MICROSOFT GRAPH API
# ==============================================

MICROSOFT_CLIENT_ID=
MICROSOFT_CLIENT_SECRET=
MICROSOFT_REDIRECT_URI=https://$domain/oauth/microsoft/callback
MICROSOFT_TENANT=common

# ==============================================
# STRIPE PLATBY
# ==============================================

STRIPE_KEY=pk_live_
STRIPE_SECRET=sk_live_
STRIPE_WEBHOOK_SECRET=whsec_
STRIPE_PRO_PRICE_ID=

# ==============================================
# EMAIL
# ==============================================

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@$domain
MAIL_FROM_NAME=SyncMyDay

# ==============================================
# WEBHOOKY
# ==============================================

WEBHOOK_BASE_URL=https://$domain/webhooks

# ==============================================
# LOKALIZACE
# ==============================================

DEFAULT_LOCALE=cs
FALLBACK_LOCALE=en
FAKER_LOCALE=cs_CZ

# ==============================================
# BEZPEČNOST
# ==============================================

SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120
SESSION_SAME_SITE=lax

# ==============================================
# LOGOVÁNÍ
# ==============================================

LOG_CHANNEL=daily
LOG_LEVEL=error

# ==============================================
# OSTATNÍ
# ==============================================

BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
QUEUE_FAILED_DRIVER=database-uuids
EOF

        chmod 600 .env.production
        
        echo -e "\n${GREEN}✓ .env.production vytvořen!${NC}"
        echo -e "\n${YELLOW}Další kroky:${NC}"
        echo "1. Zkontrolujte soubor: nano .env.production"
        echo "2. Doplňte API klíče (Google, Microsoft, Stripe, Email)"
        echo "3. Nastavte Redis heslo (REDIS_PASSWORD)"
        echo "4. Zkopírujte na server: scp .env.production user@server:/var/www/syncmyday/.env"
        ;;
        
    3)
        echo -e "\n${GREEN}=== Test připojení k databázi ===${NC}\n"
        
        if [ ! -f ".env" ]; then
            echo -e "${RED}✗ .env soubor neexistuje${NC}"
            exit 1
        fi
        
        # Načtení .env
        export $(cat .env | grep -v '^#' | xargs)
        
        echo "Testuji připojení k databázi..."
        echo "Host: $DB_HOST"
        echo "Database: $DB_DATABASE"
        echo "User: $DB_USERNAME"
        echo ""
        
        if command -v mysql &> /dev/null; then
            if mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" -e "USE $DB_DATABASE;" 2>/dev/null; then
                echo -e "${GREEN}✓ Připojení k databázi úspěšné!${NC}"
            else
                echo -e "${RED}✗ Nelze se připojit k databázi${NC}"
                echo "Zkontrolujte přihlašovací údaje v .env"
            fi
        else
            echo -e "${YELLOW}⚠ MySQL klient není nainstalován, nemohu otestovat připojení${NC}"
        fi
        ;;
        
    4)
        echo -e "\n${GREEN}=== Kontrola PHP požadavků ===${NC}\n"
        
        if ! command -v php &> /dev/null; then
            echo -e "${RED}✗ PHP není nainstalováno${NC}"
            exit 1
        fi
        
        PHP_VERSION=$(php -r "echo PHP_VERSION;")
        echo -e "PHP verze: ${GREEN}$PHP_VERSION${NC}"
        
        if php -r "exit(version_compare(PHP_VERSION, '8.2.0', '>=') ? 0 : 1);"; then
            echo -e "${GREEN}✓ PHP verze je 8.2+${NC}"
        else
            echo -e "${RED}✗ Vyžadována PHP 8.2 nebo novější${NC}"
        fi
        
        echo -e "\n${YELLOW}Kontrola rozšíření:${NC}"
        
        extensions=("bcmath" "ctype" "fileinfo" "json" "mbstring" "openssl" "pdo" "pdo_mysql" "tokenizer" "xml" "curl" "gd" "sodium" "redis")
        
        for ext in "${extensions[@]}"; do
            if php -m | grep -qi "^$ext$"; then
                echo -e "${GREEN}✓${NC} $ext"
            else
                echo -e "${RED}✗${NC} $ext ${RED}(chybí)${NC}"
            fi
        done
        
        echo ""
        if command -v composer &> /dev/null; then
            COMPOSER_VERSION=$(composer --version | cut -d' ' -f3)
            echo -e "${GREEN}✓${NC} Composer: $COMPOSER_VERSION"
        else
            echo -e "${RED}✗${NC} Composer není nainstalován"
        fi
        ;;
        
    5)
        echo -e "\n${GREEN}=== Deployment Checklist ===${NC}\n"
        
        echo "Připravenost serveru:"
        echo "□ VPS s Ubuntu 20.04+ nebo podobným"
        echo "□ Doména nakoupena a DNS nastaveno"
        echo "□ Docker nainstalován (nebo PHP/MySQL/Redis/Nginx)"
        echo ""
        echo "Konfigurace:"
        echo "□ .env soubor vytvořen s produkčními hodnotami"
        echo "□ APP_KEY vygenerován"
        echo "□ TOKEN_ENCRYPTION_KEY vygenerován"
        echo "□ Databáze vytvořena"
        echo "□ SSL certifikát nastaven (Let's Encrypt)"
        echo ""
        echo "OAuth & API:"
        echo "□ Google OAuth credentials (produkční)"
        echo "□ Microsoft OAuth credentials (produkční)"
        echo "□ Stripe LIVE API klíče"
        echo "□ Email služba nakonfigurována (SendGrid/Mailgun)"
        echo ""
        echo "Zabezpečení:"
        echo "□ APP_ENV=production"
        echo "□ APP_DEBUG=false"
        echo "□ SESSION_SECURE_COOKIE=true"
        echo "□ Silná hesla pro DB a Redis"
        echo "□ Firewall aktivní (porty 22, 80, 443)"
        echo "□ .env má oprávnění 600"
        echo ""
        echo "Služby:"
        echo "□ Queue worker běží (Supervisor/Docker)"
        echo "□ Scheduler nastaven (Cron)"
        echo "□ Migrace spuštěny"
        echo "□ Cache optimalizace spuštěna"
        echo ""
        echo "Monitoring:"
        echo "□ Zálohy nastaveny"
        echo "□ Uptime monitoring (UptimeRobot)"
        echo "□ Testovací sync rule funguje"
        echo ""
        
        if [ -f ".env" ]; then
            echo -e "\n${YELLOW}Kontrola aktuálního .env:${NC}"
            
            source .env 2>/dev/null || true
            
            [ "$APP_ENV" == "production" ] && echo -e "${GREEN}✓${NC} APP_ENV=production" || echo -e "${RED}✗${NC} APP_ENV není production"
            [ "$APP_DEBUG" == "false" ] && echo -e "${GREEN}✓${NC} APP_DEBUG=false" || echo -e "${RED}✗${NC} APP_DEBUG není false"
            [ ! -z "$TOKEN_ENCRYPTION_KEY" ] && echo -e "${GREEN}✓${NC} TOKEN_ENCRYPTION_KEY nastaven" || echo -e "${RED}✗${NC} TOKEN_ENCRYPTION_KEY chybí"
            [ ! -z "$GOOGLE_CLIENT_ID" ] && echo -e "${GREEN}✓${NC} Google OAuth nastaven" || echo -e "${YELLOW}⚠${NC} Google OAuth nenastaven"
            [ ! -z "$MICROSOFT_CLIENT_ID" ] && echo -e "${GREEN}✓${NC} Microsoft OAuth nastaven" || echo -e "${YELLOW}⚠${NC} Microsoft OAuth nenastaven"
            [[ "$STRIPE_KEY" == pk_live_* ]] && echo -e "${GREEN}✓${NC} Stripe LIVE key" || echo -e "${YELLOW}⚠${NC} Stripe není v LIVE režimu"
        fi
        ;;
        
    6)
        echo -e "\n${GREEN}=== Kompletní příprava pro deployment ===${NC}\n"
        
        # PHP check
        if ! command -v php &> /dev/null; then
            echo -e "${RED}✗ PHP není nainstalováno. Nainstalujte PHP 8.2+ a zkuste znovu.${NC}"
            exit 1
        fi
        
        # Generování klíčů
        echo -e "${BLUE}[1/5]${NC} Generování šifrovacích klíčů..."
        APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
        TOKEN_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
        echo -e "${GREEN}✓ Klíče vygenerovány${NC}"
        
        # Vytvoření .env
        echo -e "\n${BLUE}[2/5]${NC} Vytvoření .env.production..."
        read -p "Zadejte vaši doménu (např. syncmyday.cz): " domain
        
        cat > .env.production << EOF
APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_KEY=$APP_KEY
APP_URL=https://$domain

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=syncmyday_prod
DB_USERNAME=syncmyday_prod
DB_PASSWORD=NASTAVTE_SILNE_HESLO

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=NASTAVTE_SILNE_HESLO
REDIS_PORT=6379

TOKEN_ENCRYPTION_KEY=$TOKEN_KEY

GOOGLE_CLIENT_ID=DOPLNTE
GOOGLE_CLIENT_SECRET=DOPLNTE
GOOGLE_REDIRECT_URI=https://$domain/oauth/google/callback

MICROSOFT_CLIENT_ID=DOPLNTE
MICROSOFT_CLIENT_SECRET=DOPLNTE
MICROSOFT_REDIRECT_URI=https://$domain/oauth/microsoft/callback
MICROSOFT_TENANT=common

STRIPE_KEY=pk_live_DOPLNTE
STRIPE_SECRET=sk_live_DOPLNTE
STRIPE_WEBHOOK_SECRET=whsec_DOPLNTE
STRIPE_PRO_PRICE_ID=DOPLNTE

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=DOPLNTE
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@$domain
MAIL_FROM_NAME=SyncMyDay

WEBHOOK_BASE_URL=https://$domain/webhooks

SESSION_SECURE_COOKIE=true
LOG_LEVEL=error
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
EOF

        chmod 600 .env.production
        echo -e "${GREEN}✓ .env.production vytvořen${NC}"
        
        # PHP requirements check
        echo -e "\n${BLUE}[3/5]${NC} Kontrola PHP požadavků..."
        missing_extensions=()
        for ext in bcmath ctype fileinfo json mbstring openssl pdo pdo_mysql tokenizer xml curl gd sodium redis; do
            if ! php -m | grep -qi "^$ext$"; then
                missing_extensions+=("$ext")
            fi
        done
        
        if [ ${#missing_extensions[@]} -eq 0 ]; then
            echo -e "${GREEN}✓ Všechna PHP rozšíření jsou nainstalována${NC}"
        else
            echo -e "${YELLOW}⚠ Chybí rozšíření: ${missing_extensions[*]}${NC}"
        fi
        
        # Vytvoření deployment skriptu
        echo -e "\n${BLUE}[4/5]${NC} Vytvoření deployment skriptu..."
        cat > deploy.sh << 'DEPLOY_EOF'
#!/bin/bash
set -e

echo "🚀 Spouštím deployment..."

# Aktualizace kódu
echo "📥 Stahování nejnovějšího kódu..."
git pull origin master

# Závislosti
echo "📦 Instalace závislostí..."
composer install --no-dev --optimize-autoloader

# Migrace
echo "🗄️  Spouštění migrací..."
php artisan migrate --force

# Cache
echo "⚡ Optimalizace..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Restart services
echo "🔄 Restart služeb..."
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart syncmyday-worker:*
fi

echo "✅ Deployment dokončen!"
DEPLOY_EOF

        chmod +x deploy.sh
        echo -e "${GREEN}✓ deploy.sh vytvořen${NC}"
        
        # Shrnutí
        echo -e "\n${BLUE}[5/5]${NC} Shrnutí a další kroky..."
        echo ""
        echo -e "${GREEN}✅ Příprava dokončena!${NC}"
        echo ""
        echo -e "${YELLOW}Vaše šifrovací klíče:${NC}"
        echo "APP_KEY=$APP_KEY"
        echo "TOKEN_ENCRYPTION_KEY=$TOKEN_KEY"
        echo ""
        echo -e "${RED}⚠️  DŮLEŽITÉ: Uložte si tyto klíče na bezpečné místo!${NC}"
        echo ""
        echo -e "${YELLOW}Vytvořené soubory:${NC}"
        echo "• .env.production - Produkční konfigurace"
        echo "• deploy.sh - Deployment skript"
        echo ""
        echo -e "${YELLOW}Další kroky:${NC}"
        echo "1. Upravte .env.production a doplňte všechny API klíče"
        echo "2. Přečtěte si NASAZENI_PRODUKCE.md pro detailní instrukce"
        echo "3. Připravte server (VPS) a nainstalujte závislosti"
        echo "4. Zkopírujte soubory na server"
        echo "5. Spusťte aplikaci podle průvodce"
        echo ""
        echo -e "${GREEN}Hodně štěstí! 🍀${NC}"
        ;;
        
    0)
        echo "Nashledanou!"
        exit 0
        ;;
        
    *)
        echo -e "${RED}Neplatná volba${NC}"
        exit 1
        ;;
esac

echo ""


