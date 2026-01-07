# Mailgun Configuration Backup

**Datum zálohy:** 2026-01-07  
**Účel:** Záloha Mailgun konfigurace před migrací na MXroute-only setup  
**Git tag:** `mailgun-config-backup`

## Přehled

Mailgun byl použit pro:
- **Odchozí emaily:** events@ kalendářové blocker emaily (vysoký objem)
- **Příchozí emaily:** Zpracování odpovědí přes webhooky

## 1. Config/mail.php - Mailgun Mailer

```php
// config/mail.php (řádky 50-61)

// Mailgun - for calendar blockers (events@)
// High volume: calendar invitations, updates, cancellations
'mailgun' => [
    'transport' => 'smtp',
    'host' => env('MAILGUN_SMTP_HOST', 'smtp.mailgun.org'),
    'port' => env('MAILGUN_SMTP_PORT', 587),
    'encryption' => env('MAILGUN_SMTP_ENCRYPTION', 'tls'),
    'username' => env('MAILGUN_SMTP_USERNAME'),
    'password' => env('MAILGUN_SMTP_PASSWORD'),
    'timeout' => null,
    'verify_peer' => true,
],
```

## 2. Config/services.php - Mailgun Service

```php
// config/services.php (řádky 96-102)

// Mailgun - for calendar blockers (events@) and inbound email processing
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    'webhook_signing_key' => env('MAILGUN_WEBHOOK_SIGNING_KEY'),
],
```

## 3. Routes - Webhook Endpoints

### routes/api.php (řádky 40-44)

```php
// Mailgun Inbound Webhook
// Receives calendar responses (ACCEPT/DECLINE) from email calendar systems
Route::post('/webhook/mailgun-inbound', [
    \App\Http\Controllers\Webhook\MailgunInboundController::class,
    'handle'
])->name('webhook.mailgun-inbound');
```

### routes/web.php (řádky 339-340)

```php
// Email webhooks (for inbound calendar emails)
Route::post('/webhooks/email/mailgun', [EmailWebhookController::class, 'mailgun'])
    ->name('webhooks.email.mailgun');
```

## 4. MailgunInboundController

**Soubor:** `app/Http/Controllers/Webhook/MailgunInboundController.php`

Kompletní controller pro zpracování příchozích emailů z Mailgun:

```php
<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Email\InboundEmailProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Mailgun Inbound Webhook Controller
 * 
 * Receives and processes inbound emails from Mailgun Routes
 * Route: POST /api/webhook/mailgun-inbound
 */
class MailgunInboundController extends Controller
{
    public function __construct(
        private InboundEmailProcessor $processor
    ) {}

    /**
     * Handle Mailgun inbound email webhook
     * 
     * Mailgun sends POST data with:
     * - sender: From address
     * - recipient: To address (events@syncmyday.*)
     * - subject: Email subject
     * - body-plain: Plain text body
     * - body-html: HTML body
     * - stripped-text: Body without signature
     * - attachments: Array of attachments
     */
    public function handle(Request $request)
    {
        try {
            // Verify webhook signature (security)
            if (!$this->verifySignature($request)) {
                Log::warning('Mailgun webhook signature verification failed', [
                    'timestamp' => $request->input('timestamp'),
                    'token' => substr($request->input('token', ''), 0, 10) . '...',
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Extract email data
            $from = $request->input('sender');
            $to = $request->input('recipient');
            $subject = $request->input('subject', '');
            $body = $request->input('stripped-text') ?? $request->input('body-plain', '');

            // Check if this is a calendar response (iMIP)
            $contentType = $request->input('Content-Type', '');
            $isCalendarResponse = str_contains($contentType, 'text/calendar') 
                || str_contains($subject, 'Accepted:')
                || str_contains($subject, 'Declined:')
                || str_contains($subject, 'Tentative:');

            // Get .ics attachment if present
            $icsContent = null;
            $attachmentCount = $request->input('attachment-count', 0);
            
            for ($i = 1; $i <= $attachmentCount; $i++) {
                $attachmentName = $request->input("attachment-{$i}");
                if ($attachmentName && str_ends_with(strtolower($attachmentName), '.ics')) {
                    // Mailgun provides file via multipart/form-data
                    if ($request->hasFile("attachment-{$i}")) {
                        $icsContent = file_get_contents($request->file("attachment-{$i}")->getRealPath());
                        break;
                    }
                }
            }

            // Also check inline calendar data
            if (!$icsContent && str_contains($contentType, 'text/calendar')) {
                $icsContent = $request->input('body-calendar');
            }

            Log::info('Mailgun inbound email received', [
                'from' => $from,
                'to' => $to,
                'subject' => $subject,
                'is_calendar' => $isCalendarResponse,
                'has_ics' => !empty($icsContent),
            ]);

            // Process calendar response if applicable
            if ($isCalendarResponse && $icsContent) {
                $result = $this->processor->processCalendarResponse($from, $to, $icsContent);
                
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Calendar response processed',
                        'data' => $result,
                    ]);
                } else {
                    Log::warning('Failed to process calendar response', $result);
                    return response()->json([
                        'success' => false,
                        'message' => $result['error'] ?? 'Processing failed',
                    ], 422);
                }
            }

            // Not a calendar response - just acknowledge
            return response()->json([
                'success' => true,
                'message' => 'Email received (not a calendar response)',
            ]);

        } catch (\Exception $e) {
            Log::error('Mailgun webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Verify Mailgun webhook signature
     * 
     * See: https://documentation.mailgun.com/en/latest/user_manual.html#webhooks
     */
    private function verifySignature(Request $request): bool
    {
        $signingKey = config('services.mailgun.webhook_signing_key');
        
        // Skip verification if key not configured (dev/testing)
        if (!$signingKey) {
            Log::warning('Mailgun webhook signing key not configured - skipping verification');
            return true;
        }

        $timestamp = $request->input('timestamp');
        $token = $request->input('token');
        $signature = $request->input('signature');

        if (!$timestamp || !$token || !$signature) {
            return false;
        }

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $timestamp . $token, $signingKey);
        
        return hash_equals($expectedSignature, $signature);
    }
}
```

