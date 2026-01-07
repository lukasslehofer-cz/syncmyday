# Troubleshooting Guide

Common issues and their solutions for SyncMyDay.

## 🔐 OAuth Issues

### "Přístup zablokován: aplikace neprošla ověřením Googlem"

**Příčina:** Aplikace je v Testing módu a uživatel není v seznamu Test users.

**Řešení:**

1. Google Cloud Console → APIs & Services → OAuth consent screen
2. Sekce "Test users" → ADD USERS
3. Přidejte emailové adresy Google účtů, se kterými chcete testovat
4. SAVE
5. Zkuste OAuth flow znovu

---

### "Error 401: invalid_client"

**Příčina:** Client ID nebo Secret není správně nakonfigurován.

**Řešení:**

1. Zkontrolujte `.env` soubor
2. Ujistěte se, že hodnoty neobsahují extra mezery nebo uvozovky:
   ```env
   GOOGLE_CLIENT_ID=123456.apps.googleusercontent.com
   GOOGLE_CLIENT_SECRET=GOCSPX-abc123
   ```
3. Vyčistěte cache: `php artisan config:clear`
4. Restartujte server

---

### "redirect_uri_mismatch"

**Příčina:** Redirect URI v aplikaci neodpovídá URI v OAuth credentials.

**Řešení:**

1. Google Cloud Console → Credentials → Váš OAuth Client
2. Authorized redirect URIs MUSÍ obsahovat přesně:
   ```
   http://localhost:8080/oauth/google/callback
   ```
   (včetně portu a bez trailing slash)
3. Zkontrolujte APP_URL v `.env`:
   ```env
   APP_URL=http://localhost:8080
   ```

---

## 🗄️ Database Issues

### "SQLSTATE[HY000] [2002] Connection refused"

**Příčina:** MySQL není spuštěný nebo běží na jiném portu.

**Řešení:**

1. Ověřte že MAMP MySQL běží
2. Zkontrolujte port v `.env`:
   ```env
   DB_HOST=127.0.0.1
   DB_PORT=8889
   ```
3. Test připojení:
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

---

## 🖥️ Server Issues

### "View [xyz] not found"

**Příčina:** View soubor neexistuje nebo cache je zastaralá.

**Řešení:**

1. Vyčistěte view cache:
   ```bash
   php artisan view:clear
   ```
2. Ověřte že soubor existuje v `resources/views/`
3. Zkontrolujte správnou strukturu adresářů

---

### "Target class [Controller] does not exist"

**Příčina:** Chybí základní Controller třída.

**Řešení:**

1. Ujistěte se že existuje `app/Http/Controllers/Controller.php`
2. Vyčistěte cache:
   ```bash
   php artisan config:clear
   php artisan route:clear
   ```

---

## 🔄 Sync Issues

### "Webhook not receiving notifications"

**Příčina:** Pro webhooky potřebujete veřejnou URL (ne localhost).

**Řešení pro testování:**

1. Nainstalujte ngrok: https://ngrok.com/
2. Spusťte:
   ```bash
   ngrok http 8080
   ```
3. Zkopírujte ngrok URL (např. `https://abc123.ngrok.io`)
4. Aktualizujte `.env`:
   ```env
   APP_URL=https://abc123.ngrok.io
   WEBHOOK_BASE_URL=https://abc123.ngrok.io/webhooks
   ```
5. Aktualizujte redirect URIs v Google/Microsoft OAuth console
6. Vyčistěte cache: `php artisan config:clear`

---

### "Token expired" při synchronizaci

**Příčina:** Access token vypršel a refresh token nefunguje.

**Řešení:**

1. Odpojte a znovu připojte kalendář
2. Ověřte že OAuth má scope `offline_access` (Microsoft) nebo `access_type=offline` (Google)
3. Zkontrolujte v databázi že `refresh_token_encrypted` není NULL

---

## 🎨 Frontend Issues

### Tailwind CSS se nenačítá

**Příčina:** Pro MVP používáme CDN, ujistěte se že máte připojení k internetu.

**Řešení:**

1. Zkontrolujte v HTML hlavičce:
   ```html
   <script src="https://cdn.tailwindcss.com"></script>
   ```
2. Ověřte připojení k internetu
3. Pro produkci zvažte lokální build

---

## 🔑 Encryption Issues

### "TOKEN_ENCRYPTION_KEY not configured"

**Příčina:** Chybí encryption key pro tokeny.

**Řešení:**

1. Vygenerujte nový klíč:
   ```bash
   php -r "echo 'TOKEN_ENCRYPTION_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
   ```
2. Přidejte do `.env`
3. Vyčistěte cache: `php artisan config:clear`

**VAROVÁNÍ:** Změna klíče po uložení tokenů způsobí, že existující tokeny nelze dešifrovat!

---

## 📊 Queue Issues

### Jobs se nezpracovávají

**Příčina:** Queue worker neběží.

**Řešení:**

1. Spusťte worker:
   ```bash
   php artisan queue:work --sleep=3 --tries=3
   ```
2. Pro produkci použijte supervisor nebo systemd
3. Zkontrolujte failed jobs:
   ```bash
   php artisan queue:failed
   php artisan queue:retry all
   ```

---

## 🛠️ General Tips

### Vyčištění všech cache

Když nic jiného nefunguje:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

### Debug mode

Pro zobrazení detailních chyb:

```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Kontrola logů

```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/sync.log
tail -f storage/logs/webhook.log
```

---

## 📞 Stále problém?

1. Zkontrolujte README.md pro detailní návod
2. Zkontrolujte OAUTH_SETUP.md pro OAuth konfiguraci
3. Zkontrolujte logy v `storage/logs/`
4. Ověřte že všechny dependencies jsou nainstalovány: `composer install`
