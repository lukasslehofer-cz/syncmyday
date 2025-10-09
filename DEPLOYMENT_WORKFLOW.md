# 🔄 Deployment Workflow - Přesun změn z lokálního vývoje na server

Průvodce pro bezpečný a efektivní deployment změn na produkční server.

---

## 📋 Přehled možností

| Metoda        | Obtížnost | Rychlost | Doporučeno              |
| ------------- | --------- | -------- | ----------------------- |
| **Git Pull**  | Snadné    | ⚡⚡⚡   | ✅ Nejlepší             |
| **SCP/rsync** | Střední   | ⚡⚡     | ⚠️ Bez Git repo         |
| **FTP**       | Snadné    | ⚡       | ❌ Pomalé, nespolehlivé |

---

## 🎯 Metoda 1: Git Pull (DOPORUČENO)

Nejčistší a nejbezpečnější metoda.

### Předpoklady:

- Máte Git repository (GitHub, GitLab, Bitbucket)
- Server má přístup k vašemu repo (HTTPS s tokenem nebo SSH klíč)
- Pro privátní repo: GitHub Personal Access Token

### Setup (jednorázově):

#### 1. Vytvořte Git repository

```bash
# Lokálně (pokud ještě nemáte):
cd /Users/lukas/SyncMyDay
git init
git add .
git commit -m "Initial commit"

# Vytvořte repo na GitHubu/GitLabu a pushněte:
git remote add origin https://github.com/vase-jmeno/syncmyday.git
git branch -M main
git push -u origin main
```

#### 2. Vytvořte Personal Access Token (pro privátní repo)

**Pokud máte privátní repository**, budete potřebovat token pro autentizaci:

1. Jděte na https://github.com/settings/tokens
2. Klikněte **"Generate new token"** → **"Generate new token (classic)"**
3. Nastavte:
   - **Note**: `SyncMyDay Server`
   - **Expiration**: 90 days (nebo No expiration)
   - **Scopes**: Zaškrtněte ✅ **repo** (celý)
4. Klikněte **"Generate token"**
5. **ZKOPÍRUJTE TOKEN** (uvidíte ho jen jednou!)
   - Vypadá jako: `ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx`

**Pro veřejné repository tento krok přeskočte** - Git pull funguje bez autentizace.

#### 3. Clone projektu na server

**Poznámka:** Některé hostingy (např. cesky-hosting.cz) mají omezené chroot prostředí a **nepodporují SSH klíče**. V tom případě použijte HTTPS metodu.

##### Varianta A: HTTPS (doporučeno pro omezené hostingy)

```bash
# Na serveru:
cd /
git clone https://github.com/vase-jmeno/syncmyday.git syncmyday.cz

# Pro veřejné repository to funguje bez autentizace
# Pro privátní repo budete potřebovat Personal Access Token
```

Pro privátní repo při pull:
- **Username**: váš-github-username
- **Password**: Personal Access Token (ne heslo!)

##### Varianta B: SSH klíče (pouze pro plné VPS s root přístupem)

**Funguje pouze pokud máte:**
- Plný přístup k domovskému adresáři
- Možnost vytvořit `~/.ssh` složku
- Standardní Linux prostředí (ne chroot)

```bash
# Na serveru (POUZE pokud máte plný přístup):
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Vygenerujte SSH klíč:
ssh-keygen -t ed25519 -C "syncmyday@server"
# Stiskněte Enter 3x (bez hesla)

# Zobrazte veřejný klíč:
cat ~/.ssh/id_ed25519.pub
```

Zkopírujte výstup a přidejte do GitHub/GitLab:
- **GitHub**: Settings → SSH and GPG keys → New SSH key
- **GitLab**: Preferences → SSH Keys

Pak clone přes SSH:
```bash
cd /
git clone git@github.com:vase-jmeno/syncmyday.git syncmyday.cz
```

**⚠️ Pro cesky-hosting.cz a podobné hostingy s chroot: Použijte Variantu A (HTTPS)!**

---

### 🚀 Deployment workflow:

#### Krok 1: Lokální vývoj

```bash
# Na vašem počítači:
cd /Users/lukas/SyncMyDay

# Dělejte změny...
# Testujte lokálně...

# Commit:
git add .
git commit -m "Popis změn"

# Push na GitHub:
git push origin main
```

#### Krok 2: Deploy na server

```bash
# Připojte se na server:
ssh syncmyday_cz@ssh.syncmyday.cz

# Spusťte deployment skript:
bash ~/deploy.sh
```

