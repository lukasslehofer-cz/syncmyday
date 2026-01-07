# 📧 MXroute + Mailgun Setup Guide

**Architektura:**

- **MXroute** → Systémové emaily (`info@`) - Welcome, Trial, Payments, Password Reset
- **Mailgun** → Calendar blockery (`events@`) + Inbound zpracování

---

## ✅ **ČÁST 1: MXroute Setup (info@)**

### 1.1 Přidat všechny domény

1. **Login:** https://directadmin.mxroute.com/
2. **Account Manager → Create Account:**
   - Pro každou doménu: `syncmyday.cz`, `syncmyday.sk`, `syncmyday.pl`, `syncmyday.de`, `syncmyday.eu`
   - Username: `syncmyday` (nebo jiný)
   - Package: Vyberte svůj balíček
   - Email Quota: 500 MB per domain (stačí)

### 1.2 Vytvořit mailboxy

3. **Email Accounts → Create:**

```
info@syncmyday.cz
info@syncmyday.sk
info@syncmyday.pl
info@syncmyday.de
info@syncmyday.eu
```

- Password: Vygeneruj silné heslo (uložíš do `.env`)
- Quota: 100 MB per mailbox

### 1.3 Získat SMTP credentials

4. **Klikni na jakýkoliv `info@` email** → zobrazí se SMTP settings:

```
Server: bunny.mxroute.com (nebo tvůj konkrétní)
Port: 587 (TLS) nebo 465 (SSL)
Username: info@syncmyday.cz (použij jeden pro všechny)
Password: [heslo které jsi nastavil]
Encryption: TLS
```

> **TIP:** MXroute umožňuje použít **jeden** mailbox pro všechny `FROM` adresy, takže stačí vytvořit jen `info@syncmyday.cz` a použít ho pro všechny domény. Ale je lepší mít samostatné mailboxy pro lepší organizaci.

### 1.4 Získat DKIM záznamy

5. **Email Authentication → DKIM Records:**
   - Pro každou doménu klikni "View DKIM"
   - Zkopíruj TXT záznam (bude začínat `default._domainkey`)

---

## ✅ **ČÁST 2: Mailgun Setup (events@)**

### 2.1 Registrace a plán

1. **Registrace:** https://signup.mailgun.com/
2. **Vyber plán:**
   - **Foundation:** $35/měsíc (50 000 emailů/měsíc)
   - **Growth:** $80/měsíc (100 000 emailů/měsíc)
   - Pro začátek stačí Foundation

### 2.2 Přidat domény

3. **Sending → Domains → Add New Domain:**

Přidej všech 5 domén:

- `syncmyday.cz`
- `syncmyday.sk`
- `syncmyday.pl`
- `syncmyday.de`
- `syncmyday.eu`

### 2.3 Ověřit domény (DNS)

Pro každou doménu Mailgun zobrazí DNS záznamy:

```
# TXT záznam pro SPF
Type: TXT
Hostname: @ (nebo syncmyday.cz)
Value: v=spf1 include:mailgun.org ~all

# TXT záznam pro DKIM
Type: TXT
Hostname: smtp._domainkey.syncmyday.cz
Value: k=rsa; p=MIGfMA0GCSqGSIb3... (dlouhý klíč)

# CNAME pro tracking (volitelné)
Type: CNAME
Hostname: email.syncmyday.cz
Value: mailgun.org
```

> **Ověření trvá až 24 hodin.** Mailgun pošle email když je doména verified.

### 2.4 Získat SMTP credentials

4. **Klikni na doménu → SMTP credentials:**

```
SMTP Host: smtp.mailgun.org
Port: 587 (TLS) nebo 465 (SSL)
Username: postmaster@syncmyday.cz
Password: [vygeneruj v Mailgun dashboard]
```

> **Pro VŠECHNY domény můžeš použít STEJNÝ SMTP server**, jen změň `FROM` hlavičku.

### 2.5 Vytvořit API klíč

5. **Settings → API Keys → Create New Key:**
   - Name: "SyncMyDay Production"
   - Scope: "Full Access" (nebo jen "Send", "Receive")
   - **Zkopíruj klíč** - zobrazí se jen jednou!

```
Private API Key: key-1234567890abcdef...
```

### 2.6 Nastavit Inbound Route

6. **Receiving → Routes → Create Route:**

Pro **každou doménu** vytvoř route:

```
Priority: 0
Expression Type: Match Recipient
Expression: events@syncmyday.cz
Action: Forward to URL
URL: https://syncmyday.cz/api/webhook/mailgun-inbound
Description: Forward events@ to webhook
```

Opakuj pro všechny domény:

- `events@syncmyday.sk` → webhook
- `events@syncmyday.pl` → webhook
- `events@syncmyday.de` → webhook
- `events@syncmyday.eu` → webhook

### 2.7 Získat Webhook Signing Key

7. **Settings → Webhooks → Webhook Signing Key:**

