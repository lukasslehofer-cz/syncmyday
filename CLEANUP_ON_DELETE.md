# 🧹 Automatické čištění blocker při odpojení kalendáře

Tento dokument popisuje, jak systém automaticky odstraňuje vytvořené blocker události při odpojení kalendáře nebo smazání sync pravidla.

---

## 🎯 Problém

**Bez automatického čištění:**

- Uživatel odpojí kalendář nebo smaže sync pravidlo
- Blocker události **ZŮSTÁVAJÍ** v cílových kalendářích
- Uživatel musí manuálně mazat všechny blockery
- Porušení GDPR / data retention policies

---

## ✅ Řešení: Model Observers

### 1. CalendarConnectionObserver

**Spouští se:** Při smazání calendar connection (odpojení kalendáře)

**Co dělá:**

1. Najde všechny mappings, kde je toto connection **TARGET** (cílový kalendář)
2. Pro každý mapping smaže skutečný blocker v kalendáři pomocí Calendar API
3. Mappings se automaticky smažou díky cascade delete v databázi

**Kdy se spouští:**

- Uživatel klikne "Remove" u připojení kalendáře
- Admin smaže connection
- Connection se smaže automaticky (např. při smazání uživatele)

**Příklad:**

```
Uživatel má:
- Google Calendar #1 (source) → Google Calendar #2 (target)
- Sync rule: GCal1 → GCal2

V GCal2 jsou blockery:
- "Busy — Sync" @ 10:00-11:00
- "Busy — Sync" @ 14:00-15:00
- "Busy — Sync" @ 16:00-17:00

Uživatel odpojí GCal2:
→ Observer automaticky smaže všechny 3 blockery z GCal2
→ Sync rules s GCal2 jako target se smažou (cascade)
→ Mappings se smažou (cascade)
```

---

### 2. SyncRuleObserver

**Spouští se:** Při smazání sync rule

**Co dělá:**

1. Najde všechny mappings pro toto pravidlo
2. Seskupí podle target connection (pro efektivitu)
3. Pro každý mapping smaže skutečný blocker v cílovém kalendáři
4. Mappings se automaticky smažou díky cascade delete

**Kdy se spouští:**

- Uživatel klikne "Delete" u sync pravidla
- Admin smaže rule
- Rule se smaže automaticky (např. při smazání source connection)

**Příklad:**

```
Sync Rule #1: GCal1 → GCal2 + GCal3

Vytvořené blockery:
GCal2:
- Event A → Blocker A
- Event B → Blocker B

GCal3:
- Event A → Blocker A'
- Event B → Blocker B'

Uživatel smaže Sync Rule #1:
→ Observer smaže Blocker A + B z GCal2
→ Observer smaže Blocker A' + B' z GCal3
→ Všechny mappings se smažou
```

---

## 🔄 Workflow

### Scénář 1: Odpojení kalendáře

```bash
# Před:
Connections:
  - Google: work@gmail.com (source)
  - Google: personal@gmail.com (target) ← má 10 blocker

Sync Rules:
  - Rule #1: work → personal

# Uživatel klikne "Remove" u personal@gmail.com

# Co se stane:
1. CalendarConnectionObserver.deleting() se spustí
2. Najde 10 mappings kde target_connection_id = personal
3. Inicializuje GoogleCalendarService s personal connection
4. Smaže všech 10 blocker v personal kalendáři
5. Connection se smaže z DB
6. Mappings se smažou (cascade)
7. Sync Rule #1 se smaže (cascade)

# Po:
Connections:
  - Google: work@gmail.com (source)

Sync Rules: (prázdné)

personal@gmail.com kalendář: ŽÁDNÉ blockery ✅
```

---

### Scénář 2: Smazání sync pravidla

