# OAuth Login Setup (Google & Microsoft)

Tento průvodce vysvětluje, jak nastavit OAuth přihlašování přes Google a Microsoft pro SyncMyDay.

## 🎯 Co je implementováno

✅ **OAuth přihlašování a registrace** přes Google a Microsoft  
✅ **Automatické připojení kalendářů** při prvním přihlášení  
✅ **Stripe checkout pro platební metodu** - automaticky po OAuth login/registraci  
✅ **Kontrola platební metody** - při každém přihlášení se kontroluje Stripe subscription  
✅ **Ochrana proti duplicitním účtům** - stejný email nemůže existovat s různými metodami přihlášení  
✅ **Předověřené emaily** - OAuth uživatelé mají automaticky ověřený email  
✅ **30denní trial** pro nové OAuth uživatele

## 🔧 Nastavení Redirect URIs

### Google Cloud Console

1. Přejděte na: https://console.cloud.google.com/
2. Vyberte váš projekt `SyncMyDay`
3. **APIs & Services → Credentials → OAuth 2.0 Client IDs**
4. Přidejte tyto **Authorized redirect URIs**:

```
# Pro development
http://localhost:8080/auth/google/callback
http://localhost:8081/auth/google/callback
http://localhost:8080/oauth/google/callback
http://localhost:8081/oauth/google/callback

# Pro production (nahraďte vaší doménou)
https://yourdomain.com/auth/google/callback
https://yourdomain.com/oauth/google/callback
```

### Microsoft Azure Portal

1. Přejděte na: https://portal.azure.com/
2. **Microsoft Entra ID → App registrations**
3. Vyberte aplikaci `SyncMyDay`
4. **Authentication → Platform configurations → Web → Redirect URIs**
5. Přidejte tyto redirect URIs:

```
# Pro development
http://localhost:8080/auth/microsoft/callback
http://localhost:8081/auth/microsoft/callback
http://localhost:8080/oauth/microsoft/callback
http://localhost:8081/oauth/microsoft/callback

# Pro production (nahraďte vaší doménou)
https://yourdomain.com/auth/microsoft/callback
https://yourdomain.com/oauth/microsoft/callback
```

## 📝 Rozdíl mezi `/auth` a `/oauth` routami

### `/auth/{provider}/callback` - OAuth Login/Registrace

- **Použití**: Přihlášení nebo registrace nového uživatele
- **Dostupné pro**: Nepřihlášené uživatele (guest)
- **Funkce**:
  - Vytvoří nový účet nebo přihlásí existujícího uživatele
  - Automaticky připojí kalendáře
  - Nastaví 30denní trial

### `/oauth/{provider}/callback` - Připojení kalendáře

- **Použití**: Připojení dalšího kalendáře k existujícímu účtu
- **Dostupné pro**: Přihlášené uživatele (auth)
- **Funkce**:
  - Přidá další kalendářové připojení
  - Neprovádí registraci ani přihlášení

## 🔐 Environment Variables

V `.env` souboru musíte mít nastaveno:

```env
# Google OAuth
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your-secret-here
GOOGLE_REDIRECT_URI=http://localhost:8080/oauth/google/callback

# Microsoft OAuth
MICROSOFT_CLIENT_ID=your-application-id-uuid
MICROSOFT_CLIENT_SECRET=your-client-secret-value
MICROSOFT_REDIRECT_URI=http://localhost:8080/oauth/microsoft/callback
MICROSOFT_TENANT=common
```

**Poznámka**: `REDIRECT_URI` proměnné v `.env` se používají pro připojení kalendářů (`/oauth/*`). OAuth login (`/auth/*`) používá hardcoded callback routy v controllerech.

## 🧪 Testování OAuth Login

1. Spusťte aplikaci: `php artisan serve --port=8080`
2. Přejděte na: http://localhost:8080/login
3. Klikněte na tlačítko **"Continue with Google"** nebo **"Continue with Microsoft"**
4. Autorizujte aplikaci
5. Měli byste být automaticky:
   - Přihlášeni (nebo registrováni jako nový uživatel)
   - Přesměrováni na dashboard
   - Vaše kalendáře by měly být automaticky připojeny

## 🔄 Migrace databáze

OAuth funkce vyžaduje nová pole v `users` tabulce:

```bash
php artisan migrate
```

Migrace přidá:

- `oauth_provider` (google/microsoft/null)
- `oauth_provider_id` (unique ID od providera)
- `oauth_provider_email` (email od providera)
- `password` je nyní nullable (OAuth uživatelé nemají heslo)

## 🛡️ Bezpečnostní funkce

### Ochrana proti duplicitám

- Pokud email již existuje s jinou metodou přihlášení, OAuth registrace selže
- Uživatel dostane zprávu s návodem jak se přihlásit správnou metodou

### Detekce OAuth účtů při emailovém přihlášení

- Pokud OAuth uživatel zkusí použít email/heslo přihlášení
- Systém ho upozorní, že má použít OAuth tlačítko

### Automatické ověření emailu

- OAuth uživatelé mají `email_verified_at` nastaveno automaticky
- Nepotřebují procházet email verifikací

## 📊 Workflow OAuth Login

### Nový uživatel (První přihlášení)

1. Uživatel klikne "Sign up with Google/Microsoft"
2. Autorizuje přístup k účtu a kalendářům
3. Systém:
   - Vytvoří nový uživatelský účet
   - Nastaví OAuth údaje
   - Ověří email automaticky
   - Vytvoří Stripe zákazníka
   - Přidělí 30denní trial
   - Připojí kalendáře automaticky
4. Přesměrování na dashboard

### Existující OAuth uživatel

1. Uživatel klikne "Continue with Google/Microsoft"
2. Autorizuje přístup
3. Systém:
   - Najde existujícího uživatele podle OAuth ID
   - Přihlásí uživatele
   - Aktualizuje kalendářové připojení (refresh tokeny)
4. Přesměrování na dashboard

## 🔍 Troubleshooting

### "Redirect URI mismatch" chyba

- Zkontrolujte, že máte přidané VŠECHNY redirect URIs (obě `/auth` i `/oauth` verze)
- Port musí odpovídat (8080 nebo 8081)
- Pro development použijte `http://`, pro production `https://`

### "This email is already registered"

- Email již existuje s jinou metodou přihlášení
- Uživatel musí použít původní metodu (email/heslo nebo jiný OAuth provider)

### OAuth uživatel nemůže použít email/heslo

- OAuth účty nemají heslo
- Systém automaticky upozorní uživatele, aby použil OAuth tlačítko

### Kalendáře se nepřipojí automaticky

- Zkontrolujte logy: `storage/logs/laravel.log`
- Ujistěte se, že máte správné OAuth scopes v `config/services.php`
- Pro Google: `calendar`, `calendar.events`
- Pro Microsoft: `Calendars.ReadWrite`, `User.Read`, `offline_access`

## 📚 Dokumentace API

- **Google Calendar API**: https://developers.google.com/calendar/api
- **Microsoft Graph Calendar**: https://learn.microsoft.com/en-us/graph/api/resources/calendar

## ✅ Checklist pro produkci

- [ ] Přidat production redirect URIs do Google/Microsoft konzolí
- [ ] Změnit `GOOGLE_REDIRECT_URI` a `MICROSOFT_REDIRECT_URI` v `.env`
- [ ] Použít HTTPS pro všechny redirect URIs
- [ ] Otestovat OAuth flow na production prostředí
- [ ] Zkontrolovat logy pro případné chyby
- [ ] Nastavit email notifikace pro OAuth chyby