```
Signing Key: whsec_1234567890abcdef...
```

Tento klíč slouží k ověření, že webhook volání přišlo opravdu z Mailgunu.

---

## ✅ **ČÁST 3: DNS Konfigurace**

Pro **každou doménu** (`syncmyday.cz`, `.sk`, `.pl`, `.de`, `.eu`) nastav tyto záznamy:

### 3.1 A záznam (web)

```
Type: A
Hostname: @ (nebo syncmyday.cz)
Value: [IP tvého Hostinger VPS]
TTL: 3600
```

### 3.2 MX záznamy (MXroute)

```
Type: MX
Hostname: @ (nebo syncmyday.cz)
Value: bunny.mxroute.com (nebo tvůj server)
Priority: 10
TTL: 3600
```

### 3.3 SPF záznam (COMBO - MXroute + Mailgun)

```
Type: TXT
Hostname: @ (nebo syncmyday.cz)
Value: v=spf1 include:mxroute.com include:mailgun.org ~all
TTL: 3600
```

> **POZOR:** Tento SPF záznam povoluje posílání emailů z **obou** serverů!

### 3.4 DKIM záznamy

Musíš přidat **DVA** DKIM záznamy (jeden pro MXroute, jeden pro Mailgun):

**MXroute DKIM:**

```
Type: TXT
Hostname: default._domainkey.syncmyday.cz
Value: v=DKIM1; k=rsa; p=MIGfMA0... (zkopíruj z MXroute)
TTL: 3600
```

**Mailgun DKIM:**

```
Type: TXT
Hostname: smtp._domainkey.syncmyday.cz
Value: k=rsa; p=MIGfMA0... (zkopíruj z Mailgun)
TTL: 3600
```

### 3.5 DMARC záznam

```
Type: TXT
Hostname: _dmarc.syncmyday.cz
Value: v=DMARC1; p=quarantine; rua=mailto:dmarc@syncmyday.cz
TTL: 3600
```

---

## ✅ **ČÁST 4: Hostinger VPS Setup**

### 4.1 Přidat všechny domény do web serveru

**Nginx příklad:**

```bash
# SSH do VPS
ssh root@[IP_VPS]

# Vytvoř config pro každou doménu
cd /etc/nginx/sites-available/

# syncmyday.sk
nano syncmyday.sk.conf
```

Config:

```nginx
server {
    listen 80;
    server_name syncmyday.sk www.syncmyday.sk;
    root /var/www/syncmyday/public;

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
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

### 4.2 SSL certifikáty (Let's Encrypt)

```bash
# Nainstaluj Certbot
apt update && apt install certbot python3-certbot-nginx

# Získej certifikáty pro všechny domény najednou
certbot --nginx -d syncmyday.cz -d www.syncmyday.cz \
                 -d syncmyday.sk -d www.syncmyday.sk \
                 -d syncmyday.pl -d www.syncmyday.pl \
                 -d syncmyday.de -d www.syncmyday.de \
                 -d syncmyday.eu -d www.syncmyday.eu

# Auto-renewal (certbot to nastaví sám)
systemctl status certbot.timer
```

---

## ✅ **ČÁST 5: .env Konfigurace**

Do svého `.env` souboru na VPS přidej:

```bash
# ============================================
# MAIL CONFIGURATION - MXroute + Mailgun
# ============================================

# Default mail driver
MAIL_MAILER=mxroute

# MXroute - for system emails (info@)
MXROUTE_HOST=bunny.mxroute.com
MXROUTE_PORT=587
MXROUTE_USERNAME=info@syncmyday.cz
MXROUTE_PASSWORD="tvoje_mxroute_heslo"
MXROUTE_ENCRYPTION=tls

# Mailgun - for calendar blockers (events@)
MAILGUN_SMTP_HOST=smtp.mailgun.org
MAILGUN_SMTP_PORT=587
MAILGUN_SMTP_USERNAME=postmaster@syncmyday.cz
MAILGUN_SMTP_PASSWORD="tvoje_mailgun_heslo"
MAILGUN_SMTP_ENCRYPTION=tls

# Mailgun API (for inbound webhooks)
MAILGUN_DOMAIN=syncmyday.cz
MAILGUN_SECRET="tvuj_mailgun_api_klic"
MAILGUN_ENDPOINT=api.mailgun.net
MAILGUN_WEBHOOK_SIGNING_KEY="tvuj_webhook_signing_key"