```bash
# Před:
Sync Rule #1: work → personal + business

Blockery:
  personal: 5 blocker
  business: 5 blocker

# Uživatel klikne "Delete" u Rule #1

# Co se stane:
1. SyncRuleObserver.deleting() se spustí
2. Najde 10 mappings pro rule_id = 1
3. Seskupí podle target_connection_id:
   - personal connection: 5 mappings
   - business connection: 5 mappings
4. Inicializuje service pro personal, smaže 5 blocker
5. Inicializuje service pro business, smaže 5 blocker
6. Rule se smaže z DB
7. Mappings se smažou (cascade)

# Po:
Sync Rules: (prázdné)

personal kalendář: ŽÁDNÉ blockery ✅
business kalendář: ŽÁDNÉ blockery ✅
```

---

## 🎨 UI Varování

### Při odpojení kalendáře

**Varování v connections/index.blade.php:**

```
⚠️ Are you sure?

This will:
• Delete related sync rules
• Remove all blocker events created by this calendar
• Stop all webhooks

This action cannot be undone.
```

### Při smazání sync pravidla

**Varování v sync-rules/index.blade.php:**

```
⚠️ Are you sure?

This will:
• Delete this sync rule
• Remove ALL blocker events created by this rule from target calendars

This action cannot be undone.
```

---

## 📊 Logování

Vše se loguje do `storage/logs/laravel.log`:

### Úspěšné čištění

```
[2025-10-08 12:00:00] local.INFO: Cleaning up blockers before deleting connection
    connection_id: 5
    provider: google

[2025-10-08 12:00:00] local.INFO: Found 10 blocker(s) to delete

[2025-10-08 12:00:01] local.DEBUG: Blocker deleted
    mapping_id: 123
    target_event_id: abc123xyz

[2025-10-08 12:00:05] local.INFO: Connection cleanup completed
    connection_id: 5
    deleted: 10
    errors: 0

[2025-10-08 12:00:05] local.INFO: Calendar connection deleted
    connection_id: 5
    provider: google
```

### S chybami

```
[2025-10-08 12:00:00] local.INFO: Cleaning up blockers before deleting sync rule
    rule_id: 3
    user_id: 1

[2025-10-08 12:00:00] local.INFO: Found 5 blocker(s) to delete

[2025-10-08 12:00:01] local.WARNING: Failed to delete blocker during rule cleanup
    mapping_id: 456
    target_event_id: def456
    error: Event not found

[2025-10-08 12:00:05] local.INFO: Sync rule cleanup completed
    rule_id: 3
    deleted: 4
    errors: 1

[2025-10-08 12:00:05] local.INFO: Sync rule deleted
    rule_id: 3
    user_id: 1
```

---

## 🔒 Error Handling

### Co se stane když blocker už neexistuje?

**Scénář:**

- Uživatel manuálně smazal blocker v kalendáři
- Ale mapping stále existuje v DB
- Nyní se odpojuje kalendář nebo maže pravidlo

**Chování:**

1. Observer se pokusí smazat blocker
2. API vrátí chybu "Event not found"
3. **Warning** se zaloguje (ne error!)
4. Proces **pokračuje** pro další blockery
5. Mapping se stejně smaže (cascade)

**Výsledek:** Partial success - většina blocker se smaže, chyby se logují ale nezpůsobí selhání celého procesu.

---

### Co se stane když API selže?

**Scénář:**

- OAuth token expiroval
- Network error
- Rate limit

**Chování:**

1. Observer se pokusí inicializovat service
2. Service throw exception
3. **Error** se zaloguje
4. Všechny blockery pro tuto connection se **NEPŘEPÍŠÍ**
5. Connection/Rule se **STEJNĚ SMAŽE** z DB
6. Mappings se smažou (cascade)

**Výsledek:** Connection/rule je smazán, ale blockery zůstávají v kalendáři (graceful degradation).

**Řešení:**

- Uživatel může použít cleanup command manuálně:
  ```bash
  php artisan calendars:cleanup-duplicates
  ```
- Nebo smazat blockery manuálně v kalendáři

---

## 🧪 Testování

### Test 1: Odpojení kalendáře

