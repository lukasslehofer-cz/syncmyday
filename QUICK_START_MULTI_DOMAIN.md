# 🚀 Quick Start: Multi-Domain Setup

## Co jsem opravil

Problém byl, že aplikace používala `url()` helper, který generoval OAuth callback URL s **aktuální doménou** (např. `syncmyday.eu`), ale Google/Microsoft mají registrované jen `syncmyday.cz`.

**Řešení**: OAuth redirect URI je nyní **hardcoded na primární doménu** v konfiguraci.

## ✅ Co musíš udělat

### 1. Aktualizovat `.env` soubor

Přidej tyto dva řádky do `/Users/lukas/SyncMyDay/.env`:

```env
# Primární OAuth doména
OAUTH_PRIMARY_DOMAIN=syncmyday.cz

# Mapování domén na jazyky (JSON formát)
DOMAIN_LOCALES='{"syncmyday.cz":"cs","syncmyday.sk":"sk","syncmyday.pl":"pl","syncmyday.eu":"en"}'
```

**Volitelné**: Můžeš odstranit tyto staré řádky (už se nepoužívají):
```env
GOOGLE_REDIRECT_URI="${APP_URL}/oauth/google/callback"
MICROSOFT_REDIRECT_URI="${APP_URL}/oauth/microsoft/callback"
```

### 2. Restartovat aplikaci

```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Ověřit OAuth nastavení

Zkontroluj, že v **Google Cloud Console** a **Microsoft Azure** máš tyto Redirect URIs:

**Google:**
- `https://syncmyday.cz/oauth/google/callback` (pro připojení kalendářů)
- `https://syncmyday.cz/auth/google/callback` (pro login/registraci)

**Microsoft:**
- `https://syncmyday.cz/oauth/microsoft/callback` (pro připojení kalendářů)
- `https://syncmyday.cz/auth/microsoft/callback` (pro login/registraci)

✅ **Už máš to správně nastavené!**

## 🎉 Hotovo!

Nyní:
- ✅ Uživatelé můžou navštívit **libovolnou doménu** (`.cz`, `.sk`, `.pl`, `.eu`)
- ✅ Uvidí web ve **správném jazyce** podle domény
- ✅ OAuth login funguje ze **všech domén**
- ✅ Google/Microsoft callback vždy přijde na `syncmyday.cz` (jak je v konzolích nakonfigurované)

## 🧪 Test

1. Navštiv `https://syncmyday.eu` (anglická verze)
2. Klikni "Continue with Google"
3. Po přihlášení by už **neměla** být chyba 400 redirect_uri_mismatch

## 📚 Přidání další země

Když budeš chtít přidat další zemi (např. `.de`):

1. **Nastav DNS** - doména musí ukazovat na server
2. **Přidej do `.env`**:
   ```env
   DOMAIN_LOCALES='{"syncmyday.cz":"cs","syncmyday.sk":"sk","syncmyday.pl":"pl","syncmyday.eu":"en","syncmyday.de":"de"}'
   ```
3. **Vytvoř jazykový soubor**: `lang/de/messages.php`
4. **Přidej do Nginx** konfigurace: `server_name ... syncmyday.de;`
5. **SSL certifikát**: `certbot ... -d syncmyday.de`
6. **Restart**: `php artisan config:clear`

**NEPOTŘEBUJEŠ** měnit nic v Google/Microsoft OAuth konzolích! ✅

## 🔍 Jak to funguje

```
┌─────────────────────────────────────────────────────────────────┐
│ User visits syncmyday.eu → "Continue with Google"              │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ App redirects to Google with:                                   │
│ redirect_uri=https://syncmyday.cz/auth/google/callback         │
│ (hardcoded in config/services.php)                             │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ Google authenticates user                                       │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ Google redirects to: https://syncmyday.cz/auth/google/callback │
│ (matches what's in Google Console ✅)                           │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ User logged in on syncmyday.cz                                  │
│ Locale set based on preference (can redirect back to .eu)      │
└─────────────────────────────────────────────────────────────────┘
```

## 📝 Soubory které byly změněny

- ✅ `config/services.php` - OAuth redirect URIs hardcoded na primární doménu
- ✅ `config/app.php` - přidána `oauth_primary_domain` konfigurace
- ✅ `app/Http/Controllers/Auth/SocialAuthController.php` - použití nové konfigurace
- ✅ `app/Http/Middleware/OAuthProxyRedirect.php` - fallback middleware (safety net)
- ✅ `MULTI_DOMAIN_SETUP.md` - aktualizovaná dokumentace

## 🆘 Pokud to pořád nefunguje

1. **Zkontroluj `.env`**:
   ```bash
   grep OAUTH_PRIMARY_DOMAIN .env
   grep DOMAIN_LOCALES .env
   ```

2. **Clear config znovu**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Zkontroluj logy**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Test config**:
   ```bash
   php artisan tinker
   >>> config('app.oauth_primary_domain')
   => "syncmyday.cz"
   >>> config('services.google.redirect_login')
   => "https://syncmyday.cz/auth/google/callback"
   ```

