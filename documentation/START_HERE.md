# 🚀 ZAČNĚTE TADY - Nasazení SyncMyDay

> **Pro nasazení na komerční hosting začněte tímto dokumentem**

---

## 👋 Vítejte!

Tento dokument vám pomůže nasadit aplikaci SyncMyDay na váš hosting v několika krocích.

---

## ⚡ RYCHLÝ START (5 minut přípravy)

### Pro sdílený hosting (Wedos, Forpsi, WebSupport, atd.):

```bash
# 1. Otevřete terminál a přejděte do složky projektu
cd /Users/lukas/SyncMyDay

# 2. Spusťte pomocný skript
./prepare-shared-hosting.sh

# 3. Postupujte podle instrukcí na obrazovce
```

Skript vytvoří vše potřebné pro upload na hosting! ✨

---

## 📚 Dokumentace (vyberte si podle vašeho hostingu)

### 🔰 Mám sdílený hosting (cPanel/FTP)

**Nejdřív přečtěte:**
1. 📄 **[JAK_NASADIT.md](JAK_NASADIT.md)** - Stručný průvodce česky
2. 📄 **[NASAZENI_SDILENY_HOSTING.md](NASAZENI_SDILENY_HOSTING.md)** - Detailní instrukce

**Pak použijte:**
- 🔧 `./prepare-shared-hosting.sh` - Vytvoří archiv k nahrání

---

### 💻 Mám VPS (vlastní server s SSH)

**Nejdřív přečtěte:**
1. 📄 **[NASAZENI_PRODUKCE.md](NASAZENI_PRODUKCE.md)** - Kompletní průvodce
2. 📄 **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** - Srovnání možností

**Pak použijte:**
- 🔧 `./deploy-helper.sh` - Interaktivní pomocník

---

## 🎯 Rozhodovací strom

```
┌─────────────────────────────────────────┐
│ Jaký typ hostingu máte?                 │
└─────────────────┬───────────────────────┘
                  │
        ┌─────────┴─────────┐
        │                   │
        ▼                   ▼
  ┌──────────┐        ┌──────────┐
  │ Sdílený  │        │   VPS    │
  │ hosting  │        │  server  │
  └────┬─────┘        └────┬─────┘
       │                   │
       │                   │
       ▼                   ▼
  📄 JAK_NASADIT.md   📄 NASAZENI_PRODUKCE.md
  🔧 prepare-shared-  🔧 deploy-helper.sh
     hosting.sh
```

### Nevíte, co máte?

**Sdílený hosting = když:**
- ✅ Platíte ~50-200 Kč/měsíc
- ✅ Máte přístup přes cPanel nebo FTP
- ✅ Nemůžete instalovat software

**VPS = když:**
- ✅ Platíte ~200-500 Kč/měsíc
- ✅ Máte SSH přístup jako root
- ✅ Můžete instalovat software (Docker, atd.)

---

## 📁 Přehled všech souborů

### 🔥 Hlavní průvodci (začněte tady):

| Soubor | Pro koho | Co obsahuje |
|--------|----------|-------------|
| **START_HERE.md** | Všichni | Tento soubor - začněte tady |
| **JAK_NASADIT.md** | Sdílený hosting | Stručný průvodce česky |
| **NASAZENI_SDILENY_HOSTING.md** | Sdílený hosting | Detailní instrukce |
| **NASAZENI_PRODUKCE.md** | VPS | Kompletní průvodce pro VPS |
| **DEPLOYMENT_GUIDE.md** | Všichni | Porovnání všech možností |

### 🔧 Pomocné skripty:

| Soubor | Použití |
|--------|---------|
| `prepare-shared-hosting.sh` | Vytvoří archiv pro sdílený hosting |
| `deploy-helper.sh` | Interaktivní pomocník pro VPS |

### 📖 Další dokumentace:

| Soubor | Účel |
|--------|------|
| `README.md` | Celkový popis projektu |
| `OAUTH_SETUP.md` | Nastavení Google/Microsoft OAuth |
| `STRIPE_TRIAL_SETUP.md` | Nastavení Stripe plateb |
| `EMAIL_SYSTEM.md` | Konfigurace emailů |
| `TROUBLESHOOTING.md` | Řešení problémů |

---

## ✅ Checklist před začátkem

Ujistěte se, že máte:

### Hosting:
- [ ] Doménu (např. `mojedomena.cz`)
- [ ] Hosting s PHP 8.2+ a MySQL
- [ ] Přístup do administrace (cPanel/SSH)

### API účty (všechny zdarma):
- [ ] Google Cloud účet → [console.cloud.google.com](https://console.cloud.google.com/)
- [ ] Microsoft Azure účet → [portal.azure.com](https://portal.azure.com/)
- [ ] Stripe účet → [dashboard.stripe.com](https://dashboard.stripe.com/)
- [ ] SendGrid účet (volitelně) → [sendgrid.com](https://sendgrid.com/)

### Lokálně na počítači:
- [ ] PHP 8.2+ nainstalované
- [ ] Composer nainstalovaný
- [ ] Terminál/příkazový řádek

---

## 🎬 Krok za krokem (sdílený hosting)

### 1️⃣ Příprava lokálně (10 minut)

```bash
cd /Users/lukas/SyncMyDay
./prepare-shared-hosting.sh
```

Otevřete `.env.shared-hosting` a doplňte:
- Údaje k databázi
- Google OAuth klíče
- Microsoft OAuth klíče
- Stripe klíče
- Email SMTP údaje

### 2️⃣ Získání API klíčů (20 minut)

Postupujte podle sekcí v [JAK_NASADIT.md](JAK_NASADIT.md):
- Google OAuth (5 min)
- Microsoft OAuth (5 min)
- Stripe (5 min)
- SendGrid (5 min)

### 3️⃣ Upload na hosting (5 minut)

1. Přihlaste se do cPanel
2. File Manager → nahrajte archiv
3. Rozbalte archiv
4. Přejmenujte `.env.shared-hosting` → `.env`

### 4️⃣ Instalace (2 minuty)

1. Otevřete `https://vase-domena.cz/install.php`
2. Spusťte instalaci
3. **SMAŽTE `install.php`!**

### 5️⃣ Nastavení cron (3 minuty)

V cPanel → Cron Jobs přidejte:

```
*/5 * * * * cd /home/uzivatel/public_html && php artisan queue:work --stop-when-empty
* * * * * cd /home/uzivatel/public_html && php artisan schedule:run
```

### 6️⃣ Hotovo! 🎉

Otevřete `https://vase-domena.cz` a zaregistrujte se!

---

## 💡 Tipy pro úspěch

### ✅ DO:
- ✅ Používejte LIVE klíče pro Stripe (ne test!)
- ✅ Nastavte silná hesla pro databázi
- ✅ Zkontrolujte, že máte PHP 8.2+
- ✅ Aktivujte SSL certifikát (HTTPS)
- ✅ Nastavte cron joby

### ❌ DON'T:
- ❌ Nesdílejte `.env` soubor
- ❌ Nenechávejte `install.php` na serveru
- ❌ Nepoužívejte test klíče v produkci
- ❌ Nezapomeňte na cron joby
- ❌ Nenastavujte `APP_DEBUG=true` v produkci

---

## 🆘 Potřebujete pomoc?

### Chyby při příprave:
```bash
# Zkontrolujte PHP verzi
php -v

# Zkontrolujte Composer
composer --version

# Reinstalujte závislosti
composer install
```

### Chyby na serveru:
```bash
# Zkontrolujte logy
tail -f storage/logs/laravel.log

# Oprávnění
chmod -R 775 storage bootstrap/cache
chmod 600 .env
```

### Pomoc online:
- 📧 Kontaktujte support vašeho hostingu
- 📖 Přečtěte si [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
- 🔍 Zkontrolujte `storage/logs/laravel.log`

---

## 📊 Porovnání možností

| Vlastnost | Sdílený hosting | VPS |
|-----------|-----------------|-----|
| **Cena** | 50-200 Kč/měsíc | 200-500 Kč/měsíc |
| **Obtížnost** | Snadné (cPanel) | Středně těžké (Linux) |
| **Synchronizace** | 5 minut | 1-2 minuty (real-time) |
| **Cache** | Database (pomalejší) | Redis (rychlé) |
| **Škálovatelnost** | Do ~50 uživatelů | Stovky uživatelů |
| **Doporučeno pro** | Začátečníky, malé projekty | Pokročilé, větší projekty |

---

## 🎯 Co dál po nasazení?

### 1. Nastavte se jako admin

```bash
php artisan tinker
>>> $user = User::first();
>>> $user->is_admin = true;
>>> $user->save();
```

### 2. Otestujte funkce

- ✅ Registrace
- ✅ Přihlášení
- ✅ Připojení Google kalendáře
- ✅ Připojení Microsoft kalendáře
- ✅ Vytvoření sync rule
- ✅ Kontrola synchronizace

### 3. Monitoring

Nastavte uptime monitoring:
- [UptimeRobot](https://uptimerobot.com/) (zdarma)
- Sledovat: `https://vase-domena.cz/health`

### 4. Zálohy

Nastavte automatické zálohy databáze v cPanel.

---

## 🌟 Často kladené otázky

**Q: Jak dlouho to trvá?**  
A: První nasazení ~40 minut (včetně získání API klíčů). Poté aktualizace ~5 minut.

**Q: Kolik to stojí?**  
A: Minimum ~67 Kč/měsíc (hosting) + 200 Kč/rok (doména) = **~1000 Kč/rok celkem**

**Q: Je to bezpečné?**  
A: Ano, tokeny jsou šifrované, používáme HTTPS, a neukládáme citlivá data.

**Q: Můžu to vyzkoušet zdarma?**  
A: Většina hostingů má 30denní záruku vrácení peněz.

**Q: Co když něco pokazím?**  
A: Databázi lze snadno obnovit ze zálohy. Projekt lze znovu nahrát.

**Q: Potřebuji znát programování?**  
A: Ne, stačí postupovat krok za krokem podle průvodce.

**Q: Funguje to na mobilu?**  
A: Ano, web je responzivní a funguje na všech zařízeních.

**Q: Kolik uživatelů to zvládne?**  
A: Na sdíleném hostingu ~20-50 aktivních uživatelů. Na VPS stovky.

---

## 🎉 Shrnutí

1. **Spusťte** `./prepare-shared-hosting.sh`
2. **Vyplňte** API klíče do `.env.shared-hosting`  
3. **Nahrajte** na hosting  
4. **Spusťte** install.php  
5. **Nastavte** cron joby  
6. **Hotovo!** 🚀

---

## 📞 Kontakty a odkazy

### Doporučené hostingy:
- 🇨🇿 [Wedos](https://www.wedos.cz/) - od 49 Kč/měsíc
- 🇸🇰 [WebSupport](https://www.websupport.sk/) - od 2.99 €/měsíc
- 🇩🇪 [Hetzner](https://www.hetzner.com/) - VPS od 4.15 €/měsíc

### API služby:
- [Google Cloud Console](https://console.cloud.google.com/)
- [Microsoft Azure Portal](https://portal.azure.com/)
- [Stripe Dashboard](https://dashboard.stripe.com/)
- [SendGrid](https://sendgrid.com/)

### Monitoring:
- [UptimeRobot](https://uptimerobot.com/) - zdarma

---

**Přeji hodně štěstí s nasazením! 🍀**

**Máte-li jakékoli problémy, podívejte se do detailních průvodců nebo kontaktujte support vašeho hostingu.**

---

*Vytvořeno pro SyncMyDay - Privacy-first kalendářová synchronizace* ❤️


