# Account Re-registration After Deletion

## Problem

When a user deleted their SyncMyDay account and later tried to re-register with the same email address, the registration would fail. This was because:

1. The `User` model uses **SoftDeletes** - deleted accounts remain in the database with a `deleted_at` timestamp
2. The email field has a **unique constraint** in the database
3. Even though the account was soft-deleted, the unique email constraint prevented re-registration

This affected both:

- **Regular email/password registration** - validation would fail
- **OAuth registration** (Google/Microsoft) - checks for existing email would find the deleted account

## Solution Implemented

**Anonymization of unique fields before soft delete**

When a user deletes their account, we now anonymize all unique identifier fields BEFORE the soft delete. This frees up the original values (email, OAuth IDs, Stripe customer ID) for future re-registration.

### Changes Made

#### 1. AccountController::destroy()

Added anonymization before soft delete:

```php
// Anonymize unique fields before soft delete to allow re-registration with same email
// This frees up the email, OAuth provider ID, and Stripe customer ID for future use
$user->update([
    'email' => 'deleted_' . $user->id . '_' . time() . '@deleted.syncmyday.local',
    'stripe_customer_id' => $user->stripe_customer_id ? 'deleted_' . $user->stripe_customer_id : null,
    'oauth_provider_id' => null,
    'oauth_provider_email' => null,
]);

// Soft delete user
$user->delete();
```

**File:** `app/Http/Controllers/AccountController.php` (lines 247-254)

#### 2. SocialAuthController (Google & Microsoft OAuth)

Added explicit check to ignore soft-deleted users when checking for existing emails:

**Google OAuth registration:**

```php
// Check if email already exists with different provider (ignore soft-deleted)
$existingUser = User::where('email', $googleEmail)
                    ->whereNull('deleted_at')
                    ->first();
```

**File:** `app/Http/Controllers/Auth/SocialAuthController.php` (lines 136-139, 320-323)

#### 3. AuthController (Regular Registration)

Made the email unique validation explicitly ignore soft-deleted users:

```php
'email' => [
    'required',
    'string',
    'email',
    'max:255',
    Rule::unique('users')->whereNull('deleted_at'),
],
```

**File:** `app/Http/Controllers/Auth/AuthController.php` (lines 31-37)

## How It Works Now

### When a user deletes their account:

1. Stripe subscription is cancelled and customer is deleted (if exists)
2. User is logged out
3. **Unique fields are anonymized:**
   - `email` → `deleted_{user_id}_{timestamp}@deleted.syncmyday.local`
   - `stripe_customer_id` → `deleted_{original_customer_id}` or `null`
   - `oauth_provider_id` → `null`
   - `oauth_provider_email` → `null`
4. Account is soft-deleted (remains in DB with `deleted_at` timestamp)
5. Confirmation email is sent to the original email address

### When a user re-registers with the same email:

1. **Regular registration:** Validation checks `unique:users` with `whereNull('deleted_at')` - passes ✅
2. **OAuth registration:** Checks for existing email with `whereNull('deleted_at')` - not found ✅
3. New account is created as a completely fresh user with trial period
4. All previous data remains in the old (soft-deleted + anonymized) account for audit purposes

## Benefits

✅ **Users can re-register** with the same email after deleting their account
✅ **Fresh start** - new account has no connection to the old one
✅ **Audit trail preserved** - old account data remains in database (but anonymized)
✅ **GDPR compliant** - personal data (email, OAuth IDs) is anonymized
✅ **No Stripe conflicts** - customer ID is also anonymized/cleared
✅ **Works for all registration methods:**

- Email/password registration
- Google OAuth registration
- Microsoft OAuth registration

## Technical Notes

### Why anonymization instead of hard delete?

1. **Audit trail** - we can see that an account existed and was deleted
2. **Data integrity** - related records (logs, etc.) remain referentially valid
3. **Analytics** - we can track account deletions and re-registrations
4. **GDPR compliance** - personal identifiable information is removed while keeping non-personal metadata

### Why not restore the old account?

Restoring would be more complex:

- Would need to clean up all related data (connections, sync rules, mappings, etc.)
- Potential Stripe conflicts with same user ID
- User expects a "fresh start" - not their old account restored
- More edge cases to handle

### Fields that get anonymized:

| Field                  | Original Value     | After Anonymization                              |
| ---------------------- | ------------------ | ------------------------------------------------ |
| `email`                | `user@example.com` | `deleted_123_1234567890@deleted.syncmyday.local` |
| `stripe_customer_id`   | `cus_ABC123`       | `deleted_cus_ABC123` or `null`                   |
| `oauth_provider_id`    | `google_id_xyz`    | `null`                                           |
| `oauth_provider_email` | `user@gmail.com`   | `null`                                           |

## Testing Checklist

To verify this works correctly:

- [ ] User with email/password can delete account
- [ ] Same user can re-register with same email
- [ ] New account has fresh trial period
- [ ] No data from old account appears in new account
- [ ] User with Google OAuth can delete account
- [ ] Same user can re-register via Google OAuth with same email
- [ ] User with Microsoft OAuth can delete account
- [ ] Same user can re-register via Microsoft OAuth with same email
- [ ] Old account data remains in database (but anonymized)
- [ ] Stripe customer ID is freed up for re-use

## Related Files

- `app/Http/Controllers/AccountController.php` - Account deletion logic
- `app/Http/Controllers/Auth/AuthController.php` - Regular registration
- `app/Http/Controllers/Auth/SocialAuthController.php` - OAuth registration
- `app/Models/User.php` - User model (uses SoftDeletes trait)
- `database/migrations/2024_01_01_000001_create_users_table.php` - Users table structure

## Date Implemented

October 19, 2025
