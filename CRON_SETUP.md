# Cron Jobs Setup (Shared Hosting Compatible)

All cron scripts are compatible with shared hosting where `proc_open()` is disabled.

## 🔐 Security

All cron scripts require `CRON_SECRET` token from your `.env` file:

```env
CRON_SECRET=your_random_secret_token_here
```

## 📋 Available Cron Jobs

### 1. **Inbound Email Processing** (Every minute)

Processes incoming calendar emails via IMAP polling.

**URL:** `https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
* * * * * curl -s "https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 2. **Calendar Sync** (Every 5 minutes)

Syncs calendars according to active sync rules.

**URL:** `https://syncmyday.cz/cron-calendars-sync.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
*/5 * * * * curl -s "https://syncmyday.cz/cron-calendars-sync.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 3. **Webhook Renewal** (Every 6 hours)

Renews expiring webhook subscriptions for Google/Microsoft calendars.

**URL:** `https://syncmyday.cz/cron-webhooks-renew.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
0 */6 * * * curl -s "https://syncmyday.cz/cron-webhooks-renew.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 4. **Queue Processing** (Every 5 minutes)

Processes queued jobs (background tasks).

**URL:** `https://syncmyday.cz/cron-queue.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
*/5 * * * * curl -s "https://syncmyday.cz/cron-queue.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 5. **Connections Health Check** (Every hour)

Checks calendar connections for expired tokens.

**URL:** `https://syncmyday.cz/cron-connections-check.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
0 * * * * curl -s "https://syncmyday.cz/cron-connections-check.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 6. **Logs Cleanup** (Daily at midnight)

Cleans up old sync logs (30 days retention).

**URL:** `https://syncmyday.cz/cron-logs-clean.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
0 0 * * * curl -s "https://syncmyday.cz/cron-logs-clean.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 7. **Trial Ending Notifications** (Daily at 9:00)

Sends email notifications to users whose trial is ending soon.

**URL:** `https://syncmyday.cz/cron-trial-notifications.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
0 9 * * * curl -s "https://syncmyday.cz/cron-trial-notifications.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

### 8. **Trial Expiration** (Daily at midnight)

Expires trial periods for users without active subscriptions.

**URL:** `https://syncmyday.cz/cron-trial-expire.php?token=YOUR_CRON_SECRET`

**Cron syntax:**

```
0 0 * * * curl -s "https://syncmyday.cz/cron-trial-expire.php?token=YOUR_CRON_SECRET" > /dev/null 2>&1
```

---

## 🚀 Complete crontab

Copy and paste this into your crontab (replace `YOUR_CRON_SECRET` with actual token):

```bash
# SyncMyDay Cron Jobs
CRON_SECRET=your_cron_secret_here

# Inbound email processing (every minute)
* * * * * curl -s "https://syncmyday.cz/cron-inbound-emails.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Calendar sync (every 5 minutes)
*/5 * * * * curl -s "https://syncmyday.cz/cron-calendars-sync.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Webhook renewal (every 6 hours)
0 */6 * * * curl -s "https://syncmyday.cz/cron-webhooks-renew.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Queue processing (every 5 minutes)
*/5 * * * * curl -s "https://syncmyday.cz/cron-queue.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Connections check (every hour)
0 * * * * curl -s "https://syncmyday.cz/cron-connections-check.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Logs cleanup (daily at midnight)
0 0 * * * curl -s "https://syncmyday.cz/cron-logs-clean.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Trial notifications (daily at 9:00)
0 9 * * * curl -s "https://syncmyday.cz/cron-trial-notifications.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Trial expiration (daily at midnight)
0 0 * * * curl -s "https://syncmyday.cz/cron-trial-expire.php?token=${CRON_SECRET}" > /dev/null 2>&1
```

---

## 🧪 Testing Cron Jobs

You can test any cron job manually via browser or curl:

```bash
curl "https://syncmyday.cz/cron-inbound-emails.php?token=YOUR_CRON_SECRET"
```

Expected response:

```json
{
  "status": "success",
  "processed": 0,
  "output": "...",
  "duration": "0.23s",
  "time": "2025-10-10 23:45:00"
}
```

---

## 📝 Monitoring

All cron scripts:

- ✅ Return JSON responses when called via HTTP
- ✅ Return plain text when called via CLI
- ✅ Log errors to `storage/logs/laravel.log`
- ✅ Return appropriate HTTP status codes (200/401/500)

To monitor cron execution, check your Laravel logs:

```bash
tail -f /path/to/syncmyday/storage/logs/laravel.log
```

---

## 🔧 Troubleshooting

### 401 Unauthorized

- Check that `CRON_SECRET` is set in `.env`
- Verify you're using the correct token in the URL

### 500 Internal Server Error

- Check Laravel logs: `storage/logs/laravel.log`
- Verify all `.env` variables are set correctly
- Check file permissions (775 for directories, 664 for files)

### Cron jobs not running

- Verify crontab syntax with `crontab -l`
- Check server time: `date`
- Test manually first via browser/curl
- Check hosting control panel cron logs

---

## 🎯 Minimal Setup (Essential Jobs Only)

If you want to start with only the most important cron jobs:

```bash
# Essential: Inbound email processing (every minute)
* * * * * curl -s "https://syncmyday.cz/cron-inbound-emails.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Essential: Calendar sync (every 5 minutes)
*/5 * * * * curl -s "https://syncmyday.cz/cron-calendars-sync.php?token=${CRON_SECRET}" > /dev/null 2>&1

# Important: Webhook renewal (every 6 hours)
0 */6 * * * curl -s "https://syncmyday.cz/cron-webhooks-renew.php?token=${CRON_SECRET}" > /dev/null 2>&1
```

You can add other jobs later as needed.
