# Analýza problému: Nekonečná smyčka synchronizace Microsoft kalendářů

## 🔴 Symptomy z logů

```
[2025-10-21 19:50:00] Syncing connection (connection_id: 6)
[2025-10-21 19:50:00] Sync completed (fetched: 52, processed: 52, missing_start_end: 9)
[2025-10-21 19:50:00] Syncing connection (connection_id: 6) <-- OPAKUJE SE OKAMŽITĚ
[2025-10-21 19:50:01] Syncing connection (connection_id: 6)
[2025-10-21 19:50:02] Syncing connection (connection_id: 6)
```

**Frekvence**: Každou vteřinu (někdy i vícekrát za sekundu)  
**Eventi načtení**: Pokaždé stejných 52 eventů  
**Trvání**: ~600ms per sync  
**Problémové eventi**: 9 eventů bez start/end datetime

---

## 🔍 Identifikované problémy

### Problém #1: Chybějící Delta Link pagination

**Lokace**: `MicrosoftCalendarService::getChangedEvents()` (řádek 395-438)

```php
public function getChangedEvents(string $calendarId, ?string $deltaLink = null): array
{
    if ($deltaLink) {
        $request = $this->graph->createRequest('GET', $deltaLink);
    } else {
        // Full sync s calendarView
        $url = "/me/calendars/{$calendarId}/calendarView"
            . "?startDateTime={$startDateTime}"
            . "&endDateTime={$endDateTime}";

        $request = $this->graph->createRequest('GET', $url)
            ->addHeaders(['Prefer' => 'odata.track-changes, odata.maxpagesize=50']);
    }

    $response = $request->execute();
    $data = $response->getBody();

    return [
        'events' => $data['value'] ?? [],
        'delta_link' => $data['@odata.deltaLink'] ?? null,  // ❌ PROBLÉM!
        'next_link' => $data['@odata.nextLink'] ?? null,
    ];
}
```

**Problém**:

- Microsoft API vrací `@odata.deltaLink` POUZE na poslední stránce výsledků
- Pokud je víc než 50 eventů, dostaneme `@odata.nextLink` místo `@odata.deltaLink`
- Kód nenačítá další stránky → delta link se NIKDY neuloží
- Bez delta linku se VŽDY dělá full sync (všech 52 eventů)

**Důsledek**:

```
1. První sync: Načte 52 eventů, vrátí @odata.nextLink (ne deltaLink)
2. Další sync: sync_token = null → opět full sync
3. Další sync: sync_token = null → opět full sync
4. ... NEKONEČNĚ
```

---

### Problém #2: Chybějící debouncing/rate limiting webhooků

**Lokace**: `WebhookController::microsoft()` (řádek 97-155)

```php
public function microsoft(Request $request, string $connectionId)
{
    $notifications = $request->input('value', []);

    foreach ($notifications as $notification) {
        // ...
        ProcessCalendarWebhookJob::dispatch($connection->id, $subscription->calendar_id);
        $processedCount++;
    }

    return response('Accepted', 202);
}
```

**Problémy**:

1. ❌ Žádné rate limiting - každý webhook okamžitě vytvoří nový job
2. ❌ Žádné deduplikace - pokud Microsoft pošle 10 stejných notifikací, vytvoří se 10 jobů
3. ❌ Žádné zpoždění - všechny joby běží naráz a mohou se překrývat

**Scénář smyčky**:

```
Microsoft webhook → Job #1 spuštěn
  ↓
Sync vytvoří/updatuje blocker v target kalendáři
  ↓
Target kalendář má taky webhook → Microsoft pošle notifikaci
  ↓
Nový webhook → Job #2 spuštěn
  ↓
Job #2 synchronizuje zpět → vytvoří blocker v původním kalendáři
  ↓
Původní kalendář webhook → Microsoft pošle notifikaci
  ↓
LOOP! 🔄
```

---

### Problém #3: Blockery možná nejsou správně filtrovány

**Lokace**: `SyncEngine::processEvent()` (řádek 329-347)

```php
// Check if this is our own blocker - skip to prevent loops
if ($sourceService->isOurBlocker($event)) {
    Log::channel('sync')->debug('Skipping own blocker (by category/tag)', ['event_id' => $sourceEventId]);
    return;
}
```

**Možný problém**:

- Microsoft události bez start/end (9 eventů v logu) mohou být deleted/removed události
- `isOurBlocker()` kontroluje jenom `categories` field
- Pokud deleted event nemá categories, nepozná se jako náš blocker
- Může se pokusit synchronizovat náš vlastní deleted blocker → triggerne další webhook

---

### Problém #4: Webhook se spouští při KAŽDÉ změně, i našich blockerech

**Lokace**: `MicrosoftCalendarService::createBlocker()` (řádek 212-245)

```php
public function createBlocker(...)
{
    $event = [
        'subject' => $title,
        'categories' => ['SyncMyDay'],  // ✅ Toto je dobře
        'transactionId' => $transactionId,  // ❌ Ale Microsoft webhook se stejně spustí!
        // ...
    ];

    $response = $this->graph->createRequest('POST', "/me/calendars/{$calendarId}/events")
        ->attachBody($event)
        ->execute();
}
```

**Problém**:

- I když označíme event jako "SyncMyDay" blocker, Microsoft VŽDY pošle webhook
- Ten webhook vytvoří job, který musí načíst všechny eventi a filtrovat blockery
- Pokud máme bidirectional sync (two_way), může vzniknout ping-pong efekt

