# OAuth Setup Guide

Quick guide to set up Google and Microsoft OAuth credentials for SyncMyDay.

## ✅ Google Calendar API

### Steps:

1. **Google Cloud Console:** https://console.cloud.google.com/
2. **Create Project** → Name: `SyncMyDay`
3. **Enable API:** APIs & Services → Library → "Google Calendar API" → Enable
4. **Create Credentials:**
   - APIs & Services → Credentials
   - Configure OAuth Consent Screen (External, fill basic info)
   - Create OAuth Client ID
   - Type: Web application
   - Authorized redirect URIs:
     - `http://localhost:8080/oauth/google/callback`
     - `http://localhost:8081/oauth/google/callback`
5. **Copy credentials:**
   - Client ID (ends with `.apps.googleusercontent.com`)
   - Client secret

### Add to .env:

```env
GOOGLE_CLIENT_ID=your-client-id-here.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-your-secret-here
```

---

## ✅ Microsoft Graph API (Calendar)

### Steps:

1. **Azure Portal:** https://portal.azure.com/
2. **Microsoft Entra ID** → App registrations → New registration
3. **Register:**
   - Name: `SyncMyDay`
   - Supported accounts: Any organizational directory + personal
   - Redirect URI (Web): `http://localhost:8080/oauth/microsoft/callback`
4. **Create Client Secret:**
   - Certificates & secrets → New client secret
   - Copy the **Value** immediately!
5. **API Permissions:**
   - API permissions → Add permission → Microsoft Graph → Delegated
   - Add: `Calendars.ReadWrite`, `offline_access`
6. **Copy Application ID** from Overview page

### Add to .env:

```env
MICROSOFT_CLIENT_ID=your-application-id-uuid
MICROSOFT_CLIENT_SECRET=your-client-secret-value
```

---

## 🔄 After updating .env:

```bash
php artisan config:clear
```

---

## 🧪 Testing:

1. Go to http://localhost:8080/connections
2. Click "Add Google Calendar" or "Add Microsoft Calendar"
3. Authorize the app
4. You should be redirected back with success message

---

## ⚠️ Common Issues:

### "invalid_client" error

- Check that Client ID and Secret are correctly copied to .env
- No extra spaces or quotes
- Run `php artisan config:clear`

### "redirect_uri_mismatch" error

- Make sure redirect URI in OAuth console exactly matches:
  - Google: `http://localhost:8080/oauth/google/callback`
  - Microsoft: `http://localhost:8080/oauth/microsoft/callback`
- Include port number (8080 or 8081)
- Use http (not https) for local development

### "access_denied" error

- Make sure you're using the correct Google/Microsoft account
- Check that Calendar API is enabled (Google)
- Check that permissions are granted (Microsoft)

---

## 🔒 Security Notes:

- Never commit `.env` to git
- Keep Client Secrets private
- For production, use HTTPS and update redirect URIs
- Consider using environment-specific credentials (dev vs prod)

---

## 📚 Official Documentation:

- **Google Calendar API:** https://developers.google.com/calendar/api/guides/overview
- **Microsoft Graph Calendar:** https://learn.microsoft.com/en-us/graph/api/resources/calendar
