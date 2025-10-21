#!/bin/bash
# ============================================
# Modern Deployment Script for VPS
# SyncMyDay - Full Server Control
# ============================================

set -e

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configuration - ADJUST THESE!
PROJECT_PATH="/var/www/syncmyday"  # Adjust to your path
PHP_VERSION="8.3"  # Adjust if needed
DO_BACKUP="${1:-no}"  # Default: no, enable with: bash deploy-vps.sh yes

# Banner
echo -e "${BLUE}"
cat << "EOF"
╔═══════════════════════════════════════════╗
║                                           ║
║         SyncMyDay VPS Deployment          ║
║                                           ║
╚═══════════════════════════════════════════╝
EOF
echo -e "${NC}"

# Check if we're in the project directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Error: artisan file not found. Are you in the project directory?${NC}"
    echo -e "${YELLOW}   Expected: $PROJECT_PATH${NC}"
    exit 1
fi

cd "$PROJECT_PATH" || exit 1

# 1. ENABLE MAINTENANCE MODE
echo -e "${YELLOW}🔒 Aktivuji maintenance mode...${NC}"
php artisan down --retry=60 || true
echo -e "${GREEN}✓ Maintenance mode aktivní${NC}"

# 2. BACKUP (OPTIONAL)
if [ "$DO_BACKUP" = "yes" ] || [ "$DO_BACKUP" = "backup" ]; then
    echo -e "${YELLOW}📦 Vytvářím zálohu...${NC}"
    BACKUP_DIR="$PROJECT_PATH/backups"
    mkdir -p "$BACKUP_DIR"
    DATE=$(date +%Y%m%d_%H%M%S)
    
    tar -czf "$BACKUP_DIR/syncmyday_$DATE.tar.gz" \
        --exclude='vendor' \
        --exclude='node_modules' \
        --exclude='storage/logs/*' \
        --exclude='storage/framework/cache/*' \
        --exclude='storage/framework/sessions/*' \
        --exclude='storage/framework/views/*' \
        --exclude='backups' \
        . 2>/dev/null || true
    
    echo -e "${GREEN}✓ Záloha: syncmyday_$DATE.tar.gz${NC}"
    
    # Clean old backups (>7 days)
    find "$BACKUP_DIR" -name "syncmyday_*.tar.gz" -mtime +7 -delete 2>/dev/null || true
else
    echo -e "${YELLOW}⏭  Záloha přeskočena${NC}"
fi

# 3. GIT PULL
echo -e "${YELLOW}📥 Stahuji změny z Gitu...${NC}"
if [ -d ".git" ]; then
    git fetch --prune origin
    git reset --hard origin/main
    echo -e "${GREEN}✓ Změny staženy ($(git log -1 --pretty=format:'%h - %s'))${NC}"
else
    echo -e "${RED}⚠  Git není inicializován${NC}"
    exit 1
fi

# 4. COMPOSER INSTALL
echo -e "${YELLOW}📦 Instaluji závislosti (Composer)...${NC}"
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction
echo -e "${GREEN}✓ Závislosti nainstalovány${NC}"

# 5. DATABASE MIGRATIONS
echo -e "${YELLOW}🗄️  Spouštím databázové migrace...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Migrace dokončeny${NC}"

# 6. CLEAR OLD CACHE
echo -e "${YELLOW}⚡ Čistím cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
echo -e "${GREEN}✓ Cache vyčištěna${NC}"

# 7. REBUILD CACHE
echo -e "${YELLOW}⚡ Rebuilduji cache...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓ Cache obnovena${NC}"

# 8. PERMISSIONS
echo -e "${YELLOW}🔒 Nastavuji oprávnění...${NC}"
chmod -R 775 storage bootstrap/cache
chmod 600 .env 2>/dev/null || true
echo -e "${GREEN}✓ Oprávnění nastavena${NC}"

# 9. RESTART QUEUE WORKERS (Supervisor)
echo -e "${YELLOW}♻️  Restartuji queue workers...${NC}"
if command -v supervisorctl &> /dev/null; then
    sudo supervisorctl restart syncmyday-worker:* || echo "⚠️  Supervisor restart failed (může být OK)"
    echo -e "${GREEN}✓ Queue workers restartovány${NC}"
else
    echo -e "${YELLOW}⚠  Supervisor nenalezen - přeskakuji restart workers${NC}"
fi

# 10. RESTART PHP-FPM (optional but recommended)
echo -e "${YELLOW}♻️  Restartuji PHP-FPM...${NC}"
if command -v systemctl &> /dev/null; then
    sudo systemctl reload php${PHP_VERSION}-fpm || echo "⚠️  PHP-FPM reload failed"
    echo -e "${GREEN}✓ PHP-FPM restartován${NC}"
else
    echo -e "${YELLOW}⚠  systemctl nenalezen - přeskakuji PHP-FPM restart${NC}"
fi

# 11. CLEAR REDIS CACHE (optional)
echo -e "${YELLOW}🔄 Čistím Redis cache...${NC}"
php artisan cache:clear --tags=app
echo -e "${GREEN}✓ Redis cache vyčištěn${NC}"

# 12. DISABLE MAINTENANCE MODE
echo -e "${YELLOW}🔓 Deaktivuji maintenance mode...${NC}"
php artisan up
echo -e "${GREEN}✓ Aplikace je opět online!${NC}"

# 13. CLEANUP OLD BACKUPS
if { [ "$DO_BACKUP" = "yes" ] || [ "$DO_BACKUP" = "backup" ]; } && [ -d "$BACKUP_DIR" ]; then
    echo -e "${YELLOW}🧹 Čistím staré zálohy (>7 dní)...${NC}"
    find "$BACKUP_DIR" -name "syncmyday_*.tar.gz" -mtime +7 -delete 2>/dev/null || true
    BACKUP_COUNT=$(ls -1 "$BACKUP_DIR"/syncmyday_*.tar.gz 2>/dev/null | wc -l || echo 0)
    echo -e "${GREEN}✓ Zálohy: $BACKUP_COUNT souborů${NC}"
fi

# SUMMARY
echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                           ║${NC}"
echo -e "${GREEN}║     ✅  Deployment dokončen úspěšně!      ║${NC}"
echo -e "${GREEN}║                                           ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📊 Statistiky:${NC}"
echo -e "   Commit: $(git log -1 --pretty=format:'%h - %s')"
echo -e "   Autor: $(git log -1 --pretty=format:'%an (%ar)')"
echo -e "   PHP: $(php -v | head -n 1 | cut -d' ' -f1-2)"
echo -e "   Čas: $(date +'%Y-%m-%d %H:%M:%S')"
echo ""
echo -e "${BLUE}🚀 Next Steps:${NC}"
echo -e "   • Zkontroluj logy: ${GREEN}tail -f $PROJECT_PATH/storage/logs/laravel.log${NC}"
echo -e "   • Zkontroluj queue: ${GREEN}php artisan queue:monitor${NC}"
echo -e "   • Zkontroluj Redis: ${GREEN}redis-cli ping${NC}"
echo ""

