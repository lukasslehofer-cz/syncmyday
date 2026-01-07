# 📧 Email-Based Calendar Sync

## Overview

Email-based calendar sync allows users to connect calendars that don't have API access (like corporate Exchange servers) by forwarding calendar invitations to a unique email address.

## How It Works

```
1. User creates email calendar → Gets unique address (abc123@syncmyday.local)
2. User sets up forwarding in their calendar
3. Calendar invitation arrives → Forwarded to abc123@syncmyday.local
4. SyncMyDay processes email → Extracts .ics attachment
5. Creates blockers in target calendars
6. NO response sent (organizer doesn't know about forwarding)
```

## Features

✅ **Silent Processing** - No notifications sent to meeting organizers  
✅ **Automatic Sync** - Forwards are processed automatically  
✅ **Sender Whitelist** - Optional security via allowed sender emails  
✅ **Update Detection** - Handles event updates (SEQUENCE tracking)  
✅ **Cancellation Support** - Removes blockers when events are cancelled  
✅ **Local Testing** - Test without real email server

---

## Local Development & Testing

### 1. Create Email Calendar

```bash
# Visit in browser:
http://localhost:8080/email-calendars/create

# Fill form and submit
# You'll get unique email like: a7b2c9f4@syncmyday.local
```

### 2. Test via Web UI

```bash
# Visit:
http://localhost:8080/email-calendars/{id}/test

# Paste sample email (see template in UI)
# Click "Process Test Email"
```

### 3. Test via Artisan Command

```bash
# Create test email file
cat > test-email.txt << 'EOF'
From: organizer@company.com
To: a7b2c9f4@syncmyday.local
Subject: Meeting Invitation
Content-Type: multipart/mixed; boundary="boundary123"

--boundary123
Content-Type: text/plain

Meeting invitation attached.

--boundary123
Content-Type: text/calendar; name="meeting.ics"

BEGIN:VCALENDAR
VERSION:2.0
METHOD:REQUEST
BEGIN:VEVENT
UID:test-12345@company.com
DTSTAMP:20251008T120000Z
DTSTART:20251010T140000Z
DTEND:20251010T150000Z
SUMMARY:Team Meeting
STATUS:CONFIRMED
SEQUENCE:0
END:VEVENT
END:VCALENDAR

--boundary123--
EOF

# Process email
php artisan email:process-test a7b2c9f4 --file=test-email.txt
```

---

## Production Setup

### Prerequisites

1. **Email Server** - You need incoming email handling
2. **Domain** - Configured for email (e.g., `@syncmyday.com`)
3. **MX Records** - Pointing to your mail server

### Option 1: Postfix + Pipe to Laravel

```bash
# 1. Install Postfix
sudo apt-get install postfix

# 2. Configure virtual alias
# /etc/postfix/virtual
*@syncmyday.com     calendar-handler

# 3. Create pipe script
# /usr/local/bin/process-calendar-email.sh
#!/bin/bash
EMAIL_TOKEN=$(echo "$1" | cut -d'@' -f1)
cd /var/www/syncmyday
php artisan email:process "$EMAIL_TOKEN" --stdin

# 4. Configure alias
# /etc/aliases
calendar-handler: "|/usr/local/bin/process-calendar-email.sh"

# 5. Rebuild aliases
sudo newaliases
sudo postfix reload
```

### Option 2: Mailgun Webhooks

```php
// routes/api.php
Route::post('/webhooks/mailgun', function (Request $request) {
    // Verify Mailgun signature
    $signature = hash_hmac(
        'sha256',
        $request->input('timestamp') . $request->input('token'),
        config('services.mailgun.webhook_secret')
    );

    if (!hash_equals($signature, $request->input('signature'))) {
        abort(403);
    }

    // Extract email token from recipient
    $recipient = $request->input('recipient'); // abc123@syncmyday.com
    $emailToken = explode('@', $recipient)[0];

    // Get raw email
    $rawEmail = $request->input('body-mime');

    // Process
    app(EmailCalendarSyncService::class)->processIncomingEmail($emailToken, $rawEmail);

    return response()->json(['status' => 'ok']);
});
```

### Option 3: AWS SES + Lambda

```javascript
// Lambda function
exports.handler = async (event) => {
  const message = event.Records[0].ses.mail;
  const recipient = message.destination[0]; // abc123@syncmyday.com
  const emailToken = recipient.split("@")[0];

  // Download email from S3
  const s3 = new AWS.S3();
  const email = await s3
    .getObject({
      Bucket: "calendar-emails",
      Key: message.messageId,
    })
    .promise();

  // Send to Laravel API
  await axios.post("https://syncmyday.com/api/email/process", {
    email_token: emailToken,
    email_content: email.Body.toString(),
  });
};
```

