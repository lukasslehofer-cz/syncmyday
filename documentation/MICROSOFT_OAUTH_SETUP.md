# 🔐 Microsoft OAuth Setup - Checklist

## ✅ Co už máte:

```
✅ Client ID: 90fc130f-9adb-4d9d-8b98-929dfad1220f
✅ Client Secret: 3f3a1f74-8297-4603-b332-a169498ac06e
✅ Tenant ID: 8a18109f-ef05-47ca-86cf-6ace282b8f98
✅ Credentials uloženy do .env
```

---

## 🔧 Co ještě MUSÍTE udělat v Azure Portal:

### 1️⃣ **Přidat Redirect URI**

**Kde:**

1. Otevřete: **https://portal.azure.com/**
2. Azure Active Directory → **App registrations**
3. Najděte vaši aplikaci (nebo použijte App ID: `90fc130f-9adb-4d9d-8b98-929dfad1220f`)
4. V levém menu: **Authentication**

**Co přidat:**

V sekci **"Platform configurations"** → **Web** → **Redirect URIs**:

```
http://localhost:8080/oauth/microsoft/callback
```

**DŮLEŽITÉ:**

- ✅ Musí být PŘESNĚ tento URL (včetně `http://` a `/callback` na konci)
- ✅ Nezapomeňte kliknout **"Save"** dole na stránce

---

### 2️⃣ **Nastavit API Permissions**

**Kde:**

1. Stejná aplikace v Azure Portal
2. V levém menu: **API permissions**

**Co přidat:**

Klikněte **"+ Add a permission"** → **Microsoft Graph** → **Delegated permissions**

Zaškrtněte:

- ✅ `Calendars.ReadWrite` - Read and write user calendars
- ✅ `offline_access` - Maintain access to data you have given it access to
- ✅ `User.Read` - Sign in and read user profile (mělo by být už automaticky)

Pak klikněte **"Add permissions"**

**Screenshot by měl vypadat:**

```
Microsoft Graph (3)
  Calendars.ReadWrite    Delegated
  offline_access         Delegated
  User.Read              Delegated
```

---

### 3️⃣ **Grant Admin Consent (pokud je vyžadován)**

**Kdy je potřeba:**

- Pokud vidíte žlutý výkřičník u permissions
- Pokud vaše organizace vyžaduje admin approval

**Jak:**

1. Na stránce **API permissions**
2. Klikněte **"Grant admin consent for [název organizace]"**
3. Potvrďte

---

### 4️⃣ **Ověřit Client Secret**

**Kde:**

1. V levém menu: **Certificates & secrets**
2. Záložka: **Client secrets**

**Co zkontrolovat:**

- ✅ Secret existuje a **NENÍ expirovaný**
- ✅ Value je: `3f3a1f74-8297-4603-b332-a169498ac06e`

**Pokud expiroval nebo neexistuje:**

1. Klikněte **"+ New client secret"**
2. Description: "SyncMyDay Local Dev"
3. Expires: 6 months (nebo podle vaší organizace)
4. Klikněte **"Add"**
5. **OKAMŽITĚ zkopírujte "Value"** (zobrazí se jen jednou!)
6. Aktualizujte v `.env`: `MICROSOFT_CLIENT_SECRET=nová-hodnota`

---

## 🧪 Testování

### Krok 1: Zkontrolujte konfiguraci

```bash
php artisan config:clear

php -r "
\$config = config('services.microsoft');
echo 'Microsoft OAuth Config:' . PHP_EOL;
echo '  Client ID: ' . \$config['client_id'] . PHP_EOL;
echo '  Redirect: ' . \$config['redirect'] . PHP_EOL;
echo '  Tenant: ' . \$config['tenant'] . PHP_EOL;
echo '  Scopes: ' . implode(', ', \$config['scopes']) . PHP_EOL;
"
```

**Měli byste vidět:**

```
Microsoft OAuth Config:
  Client ID: 90fc130f-9adb-4d9d-8b98-929dfad1220f
  Redirect: http://localhost:8080/oauth/microsoft/callback
  Tenant: 8a18109f-ef05-47ca-86cf-6ace282b8f98
  Scopes: Calendars.ReadWrite, offline_access
```

---

### Krok 2: Zkuste připojit Microsoft kalendář

1. Otevřete: **http://localhost:8080/connections**
2. Klikněte: **"Add Microsoft Calendar"**
3. Měli byste být **přesměrováni na Microsoft přihlášení**

---

## 🚨 Možné chyby a řešení

### Chyba: "AADSTS50011: The reply URL... does not match"

**Příčina:** Redirect URI není správně nastavené v Azure Portal

**Řešení:**

1. Zkontrolujte že redirect URI je PŘESNĚ: `http://localhost:8080/oauth/microsoft/callback`
2. Uložte změny v Azure Portal
3. Počkejte 1-2 minuty (propagace změn)
4. Zkuste znovu

---

### Chyba: "AADSTS65001: The user or administrator has not consented"

**Příčina:** Chybí API permissions nebo admin consent

**Řešení:**

1. Zkontrolujte že máte všechny 3 permissions (Calendars.ReadWrite, offline_access, User.Read)
2. Pokud je vyžadován, klikněte "Grant admin consent"
3. Zkuste znovu

---

### Chyba: "AADSTS7000215: Invalid client secret"

**Příčina:** Client secret je špatný nebo expirovaný

**Řešení:**

1. Azure Portal → Certificates & secrets
2. Vytvořte nový client secret
3. Zkopírujte Value
4. Aktualizujte `.env`: `MICROSOFT_CLIENT_SECRET=nová-hodnota`
5. `php artisan config:clear`
6. Zkuste znovu

---

### Chyba: "invalid_grant" při callback

**Příčina:** Problém s refresh tokenem nebo tenant

**Řešení:**

1. Zkontrolujte že `MICROSOFT_TENANT` je správné Tenant ID
2. Pokud používáte personal Microsoft account (Outlook.com), změňte tenant na `common`:
   ```env
   MICROSOFT_TENANT=common
   ```
3. Pokud používáte work/school account, použijte konkrétní Tenant ID:
   ```env
   MICROSOFT_TENANT=8a18109f-ef05-47ca-86cf-6ace282b8f98
   ```

---

## 📋 Finální kontrolní seznam

Před testováním zkontrolujte:

- [ ] ✅ Redirect URI: `http://localhost:8080/oauth/microsoft/callback` nastaven v Azure Portal
- [ ] ✅ API Permissions: Calendars.ReadWrite + offline_access přidány
- [ ] ✅ Admin consent: Udělen (pokud je vyžadován)
- [ ] ✅ Client Secret: Není expirovaný
- [ ] ✅ `.env` aktualizován s credentials
- [ ] ✅ Config cache vyčištěna: `php artisan config:clear`
- [ ] ✅ Server běží: `php -S localhost:8080 -t public` (nebo MAMP)

---

## 🎯 Až budete mít všechno nastavené:

1. Jděte na: http://localhost:8080/connections
2. Klikněte: **"Add Microsoft Calendar"**
3. Přihlaste se Microsoft účtem
4. **Povolte přístupy** k Calendar
5. Měli byste být **přesměrováni zpět** do aplikace
6. **Zpráva:** "Calendar connected successfully!" ✅

---

**Až to bude fungovat, dejte vědět! 🎉**
