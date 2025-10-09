#!/bin/bash
set -e

# Barvy
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m'

# Konfigurace
PROJECT_PATH="/syncmyday.cz"
DO_BACKUP="${1:-no}"  # Záloha pouze pokud spustíte: bash deploy.sh backup

# Banner
echo -e "${BLUE}"
cat << "EOF"
╔═══════════════════════════════════════════╗
║                                           ║
║         SyncMyDay Deployment              ║
║                                           ║
╚═══════════════════════════════════════════╝
EOF
echo -e "${NC}"

cd $PROJECT_PATH || exit 1

# 1. ZÁLOHA (VOLITELNÁ)
if [ "$DO_BACKUP" = "backup" ]; then
    echo -e "${YELLOW}📦 Vytvářím zálohu...${NC}"
    BACKUP_DIR="$PROJECT_PATH/backups"
    mkdir -p $BACKUP_DIR
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
    
    # Smazat staré zálohy
    find $BACKUP_DIR -name "syncmyday_*.tar.gz" -mtime +7 -delete 2>/dev/null || true
else
    echo -e "${YELLOW}⏭  Záloha přeskočena (pro zálohu: bash deploy.sh backup)${NC}"
fi

# 2. GIT PULL
echo -e "${YELLOW}📥 Stahuji změny z Gitu...${NC}"
if [ -d ".git" ]; then
    git fetch --prune origin 2>/dev/null || true
    git reset --hard origin/main 2>/dev/null || true
    echo -e "${GREEN}✓ Změny staženy${NC}"
else
    echo -e "${RED}⚠  Git není inicializován v ${PROJECT_PATH} (přeskakuji pull)${NC}"
fi

# 3. COMPOSER - PŘESKOČENO (proc_open zakázán na cesky-hosting)
echo -e "${YELLOW}⚠  Composer přeskočen - nahrajte vendor lokálně přes rsync${NC}"
echo -e "${YELLOW}   Příkaz: rsync -avz --delete vendor/ syncmyday_cz@syncmyday.cz:/syncmyday.cz/vendor/${NC}"

# 4. MIGRACE
echo -e "${YELLOW}🗄️  Spouštím databázové migrace...${NC}"
php run-artisan.php migrate --force 2>&1 | tail -5
echo -e "${GREEN}✓ Migrace dokončeny${NC}"

# 5. CACHE CLEAR
echo -e "${YELLOW}⚡ Čistím cache...${NC}"
php run-artisan.php config:clear > /dev/null 2>&1
php run-artisan.php cache:clear > /dev/null 2>&1
php run-artisan.php view:clear > /dev/null 2>&1
php run-artisan.php route:clear > /dev/null 2>&1
echo -e "${GREEN}✓ Cache vyčištěna${NC}"

# 6. CACHE REBUILD
echo -e "${YELLOW}⚡ Rebuilduji cache...${NC}"
php run-artisan.php config:cache > /dev/null 2>&1
php run-artisan.php route:cache > /dev/null 2>&1
php run-artisan.php view:cache > /dev/null 2>&1
echo -e "${GREEN}✓ Cache obnovena${NC}"

# 7. OPRÁVNĚNÍ
echo -e "${YELLOW}🔒 Nastavuji oprávnění...${NC}"
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chmod 600 .env 2>/dev/null || true
echo -e "${GREEN}✓ Oprávnění nastavena${NC}"

# 8. ČIŠTĚNÍ STARÝCH ZÁLOH
echo -e "${YELLOW}🧹 Čistím staré zálohy (>7 dní)...${NC}"
if [ -n "${BACKUP_DIR}" ] && [ -d "${BACKUP_DIR}" ]; then
    find "${BACKUP_DIR}" -name "syncmyday_*.tar.gz" -mtime +7 -delete 2>/dev/null || true
    BACKUP_COUNT=$(ls -1 "${BACKUP_DIR}"/syncmyday_*.tar.gz 2>/dev/null | wc -l)
    echo -e "${GREEN}✓ Zálohy: $BACKUP_COUNT souborů${NC}"
else
    echo -e "${YELLOW}⏭  Žádné zálohy k čištění${NC}"
fi

# SHRNUTÍ
echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                                           ║${NC}"
echo -e "${GREEN}║     ✅  Deployment dokončen úspěšně!      ║${NC}"
echo -e "${GREEN}║                                           ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📊 Statistiky:${NC}"
if [ -d ".git" ]; then
    echo -e "   $(git log -1 --pretty=format:'Commit: %h - %s' 2>/dev/null)"
    echo -e "   $(git log -1 --pretty=format:'Autor: %an (%ar)' 2>/dev/null)"
fi
echo -e "   PHP: $(php -v 2>/dev/null | head -n 1 | cut -d' ' -f1-2)"
echo -e "   Čas: $(date +'%Y-%m-%d %H:%M:%S')"
echo ""
echo -e "${BLUE}🌐 Aplikace: ${GREEN}https://syncmyday.cz${NC}"
echo -e "${BLUE}📝 Logy: ${NC}tail -f $PROJECT_PATH/storage/logs/laravel.log"
echo ""