# 🇨🇿 Jak nasadit SyncMyDay na hosting?

> **Stručný průvodce česky pro komerční hosting**

---

## 🎯 Co máte?

### Mám **sdílený hosting** (Wedos, Forpsi, WebSupport, atd.)

- ✅ Přístup jen přes cPanel/FTP
- ✅ Nemůžu instalovat software
- ✅ Budget ~50-200 Kč/měsíc

**👉 Postupujte podle:** [NASAZENI_SDILENY_HOSTING.md](NASAZENI_SDILENY_HOSTING.md)

---

### Mám **VPS** (vlastní server)

- ✅ SSH root přístup
- ✅ Můžu instalovat software
- ✅ Budget ~200-500 Kč/měsíc

**👉 Postupujte podle:** [NASAZENI_PRODUKCE.md](NASAZENI_PRODUKCE.md)

---

## ⚡ Rychlý start - SDÍLENÝ HOSTING

### Krok 1: Příprava (na vašem počítači)

```bash
cd /Users/lukas/SyncMyDay

# Spusťte pomocný skript
./prepare-shared-hosting.sh
```

Skript vytvoří:

- ✅ Archiv projektu k nahrání
- ✅ `.env.shared-hosting` - konfiguraci
- ✅ `install.php` - instalační skript

### Krok 2: Doplňte údaje

Otevřete `.env.shared-hosting` a vyplňte:

```env
# Údaje k databázi (dostanete od hostingu)
DB_DATABASE=nazev_databaze
DB_USERNAME=uzivatel
DB_PASSWORD=heslo

# Google OAuth klíče (z https://console.cloud.google.com/)
GOOGLE_CLIENT_ID=xxxxx
GOOGLE_CLIENT_SECRET=xxxxx

# Microsoft OAuth klíče (z https://portal.azure.com/)
MICROSOFT_CLIENT_ID=xxxxx
MICROSOFT_CLIENT_SECRET=xxxxx

# Stripe LIVE klíče (z https://dashboard.stripe.com/)
STRIPE_KEY=pk_live_xxxxx
STRIPE_SECRET=sk_live_xxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxx
STRIPE_PRO_PRICE_ID=price_xxxxx

# Email SMTP (od vašeho hostingu nebo SendGrid)
MAIL_HOST=smtp.vase-domena.cz
MAIL_USERNAME=noreply@vase-domena.cz
MAIL_PASSWORD=heslo
```

### Krok 3: Nahrajte na hosting

1. **Přihlaste se do cPanel**
2. **File Manager** → `public_html` nebo `www`
3. **Nahrajte** archiv `syncmyday-shared-hosting-*.tar.gz`
4. **Rozbalte** archiv (pravý klik → Extract)
5. **Přejmenujte** `.env.shared-hosting` → `.env`

### Krok 4: Nastavte databázi

V cPanel:

1. **MySQL Databases** → Create Database
2. **Create User** → vytvořte uživatele
3. **Add User to Database** → ALL PRIVILEGES

### Krok 5: Spusťte instalaci

1. Otevřete v prohlížeči: `https://vase-domena.cz/install.php`
2. Zadejte heslo (výchozí: `change-me-before-upload`)
3. Klikněte **Spustit instalaci**
4. **IHNED po dokončení SMAŽTE** `install.php`!

### Krok 6: Nastavte Cron

V cPanel → Cron Jobs:

```bash
# Každých 5 minut
*/5 * * * * cd /home/uzivatel/public_html && php artisan queue:work --stop-when-empty --max-time=240 >> /dev/null 2>&1

# Každou minutu (scheduler)
* * * * * cd /home/uzivatel/public_html && php artisan schedule:run >> /dev/null 2>&1
```

### Krok 7: Hotovo! 🎉

Otevřete `https://vase-domena.cz` a zaregistrujte se!

---

## 📋 Checklist

### Před začátkem potřebujete:

- [ ] Doménu (např. `mojedomena.cz`) - ~200 Kč/rok
- [ ] Hosting s PHP 8.2+ - ~50-200 Kč/měsíc
- [ ] Google Cloud účet (OAuth) - zdarma
- [ ] Microsoft Azure účet (OAuth) - zdarma
- [ ] Stripe účet (platby) - zdarma
- [ ] Email službu (SendGrid 100/den zdarma)

### Jak získat API klíče:

#### 1. Google OAuth (5 minut)

```
1. Jděte na https://console.cloud.google.com/
2. Vytvořte projekt
3. APIs & Services → Library → Google Calendar API → Enable
4. Credentials → Create → OAuth 2.0 Client ID
5. Redirect URI: https://vase-domena.cz/oauth/google/callback
6. Zkopírujte Client ID a Secret
```

#### 2. Microsoft OAuth (5 minut)

```
1. Jděte na https://portal.azure.com/
2. App registrations → New
3. Redirect URI: https://vase-domena.cz/oauth/microsoft/callback
4. Certificates & secrets → New client secret
5. API permissions → Microsoft Graph → Calendars.ReadWrite, offline_access
6. Zkopírujte Client ID a Secret
```

#### 3. Stripe (3 minuty)

