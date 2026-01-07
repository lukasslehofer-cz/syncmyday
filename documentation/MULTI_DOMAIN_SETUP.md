# Multi-Domain Setup Documentation

## Overview

SyncMyDay supports operation under multiple national domains (e.g., syncmyday.cz, syncmyday.pl, syncmyday.sk, syncmyday.de) while maintaining a single codebase, database, and OAuth configuration.

## Architecture

### Key Features

1. **Single Codebase & Database**: All domains point to the same application and database
2. **Domain-Based Localization**: Language is automatically set based on the domain
3. **OAuth Proxy Redirect**: OAuth callbacks from all domains are proxied to the primary domain
4. **Centralized OAuth Configuration**: OAuth apps are configured only on the primary domain (.cz)

### How It Works

#### 1. Domain-Based Localization

**Middleware**: `App\Http\Middleware\SetLocaleFromDomain`

When a user visits any domain:

- The middleware checks the domain against `config('app.domain_locales')`
- Sets the appropriate locale (cs, sk, pl, de, etc.)
- All content is displayed in the correct language using `__('messages.key')` helpers

**Configuration** (`config/app.php`):

```php
'domain_locales' => [
    'syncmyday.cz' => 'cs',
    'syncmyday.pl' => 'pl',
    'syncmyday.sk' => 'sk',
    'syncmyday.de' => 'de',
],
```

#### 2. OAuth Proxy Redirect

**Problem**: OAuth applications (Google, Microsoft) require exact callback URLs. We cannot register hundreds of potential national domains.

**Solution**: Hardcoded Primary Domain in OAuth Configuration

**Flow**:

1. User clicks "Continue with Google" on `syncmyday.pl`
2. Application redirects to Google with `redirect_uri=https://syncmyday.cz/auth/google/callback` (hardcoded primary domain)
3. User authenticates with Google
4. Google redirects back to: `https://syncmyday.cz/auth/google/callback?code=...` (as configured)
5. OAuth processing completes on .cz domain
6. User is logged in with locale set based on their original domain preference

**Benefits**:

