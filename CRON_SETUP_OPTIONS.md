# Cron Setup - Všechny možnosti

Existují **3 způsoby**, jak spustit cron job na sdíleném hostingu:

---

## ✅ Možnost 1: PHP Soubor (cron.php) - DOPORUČENO

Nejjednodušší varianta pro většinu hostingů.

### Soubor: `cron.php`

Tento soubor je již připraven v rootu projektu.

### Nastavení v cPanel:

**Common Settings:** Once Per Minute (každou minutu)

**Command:**
```bash
/usr/bin/php /home/username/public_html/syncmyday/cron.php
```

**Alternativy (podle vašeho hostingu):**
```bash
# Pokud máte PHP 8.0
/usr/bin/php80 /home/username/public_html/syncmyday/cron.php

# Pokud máte PHP 8.2
/usr/bin/php82 /home/username/public_html/syncmyday/cron.php

# Nebo s cd do složky
cd /home/username/public_html/syncmyday && /usr/bin/php cron.php
```

### Výhody:
- ✅ Jednoduché
- ✅ Funguje na většině hostingů
- ✅ Přímý PHP soubor bez artisan

### Test:
```bash
# V SSH
php /path/to/syncmyday/cron.php

# Měli byste vidět:
# [2025-10-09 19:00:00] Schedule:run executed with status: 0
```

---

## ✅ Možnost 2: HTTP Endpoint (přes URL)

Pro hostingy, kde nelze spouštět PHP CLI přímo.

### Krok 1: Nastavte CRON_SECRET v .env

```env
CRON_SECRET=nahodny-dlouhy-tajny-retezec-123456
```

**Vygenerujte náhodný token:**
```bash
php -r "echo bin2hex(random_bytes(32));"
# Výstup např: 7f4a9b2c8d3e1f6a5b9c0d2e4f8a1c3e5b7d9f0a2c4e6f8a0b2d4e6f8a0c2e4
```

### Krok 2: Nastavte cron v cPanel

**Common Settings:** Once Per Minute

**Command:**
```bash
curl -s "https://syncmyday.cz/cron/run?token=vas-cron-secret-token" > /dev/null 2>&1
```

**Nebo s wget:**
```bash
wget -q -O /dev/null "https://syncmyday.cz/cron/run?token=vas-cron-secret-token"
```

### Výhody:
- ✅ Funguje i bez CLI přístupu
- ✅ Lze volat i z externích cron služeb (cron-job.org)
- ✅ Zabezpečeno tokenem

### Test:
```bash
# V prohlížeči nebo curl
curl "https://syncmyday.cz/cron/run?token=vas-token"

# Odpověď:
# {
#   "status": "success",
#   "message": "Scheduled tasks executed",
#   "output": "...",
#   "timestamp": "2025-10-09 19:00:00"
# }
```

---

## ✅ Možnost 3: Externí Cron Služba

Pokud hosting vůbec nepodporuje cron jobs.

### Používá: HTTP endpoint (možnost 2)

### Služby, které můžete použít:

1. **cron-job.org** (zdarma)
   - https://cron-job.org
   - Registrace zdarma
   - Nastavíte URL: `https://syncmyday.cz/cron/run?token=vas-token`
   - Interval: Every minute

2. **EasyCron** (zdarma do 20 jobů)
   - https://www.easycron.com
   
3. **UptimeRobot** (může sloužit jako workaround)
   - https://uptimerobot.com
   - Nastavíte jako "monitor" s 1min intervalem

### Nastavení na cron-job.org:

1. Registrace na https://cron-job.org
2. Create Cronjob
   - **Title:** SyncMyDay Scheduler
   - **Address:** `https://syncmyday.cz/cron/run?token=vas-token`
   - **Schedule:** Every minute
   - **Enabled:** Yes
3. Save

### Výhody:
- ✅ Funguje i bez přístupu k hostingu
- ✅ Spolehlivé externí služby
- ✅ Notifikace, když cron selže

---

## 📋 Srovnání možností

