# Multi-Domain OAuth Setup

Since SyncMyDay runs on multiple domains (.cz, .eu, .sk, .pl, .de), you need to configure ALL redirect URIs in your OAuth providers.

## 📋 Required Redirect URIs

For **each domain**, you need to add **4 redirect URIs**:

### syncmyday.cz

- `https://syncmyday.cz/oauth/google/callback`
- `https://syncmyday.cz/auth/google/callback`
- `https://syncmyday.cz/oauth/microsoft/callback`
- `https://syncmyday.cz/auth/microsoft/callback`

### syncmyday.eu

- `https://syncmyday.eu/oauth/google/callback`
- `https://syncmyday.eu/auth/google/callback`
- `https://syncmyday.eu/oauth/microsoft/callback`
- `https://syncmyday.eu/auth/microsoft/callback`

### syncmyday.sk

- `https://syncmyday.sk/oauth/google/callback`
- `https://syncmyday.sk/auth/google/callback`
- `https://syncmyday.sk/oauth/microsoft/callback`
- `https://syncmyday.sk/auth/microsoft/callback`

### syncmyday.pl

- `https://syncmyday.pl/oauth/google/callback`
- `https://syncmyday.pl/auth/google/callback`
- `https://syncmyday.pl/oauth/microsoft/callback`
- `https://syncmyday.pl/auth/microsoft/callback`

### syncmyday.de

- `https://syncmyday.de/oauth/google/callback`
- `https://syncmyday.de/auth/google/callback`
- `https://syncmyday.de/oauth/microsoft/callback`
- `https://syncmyday.de/auth/microsoft/callback`

---

## 🔵 Google Cloud Console Setup

1. Go to https://console.cloud.google.com/apis/credentials
2. Select your OAuth 2.0 Client ID
3. Under **Authorized redirect URIs**, add ALL URIs listed above
4. Click **Save**

### Complete List for Google (Copy & Paste):

```
https://syncmyday.cz/oauth/google/callback
https://syncmyday.cz/auth/google/callback
https://syncmyday.eu/oauth/google/callback
https://syncmyday.eu/auth/google/callback
https://syncmyday.sk/oauth/google/callback
https://syncmyday.sk/auth/google/callback
https://syncmyday.pl/oauth/google/callback
https://syncmyday.pl/auth/google/callback
https://syncmyday.de/oauth/google/callback
https://syncmyday.de/auth/google/callback
```

---

## 🔷 Microsoft Azure Portal Setup

1. Go to https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade
2. Select your app registration
3. Go to **Authentication** → **Platform configurations** → **Web**
4. Under **Redirect URIs**, add ALL URIs listed above
5. Click **Save**

### Complete List for Microsoft (Copy & Paste):

```
https://syncmyday.cz/oauth/microsoft/callback
https://syncmyday.cz/auth/microsoft/callback
https://syncmyday.eu/oauth/microsoft/callback
https://syncmyday.eu/auth/microsoft/callback
https://syncmyday.sk/oauth/microsoft/callback
https://syncmyday.sk/auth/microsoft/callback
https://syncmyday.pl/oauth/microsoft/callback
https://syncmyday.pl/auth/microsoft/callback
https://syncmyday.de/oauth/microsoft/callback
https://syncmyday.de/auth/microsoft/callback
```

---

## ⚙️ How It Works Now

The application automatically detects the current domain and uses the correct redirect URI:

### Architecture:

1. **Config** (`config/services.php`): Stores APP_URL-based redirect URIs

   ```php
   'redirect' => env('APP_URL') . '/oauth/microsoft/callback'
   ```

2. **Services** (GoogleCalendarService, MicrosoftCalendarService):
   - Detect current domain in HTTP context
   - Replace APP_URL with current domain
   - Use config value as-is in CLI (for artisan commands)
3. **Controllers** (SocialAuthController):
   - Use domain-aware helpers
   - Ensure correct redirect URI per request

### Result:

- User on **syncmyday.eu** → Redirects to **syncmyday.eu** ✅
- User on **syncmyday.cz** → Redirects to **syncmyday.cz** ✅
- User on **syncmyday.sk** → Redirects to **syncmyday.sk** ✅
- `php artisan config:cache` works in CLI ✅

---

## 🧪 Testing

1. Clear config cache on all servers:

   ```bash
   php artisan config:cache
   ```

2. Test OAuth on each domain:

   - https://syncmyday.cz/connections/connect
   - https://syncmyday.eu/connections/connect
   - https://syncmyday.sk/connections/connect
   - https://syncmyday.pl/connections/connect
   - https://syncmyday.de/connections/connect

3. Verify that after OAuth callback, you stay on the same domain you started from

---

## 📝 Notes

- The `.env` variables `GOOGLE_REDIRECT_URI` and `MICROSOFT_REDIRECT_URI` are now **optional**
- If set, they override the dynamic URL (useful for testing)
- In production, **don't set them** to use automatic domain detection
- Make sure `APP_URL` is set correctly for each domain in their respective `.env` files