---

## Security Considerations

### 1. Sender Whitelist

```php
// Allow only specific senders
$connection->sender_whitelist = [
    'calendar@company.com',
    '*@company.com',  // Wildcard domain
];
```

### 2. Rate Limiting

```php
// In EmailCalendarSyncService
if ($connection->emails_received > 100 &&
    $connection->last_email_at > now()->subHour()) {
    // Too many emails in last hour
    return ['success' => false, 'error' => 'Rate limit exceeded'];
}
```

### 3. Email Validation

- ✅ Verify sender is in whitelist (if configured)
- ✅ Check for .ics attachments only
- ✅ Validate .ics format before processing
- ✅ NO auto-replies (prevent mail loops)

---

## Troubleshooting

### Email not being processed

**Check:**

1. Email calendar status: `SELECT * FROM email_calendar_connections WHERE email_token='abc123'`
2. Logs: `tail -f storage/logs/laravel.log | grep 'Email'`
3. Test manually: `php artisan email:process-test abc123 --file=test.eml`

### Events not creating blockers

**Check:**

1. Active sync rules: `SELECT * FROM sync_rules WHERE user_id=X AND is_active=1`
2. Target connections: `SELECT * FROM calendar_connections WHERE user_id=X AND status='active'`
3. Sync logs: `SELECT * FROM sync_logs WHERE transaction_id='...' ORDER BY created_at DESC`

### Duplicate blockers

**Not possible!** The system uses `sync_event_mappings` table with unique constraint on:

- `sync_rule_id`
- `original_event_uid`
- `target_connection_id`
- `target_calendar_id`

---

## API Reference

### Process Email (Production)

```http
POST /api/email/process
Content-Type: application/json
Authorization: Bearer {API_TOKEN}

{
    "email_token": "abc123",
    "email_content": "From: ...\nTo: ...\n\n..."
}
```

### Artisan Commands

```bash
# Process test email from file
php artisan email:process-test {token} --file={path}

# List email calendars
php artisan tinker
>>> App\Models\EmailCalendarConnection::all()

# Manually trigger sync for all rules
php artisan calendars:sync
```

---

## Database Schema

### email_calendar_connections

```sql
id, user_id, email_address, email_token, name, description,
sender_whitelist (JSON), emails_received, events_processed,
last_email_at, status, last_error, created_at, updated_at
```

### sync_event_mappings (extended)

```sql
... existing fields ...
source_type (api|email|ics),
email_connection_id,
original_event_uid
```

---

## Example Use Cases

### Corporate Exchange (no API)

```
User: "My work uses Exchange, I can't access API"
Solution:
1. Create email calendar
2. Set Outlook rule: Forward invitations to abc123@syncmyday.local
3. Blockers auto-created in personal Google Calendar
```

### Legacy Calendar System

```
User: "Our company calendar is ancient, no integrations"
Solution:
1. Get email notifications from legacy system
2. Forward to email calendar
3. Events appear in modern calendars
```

### Privacy Mode

```
User: "I want to share availability, not details"
Solution:
1. Email calendar processes invitations
2. Creates "Busy" blockers (no details)
3. Colleagues see you're unavailable, but not why
```

---

## Configuration

### .env

```bash
# Email domain for calendar emails
EMAIL_DOMAIN=syncmyday.local  # localhost
# EMAIL_DOMAIN=syncmyday.com  # production

# Optional: Email provider settings (for production)
MAILGUN_DOMAIN=syncmyday.com
MAILGUN_SECRET=key-xxx
MAILGUN_WEBHOOK_SECRET=whsec_xxx
```

### config/app.php

```php
'email_domain' => env('EMAIL_DOMAIN', 'syncmyday.local'),
```

---

## Testing Checklist

- [ ] Create email calendar via web UI
- [ ] Copy unique email address
- [ ] Generate test .ics file
- [ ] Test via web UI (paste email)
- [ ] Test via artisan command
- [ ] Verify blocker created in target calendar
- [ ] Test event update (same UID, higher SEQUENCE)
- [ ] Test event cancellation (METHOD:CANCEL)
- [ ] Test sender whitelist
- [ ] Check sync logs

---

## Support

For issues or questions:

- Check logs: `storage/logs/laravel.log`
- Run diagnostics: `php artisan email:process-test --verbose`
- Check database: `sync_event_mappings`, `sync_logs`
