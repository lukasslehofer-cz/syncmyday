# Multi-Domain Locale Configuration

## Overview

SyncMyDay supports multi-domain deployment with different language configurations per domain. Each domain can have:
- A default language
- A list of available languages for users to choose from

## Configuration

All locale configuration is centralized in `config/locales.php`.

### Domain Configuration Structure

```php
'domains' => [
    'syncmyday.cz' => [
        'default' => 'cs',
        'available' => ['cs', 'sk', 'en'],
    ],
    'syncmyday.sk' => [
        'default' => 'sk',
        'available' => ['sk', 'cs', 'en'],
    ],
    // ... more domains
],
```

### Configuration Options

- **`default`**: The default language for the domain. Used for:
  - New user registration (if browser language not detected)
  - Guest visitors
  - Users who haven't selected a language preference

- **`available`**: Array of language codes that users can select from on the `/account` page. Only these languages will appear in the language selector for users on this domain.

### Fallback Configuration

If a domain is not found in the configuration, the fallback configuration is used:

```php
'fallback' => [
    'default' => 'en',
    'available' => ['en', 'cs', 'sk', 'de', 'pl'],
],
```

## How It Works

### Language Priority Order

1. **User's Saved Preference** (highest priority)
   - If authenticated user has selected a language in `/account`
   - Only applied if the language is available for the current domain
   
2. **Domain Default**
   - Configured in `config/locales.php` for each domain
   - Used when user has no preference or their preference is not available

3. **Browser Detection** (during registration only)
   - Detected via JavaScript on registration page
   - Set as user's initial preference

### Cross-Domain Behavior

When a user switches domains (e.g., from syncmyday.cz to syncmyday.sk):
- If their saved language preference is available on the new domain, it's used
- If their saved language is NOT available, they're automatically switched to the new domain's default language
- Their preference in the database is updated to the new domain's default

Example:
- User has Polish (`pl`) selected on syncmyday.eu
- They visit syncmyday.cz (which only offers `cs`, `sk`, `en`)
- System automatically switches them to Czech (`cs`) and updates their preference

## Implementation Files

### Core Files

1. **`config/locales.php`**
   - Central configuration for all domains
   - Define available languages per domain

2. **`app/Helpers/LocaleHelper.php`**
   - Helper class with static methods
   - `getDomainConfig()` - get configuration for current domain
   - `getAvailableLocales()` - get array of available locale codes
   - `getAvailableLocalesWithNames()` - get associative array with display names
   - `isLocaleAvailable($locale)` - check if locale is available for current domain

3. **`app/Http/Middleware/SetLocaleFromDomain.php`**
   - Runs on every request
   - Sets application locale based on domain and user preference
   - Handles cross-domain language switching

4. **`app/Http/Controllers/AccountController.php`**
   - Displays only available languages for current domain
   - Validates language selection against available languages
   - Applies locale change immediately after saving

### Language Files

Translation files are stored in `lang/{locale}/messages.php`:
- `lang/en/messages.php` - English
- `lang/cs/messages.php` - Czech
- `lang/sk/messages.php` - Slovak
- `lang/de/messages.php` - German
- `lang/pl/messages.php` - Polish

## Adding a New Domain

To add a new domain with specific language configuration:

1. Open `config/locales.php`
2. Add new entry to the `domains` array:

```php
'domains' => [
    // ... existing domains
    
    'syncmyday.fr' => [
        'default' => 'fr',           // Default language
        'available' => ['fr', 'en'], // Available languages
    ],
],
```

3. Ensure translation files exist in `lang/{locale}/` directory
4. No code changes needed - configuration is automatically picked up

## Adding a New Language

To add a completely new language to the system:

1. Create new translation directory: `lang/{locale}/`
2. Copy `lang/en/messages.php` to `lang/{locale}/messages.php`
3. Translate all strings in the new file
4. Add language to `config/locales.php` in the `supported` array:

```php
'supported' => [
    'en' => 'English',
    'cs' => 'Čeština',
    // ... existing languages
    'fr' => 'Français', // New language
],
```

5. Add language to desired domain configurations

## Testing

### Test User Preference

1. Login to `/account`
2. Change language in the "Language & Timezone" section
3. Save changes
4. Verify language changes immediately
5. Navigate to different pages to confirm persistence

### Test Domain Default

1. Logout or use incognito mode
2. Visit different domains (e.g., syncmyday.cz vs syncmyday.sk)
3. Verify default language is different on each domain

### Test Cross-Domain Switching

1. Login with language preference set
2. Note your current language
3. Change domain in URL (e.g., from .cz to .sk)
4. Verify language switches appropriately:
   - Keeps your preference if available
   - Switches to domain default if not available

## API Helper Methods

```php
use App\Helpers\LocaleHelper;

// Get available locales for current domain
$locales = LocaleHelper::getAvailableLocales();
// Returns: ['cs', 'sk', 'en']

// Get available locales with display names
$localesWithNames = LocaleHelper::getAvailableLocalesWithNames();
// Returns: ['cs' => 'Čeština', 'sk' => 'Slovenčina', 'en' => 'English']

// Get default locale for current domain
$default = LocaleHelper::getDefaultLocale();
// Returns: 'cs'

// Check if locale is available
$isAvailable = LocaleHelper::isLocaleAvailable('pl');
// Returns: false (if on syncmyday.cz)

// Get all supported locales (regardless of domain)
$all = LocaleHelper::getAllSupportedLocales();
// Returns: ['en' => 'English', 'cs' => 'Čeština', ...]
```

## Environment Variables

No environment variables are required. All configuration is in `config/locales.php`.

If you want to make it environment-specific, you can use:

```php
'domains' => env('APP_ENV') === 'production' 
    ? [ /* production domains */ ]
    : [ /* development domains */ ],
```

But this is not necessary for normal operation.

