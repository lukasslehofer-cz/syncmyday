# Stripe Test Data Cleanup Instructions

## 🎯 Účel

Tento skript slouží k vyčištění testovacích Stripe dat z databáze při přechodu z test mode na live mode.

## ⚠️ Kdy použít

Použijte tento skript když:
- Přecházíte z Stripe test mode na live mode
- Vidíte chybu: `No such customer: 'cus_XXX'` v logách
- Databáze obsahuje test customer IDs, ale používáte live Stripe klíče

## 📝 Návod k použití

### Krok 1: Nastavit secret klíč

Otevřete soubor `public/cleanup-stripe-test-data.php` a změňte:

```php
define('CLEANUP_SECRET', 'change-this-to-random-string-123456');
```

Na něco bezpečného, například:

```php
define('CLEANUP_SECRET', 'muj-tajny-klic-789xyz-abc');
```

### Krok 2: Spustit skript

Otevřete v prohlížeči:

```
https://vasedomena.cz/cleanup-stripe-test-data.php?secret=muj-tajny-klic-789xyz-abc
```

**POZOR:** Použijte stejný secret klíč, jaký jste nastavili v kroku 1!

### Krok 3: Zkontrolovat a potvrdit

1. Skript vám ukáže seznam uživatelů s Stripe daty
2. Zkontrolujte, že jsou to správní uživatelé
3. Klikněte na **"Yes, Clean Up Now"**

### Krok 4: Smazat skript (DŮLEŽITÉ!)

Po úspěšném vyčištění **OKAMŽITĚ SMAŽTE** soubor ze serveru:

```bash
rm public/cleanup-stripe-test-data.php
```

Nebo přes FTP smažte: `public/cleanup-stripe-test-data.php`

## 🔒 Bezpečnost

- ✅ Skript je chráněný secret klíčem
- ✅ Zobrazí preview před vymazáním
- ✅ Vyžaduje potvrzení
- ⚠️ **SMAZAT PO POUŽITÍ!**

## 📊 Co skript dělá

1. Najde všechny uživatele s `stripe_customer_id` nebo `stripe_subscription_id`
2. Zobrazí je v tabulce
3. Po potvrzení nastaví oba sloupce na `NULL`
4. Uživatelé si budou moci znovu zadat platební kartu s live Stripe klíči

## 🆘 Troubleshooting

### Chyba: "Access denied"
- Zkontrolujte, že používáte správný secret klíč v URL

### Chyba: "500 Internal Server Error"
- Zkontrolujte, že máte správně nastavené .env (DB připojení)
- Zkontrolujte `storage/logs/laravel.log`

### Skript nic nenašel
- To je dobře! Znamená to, že databáze neobsahuje test Stripe data

## 🔄 Alternativa: Artisan Command

Pokud máte přístup k SSH, můžete použít:

```bash
php artisan stripe:clean-test-data
```

Tento command dělá totéž, ale je bezpečnější (nepotřebuje veřejný PHP soubor).

## 📞 Podpora

Pokud narazíte na problém, kontaktujte vývojáře.

