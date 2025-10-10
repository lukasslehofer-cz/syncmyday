# OAuth Login - Quick Start Guide 🚀

Rychlý návod jak zprovoznit OAuth přihlašování přes Google a Microsoft.

## ✅ Co bylo implementováno

- ✅ OAuth přihlašování a registrace (Google + Microsoft)
- ✅ Automatické připojení kalendářů při prvním přihlášení
- ✅ OAuth tlačítka na login, register a homepage
- ✅ Ochrana proti duplicitním účtům
- ✅ 30denní trial pro nové uživatele

## 🏃 Rychlé zprovoznění (5 kroků)

### 1. Spustit migraci databáze

```bash
cd /Users/lukas/SyncMyDay
php artisan migrate
```

### 2. Přidat redirect URIs do Google Cloud Console

**URL:** https://console.cloud.google.com/

1. Vyberte projekt `SyncMyDay`
2. Jděte na: **APIs & Services → Credentials**
3. Klikněte na váš OAuth 2.0 Client ID
4. V sekci **Authorized redirect URIs** přidejte:

```
http://localhost:8080/auth/google/callback
http://localhost:8081/auth/google/callback
http://localhost:8080/oauth/google/callback
http://localhost:8081/oauth/google/callback
```

5. Klikněte **SAVE**

### 3. Přidat redirect URIs do Microsoft Azure Portal

**URL:** https://portal.azure.com/

1. Jděte na: **Microsoft Entra ID → App registrations**
2. Vyberte aplikaci `SyncMyDay`
3. Klikněte na: **Authentication**
4. V sekci **Platform configurations → Web** přidejte redirect URIs:

```
http://localhost:8080/auth/microsoft/callback
http://localhost:8081/auth/microsoft/callback
http://localhost:8080/oauth/microsoft/callback
http://localhost:8081/oauth/microsoft/callback
```

5. Klikněte **Save**

### 4. Spustit aplikaci

```bash
php artisan serve --port=8080
```

### 5. Otestovat OAuth login

1. Otevřete: http://localhost:8080
2. Klikněte **"Sign up with Google"** nebo **"Sign up with Microsoft"**
3. Autorizujte aplikaci
4. Měli byste být:
   - ✅ Automaticky registrováni/přihlášeni
   - ✅ Přesměrováni na dashboard
   - ✅ Vaše kalendáře automaticky připojeny

## 🎯 Kde najít OAuth tlačítka

### Homepage (Welcome stránka)

- http://localhost:8080/
- Hero sekce obsahuje 3 tlačítka:
  - "Sign up with Google"
  - "Sign up with Microsoft"
  - "Sign up with Email"

### Login stránka

- http://localhost:8080/login
- Tlačítka nahoře:
  - "Continue with Google"
  - "Continue with Microsoft"

### Register stránka

- http://localhost:8080/register
- Tlačítka nahoře:
  - "Sign up with Google"
  - "Sign up with Microsoft"

## 📂 Změněné/nové soubory

### Nové soubory:

- `database/migrations/2024_10_10_000001_add_oauth_fields_to_users_table.php`
- `app/Http/Controllers/Auth/SocialAuthController.php`
- `OAUTH_LOGIN_SETUP.md` (podrobná dokumentace)
- `OAUTH_LOGIN_CHANGES.md` (přehled změn)
- `OAUTH_LOGIN_QUICKSTART.md` (tento soubor)

### Upravené soubory:

- `app/Models/User.php` - přidány OAuth metody
- `routes/web.php` - přidány OAuth routy
- `app/Http/Controllers/Auth/AuthController.php` - detekce OAuth účtů
- `resources/views/auth/login.blade.php` - OAuth tlačítka
- `resources/views/auth/register.blade.php` - OAuth tlačítka
- `resources/views/welcome.blade.php` - OAuth tlačítka v hero sekci
- `config/services.php` - komentář o redirect URIs

