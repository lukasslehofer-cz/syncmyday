# ✅ Instalace Dokončena!

SyncMyDay byl úspěšně nainstalován pro MAMP prostředí.

## 📊 Status

✅ Composer dependencies nainstalovány  
✅ Laravel APP_KEY vygenerován  
✅ Token Encryption Key vygenerován  
✅ Databáze `syncmyday` vytvořena  
✅ Všechny migrace provedeny (8 tabulek)  
✅ Konfigurace cachována

## 🚀 Přístup k Aplikaci

**URL MAMP:** http://localhost:8080  
**URL PHP Server:** http://localhost:8081

Aplikace běží na **OBOU** portech!

### Hlavní Stránky:

- 🏠 Domovská stránka: http://localhost:8080
- 📝 Registrace: http://localhost:8080/register
- 🔐 Přihlášení: http://localhost:8080/login

## ⚙️ Konfigurace OAuth (DŮLEŽITÉ!)

Před použitím kalendářové synchronizace musíte nakonfigurovat OAuth credentials:

### Google Calendar API

1. Jděte na https://console.cloud.google.com/
2. Vytvořte nový projekt
3. Aktivujte Google Calendar API
4. Vytvořte OAuth 2.0 credentials
5. Přidejte redirect URI: `http://localhost:8080/oauth/google/callback`
6. Zkopírujte Client ID a Secret do `.env`:
   ```
   GOOGLE_CLIENT_ID=váš-client-id
   GOOGLE_CLIENT_SECRET=váš-client-secret
   ```

### Microsoft Graph API

1. Jděte na https://portal.azure.com/
2. Vytvořte App Registration
3. Přidejte redirect URI: `http://localhost:8080/oauth/microsoft/callback`
4. Přidejte API permissions: `Calendars.ReadWrite`, `offline_access`
5. Vytvořte Client Secret
6. Zkopírujte do `.env`:
   ```
   MICROSOFT_CLIENT_ID=váš-client-id
   MICROSOFT_CLIENT_SECRET=váš-client-secret
   ```

## 🔧 Testování Webhooků (Volitelné)

Pro webhooky potřebujete veřejnou URL. Použijte ngrok:

```bash
ngrok http 8080
```

Poté aktualizujte v `.env`:

```
APP_URL=https://your-ngrok-url.ngrok.io
```

A OAuth redirect URIs v Google/Microsoft konzolích.

## 🎯 Spuštění Fronty

Pro zpracování synchronizačních úloh spusťte worker:

```bash
cd /Users/lukas/SyncMyDay
php artisan queue:work --sleep=3 --tries=3
```

**Tip:** Nechte běžet v samostatném terminálu.

## 🧪 První Kroky

1. **Zaregistrujte se** na http://localhost:8080/register
2. **Připojte kalendáře** (Google/Microsoft)
3. **Vytvořte sync pravidlo**
4. **Otestujte** - vytvořte událost v jednom kalendáři a sledujte blocker v druhém

## 📁 Struktura Databáze

Vytvořené tabulky:

- `users` - Uživatelské účty
- `calendar_connections` - OAuth připojení (tokeny šifrované)
- `sync_rules` - Pravidla synchronizace
- `sync_rule_targets` - Cílové kalendáře pro pravidla
- `webhook_subscriptions` - Aktivní webhook odběry
- `sync_logs` - Historie synchronizací
- `sessions` - Uživatelské relace
- `jobs` - Fronta úloh

## 🔐 Bezpečnost

✅ OAuth tokeny šifrované pomocí Sodium  
✅ Minimální data - žádné názvy událostí  
✅ CSRF ochrana aktivní  
✅ XSS ochrana v šablonách  
✅ Bezpečné headers v Nginx

## 🆘 Řešení Problémů

### Chyba 500

```bash
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
```

### Nedá se připojit k DB

Zkontrolujte, že MAMP MySQL běží na portu 8889.

### OAuth nefunguje

1. Zkontrolujte redirect URIs v OAuth konzolích
2. Ověřte že `APP_URL` je správně nastaveno
3. Ujistěte se, že máte správné Client ID/Secret v `.env`

## 📞 Podpora

Dokumentace: `/Users/lukas/SyncMyDay/README.md`  
Deployment: `/Users/lukas/SyncMyDay/DEPLOYMENT.md`

---

**Databázové přihlášení:**

- Host: 127.0.0.1
- Port: 8889
- Database: syncmyday
- Username: root
- Password: root

**Token Encryption Key (uložen v .env):**

```
TOKEN_ENCRYPTION_KEY=base64:MwEuCwObIrFHyHPR0SBR0qqN0XiCbMn9SjxZruZ8CV8=
```

---

✨ **Vše je připraveno k použití!** ✨
