# OAuth Login - Přehled Změn

## ✨ Nově implementováno

### 1. OAuth Přihlašování a Registrace

- Uživatelé se mohou přihlásit/registrovat přes Google nebo Microsoft
- Při prvním přihlášení jsou automaticky připojeny kalendáře
- OAuth uživatelé mají automaticky ověřený email

### 2. Automatické připojení kalendářů

- Po úspěšném OAuth přihlášení se automaticky připojí kalendáře
- Uživatel nemusí procházet separátním krokem připojení

### 3. Bezpečnostní vylepšení

- Ochrana proti duplicitním účtům (stejný email, různé metody)
- Detekce OAuth účtů při pokusu o email/heslo přihlášení
- Password je nyní nullable pro OAuth uživatele

## 📁 Nové a upravené soubory

### Nové soubory

1. **`database/migrations/2024_10_10_000001_add_oauth_fields_to_users_table.php`**

   - Přidává OAuth pole do users tabulky
   - Password je nyní nullable

2. **`app/Http/Controllers/Auth/SocialAuthController.php`**

   - Controller pro OAuth přihlašování
   - Obsahuje metody pro Google i Microsoft
   - Automaticky připojuje kalendáře

3. **`OAUTH_LOGIN_SETUP.md`**

   - Kompletní dokumentace pro nastavení
   - Návod na konfiguraci redirect URIs
   - Troubleshooting

4. **`OAUTH_LOGIN_CHANGES.md`** (tento soubor)
   - Přehled změn
   - Seznam upravených souborů

### Upravené soubory

1. **`app/Models/User.php`**

   - Přidány fillable: `oauth_provider`, `oauth_provider_id`, `oauth_provider_email`
   - Metoda `isOAuthUser()` - kontrola, zda je uživatel OAuth
   - Metoda `getOAuthProviderName()` - vrací název providera

2. **`routes/web.php`**

   - Přidány routy pro OAuth login:
     - `GET /auth/google` → přesměrování na Google
     - `GET /auth/google/callback` → zpracování Google callback
     - `GET /auth/microsoft` → přesměrování na Microsoft
     - `GET /auth/microsoft/callback` → zpracování Microsoft callback

3. **`app/Http/Controllers/Auth/AuthController.php`**

   - Upravena metoda `login()` - detekuje OAuth účty
   - Pokud OAuth uživatel zkusí email/heslo login, dostane instrukce

4. **`resources/views/auth/login.blade.php`**

   - Přidána OAuth tlačítka (Google, Microsoft)
   - Vizuální oddělení mezi OAuth a email přihlášením
   - "Continue with Google" a "Continue with Microsoft" tlačítka

5. **`resources/views/auth/register.blade.php`**

   - Přidána OAuth tlačítka pro registraci
   - "Sign up with Google" a "Sign up with Microsoft" tlačítka
   - Vizuální oddělení

6. **`config/services.php`**
   - Přidán komentář vysvětlující rozdíl mezi `/auth` a `/oauth` routami

## 🔄 OAuth Flow

### Nový uživatel

```
Uživatel → Klikne "Sign up with Google/Microsoft"
         ↓
    OAuth provider (autorizace)
         ↓
    SocialAuthController::handleCallback()
         ↓
    Vytvoří User (oauth_provider, oauth_provider_id)
         ↓
    Vytvoří Stripe Customer
         ↓
    Přihlásí uživatele (Auth::login)
         ↓
    Připojí kalendáře automaticky
         ↓
    Redirect → Dashboard s úspěšnou zprávou
```

### Existující OAuth uživatel

```
Uživatel → Klikne "Continue with Google/Microsoft"
         ↓
    OAuth provider (autorizace)
         ↓
    SocialAuthController::handleCallback()
         ↓
    Najde existujícího User podle oauth_provider_id
         ↓
    Přihlásí uživatele (Auth::login)
         ↓
    Aktualizuje kalendářové připojení
         ↓
    Redirect → Dashboard s úspěšnou zprávou
```

## 🗄️ Databázové změny

### Users tabulka - nová pole

- `oauth_provider` (string, nullable) - 'google' nebo 'microsoft'
- `oauth_provider_id` (string, nullable) - unique ID od providera
- `oauth_provider_email` (string, nullable) - email od providera
- `password` (string, nullable) - nyní nullable pro OAuth uživatele

### Index

- Unique index na `(oauth_provider, oauth_provider_id)` - zabraňuje duplicitním OAuth účtům

## 🔑 Potřebné konfigurace

### OAuth Redirect URIs (Google & Microsoft Console)

**Development:**

```
http://localhost:8080/auth/google/callback
http://localhost:8080/auth/microsoft/callback
http://localhost:8080/oauth/google/callback
http://localhost:8080/oauth/microsoft/callback
```

**Production:**

```
https://yourdomain.com/auth/google/callback
https://yourdomain.com/auth/microsoft/callback
https://yourdomain.com/oauth/google/callback
https://yourdomain.com/oauth/microsoft/callback
```

### Environment Variables

Stávající proměnné v `.env` zůstávají beze změny:

```env
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...
GOOGLE_REDIRECT_URI=http://localhost:8080/oauth/google/callback

MICROSOFT_CLIENT_ID=...
MICROSOFT_CLIENT_SECRET=...
MICROSOFT_REDIRECT_URI=http://localhost:8080/oauth/microsoft/callback
```

## 📋 Checklist pro nasazení

- [x] Migrace databáze vytvořena
- [x] User model aktualizován
- [x] SocialAuthController vytvořen
- [x] Routy přidány
- [x] Login stránka aktualizována
- [x] Register stránka aktualizována
- [x] Dokumentace vytvořena
- [ ] Přidat redirect URIs do Google Cloud Console
- [ ] Přidat redirect URIs do Microsoft Azure Portal
- [ ] Spustit migraci: `php artisan migrate`
- [ ] Otestovat OAuth flow s Google
- [ ] Otestovat OAuth flow s Microsoft
- [ ] Otestovat automatické připojení kalendářů

## 🧪 Jak testovat

1. **Přidat redirect URIs** do Google a Microsoft konzolí (viz dokumentace)
2. **Spustit migraci**:
   ```bash
   php artisan migrate
   ```
3. **Spustit aplikaci**:
   ```bash
   php artisan serve --port=8080
   ```
4. **Otestovat registraci**:
   - Jít na http://localhost:8080/register
   - Kliknout "Sign up with Google"
   - Autorizovat
   - Ověřit: vytvořen účet + připojené kalendáře
5. **Otestovat přihlášení**:
   - Odhlásit se
   - Jít na http://localhost:8080/login
   - Kliknout "Continue with Google"
   - Ověřit: přihlášen + kalendáře aktualizovány

## 🐛 Známé limitace

1. **Duplicitní emaily** - Pokud uživatel má účet s email/heslem a zkusí OAuth se stejným emailem, dostane chybu
2. **Změna providera** - Uživatel nemůže změnit OAuth providera pokud má již účet
3. **Jméno z Google** - Google API nevrací jméno, použije se email jako name

## 💡 Možná budoucí vylepšení

- [ ] Umožnit propojení více OAuth providerů k jednomu účtu
- [ ] Password reset pro konverzi OAuth účtu na email/heslo účet
- [ ] Lepší získání jména z Google (použít People API)
- [ ] OAuth přes další providery (Apple, Facebook)
- [ ] Profile stránka zobrazující připojené OAuth providery
