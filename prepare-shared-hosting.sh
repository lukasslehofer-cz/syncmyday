#!/bin/bash

###############################################################################
# Příprava projektu pro upload na sdílený hosting
# Tento skript vytvoří archiv připravený pro nahrání
###############################################################################

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}"
cat << "EOF"
╔═══════════════════════════════════════════════════════╗
║                                                       ║
║        Příprava pro sdílený hosting                  ║
║        SyncMyDay Production Package                   ║
║                                                       ║
╚═══════════════════════════════════════════════════════╝
EOF
echo -e "${NC}"

# Kontrola, že jsme v správné složce
if [ ! -f "artisan" ]; then
    echo -e "${RED}✗ Chyba: Nejste v kořenové složce Laravel projektu${NC}"
    exit 1
fi

echo -e "${GREEN}=== Krok 1/6: Kontrola požadavků ===${NC}\n"

# PHP check
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗ PHP není nainstalováno${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo -e "✓ PHP verze: ${GREEN}$PHP_VERSION${NC}"

# Composer check
if ! command -v composer &> /dev/null; then
    echo -e "${RED}✗ Composer není nainstalován${NC}"
    exit 1
fi
echo -e "✓ Composer nainstalován"

echo -e "\n${GREEN}=== Krok 2/6: Instalace produkčních závislostí ===${NC}\n"

read -p "Chcete (re)instalovat závislosti? (y/N): " install_deps
if [ "$install_deps" == "y" ] || [ "$install_deps" == "Y" ]; then
    echo "Instaluji závislosti..."
    composer install --no-dev --optimize-autoloader --no-interaction
    echo -e "${GREEN}✓ Závislosti nainstalovány${NC}"
else
    echo "Přeskakuji instalaci závislostí"
fi

echo -e "\n${GREEN}=== Krok 3/6: Vytvoření .env.shared-hosting ===${NC}\n"

if [ -f ".env.shared-hosting" ]; then
    read -p ".env.shared-hosting již existuje. Přepsat? (y/N): " overwrite
    if [ "$overwrite" != "y" ] && [ "$overwrite" != "Y" ]; then
        echo "Používám existující .env.shared-hosting"
    else
        rm .env.shared-hosting
    fi
fi

if [ ! -f ".env.shared-hosting" ]; then
    read -p "Zadejte vaši doménu (např. mojedomena.cz): " domain
    
    # Generování klíčů
    APP_KEY=$(php artisan key:generate --show 2>/dev/null || echo "base64:$(openssl rand -base64 32)")
    TOKEN_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
    
    cat > .env.shared-hosting << EOF
# ==============================================
# KONFIGURACE PRO SDÍLENÝ HOSTING
# Vygenerováno: $(date)
# ==============================================

APP_NAME=SyncMyDay
APP_ENV=production
APP_DEBUG=false
APP_KEY=$APP_KEY
APP_URL=https://$domain

# ==============================================
# DATABÁZE (vyplňte údaje od hostingu)
# ==============================================

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=DOPLNTE_NAZEV_DATABAZE
DB_USERNAME=DOPLNTE_UZIVATELE
DB_PASSWORD=DOPLNTE_HESLO

# ==============================================
# CACHE A FRONTY - PRO SDÍLENÝ HOSTING!
# ==============================================

CACHE_DRIVER=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Redis VYPNUTO (není k dispozici na sdíleném hostingu)
REDIS_CLIENT=null

# ==============================================
# ŠIFROVÁNÍ TOKENŮ
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
# STRIPE PLATBY (LIVE klíče!)
# ==============================================

STRIPE_KEY=pk_live_
STRIPE_SECRET=sk_live_
STRIPE_WEBHOOK_SECRET=whsec_
STRIPE_PRO_PRICE_ID=

# ==============================================
# EMAIL (SMTP od hostingu nebo SendGrid)
# ==============================================

MAIL_MAILER=smtp
MAIL_HOST=smtp.$domain
MAIL_PORT=587
MAIL_USERNAME=noreply@$domain
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@$domain
MAIL_FROM_NAME=SyncMyDay

# ==============================================
# WEBHOOKY
# ==============================================

WEBHOOK_BASE_URL=https://$domain/webhooks

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
LOG_DEPRECATIONS_CHANNEL=null

# ==============================================
# OSTATNÍ
# ==============================================

BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
QUEUE_FAILED_DRIVER=database-uuids

DEFAULT_LOCALE=cs
FALLBACK_LOCALE=en
EOF

    echo -e "${GREEN}✓ .env.shared-hosting vytvořen${NC}"
    echo -e "${YELLOW}⚠  Nezapomeňte doplnit API klíče před nahráním!${NC}"
fi

echo -e "\n${GREEN}=== Krok 4/6: Vytvoření install.php skriptu ===${NC}\n"

cat > install.php << 'EOF'
<?php
/**
 * Jednorázový instalační skript pro sdílený hosting
 * Použití: Otevřete https://vase-domena.cz/install.php v prohlížeči
 * 
 * ⚠️  DŮLEŽITÉ: Po dokončení SMAŽTE tento soubor ze serveru!
 */

// Základní bezpečnost - změňte heslo!
define('INSTALL_PASSWORD', 'change-me-before-upload');

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SyncMyDay - Instalace</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🚀 SyncMyDay - Instalace</h1>
    
    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'] ?? '';
        
        if ($password !== INSTALL_PASSWORD) {
            echo '<p class="error">❌ Nesprávné heslo!</p>';
            exit;
        }
        
        echo '<h2>Spouštím instalaci...</h2>';
        
        try {
            require __DIR__.'/vendor/autoload.php';
            $app = require_once __DIR__.'/bootstrap/app.php';
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
            
            // Test připojení k databázi
            echo '<p>🔌 Testuji připojení k databázi...</p>';
            $pdo = DB::connection()->getPdo();
            echo '<p class="success">✅ Připojení k databázi úspěšné!</p>';
            
            // Spustit migrace
            echo '<p>📊 Spouštím databázové migrace...</p>';
            ob_start();
            $status = $kernel->call('migrate', ['--force' => true]);
            $output = ob_get_clean();
            
            echo '<pre>' . htmlspecialchars($output) . '</pre>';
            
            if ($status === 0) {
                echo '<p class="success">✅ Migrace dokončeny úspěšně!</p>';
            } else {
                echo '<p class="error">❌ Migrace selhaly se statusem: ' . $status . '</p>';
            }
            
            // Cache
            echo '<p>⚡ Optimalizuji cache...</p>';
            $kernel->call('config:cache');
            $kernel->call('route:cache');
            $kernel->call('view:cache');
            echo '<p class="success">✅ Cache optimalizována!</p>';
            
            echo '<hr>';
            echo '<h2 class="success">🎉 Instalace dokončena!</h2>';
            echo '<p><strong class="warning">⚠️  DŮLEŽITÉ: NYNÍ SMAŽTE soubor install.php ze serveru!</strong></p>';
            echo '<p>Můžete přejít na: <a href="/">Úvodní stránku</a></p>';
            
        } catch (Exception $e) {
            echo '<p class="error">❌ Chyba: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        }
    } else {
        ?>
        <p>Tento skript nainstaluje databázi a připraví aplikaci.</p>
        <p class="warning">⚠️ Před spuštěním ověřte, že:</p>
        <ul>
            <li>Všechny soubory jsou nahrány na server</li>
            <li>.env soubor je správně nakonfigurován</li>
            <li>Databáze je vytvořena</li>
            <li>Složky storage/ a bootstrap/cache/ mají oprávnění 775</li>
        </ul>
        
        <form method="POST">
            <p>
                <label>Instalační heslo: <input type="password" name="password" required></label>
            </p>
            <p class="warning">Výchozí heslo: <code>change-me-before-upload</code> (změňte před nahráním!)</p>
            <p><button type="submit">Spustit instalaci</button></p>
        </form>
        <?php
    }
    ?>
</body>
</html>
EOF

echo -e "${GREEN}✓ install.php vytvořen${NC}"
echo -e "${YELLOW}⚠  Před nahráním změňte heslo v install.php!${NC}"

echo -e "\n${GREEN}=== Krok 5/6: Vytváření archivu ===${NC}\n"

ARCHIVE_NAME="syncmyday-shared-hosting-$(date +%Y%m%d).tar.gz"

echo "Vytvářím archiv: $ARCHIVE_NAME"

# Vytvoření archivu s vyloučením nepotřebných souborů
tar -czf "$ARCHIVE_NAME" \
  --exclude='.git' \
  --exclude='.gitignore' \
  --exclude='node_modules' \
  --exclude='tests' \
  --exclude='.env' \
  --exclude='.env.example' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='*.tar.gz' \
  --exclude='*.zip' \
  --exclude='.DS_Store' \
  --exclude='deploy*.sh' \
  --exclude='docker-compose.yml' \
  --exclude='docker' \
  --exclude='phpunit.xml' \
  --exclude='README.md' \
  --exclude='DEPLOYMENT.md' \
  .

if [ -f "$ARCHIVE_NAME" ]; then
    ARCHIVE_SIZE=$(du -h "$ARCHIVE_NAME" | cut -f1)
    echo -e "${GREEN}✓ Archiv vytvořen: $ARCHIVE_NAME (velikost: $ARCHIVE_SIZE)${NC}"
else
    echo -e "${RED}✗ Nepodařilo se vytvořit archiv${NC}"
    exit 1
fi

echo -e "\n${GREEN}=== Krok 6/6: Shrnutí ===${NC}\n"

echo -e "📦 ${GREEN}Příprava dokončena!${NC}\n"

echo -e "${YELLOW}Vytvořené soubory:${NC}"
echo "  • $ARCHIVE_NAME - archiv projektu pro upload"
echo "  • .env.shared-hosting - konfigurace pro produkci"
echo "  • install.php - instalační skript (už je v archivu)"
echo ""

echo -e "${YELLOW}Vaše šifrovací klíče:${NC}"
grep "APP_KEY=" .env.shared-hosting
grep "TOKEN_ENCRYPTION_KEY=" .env.shared-hosting
echo ""
echo -e "${RED}⚠️  Uložte si tyto klíče na bezpečné místo! Po nasazení je NIKDY neměňte!${NC}"
echo ""

echo -e "${YELLOW}Další kroky:${NC}"
echo "1. Otevřete .env.shared-hosting a doplňte:"
echo "   - Údaje k databázi od vašeho hostingu"
echo "   - Google OAuth klíče"
echo "   - Microsoft OAuth klíče"
echo "   - Stripe LIVE klíče"
echo "   - Email SMTP údaje"
echo ""
echo "2. V souboru install.php změňte heslo INSTALL_PASSWORD"
echo ""
echo "3. Přihlaste se na váš hosting (cPanel/FTP)"
echo ""
echo "4. Nahrajte archiv $ARCHIVE_NAME"
echo ""
echo "5. Rozbalte archiv na serveru"
echo ""
echo "6. Přejmenujte .env.shared-hosting na .env"
echo ""
echo "7. Nastavte document root na složku /public"
echo ""
echo "8. Spusťte https://vase-domena.cz/install.php"
echo ""
echo "9. Po úspěšné instalaci SMAŽTE install.php"
echo ""
echo "10. Nastavte cron joby (viz NASAZENI_SDILENY_HOSTING.md)"
echo ""

echo -e "${BLUE}📖 Detailní instrukce najdete v: NASAZENI_SDILENY_HOSTING.md${NC}"
echo ""
echo -e "${GREEN}Hodně štěstí s nasazením! 🚀${NC}"