```
1. Jděte na https://dashboard.stripe.com/
2. Přepněte na LIVE mode (DŮLEŽITÉ!)
3. Developers → API keys → zkopírujte
4. Products → vytvořte produkt "Pro Plan" → zkopírujte Price ID
5. Webhooks → Add endpoint: https://vase-domena.cz/webhooks/stripe
```

#### 4. Email (SendGrid - 5 minut)

```
1. Registrace na https://sendgrid.com/ (zdarma)
2. Settings → API Keys → Create
3. Zkopírujte API key
4. V .env:
   MAIL_HOST=smtp.sendgrid.net
   MAIL_USERNAME=apikey
   MAIL_PASSWORD=SG.xxxxxx
```

---

## 🆘 Nejčastější problémy

### "500 Internal Server Error"

```bash
# Zkontrolujte oprávnění
chmod -R 775 storage bootstrap/cache
chmod 600 .env

# Vymažte cache
php artisan config:clear
php artisan cache:clear
```

### "Synchronizace nefunguje"

```bash
# Zkontrolujte, že běží cron (v cPanel)
# Ručně spusťte:
php artisan queue:work --once
```

### "Database connection error"

```bash
# Ověřte údaje v .env
# Zkontrolujte, že databáze existuje (cPanel → MySQL Databases)
```

---

## 💡 Tipy

### ✅ Doporučené hostingy pro ČR/SK:

| Hosting                 | Cena         | PHP 8.2 | SSH | Hodnocení  |
| ----------------------- | ------------ | ------- | --- | ---------- |
| **Wedos WebHosting M**  | 49 Kč/měsíc  | ✅      | ✅  | ⭐⭐⭐⭐⭐ |
| **WebSupport Standard** | 2.99 €/měsíc | ✅      | ✅  | ⭐⭐⭐⭐   |
| **Forpsi WebHosting M** | 59 Kč/měsíc  | ✅      | ❌  | ⭐⭐⭐     |

**Doporučení:** Wedos - nejlevnější, PHP 8.2, SSH přístup

### 💰 Celkové náklady:

```
Doména .cz:          ~200 Kč/rok
Wedos WebHosting M:   49 Kč/měsíc = 588 Kč/rok
SendGrid:             Zdarma (100 emailů/den)
SSL certifikát:       Zdarma (Let's Encrypt)
Google/Microsoft API: Zdarma
Stripe:               Zdarma (poplatky až při platbách)

CELKEM: ~800 Kč/rok (67 Kč/měsíc)
```

---

## 📖 Další dokumentace

- **NASAZENI_SDILENY_HOSTING.md** - Detailní průvodce pro sdílený hosting
- **NASAZENI_PRODUKCE.md** - Průvodce pro VPS
- **DEPLOYMENT_GUIDE.md** - Porovnání možností
- **OAUTH_SETUP.md** - Podrobné nastavení OAuth
- **STRIPE_TRIAL_SETUP.md** - Nastavení zkušební doby

---

## 🚀 Poté, co nasadíte

### 1. Nastavte se jako admin

```bash
# Přes SSH nebo přes cPanel Terminal
php artisan tinker

>>> $user = User::where('email', 'vas@email.cz')->first();
>>> $user->is_admin = true;
>>> $user->save();
>>> exit
```

### 2. Otestujte funkčnost

- ✅ Registrace funguje
- ✅ Přihlášení funguje
- ✅ Připojení Google kalendáře
- ✅ Připojení Microsoft kalendáře
- ✅ Vytvoření sync rule
- ✅ Synchronizace proběhla (zkontrolujte po 5 minutách)

### 3. Monitoring

Nastavte uptime monitoring zdarma:

- https://uptimerobot.com/ (zdarma)
- Sledovat URL: https://vase-domena.cz/health

---

## ❓ Časté dotazy

**Q: Jak dlouho trvá nasazení?**  
A: S připravenými API klíči ~20-30 minut poprvé.

**Q: Je to složité?**  
A: Ne, stačí postupovat krok za krokem. Skript vše připraví.

**Q: Můžu to vyzkoušet zdarma?**  
A: Ano! Většina hostingů má 30denní záruku vrácení peněz.

**Q: Co když něco nefunguje?**  
A: Zkontrolujte `storage/logs/laravel.log` nebo napište support hostingu.

**Q: Bude to real-time?**  
A: Na sdíleném hostingu synchronizace každých 5 minut. Pro real-time potřebujete VPS.

**Q: Kolik uživatelů to unese?**  
A: Na sdíleném hostingu ~20-50 aktivních uživatelů. Pro víc použijte VPS.

---

## 🎯 Shrnutí

1. **Spusťte** `./prepare-shared-hosting.sh`
2. **Vyplňte** API klíče do `.env.shared-hosting`
3. **Nahrajte** archiv na hosting
4. **Rozbalte** a přejmenujte `.env`
5. **Spusťte** `install.php` v prohlížeči
6. **Nastavte** cron joby
7. **Hotovo!** 🎉

---

**Potřebujete pomoc? Podívejte se do detailních průvodců nebo kontaktujte support vašeho hostingu.**

**Hodně štěstí! 🍀**