#### Krok 3: Deployment skript

Vytvořte na serveru deployment skript.

**Pro standardní VPS:**
```bash
nano ~/deploy.sh
```

**Pro cesky-hosting.cz a podobné (kde `~` = `/`):**
```bash
nano /deploy.sh
```

Vložte:

```bash
#!/bin/bash
set -e

echo "🚀 Začínám deployment..."
echo "================================"

# Barvy pro output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Cesta k projektu
PROJECT_PATH="/syncmyday.cz"

# Přejít do projektu
cd $PROJECT_PATH

# 1. Udělat zálohu (pro případ problémů)
echo -e "${YELLOW}📦 Vytvářím zálohu...${NC}"
# Pro standardní VPS použijte $HOME/backups
# Pro chroot (cesky-hosting) použijte /backups
BACKUP_DIR="${HOME}/backups"
[ "$HOME" = "/" ] && BACKUP_DIR="/backups"
mkdir -p $BACKUP_DIR
DATE=$(date +%Y%m%d_%H%M%S)
tar -czf "$BACKUP_DIR/syncmyday_$DATE.tar.gz" \
    --exclude='vendor' \
    --exclude='node_modules' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    . 2>/dev/null || true
echo -e "${GREEN}✓ Záloha vytvořena: syncmyday_$DATE.tar.gz${NC}"

# 2. Stáhnout nejnovější změny
echo -e "${YELLOW}📥 Stahuji změny z Gitu...${NC}"
git fetch origin
git reset --hard origin/main
echo -e "${GREEN}✓ Změny staženy${NC}"

# 3. Composer install (pokud se změnil composer.json)
if git diff HEAD@{1} --name-only | grep -q "composer"; then
    echo -e "${YELLOW}📦 Aktualizuji závislosti (composer)...${NC}"
    composer install --no-dev --optimize-autoloader --no-interaction
    echo -e "${GREEN}✓ Závislosti aktualizovány${NC}"
else
    echo -e "${GREEN}⏭  Composer beze změn${NC}"
fi

# 4. Spustit migrace
echo -e "${YELLOW}🗄️  Spouštím databázové migrace...${NC}"
php run-artisan.php migrate --force
echo -e "${GREEN}✓ Migrace dokončeny${NC}"

# 5. Cache clear & rebuild
echo -e "${YELLOW}⚡ Čistím cache...${NC}"
php run-artisan.php config:clear
php run-artisan.php cache:clear
php run-artisan.php view:clear
echo -e "${GREEN}✓ Cache vyčištěna${NC}"

echo -e "${YELLOW}⚡ Rebuilduji cache...${NC}"
php run-artisan.php config:cache
php run-artisan.php route:cache
php run-artisan.php view:cache
echo -e "${GREEN}✓ Cache obnovena${NC}"

# 6. Nastavení oprávnění
echo -e "${YELLOW}🔒 Nastavuji oprávnění...${NC}"
chmod -R 775 storage bootstrap/cache
chmod 600 .env
echo -e "${GREEN}✓ Oprávnění nastavena${NC}"

# 7. Smazat staré zálohy (starší než 7 dní)
echo -e "${YELLOW}🧹 Čistím staré zálohy...${NC}"
find $BACKUP_DIR -name "syncmyday_*.tar.gz" -mtime +7 -delete
echo -e "${GREEN}✓ Staré zálohy smazány${NC}"

echo ""
echo -e "${GREEN}================================${NC}"
echo -e "${GREEN}✅ Deployment dokončen!${NC}"
echo -e "${GREEN}================================${NC}"
echo ""
echo "📊 Statistiky:"
git log -1 --pretty=format:"   Poslední commit: %h - %s (%cr)"
echo ""
echo "   Verze PHP: $(php -v | head -n 1)"
echo ""
echo "🌐 Aplikace: https://syncmyday.cz"
```

Uložte (Ctrl+O, Enter, Ctrl+X) a nastavte oprávnění:

```bash
chmod +x ~/deploy.sh
```

---

### 📝 Kompletní workflow příklad:

```bash
# ========================================
# LOKÁLNĚ (váš počítač):
# ========================================

cd /Users/lukas/SyncMyDay

# Upravte soubor:
nano app/Http/Controllers/DashboardController.php

# Otestujte lokálně:
php artisan serve
# Otevřete http://localhost:8000 a zkontrolujte

# Commit:
git add .
git commit -m "Oprava dashboardu - přidání nového widgetu"
git push origin main

# ========================================
# NA SERVERU:
# ========================================

ssh syncmyday_cz@ssh.syncmyday.cz
bash ~/deploy.sh

# Hotovo! 🎉
```

