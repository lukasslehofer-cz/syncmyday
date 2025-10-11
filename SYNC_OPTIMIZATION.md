# 🚀 Optimalizace Synchronizace - Prevence Zbytečných Updateů

**Datum:** 11. října 2025  
**Status:** ✅ Optimalizováno

---

## 🔴 Problém

Po opravě kritických bugů synchronizace fungovala, ale:

### Dashboard plný "updated" logů

Při každém syncu (každých 5 minut):
1. ✅ Našlo všechny události v kalendáři
2. ✅ Pro každou existující událost našlo mapping
3. ❌ **Volalo `updateBlocker()`** - i když se **NIC nezměnilo**!
4. ❌ Logovalo "Blokující událost aktualizována" do DB
5. ❌ Dělalo **zbytečný API call** na Google/Microsoft
6. ❌ Pro email targety posílalo **zbytečný iMIP email**

### Důsledky:

- 🗑️ **Dashboard spam** - stovky "updated" logů
- 💸 **Zbytečné API calls** → může vést k rate limits
- 📧 **Zbytečné emaily** každých 5 minut (pro email calendars)
- 😕 **Matoucí pro uživatele** - vypadá to, že se pořád něco děje

---

## ✅ Řešení

### Inteligentní detekce změn

Před voláním `updateBlocker()` nebo odesláním iMIP emailu:

1. **Porovnáme časy** start/end s uloženým mappingem
2. **Pokud se nic nezměnilo** → early return, žádný update, žádný log
3. **Pokud se změnil čas** → update + log

### Tolerance 1 minuta

Kvůli zaokrouhlení a různým timezone formátům povolujeme toleranci **60 sekund**:

```php
if (abs($mappingStart->getTimestamp() - $start->getTimestamp()) > 60) {
    $needsUpdate = true;
}
```

---

## 🔧 Implementace

### 1. API Calendar Targets (Google/Microsoft)

**Soubor:** `app/Services/Sync/SyncEngine.php` → `createOrUpdateBlockerInTarget()`

```php
if ($mapping) {
    // Mapping exists - check if update is needed
    $needsUpdate = false;
    $maxTimestamp = new \DateTime('2038-01-01');
    
    // Check if start/end time changed
    $mappingStart = $mapping->event_start;
    $mappingEnd = $mapping->event_end;
    
    if ($mappingStart && $start && $start <= $maxTimestamp) {
        // Compare timestamps (allow 1 minute tolerance for rounding)
        if (abs($mappingStart->getTimestamp() - $start->getTimestamp()) > 60) {
            $needsUpdate = true;
        }
    } elseif (!$mappingStart && $start) {
        $needsUpdate = true;
    }
    
    if ($mappingEnd && $end && $end <= $maxTimestamp) {
        if (abs($mappingEnd->getTimestamp() - $end->getTimestamp()) > 60) {
            $needsUpdate = true;
        }
    } elseif (!$mappingEnd && $end) {
        $needsUpdate = true;
    }
    
    if ($needsUpdate) {
        // Event time changed - update the blocker
        $targetService->updateBlocker(...);
        $action = 'updated';
        
        Log::channel('sync')->info('Blocker updated due to time change', [
            'event_id' => $sourceEventId,
            'old_start' => $mappingStart?->format('Y-m-d H:i:s'),
            'new_start' => $start->format('Y-m-d H:i:s'),
        ]);
    } else {
        // No changes detected - skip update and logging
        Log::channel('sync')->debug('Blocker unchanged, skipping update', [
            'event_id' => $sourceEventId,
            'blocker_id' => $mapping->target_event_id,
        ]);
        return; // Early return - don't log anything
    }
}
```

### 2. Email Calendar Targets (iMIP)

**Soubor:** `app/Services/Sync/SyncEngine.php` → `createOrUpdateBlockerInEmailTarget()`

```php
// Check if update is needed (for existing mappings)
if ($mapping) {
    $needsUpdate = false;
    $mappingStart = $mapping->event_start;
    $mappingEnd = $mapping->event_end;
    
    // Check if start/end time changed
    if ($mappingStart && $start && $start <= $maxTimestamp) {
        if (abs($mappingStart->getTimestamp() - $start->getTimestamp()) > 60) {
            $needsUpdate = true;
        }
    } elseif (!$mappingStart && $start) {
        $needsUpdate = true;
    }
    
    if ($mappingEnd && $end && $end <= $maxTimestamp) {
        if (abs($mappingEnd->getTimestamp() - $end->getTimestamp()) > 60) {
            $needsUpdate = true;
        }
    } elseif (!$mappingEnd && $end) {
        $needsUpdate = true;
    }
    
    if (!$needsUpdate) {
        // No changes - skip sending email
        Log::channel('sync')->debug('Email blocker unchanged, skipping iMIP', [
            'event_id' => $sourceEventId,
            'target_email' => $targetEmailConnection->target_email,
        ]);
        return;
    }
    
    $action = 'updated';
}

// Only send iMIP if needed
$success = $this->imipEmail->sendBlockerInvitation(...);
```

