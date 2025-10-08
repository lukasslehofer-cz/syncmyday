# 🚀 Email Calendar Sync - Quickstart

## ✅ Implementace dokončena!

Email-based calendar sync je **plně funkční** a připravený k testování na lokálním prostředí.

---

## 📋 Co bylo implementováno

### 1. **Database**

- ✅ `email_calendar_connections` tabulka
- ✅ Extended `sync_event_mappings` (podpora pro email source)
- ✅ Migrace proběhly úspěšně

### 2. **Backend**

- ✅ `EmailCalendarConnection` model
- ✅ `IcsParserService` - parsování .ics souborů
- ✅ `EmailParserService` - parsování emailů
- ✅ `EmailCalendarSyncService` - hlavní sync logika
- ✅ `EmailCalendarController` - web UI
- ✅ Policy pro autorizaci

### 3. **Frontend**

- ✅ Index stránka (seznam email kalendářů)
- ✅ Create stránka (vytvoření nového)
- ✅ Show stránka (detail + instrukce)
- ✅ Test stránka (lokální testování)
- ✅ Translations (EN/CS/SK/PL)

### 4. **Testing**

- ✅ Artisan command pro CLI testování
- ✅ Web UI pro testování
- ✅ Sample email templates

### 5. **Documentation**

- ✅ Kompletní setup guide (`EMAIL_CALENDAR_SETUP.md`)
- ✅ Production deployment options
- ✅ Troubleshooting guide

---

## 🧪 Jak to otestovat (HNED TEĎ!)

### Krok 1: Vytvoř Email Calendar

```bash
# Otevři v prohlížeči:
http://localhost:8080/email-calendars/create

# Vyplň:
# - Name: "Test Calendar"
# - Description: "Testing email sync"
# - Sender Whitelist: (nech prázdné)

# Klikni "Create Email Calendar"
```

### Krok 2: Zkopíruj vygenerovaný email

```
Dostaneš např.: a7b2c9f4@syncmyday.local
```

### Krok 3A: Test přes Web UI

```bash
# Jdi na:
http://localhost:8080/email-calendars/{id}/test

# Klikni na "Copy to Form" (vzorový email se zkopíruje)
# Klikni "Process Test Email"

# Měl bys vidět:
✅ "Email processed successfully! 1 event(s) synced."
```

### Krok 3B: Test přes Artisan Command

```bash
# Vytvoř testovací email soubor
cat > test-email.txt << 'EOF'
From: boss@company.com
To: a7b2c9f4@syncmyday.local
Subject: Important Meeting
Content-Type: multipart/mixed; boundary="boundary123"

--boundary123
Content-Type: text/plain

Meeting invitation.

--boundary123
Content-Type: text/calendar; name="invite.ics"

BEGIN:VCALENDAR
VERSION:2.0
METHOD:REQUEST
BEGIN:VEVENT
UID:meeting-12345@company.com
DTSTAMP:20251008T120000Z
DTSTART:20251010T140000Z
DTEND:20251010T150000Z
SUMMARY:Important Team Meeting
DESCRIPTION:Quarterly review
STATUS:CONFIRMED
SEQUENCE:0
END:VEVENT
END:VCALENDAR

--boundary123--
EOF

# Spusť processing
php artisan email:process-test a7b2c9f4 --file=test-email.txt
```

### Krok 4: Zkontroluj výsledek

```bash
# Podívej se do logů
tail -50 storage/logs/laravel.log

# Nebo do databáze
php artisan tinker
>>> \App\Models\SyncEventMapping::latest()->first()
>>> \App\Models\SyncLog::latest()->take(5)->get()
```

---

## 📊 Co se stane při zpracování

```
1. Email přijat → Parsován
2. .ics příloha → Extrahována
3. VEVENT → Parsován (UID, DTSTART, DTEND, SUMMARY)
4. Pro každé aktivní sync pravidlo:
   a. Najdi target kalendáře
   b. Vytvoř/update blocker
   c. Zaznamenej mapping (source UID → target blocker ID)
5. Log → Uložen
6. Stats → Updatovány
7. ŽÁDNÁ odpověď organizátorovi!
```

---

## 🎯 Integrace se sync pravidly

Email kalendář funguje **automaticky** s existujícími sync pravidly!

```
Pokud máš sync pravidlo:
- Source: Email Calendar (test@syncmyday.local)
- Target: Google Calendar (personal)
- Blocker title: "Busy"

→ Při příchodu emailu se automaticky vytvoří "Busy" blocker v Google Calendar
```

**Poznámka:** Aktuálně email calendars fungují pro VŠECHNA aktivní pravidla uživatele. Pro produkci bys mohl přidat:

- Vztah `sync_rule.source_email_calendar_id`
- Nebo "fallback" logiku pro pravidla bez zdroje

---

## 🔒 Bezpečnost

### Co je zajištěno:

✅ **No Auto-Reply** - Organizátor se NIKDY nedozví  
✅ **Sender Whitelist** - Volitelné filtrování odesílatelů  
✅ **Unique UID Mapping** - Prevence duplikátů  
✅ **SEQUENCE tracking** - Správné update detection  
✅ **METHOD:CANCEL** - Automatické mazání při zrušení

### Co NENÍ v kódu:

❌ NEPOSÍLÁME iMIP REPLY  
❌ NEPŘIDÁVÁME se jako ATTENDEE  
❌ NEMĚNÍME původní událost

---

## 📝 Praktické use case

### Use Case 1: Firemní Exchange bez API

```
Problém:
- Pracuješ ve firmě s locked-down Exchange
- Nemáš API přístup
- Chceš blokery v osobním Google Calendar

Řešení:
1. Vytvoř email calendar v SyncMyDay
2. V Outlooku nastav forwarding pravidlo:
   "Pokud email obsahuje 'invitation' → forward na abc123@syncmyday.local"
3. Vytvoř sync pravidlo: Email Calendar → Google Calendar
4. PROFIT! Automatické blokery bez toho, aby o tom někdo věděl
```

### Use Case 2: Legacy kalendářový systém

```
Problém:
- Firemní kalendář z roku 2005
- Žádná API, žádné webové rozhraní
- Ale posílá email notifikace

Řešení:
- Forward notifikace na email calendar
- Auto-sync do moderních kalendářů
```

---

## 🚀 Production Deployment

Pro produkci budeš potřebovat:

### 1. **Email Server Setup**

Viz `EMAIL_CALENDAR_SETUP.md` - sekce "Production Setup"

**Možnosti:**

- Postfix + Pipe to Laravel
- Mailgun Webhooks
- AWS SES + Lambda
- SendGrid Inbound Parse

### 2. **DNS Configuration**

```bash
# MX Records
syncmyday.com.  MX  10  mail.syncmyday.com.

# SPF (pro odesílání)
syncmyday.com.  TXT  "v=spf1 include:_spf.google.com ~all"
```

### 3. **Update .env**

```bash
EMAIL_DOMAIN=syncmyday.com  # místo syncmyday.local
```

---

## 📚 Dokumentace

- **Setup Guide:** `EMAIL_CALENDAR_SETUP.md`
- **This Quickstart:** `EMAIL_SYNC_QUICKSTART.md`
- **API Docs:** Viz "API Reference" v setup guide

---

## 🐛 Troubleshooting

### Email se nezpracoval

```bash
# Zkontroluj status
SELECT * FROM email_calendar_connections WHERE email_token='abc123';

# Podívej se do logů
tail -100 storage/logs/laravel.log | grep -i email

# Test manually
php artisan email:process-test abc123 --file=test.eml -v
```

### Blocker se nevytvořil

```bash
# Zkontroluj sync pravidla
SELECT * FROM sync_rules WHERE user_id=1 AND is_active=1;

# Zkontroluj target connections
SELECT * FROM calendar_connections WHERE user_id=1 AND status='active';

# Podívej se do sync logů
SELECT * FROM sync_logs ORDER BY created_at DESC LIMIT 10;
```

---

## ✨ Features

### Aktuálně funguje:

✅ Vytvoření email kalendáře  
✅ Parsing emailů s .ics přílohami  
✅ Extraction událostí z .ics  
✅ Vytváření blockerů v target kalendářích  
✅ Update detection (SEQUENCE)  
✅ Cancellation handling (METHOD:CANCEL)  
✅ Sender whitelist  
✅ Lokální testování (web UI + CLI)  
✅ Stats tracking  
✅ Error handling

### Pro budoucnost (volitelné):

🔮 Real-time email processing (webhooks)  
🔮 Email templates pro různé kalendářové systémy  
🔮 Auto-detection calendar type z emailu  
🔮 Batch processing pro hromadné importy  
🔮 Email-to-calendar mapping UI

---

## 🎉 Shrnutí

**Email-Based Calendar Sync je HOTOVÝ a FUNKČNÍ!**

Můžeš:

1. ✅ Vytvářet email kalendáře přes web UI
2. ✅ Testovat lokálně (bez reálného email serveru)
3. ✅ Automaticky sync-ovat události do target kalendářů
4. ✅ Organizátoři se o ničem nedozví

Pro produkci stačí:

- Nastavit příjem emailů (Postfix/Mailgun/SES)
- Změnit `EMAIL_DOMAIN` v .env
- Hotovo!

---

**Happy Syncing!** 🚀📧📅
