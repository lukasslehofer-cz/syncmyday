# Email Rate Limiting

**Datum:** 2026-01-07  
**Účel:** Dokumentace rate limitingu pro MXroute compliance

## Přehled

Kalendářové blocker emaily jsou odesílány přes Laravel Queue s rate limitingem, aby byl dodržen MXroute limit **400 emailů/hodinu na mailbox**. Nastavili jsme bezpečný limit **300 emailů/hodinu**.

## Jak to funguje

```
┌─────────────────┐
│  Sync Engine    │
│  (vytvoří event)│
└────────┬────────┘
         │
         v
┌─────────────────────────┐
│  ImipEmailService       │
│  (připraví email)       │
└────────┬────────────────┘
         │
         v
┌──────────────────────────────────┐
│  SendCalendarBlockerEmail (Job)  │ ← Přidá se do queue
│  - Rate limiting check           │
│  - 300 emailů/hodinu limit       │
│  - Per mailbox tracking          │
└────────┬─────────────────────────┘
         │
         v
┌──────────────────┐
│  Queue Worker    │ ← Běží na pozadí
│  (zpracovává)    │
└────────┬─────────┘
         │
         v (pokud NENÍ dosažen limit)
┌─────────────────┐
│  MXroute SMTP   │
│  (odešle email) │
└─────────────────┘
```

## Rate Limiting Logika

### Per Mailbox Tracking

Rate limit je sledován **per FROM address** (mailbox):

```php
$key = 'send-email:events@syncmyday.cz';
RateLimiter::attempt($key, 300, function() {
    // Odeslat email
}, 3600); // 1 hodina
```

**Příklad:**
- `events@syncmyday.cz` → může poslat 300 emailů/hodinu
- `events@syncmyday.sk` → může poslat 300 emailů/hodinu
- `info@syncmyday.cz` → může poslat 300 emailů/hodinu

Každý mailbox má **vlastní počítadlo**.

### Co se stane když je dosažen limit?

1. **Job se neprovede okamžitě**
2. **Job je vrácen zpět do fronty** (released)
3. **Automatický retry** podle backoff strategie:
   - 1. pokus: za 5 minut
   - 2. pokus: za 10 minut
   - 3. pokus: za 15 minut
   - 4. pokus: za 20 minut
   - 5. pokus: za 25 minut
4. **Po 5 neúspěšných pokusech** → job selže a přesune se do `failed_jobs`

### Backoff Strategie

```php
public $backoff = [300, 600, 900, 1200, 1500]; // sekundy
```

Toto zajišťuje, že pokud je vysoká zátěž, emaily se postupně odešlou během následující hodiny.

## Konfigurace

### .env proměnné

```bash
# Queue connection
QUEUE_CONNECTION=database

# Rate limit (emailů za hodinu)
MAIL_RATE_LIMIT_PER_HOUR=300
```

### config/mail.php

```php
'rate_limit_per_hour' => env('MAIL_RATE_LIMIT_PER_HOUR', 300),
```

## Spuštění Queue Workera

### Development

```bash
php artisan queue:work --tries=5 --timeout=60
```

### Production - Supervisor

Vytvořte `/etc/supervisor/conf.d/syncmyday-queue.conf`:

```ini
[program:syncmyday-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/syncmyday.cz/artisan queue:work database --tries=5 --timeout=60
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/syncmyday.cz/storage/logs/queue.log
stopwaitsecs=3600
```

Spusťte:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start syncmyday-queue:*
```

### Production - Systemd

Vytvořte `/etc/systemd/system/syncmyday-queue.service`:

```ini
[Unit]
Description=SyncMyDay Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/syncmyday.cz
ExecStart=/usr/bin/php /var/www/syncmyday.cz/artisan queue:work database --tries=5 --timeout=60
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
```

Spusťte:

```bash
sudo systemctl daemon-reload
sudo systemctl enable syncmyday-queue
sudo systemctl start syncmyday-queue
```

## Monitoring

### Sledovat frontu

```bash
# Zobrazit počet jobů v queue
php artisan queue:monitor database

# Zobrazit neúspěšné joby
php artisan queue:failed

# Zobrazit tabulku jobs
mysql -e "SELECT COUNT(*) FROM jobs WHERE queue='default'"
```

### Logování

Rate limit události jsou logovány:

```
[INFO] Calendar blocker email sent via queue
  from: events@syncmyday.cz
  to: user@example.com
  method: REQUEST
  mailer: mxroute

[WARNING] Rate limit exceeded for email sending
  from: events@syncmyday.cz
  to: user@example.com
  available_in_seconds: 1234
  rate_limit: 300/hour
