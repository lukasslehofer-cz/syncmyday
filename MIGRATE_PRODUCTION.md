# Migrace na produkci

Laravel automaticky detekuje production environment a ptá se na potvrzení.

## Řešení:

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

```bash
php artisan migrate:rollback --force --step=1
```

Tím vrátíš poslední migraci zpět.
