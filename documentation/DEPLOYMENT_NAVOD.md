# 🚀 Jak nahrávat změny na server - Rychlý návod

Stručný průvodce pro deployment změn z vašeho počítače na produkční server.

---

## ⚡ Nejrychlejší způsob (s Gitem)

### Jednorázové nastavení:

```bash
# 1. Vytvořte Git repo (pokud nemáte):
cd /Users/lukas/SyncMyDay
git init
git add .
git commit -m "Initial commit"

# 2. Vytvořte repo na GitHubu a pushněte:
git remote add origin https://github.com/vase-jmeno/syncmyday.git
git push -u origin main

# 3. Na serveru nastavte deployment skript:
ssh syncmyday_cz@ssh.syncmyday.cz
nano ~/deploy.sh
```

Zkopírujte obsah ze souboru `server-deploy.sh` a uložte.

```bash
chmod +x ~/deploy.sh
```

### Každý deployment:

```bash
# 1. LOKÁLNĚ - Upravte kód, otestujte:
cd /Users/lukas/SyncMyDay
# ... dělejte změny ...

# 2. LOKÁLNĚ - Commit a push:
git add .
git commit -m "Popis změn"
git push origin main

# 3. NA SERVERU - Deploy:
ssh syncmyday_cz@ssh.syncmyday.cz
bash ~/deploy.sh
```

**To je vše! 🎉**

---

## 🔄 Kompletní workflow příklad

### Scénář: Opravujete bug v dashboardu

```bash
# ========================================
# NA VAŠEM POČÍTAČI:
# ========================================

cd /Users/lukas/SyncMyDay

# 1. Upravte soubor
nano app/Http/Controllers/DashboardController.php

# 2. Otestujte lokálně
php artisan serve
# Otevřete http://localhost:8000 v prohlížeči

# 3. Pokud funguje, commitněte
git add app/Http/Controllers/DashboardController.php
git commit -m "Fix: oprava chyby v načítání statistik dashboardu"
git push origin main

# ========================================
# NA SERVERU (jednoduchý způsob):
# ========================================

ssh syncmyday_cz@ssh.syncmyday.cz "bash ~/deploy.sh"

# Hotovo! ✅
```

---

## 📋 Deployment checklist

### Před každým deploymentem:

```
□ Kód otestován lokálně
□ Žádné chyby v logu
□ Git commit má popisnou zprávu
□ Pushli jste na GitHub/GitLab
```

### Po deploymentu:

```
□ Zkontrolovat https://syncmyday.cz - funguje?
□ Zkontrolovat logy - žádné errory?
□ Test přihlášení - funguje?
```

---

## 🆘 Co když něco pokazím?

### Rychlý rollback (vrácení změn):

```bash
# Připojte se na server:
ssh syncmyday_cz@ssh.syncmyday.cz

# Zobrazte posledních 5 commitů:
cd /syncmyday.cz
git log --oneline -5

# Vrátit se na předchozí commit:
git reset --hard HASH_PREDCHOZIHO_COMMITU

# Rebuild cache:
php run-artisan.php config:cache
php run-artisan.php route:cache
```

### Obnovení ze zálohy:

```bash
# Seznam záloh:
ls -lh ~/backups/

# Obnovit:
cd /syncmyday.cz
tar -xzf ~/backups/syncmyday_20251009_143022.tar.gz
php run-artisan.php config:cache
```

---

## 💡 Užitečné aliasy

Přidejte do `~/.zshrc` nebo `~/.bashrc`:

```bash
# Rychlý deployment
alias deploy='git push origin main && ssh syncmyday_cz@ssh.syncmyday.cz "bash ~/deploy.sh"'

# Kontrola serveru
alias server-check='curl -s https://syncmyday.cz/health && echo " ✅"'

# Zobrazit logy
alias server-logs='ssh syncmyday_cz@ssh.syncmyday.cz "tail -50 /syncmyday.cz/storage/logs/laravel.log"'
```

Pak stačí:

```bash
deploy          # Nasadí změny
server-check    # Zkontroluje server
server-logs     # Zobrazí logy
```

---

## 🎯 Typické scénáře

### 1. Malá změna (oprava textu, styling):

```bash
# Lokálně:
git add .
git commit -m "Fix: oprava překlepu"
git push

# Server:
ssh syncmyday_cz@ssh.syncmyday.cz "bash ~/deploy.sh"
```

### 2. Nová funkce (s databázovou migrací):

```bash
# Lokálně:
php artisan make:migration add_new_feature
# ... vytvořte migraci ...
git add .
git commit -m "Feature: nová funkce XYZ"
git push

# Server - s maintenance mode:
ssh syncmyday_cz@ssh.syncmyday.cz
cd /syncmyday.cz
php run-artisan.php down --secret="pristup123"
bash ~/deploy.sh
php run-artisan.php up
```

### 3. Jen CSS/JavaScript změny:

```bash
# Lokálně:
git add .
git commit -m "Style: úprava vzhledu dashboardu"
git push

# Server (bez migrace, jen cache):
ssh syncmyday_cz@ssh.syncmyday.cz
cd /syncmyday.cz
git pull origin main
php run-artisan.php view:cache
```

---

## 📊 Monitoring po deploymentu

### Rychlá kontrola:

```bash
# 1. Server běží?
curl https://syncmyday.cz/health

# 2. Žádné chyby v logu?
ssh syncmyday_cz@ssh.syncmyday.cz "tail -20 /syncmyday.cz/storage/logs/laravel.log"

# 3. Přihlášení funguje?
# Otevřete https://syncmyday.cz v prohlížeči
```

---

## 🔧 Časté problémy a řešení

| Problém                  | Příčina            | Řešení                                                                 |
| ------------------------ | ------------------ | ---------------------------------------------------------------------- |
| **500 Error**            | Stará cache        | `php run-artisan.php config:clear && php run-artisan.php config:cache` |
| **Class not found**      | Autoload zastaralý | `composer dump-autoload`                                               |
| **Změny se neprojevují** | Cache              | `php run-artisan.php cache:clear`                                      |
| **Git pull nefunguje**   | Lokální změny      | `git reset --hard origin/main`                                         |

---

## 📚 Detailní dokumentace

Pro podrobnosti viz:

- **[DEPLOYMENT_WORKFLOW.md](DEPLOYMENT_WORKFLOW.md)** - Kompletní průvodce
- **[NASAZENI_WEDOS_SSH.md](NASAZENI_WEDOS_SSH.md)** - Specifika vašeho hostingu

---

## ✅ Shrnutí

**Základní workflow:**

```
1. Lokálně: Úpravy → Test → Git commit → Git push
2. Server: SSH → bash ~/deploy.sh
3. Kontrola: https://syncmyday.cz funguje?
```

**To je vše co potřebujete! 🚀**

---

**Máte otázky? Ptejte se!** 😊
