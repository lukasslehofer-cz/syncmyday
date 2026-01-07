# ✅ MXroute + Mailgun - Quick Checklist

Stručný návod na nastavení. **Kompletní instrukce:** `MXROUTE_MAILGUN_SETUP.md`

---

## 📝 **TVOJE AKCE (co musíš udělat)**

### 1️⃣ **MXroute Setup (10 min)**

1. **Login:** https://directadmin.mxroute.com/
2. **Přidat domény** (Account Manager → Create Account):
   - syncmyday.cz, syncmyday.sk, syncmyday.pl, syncmyday.de, syncmyday.eu
3. **Vytvořit mailboxy** (Email Accounts → Create):
   - `info@syncmyday.cz` (a ostatní domény)
4. **Zkopíruj SMTP** (klikni na email):
   ```
   Server: bunny.mxroute.com
   Port: 587
   Username: info@syncmyday.cz
   Password: [vygeneruj]
   ```
5. **Zkopíruj DKIM** (Email Authentication → DKIM)

---

### 2️⃣ **Mailgun Setup (15 min)**

1. **Registrace:** https://signup.mailgun.com/
   - Vyber plán: **Foundation** ($35/měsíc - 50k emailů)
2. **Přidat domény** (Sending → Domains → Add Domain):
   - syncmyday.cz, syncmyday.sk, syncmyday.pl, syncmyday.de, syncmyday.eu
3. **Zkopíruj DNS záznamy** (pro každou doménu):
   ```
   SPF: v=spf1 include:mailgun.org ~all
   DKIM: smtp._domainkey.syncmyday.cz → [dlouhý klíč]
   ```
4. **Zkopíruj SMTP** (klikni na doménu → SMTP credentials):
   ```
   Host: smtp.mailgun.org
   Port: 587
   Username: postmaster@syncmyday.cz
   Password: [vygeneruj]
   ```
5. **Vytvoř API klíč** (Settings → API Keys):
   ```
   Private API Key: key-abc123...
   ```
6. **Vytvoř Inbound Routes** (Receiving → Routes → Create Route):

   **Pro KAŽDOU doménu:**

   ```
   Expression: events@syncmyday.cz
   Action: https://syncmyday.cz/api/webhook/mailgun-inbound
   ```

   Opakuj pro: `.sk`, `.pl`, `.de`, `.eu`

7. **Zkopíruj Webhook Key** (Settings → Webhooks):
   ```
   Signing Key: whsec_abc123...
   ```

---

### 3️⃣ **DNS Konfigurace (30 min)**

**Pro KAŽDOU doménu** (`.cz`, `.sk`, `.pl`, `.de`, `.eu`):

```
# A záznam (web)
Type: A
Hostname: @
Value: [IP tvého Hostinger VPS]

# MX záznam (mail)
Type: MX
Hostname: @
Value: bunny.mxroute.com
Priority: 10

# SPF (COMBO - MXroute + Mailgun!)
Type: TXT
Hostname: @
Value: v=spf1 include:mxroute.com include:mailgun.org ~all

# DKIM - MXroute
Type: TXT
Hostname: default._domainkey
Value: v=DKIM1; k=rsa; p=MIGfMA0... [z MXroute]

# DKIM - Mailgun
Type: TXT
Hostname: smtp._domainkey
Value: k=rsa; p=MIGfMA0... [z Mailgun]

# DMARC
Type: TXT
Hostname: _dmarc
Value: v=DMARC1; p=quarantine; rua=mailto:dmarc@syncmyday.cz
```

**Ověř:**

```bash
dig syncmyday.cz MX
dig syncmyday.cz TXT
dig default._domainkey.syncmyday.cz TXT
dig smtp._domainkey.syncmyday.cz TXT
```

---

### 4️⃣ **Hostinger VPS - Web Server (45 min)**

```bash
# SSH do VPS
ssh root@[IP_VPS]

# Vytvoř Nginx config pro každou doménu
cd /etc/nginx/sites-available/
nano syncmyday.sk.conf
```

Config:

```nginx
server {
    listen 80;
    server_name syncmyday.sk www.syncmyday.sk;
    root /var/www/syncmyday/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        include fastcgi_params;
    }
}
```

Aktivuj:

```bash
ln -s /etc/nginx/sites-available/syncmyday.sk.conf /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

Opakuj pro všechny domény.

---

### 5️⃣ **SSL Certifikáty (5 min)**

```bash
# Certbot
certbot --nginx -d syncmyday.cz -d www.syncmyday.cz \
                 -d syncmyday.sk -d www.syncmyday.sk \
                 -d syncmyday.pl -d www.syncmyday.pl \
                 -d syncmyday.de -d www.syncmyday.de \
                 -d syncmyday.eu -d www.syncmyday.eu
