# 🚀 VPS Deployment Guide - SyncMyDay

Moderní deployment pro VPS s plným root přístupem, Redis a Supervisor.

## 📋 Předpoklady na serveru

Ujisti se, že máš nainstalováno:

```bash
# PHP 8.2+
php -v

# Composer
composer --version

# Redis
redis-cli ping  # mělo by vrátit "PONG"

# Git
git --version

# Supervisor (pro queue workers)
supervisorctl version
```

## 🔧 Prvotní nastavení na serveru

### 1. Naklonuj projekt na server

```bash
# Připoj se na server
ssh root@tvuj-server.cz

# Vytvoř složku pro projekt
mkdir -p /var/www/syncmyday
cd /var/www/syncmyday

# Naklonuj z GitHubu
git clone https://github.com/lukasslehofer-cz/syncmyday.git .

# Nastav správná oprávnění
chown -R www-data:www-data /var/www/syncmyday
chmod -R 775 storage bootstrap/cache
```

### 2. Nastav .env soubor

```bash
# Zkopíruj .env.example nebo vytvoř nový
nano /var/www/syncmyday/.env
```

Důležité proměnné pro VPS:
```env
APP_ENV=production
APP_DEBUG=false

# Redis pro cache, sessions a queue!
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Spusť první deployment

```bash
cd /var/www/syncmyday

# Proveď úpravy v deploy-vps.sh (nastavit PROJECT_PATH)
nano deploy-vps.sh
# Změň: PROJECT_PATH="/var/www/syncmyday"  # tvoje cesta