---

## 🎯 Hlavní příčina smyčky

**Kombinace problémů:**

1. **Delta link se nikdy neuloží** → Každý sync načítá 52 eventů (full sync)
2. **Webhook rate limiting chybí** → Microsoft posílá notifications často
3. **Job queue se plní** → Joby se zpracovávají rychle (600ms), ale vytváří se rychleji než se stihnou zpracovat
4. **Možná bidirectional sync** → Vytváření blockerů v obou směrech triggeruje další webhooky

---

## 📊 Co se děje v praxi

```
Time: 19:50:00.000
↓
Microsoft webhook přijat → Job #1 queued
↓
Job #1 START: Sync connection 6
  ├─ Načte 52 eventů (full sync, protože delta_link = null)
  ├─ 9 eventů nemá start/end → přeskočeno
  ├─ 43 eventů zpracováno
  ├─ Vytvoří/updatuje blockery v target kalendářích
  └─ KONČÍ (600ms)
↓
Microsoft webhook přijat → Job #2 queued (triggered vytváření blockerů)
↓
Time: 19:50:00.600

Microsoft webhook přijat → Job #3 queued (další notifikace)
↓
Job #2 START: Sync connection 6
  ├─ Načte 52 eventů (OPĚT full sync!)
  └─ ...
↓
Time: 19:50:01.000

... OPAKUJE SE ...
```

---

## ✅ Řešení

### 1. Implementovat pagination pro Delta Link

```php
// MicrosoftCalendarService.php
public function getChangedEvents(string $calendarId, ?string $deltaLink = null): array
{
    $allEvents = [];
    $url = $deltaLink ?? $this->buildInitialUrl($calendarId);

    do {
        $request = $this->graph->createRequest('GET', $url);
        $response = $request->execute();
        $data = $response->getBody();

        $allEvents = array_merge($allEvents, $data['value'] ?? []);

        // Pokračuj na další stránku
        $url = $data['@odata.nextLink'] ?? null;

    } while ($url); // Dokud není nextLink

    // Teď máme všechny události a delta link na konci
    return [
        'events' => $allEvents,
        'delta_link' => $data['@odata.deltaLink'] ?? null,
    ];
}
```

### 2. Přidat debouncing pro webhooky

```php
// WebhookController.php - Microsoft webhook
public function microsoft(Request $request, string $connectionId)
{
    $notifications = $request->input('value', []);

    foreach ($notifications as $notification) {
        $subscriptionId = $notification['subscriptionId'];
        $subscription = ...;

        // ✅ Použij unique job ID pro deduplikaci
        $jobId = "webhook-{$connection->id}-{$subscription->calendar_id}";

        // ✅ Dispatch s delay a unique ID
        ProcessCalendarWebhookJob::dispatch($connection->id, $subscription->calendar_id)
            ->delay(now()->addSeconds(2))  // Debounce 2 sekundy
            ->onQueue('webhooks')
            ->withChain([]);  // Možná přidat unique constraint
    }

    return response('Accepted', 202);
}
```

### 3. Rate limiting v Job

```php
// ProcessCalendarWebhookJob.php
use Illuminate\Support\Facades\Cache;

public function handle(SyncEngine $syncEngine): void
{
    $lockKey = "sync-lock-{$this->connectionId}";

    // ✅ Zkontroluj, jestli už neběží sync pro toto připojení
    if (Cache::has($lockKey)) {
        Log::channel('webhook')->info('Sync already running, skipping', [
            'connection_id' => $this->connectionId,
        ]);
        return;
    }

    // ✅ Nastav lock na 90 sekund (timeout job)
    Cache::put($lockKey, true, 90);

    try {
        $connection = CalendarConnection::find($this->connectionId);
        // ...
        $syncEngine->syncConnection($connection);
    } finally {
        // ✅ Vždy uvolni lock
        Cache::forget($lockKey);
    }
}
```

### 4. Přeskočit události bez start/end dříve

```php
// SyncEngine.php - v syncRule metodě
foreach ($changedData['events'] as $event) {
    // ✅ Přeskoč eventi bez start/end PŘED dalším zpracováním
    $eventStart = $this->getEventStart($event, $sourceConnection->provider);

    if (!$eventStart && $sourceConnection->provider === 'microsoft') {
        $stats['missing_start_end']++;

        // ✅ Log a přeskoč
        Log::channel('sync')->debug('Microsoft event missing start/end, skipping', [
            'event_id' => $this->getEventId($event),
            'event_keys' => array_keys($event),
        ]);
        continue;  // ✅ SKIP celý event
    }

    // Zbytek logiky...
}
```

---

## 🔧 Priorita oprav

1. **URGENTNÍ**: Implementovat pagination pro delta link
2. **VYSOKÁ**: Přidat rate limiting/debouncing
3. **STŘEDNÍ**: Přeskočit události bez start/end dříve
4. **NÍZKÁ**: Optimalizace isOurBlocker kontroly

---

## 📝 Závěr

Nekonečná smyčka je způsobena kombinací:

- **Chybějící pagination** → Delta link se nikdy neuloží → Vždy full sync
- **Chybějící rate limiting** → Webhooky vytváří joby rychleji než se zpracují
- **Možná bidirectional sync** → Ping-pong efekt mezi kalendáři

**Hlavní fix**: Implementovat pagination v `getChangedEvents()`, aby se delta link uložil a přestal se dělat full sync každých 600ms.
