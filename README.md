# SyncMyDay - Calendar Synchronization SaaS

Privacy-first calendar synchronization service that automatically creates "busy blockers" across multiple calendar accounts to prevent double-booking.

## 📋 Table of Contents

- [Features](#features)
- [Architecture](#architecture)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Local Setup](#local-setup)
- [OAuth Configuration](#oauth-configuration)
- [Environment Variables](#environment-variables)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [Deployment](#deployment)
- [Security](#security)
- [License](#license)

## ✨ Features

### Core Functionality

- **Multi-Account Sync**: Connect Google and Microsoft 365 calendars
- **Automatic Blockers**: Creates private "busy" events without details
- **Real-time Sync**: Webhook-based change detection (minutes, not hours)
- **Smart Filters**: Sync only busy events, ignore all-day, work hours filtering
- **Bi-directional Sync**: Optional two-way synchronization
- **Privacy First**: Stores only event IDs and times, never titles or details

### User Experience

- **Mobile-First UI**: Responsive design with Tailwind CSS
- **3-Step Onboarding**: Connect → Select → Sync
- **Multi-Language**: Czech, Slovak, Polish, English
- **Domain-based Localization**: Auto-detect language from domain

### Technical

- **Encrypted Tokens**: OAuth tokens encrypted at rest with Sodium
- **Loop Prevention**: Transaction IDs prevent infinite sync loops
- **Auto-renewal**: Webhook subscriptions auto-renew before expiration
- **Health Monitoring**: Admin dashboard with system metrics
- **Queue System**: Async processing with Redis/database queues

## 🏗 Architecture

### Components

```
┌─────────────┐     OAuth      ┌──────────────┐
│   Browser   │◄──────────────►│  Google API  │
│    (User)   │                 │ Microsoft API│
└──────┬──────┘                 └──────┬───────┘
       │                               │
       │ HTTPS                         │ Webhooks
       │                               │
┌──────▼────────────────────────────┬──▼─────────┐
│         Web Server (Nginx)        │  Webhooks  │
│                                   │  Endpoint  │
└───────────────┬───────────────────┴────────────┘
                │
      ┌─────────▼─────────┐
      │  Laravel App      │
      │  (PHP 8.2+)       │
      │                   │
      │  - Controllers    │
      │  - Services       │
      │  - Models         │
      └───────┬─────┬─────┘
              │     │
     ┌────────▼─┐ ┌─▼──────────┐
     │  MySQL  │ │   Redis    │
     │Database │ │  (Cache +  │
     │         │ │   Queue)   │
     └─────────┘ └─────┬──────┘
                       │
              ┌────────▼────────┐
              │  Queue Workers  │
              │                 │
              │ - Sync Engine   │
              │ - Webhook Jobs  │
              │ - Renewals      │
              └─────────────────┘
```

### Data Flow

1. **User Creates Sync Rule** → Stored in DB → Webhook subscription created
2. **Event Created in Source Calendar** → Provider sends webhook → Job queued
3. **Worker Processes Job** → Fetches event details → Applies filters → Creates blocker in targets
4. **Blocker Created** → Marked with transaction ID → Logged to sync_logs

### Database Schema

**Key Tables:**

- `users` - User accounts with subscription tier
- `calendar_connections` - OAuth tokens (encrypted) and calendar metadata
- `sync_rules` - Synchronization rules with filters
- `sync_rule_targets` - Many-to-many: rules to target calendars
- `webhook_subscriptions` - Active webhook channels with expiry
- `sync_logs` - Minimal sync history (no sensitive data)

## 🛠 Tech Stack

- **Backend**: PHP 8.2+, Laravel 10
- **Frontend**: Blade templates, Tailwind CSS (mobile-first)
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis
- **APIs**: Google Calendar API, Microsoft Graph API
- **Payments**: Stripe (Checkout + Customer Portal)
- **Deployment**: Docker, Docker Compose

## 📦 Prerequisites

- Docker & Docker Compose (recommended) OR:
  - PHP 8.2+
  - Composer
  - MySQL 8.0+
  - Redis
  - Node.js (optional, for asset building)

## 🚀 Local Setup

### 1. Clone Repository

```bash
git clone <repository-url>
cd SyncMyDay
```

### 2. Copy Environment File

```bash
cp .env.example .env
```

### 3. Generate Encryption Keys

```bash
# Generate Laravel app key
php artisan key:generate

# Generate token encryption key (32 bytes, base64 encoded)
php -r "echo 'TOKEN_ENCRYPTION_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

Add the generated token encryption key to your `.env` file.

### 4. Install Dependencies (if not using Docker)

```bash
composer install
```

### 5. Configure Environment

Edit `.env` with your settings (see [Environment Variables](#environment-variables) below).

### 6. Start with Docker Compose

```bash
docker-compose up -d
```

This starts:

- `app` - PHP-FPM container
- `nginx` - Web server (port 8000)
- `mysql` - Database (port 3307)
- `redis` - Cache/queue (port 6380)
- `worker` - Queue worker
- `scheduler` - Cron scheduler
- `mailhog` - Mail testing (port 8025)

### 7. Run Migrations

```bash
docker-compose exec app php artisan migrate
```

### 8. Access Application

- **App**: http://localhost:8000
- **Mailhog**: http://localhost:8025
- **Health Check**: http://localhost:8000/health

## 🔐 OAuth Configuration

### Google Calendar API

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project or select existing
3. Enable **Google Calendar API**
4. Create OAuth 2.0 credentials:
   - Application type: Web application
   - Authorized redirect URIs: `http://localhost:8000/oauth/google/callback`
5. Add scopes:
   - `https://www.googleapis.com/auth/calendar`
   - `https://www.googleapis.com/auth/calendar.events`
6. Copy **Client ID** and **Client Secret** to `.env`:

```env
GOOGLE_CLIENT_ID=your-client-id
GOOGLE_CLIENT_SECRET=your-client-secret
```

### Microsoft Graph API

1. Go to [Azure Portal](https://portal.azure.com/)
2. Navigate to **App registrations** → **New registration**
3. Name: "SyncMyDay"
4. Redirect URI: `http://localhost:8000/oauth/microsoft/callback`
5. After creation, go to **Certificates & secrets** → New client secret
6. Go to **API permissions** → Add permission → Microsoft Graph:
   - `Calendars.ReadWrite` (Delegated)
   - `offline_access` (Delegated)
7. Copy **Application (client) ID** and **Client Secret** to `.env`:

```env
MICROSOFT_CLIENT_ID=your-client-id
MICROSOFT_CLIENT_SECRET=your-client-secret
MICROSOFT_TENANT=common
```

### Webhook Configuration

For webhooks to work, you need a publicly accessible URL:

#### Development (Using ngrok)

```bash
# Install ngrok: https://ngrok.com/
ngrok http 8000

# Update .env with ngrok URL
APP_URL=https://your-subdomain.ngrok.io
WEBHOOK_BASE_URL=https://your-subdomain.ngrok.io/webhooks
```

**Important**: Update OAuth redirect URIs in Google/Microsoft consoles to use ngrok URL.

#### Production

Set your production domain:

```env
APP_URL=https://syncmyday.app
WEBHOOK_BASE_URL=https://syncmyday.app/webhooks
```

## ⚙️ Environment Variables

### Essential Configuration

```env
# Application
APP_NAME=SyncMyDay
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=syncmyday
DB_USERNAME=syncmyday
DB_PASSWORD=secret

# Redis
REDIS_HOST=redis
REDIS_PORT=6379
QUEUE_CONNECTION=redis

# Token Encryption (REQUIRED!)
TOKEN_ENCRYPTION_KEY=base64:...

# Google OAuth
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...

# Microsoft OAuth
MICROSOFT_CLIENT_ID=...
MICROSOFT_CLIENT_SECRET=...
MICROSOFT_TENANT=common

# Stripe (for billing)
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
STRIPE_WEBHOOK_SECRET=whsec_...
STRIPE_PRO_PRICE_ID=price_...

# Multi-Domain Localization
DEFAULT_LOCALE=en
DOMAIN_LOCALES='{"syncmyday.cz":"cs","syncmyday.sk":"sk","syncmyday.pl":"pl","syncmyday.eu":"en"}'
```

## 🏃 Running the Application

### With Docker (Recommended)

```bash
# Start all services
docker-compose up -d

# View logs
docker-compose logs -f

# Run artisan commands
docker-compose exec app php artisan ...

# Run queue worker manually
docker-compose exec app php artisan queue:work

# Run tests
docker-compose exec app php artisan test
```

### Without Docker

```bash
# Start PHP server
php artisan serve

# Start queue worker (separate terminal)
php artisan queue:work

# Start scheduler (separate terminal or add to cron)
php artisan schedule:work
```

## 🧪 Testing

### Run All Tests

```bash
docker-compose exec app php artisan test
```

### Run Specific Test Suite

```bash
# Unit tests
docker-compose exec app php artisan test --testsuite=Unit

# Feature tests
docker-compose exec app php artisan test --testsuite=Feature
```

### Test Coverage

```bash
docker-compose exec app php artisan test --coverage
```

### Manual Testing Flow

1. **Register** at http://localhost:8000/register
2. **Connect Google Calendar** via OAuth
3. **Connect Microsoft Calendar** via OAuth
4. **Create Sync Rule**: Source (Google) → Target (Microsoft)
5. **Create test event** in source Google calendar
6. **Verify blocker** appears in Microsoft calendar within minutes

## 🌍 Deployment

### Production Checklist

- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Generate strong `APP_KEY` and `TOKEN_ENCRYPTION_KEY`
- [ ] Use production database with backups
- [ ] Configure Redis with persistence
- [ ] Set up SSL/TLS (force HTTPS)
- [ ] Configure real OAuth credentials for production domains
- [ ] Set up Stripe production keys
- [ ] Configure mail service (not Mailhog)
- [ ] Set up monitoring (logs, health checks)
- [ ] Run `php artisan config:cache` and `php artisan route:cache`
- [ ] Set up queue supervisor for workers
- [ ] Configure cron for `php artisan schedule:run`

### Docker Production Build

```bash
# Build optimized image
docker build -f docker/Dockerfile.prod -t syncmyday:latest .

# Run with production compose
docker-compose -f docker-compose.prod.yml up -d
```

### Queue Supervisor (for VPS deployment)

```ini
[program:syncmyday-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/worker.log
stopwaitsecs=3600
```

## 🔒 Security

### Implemented Security Measures

1. **Token Encryption**: OAuth tokens encrypted with Sodium (libsodium)
   - Uses `sodium_crypto_secretbox` with random nonces
   - Separate encryption key from Laravel APP_KEY
2. **Minimal Data Storage**: Only event IDs and timestamps stored, never titles or content

3. **CSRF Protection**: Enabled on all state-changing routes

4. **XSS Protection**: Blade templates auto-escape output

5. **Secure Headers**: X-Frame-Options, X-Content-Type-Options, etc.

6. **Password Hashing**: Bcrypt with configurable rounds

7. **Rate Limiting**: Applied to webhooks and API routes

8. **Sensitive Data Masking**: Error logs mask tokens and secrets

### Token Encryption

Tokens are encrypted using the following flow:

```php
// Encryption
$nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
$ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $key);
$encrypted = base64_encode($nonce . $ciphertext);

// Decryption
$decoded = base64_decode($encrypted);
$nonce = substr($decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
$ciphertext = substr($decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
$plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
```

**Key Management**:

- Development: Store in `.env`
- Production: Use secret manager (AWS Secrets Manager, HashiCorp Vault, etc.)
- Never commit encryption keys to version control

### GDPR Compliance

- Users can delete their account (soft delete)
- Deleting account removes all calendar connections and sync rules
- Sync logs contain no personal event data
- Privacy policy template included (customize for production)

## 📚 Additional Documentation

### Admin Panel

Access admin panel at `/admin` (requires `is_admin = true` on user):

- User management
- Connection monitoring
- Webhook subscription status
- Sync logs
- System health metrics

### Artisan Commands

```bash
# Renew expiring webhook subscriptions
php artisan webhooks:renew

# Clean old sync logs (default: 30 days)
php artisan logs:clean --days=30

# Check connection health
php artisan connections:check
```

### API Endpoints

- `GET /health` - System health check (JSON)
- `POST /webhooks/google/{connectionId}` - Google webhook receiver
- `POST /webhooks/microsoft/{connectionId}` - Microsoft webhook receiver
- `POST /webhooks/stripe` - Stripe webhook receiver

## 🐛 Troubleshooting

### Webhooks Not Working

1. Check webhook URL is publicly accessible
2. Verify SSL certificate is valid (webhooks require HTTPS in production)
3. Check logs: `storage/logs/webhook.log`
4. Manually trigger sync: `php artisan queue:work --once`

### Token Refresh Failures

1. Ensure `TOKEN_ENCRYPTION_KEY` hasn't changed
2. Check `refresh_token` is stored for connection
3. Verify OAuth scopes include `offline_access` (Microsoft)
4. Re-authenticate connection if tokens are corrupted

### Queue Not Processing

1. Check Redis is running: `redis-cli ping`
2. Verify worker is running: `docker-compose ps worker`
3. Check failed jobs: `php artisan queue:failed`
4. Retry failed jobs: `php artisan queue:retry all`

## 📝 License

Proprietary. All rights reserved.

## 🤝 Contributing

This is an MVP. For production enhancements, consider:

- [ ] Event deletion tracking (map source events to created blockers)
- [ ] Recurring event support (currently basic)
- [ ] More filter options (categories, keywords)
- [ ] Email notifications for sync errors
- [ ] Mobile app
- [ ] More calendar providers (Apple, Outlook.com)
- [ ] Advanced scheduling rules
- [ ] Team/organization support

## 📞 Support

For issues or questions:

- Check logs in `storage/logs/`
- Review health endpoint: `/health`
- Contact: support@syncmyday.app

---

Built with ❤️ for privacy-conscious calendar users.