| Možnost | Složitost | Požadavky | Doporučeno |
|---------|-----------|-----------|------------|
| **PHP soubor** | Nejjednodušší | PHP CLI přístup v cronu | ✅ Ano |
| **HTTP endpoint** | Střední | HTTPS, CRON_SECRET | Pokud možnost 1 nefunguje |
| **Externí služba** | Snadná | Žádné na hostingu | Pokud hosting nemá cron |

---

## 🔍 Jak zjistit, která možnost použít?

### Test 1: Má hosting cron jobs?

V cPanel hledejte:
- "Cron Jobs"
- "Naplánované úlohy"
- "Advanced" → "Cron Jobs"

**ANO** → Pokračujte na Test 2  
**NE** → Použijte Možnost 3 (Externí služba)

### Test 2: Podporuje hosting PHP CLI?

V cPanel cronu zkuste:
```bash
/usr/bin/php -v
```

**Funguje** → Použijte Možnost 1 (PHP soubor) ✅  
**Nefunguje** → Použijte Možnost 2 (HTTP endpoint)

---

## ⚙️ Nastavení pro jednotlivé možnosti

### .env konfigurace

**Pro možnost 1 (PHP soubor):**
```env
# Žádná speciální konfigurace není potřeba
```

**Pro možnost 2 a 3 (HTTP endpoint):**
```env
CRON_SECRET=nahodny-dlouhy-tajny-retezec-123456
```

Vygenerujte tajný token:
```bash
php -r "echo bin2hex(random_bytes(32));"
```

---

## 🧪 Testování

### Test PHP souboru:
```bash
php /path/to/syncmyday/cron.php
```

### Test HTTP endpointu:
```bash
curl "https://syncmyday.cz/cron/run?token=vas-token"
```

### Kontrola, zda cron běží:

Sledujte logy:
```bash
tail -f storage/logs/laravel.log
```

Každou minutu by měl běžet `schedule:run` a v 9:00 `trial:send-ending-notifications`.

---

## 🔒 Zabezpečení

### Pro HTTP endpoint:

1. **Používejte silný CRON_SECRET:**
   - Minimálně 32 znaků
   - Náhodný, generovaný

2. **HTTPS only:**
   - Nikdy nepoužívejte HTTP (token by byl viditelný)

3. **Rate limiting:**
   - HTTP endpoint má automatický throttling

4. **Nelogujte token:**
   - Token nikdy nevypisujte do logů

---

## 📝 Příklady pro různé hostingy

### Czech Hosting (cPanel):

**Možnost 1 - PHP soubor:**
```
* * * * * /usr/bin/php /home/username/domains/syncmyday.cz/public_html/cron.php
```

### Wedos (cPanel):

**Možnost 1 - PHP soubor:**
```
* * * * * /usr/local/bin/php /home/username/www/cron.php
```

### Forpsi (DirectAdmin):

**Možnost 2 - HTTP endpoint:**
```
* * * * * curl -s "https://syncmyday.cz/cron/run?token=vas-token" > /dev/null
```

---

## ✅ Doporučený postup

1. **Zkuste nejdřív Možnost 1** (PHP soubor)
2. Pokud nefunguje → **Možnost 2** (HTTP endpoint)
3. Pokud hosting vůbec nemá cron → **Možnost 3** (Externí služba)

---

## 🎯 Co dělá cron job?

Každou minutu zkontroluje, jestli není čas spustit nějaký scheduled task.

**Scheduled tasky:**

| Kdy | Task | Popis |
|-----|------|-------|
| Každých 5 minut | `calendars:sync` | Synchronizuje kalendáře |
| Každých 6 hodin | `webhooks:renew` | Obnovuje webhooks |
| Každou hodinu | `connections:check` | Kontroluje připojení |
| Denně v 00:00 | `logs:clean` | Čistí staré logy |
| **Denně v 09:00** | **`trial:send-ending-notifications`** | **Posílá trial ending emaily** |

Laravel automaticky spustí jen ty tasky, které mají přijít na řadu.

---

Máte otázky? Postupujte podle testu výše a vyberte vhodnou možnost pro váš hosting! 🚀

