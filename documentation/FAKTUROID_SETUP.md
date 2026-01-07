# Fakturoid Integration Setup

This document explains how to set up the Fakturoid integration for automatic invoice generation in SyncMyDay.

## Overview

SyncMyDay is integrated with [Fakturoid](https://www.fakturoid.cz/), a Czech invoicing system that complies with Czech accounting laws. Every time a user makes a payment (non-zero amount), a Fakturoid invoice is automatically created with the following features:

- **Automatic generation**: Invoices are created via Stripe webhook when payment succeeds
- **Multi-language**: Invoices are generated in the user's language (cs, en, de, pl, sk)
- **VAT included**: All invoices include 21% DPH (Czech VAT)
- **Numbered series**: All invoices use the custom number format "SMD-YYYY-###" (e.g. SMD-2025-001)
- **Paid status**: Invoices are marked as paid immediately since payment is already processed
- **PDF download**: Users can download invoices from `/billing/manage` page

## Prerequisites

1. Active Fakturoid account
2. API access enabled in Fakturoid
3. Custom invoice number series created in Fakturoid

## Getting API Credentials

### Step 1: Log in to Fakturoid

Go to [app.fakturoid.cz](https://app.fakturoid.cz) and log in to your account.

### Step 2: Get Your Account Slug

The account slug is your account name visible in the URL:

```
https://app.fakturoid.cz/YOUR_ACCOUNT_SLUG/...
```

For example, if your URL is `https://app.fakturoid.cz/syncmyday/invoices`, your slug is `syncmyday`.

### Step 3: Get OAuth 2.0 Credentials

Fakturoid API v3 uses **OAuth 2.0 Client Credentials Flow**. You need to get credentials from your user account.

1. In Fakturoid, click your profile icon → **Nastavení** (Settings)
2. Go to **Uživatelský účet** (User account) tab
3. Scroll down to **API přístup** (API access) section
4. Click **"Stáhnout přihlašovací údaje"** (Download credentials) or **"Vygenerovat nové"** (Generate new)
5. You will receive:
   - **Client ID**: e.g. `e408067c409fe6557a088c63539abbdb96fcc939`
   - **Client Secret**: e.g. `31d475d1fe9f5c8af6c26c59517b846cec212a28`
6. **Save both immediately** - you won't see them again!

⚠️ **Important**: These are from **User account**, NOT from OAuth applications section!

### Step 4: Create Invoice Number Series

1. Go to **Settings** → **Doklady a číselné řady** (Documents and Number Formats) in Fakturoid
2. Create a new number format (or use existing one)
3. Set the format to: `SMD-{YYYY}-{#####}`
   - `SMD` = prefix for SyncMyDay
   - `{YYYY}` = current year
   - `{#####}` = sequential number with 5 digits (e.g. 00001)
4. Save the number format
5. **Important**: After saving, click "Edit" on the format and look at the URL:
   - URL will be: `https://app.fakturoid.cz/ACCOUNT/number_formats/12345/edit`
   - The number `12345` is your **number_format_id** - write it down!
   - You'll need this ID for the `.env` configuration

## Environment Configuration

Add the following variables to your `.env` file:

```bash
# Fakturoid API Configuration (OAuth 2.0)
FAKTUROID_CLIENT_ID=your_client_id_here
FAKTUROID_CLIENT_SECRET=your_client_secret_here
FAKTUROID_SLUG=digimix
FAKTUROID_NUMBER_FORMAT=1284290
FAKTUROID_USER_AGENT="SyncMyDay (support@syncmyday.com)"
```

**Important**: Replace the values with your actual credentials:

- `FAKTUROID_CLIENT_ID`: Client ID from Step 2 (from user account, NOT OAuth app)
- `FAKTUROID_CLIENT_SECRET`: Client Secret from Step 2
- `FAKTUROID_SLUG`: Your account subdomain (e.g. `digimix`, `syncmyday`)
- `FAKTUROID_NUMBER_FORMAT`: The **numeric ID** of your number series (from Step 4, found in URL)

## Running the Migration

After configuring the environment variables, run the database migration:

```bash
php artisan migrate
```

This will create the `fakturoid_invoices` table to store invoice data locally.

## How It Works

### Invoice Creation Flow

1. **User makes a payment** → Stripe processes the payment
2. **Stripe webhook fires** → `invoice.payment_succeeded` event is sent to `/webhooks/stripe`
3. **Webhook handler** → `BillingController@handlePaymentSucceeded()` is called
4. **Check amount** → If amount > 0, proceed (skip trial/$0 invoices)
5. **Create local record** → Insert pending invoice into `fakturoid_invoices` table
6. **Call Fakturoid API** → Create invoice via `FakturoidService@createInvoice()`
7. **Update record** → Store Fakturoid ID and number, mark as "created"
8. **On failure** → Mark invoice as "failed" for retry later

### Invoice Data Structure

Each invoice sent to Fakturoid includes:

```php
[
    'subject_id' => 123,                     // Contact ID (created automatically if not exists)
    'number_format_id' => 12345,             // Custom series ID (numeric)
    'currency' => 'czk',                     // Lowercase: czk, eur, pln
    'language' => 'cz',                      // cz, en, de, pl, sk
    'issued_on' => '2025-10-19',             // Today's date
    'due_on' => '2025-10-19',                // Immediate (already paid)
    'paid_on' => '2025-10-19',               // Mark as paid
    'lines' => [
        [
            'name' => 'SyncMyDay Pro - Yearly Subscription',
            'quantity' => 1,
            'unit_name' => 'ks',
            'unit_price' => 249.00,          // Amount from Stripe
            'vat_rate' => 21,                // 21% DPH
        ],
    ],
    'note' => 'Thank you for your payment...',
    'custom_id' => 'in_xxxxxxxxxxxxx',       // Stripe invoice ID
]
```

### Viewing Invoices

Users can view and download their Fakturoid invoices from:

**URL**: `/billing/manage`

The page shows:

- Invoice number (e.g. SMD-2025-001)
- Issue date
- Amount with currency
- Status (Paid)
- Download link (PDF)

### PDF Download

When user clicks "Download PDF":

1. **Route**: `/billing/invoice/{invoice}/pdf`
2. **Controller**: `BillingController@downloadInvoicePdf()`
3. **Authorization check**: Verify invoice belongs to logged-in user
4. **Fetch PDF**: Call Fakturoid API with Basic Auth
5. **Stream response**: Return PDF to user's browser

The PDF is **not cached** locally - it's streamed directly from Fakturoid to provide:

- Always up-to-date invoice data
- No storage overhead
- Security (credentials hidden from user)

## Retry Mechanism

If invoice creation fails (API down, network error, etc.), the system will:

1. **Mark invoice as "failed"** in database
2. **Store error message** for debugging
3. **Retry via cron job** (see next section)

Failed invoices can be retried:

- Maximum 5 retry attempts
- Only for invoices less than 7 days old
- Runs daily via scheduled task

## Cron Job for Retries

A scheduled command `RetryFailedFakturoidInvoices` runs daily to retry failed invoice creations.

### Manual Retry

```bash
php artisan fakturoid:retry-failed
```

This command will:

- Find all invoices with status "pending" or "failed"
- Retry count < 5
- Created within last 7 days
- Attempt to recreate them in Fakturoid

### Scheduling (Production)

The command is already scheduled in `app/Console/Kernel.php`:

```php
$schedule->command('fakturoid:retry-failed')->daily();
```

Make sure your cron is running:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Testing

### Test Invoice Creation

To test the integration without making a real payment:

1. **Create a test invoice manually**:

```php
// In tinker or test command
$user = User::find(1);
$service = new \App\Services\FakturoidService();

$invoiceData = $service->buildInvoiceData(
    $user,
    249.00,
    'CZK',
    'SyncMyDay Pro - Test Invoice',
    'test_' . time()
);

$result = $service->createInvoice($invoiceData);
dd($result);
```

2. **Check Fakturoid** to verify the invoice was created
3. **Check database** `fakturoid_invoices` table for the record

### Verify PDF Download

1. Log in as a user with an invoice
2. Go to `/billing/manage`
3. Click "Download PDF" on any invoice
4. Verify PDF downloads correctly

## Troubleshooting

### Invoice Not Created

**Check logs**: `storage/logs/laravel.log`

Common issues:

1. **Wrong API credentials** → Verify `.env` settings
2. **Number format doesn't exist** → Create "SMD" series in Fakturoid
3. **Network/firewall** → Ensure server can reach `app.fakturoid.cz`
4. **Invalid data** → Check user has valid name/email

### PDF Download Fails

1. **Check Fakturoid ID exists** → Query `fakturoid_invoices` table
2. **Verify API access** → Test with `curl`:
   ```bash
   curl -u "email:token" https://app.fakturoid.cz/api/v3/accounts/SLUG/invoices/ID.json
   ```
3. **Check invoice status** → Must be "created" in database

### Retry Not Working

1. **Check cron is running**: `ps aux | grep schedule:run`
2. **Check last run**: Look for `fakturoid:retry-failed` in logs
3. **Verify invoice eligibility**:
   - Status is "pending" or "failed"
   - Retry count < 5
   - Created < 7 days ago

## API Rate Limits

Fakturoid API v3 has rate limits:

- Default: **400 requests per 60 seconds**
- Headers: `X-RateLimit-Policy` and `X-RateLimit`

For normal operation (1-2 payments per minute), this is more than sufficient.

If rate limit is exceeded:

- Response: `429 Too Many Requests`
- Wait time shown in `X-RateLimit` header
- Retry after wait time

## Security Considerations

1. **Never expose API credentials** - Keep `.env` secure
2. **Use HTTPS** - All Fakturoid API calls use HTTPS
3. **Authorization checks** - Users can only download their own invoices
4. **Basic Auth** - API uses email + token (secure)
5. **PDF streaming** - Credentials not visible to users

## Additional Resources

- [Fakturoid API Documentation](https://www.fakturoid.cz/api/v3)
- [Fakturoid Support](mailto:podpora@fakturoid.cz)
- SyncMyDay Invoice Service: `app/Services/FakturoidService.php`
- SyncMyDay Invoice Model: `app/Models/FakturoidInvoice.php`

## Support

If you need help with Fakturoid integration:

- Check logs first: `storage/logs/laravel.log`
- Review this documentation
- Contact SyncMyDay support: support@syncmyday.com
