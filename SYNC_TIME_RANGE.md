# ⏰ Sync Time Range Configuration

Tento dokument popisuje časové omezení synchronizace kalendářů.

---

## 🎯 Proč je to důležité?

**Problémy s neomezenou synchronizací:**

1. ❌ **Y2038 Bug** - MySQL TIMESTAMP má max. datum 2038-01-19
2. ❌ **Performance** - Načítání událostí až do roku 2055+ (opakující se narozeniny)
3. ❌ **Zbytečná data** - Kdo potřebuje blockery za 30 let?
4. ❌ **API rate limits** - Čím více dat, tím více API calls

---

## ✅ Aktuální nastavení

### Default konfigurace

**`config/sync.php`:**

```php
'time_range' => [
    'past_days' => 7,        // 7 dní zpětně
    'future_months' => 6,     // 6 měsíců dopředu
    'max_year' => 2037,       // Hard limit (Y2038)
],
```

### Současný rozsah synchronizace:

```
📅 Od: now() - 7 dní      (zachytí zpětné změny)
📅 Do: now() + 6 měsíců   (běžné plánování)
📊 Celkem: ~6.3 měsíců dat
```

---

## 🔧 Jak to funguje?

### 1. **První/Full Sync**

Když sync běží poprvé nebo po resetu sync tokenu:

**Google Calendar:**

```php
$optParams = [
    'timeMin' => now()->subDays(7)->toRfc3339String(),
    'timeMax' => now()->addMonths(6)->toRfc3339String(),
    'singleEvents' => true,
];
```

**Microsoft Calendar:**

```php
$url = "/me/calendars/{$calendarId}/calendarView"
    . "?startDateTime=" . now()->subDays(7)->format('Y-m-d\TH:i:s')
    . "&endDateTime=" . now()->addMonths(6)->format('Y-m-d\TH:i:s');
```

### 2. **Incremental Sync (se sync tokenem)**

Následující syncy používají sync token který vrací JEN ZMĚNY:

**Google:**

```php
$optParams = [
    'syncToken' => $previousSyncToken,
];
```

**Microsoft:**

```php
$request = $this->graph->createRequest('GET', $deltaLink);
```

**DŮLEŽITÉ:** I při incremental syncu se **filtrují události** podle time range v `SyncEngine`:

```php
if ($eventStart < $timeMin || $eventStart > $timeMax) {
    // Skip event outside sync range
    continue;
}
```

---

## 🎨 Změna nastavení

### Metoda 1: .env soubor

Přidejte do `.env`:

```env
# Sync time range (optional, defaults in config/sync.php)
SYNC_PAST_DAYS=7
SYNC_FUTURE_MONTHS=6
```

### Metoda 2: Config soubor

Upravte `config/sync.php`:

```php
'time_range' => [
    'past_days' => env('SYNC_PAST_DAYS', 14),      // 2 týdny
    'future_months' => env('SYNC_FUTURE_MONTHS', 12), // 1 rok
],
```

### Metoda 3: Dynamicky per pravidlo (budoucí feature)

V `sync_rules` tabulce:

```sql
ALTER TABLE sync_rules ADD COLUMN sync_range_days INT DEFAULT 180;
```

---

## 🔄 Reset sync tokenů

Po změně time range je doporučeno resetovat sync tokeny aby se provedl full sync s novým rozsahem:

```bash
# Reset sync tokenů
php artisan sync:reset-tokens

# Spusťte sync
php artisan calendars:sync
```

**Nebo s force flag:**

```bash
php artisan sync:reset-tokens --force
```

---

## 📊 Doporučené hodnoty podle use case

| Use Case       | Past Days | Future Months | Popis              |
| -------------- | --------- | ------------- | ------------------ |
| **Personal**   | 7         | 3             | Běžné plánování    |
| **Business**   | 14        | 6             | Delší projekty     |
| **Enterprise** | 30        | 12            | Roční plánování    |
| **Minimal**    | 1         | 1             | Jen aktuální měsíc |

---

## 🧪 Testování