---

## 📊 Výsledky

### Před optimalizací:

| Metrika | Hodnota |
|---------|---------|
| Sync logů za hodinu | ~120 (20 eventů × 6 syncù) |
| API calls za hodinu | ~120 |
| iMIP emailů za hodinu | ~120 (pro email targets) |
| Dashboard | Spam "updated" logů |

### Po optimalizaci:

| Metrika | Hodnota |
|---------|---------|
| Sync logů za hodinu | ~20 (pouze při prvním syncu) |
| API calls za hodinu | ~0 (jen při skutečných změnách) |
| iMIP emailů za hodinu | ~0 (jen při skutečných změnách) |
| Dashboard | Jen relevantní změny |

### Úspora:

- 📉 **~83% méně DB logů**
- 📉 **~100% úspora API calls** (pro nezměněné události)
- 📉 **~100% úspora emailů** (pro email targets)
- 🚀 **Rychlejší sync** (méně operací)

---

## 🎯 Kdy se "updated" log objeví

Teď se "updated" log objeví **jen když se skutečně něco změnilo**:

### Scénáře pro update:

1. ✅ **Čas začátku se změnil** (>1 min rozdíl)
   - Uživatel posunul schůzku z 10:00 na 11:00
   - → Update blockeru + log

2. ✅ **Čas konce se změnil** (>1 min rozdíl)
   - Schůzka se prodloužila z 1h na 2h
   - → Update blockeru + log

3. ✅ **Obojí se změnilo**
   - Kompletní přesun schůzky
   - → Update blockeru + log

### Scénáře BEZ update:

1. ❌ **Žádná změna**
   - Sync běží každých 5 min, ale událost se nezměnila
   - → Skip (žádný API call, žádný log)

2. ❌ **Malý rozdíl (<1 min)**
   - Kvůli zaokrouhlení nebo timezone
   - → Skip (považuje se za stejný čas)

---

## 🔍 Debug Logy

Pokud chceš vidět, co se děje na pozadí:

### Storage logs (debug level):

```bash
# Zobrazí i skipped události
tail -f storage/logs/sync.log | grep -E "unchanged|skipping"
```

Uvidíš:
```
[2025-10-11 10:05:23] Blocker unchanged, skipping update
[2025-10-11 10:10:24] Email blocker unchanged, skipping iMIP
```

### Dashboard:

Zobrazuje **jen relevantní operace**:
- ✅ Created (nová událost)
- ✅ Updated (změněný čas)
- ✅ Deleted (zrušená událost)
- ✅ Skipped (filtrovaná událost)
- ✅ Error (chyba)

---

## 💡 Pro Pokročilé

### Rozšíření - detekce změny názvu

Aktuálně se kontroluje jen **čas**. Pokud bychom chtěli detekovat i změnu **blocker_title**, potřebovali bychom:

1. Uložit `blocker_title` do mappingu
2. Porovnat v check logice
3. Updatovat při změně

**Proč to zatím neděláme:**
- Název blockeru se mění vzácně (když user edituje pravidlo)
- Při změně pravidla se stejně deletují všechny blockery a vytvářejí nové

### Fallback pro chyby

Pokud `mapping->event_start` nebo `event_end` jsou `null` (staré záznamy), považuje se to za změnu → update proběhne.

```php
elseif (!$mappingStart && $start) {
    $needsUpdate = true; // Safe fallback
}
```

---

## ✅ Checklist

- [x] Detekce změn pro API calendar targets
- [x] Detekce změn pro email calendar targets
- [x] Tolerance 1 minuta pro zaokrouhlení
- [x] Debug logy pro skipped události
- [x] Early return - žádný zbytečný log
- [x] Dokumentace

---

## 📈 Monitoring

### Sleduj tyto metriky:

1. **Počet "updated" logů** - měl by klesnout ~83%
2. **API rate limits** - neměly by se už triggovat
3. **Email deliverability** - méně emailů = lepší reputation
4. **Sync rychlost** - měl by být rychlejší

### SQL queries pro monitoring:

```sql
-- Počet updateů za poslední hodinu
SELECT COUNT(*) 
FROM sync_logs 
WHERE action = 'updated' 
AND created_at > NOW() - INTERVAL 1 HOUR;

-- Porovnání před/po (minulý týden vs tento týden)
SELECT 
    WEEK(created_at) as week,
    action,
    COUNT(*) as count
FROM sync_logs
WHERE action IN ('created', 'updated')
GROUP BY WEEK(created_at), action
ORDER BY week DESC;
```

---

**Status:** 🎉 Optimalizace dokončena!

Dashboard je nyní čistý a zobrazuje jen relevantní změny.