# Spusť deployment
bash deploy-vps.sh
```

### 4. Nastav Supervisor pro queue workers

Vytvoř `/etc/supervisor/conf.d/syncmyday-worker.conf`:

```ini
[program:syncmyday-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/syncmyday/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 --timeout=90
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

Aktivuj:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start syncmyday-worker:*
sudo supervisorctl status
```

### 5. Nastav Laravel Scheduler (cron)

```bash
crontab -e
```

Přidej POUZE TENTO jeden řádek:
```bash
* * * * * cd /var/www/syncmyday && php artisan schedule:run >> /dev/null 2>&1
```

Tento jeden cron job se postará o všechno (sync každých 5 minut, webhook renewal, logy, atd.).

---

## 🔄 Běžný deployment (po každém git push)

Na serveru:

```bash
# SSH připojení
ssh root@tvuj-server.cz

# Přejdi do projektu
cd /var/www/syncmyday

# Spusť deployment
bash deploy-vps.sh
```

**To je vše!** Skript automaticky:
- ✅ Zapne maintenance mode
- ✅ Vytvoří zálohu
- ✅ Pullne změny z Gitu
- ✅ Nainstaluje Composer závislosti
- ✅ Spustí migrace
- ✅ Vyčistí a znovu vytvoří cache
- ✅ Restartuje queue workers (Supervisor)
- ✅ Restartuje PHP-FPM
- ✅ Vypne maintenance mode

---

## ⚙️ Co deploy-vps.sh dělá

```bash
# Běžný deployment (s backupem)
bash deploy-vps.sh

# Bez zálohy (rychlejší)
bash deploy-vps.sh no
```

### Kroky deployment skriptu:

1. **Maintenance Mode** - zapne "We're updating" stránku
2. **Backup** - vytvoří zálohu projektu (volitelné)
3. **Git Pull** - stáhne změny z `main` větve
4. **Composer Install** - nainstaluje PHP závislosti
5. **Migrations** - spustí databázové migrace
6. **Cache Clear & Rebuild** - aktualizuje všechny cache
7. **Permissions** - nastaví správná oprávnění
8. **Restart Queue Workers** - přes Supervisor
9. **Restart PHP-FPM** - reload PHP služby
10. **Redis Clear** - vyčistí Redis cache
11. **Maintenance Off** - vypne maintenance mode
12. **Cleanup** - smaže staré zálohy (>7 dní)

---

## 🔍 Monitoring a kontrola

### Zkontroluj queue workers:
```bash
# Stav workerů
sudo supervisorctl status syncmyday-worker:*

# Restart workerů
sudo supervisorctl restart syncmyday-worker:*

# Logy workerů
tail -f /var/www/syncmyday/storage/logs/worker.log
```

### Zkontroluj aplikační logy:
```bash
tail -f /var/www/syncmyday/storage/logs/laravel.log
```

### Zkontroluj frontu jobů:
```bash
cd /var/www/syncmyday
php artisan queue:monitor
php artisan queue:failed
```

### Zkontroluj Redis:
```bash
redis-cli ping  # mělo by vrátit "PONG"
redis-cli info stats
```

### Zkontroluj cron:
```bash
# Zkontroluj, že cron běží
crontab -l

# Zkontroluj poslední spuštění scheduleru
grep "schedule:run" /var/log/syslog | tail -20
```

---

## 🐛 Troubleshooting

### Queue workers nejdou restartovat
```bash
# Zkontroluj Supervisor
sudo supervisorctl status

# Restart celého Supervisoru
sudo systemctl restart supervisor

# Zkontroluj logy Supervisoru
sudo tail -f /var/log/supervisor/supervisord.log
```

### Deployment selhal na migraci
```bash
# Vrať se k předchozí verzi
cd /var/www/syncmyday
git reset --hard HEAD~1

# Nebo obnov zálohu
tar -xzf backups/syncmyday_YYYYMMDD_HHMMSS.tar.gz
```

### Redis nefunguje
```bash
# Zkontroluj, že Redis běží
sudo systemctl status redis-server

# Restart Redis
sudo systemctl restart redis-server

# Test připojení
redis-cli ping
```

### Permissions error
```bash
# Nastav správná oprávnění
cd /var/www/syncmyday
sudo chown -R www-data:www-data .
sudo chmod -R 775 storage bootstrap/cache
```

---

## 🎯 Best Practices

1. **Vždy testuj lokálně před pushem na main!**
2. **Kontroluj logy po každém deploymentu**
3. **Pravidelně zálohuj databázi**:
   ```bash
   mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
   ```
4. **Monitoruj disk space**:
   ```bash
   df -h
   du -sh /var/www/syncmyday/storage/logs/*
   ```
5. **Používej Git tags pro verze**:
   ```bash
   git tag -a v1.0.0 -m "Production release 1.0.0"
   git push origin v1.0.0
   ```

---

## 📞 Časté příkazy

```bash
# Rychlý deployment
bash deploy-vps.sh

# Deployment bez zálohy
bash deploy-vps.sh no

# Restart queue workers
sudo supervisorctl restart syncmyday-worker:*

# Vyčistit cache ručně
php artisan cache:clear && php artisan config:cache

# Spustit konkrétní artisan příkaz
php artisan calendars:sync

# Zkontrolovat failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## 🔒 Bezpečnost

- ✅ `.env` soubor má oprávnění 600 (jen owner)
- ✅ Maintenance mode při deploymentu
- ✅ Zálohy automaticky po 7 dnech smazány
- ✅ Git ignoruje citlivé soubory
- ✅ Queue workers běží jako `www-data` user

---

## 🎉 Výhody VPS oproti sdílenému hostingu

| Funkce | Sdílený hosting | VPS |
|--------|----------------|-----|
| Real-time sync | ❌ Každých 5 min | ✅ 1-2 minuty |
| Queue workers | ❌ Cron co 5 min | ✅ Běží non-stop |
| Redis cache | ❌ Database cache | ✅ Redis |
| Composer | ❌ Ručně nahrávat | ✅ Na serveru |
| Supervisor | ❌ Není | ✅ Automatický restart |
| Webhooky | ⚠️ Zpožděné | ✅ Okamžité |

---

**Gratuluju k upgradu na VPS! 🚀 Aplikace teď běží na plný výkon.**