```

---

### 6️⃣ **.env Konfigurace (5 min)**

Na VPS otevři `.env` a přidej:

```bash
# MXroute - system emails (info@)
MAIL_MAILER=mxroute
MXROUTE_HOST=bunny.mxroute.com
MXROUTE_PORT=587
MXROUTE_USERNAME=info@syncmyday.cz
MXROUTE_PASSWORD="tvoje_heslo_z_mxroute"
MXROUTE_ENCRYPTION=tls

# Mailgun - blockers (events@)
MAILGUN_SMTP_HOST=smtp.mailgun.org
MAILGUN_SMTP_PORT=587
MAILGUN_SMTP_USERNAME=postmaster@syncmyday.cz
MAILGUN_SMTP_PASSWORD="tvoje_heslo_z_mailgun"
MAILGUN_SMTP_ENCRYPTION=tls

# Mailgun API
MAILGUN_DOMAIN=syncmyday.cz
MAILGUN_SECRET="tvuj_api_klic_z_mailgun"
MAILGUN_ENDPOINT=api.mailgun.net
MAILGUN_WEBHOOK_SIGNING_KEY="tvuj_webhook_key"

# Legacy fallback
MAIL_HOST=bunny.mxroute.com
MAIL_PORT=587
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD="tvoje_heslo_z_mxroute"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@syncmyday.cz
MAIL_FROM_NAME="SyncMyDay"
```

Clear cache:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ✅ **TESTOVÁNÍ**

```bash
# Test 1: System email (MXroute)
php artisan tinker
>>> $user = \App\Models\User::first();
>>> Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
# Zkontroluj: Email přišel z info@{domain}

# Test 2: Blocker email (Mailgun)
php artisan tinker
>>> $connection = \App\Models\EmailCalendarConnection::first();
>>> $service = app(\App\Services\Email\ImipEmailService::class);
>>> $service->sendBlockerInvitation(
...     $connection, 'test@example.com', 'uid123', 'Test',
...     new DateTime('2025-01-15 10:00'), new DateTime('2025-01-15 11:00')
... );
# Zkontroluj: Email přišel z events@{domain}

# Test 3: Inbound webhook
# Odpověz na blocker email z Outlook
# Zkontroluj logy: tail -f storage/logs/laravel.log | grep Mailgun
```

---

## 📊 **Co kde najdeš**

| Věc                      | Kde najít                                   |
| ------------------------ | ------------------------------------------- |
| MXroute SMTP credentials | DirectAdmin → Email Accounts → klikni email |
| MXroute DKIM             | DirectAdmin → Email Authentication → DKIM   |
| Mailgun SMTP credentials | Dashboard → Domain → SMTP credentials       |
| Mailgun API key          | Dashboard → Settings → API Keys             |
| Mailgun DKIM             | Dashboard → Domain → Domain verification    |
| Mailgun Webhook key      | Dashboard → Settings → Webhooks             |
| Mailgun Routes           | Dashboard → Receiving → Routes              |
| Hostinger VPS IP         | Hosting panel nebo `curl ifconfig.me`       |

---

## 🚨 **Troubleshooting**

**Email se neposílá:**

- Zkontroluj credentials v `.env`
- `php artisan config:clear`
- Test: `telnet smtp.mailgun.org 587`

**Špatný FROM address:**

- Zkontroluj `User::find(X)->registration_domain`
- Clear cache

**Domain not verified:**

- Počkej 24h na DNS propagaci
- `dig smtp._domainkey.syncmyday.cz TXT`

**Webhook nefunguje:**

- Zkontroluj route: `php artisan route:list | grep mailgun`
- Zkontroluj logy: `tail -f storage/logs/laravel.log`

---

## ⏱️ **Časová náročnost**

- MXroute setup: **10 min**
- Mailgun setup: **15 min**
- DNS konfigurace: **30 min** (+ 24h propagace)
- VPS web server: **45 min**
- SSL certifikáty: **5 min**
- .env konfigurace: **5 min**
- Testování: **10 min**

**Celkem: ~2 hodiny práce** (+ čekání na DNS)

---

## 💰 **Ceny**

- **MXroute:** $15-25/rok
- **Mailgun Foundation:** $35/měsíc ($420/rok)
- **Celkem:** ~$440-445/rok

---

**🎉 Hotovo! Kompletní dokumentace v `MXROUTE_MAILGUN_SETUP.md`**