### Zobrazit aktuální rozsah

```bash
php -r "
\$pastDays = config('sync.time_range.past_days', 7);
\$futureMonths = config('sync.time_range.future_months', 6);
\$timeMin = now()->subDays(\$pastDays);
\$timeMax = now()->addMonths(\$futureMonths);
echo 'From: ' . \$timeMin . PHP_EOL;
echo 'To:   ' . \$timeMax . PHP_EOL;
"
```

### Zobrazit kolik událostí je mimo rozsah

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

\$timeMax = now()->addMonths(6);
\$farFuture = \App\Models\SyncEventMapping::where('event_start', '>', \$timeMax)->count();
echo 'Events beyond sync range: ' . \$farFuture . PHP_EOL;
"
```

---

## 🔍 Logování

Time range se loguje při full syncu:

**Google:**

```
[INFO] Google full sync with time range
    calendar_id: user@gmail.com
    time_min: 2025-10-01T13:00:00+00:00
    time_max: 2026-04-08T13:00:00+00:00
```

**Microsoft:**

```
[INFO] Microsoft full sync with time range
    calendar_id: user@gmail.com
    start_date_time: 2025-10-01T13:00:00
    end_date_time: 2026-04-08T13:00:00
```

**Filtrované události:**

```
[DEBUG] Event outside sync range, skipping
    event_id: event123
    event_start: 2055-06-28 08:00:00
    time_min: 2025-10-01 13:00:00
    time_max: 2026-04-08 13:00:00

[INFO] Skipped events outside sync range
    rule_id: 5
    processed: 10
    skipped: 15
```

---

## 🚀 Performance Impact

### Před (unlimited):

- **Events API response:** ~500 events (including far-future recurrences)
- **Processing time:** ~5 seconds
- **Y2038 errors:** 15-20 per sync
- **Database load:** High (many failed inserts)

### Po (7 days past + 6 months future):

- **Events API response:** ~50-100 events
- **Processing time:** ~1 second ✅
- **Y2038 errors:** 0 ✅
- **Database load:** Minimal ✅

---

## 🔐 Bezpečnost & Privacy

### GDPR Compliance

Time range pomáhá s:

- ✅ **Data minimization** - Synchronizuje jen relevantní události
- ✅ **Storage limitation** - Neuchovává data déle než nutné
- ✅ **Purpose limitation** - Jen události potřebné pro prevenci double-booking

### Automatické čištění

Staré blockery mimo rozsah můžete čistit pomocí:

```bash
# Smaž blockery starší než 30 dní
php artisan calendars:cleanup-old-blockers --days=30
```

_(Tento příkaz by se dal přidat jako budoucí feature)_

---

## 📈 Monitoring

### Dashboard Stats

Můžete přidat do dashboardu:

```php
$stats['sync_range'] = [
    'past_days' => config('sync.time_range.past_days'),
    'future_months' => config('sync.time_range.future_months'),
    'total_events_in_range' => SyncEventMapping::where('event_start', '>=', now()->subDays(7))
        ->where('event_start', '<=', now()->addMonths(6))
        ->count(),
];
```

---

## 🎓 FAQ

### Q: Co se stane s událostmi které už jsou synchronizované ale přesahují nový rozsah?

**A:** Zůstávají v databázi (v `sync_event_mappings`), ale při příštím syncu se neaktualizují. Můžete je smazat pomocí cleanup příkazu.

### Q: Co když mám důležitou událost za rok?

**A:** Zvyšte `SYNC_FUTURE_MONTHS` na 12 nebo více. Pak resetujte sync tokeny.

### Q: Funguje to i s webhooky?

**A:** Ano! Webhook notifikace se stále přijímají, ale události mimo rozsah se filtrují v `SyncEngine`.

### Q: Co když změním nastavení?

**A:** Spusťte `php artisan sync:reset-tokens --force` aby se provedl full sync s novým rozsahem.

---

**Time range zajišťuje rychlou, efektivní synchronizaci bez Y2038 bugů!** ✅
