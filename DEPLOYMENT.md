# Deployment Guide

## Quick Deployment Steps

### 1. Server Requirements

- Ubuntu 20.04+ or similar Linux distribution
- Docker & Docker Compose installed
- Domain with DNS pointing to server
- SSL certificate (Let's Encrypt recommended)

### 2. Clone & Configure

```bash
# Clone repository
git clone <repository-url> /var/www/syncmyday
cd /var/www/syncmyday

# Copy environment file
cp .env.example .env

# Edit configuration
nano .env
```

### 3. Generate Keys

```bash
# Generate Laravel application key
docker-compose run --rm app php artisan key:generate

# Generate token encryption key
docker-compose run --rm app php -r "echo 'TOKEN_ENCRYPTION_KEY=base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

### 4. Configure OAuth

Update OAuth redirect URIs in Google Cloud Console and Azure Portal to use your production domain:

- Google: `https://yourdomain.com/oauth/google/callback`
- Microsoft: `https://yourdomain.com/oauth/microsoft/callback`

### 5. Deploy

```bash
# Start services
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --force

# Cache configuration
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### 6. Set Up SSL

Use Let's Encrypt with Certbot:

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Get certificate
sudo certbot --nginx -d yourdomain.com

# Auto-renewal is configured automatically
```

### 7. Monitor

- Check health: `https://yourdomain.com/health`
- View logs: `docker-compose logs -f`
- Monitor queue: `docker-compose exec app php artisan queue:monitor`

## Production Environment Variables

Critical settings for production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Strong keys (never reuse from .env.example!)
APP_KEY=base64:...
TOKEN_ENCRYPTION_KEY=base64:...

# Production database
DB_HOST=your-db-host
DB_DATABASE=syncmyday_prod
DB_USERNAME=syncmyday_prod
DB_PASSWORD=strong-random-password

# Production Redis
REDIS_HOST=your-redis-host
REDIS_PASSWORD=strong-random-password

# Stripe production keys
STRIPE_KEY=pk_live_...
STRIPE_SECRET=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...

# Mail service (e.g., SendGrid, Mailgun)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-api-key
```

## Backup Strategy

### Database Backup

```bash
# Daily backup script
docker-compose exec mysql mysqldump -u syncmyday -p syncmyday > backup_$(date +%Y%m%d).sql

# Restore from backup
docker-compose exec -T mysql mysql -u syncmyday -p syncmyday < backup_20240101.sql
```

### Automated Backups

Add to crontab:

```bash
# Daily database backup at 2 AM
0 2 * * * cd /var/www/syncmyday && docker-compose exec -T mysql mysqldump -u syncmyday -p syncmyday | gzip > /backups/syncmyday_$(date +\%Y\%m\%d).sql.gz
```

## Monitoring

### Log Monitoring

```bash
# Application logs
tail -f storage/logs/laravel.log

# Sync logs
tail -f storage/logs/sync.log

# Webhook logs
tail -f storage/logs/webhook.log

# Queue logs
docker-compose logs -f worker
```

### Performance Monitoring

Consider adding:

- **Sentry** for error tracking
- **New Relic** or **DataDog** for APM
- **Uptime monitoring** (UptimeRobot, Pingdom)
- **Log aggregation** (Loggly, Papertrail)

## Scaling

### Horizontal Scaling

1. **Multiple Workers**: Increase worker count in docker-compose.yml
2. **Load Balancer**: Use nginx or cloud load balancer
3. **Separate Services**: Run database and Redis on separate servers
4. **CDN**: Serve static assets via CDN

### Database Optimization

- Enable query caching
- Add indexes for common queries
- Regular OPTIMIZE TABLE maintenance
- Consider read replicas for heavy traffic

## Security Hardening

1. **Firewall**: Only expose ports 80, 443
2. **Fail2ban**: Protect against brute force
3. **Regular Updates**: Keep Docker images and packages updated
4. **Secrets Management**: Use AWS Secrets Manager or Vault in production
5. **Rate Limiting**: Configure aggressive rate limits on public endpoints

## Rollback Procedure

If deployment fails:

```bash
# Stop services
docker-compose down

# Restore database backup
docker-compose exec -T mysql mysql -u syncmyday -p syncmyday < backup_latest.sql

# Checkout previous version
git checkout <previous-tag>

# Restart services
docker-compose up -d
```

## Common Issues

### Queue Not Processing

```bash
# Restart worker
docker-compose restart worker

# Check failed jobs
docker-compose exec app php artisan queue:failed

# Retry failed jobs
docker-compose exec app php artisan queue:retry all
```

### Webhook Subscriptions Expired

```bash
# Manually renew all subscriptions
docker-compose exec app php artisan webhooks:renew
```

### High Memory Usage

```bash
# Clear all caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

## Support & Maintenance

- Regular security updates
- Monitor error rates
- Check queue length
- Review sync logs for failures
- Update OAuth tokens if expired