```bash
# 1. Vytvořte testovací sync rule
# Např. GCal1 → GCal2

# 2. Spusťte sync aby se vytvořily blockery
php artisan calendars:sync

# 3. Zkontrolujte cílový kalendář (GCal2)
# → Měli byste vidět několik "Busy — Sync" blocker

# 4. Odpojte cílový kalendář přes UI
http://localhost:8080/connections
# → Klikněte "Remove" u GCal2

# 5. Zkontrolujte logy
tail -f storage/logs/laravel.log
# → Měli byste vidět "Cleaning up blockers before deleting connection"
# → Měli byste vidět "Blocker deleted" pro každý blocker

# 6. Zkontrolujte cílový kalendář (GCal2)
# → Všechny blockery by měly být SMAZANÉ ✅
```

---

### Test 2: Smazání sync pravidla

```bash
# 1. Vytvořte testovací sync rule
# Např. GCal1 → GCal2

# 2. Spusťte sync
php artisan calendars:sync

# 3. Zkontrolujte cílový kalendář
# → Měli byste vidět blockery

# 4. Smažte sync pravidlo přes UI
http://localhost:8080/sync-rules
# → Klikněte "Delete" u pravidla

# 5. Zkontrolujte logy
tail -f storage/logs/laravel.log
# → Měli byste vidět "Cleaning up blockers before deleting sync rule"

# 6. Zkontrolujte cílový kalendář
# → Všechny blockery vytvořené tímto pravidlem by měly být SMAZANÉ ✅
```

---

## 📈 Database Cascade

**Migrace obsahují tyto cascade deletes:**

```sql
-- calendar_connections
id (PK)
↓ onDelete('cascade')
├── sync_rules (where source_connection_id)
│   ↓ onDelete('cascade')
│   ├── sync_rule_targets
│   └── sync_event_mappings
├── sync_rule_targets (where target_connection_id)
├── webhook_subscriptions
└── sync_event_mappings (where source_connection_id OR target_connection_id)
```

**Kdy se co smaže:**

| Akce              | Cascade Delete v DB                | Observer čištění          |
| ----------------- | ---------------------------------- | ------------------------- |
| Smazat connection | rules, targets, webhooks, mappings | ✅ Blockery v kalendářích |
| Smazat rule       | targets, mappings                  | ✅ Blockery v kalendářích |
| Smazat user       | connections → rules → ...          | ✅ Všechny blockery       |

---

## 🎓 Pro vývojáře

### Implementace vlastního observeru

```php
namespace App\Observers;

use App\Models\CalendarConnection;
use Illuminate\Support\Facades\Log;

class CalendarConnectionObserver
{
    public function deleting(CalendarConnection $connection)
    {
        Log::info('Connection deleting', ['id' => $connection->id]);

        // Cleanup logic here
        // - Find all related data
        // - Delete external resources (API calls)
        // - DB relations will cascade automatically
    }

    public function deleted(CalendarConnection $connection)
    {
        Log::info('Connection deleted', ['id' => $connection->id]);

        // Post-deletion logic (if needed)
    }
}
```

### Registrace observeru

```php
// app/Providers/AppServiceProvider.php

public function boot(): void
{
    \App\Models\CalendarConnection::observe(
        \App\Observers\CalendarConnectionObserver::class
    );
}
```

---

## 🚨 GDPR Compliance

Automatické čištění blocker pomáhá s GDPR compliance:

✅ **Right to erasure (Article 17):**

- Když uživatel odpojí kalendář, všechna související data (včetně blocker) jsou smazána
- Žádné "siroté" blockery nezůstávají v cizích kalendářích

✅ **Data minimization (Article 5):**

- Blocker události existují pouze dokud je sync pravidlo aktivní
- Po smazání pravidla se data automaticky odstraní

✅ **Storage limitation (Article 5):**

- Data se neuchovávají déle než je nutné
- Smazání connection → okamžité smazání všech souvisejících dat

---

**Systém automaticky čistí všechny vytvořené blockery při odpojení kalendáře nebo smazání sync pravidla. Žádné "siroté" události!** ✅
