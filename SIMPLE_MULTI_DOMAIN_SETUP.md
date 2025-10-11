# 🌍 Jednoduchý Multi-Domain Setup

## Přehled

**Jednoduché řešení**: Přidej všechny své domény do Google a Microsoft OAuth konzolí. Pro 5-10 domén je to nejjednodušší a nejspolehlivější přístup.

## ✅ Kroky k nastavení

### 1. Přidej domény do Google Cloud Console

Jdi do [Google Cloud Console](https://console.cloud.google.com/) → Credentials → OAuth 2.0 Client IDs

**Přidej tyto Authorized redirect URIs**:

```
# Pro login/registraci:
https://syncmyday.cz/auth/google/callback
https://syncmyday.sk/auth/google/callback
https://syncmyday.pl/auth/google/callback
https://syncmyday.eu/auth/google/callback
https://syncmyday.de/auth/google/callback

# Pro připojení kalendářů (authenticated users):
https://syncmyday.cz/oauth/google/callback
https://syncmyday.sk/oauth/google/callback
https://syncmyday.pl/oauth/google/callback
https://syncmyday.eu/oauth/google/callback
https://syncmyday.de/oauth/google/callback
```

### 2. Přidej domény do Microsoft Azure Portal

Jdi do [Azure Portal](https://portal.azure.com/) → App registrations → Authentication → Redirect URIs

**Přidej tyto Redirect URIs**:

```
# Pro login/registraci:
https://syncmyday.cz/auth/microsoft/callback
https://syncmyday.sk/auth/microsoft/callback
https://syncmyday.pl/auth/microsoft/callback
https://syncmyday.eu/auth/microsoft/callback
https://syncmyday.de/auth/microsoft/callback

# Pro připojení kalendářů:
https://syncmyday.cz/oauth/microsoft/callback
https://syncmyday.sk/oauth/microsoft/callback
https://syncmyday.pl/oauth/microsoft/callback
https://syncmyday.eu/oauth/microsoft/callback
https://syncmyday.de/oauth/microsoft/callback
```

### 3. Nastav `.env` soubor

```env
# Domain to Locale Mapping (JSON formát)
DOMAIN_LOCALES='{"syncmyday.cz":"cs","syncmyday.sk":"sk","syncmyday.pl":"pl","syncmyday.eu":"en","syncmyday.de":"de"}'

# OAuth credentials (stejné pro všechny domény)
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

MICROSOFT_CLIENT_ID=your_microsoft_client_id
MICROSOFT_CLIENT_SECRET=your_microsoft_client_secret
MICROSOFT_TENANT=common
```

**Poznámka**: Už NEPOTŘEBUJEŠ `OAUTH_PRIMARY_DOMAIN` - každá doména si spravuje své vlastní OAuth callbacky.

### 4. Vytvoř jazykové soubory

Pro každý jazyk vytvoř soubor `lang/{locale}/messages.php`:

```bash
# Slovenština
cp lang/cs/messages.php lang/sk/messages.php

# Polština
cp lang/cs/messages.php lang/pl/messages.php

# Němčina
cp lang/cs/messages.php lang/de/messages.php
```

Pak přelož texty v každém souboru.

### 5. Nastav Nginx

Ujisti se, že Nginx má všechny domény v `server_name`:

```nginx
server {
    listen 443 ssl http2;
    server_name syncmyday.cz syncmyday.sk syncmyday.pl syncmyday.eu syncmyday.de;
    
    # ... zbytek konfigurace
}
```

### 6. SSL certifikáty

Vygeneruj SSL certifikáty pro všechny domény:

```bash
certbot certonly --nginx \
  -d syncmyday.cz \
  -d syncmyday.sk \
  -d syncmyday.pl \
  -d syncmyday.eu \
  -d syncmyday.de
```

### 7. Restartuj aplikaci

```bash
php artisan config:clear
php artisan cache:clear
```

## 🎉 Hotovo!

Nyní:
- ✅ Uživatel navštíví **libovolnou doménu** (např. `syncmyday.sk`)
- ✅ Uvidí web ve **slovenštině** (podle DOMAIN_LOCALES)
- ✅ Klikne "Continue with Google"
- ✅ Google redirect na `https://syncmyday.sk/auth/google/callback` ✅
- ✅ Uživatel přihlášen **přímo na slovenské doméně**
- ✅ Session cookie je pro `.sk` doménu
- ✅ Vše funguje včetně logout, sync rules, atd.

## 🔄 Jak to funguje

```
┌─────────────────────────────────────────────────────────────────┐
│ User visits syncmyday.sk                                        │
│ → SetLocaleFromDomain middleware nastaví jazyk na "sk"         │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ User clicks "Continue with Google"                              │
│ → url('/auth/google/callback') = syncmyday.sk/auth/...         │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ Google OAuth with redirect_uri=https://syncmyday.sk/auth/...   │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ Google redirects to: https://syncmyday.sk/auth/google/callback │
│ (matches what's in Google Console ✅)                           │
└─────────────────────┬───────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────────┐
│ User logged in on syncmyday.sk with Slovak locale              │
│ Session cookie: .syncmyday.sk                                  │
│ All subsequent requests stay on .sk domain                     │
└─────────────────────────────────────────────────────────────────┘
```

## 📈 Přidání další země

Když budeš chtít přidat novou zemi:

1. **DNS**: Nastav A record pro novou doménu
2. **Google Console**: Přidej `https://nova-domena.com/auth/google/callback` a `/oauth/google/callback`
3. **Microsoft Azure**: Přidej `https://nova-domena.com/auth/microsoft/callback` a `/oauth/microsoft/callback`
4. **`.env`**: Uprav `DOMAIN_LOCALES` a přidej novou doménu
5. **Lang**: Vytvoř `lang/{locale}/messages.php`
6. **Nginx**: Přidej doménu do `server_name`
7. **SSL**: `certbot ... -d nova-domena.com`
8. **Restart**: `php artisan config:clear`

✅ **Celé to zabere 5-10 minut!**

## 🆚 Proč toto místo složitějšího řešení?

| Aspekt | Složité řešení (proxy redirect) | Jednoduché řešení (všechny domény) |
|--------|----------------------------------|-------------------------------------|
| **Session cookie** | ❌ Cross-domain problém | ✅ Cookie pro správnou doménu |
| **OAuth setup** | ✅ Jen jedna doména v konzolích | ⚠️ Všechny domény v konzolích |
| **Middleware** | ⚠️ Extra middleware nutný | ✅ Žádný extra middleware |
| **User experience** | ⚠️ Redirect mezi doménami | ✅ Zůstane na jedné doméně |
| **Debugging** | ❌ Složitější | ✅ Jednodušší |
| **Škálovatelnost** | ✅ Pro 100+ domén | ⚠️ Pro 5-10 domén ideální |

**Pro tvůj use case (5-10 národních domén) je jednoduché řešení LEPŠÍ! 🎯**

## 🔍 Testování

1. Navštiv `https://syncmyday.sk`
2. Měl by ses vidět slovenský web
3. Klikni "Continue with Google"
4. Po přihlášení by ses měl vrátit na `syncmyday.sk` (ne `.cz`)
5. Vytvořit sync rule
6. Logout
7. Login znovu - vše by mělo fungovat

## 🆘 Troubleshooting

### Problém: redirect_uri_mismatch
**Řešení**: Zkontroluj, že máš danou doménu přidanou v Google/Microsoft konzoli

### Problém: Po přihlášení se vrátím na login
**Řešení**: 
- Zkontroluj session cookie doménu
- Ujisti se, že `SESSION_DOMAIN` v `.env` není nastavena (nebo je `null`)

### Problém: Špatný jazyk po přihlášení
**Řešení**:
- Zkontroluj `DOMAIN_LOCALES` v `.env`
- Ujisti se, že je to validní JSON
- `php artisan config:clear`

## 📝 Soubory které byly změněny

- ✅ `config/services.php` - vráceno na `url()` helper
- ✅ `config/app.php` - odstraněna `oauth_primary_domain` konfigurace
- ✅ `app/Http/Controllers/Auth/SocialAuthController.php` - používá `url()` helper
- ✅ `app/Http/Kernel.php` - odstraněn `OAuthProxyRedirect` middleware
- ❌ `app/Http/Middleware/OAuthProxyRedirect.php` - SMAZÁNO (už nepotřebujeme)

## 📚 Související dokumentace

- `MULTI_DOMAIN_SETUP.md` - Původní složitější dokumentace (pro referenci)
- `README.md` - Hlavní dokumentace projektu
- `DEPLOYMENT.md` - Deployment instrukce

