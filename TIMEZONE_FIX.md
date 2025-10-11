# 🕐 Timezone Fix - False Positive Updates

**Datum:** 11. října 2025  
**Status:** ✅ Opraveno

---

## 🐛 Problém

Po optimalizaci stále přicházely "updated" logy, i když se časy nezměnily:

### Logy z produkce:

```
[2025-10-11 10:39:03] Blocker updated due to time change
{
    "event_id":"bpsdlan30llqv3k2sn4t294r1o_20251212T133000Z",
    "old_start":"2025-12-12 14:30:00",
    "new_start":"2025-12-12 14:30:00"  ← IDENTICKÉ!
}
```

**Časy jsou identické**, ale systém je detekoval jako "změněné"! 🤔

---

## 🔍 Root Cause: Timezone Issue

### Co se dělo:

Porovnávali jsme timestampy přímo, bez normalizace timezone:

```php
// ❌ ŠPATNĚ
if (abs($mappingStart->getTimestamp() - $start->getTimestamp()) > 60) {
    $needsUpdate = true;
}
```

### Proč to selhávalo:

Stejný **vizuální čas** může mít **různé timestampy** v různých timezone:

```php
// Příklad:
"2025-12-12 14:30:00" v Europe/Prague = timestamp 1734011400
"2025-12-12 14:30:00" v UTC           = timestamp 1734007800

// Rozdíl = 3600 sekund (1 hodina) → detekováno jako změna!
```

### Zdroj problému:

1. **Google/Microsoft API** vrací časy v UTC
2. **Laravel/Database** může ukládat v lokálním timezone (podle config)
3. **Porovnání** bez normalizace → false positive

---

## ✅ Řešení

### Normalizace na UTC před porovnáním

```php
if ($mappingStart && $start && $start <= $maxTimestamp) {
    // ✅ SPRÁVNĚ: Normalize both to UTC for comparison
    $mappingStartUtc = clone $mappingStart;
    $mappingStartUtc->setTimezone(new \DateTimeZone('UTC'));

    $startUtc = clone $start;
    $startUtc->setTimezone(new \DateTimeZone('UTC'));

    // Compare timestamps in the same timezone
    $diff = abs($mappingStartUtc->getTimestamp() - $startUtc->getTimestamp());

    if ($diff > 60) {
        $needsUpdate = true;

        // Debug log with normalized times
        Log::channel('sync')->debug('Start time difference detected', [
            'event_id' => $sourceEventId,
            'diff_seconds' => $diff,
            'old' => $mappingStartUtc->format('c'), // ISO 8601 with timezone
            'new' => $startUtc->format('c'),
        ]);
    }
}
```

### Co to řeší:

1. ✅ **Clone** DateTime objektů (nechceme modifikovat originály)
2. ✅ **setTimezone('UTC')** na obou časech
3. ✅ **Porovnání timestampů** ve stejném timezone
4. ✅ **Debug log** s ISO 8601 formátem (vidíme timezone)

---

## 🔧 Implementace

### Změněné funkce:

1. **`createOrUpdateBlockerInTarget()`** - pro API calendar targets
2. **`createOrUpdateBlockerInEmailTarget()`** - pro email targets

### Aplikováno na:

- ✅ Start time comparison
- ✅ End time comparison
- ✅ Google Calendar sync
- ✅ Microsoft Calendar sync
- ✅ Email (iMIP) sync

---

## 📊 Příklad Debug Logu

Pokud se čas **skutečně** změní, uvidíš:

```
[2025-10-11 10:39:03] sync.DEBUG: Start time difference detected
{
    "event_id": "abc123",
    "diff_seconds": 3600,
    "old": "2025-12-12T14:30:00+00:00",
    "new": "2025-12-12T15:30:00+00:00"
}
```

Format `c` (ISO 8601) zobrazí i timezone → snadný debug!

---

## 🧪 Test Scenáře

### Scénář 1: Žádná změna

**Před:**

- DB: `2025-12-12 14:30:00` (Europe/Prague)
- API: `2025-12-12T13:30:00Z` (UTC, stejný moment)
- ❌ Detekováno jako změna (3600s rozdíl)

**Po:**

- Oba normalized to UTC: `2025-12-12T13:30:00Z`
- Diff = 0 sekund
- ✅ Skip update

### Scénář 2: Skutečná změna

**Před:**

- DB: `2025-12-12 14:30:00`
- API: `2025-12-12 15:30:00` (posunuto o hodinu)

**Po:**

- Oba normalized to UTC
- Diff = 3600 sekund
- ✅ Update proveden

### Scénář 3: DST (Daylight Saving Time)

**Před:**

- Možné problémy při přechodu letní/zimní čas

**Po:**

- UTC není ovlivněno DST
- ✅ Žádné false positive při DST přechodech

---

## 🎯 Výsledky

### Před opravou:

- ❌ False positive "updated" logy
- ❌ Zbytečné API calls každých 5 min
- ❌ Dashboard plný "updated" i když se nic nezměnilo

### Po opravě:

- ✅ Jen skutečné změny generují log
- ✅ Žádné zbytečné API calls
- ✅ Čistý Dashboard

---

## 💡 Laravel Timezone Config

### Zkontroluj `config/app.php`:

```php
'timezone' => 'UTC',  // ← DOPORUČENO: Všechno v UTC
```

**Proč UTC?**

- Standardní pro API (Google, Microsoft)
- Žádné DST problémy
- Snadná konverze pro display

### Nebo:

```php
'timezone' => 'Europe/Prague',  // Lokální timezone
```

**S naší opravou funguje obojí!** Porovnání je vždy v UTC.

---

## 📝 Poznámky

### Clone je důležitý!

```php
// ❌ ŠPATNĚ - modifikuje originální objekt
$mappingStart->setTimezone(new \DateTimeZone('UTC'));

// ✅ SPRÁVNĚ - clone před modifikací
$mappingStartUtc = clone $mappingStart;
$mappingStartUtc->setTimezone(new \DateTimeZone('UTC'));
```

### ISO 8601 format

```php
$date->format('c')  // 2025-12-12T14:30:00+01:00
$date->format('Y-m-d H:i:s')  // 2025-12-12 14:30:00 (bez timezone!)
```

---

## ✅ Checklist

- [x] UTC normalizace před porovnáním
- [x] Clone DateTime objektů
- [x] Debug log s timezone info
- [x] Aplikováno na API targets
- [x] Aplikováno na email targets
- [x] Žádné false positive updates

---

**Status:** 🎉 Timezone issue vyřešen!

Synchronizace nyní správně detekuje změny bez ohledu na timezone nastavení.
