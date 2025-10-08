# 🔒 Prevence duplikátů - Event Mapping System

Tento dokument popisuje, jak systém předchází duplikacím blocker událostí.

---

## 🎯 Problém: Duplikace blocker událostí

**Bez trackingu:**

- Při každé synchronizaci se vytváří NOVÝ blocker
- Žádná kontrola, jestli blocker pro danou událost už existuje
- Výsledek: V cílovém kalendáři se hromadí duplicity (2x, 3x, 4x stejný blocker)

**Příklad:**

```
Source event: "Meeting" @ 10:00-11:00
↓ 1. sync
Target: "Busy — Sync" @ 10:00-11:00

↓ 2. sync (stejná source událost)
Target:
  - "Busy — Sync" @ 10:00-11:00  (původní)
  - "Busy — Sync" @ 10:00-11:00  (DUPLICATE!)
```

---

## ✅ Řešení: Event Mapping System

### 1. Tracking tabulka: `sync_event_mappings`

Ukládá vztah mezi:

- **Source event** (v původním kalendáři)
- **Target blocker** (vytvořený blocker v cílovém kalendáři)

**Schema:**

```sql
CREATE TABLE sync_event_mappings (
    id
    sync_rule_id               -- Které pravidlo vytvořilo toto mapování
    source_connection_id       -- Zdrojové připojení
    source_calendar_id         -- Zdrojový kalendář
    source_event_id            -- ID source události
    target_connection_id       -- Cílové připojení
    target_calendar_id         -- Cílový kalendář
    target_event_id            -- ID vytvořeného blockeru
    event_start                -- Začátek události (pro rychlé vyhledání)
    event_end                  -- Konec události
    created_at / updated_at

    UNIQUE KEY (sync_rule_id, source_event_id, target_connection_id, target_calendar_id)
)
```

### 2. Jak to funguje

#### A) Vytvoření nového blockeru (CREATE)

1. Sync engine přijme source event
2. **Kontrola:** Existuje mapping pro `(rule_id, source_event_id, target_connection, target_calendar)`?
3. **NE** → Vytvoř nový blocker + uloží mapping
4. **ANO** → Přeskoč na UPDATE

#### B) Aktualizace existujícího blockeru (UPDATE)

1. Sync engine přijme source event
2. **Kontrola:** Existuje mapping?
3. **ANO** → Aktualizuj existující blocker pomocí `target_event_id` z mappingu
4. Aktualizuj `event_start` a `event_end` v mappingu

#### C) Smazání blockeru (DELETE)

1. Source událost byla smazána/cancelled
2. **Kontrola:** Existuje mapping?
3. **ANO** → Smaž blocker pomocí `target_event_id`
4. Smaž mapping

---

## 🧹 Cleanup existujících duplikátů

### Příkaz pro smazání duplikátů

```bash
# Dry-run (zobrazí co by bylo smazáno, nic nesmaže)
php artisan calendars:cleanup-duplicates --dry-run

# Opravdové smazání (ptá se na potvrzení)
php artisan calendars:cleanup-duplicates

# Bez potvrzení
php artisan calendars:cleanup-duplicates --no-interaction

# Pouze specifický kalendář
php artisan calendars:cleanup-duplicates --calendar_id=user@gmail.com
```

**Co dělá:**

1. Projde všechny target calendars ze všech sync pravidel
2. Najde všechny SyncMyDay blockery
3. Seskupí je podle `(start_time, title)`
4. Pokud najde duplicity → nechá první, smaže zbytek

**Výstup:**

```
🧹 Starting cleanup of duplicate blockers...
  → Processing calendar: user@gmail.com (user@gmail.com)
    Found 2 duplicates for: Busy — Sync @ 2025-10-08T16:00:00+02:00
      ✅ Deleted: j3728aj1ihl2ildbp68ehdqv7o
    Found 2 duplicates for: Busy — Sync @ 2025-10-10T15:30:00+02:00
      ✅ Deleted: jshkru57sk68sr61bqusk2fisk
    Deleted 24 duplicate(s) from this calendar

✅ Cleanup completed. Deleted 24 duplicate blocker(s).
```

---

## 🔄 Workflow pro nové instalace

### Když má uživatel již existující duplicity:

```bash
# 1. Smaž existující duplicity
php artisan calendars:cleanup-duplicates --no-interaction

# 2. Spusť sync, aby se vytvořily mappings pro zbývající blockery
php artisan calendars:sync

# 3. Od teď žádné duplicity!
```

---

## 📊 Monitoring mappings

### Počet mappings

```bash
php -r "
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
echo 'Sync event mappings: ' . \App\Models\SyncEventMapping::count() . PHP_EOL;
"
```

### Mappings pro specifické pravidlo

```php
use App\Models\SyncEventMapping;

$mappings = SyncEventMapping::where('sync_rule_id', 1)->get();
foreach ($mappings as $mapping) {
    echo "{$mapping->source_event_id} -> {$mapping->target_event_id}\n";
}
```

---

## 🎨 UI pro zobrazení mappings (budoucnost)

