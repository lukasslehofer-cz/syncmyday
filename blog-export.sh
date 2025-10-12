#!/bin/bash
set -e

# Barvy
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}╔═══════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║     📤 Blog Export (Local)                ║${NC}"
echo -e "${BLUE}╚═══════════════════════════════════════════╝${NC}"
echo ""

# Export článků do JSON
echo -e "${YELLOW}📤 Exportuji články do JSON...${NC}"
php artisan blog:export --file=storage/blog-export.json
echo -e "${GREEN}✓ Export dokončen: storage/blog-export.json${NC}"

# Zkopírovat do root (pro snadné nahrání na Git)
echo -e "${YELLOW}📋 Kopíruji do root...${NC}"
cp storage/blog-export.json blog-export.json
echo -e "${GREEN}✓ Soubor: blog-export.json${NC}"

echo ""
echo -e "${GREEN}╔═══════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║     ✅ Export dokončen!                    ║${NC}"
echo -e "${GREEN}╚═══════════════════════════════════════════╝${NC}"
echo ""
echo -e "${BLUE}📋 Další kroky:${NC}"
echo -e "   1. Zkontroluj: ${YELLOW}blog-export.json${NC}"
echo -e "   2. Commitni do Gitu: ${YELLOW}git add blog-export.json${NC}"
echo -e "   3. Push: ${YELLOW}git push${NC}"
echo -e "   4. Nahraj obrázky FTP/SFTP do: ${YELLOW}public/images/blog/${NC}"
echo -e "   5. Na serveru: ${YELLOW}bash deploy.sh && bash blog-import.sh${NC}"
echo ""

