# Kontrola logů na produkci

Návod, jak zkontrolovat, jestli cron běží a emaily se posílají.

---

## 📋 Kde najít logy

### 1. Laravel logy

**Umístění:**

```
storage/logs/laravel.log
```

**Jak se podívat:**

#### A) Přes SSH:

```bash
# Poslední řádky
tail -100 storage/logs/laravel.log

# Sledovat logy v reálném čase
tail -f storage/logs/laravel.log

# Hledat chyby
grep "ERROR" storage/logs/laravel.log

# Hledat email logy
grep "trial:send-ending" storage/logs/laravel.log
```

#### B) Přes FTP/cPanel:

1. Přihlaste se do cPanel
2. File Manager
3. Najděte `storage/logs/laravel.log`
4. Klikněte pravým → Edit nebo Download
5. Hledejte od konce souboru

---

## 🔍 Co hledat v lozích

### Úspěšné spuštění cronu:

```
[2025-10-09 19:00:00] Schedule:run executed with status: 0
```

Pokud tohle vidíte, cron běží! ✅

### Běh trial notifications:

```
Checking for users with trial ending soon...
Found 2 users with trial ending in 3 days
✓ Sent 3-day notification to: user@example.com
```

### Chyby při posílání emailů:

```
✗ Failed to send to user@example.com: Connection refused
```

Nebo:

```
ERROR: Swift_TransportException: Connection could not be established with host smtp.cesky-hosting.cz
```

---

## 🧪 Testování na produkci

### Test 1: Zkontrolujte, jestli cron běží

**Přes SSH:**

```bash
cd /path/to/syncmyday
php artisan schedule:run
```

**Výstup by měl obsahovat:**

```
No scheduled commands are ready to run.
```

Nebo pokud je 9:00, uvidíte:

```
Running scheduled command: php artisan trial:send-ending-notifications
```

---

### Test 2: Spusťte trial notification ručně

```bash
php artisan trial:send-ending-notifications
```

**Výstup:**

```
Checking for users with trial ending soon...
Found 0 users with trial ending in 3 days
Found 0 users with trial ending in 1 day
Finished! Total notifications sent: 0
```

Pokud to vidíte, command funguje! ✅

---

### Test 3: Odešlete testovací email

```bash
php artisan email:test info@syncmyday.cz --type=trial-7
```

**Výstup:**

```
Sending trial-7 email to: info@syncmyday.cz
✓ Email sent successfully!
```

Pokud email dorazí, SMTP funguje! ✅

---

## 🐛 Časté chyby a řešení

### Chyba: "Route [help_center] not defined"

**Řešení:** Starý cache. Vymažte:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

Nebo přes FTP smažte:

```
bootstrap/cache/*.php
storage/framework/views/*.php
```

---

### Chyba: "Connection refused" při posílání emailů

**Příčina:** SMTP není správně nakonfigurované.

**Kontrola:**

1. Otevřete `.env`
2. Zkontrolujte:

```env
MAIL_HOST=smtp.cesky-hosting.cz
MAIL_PORT=625
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD=Login2025-
MAIL_ENCRYPTION=tls
```

3. Zkuste ping SMTP serveru:

```bash
telnet smtp.cesky-hosting.cz 625
```

Pokud connection refused → kontaktujte Czech Hosting support.

---

### Chyba: Schedule se nespouští

**Příčina:** Cron job není správně nastavený.

**Kontrola:**

1. **V cPanel → Cron Jobs**
2. Zkontrolujte, jestli máte:

```bash
/usr/bin/php /home/username/public_html/syncmyday/public/cron-email.php
```

3. **Common Settings:** Once Per Minute

4. **Test:** Počkejte 2 minuty a zkontrolujte `storage/logs/laravel.log`

Měli byste vidět:

```
[2025-10-09 19:01:00] Schedule:run executed with status: 0
[2025-10-09 19:02:00] Schedule:run executed with status: 0
```

---

## 📊 Kontrolní checklist

Po nasazení na produkci zkontrolujte:

- [ ] `git pull origin main` proběhlo úspěšně
- [ ] `.env` má správné SMTP credentials
- [ ] `storage/logs/laravel.log` existuje a je zapisovatelný
- [ ] `storage/` má oprávnění 755 nebo 777
- [ ] Cron job je nastavený v cPanel
- [ ] Po 2 minutách vidíte v `laravel.log` záznamy o `Schedule:run`
- [ ] Testovací email `php artisan email:test` dorazí
- [ ] `php artisan trial:send-ending-notifications` běží bez chyb

---

## 🎯 Shrnutí - jak ověřit, že vše funguje

### Pokud MÁTE SSH:

```bash
# 1. Aktualizujte kód
cd /path/to/syncmyday
git pull origin main

# 2. Vymažte cache
php artisan config:clear
php artisan cache:clear

# 3. Test cron
php public/cron-email.php

# 4. Test email
php artisan email:test info@syncmyday.cz --type=payment

# 5. Zkontrolujte logy
tail -50 storage/logs/laravel.log
```

### Pokud NEMÁTE SSH:

1. **Git pull** přes hosting panel (pokud je Git deploy)
2. **Smažte cache** přes FTP: `bootstrap/cache/*.php`
3. **Počkejte 2 minuty** (cron by měl běžet)
4. **Stáhněte** `storage/logs/laravel.log` přes FTP
5. **Hledejte** záznamy o `Schedule:run`

---

## 💡 Tip: Real-time log monitoring

Pokud máte SSH, můžete sledovat logy v reálném čase:

```bash
tail -f storage/logs/laravel.log
```

Pak v druhém terminálu spusťte:

```bash
php artisan email:test test@example.com
```

V prvním terminálu uvidíte všechny logy v reálném čase!

---

**Máte otázky nebo vidíte chyby v lozích? Pošlete mi snippet z `laravel.log`!** 🚀