**Poznámka:** Controller zůstane v projektu, ale nebude používán po migraci na IMAP.

## 5. Environment Variables

```bash
# ============================================
# Mailgun - Calendar Blockers (events@)
# ============================================
# High volume: calendar invitations, updates, cancellations
MAILGUN_SMTP_HOST=smtp.mailgun.org
MAILGUN_SMTP_PORT=587
MAILGUN_SMTP_USERNAME=postmaster@syncmyday.cz
MAILGUN_SMTP_PASSWORD="tvoje_mailgun_smtp_heslo_zde"
MAILGUN_SMTP_ENCRYPTION=tls

# ============================================
# Mailgun API & Webhooks
# ============================================
# For inbound email processing (events@ responses)
MAILGUN_DOMAIN=syncmyday.cz
MAILGUN_SECRET="tvuj_mailgun_api_klic_zde"
MAILGUN_ENDPOINT=api.mailgun.net
MAILGUN_WEBHOOK_SIGNING_KEY="tvuj_webhook_signing_key_zde"
```

## 6. Jak Mailgun fungoval

### Odchozí emaily (events@)

1. **EmailHelper** rozhodoval, který mailer použít:
   - `info@` → `mxroute` mailer (MXroute SMTP)
   - `events@` → `mailgun` mailer (Mailgun SMTP)

2. Mailgun posílal emaily přes SMTP:
   - Host: `smtp.mailgun.org`
   - Port: `587` (TLS)
   - Auth: `postmaster@syncmyday.cz` + heslo

### Příchozí emaily

1. **Mailgun Routes** přijímal emaily na `events@syncmyday.cz`
2. Mailgun posílal webhook na: `https://syncmyday.cz/api/webhook/mailgun-inbound`
3. **MailgunInboundController** zpracovával webhook:
   - Ověřoval HMAC podpis
   - Extrahoval .ics přílohy
   - Předával `InboundEmailProcessor` pro zpracování
4. Odpovědi (ACCEPT/DECLINE) byly okamžitě zpracovány

### Výhody Mailgun webhooků

- **Okamžité zpracování** (real-time, bez zpoždění)
- **Push model** (Mailgun aktivně volá API)
- **Spolehlivé** (Mailgun retry při selhání)

### Nevýhody

- **Vendor lock-in** (závislost na Mailgun)
- **Náklady** (platba za službu)
- **Komplexita** (webhook setup, signature verification)

## 7. Jak obnovit Mailgun

Pokud se rozhodnete vrátit k Mailgun:

### 1. Git checkout
```bash
git checkout mailgun-config-backup
```

### 2. Nebo manuálně:

**a) Obnovit mailer v config/mail.php:**
- Odkomentovat/obnovit 'mailgun' mailer (řádky 50-61)

**b) Obnovit services v config/services.php:**
- Odkomentovat/obnovit 'mailgun' sekci (řádky 96-102)

**c) Obnovit routes:**
- V `routes/api.php` odkomentovat řádky 40-44
- V `routes/web.php` odkomentovat řádky 339-340

**d) Změnit EmailHelper:**
- V `app/Helpers/EmailHelper.php` řádek 54:
  ```php
  $mailer = 'mailgun'; // změnit z 'mxroute' na 'mailgun'
  ```

**e) Nastavit .env proměnné:**
- Přidat všechny `MAILGUN_*` proměnné (viz sekce 5)

**f) Nastavit Mailgun Routes:**
- V Mailgun dashboardu: Receiving → Routes
- Expression: `events@syncmyday.cz`
- Action: `https://syncmyday.cz/api/webhook/mailgun-inbound`

**g) Clear config:**
```bash
php artisan config:clear
```

## 8. Poznámky

- **MailgunInboundController** zůstává v projektu i po migraci (pro případné obnovení)
- **EmailWebhookController** má také metodu `mailgun()` která je podobná
- IMAP polling je pomalejší (5 min zpoždění), ale nezávislý na vendor
- MXroute nemá webhooky, proto se přepíná na IMAP polling

---

**Důležité:** Tento backup slouží pouze jako dokumentace. Controller a konfigurace zůstávají v kódu,  
jen se zakomentují/deaktivují.