---

## 🔧 Metoda 2: SCP/rsync (bez Gitu)

Pokud nepoužíváte Git, můžete nahrávat soubory přímo.

### Varianta A: SCP pro jednotlivé soubory

```bash
# Lokálně - nahrát jeden soubor:
scp app/Http/Controllers/DashboardController.php \
    syncmyday_cz@ssh.syncmyday.cz:/syncmyday.cz/app/Http/Controllers/

# Nahrát celou složku:
scp -r app/Http/Controllers/* \
    syncmyday_cz@ssh.syncmyday.cz:/syncmyday.cz/app/Http/Controllers/
```

### Varianta B: rsync (rychlejší, jen změny)

```bash
# Lokálně - synchronizovat celý projekt:
rsync -avz --delete \
    --exclude='.git' \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.env' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    /Users/lukas/SyncMyDay/ \
    syncmyday_cz@ssh.syncmyday.cz:/syncmyday.cz/
```

Pak na serveru:

```bash
ssh syncmyday_cz@ssh.syncmyday.cz
cd /syncmyday.cz

# Composer install (pokud se změnil composer.json)
composer install --no-dev --optimize-autoloader

# Migrace
php run-artisan.php migrate --force

# Cache
php run-artisan.php config:cache
php run-artisan.php route:cache
php run-artisan.php view:cache
```

---

## 🛡️ Bezpečné postupy

### 1. Vždy testujte lokálně

```bash
# Před commitem:
cd /Users/lukas/SyncMyDay
php artisan test
php artisan config:cache
php artisan serve
# Otevřete localhost:8000 a zkontrolujte
```

### 2. Používejte větve (branches)

```bash
# Pro větší změny vytvořte branch:
git checkout -b feature/nova-funkce

# Dělejte změny...
git add .
git commit -m "Implementace nové funkce"
git push origin feature/nova-funkce

# Až je vše otestované, mergněte do main:
git checkout main
git merge feature/nova-funkce
git push origin main

# Pak teprve deployujte na server
```

### 3. Database zálohy před migrací

```bash
# Na serveru před spuštěním migrací:
BACKUP_DIR="$HOME/db_backups"
mkdir -p $BACKUP_DIR
DATE=$(date +%Y%m%d_%H%M%S)

# Export databáze
mysqldump -u VASE_DB_USER -p VASE_DB_NAME > "$BACKUP_DIR/db_$DATE.sql"

# Pak teprve migrace
php run-artisan.php migrate --force
```

### 4. Maintenance mode při velkých změnách

```bash
# Na serveru:
cd /syncmyday.cz

# Zapnout maintenance mode:
php run-artisan.php down --secret="tajny-pristup-123"
# Stále můžete přistoupit přes: https://syncmyday.cz/tajny-pristup-123

# Deployment...
bash ~/deploy.sh

# Vypnout maintenance mode:
php run-artisan.php up
```

---

## 🔄 Rollback (návrat zpět)

Pokud po deploymentu něco nefunguje:

### Metoda 1: Git rollback

```bash
# Na serveru:
cd /syncmyday.cz

# Zobrazit historii:
git log --oneline -10

# Vrátit se na předchozí commit:
git reset --hard COMMIT_HASH

# Obnovit cache:
php run-artisan.php config:cache
php run-artisan.php route:cache
php run-artisan.php view:cache
```

### Metoda 2: Záloha

```bash
# Na serveru:
cd ~

# Seznam záloh:
ls -lh backups/

# Obnovit ze zálohy:
cd /syncmyday.cz
tar -xzf ~/backups/syncmyday_20251009_143022.tar.gz

# Cache:
php run-artisan.php config:cache
```

### Metoda 3: Database rollback

```bash
# Obnovit databázi ze zálohy:
mysql -u VASE_DB_USER -p VASE_DB_NAME < ~/db_backups/db_20251009_143022.sql
```

---

## 📊 Monitoring po deploymentu

### 1. Zkontrolujte logy:

```bash
# Na serveru:
tail -50 /syncmyday.cz/storage/logs/laravel.log
```

### 2. Zkontrolujte funkčnost:

```bash
# Zdraví aplikace:
curl https://syncmyday.cz/health

# Odpověď by měla být: {"status":"ok"}
```

### 3. Test klíčových funkcí:

- ✅ Přihlášení funguje
- ✅ Dashboard se načte
- ✅ Připojení kalendářů funguje
- ✅ Sync pravidla se ukládají