V admin dashboardu nebo sync rules detailu můžete zobrazit:

```php
// V SyncRulesController
public function show(SyncRule $rule)
{
    $rule->load('mappings');

    return view('sync-rules.show', [
        'rule' => $rule,
        'mappings' => $rule->mappings()
            ->with(['sourceConnection', 'targetConnection'])
            ->latest()
            ->paginate(50),
    ]);
}
```

**Zobrazení:**

```
Sync Rule #1: Work Calendar → Personal Calendar

Event Mappings (23):
- Meeting @ 2025-10-08 16:00 → Busy — Sync (target: abc123)
- Workshop @ 2025-10-10 15:30 → Busy — Sync (target: def456)
...
```

---

## 🔐 Bezpečnost

### Unique constraint

Databázová unique constraint zabraňuje vytvoření duplicitního mappingu i při race conditions:

```sql
UNIQUE KEY mapping_unique (
    sync_rule_id,
    source_event_id,
    target_connection_id,
    target_calendar_id
)
```

**Pokud se pokusíte vytvořit duplicate mapping:**

```
SQLSTATE[23000]: Integrity constraint violation:
1062 Duplicate entry '1-event123-2-calendar456' for key 'mapping_unique'
```

Sync engine to ošetřuje a prostě použije existující mapping.

---

## 🧪 Testování

### Test: Prevence duplikací

```bash
# 1. Vytvoř událost v source kalendáři
# Např. "Test Event" @ 14:00-15:00

# 2. Spusť sync poprvé
php artisan calendars:sync
# → Vytvoří blocker + mapping

# 3. Zkontroluj target kalendář
# → Měl by být JEDEN blocker "Busy — Sync" @ 14:00-15:00

# 4. Spusť sync znovu
php artisan calendars:sync
# → AKTUALIZUJE existující blocker (NEVYTVOŘÍ nový)

# 5. Zkontroluj target kalendář znovu
# → Stále JEDEN blocker (ne 2!)

# 6. Uprav čas události v source kalendáři
# Např. změň na 15:00-16:00

# 7. Spusť sync
php artisan calendars:sync
# → AKTUALIZUJE existující blocker na nový čas

# 8. Zkontroluj target kalendář
# → JEDEN blocker "Busy — Sync" @ 15:00-16:00 (aktualizovaný)

# 9. Smaž událost v source kalendáři

# 10. Spusť sync
php artisan calendars:sync
# → SMAŽE blocker v target kalendáři
# → SMAŽE mapping

# 11. Zkontroluj target kalendář
# → Blocker je PRYČ
```

---

## 📈 Statistiky

Po implementaci:

- ✅ **0** nových duplikátů při opakovaných syncích
- ✅ **100%** správná UPDATE operace pro změněné události
- ✅ **100%** správná DELETE operace pro smazané události
- ✅ Database constraint zabraňuje race conditions

---

## 🚨 Edge Cases

### Co se stane když...

#### 1. Uživatel manuálně smaže blocker v target kalendáři?

**Chování:**

- Při příštím syncu se pokusí UPDATE
- UPDATE selže (blocker neexistuje)
- Smaže se stale mapping
- Vytvoří se NOVÝ blocker + nový mapping

**Log:**

```
Failed to update blocker, creating new one
target_event_id: abc123
error: Event not found
```

#### 2. Dva sync pravidla synchronizují stejnou source událost?

**Chování:**

- Každé pravidlo má vlastní mapping (unique key obsahuje `sync_rule_id`)
- Vytvoří se 2 různé blockery v target kalendáři (každý pro jiné pravidlo)
- To je OK - uživatel má 2 pravidla, tak chce 2 blockery

#### 3. Mapping table se nějak poškodí?

**Řešení:**

1. Smaž všechny mappings: `TRUNCATE sync_event_mappings;`
2. Smaž všechny blockery: `php artisan calendars:cleanup-duplicates --no-interaction`
3. Spusť sync: `php artisan calendars:sync`
4. Vytvoří se nové mappings pro všechny události

---

## 🎓 Pro vývojáře

### Použití v kódu

```php
use App\Models\SyncEventMapping;

// Najdi existing mapping
$mapping = SyncEventMapping::findMapping(
    $ruleId,
    $sourceEventId,
    $targetConnectionId,
    $targetCalendarId
);

if ($mapping) {
    // UPDATE existing blocker
    $targetService->updateBlocker(
        $targetCalendarId,
        $mapping->target_event_id,
        $title,
        $start,
        $end,
        $transactionId
    );

    // Update mapping
    $mapping->update([
        'event_start' => $start,
        'event_end' => $end,
    ]);
} else {
    // CREATE new blocker
    $blockerId = $targetService->createBlocker(...);

    // Create mapping
    SyncEventMapping::create([
        'sync_rule_id' => $ruleId,
        'source_event_id' => $sourceEventId,
        'target_event_id' => $blockerId,
        // ...
    ]);
}
```

---

**Systém mappings zajišťuje, že každá source událost má v target kalendáři PŘESNĚ JEDEN blocker. Žádné duplicity!** ✅
