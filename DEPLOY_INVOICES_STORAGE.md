# Deployment: Invoice Storage System

## 📋 Co se změnilo

Systém stahování faktur byl vylepšen na **hybridní řešení**:

- PDF faktury se nyní **ukládají lokálně** do `storage/app/invoices/`
- **Rychlejší stahování** - faktury se načítají z disku místo z Fakturoid API
- **Spolehlivější** - funguje i když Fakturoid API má výpadek
- **Automatické fallback** - pokud PDF chybí, stáhne se z API

---

## 🚀 Deployment na produkci

### 1. Nahrát nový soubor na server

Nahrajte tento nový soubor (vytvořený lokálně):

```
config/filesystems.php
```

### 2. Vytvořit adresáře na serveru

Připojte se přes SSH nebo FTP a vytvořte tyto adresáře:

```bash
# Přes SSH:
cd /path/to/syncmyday
mkdir -p storage/app/invoices
chmod -R 775 storage/app
chmod -R 775 storage/app/invoices
```

**Nebo přes FTP/cPanel File Manager:**

1. Otevřete složku `storage/`
2. Vytvořte složku `app/` (pokud neexistuje)
3. Otevřete `storage/app/`
4. Vytvořte složku `invoices/`

### 3. Nastavit správná oprávnění

**DŮLEŽITÉ:** Webserver musí mít právo zapisovat do těchto adresářů.

```bash
# Nastavit vlastníka (pokud máte SSH přístup):
chown -R www-data:www-data storage/app
chown -R www-data:www-data storage/app/invoices

# NEBO nastavit oprávnění:
chmod -R 775 storage/app
chmod -R 775 storage/app/invoices
```

**Na shared hostingu (např. Wedos, Forpsi):**

- Většinou není potřeba nic nastavovat
- Stačí vytvořit složky přes FTP/File Manager
- Pokud máte problémy, zkuste nastavit oprávnění `777` (dočasně pro test)

### 4. Vyčistit cache

```bash
# Přes SSH:
php artisan config:clear
php artisan cache:clear

# Nebo smazat ručně přes FTP:
- storage/framework/cache/*
- bootstrap/cache/config.php
```

### 5. Stáhnout existující faktury (volitelné, ale doporučené)

Pro stažení všech existujících faktur, které ještě nemají PDF uložené lokálně:

```bash
php artisan invoices:download-missing
```

**Parametry:**

- `--limit=50` - Maximální počet faktur k stažení (výchozí 50)
- `--force` - Znovu stáhnout i faktury, které už mají PDF

**Příklad:**

```bash
# Stáhnout prvních 50 faktur bez PDF
php artisan invoices:download-missing

# Stáhnout všech 100 faktur
php artisan invoices:download-missing --limit=100

# Znovu stáhnout všechny faktury (přepsat existující)
php artisan invoices:download-missing --force --limit=100
```

### 6. Test funkčnosti

1. Přihlaste se jako uživatel s předplatným
2. Jděte na `/billing/manage`
3. Klikněte na **"Stáhnout PDF"** u nějaké faktury
4. Faktura by se měla stáhnout

**Zkontrolujte logy:**

```bash
tail -f storage/logs/laravel.log
```

Měli byste vidět:

```
Invoice PDF served from local storage
```

Nebo při prvním stažení:

```
Invoice PDF downloaded from Fakturoid API and cached locally
```

---

## 📁 Struktura souborů po deploymetu

```
syncmyday/
├── config/
│   └── filesystems.php          ← NOVÝ soubor
├── storage/
│   ├── app/
│   │   ├── invoices/            ← NOVÝ adresář (právo zápisu!)
│   │   │   ├── invoice-SMD-2025-001.pdf
│   │   │   ├── invoice-SMD-2025-002.pdf
│   │   │   └── ...
│   │   └── .gitignore
│   └── logs/
│       └── laravel.log
```

---

## 🔍 Řešení problémů

### Chyba: "Disk [] does not have a configured driver"

- **Příčina:** Chybí `config/filesystems.php`
- **Řešení:** Nahrajte soubor `config/filesystems.php` na server
- **Pak:** Spusťte `php artisan config:clear`

### Chyba: "Unable to write to file"

- **Příčina:** Webserver nemá právo zapisovat do `storage/app/invoices/`
- **Řešení:** Nastavte oprávnění `chmod 775 storage/app/invoices`

### Faktury se nestahují, ale žádná chyba

- **Zkontrolujte:** `tail -f storage/logs/laravel.log`
- **Hledejte:** "Exception downloading and storing invoice PDF"
- **Možné příčiny:**
  - Fakturoid API je nedostupné (zkuste později)
  - Faktura nemá `fakturoid_id` v databázi

### Staré faktury nemají PDF

- **Je to OK!** Staré faktury se stáhnou automaticky při prvním kliknutí
- Hybridní systém použije Fakturoid API jako fallback

---

## 🧹 Údržba

### Smazání starých PDF (volitelné)

Pokud chcete šetřit místo na disku:

```bash
# Smazat PDF faktury starší než 7 let (zákonná lhůta)
find storage/app/invoices/ -name "*.pdf" -mtime +2555 -delete
```

### Záloha PDF faktur

Doporučuji zálohovat adresář `storage/app/invoices/` společně s databází.

```bash
# Přidat do backup skriptu:
tar -czf invoices-backup-$(date +%Y%m%d).tar.gz storage/app/invoices/
```

---

## 📊 Velikost souborů

- 1 PDF faktura: ~50-200 KB
- 100 faktur: ~5-20 MB
- 1000 faktur: ~50-200 MB

**Závěr:** Ukládání PDF faktur lokálně zabere málo místa.

---

## 🔄 Automatické stahování pro staré faktury

Pokud máte existující faktury z minulosti, můžete je stáhnout automaticky:

```bash
# Stáhnout PDF pro všechny faktury bez lokální kopie
php artisan invoices:download-missing --limit=100
```

To zajistí, že i staré faktury budou rychle dostupné bez čekání na Fakturoid API.

---

## ✅ Checklist

- [ ] Nahrán `config/filesystems.php` na server
- [ ] Nahrán `app/Console/Commands/DownloadMissingInvoicePdfs.php` na server
- [ ] Vytvořen adresář `storage/app/` na serveru
- [ ] Vytvořen adresář `storage/app/invoices/` na serveru
- [ ] Nastavena oprávnění (775 nebo 777)
- [ ] Vyčištěna cache (`php artisan config:clear`)
- [ ] Spuštěn command pro stažení starých faktur (volitelné)
- [ ] Otestováno stažení faktury
- [ ] Zkontrolovány logy (žádné chyby)

---

**Hotovo! Systém by měl nyní fungovat.** 🎉