# Fallback mail config (legacy - same as MXroute)
MAIL_HOST=bunny.mxroute.com
MAIL_PORT=587
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD="tvoje_mxroute_heslo"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=info@syncmyday.cz
MAIL_FROM_NAME="SyncMyDay"
```

Po změně `.env`:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## ✅ **ČÁST 6: Testování**

### Test 1: System email (MXroute)

```bash
php artisan tinker
>>> $user = \App\Models\User::first();
>>> Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
# Zkontroluj:
# - Email dorazil
# - FROM: info@{domain podle user->registration_domain}
# - Email prošel přes MXroute (check headers)
```

### Test 2: Calendar blocker (Mailgun)

```bash
php artisan tinker
>>> $connection = \App\Models\EmailCalendarConnection::first();
>>> $service = app(\App\Services\Email\ImipEmailService::class);
>>> $service->sendBlockerInvitation(
...     $connection,
...     'test@example.com',
...     'test-uid-123',
...     'Test Blocker',
...     new DateTime('2025-01-15 10:00:00'),
...     new DateTime('2025-01-15 11:00:00')
... );
# Zkontroluj:
# - Email dorazil
# - FROM: events@{domain podle user->registration_domain}
# - Email prošel přes Mailgun
```

### Test 3: Inbound webhook (Mailgun)

```bash
# Odpověz na blocker email z Outlook/Gmail
# Mailgun automaticky forwardne na webhook
# Zkontroluj logy:
tail -f storage/logs/laravel.log | grep "Mailgun inbound"
```

---

## 📊 **Co kde najdeš**

| Věc                      | Kde najít                                      |
| ------------------------ | ---------------------------------------------- |
| MXroute SMTP credentials | DirectAdmin → Email Accounts → klikni na email |
| MXroute DKIM             | DirectAdmin → Email Authentication → DKIM      |
| Mailgun SMTP credentials | Dashboard → Domain → SMTP credentials          |
| Mailgun API key          | Dashboard → Settings → API Keys                |
| Mailgun DKIM             | Dashboard → Domain → Domain verification       |
| Mailgun Webhook key      | Dashboard → Settings → Webhooks                |
| Mailgun Routes           | Dashboard → Receiving → Routes                 |
| Hostinger VPS IP         | Hosting panel nebo `curl ifconfig.me` na VPS   |

---

## 🚨 **Troubleshooting**

**Email se neposílá:**

- Zkontroluj credentials v `.env`
- `php artisan config:clear`
- Zkontroluj firewall na VPS (port 587 otevřený?)
- Test SMTP: `telnet smtp.mailgun.org 587`

**Špatný FROM address:**

- Zkontroluj `User::find(X)->registration_domain`
- Clear cache: `php artisan config:clear`
- Zkontroluj `EmailHelper::getEmailConfig()`

**Domain not verified (Mailgun):**

- Počkej na DNS propagaci (až 24h)
- Zkontroluj DKIM záznam: `dig smtp._domainkey.syncmyday.cz TXT`
- Zkontroluj SPF: `dig syncmyday.cz TXT`

**Inbound webhook nefunguje:**

- Zkontroluj URL: `https://syncmyday.cz/api/webhook/mailgun-inbound`
- Zkontroluj že route je aktivní: `php artisan route:list | grep mailgun`
- Zkontroluj logy: `tail -f storage/logs/laravel.log`
- Test webhook: Pošli testovací email přes Mailgun dashboard

**SPF fails:**

- Ujisti se že máš `include:mxroute.com include:mailgun.org` v jednom SPF záznamu
- Nesmíš mít více SPF záznamů (jen jeden TXT záznam s `v=spf1`)

---

## 💰 **Ceny**

- **MXroute:** $15-25/rok (neomezené domény, 25-100 GB storage)
- **Mailgun:** $35/měsíc (50 000 emailů) nebo $80/měsíc (100 000)
- **Celkem:** ~$450/rok pro Foundation plán

---

## ✅ **Checklist**

- [ ] MXroute - přidány všechny domény
- [ ] MXroute - vytvořeny `info@` mailboxy
- [ ] MXroute - zkopírovány SMTP credentials
- [ ] MXroute - zkopírovány DKIM záznamy
- [ ] Mailgun - registrace a výběr plánu
- [ ] Mailgun - přidány všechny domény
- [ ] Mailgun - zkopírovány SMTP credentials
- [ ] Mailgun - vytvořen API klíč
- [ ] Mailgun - zkopírován Webhook Signing Key
- [ ] Mailgun - vytvořeny Inbound Routes (5x)
- [ ] DNS - nastaveny A záznamy (5x)
- [ ] DNS - nastaveny MX záznamy (5x)
- [ ] DNS - nastaveny SPF záznamy (5x)
- [ ] DNS - nastaveny DKIM záznamy MXroute (5x)
- [ ] DNS - nastaveny DKIM záznamy Mailgun (5x)
- [ ] DNS - nastaveny DMARC záznamy (5x)
- [ ] VPS - nakonfigurovány domény v Nginx/Apache
- [ ] VPS - získány SSL certifikáty (Let's Encrypt)
- [ ] VPS - přidány credentials do `.env`
- [ ] VPS - config cleared
- [ ] Test - system email (info@)
- [ ] Test - blocker email (events@)
- [ ] Test - inbound webhook

---

**🎉 Hotovo! Tvoje multi-domain email setup je kompletní.**

