# Migrace na produkci

## Řešení pro Shared Hosting (bez artisan podpory):

Pokud shared hosting neumožňuje spouštět `php artisan migrate --force`, použij HTTP endpoint:

```bash
https://syncmyday.cz/migrate.php?token=YOUR_CRON_SECRET
```

Script automaticky:
- ✅ Zkontroluje, které migrace ještě neproběhly
- ✅ Spustí je postupně
- ✅ Vrátí JSON s výsledky

## Lokální vývoj / VPS s plným přístupem:

```bash
php artisan migrate --force
```

Flag `--force` potvrdí, že víš co děláš a migraci chceš skutečně spustit.

## Alternativně (interaktivně):

Pokud chceš vidět prompt a odpovědět manuálně:

```bash
php artisan migrate
# Odpověz: yes
```

## Rollback (pokud by něco selhalo):

Pro shared hosting s HTTP:
```bash
https://syncmyday.cz/migrate.php?token=YOUR_CRON_SECRET&rollback=1
```

Pro VPS/lokál:
```bash
php artisan migrate:rollback --force --step=1
```

Tím vrátíš poslední migraci zpět.
