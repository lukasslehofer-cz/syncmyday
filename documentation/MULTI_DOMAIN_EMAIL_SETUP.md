# 📧 Multi-Domain Email Setup Guide

## Overview

SyncMyDay now supports sending emails from multiple domains based on user registration:

- Users registered on `syncmyday.pl` receive emails from `@syncmyday.pl`
- Users registered on `syncmyday.sk` receive emails from `@syncmyday.sk`
- etc.

Each domain has two email types:

- **`info@{domain}`** - System emails (welcome, trial, payments, password reset)
- **`events@{domain}`** - Calendar blocker invitations

---

## 🏗️ Architecture

```
User registers on syncmyday.pl
  ↓
User.registration_domain = "syncmyday.pl"
  ↓
EmailHelper.getEmailConfig(user, 'info')
  ↓
Returns: info@syncmyday.pl
```

### Key Components:

1. **`registration_domain`** column in `users` table - stores domain where user registered
2. **`EmailHelper`** - provides dynamic FROM addresses based on user's domain
3. **MXroute SMTP** - single mail server handling all domains (catch-all feature)
4. **Hostinger VPS** - web server serving all domains

---

## 📋 PART 1: INFRASTRUCTURE SETUP

### A) MXroute Configuration ⏱️ 15-30 min

#### 1. Add Domains to MXroute DirectAdmin

Login to MXroute DirectAdmin and add these domains:

- syncmyday.cz
- syncmyday.sk
- syncmyday.pl
- syncmyday.de
- syncmyday.eu

#### 2. Create Mailboxes (10 total)

For **EACH domain**, create 2 mailboxes:

```
✉️ info@syncmyday.cz     (password: strong_password_1)
✉️ events@syncmyday.cz   (password: strong_password_2)

✉️ info@syncmyday.sk     (password: strong_password_3)
✉️ events@syncmyday.sk   (password: strong_password_4)

✉️ info@syncmyday.pl     (password: strong_password_5)
✉️ events@syncmyday.pl   (password: strong_password_6)

✉️ info@syncmyday.de     (password: strong_password_7)
✉️ events@syncmyday.de   (password: strong_password_8)

✉️ info@syncmyday.eu     (password: strong_password_9)
✉️ events@syncmyday.eu   (password: strong_password_10)
```

#### 3. Get SMTP Credentials

In DirectAdmin → Email Accounts → Click on any email:

```
Server: bunny.mxroute.com (or your specific server)
Port: 587 (TLS) or 465 (SSL)
Username: full email address (e.g., info@syncmyday.cz)
Password: the password you set
```

**Important**: MXroute supports **catch-all**, so you can use ANY of these accounts as SMTP username and send from ANY FROM address!

#### 4. Copy DKIM Keys

For each domain in DirectAdmin → Email Authentication → DKIM:

- Copy the DKIM public key (you'll need it for DNS)

---

### B) DNS Configuration ⏱️ 30-60 min

Configure DNS for **ALL 5 domains** (cz, sk, pl, de, eu):

#### Example for `syncmyday.cz`:

```dns
# ===  WEB (Hostinger VPS) ===
@           A      123.456.789.123  ← your Hostinger VPS IP
www         A      123.456.789.123

# === EMAIL (MXroute) ===
@           MX     10  bunny.mxroute.com.  ← your MXroute server

# === ANTI-SPAM (from MXroute) ===
@           TXT    "v=spf1 include:mxroute.com ~all"

# === DKIM (copy from MXroute DirectAdmin) ===
default._domainkey  TXT  "v=DKIM1; k=rsa; p=MIGfMA0GCSq..."

# === DMARC ===
_dmarc      TXT    "v=DMARC1; p=quarantine; rua=mailto:dmarc@syncmyday.cz"
```

**Repeat for all domains!**

#### Verify DNS is working:

```bash
# Test MX
dig syncmyday.cz MX

# Test SPF
dig syncmyday.cz TXT

# Test DKIM
dig default._domainkey.syncmyday.cz TXT

# Wait for propagation (can take up to 24 hours, usually <1 hour)
```

---

### C) Hostinger VPS - Web Server ⏱️ 30-60 min

#### 1. SSH into Hostinger VPS

```bash
ssh root@your-vps-ip
```

#### 2. Check Web Server Type

```bash
# Check what's running
systemctl status nginx     # or
systemctl status apache2
```

#### 3A. Nginx Configuration

```bash
sudo nano /etc/nginx/sites-available/syncmyday
```

```nginx
server {
    listen 80;
    listen 443 ssl http2;

    # All domains
    server_name syncmyday.cz www.syncmyday.cz
                syncmyday.pl www.syncmyday.pl
                syncmyday.sk www.syncmyday.sk
                syncmyday.de www.syncmyday.de
                syncmyday.eu www.syncmyday.eu;

    root /var/www/syncmyday/public;
    index index.php index.html;

    # SSL certificates (set up with certbot later)
    ssl_certificate /etc/letsencrypt/live/syncmyday.cz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/syncmyday.cz/privkey.pem;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    # Deny .htaccess
    location ~ /\.ht {
        deny all;
    }

    # Logging
    access_log /var/log/nginx/syncmyday-access.log;
    error_log /var/log/nginx/syncmyday-error.log;
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/syncmyday /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### 3B. Apache Configuration (alternative)

```bash
sudo nano /etc/apache2/sites-available/syncmyday.conf
```

```apache
<VirtualHost *:80>
    ServerName syncmyday.cz
    ServerAlias www.syncmyday.cz syncmyday.pl www.syncmyday.pl syncmyday.sk www.syncmyday.sk syncmyday.de www.syncmyday.de syncmyday.eu www.syncmyday.eu

    DocumentRoot /var/www/syncmyday/public

    <Directory /var/www/syncmyday/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/syncmyday-error.log
    CustomLog ${APACHE_LOG_DIR}/syncmyday-access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName syncmyday.cz
    ServerAlias www.syncmyday.cz syncmyday.pl www.syncmyday.pl syncmyday.sk www.syncmyday.sk syncmyday.de www.syncmyday.de syncmyday.eu www.syncmyday.eu

    DocumentRoot /var/www/syncmyday/public

    <Directory /var/www/syncmyday/public>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/syncmyday.cz/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/syncmyday.cz/privkey.pem

    ErrorLog ${APACHE_LOG_DIR}/syncmyday-ssl-error.log
    CustomLog ${APACHE_LOG_DIR}/syncmyday-ssl-access.log combined
</VirtualHost>
```

Enable and reload:

```bash
sudo a2ensite syncmyday
sudo a2enmod rewrite ssl
sudo systemctl reload apache2
```

---

### D) SSL Certificates ⏱️ 10-15 min

```bash
# Install Certbot (if not installed)
sudo apt install certbot python3-certbot-nginx  # for Nginx
# or
sudo apt install certbot python3-certbot-apache  # for Apache

# Generate certificates for ALL domains
sudo certbot --nginx \
  -d syncmyday.cz -d www.syncmyday.cz \
  -d syncmyday.pl -d www.syncmyday.pl \
  -d syncmyday.sk -d www.syncmyday.sk \
  -d syncmyday.de -d www.syncmyday.de \
  -d syncmyday.eu -d www.syncmyday.eu

# Or for Apache:
sudo certbot --apache -d syncmyday.cz -d www.syncmyday.cz ...

# Test auto-renewal
sudo certbot renew --dry-run
```

---

## 📋 PART 2: APPLICATION DEPLOYMENT

### 1. Deploy Code

```bash
cd /var/www/syncmyday
git pull origin main
```

### 2. Run Migration

```bash
php artisan migrate
```

This will:

- Add `registration_domain` column to `users` table
- Backfill existing users based on their locale

### 3. Update .env

```bash
nano .env
```

Add MXroute configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=bunny.mxroute.com
MAIL_PORT=587
MAIL_USERNAME=info@syncmyday.cz
MAIL_PASSWORD=your_mxroute_password
MAIL_ENCRYPTION=tls
MAIL_FROM_NAME="SyncMyDay"
```

### 4. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Test Email Sending

```bash
# Test from Czech domain
php artisan tinker
>>> $user = \App\Models\User::where('locale', 'cs')->first();
>>> Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
# Check email - should be from info@syncmyday.cz
>>> exit

# Test from Polish domain
php artisan tinker
>>> $user = \App\Models\User::where('locale', 'pl')->first();
>>> Mail::to($user->email)->send(new \App\Mail\WelcomeMail($user));
# Check email - should be from info@syncmyday.pl
>>> exit
```

---

## 🎯 HOW IT WORKS

### Registration Flow:

1. User visits `syncmyday.pl`
2. User registers → `registration_domain = "syncmyday.pl"` is saved
3. Welcome email sent from `info@syncmyday.pl` (auto-detected by EmailHelper)

### Email Sending Flow:

```php
// In any Mailable class:
public function envelope(): Envelope
{
    $emailConfig = \App\Helpers\EmailHelper::getEmailConfig($this->user, 'info');
    // Returns: ['address' => 'info@syncmyday.pl', 'name' => 'SyncMyDay']

    return new Envelope(
        from: new Address($emailConfig['address'], $emailConfig['name']),
        subject: __('emails.welcome_subject'),
    );
}
```

### Domain Mapping:

| Locale | Domain       | System Emails     | Blocker Emails      |
| ------ | ------------ | ----------------- | ------------------- |
| cs     | syncmyday.cz | info@syncmyday.cz | events@syncmyday.cz |
| sk     | syncmyday.sk | info@syncmyday.sk | events@syncmyday.sk |
| pl     | syncmyday.pl | info@syncmyday.pl | events@syncmyday.pl |
| de     | syncmyday.de | info@syncmyday.de | events@syncmyday.de |
| en     | syncmyday.eu | info@syncmyday.eu | events@syncmyday.eu |

---

## 🧪 TESTING CHECKLIST

### Infrastructure:

- [ ] All 5 domains resolve to VPS IP
- [ ] SSL certificates work for all domains
- [ ] MX records point to MXroute
- [ ] SPF/DKIM/DMARC records are set
- [ ] All 10 mailboxes created in MXroute

### Application:

- [ ] Migration ran successfully
- [ ] Existing users have `registration_domain` set
- [ ] New registrations save `registration_domain`
- [ ] System emails (info@) work from each domain
- [ ] Blocker emails (events@) work from each domain
- [ ] Email locale matches user's language

### Test Scenarios:

**1. New Czech user:**

```
- Register on syncmyday.cz
- Check: welcome email from info@syncmyday.cz
- Create sync rule
- Check: blocker email from events@syncmyday.cz
```

**2. New Polish user:**

```
- Register on syncmyday.pl
- Check: welcome email from info@syncmyday.pl
- Password reset request
- Check: reset email from info@syncmyday.pl
```

**3. OAuth user:**

```
- Login with Google on syncmyday.sk
- Check: registration_domain = "syncmyday.sk"
- Check: welcome email from info@syncmyday.sk
```

---

## 🐛 TROUBLESHOOTING

### Email not sending:

1. Check SMTP credentials in `.env`
2. Check MXroute server status
3. Check Laravel logs: `tail -f storage/logs/laravel.log`
4. Test SMTP connection:

```bash
telnet bunny.mxroute.com 587
```

### Wrong FROM address:

1. Check user's `registration_domain`:

```bash
php artisan tinker
>>> User::find(123)->registration_domain
```

2. Clear config cache:

```bash
php artisan config:clear
```

### SPF/DKIM failures:

1. Verify DNS records are propagated:

```bash
dig syncmyday.cz TXT
dig default._domainkey.syncmyday.cz TXT
```

2. Test email deliverability: https://www.mail-tester.com/

### SSL certificate issues:

```bash
# Regenerate certificates
sudo certbot renew --force-renewal

# Check certificate expiry
sudo certbot certificates
```

---

## 📞 SUPPORT

For issues:

1. Check `storage/logs/laravel.log`
2. Check MXroute DirectAdmin logs
3. Check web server error logs: `/var/log/nginx/error.log` or `/var/log/apache2/error.log`

---

## 🎉 SUCCESS!

You now have a professional multi-domain email setup where:

- ✅ Each user receives emails from their registration domain
- ✅ System emails come from `info@{domain}`
- ✅ Calendar blockers come from `events@{domain}`
- ✅ All emails are properly authenticated (SPF/DKIM/DMARC)
- ✅ Single codebase, single database, multiple domains
