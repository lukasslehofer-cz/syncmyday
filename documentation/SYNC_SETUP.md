# 🔄 Synchronizace kalendářů - Setup

Tento dokument popisuje jak spustit synchronizaci kalendářů na localhostu (bez webhooků).

---

## 📊 Stav synchronizace

### Manuální spuštění synchronizace

```bash
php artisan calendars:sync
```

**Výstup:**

```
🔄 Starting calendar synchronization...
📋 Found 1 active sync rule(s)
  → Syncing rule #1: user@gmail.com
    ✅ Synced successfully
🎉 All rules synced successfully!
```

### Synchronizace specifického pravidla

```bash
php artisan calendars:sync --rule_id=1
```

### Synchronizace pro specifického uživatele

```bash
php artisan calendars:sync --user_id=1
```

---

## 🤖 Automatická synchronizace - Cron Setup

### Metoda 1: Laravel Scheduler (Doporučeno)

Laravel má vestavěný scheduler, který spouští úlohy podle plánu.

#### 1. Spusťte scheduler v pozadí

**Přidejte do crontab:**

```bash
# Otevřete crontab editor
crontab -e

# Přidejte tento řádek (nahraďte cestu k projektu)
* * * * * cd /Users/lukas/SyncMyDay && php artisan schedule:run >> /dev/null 2>&1
```

Tento cron job se spouští každou minutu a Laravel scheduler rozhodne které příkazy spustit podle plánu.

#### 2. Ověřte nastavený plán

```bash
php artisan schedule:list
```

**Výstup by měl obsahovat:**

```
0 */5 * * *    php artisan calendars:sync ............ Next Due: 5 minutes from now
0 */6 * * *    php artisan webhooks:renew ............ Next Due: 6 hours from now
0 0 * * *      php artisan logs:clean ................ Next Due: 1 day from now
0 * * * *      php artisan connections:check ......... Next Due: 1 hour from now
```

#### 3. Aktuální konfigurace (v `app/Console/Kernel.php`)

```php
protected function schedule(Schedule $schedule): void
{
    // Sync kalendářů každých 5 minut
    $schedule->command('calendars:sync')->everyFiveMinutes();

    // Obnovení webhook subscriptions každých 6 hodin
    $schedule->command('webhooks:renew')->everySixHours();

    // Čištění starých logů denně
    $schedule->command('logs:clean')->daily();

    // Kontrola připojení každou hodinu
    $schedule->command('connections:check')->hourly();
}
```

**Změna frekvence:**

Můžete upravit frekvenci v `app/Console/Kernel.php`:

```php
->everyMinute()          // Každou minutu (pro testování)
->everyFiveMinutes()     // Každých 5 minut (výchozí)
->everyTenMinutes()      // Každých 10 minut
->everyFifteenMinutes()  // Každých 15 minut
->hourly()               // Každou hodinu
```

---

### Metoda 2: Přímý Cron Job (Jednodušší, ale méně flexibilní)

Pokud nechcete používat Laravel scheduler:

```bash
# Otevřete crontab
crontab -e

# Přidejte:
*/5 * * * * cd /Users/lukas/SyncMyDay && php artisan calendars:sync >> /dev/null 2>&1
```

**Vysvětlení:**

- `*/5 * * * *` = Každých 5 minut
- `cd /Users/lukas/SyncMyDay` = Přejdi do adresáře projektu
- `php artisan calendars:sync` = Spusť sync příkaz
- `>> /dev/null 2>&1` = Nezapisuj výstup (nebo změňte na `>> storage/logs/cron.log 2>&1` pro logování)

---

## 📝 Logování synchronizace

### Sync logy v databázi

Všechny synchronizační akce se logují do databáze (`sync_logs` tabulka).

**Zobrazit nedávné logy:**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$logs = \App\Models\SyncLog::orderBy('created_at', 'desc')
    ->limit(10)
    ->get(['id', 'action', 'source_event_id', 'target_event_id', 'created_at']);

foreach (\$logs as \$log) {
    echo \$log->created_at . ' - ' . \$log->action . ' - ' . \$log->source_event_id . PHP_EOL;
}
"
```

### Laravel logy

```bash
tail -f storage/logs/laravel.log
tail -f storage/logs/sync.log
```

---

## 🔍 Testování synchronizace

### 1. Vytvořte testovací událost v Google Calendar

1. Otevřete Google Calendar
2. Vytvořte novou událost (např. "Test Sync Event")
3. Nastavte čas na další hodinu
4. Uložte

### 2. Spusťte manuální sync

```bash
php artisan calendars:sync
```

### 3. Zkontrolujte cílový kalendář

1. Otevřete druhý připojený kalendář (např. druhý Google účet)
2. Měli byste vidět "busy blocker" s názvem nastaveným v sync rule (např. "Busy — Sync")
3. Blocker by měl mít stejný čas jako původní událost

### 4. Zkontrolujte sync logy

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Total syncs: ' . \App\Models\SyncLog::count() . PHP_EOL;
echo 'Created: ' . \App\Models\SyncLog::where('action', 'created')->count() . PHP_EOL;
echo 'Skipped: ' . \App\Models\SyncLog::where('action', 'skipped')->count() . PHP_EOL;
echo 'Errors: ' . \App\Models\SyncLog::where('action', 'error')->count() . PHP_EOL;
"
```

