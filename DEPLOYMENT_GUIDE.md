# 🚀 Průvodce nasazením SyncMyDay

> **Rychlý průvodce pro výběr správné metody nasazení**

---

## 📊 Jaký hosting mám použít?

### Rozhodovací strom:

```
Máte možnost instalovat software na serveru? (SSH root přístup)
│
├─ ANO → Použijte VPS/Dedicated Server
│         📖 Postupujte podle: NASAZENI_PRODUKCE.md
│         💰 Cena: ~200-500 Kč/měsíc
│         ⭐ Funkčnost: 100% (včetně real-time sync)
│
└─ NE → Máte jen cPanel/FTP přístup (sdílený hosting)
          📖 Postupujte podle: NASAZENI_SDILENY_HOSTING.md
          💰 Cena: ~50-200 Kč/měsíc
          ⭐ Funkčnost: ~80% (synchronizace každých 5 minut)
```

---

## 🎯 Rychlý start

### Pro VPS (doporučeno):

1. **Připravte server**: Ubuntu 20.04+ s Dockerem
2. **Přečtěte si**: [NASAZENI_PRODUKCE.md](NASAZENI_PRODUKCE.md)
3. **Použijte helper**: `./deploy-helper.sh`
4. **Nasaďte** podle instrukcí

**Výhody:**
- ✅ Real-time synchronizace (1-2 minuty)
- ✅ Webhooky fungují perfektně
- ✅ Redis cache (rychlé)
- ✅ Plná kontrola

**Nevýhody:**
- ❌ Vyžaduje základní znalost Linuxu
- ❌ Dražší (~200 Kč/měsíc)

---

### Pro sdílený hosting:

1. **Zkontrolujte požadavky**: PHP 8.2+, MySQL, Cron
2. **Přečtěte si**: [NASAZENI_SDILENY_HOSTING.md](NASAZENI_SDILENY_HOSTING.md)
3. **Použijte přípravu**: `./prepare-shared-hosting.sh`
4. **Nahrajte** archiv na hosting
5. **Spusťte** install.php

**Výhody:**
- ✅ Levnější (~50-200 Kč/měsíc)
- ✅ Jednodušší správa (cPanel)
- ✅ Základní funkčnost zachována

**Nevýhody:**
- ❌ Synchronizace každých 5 minut (ne real-time)
- ❌ Webhooky se zpracovávají s prodlevou
- ❌ Pomalejší (database cache místo Redis)

---

## 📚 Dokumentace

### Hlavní průvodce:

| Dokument | Popis | Pro koho |
|----------|-------|----------|
| **NASAZENI_PRODUKCE.md** | Kompletní průvodce pro VPS/Dedicated | Pokročilí uživatelé s VPS |
| **NASAZENI_SDILENY_HOSTING.md** | Průvodce pro sdílený hosting | Začátečníci, kteří mají jen cPanel |
| **DEPLOYMENT_GUIDE.md** | Tento soubor - rychlý přehled | Všichni |

### Pomocné soubory:

| Soubor | Použití |
|--------|---------|
| `deploy-helper.sh` | Interaktivní pomocník pro VPS deployment |
| `prepare-shared-hosting.sh` | Vytvoří archiv pro sdílený hosting |
| `.env.example` | Šablona pro konfiguraci (VPS) |

### Další dokumentace:

- `README.md` - Celkový přehled projektu
- `OAUTH_SETUP.md` - Nastavení Google/Microsoft OAuth
- `STRIPE_TRIAL_SETUP.md` - Nastavení Stripe plateb
- `EMAIL_SYSTEM.md` - Konfigurace emailů
- `TROUBLESHOOTING.md` - Řešení problémů

---

## 🛠 Pomocné skripty

### 1. deploy-helper.sh (pro VPS)

Interaktivní pomocník pro přípravu VPS nasazení.

```bash
chmod +x deploy-helper.sh
./deploy-helper.sh
```

**Možnosti:**
- Vygenerovat šifrovací klíče
- Vytvořit .env soubor
- Otestovat připojení k databázi
- Zkontrolovat PHP požadavky
- Deployment checklist
- Všechno najednou

### 2. prepare-shared-hosting.sh (pro sdílený hosting)

Vytvoří archiv připravený pro upload na sdílený hosting.

```bash
chmod +x prepare-shared-hosting.sh
./prepare-shared-hosting.sh
```

**Co udělá:**
- Nainstaluje produkční závislosti
- Vytvoří `.env.shared-hosting` s konfigurací
- Vytvoří `install.php` instalační skript
- Zabalí projekt do archivu
- Zobrazí další kroky

---

## ⚡ Rychlé nasazení (TL;DR)

### VPS s Dockerem (5 minut):

```bash
# Na serveru:
git clone <repo> /var/www/syncmyday
cd /var/www/syncmyday
cp .env.example .env
nano .env  # Vyplňte údaje
docker-compose up -d
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan config:cache
```

### Sdílený hosting (10 minut):

```bash
# Lokálně:
./prepare-shared-hosting.sh

# Pak:
# 1. Nahrajte archiv přes cPanel
# 2. Rozbalte na serveru
# 3. Přejmenujte .env.shared-hosting na .env
# 4. Otevřete https://domena.cz/install.php
# 5. Smažte install.php
# 6. Nastavte cron (každých 5 minut)
```

---

## 🔑 Kritické kroky (nesmíte zapomenout!)

### Před nasazením:

- [ ] Vygenerovat `APP_KEY`
- [ ] Vygenerovat `TOKEN_ENCRYPTION_KEY`
- [ ] Nastavit `APP_ENV=production`
- [ ] Nastavit `APP_DEBUG=false`
- [ ] Nakonfigurovat Google OAuth (produkční)
- [ ] Nakonfigurovat Microsoft OAuth (produkční)
- [ ] Nakonfigurovat Stripe LIVE klíče
- [ ] Nastavit email službu
- [ ] SSL certifikát aktivní

### Po nasazení:

- [ ] Spustit migrace (`php artisan migrate --force`)
- [ ] Nastavit oprávnění složek (775 storage/)
- [ ] Nastavit cron joby
- [ ] Cache optimalizace (`php artisan config:cache`)
- [ ] Test registrace
- [ ] Test připojení kalendáře
- [ ] Test synchronizace

---

## 💰 Porovnání nákladů

### VPS hosting:

| Poskytovatel | Cena/měsíc | RAM | Hodnocení |
|--------------|------------|-----|-----------|
| Hetzner CPX11 | 4.15 € (~100 Kč) | 2 GB | ⭐⭐⭐⭐⭐ |
| Wedos VPS Start | 199 Kč | 1 GB | ⭐⭐⭐⭐ |
| DigitalOcean Basic | $6 (~140 Kč) | 1 GB | ⭐⭐⭐⭐ |

### Sdílený hosting:

| Poskytovatel | Cena/měsíc | PHP 8.2+ | Hodnocení |
|--------------|------------|----------|-----------|
| Wedos WebHosting M | 49 Kč | ✅ | ⭐⭐⭐⭐⭐ |
| WebSupport Standard | 2.99 € (~75 Kč) | ✅ | ⭐⭐⭐⭐ |
| Forpsi WebHosting M | 59 Kč | ✅ | ⭐⭐⭐ |

---

## 🤔 Který hosting vybrat?

### Použijte VPS, pokud:

- ✅ Potřebujete real-time synchronizaci
- ✅ Máte základní znalosti Linuxu
- ✅ Plánujete větší počet uživatelů (100+)
- ✅ Chcete plnou kontrolu
- ✅ Budget ~200-500 Kč/měsíc

### Použijte sdílený hosting, pokud:

- ✅ Budget ~50-200 Kč/měsíc
- ✅ Nevadí vám synchronizace každých 5 minut
- ✅ Máte malý počet uživatelů (do 50)
- ✅ Preferujete jednoduchou správu (cPanel)
- ✅ Žádné znalosti Linuxu

---

## 📞 Potřebujete poradit?

### Časté otázky:

**Q: Jaký je rozdíl ve funkčnosti?**

| Funkce | VPS | Sdílený hosting |
|--------|-----|-----------------|
| Synchronizace | 1-2 minuty | 5 minut |
| Webhooky | Real-time | S prodlevou |
| Cache | Redis (rychlé) | Database (pomalejší) |
| Queue workers | Běží pořád | Cron každých 5 min |
| Škálovatelnost | Vysoká | Omezená |

**Q: Můžu začít na sdíleném hostingu a později přejít na VPS?**

A: Ano! Data přenesete:
1. Exportujte databázi (phpMyAdmin → Export)
2. Nasaďte na VPS podle NASAZENI_PRODUKCE.md
3. Importujte databázi
4. Zkopírujte `.env` (použijte stejný TOKEN_ENCRYPTION_KEY!)
5. Změňte DNS na nový server

**Q: Kolik to bude stát celkem?**

A: Minimálně:
- Hosting: 50-500 Kč/měsíc (podle typu)
- Doména: ~200 Kč/rok
- SSL: Zdarma (Let's Encrypt)
- Email: Zdarma (SendGrid 100 emailů/den)
- **Celkem: ~70-520 Kč/měsíc + 200 Kč/rok doména**

**Q: Potřebuji doménu?**

A: Ano, pro OAuth a SSL certifikát. Doména .cz stojí ~200 Kč/rok.

---

## ✅ Finální checklist

Před spuštěním ověřte:

```
BEZPEČNOST:
□ APP_DEBUG=false
□ APP_ENV=production
□ SESSION_SECURE_COOKIE=true
□ Silná hesla pro DB
□ .env má oprávnění 600
□ SSL certifikát aktivní

API & SLUŽBY:
□ Google OAuth (produkční credentials)
□ Microsoft OAuth (produkční credentials)
□ Stripe LIVE klíče (ne test!)
□ Email služba nakonfigurována

DATABÁZE & CACHE:
□ Databáze vytvořena
□ Migrace spuštěny
□ Cache optimalizována

MONITORING:
□ Cron joby nastaveny
□ Zálohy nakonfigurovány
□ Uptime monitoring (UptimeRobot)

TESTING:
□ Testovací registrace funguje
□ OAuth připojení funguje
□ Sync rule vytvořena a synchronizuje
□ Webhook přijat (zkontrolujte logy)
```

---

## 🎉 Hotovo!

Po dokončení nasazení byste měli mít funkční aplikaci na:

- 🌐 **Web**: https://vase-domena.cz
- 👤 **Registrace**: https://vase-domena.cz/register
- 🔗 **OAuth**: Google + Microsoft kalendáře
- 💳 **Platby**: Stripe Checkout
- 📧 **Emaily**: Automatické notifikace

**Hodně štěstí! 🚀**

---

*Pro detailní instrukce viz:*
- *VPS: [NASAZENI_PRODUKCE.md](NASAZENI_PRODUKCE.md)*
- *Sdílený hosting: [NASAZENI_SDILENY_HOSTING.md](NASAZENI_SDILENY_HOSTING.md)*


