# Shared Hosting Setup (Czech Hosting)

Tento návod je pro sdílený hosting **BEZ** možnosti instalace Supervisoru.

## ✅ Co bylo upraveno

Všechny emaily se nyní posílají **synchronně** (přímo), není potřeba Queue Worker ani Supervisor.

---

## 🚀 Deployment na sdílený hosting

### Krok 1: Nahrát kód přes Git

```bash
# Lokálně
git add .
git commit -m "Production ready with sync emails"
git push origin main

# Na serveru (přes SSH nebo Git deploy)
cd /path/to/syncmyday
git pull origin main
```

### Krok 2: Nastavit SMTP v .env na serveru

V hosting panelu nebo přes FTP upravte soubor `.env`:

```env
# Email Configuration - Czech Hosting
MAIL_MAILER=smtp
MAIL_HOST=smtp.cesky-hosting.cz
MAIL_PORT=625
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD=Login2025-
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@syncmyday.cz"
MAIL_FROM_NAME="${APP_NAME}"
```

### Krok 3: Spustit migrace (pokud je SSH přístup)

```bash
php artisan migrate --force
```

**Pokud NENÍ SSH přístup:**

- Spusťte migrace lokálně
- Exportujte databázi
- Importujte na produkci přes phpMyAdmin

### Krok 4: Vyčistit cache (pokud je SSH)

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

**Pokud NENÍ SSH přístup:**

- Smažte složku `bootstrap/cache/*.php` přes FTP
- Aplikace si cache vytvoří automaticky

### Krok 5: Nastavit CRON JOB přes hosting panel

**🎯 TŘI MOŽNOSTI - vyberte podle vašeho hostingu:**

---

#### ✅ **Možnost 1: PHP soubor (DOPORUČENO)**

Použijte připravený soubor `cron.php` v rootu projektu.

1. Přihlaste se do cPanel
2. Najděte sekci **"Cron Jobs"**
3. Přidejte:

**Common Settings:** Once Per Minute

**Command:**

```bash
/usr/bin/php /home/username/public_html/syncmyday/cron.php
```

**Poznámky:**

- Upravte cestu podle vašeho hostingu
- Cesta k PHP může být `/usr/bin/php80`, `/usr/bin/php82` apod.

---

#### ✅ **Možnost 2: HTTP endpoint**

Pokud Možnost 1 nefunguje.

**Krok A:** V `.env` přidejte:

```env
CRON_SECRET=vas-nahodny-tajny-token
```

Vygenerujte token:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

**Krok B:** V cPanel Cron Jobs přidejte:

```bash
curl -s "https://syncmyday.cz/cron/run?token=vas-token" > /dev/null 2>&1
```

---

#### ✅ **Možnost 3: Externí služba**

Pokud hosting vůbec nemá cron jobs.

1. Registrujte se na **cron-job.org**
2. Vytvořte cron job:
   - URL: `https://syncmyday.cz/cron/run?token=vas-token`
   - Interval: Every minute
3. Hotovo!

---

> 📚 **Podrobné návody:**
>
> - `CRON_SETUP_OPTIONS.md` - všechny možnosti detailně
> - `CPANEL_CRON_SETUP.md` - krok za krokem s obrázky

---

## 📧 Jak to funguje BEZ Queue Workeru

### Registrace uživatele:

1. Uživatel se zaregistruje
2. Email verifikace se pošle **okamžitě** (synchronně)
3. Po kliknutí na link se pošle Welcome email **okamžitě**

### Platba:

1. Uživatel zaplatí přes Stripe
2. Stripe webhook volá váš server
3. Payment Success email se pošle **okamžitě**

### Trial ending emails:

1. **Každý den v 9:00** se spustí cron job
2. Command `trial:send-ending-notifications` zkontroluje uživatele
3. Emaily se pošlou **okamžitě** (ne přes queue)

**Výhody:**

- ✅ Žádný Supervisor není potřeba
- ✅ Funguje na sdíleném hostingu
- ✅ Jednoduché nasazení

**Nevýhody:**

- ⚠️ Email se posílá během HTTP requestu (může trvat 1-2 sekundy)
- ⚠️ Pokud SMTP selže, uživatel může vidět chybu

---

## 🧪 Testování

### Test email systému:

Pokud máte SSH přístup:

```bash
php artisan email:test info@syncmyday.cz --type=payment
```

Pokud NEMÁTE SSH:

- Zaregistrujte testovacího uživatele
- Email by měl dorazit automaticky

---

## 📋 Checklist pro deployment

- [ ] Kód nahrán na server (Git nebo FTP)
- [ ] `.env` nakonfigurován s Czech Hosting SMTP
- [ ] Migrace spuštěny (nebo databáze importována)
- [ ] Cache vyčištěna
- [ ] **Cron job nastaven v cPanel** (nejdůležitější!)
- [ ] Test email odeslán a doručen
- [ ] Oprávnění na `storage/` a `bootstrap/cache/` (777 nebo 755)

---

## 🔧 Troubleshooting

### Email se neposílá

**1. Zkontrolujte SMTP credentials v .env**

Ujistěte se, že jsou správně:

```env
MAIL_HOST=smtp.cesky-hosting.cz
MAIL_PORT=625
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD=Login2025-
MAIL_ENCRYPTION=tls
```

**2. Zkontrolujte logy**

Pokud je SSH:

```bash
tail -f storage/logs/laravel.log
```

Pokud není SSH:

- Stáhněte soubor `storage/logs/laravel.log` přes FTP
- Otevřete a hledejte chyby

**3. Test SMTP připojení**

Kontaktujte Czech Hosting support a ověřte:

- Je SMTP povoleno z IP vašeho serveru?
- Je port 625 otevřený?
- Jsou credentials správné?

### Cron job neběží

**1. Zkontrolujte cestu k PHP**

V SSH:

```bash
which php
# Výstup např: /usr/bin/php80
```

Použijte tuto cestu v cron jobu.

**2. Zkontrolujte cestu k projektu**

Ujistěte se, že cesta v cron jobu je správná:

```bash
cd /home/username/public_html/syncmyday
```

**3. Test cron jobu manuálně**

V SSH spusťte:

```bash
cd /path/to/syncmyday && php artisan schedule:run
```

Měli byste vidět output, jestli nějaké tasky běží.

**4. Zkontrolujte cron logy**

Většina hostingů ukládá cron logy. V cPanel:

- Advanced → Cron Jobs → View Cron Job History

### Oprávnění

Pokud vidíte chybu "Permission denied":

```bash
chmod -R 755 storage bootstrap/cache
```

Nebo přes FTP nastavte oprávnění:

- `storage/` → 755 nebo 777
- `bootstrap/cache/` → 755 nebo 777

---

## 💡 Tipy pro Czech Hosting

### 1. Umístění projektu

Většinou:

```
/home/username/public_html/
```

Nebo pokud máte více domén:

```
/home/username/domains/syncmyday.cz/public_html/
```

### 2. PHP verze

Ujistěte se, že používáte **PHP 8.2** nebo vyšší:

- V cPanel → MultiPHP Manager

### 3. Document Root

Nastavte document root na:

```
/path/to/syncmyday/public
```

Ne na kořenovou složku projektu!

### 4. .htaccess

Laravel už má připravený `.htaccess` v `public/` složce. Měl by fungovat automaticky.

---

## 📊 Scheduled Tasks (Cron)

Tyto tasky běží automaticky přes cron:

| Task                              | Kdy běží        | Co dělá                              |
| --------------------------------- | --------------- | ------------------------------------ |
| `calendars:sync`                  | Každých 5 minut | Synchronizuje kalendáře              |
| `webhooks:renew`                  | Každých 6 hodin | Obnovuje webhook subscriptions       |
| `logs:clean`                      | Denně           | Čistí staré logy                     |
| `connections:check`               | Každou hodinu   | Kontroluje připojení                 |
| `trial:send-ending-notifications` | Denně v 9:00    | Posílá upozornění před koncem trialu |

Všechny tyto tasky jsou definovány v `app/Console/Kernel.php` a spouští se automaticky přes jeden cron job.

---

## ✅ Hotovo!

Nyní můžete vše nahrát přes Git a aplikace bude fungovat i bez Supervisoru!

Emaily se budou posílat z `info@syncmyday.cz` synchronně (okamžitě).

Trial ending emaily se budou posílat automaticky každý den v 9:00 přes cron job.
