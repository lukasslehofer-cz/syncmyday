# cPanel Cron Job Setup

## 📅 Jak nastavit Cron Job v cPanel

### Krok 1: Přihlaste se do cPanel

Otevřete: `https://cpanel.cesky-hosting.cz` (nebo vaše cPanel URL)

### Krok 2: Najděte "Cron Jobs"

V cPanel vyhledejte sekci:

- **"Cron Jobs"**
- nebo **"Naplánované úlohy"**
- nebo **"Advanced" → "Cron Jobs"**

### Krok 3: Přidejte nový Cron Job

#### Common Settings (Běžná nastavení):

V rolovacím menu vyberte: **"Once Per Minute"** (Každou minutu)

#### Nebo nastavte ručně:

| Pole        | Hodnota | Popis             |
| ----------- | ------- | ----------------- |
| Minuta      | `*`     | Každou minutu     |
| Hodina      | `*`     | Každou hodinu     |
| Den         | `*`     | Každý den         |
| Měsíc       | `*`     | Každý měsíc       |
| Den v týdnu | `*`     | Každý den v týdnu |

### Krok 4: Zadejte Command (Příkaz)

**DŮLEŽITÉ:** Upravte cestu podle vašeho serveru!

```bash
cd /home/VASE_USERNAME/public_html/syncmyday && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Kde najít správnou cestu:**

1. **Cesta k projektu:**

   - V cPanel → File Manager
   - Podívejte se, kde je složka `syncmyday`
   - Běžně: `/home/username/public_html/syncmyday`
   - Nebo: `/home/username/domains/syncmyday.cz/public_html`

2. **Cesta k PHP:**
   - V cPanel → MultiPHP Manager
   - Nebo přes SSH: `which php`
   - Běžně: `/usr/bin/php` nebo `/usr/bin/php80`

### Příklady:

**Pokud je projekt v public_html:**

```bash
cd /home/myusername/public_html/syncmyday && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Pokud je projekt v doménové složce:**

```bash
cd /home/myusername/domains/syncmyday.cz/public_html && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

**Pokud používáte PHP 8.0:**

```bash
cd /home/myusername/public_html/syncmyday && /usr/bin/php80 artisan schedule:run >> /dev/null 2>&1
```

### Krok 5: Uložte

Klikněte na **"Add New Cron Job"** nebo **"Přidat novou úlohu"**

---

## ✅ Jak ověřit, že to funguje

### 1. Počkejte 1-2 minuty

Cron job se spustí poprvé během minuty.

### 2. Zkontrolujte logy

V cPanel:

- **Cron Jobs** → **View Cron Job History** (pokud je dostupné)

Nebo stáhněte přes FTP:

- `/path/to/syncmyday/storage/logs/laravel.log`

### 3. Test

Pokud máte SSH přístup, spusťte manuálně:

```bash
cd /path/to/syncmyday
php artisan schedule:run
```

Měli byste vidět:

```
No scheduled commands are ready to run.
```

Nebo pokud je 9:00:

```
Running scheduled command: Artisan::call('trial:send-ending-notifications')
```

---

## 🎯 Co tento cron job dělá

Každou minutu zkontroluje, jestli není čas spustit nějaký scheduled task:

| Čas                   | Task                                  | Co se děje                     |
| --------------------- | ------------------------------------- | ------------------------------ |
| Každých 5 minut       | `calendars:sync`                      | Synchronizuje kalendáře        |
| Každých 6 hodin       | `webhooks:renew`                      | Obnovuje webhooks              |
| Každou hodinu         | `connections:check`                   | Kontroluje připojení           |
| Každý den v 00:00     | `logs:clean`                          | Čistí staré logy               |
| **Každý den v 09:00** | **`trial:send-ending-notifications`** | **Posílá trial ending emaily** |

Laravel automaticky rozhodne, který task má běžet podle času.

---

## 🔧 Troubleshooting

### "Permission denied"

Přidejte oprávnění pro spuštění:

```bash
chmod +x /path/to/syncmyday/artisan
```

Nebo v cron příkazu použijte:

```bash
cd /path/to/syncmyday && /usr/bin/php artisan schedule:run
```

(s explicitní cestou k PHP)

### "Command not found"

Použijte absolutní cestu k PHP:

```bash
/usr/bin/php artisan schedule:run
```

Ne jen `php artisan schedule:run`

### Cron běží, ale nic se neděje

Zkontrolujte logy:

```bash
tail -f storage/logs/laravel.log
```

Nebo přidejte output do souboru pro debugging:

```bash
cd /path/to/syncmyday && /usr/bin/php artisan schedule:run >> /tmp/cron.log 2>&1
```

Pak se podívejte na `/tmp/cron.log`

---

## 📸 Screenshot příklad

V cPanel by to mělo vypadat zhruba takto:

```
┌─────────────────────────────────────────────────────┐
│ Add New Cron Job                                    │
├─────────────────────────────────────────────────────┤
│ Common Settings: [Once Per Minute ▼]                │
│                                                      │
│ Minute:  [*]    Hour:    [*]    Day:     [*]       │
│ Month:   [*]    Weekday: [*]                        │
│                                                      │
│ Command:                                            │
│ [cd /home/user/public_html/syncmyday && /usr/b...] │
│                                                      │
│               [Add New Cron Job]                    │
└─────────────────────────────────────────────────────┘
```

---

✅ **Hotovo!** Cron job je nastaven a Laravel scheduled tasks budou běžet automaticky.