## 🔄 Jak to funguje

### Pro nového uživatele:

```
1. Klikne "Sign up with Google/Microsoft"
2. Přesměrování na Google/Microsoft pro autorizaci
3. Po autorizaci:
   → Vytvoří se nový User účet (oauth_provider, oauth_provider_id)
   → Email je automaticky ověřený
   → Vytvoří se Stripe Customer
   → Nastaví se 30denní trial
   → Uživatel se automaticky přihlásí
   → Kalendáře se automaticky připojí
4. Redirect na dashboard s úspěšnou zprávou
```

### Pro existujícího OAuth uživatele:

```
1. Klikne "Continue with Google/Microsoft"
2. Přesměrování na Google/Microsoft
3. Po autorizaci:
   → Najde existujícího uživatele podle oauth_provider_id
   → Přihlásí uživatele
   → Aktualizuje kalendářové připojení (refresh tokeny)
4. Redirect na dashboard
```

## 🔑 Environment Variables

OAuth používá stávající proměnné v `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your-secret
GOOGLE_REDIRECT_URI=http://localhost:8080/oauth/google/callback

MICROSOFT_CLIENT_ID=your-app-id-uuid
MICROSOFT_CLIENT_SECRET=your-secret
MICROSOFT_REDIRECT_URI=http://localhost:8080/oauth/microsoft/callback
MICROSOFT_TENANT=common
```

**Poznámka:** Redirect URIs v `.env` se používají pro připojení kalendářů. OAuth login používá hardcoded callbacky v controllerech.

## 🐛 Troubleshooting

### ❌ "Redirect URI mismatch"

**Řešení:** Ujistěte se, že jste přidali VŠECHNY redirect URIs (včetně `/auth` i `/oauth` variant) do Google/Microsoft konzolí.

### ❌ "This email is already registered"

**Důvod:** Email už existuje s jinou metodou přihlášení.
**Řešení:** Uživatel musí použít původní metodu (email/heslo nebo jiný OAuth provider).

### ❌ Kalendáře se nepřipojí

**Řešení:**

1. Zkontrolujte logy: `storage/logs/laravel.log`
2. Ověřte OAuth scopes v `config/services.php`
3. Ujistěte se, že provider má správná oprávnění

### ❌ OAuth uživatel nemůže použít email/heslo

**Důvod:** OAuth účty nemají heslo v databázi.
**Řešení:** Systém automaticky ukáže zprávu s návodem použít OAuth tlačítko.

## 📊 Databázové změny

Migrace přidala do `users` tabulky:

- `oauth_provider` - 'google', 'microsoft' nebo NULL
- `oauth_provider_id` - unique ID od providera
- `oauth_provider_email` - email od providera
- `password` - nyní nullable (OAuth uživatelé nemají heslo)
- Unique index: `(oauth_provider, oauth_provider_id)`

## 🚀 Pro produkci

Před nasazením do produkce:

1. ✅ Přidat production redirect URIs:

   ```
   https://yourdomain.com/auth/google/callback
   https://yourdomain.com/auth/microsoft/callback
   https://yourdomain.com/oauth/google/callback
   https://yourdomain.com/oauth/microsoft/callback
   ```

2. ✅ Aktualizovat `.env`:

   ```env
   GOOGLE_REDIRECT_URI=https://yourdomain.com/oauth/google/callback
   MICROSOFT_REDIRECT_URI=https://yourdomain.com/oauth/microsoft/callback
   ```

3. ✅ Otestovat OAuth flow na production

4. ✅ Monitorovat logy pro případné chyby

## 📚 Další dokumentace

- **Podrobný setup:** `OAUTH_LOGIN_SETUP.md`
- **Přehled změn:** `OAUTH_LOGIN_CHANGES.md`
- **Původní OAuth (kalendáře):** `OAUTH_SETUP.md`

## ✨ A je to!

OAuth login je připraven k použití! 🎉