---

## 🚨 Checklist před deploymentem

```
PŘED COMMITEM (lokálně):
□ Kód otestován lokálně
□ Žádné syntax errory (php artisan config:cache)
□ Testy prošly (php artisan test)
□ .env změny zdokumentovány (pokud jsou)
□ Commit message je popisný

PŘED DEPLOYEM (server):
□ Záloha databáze vytvořena
□ Záloha souborů vytvořena (deploy.sh to dělá automaticky)
□ Nízká návštěvnost (ideálně)

PO DEPLOYMENTU:
□ Zkontrolovat logy (žádné errory)
□ Test přihlášení
□ Test hlavních funkcí
□ Monitoring běží (UptimeRobot)
```

---

## ⚡ Rychlé příkazy (cheat sheet)

### Lokálně:

```bash
# Commit a push:
git add .
git commit -m "Popis změn"
git push origin main
```

### Na serveru:

```bash
# Rychlý deployment:
ssh syncmyday_cz@ssh.syncmyday.cz "bash ~/deploy.sh"

# Nebo SSH + deploy:
ssh syncmyday_cz@ssh.syncmyday.cz
bash ~/deploy.sh

# Jen pull změn (bez cache rebuild):
cd /syncmyday.cz && git pull origin main

# Rychlý cache rebuild:
cd /syncmyday.cz && php run-artisan.php config:cache && php run-artisan.php route:cache
```

---

## 🤖 Automatizace (advanced)

### GitHub Actions (CI/CD)

Vytvořte `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy via SSH
        uses: appleboy/ssh-action@master
        with:
          host: ssh.syncmyday.cz
          username: syncmyday_cz
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /syncmyday.cz
            bash ~/deploy.sh
```

Pak každý push do `main` automaticky deployuje na server! 🚀

---

## 📝 Doporučený workflow

### Pro menší změny (bugfixy):

```bash
# 1. Lokálně:
git add .
git commit -m "Fix: oprava chyby v dashboardu"
git push

# 2. Na serveru:
ssh syncmyday_cz@ssh.syncmyday.cz "bash ~/deploy.sh"
```

### Pro větší změny (nové funkce):

```bash
# 1. Vytvořte branch:
git checkout -b feature/nova-funkce

# 2. Vývoj + testování...

# 3. Merge do main:
git checkout main
git merge feature/nova-funkce
git push

# 4. Deploy:
ssh syncmyday_cz@ssh.syncmyday.cz
php run-artisan.php down --secret="pristup123"
bash ~/deploy.sh
php run-artisan.php up
```

---

## 🎓 Tipy a triky

### 1. Alias pro rychlý deploy

Na vašem počítači v `~/.zshrc` nebo `~/.bashrc`:

```bash
alias deploy-syncmyday='git push origin main && ssh syncmyday_cz@ssh.syncmyday.cz "bash ~/deploy.sh"'
```

Pak stačí:

```bash
deploy-syncmyday
```

### 2. Watch logy v reálném čase

```bash
# Na serveru:
tail -f /syncmyday.cz/storage/logs/laravel.log
```

### 3. Rychlý health check

```bash
# Lokálně:
curl https://syncmyday.cz/health && echo " ✅ Server běží"
```

---

## 📞 Pomoc při problémech

### Deployment selhal - co dělat?

1. **Zkontrolujte logy**:

   ```bash
   tail -100 /syncmyday.cz/storage/logs/laravel.log
   ```

2. **Rollback na předchozí verzi**:

   ```bash
   cd /syncmyday.cz
   git log --oneline -5  # Najděte předchozí commit
   git reset --hard COMMIT_HASH
   php run-artisan.php config:cache
   ```

3. **Obnovte ze zálohy**:
   ```bash
   ls -lh ~/backups/
   cd /syncmyday.cz
   tar -xzf ~/backups/syncmyday_YYYYMMDD_HHMMSS.tar.gz
   ```

### Časté problémy:

| Problém                      | Řešení                                                                 |
| ---------------------------- | ---------------------------------------------------------------------- |
| **500 error po deploymentu** | `php run-artisan.php config:clear && php run-artisan.php config:cache` |
| **Class not found**          | `composer dump-autoload --optimize`                                    |
| **Migrace selhala**          | Obnovte DB ze zálohy, opravte migraci, zkuste znovu                    |
| **Změny se neprojevují**     | Vyčistěte cache: `php run-artisan.php cache:clear`                     |

---

**Máte dotazy k deploymentu? Ptejte se! 🚀**