```

### Retry neúspěšných jobů

```bash
# Všechny
php artisan queue:retry all

# Konkrétní job podle ID
php artisan queue:retry 12345

# Flush všechny failed jobs
php artisan queue:flush
```

## Multi-Domain Strategie

Pokud používáte více domén, zátěž se **automaticky rozdělí**:

```
events@syncmyday.cz  → 300 emailů/h
events@syncmyday.sk  → 300 emailů/h
events@syncmyday.pl  → 300 emailů/h
events@syncmyday.de  → 300 emailů/h
events@syncmyday.eu  → 300 emailů/h
─────────────────────────────────────
CELKEM:               1500 emailů/h
```

Každý uživatel dostává emaily z domény, na které se registroval, takže zátěž je přirozeně distribuována.

## Testování

### Test rate limitingu

```php
php artisan tinker

// Poslat 310 emailů rychle
for ($i = 0; $i < 310; $i++) {
    \App\Jobs\SendCalendarBlockerEmail::dispatch(
        'test@example.com',
        'Test Event',
        'Test body',
        'BEGIN:VCALENDAR...',
        'REQUEST',
        [
            'address' => 'events@syncmyday.cz',
            'name' => 'SyncMyDay',
            'mailer' => 'mxroute'
        ]
    );
}

// Zkontrolovat kolik je ve frontě
DB::table('jobs')->count(); // Mělo by být cca 10-20 (zbytek se zpracoval)
```

### Sledovat rate limiter

```php
php artisan tinker

use Illuminate\Support\Facades\RateLimiter;

$key = 'send-email:events@syncmyday.cz';

// Kolik pokusů zbývá?
$remaining = RateLimiter::remaining($key, 300);
echo "Remaining: $remaining / 300\n";

// Za jak dlouho se resetuje?
$availableIn = RateLimiter::availableIn($key);
echo "Resets in: $availableIn seconds\n";

// Vymazat rate limiter (pro testing)
RateLimiter::clear($key);
```

## Troubleshooting

### Problém: Emaily se neodesílají

**Kontrola:**
1. Běží queue worker?
   ```bash
   ps aux | grep "queue:work"
   ```

2. Jsou joby ve frontě?
   ```bash
   php artisan queue:monitor
   ```

3. Nejsou failed joby?
   ```bash
   php artisan queue:failed
   ```

**Řešení:**
```bash
# Spustit queue worker
php artisan queue:work --tries=5

# Retry failed jobs
php artisan queue:retry all
```

### Problém: Rate limit je moc nízký

Pokud potřebujete vyšší limit:

```bash
# V .env
MAIL_RATE_LIMIT_PER_HOUR=400  # Maximální MXroute limit

# Clear config cache
php artisan config:clear

# Restart queue worker
sudo supervisorctl restart syncmyday-queue:*
```

⚠️ **Pozor:** Nepřekračujte 400/hodinu, jinak MXroute může dočasně zablokovat odesílání!

### Problém: Queue se plní příliš rychle

**Příznaky:** Tisíce jobů ve frontě, dlouhé zpoždění

**Řešení:**
1. Spustit více queue workerů (až 3-4)
2. Použít Redis místo database queue (rychlejší)
3. Rozdělit zátěž mezi více domén

## Výhody tohoto řešení

✅ **Compliance s MXroute** - nikdy nepřekročíme 400/h  
✅ **Graceful degradation** - emaily se zpozdí, ale neztratí  
✅ **Automatický retry** - pokud selže, zkusí to znovu  
✅ **Per-mailbox tracking** - každý mailbox má vlastní limit  
✅ **Multi-domain support** - automaticky distribuuje zátěž  
✅ **Monitorovatelné** - logy, failed jobs, queue statistics  

## Srovnání s Mailgun

| Aspekt | Mailgun (před) | MXroute + Queue (nyní) |
|--------|----------------|------------------------|
| **Rychlost** | Real-time | Max 5 min zpoždění při high load |
| **Limit** | 100,000/měsíc free | 300/hodinu per mailbox |
| **Scaling** | Automatický | Multi-domain strategie |
| **Náklady** | $35+/měsíc | $0 (už používáme MXroute) |
| **Monitoring** | Dashboard | Queue monitor + logy |
| **Retry** | Automatický | Automatický (backoff) |

---

**Poznámka:** Systémové emaily (welcome, password reset) se stále odesílají **synchronně** (bez queue), protože mají nízký objem a vyžadují okamžité doručení.