- Only need to configure OAuth callbacks for one domain (.cz)
- Users can access the app from any national domain
- OAuth redirect URI is always correct (matches what's registered in Google/Microsoft)
- No additional redirects needed - cleaner flow
- **Fallback middleware** (`OAuthProxyRedirect`) catches any edge cases

**Note**: The `OAuthProxyRedirect` middleware acts as a safety net in case an OAuth callback somehow arrives on a non-primary domain, but in normal operation it should not be triggered.

## Configuration

### Environment Variables

Add to your `.env`:

```env
# OAuth Primary Domain (where OAuth apps are configured)
OAUTH_PRIMARY_DOMAIN=syncmyday.cz

# Domain to Locale Mapping (JSON format)
DOMAIN_LOCALES='{"syncmyday.cz":"cs","syncmyday.sk":"sk","syncmyday.pl":"pl","syncmyday.eu":"en"}'

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Microsoft OAuth
MICROSOFT_CLIENT_ID=your_microsoft_client_id
MICROSOFT_CLIENT_SECRET=your_microsoft_client_secret
MICROSOFT_TENANT=common
```

**Important Notes**:

- OAuth redirect URIs are now **hardcoded in `config/services.php`** using the `OAUTH_PRIMARY_DOMAIN`
- You **no longer need** `GOOGLE_REDIRECT_URI` or `MICROSOFT_REDIRECT_URI` environment variables
- `APP_URL` can be any domain - it won't affect OAuth redirects

### OAuth Provider Configuration

#### Google Cloud Console

**Authorized Redirect URIs**:

```
https://syncmyday.cz/oauth/google/callback
https://syncmyday.cz/auth/google/callback
```

#### Microsoft Azure Portal

**Redirect URIs**:

```
https://syncmyday.cz/oauth/microsoft/callback
https://syncmyday.cz/auth/microsoft/callback
```

**Note**: You do NOT need to add syncmyday.pl, .sk, .de etc. to OAuth providers. The proxy middleware handles this automatically.

### DNS Configuration

All national domains should point to the same server/application:

```
syncmyday.cz    A    your.server.ip
syncmyday.pl    A    your.server.ip
syncmyday.sk    A    your.server.ip
syncmyday.de    A    your.server.ip
```

### Web Server Configuration

#### Nginx Example

```nginx
server {
    listen 80;
    listen 443 ssl http2;

    server_name syncmyday.cz syncmyday.pl syncmyday.sk syncmyday.de;

    # SSL certificates (use wildcard or multiple certificates)
    ssl_certificate /path/to/ssl/fullchain.pem;
    ssl_certificate_key /path/to/ssl/privkey.pem;

    root /var/www/syncmyday/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## Adding New Domains

To add a new national domain (e.g., syncmyday.fr for France):

### 1. Update Configuration

Add to `config/app.php`:

```php
'domain_locales' => [
    // ... existing domains
    env('DOMAIN_FR', 'syncmyday.fr') => 'fr',
],
```

### 2. Create Translation Files

Create language files:

```
lang/fr/messages.php
lang/fr/auth.php
```

### 3. Update Environment

Add to `.env`:

```env
DOMAIN_FR=syncmyday.fr
```

### 4. Configure DNS

Point the new domain to your server:

```
syncmyday.fr    A    your.server.ip
```

### 5. Update Web Server

Add domain to server configuration:

```nginx
server_name syncmyday.cz syncmyday.pl syncmyday.sk syncmyday.de syncmyday.fr;
```

### 6. SSL Certificate

Add SSL certificate for the new domain (consider using Let's Encrypt with wildcard certificates).

**That's it!** No changes needed to OAuth configuration.

## Stripe Integration

Currently using a single Stripe account for all domains. This is configured in `.env`:

```env
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
```

### Future: Multiple Stripe Accounts

If you need different Stripe accounts per region (e.g., for local payment methods or tax purposes):

1. Add domain-based Stripe configuration
2. Update billing controller to use appropriate Stripe account based on user's domain
3. Consider storing user's "home domain" in database

## Testing

### Local Testing

1. Add entries to `/etc/hosts`:

```
127.0.0.1 syncmyday.cz
127.0.0.1 syncmyday.pl
127.0.0.1 syncmyday.sk
```

2. Set `APP_URL=http://syncmyday.cz` in `.env`

3. Test OAuth flow:
   - Visit `http://syncmyday.pl`
   - Click "Continue with Google"
   - Verify redirect to `.cz` domain for callback
   - Verify redirect back to `.pl` after auth

### Production Testing

1. Test each domain loads correctly with appropriate language
2. Test OAuth flow from each domain
3. Verify users can switch between domains seamlessly
4. Check that webhooks and scheduled tasks work correctly

## Troubleshooting

### OAuth Fails with "Redirect URI Mismatch"

**Cause**: OAuth callback is not being proxied to primary domain.

**Solution**:

1. Check `OAuthProxyRedirect` middleware is registered in `Kernel.php`
2. Verify `OAUTH_PRIMARY_DOMAIN` is set correctly in `.env`
3. Check Google/Microsoft OAuth console has correct redirect URIs

### Wrong Language Displayed

**Cause**: Domain-to-locale mapping incorrect or missing.

**Solution**:

1. Check `config/app.php` has correct `domain_locales` mapping
2. Verify `SetLocaleFromDomain` middleware is active
3. Check translation files exist for that locale

### Session Issues After OAuth

**Cause**: Session cookies not being shared across domains.

**Solution**: This is expected behavior. Each domain has separate sessions, which is actually more secure. The OAuth flow handles this correctly.

## Security Considerations

1. **CSRF Protection**: Enabled on all routes
2. **SSL/HTTPS**: Required for production (OAuth providers require HTTPS)
3. **Session Security**: Separate sessions per domain (more secure)
4. **OAuth State**: Preserved during proxy redirect
5. **Rate Limiting**: Consider implementing rate limiting on OAuth routes

## Performance

- **Minimal Overhead**: Proxy redirect adds ~50-100ms to OAuth flow (one-time per login)
- **Caching**: Consider caching translations for better performance
- **CDN**: Can use CDN for static assets across all domains

## Future Enhancements

### Planned Features

1. **In-App Language Switching**: Allow users to override domain-based locale
2. **User Preference Storage**: Remember user's language preference
3. **Regional Pricing**: Different Stripe prices per region
4. **Regional Payment Methods**: Support local payment methods per country
5. **GeoIP Redirect**: Auto-redirect users to their local domain based on IP

### Scalability

Current architecture supports:

- Unlimited domains
- Any number of locales
- Single database for all users
- Horizontal scaling (multiple app servers)

## Support

For issues or questions:

- Check logs: `storage/logs/laravel.log`
- Enable debug mode: `APP_DEBUG=true` (development only!)
- Review middleware logic in `app/Http/Middleware/`

## Changelog

### Version 1.0 (October 2025)

- Initial multi-domain support
- OAuth proxy redirect implementation
- Domain-based localization
- Support for CZ, PL, SK, DE domains
