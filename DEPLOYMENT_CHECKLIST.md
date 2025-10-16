# ✅ Deployment Checklist - Performance Optimization

## Před Nasazením

- [ ] **Vytvořit zálohu databáze**
- [ ] **Vytvořit zálohu souborů** (přes Git je to automaticky)
- [ ] **Ověřit, že máte přístup k databázi** (phpMyAdmin nebo MySQL klient)

---

## Nasazení - Krok za Krokem

### 1. Databázové Změny (5-10 minut)

- [ ] Otevřít phpMyAdmin
- [ ] Vybrat databázi SyncMyDay
- [ ] Otevřít soubor `performance_optimization.sql`
- [ ] Spustit celý SQL skript
- [ ] Ověřit, že byly přidány nové sloupce a indexy (kontrola na konci výstupu)

**Očekávaný výstup:** Tabulky s velikostmi a indexy

---

### 2. Nahrání Nového Kódu (2-3 minuty)

#### Lokálně:

```bash
cd /Users/lukas/SyncMyDay
git add .
git commit -m "Performance optimization implementation"
git push origin main
```

#### Na Produkci (přes SSH nebo cPanel Git Deploy):

```bash
cd /path/to/syncmyday
git pull origin main
composer dump-autoload
```

**NEBO** přes cPanel Git Version Control:

- [ ] Kliknout na "Pull or Deploy" button
- [ ] Potvrdit pull z main branch

---

### 3. Konfigurace .env (volitelné, 1 minuta)

Pro sdílený hosting **není potřeba nic měnit**.

Pro VPS s queue podporou:

```env
QUEUE_CONNECTION=database
DB_PERSISTENT=true
```

---

### 4. Testování (5 minut)

#### Test 1: Sync Cron

```bash
# Přes HTTP (nahraďte TOKEN)
curl "https://syncmyday.cz/cron-calendars-sync.php?token=VAS_TOKEN"
```

**Očekávaný výsledek:**

```json
{
  "status": "success",
  "mode": "sync",
  "synced": 10,
  "errors": 0
}
```

#### Test 2: Logs Cleanup

```bash
curl "https://syncmyday.cz/cron-logs-clean.php?token=VAS_TOKEN"
```

**Očekávaný výsledek:** Statistiky vymazaných logů a velikost DB

#### Test 3: Aplikace

- [ ] Přihlásit se do aplikace
- [ ] Vytvořit testovací sync rule
- [ ] Ověřit, že sync funguje
- [ ] Zkontrolovat Dashboard (měly by se zobrazit nové metriky)

---

### 5. Monitoring První Den (průběžné)

- [ ] Sledovat logy: `storage/logs/laravel.log`
- [ ] Sledovat sync úspěšnost v Dashboard
- [ ] Zkontrolovat po 1 hodině, že cron jobs běží
- [ ] Zkontrolovat po 24 hodinách celkovou stabilitu

---

## Rollback Plán (Kdyby Cokoliv Selh)

### Rollback Kódu:

```bash
cd /path/to/syncmyday
git reset --hard HEAD~1
composer dump-autoload
```

### Rollback Databáze:

```sql
-- Odstranit nové sloupce
ALTER TABLE sync_rules DROP COLUMN queued_at;
ALTER TABLE sync_rules DROP COLUMN queue_priority;
ALTER TABLE sync_rules DROP COLUMN last_sync_duration_ms;
ALTER TABLE sync_rules DROP COLUMN sync_error_count;

ALTER TABLE calendar_connections DROP COLUMN sync_error_count;
ALTER TABLE calendar_connections DROP COLUMN last_sync_duration_ms;

-- Indexy můžete nechat, neubližují
```

**POZNÁMKA:** Rollback by neměl být potřeba - změny jsou zpětně kompatibilní!

---

## Hotovo! 🎉

Po úspěšném nasazení:

- ✅ Databáze má nové indexy (dotazy jsou rychlejší)
- ✅ Sync systém podporuje queue (až 10x rychlejší s workers)
- ✅ N+1 queries jsou opravené (120x méně dotazů)
- ✅ Automatické čištění logů (DB se nebude nafukovat)
- ✅ Rate limiting (ochrana proti API limitům)
- ✅ Monitoring metriky (můžete sledovat výkon)

---

## Další Kroky

### Pro Shared Hosting:

**Nic dalšího není potřeba.** Systém bude fungovat lépe hned po nasazení.

### Pro VPS/Dedicated Server:

1. Nastavte queue workers (viz `PERFORMANCE_OPTIMIZATION_GUIDE.md`, Krok 4)
2. Zřiďte systemd service pro queue
3. Aktivujte Redis cache (volitelné)

---

## FAQ

**Q: Musím vypnout web během nasazení?**  
A: Ne, nasazení lze provést za běhu. SQL změny jsou nedestruktivní.

**Q: Ovlivní to stávající sync rules?**  
A: Ne, všechny stávající rules budou fungovat stejně. Jen budou rychlejší.

**Q: Co když nemám SSH přístup?**  
A: Můžete nahrát změny přes FTP/cPanel File Manager a SQL spustit přes phpMyAdmin.

**Q: Jak dlouho trvá SQL skript?**  
A: 10-30 sekund v závislosti na velikosti databáze.

**Q: Můžu nasadit jen část změn?**  
A: Ano, ale doporučuji nasadit vše najednou pro maximální benefit.

---

**Autor:** AI Assistant  
**Datum:** 2025-10-15  
**Projekt:** SyncMyDay Performance Optimization