---

## 🚨 Troubleshooting

### Synchronizace neběží

**1. Zkontrolujte, že pravidlo je aktivní:**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$rules = \App\Models\SyncRule::where('is_active', true)->get(['id', 'user_id']);
echo 'Active rules: ' . \$rules->count() . PHP_EOL;
foreach (\$rules as \$rule) {
    echo 'Rule ID: ' . \$rule->id . ', User: ' . \$rule->user_id . PHP_EOL;
}
"
```

**2. Zkontrolujte připojení:**

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
\$connections = \App\Models\CalendarConnection::where('status', 'active')->get(['id', 'provider', 'provider_email']);
echo 'Active connections: ' . \$connections->count() . PHP_EOL;
foreach (\$connections as \$conn) {
    echo \$conn->provider . ': ' . \$conn->provider_email . PHP_EOL;
}
"
```

**3. Spusťte sync s detailním výpisem:**

```bash
php artisan calendars:sync -vvv
```

### Cron neběží

**Ověřte, že cron job je aktivní:**

```bash
crontab -l
```

**Otestujte manuálně:**

```bash
* * * * * cd /Users/lukas/SyncMyDay && php artisan schedule:run
```

**Logujte výstup:**

```bash
* * * * * cd /Users/lukas/SyncMyDay && php artisan schedule:run >> storage/logs/cron.log 2>&1
```

Pak zkontrolujte:

```bash
cat storage/logs/cron.log
```

### Tokeny expirují

Pokud tokeny expirují a synchronizace selže:

```bash
php artisan connections:check
```

Tento příkaz zkontroluje všechna připojení a pošle notifikaci uživatelům, pokud je potřeba znovu autorizovat.

---

## 🌍 Produkční nasazení (s webhooky)

Pro produkční nasazení s HTTPS a webhooky:

1. **Nasaďte aplikaci na server s HTTPS**
2. **Aktualizujte `.env`:**
   ```env
   APP_URL=https://yourdomain.com
   ```
3. **Webhooky se automaticky vytvoří** při vytváření sync pravidel
4. **Synchronizace bude real-time** (okamžitá při změně v kalendáři)
5. **Cron jako backup** - i s webhooky je dobré mít cron jako zálohu (např. každou hodinu)

### Použití ngrok pro lokální testování webhooků

Pokud chcete otestovat webhooky lokálně:

```bash
# 1. Nainstalujte ngrok
brew install ngrok

# 2. Spusťte ngrok
ngrok http 8080

# 3. Zkopírujte HTTPS URL (např. https://abc123.ngrok.io)

# 4. Aktualizujte .env
APP_URL=https://abc123.ngrok.io

# 5. Aktualizujte OAuth redirect URIs v Google/Microsoft console
# Google: https://abc123.ngrok.io/oauth/google/callback
# Microsoft: https://abc123.ngrok.io/oauth/microsoft/callback

# 6. Vyčistěte cache
php artisan config:clear

# 7. Vytvořte nové sync pravidlo - webhook se vytvoří automaticky
```

---

## 📊 Dashboard / UI pro sync logy

V budoucí verzi můžete přidat UI pro zobrazení sync logů na dashboardu:

**V `resources/views/sync-rules/index.blade.php`** můžete přidat sekci s nedávnými logy.

**Nebo vytvořte dedikovanou stránku:**

```bash
# Vytvořte nový controller
php artisan make:controller SyncLogsController

# Přidejte route
Route::get('/sync-logs', [SyncLogsController::class, 'index'])->name('sync-logs.index');
```

---

## ✅ Checklist pro funkční synchronizaci

- [ ] Kalendáře připojeny (viditelné na `/connections`)
- [ ] Sync pravidlo vytvořeno (viditelné na `/sync-rules`)
- [ ] Pravidlo je aktivní (zelený status)
- [ ] Spustil jsem `php artisan calendars:sync` manuálně
- [ ] Viděl jsem "✅ Synced successfully"
- [ ] Zkontroloval jsem cílový kalendář a vidím blocker
- [ ] (Volitelně) Nastavil jsem cron job pro automatickou synchronizaci

---

**Máte-li jakékoliv problémy, zkontrolujte logy a TROUBLESHOOTING.md!**
