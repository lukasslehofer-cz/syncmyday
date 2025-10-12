#!/bin/bash
set -e

# Barvy
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m'

# Konfigurace
PROJECT_PATH="/syncmyday.cz"
IMPORT_FILE="blog-export.json"

echo -e "${BLUE}╔═══════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     📥 Blog Import (Production)          ║${NC}"
echo -e "${BLUE}╚═══════════════════════════════════════════╝${NC}"
echo ""

cd $PROJECT_PATH || exit 1

# Kontrola, zda existuje soubor
if [ ! -f "$IMPORT_FILE" ]; then
    echo -e "${RED}❌ Soubor nenalezen: $IMPORT_FILE${NC}"
    echo -e "${YELLOW}   Nejdřív spusťte deploy.sh a ujistěte se, že blog-export.json je v Gitu${NC}"
    exit 1
fi

# Import článků z JSON
echo -e "${YELLOW}📥 Importuji články z JSON...${NC}"
php run-artisan.php blog:import --file=$IMPORT_FILE 2>&1 | tail -10
echo -e "${GREEN}✓ Import dokončen${NC}"

# Vyčištění cache
echo -e "${YELLOW}⚡ Čistím cache...${NC}"
php run-artisan.php config:clear > /dev/null 2>&1
php run-artisan.php cache:clear > /dev/null 2>&1
php run-artisan.php view:clear > /dev/null 2>&1
echo -e "${GREEN}✓ Cache vyčištěna${NC}"

echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     ✅ Import dokončen!                    ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}🌐 Blog: ${GREEN}https://syncmyday.cz/blog${NC}"
echo -e "${BLUE}⚙️  Admin: ${GREEN}https://syncmyday.cz/admin/blog${NC}"
echo ""

