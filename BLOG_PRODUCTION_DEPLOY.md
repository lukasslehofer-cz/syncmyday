# 🚀 Nasazení blogu na produkci

## Krok 1: Nahrání souborů

### A) Nahrát nové soubory na server

Pomocí FTP/SFTP nahrajte tyto nové soubory:

```
app/Models/
  - BlogArticle.php
  - BlogArticleTranslation.php
  - BlogCategory.php
  - BlogCategoryTranslation.php

app/Http/Controllers/
  - BlogController.php

app/Http/Controllers/Admin/
  - BlogAdminController.php

resources/views/blog/
  - index.blade.php
  - category.blade.php
  - show.blade.php
  - partials/
    - sidebar.blade.php

resources/views/admin/blog/
  - index.blade.php
  - create.blade.php
  - edit.blade.php
```

### B) Vytvořit adresář pro obrázky

Vytvořte na serveru složku:

```
public/images/blog/
```

Nastavte práva 755:

```bash
chmod 755 public/images/blog/
```

### C) Aktualizovat existující soubory

Nahraďte tyto soubory aktualizovanými verzemi:

```
routes/web.php
lang/cs/messages.php
lang/de/messages.php
lang/en/messages.php
lang/pl/messages.php
lang/sk/messages.php
resources/views/layouts/public.blade.php
resources/views/layouts/app.blade.php
resources/views/layouts/legal.blade.php
resources/views/welcome.blade.php
```

---

## Krok 2: Aktualizace databáze

### Varianta A: Pomocí phpMyAdmin

1. Přihlaste se do phpMyAdmin
2. Vyberte vaši databázi
3. Klikněte na záložku "SQL"
4. Zkopírujte a vložte obsah souboru **`BLOG_PRODUCTION_MIGRATION.sql`**
5. Klikněte "Provést"

### Varianta B: Pomocí SSH (pokud máte přístup)

```bash
# Připojte se k serveru
ssh uzivatel@vase-domena.cz

# Přejděte do složky projektu
cd /cesta/k/projektu

# Spusťte SQL soubor
mysql -u DB_USERNAME -p DB_NAME < BLOG_PRODUCTION_MIGRATION.sql

# Zadejte heslo k databázi
```

### Varianta C: Pomocí Laravel migrací (pokud máte SSH)

```bash
# Připojte se k serveru
ssh uzivatel@vase-domena.cz

# Přejděte do složky projektu
cd /cesta/k/projektu

# Spusťte migrace
php artisan migrate

# Vložte základní data (kategorie)
# Pozor: Toto vytvoří ukázkové články!
php artisan db:seed --class=BlogSeeder
```

---

## Krok 3: Vyčištění cache

Po nahrání všech souborů vyčistěte cache:

### Pomocí SSH:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Pomocí webového rozhraní:

Pokud nemáte SSH, vytvořte soubor `public/clear-cache.php`:

```php
<?php
// Spusťte tento soubor v prohlížeči: https://vase-domena.cz/clear-cache.php
// SMAŽTE HO PO POUŽITÍ!

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

echo "Clearing config...\n";
$kernel->call('config:clear');

echo "Clearing cache...\n";
$kernel->call('cache:clear');

echo "Clearing routes...\n";
$kernel->call('route:clear');

echo "Clearing views...\n";
$kernel->call('view:clear');

echo "\n✅ Cache cleared successfully!\n";
echo "⚠️ NEZAPOMEŇTE SMAZAT TENTO SOUBOR!\n";
```

Spusťte v prohlížeči: `https://vase-domena.cz/clear-cache.php`

**⚠️ DŮLEŽITÉ: Ihned poté tento soubor smažte!**

---

## Krok 4: Ověření funkčnosti

### Zkontrolujte, že funguje:

1. **Frontend blog:**

   - https://vase-domena.cz/blog
   - Měl by se zobrazit seznam kategorií (zatím prázdný)

2. **Admin panel:**

   - https://vase-domena.cz/admin/blog
   - Měl by se zobrazit admin panel pro správu článků

3. **Odkazy v patičce:**
   - Zkontrolujte, že je odkaz na "Blog" v patičce na všech stránkách

---

## Krok 5: Vytvoření prvního článku

1. Přihlaste se do admin panelu
2. Jděte na `/admin/blog`
3. Klikněte na "Vytvořit nový článek"
4. Vyplňte všechny jazykové verze
5. Nahrajte featured image do `/public/images/blog/`
6. Zadejte cestu k obrázku (např. `/images/blog/obrazek.jpg` nebo jen `obrazek.jpg`)
7. Označte "Publikováno" a nastavte datum
8. Uložte

---

## Možné problémy a řešení

### 1. Chyba "Table doesn't exist"

**Řešení:** SQL migrace neproběhla správně. Znovu spusťte SQL soubor v phpMyAdmin.

### 2. Zobrazuje se stará verze

**Řešení:** Vyčistěte cache (Krok 3)

### 3. Obrázky se nezobrazují

**Řešení:**

- Zkontrolujte cestu k obrázku v článku
- Ověřte, že složka `public/images/blog/` existuje
- Zkontrolujte práva k souborům (644) a složkám (755)

### 4. Chyba 404 na /blog

**Řešení:**

- Vyčistěte route cache: `php artisan route:clear`
- Zkontrolujte, že je `routes/web.php` správně nahrán

### 5. Chybí překlady

**Řešení:**

- Zkontrolujte, že jsou nahrány všechny soubory v `lang/*/messages.php`
- Vyčistěte cache: `php artisan config:clear`

---

## Kontrolní checklist

Po nasazení zkontrolujte:

- [ ] Databázové tabulky existují (4 nové tabulky)
- [ ] Složka `/public/images/blog/` existuje
- [ ] Blog je dostupný na `/blog`
- [ ] Admin panel je dostupný na `/admin/blog`
- [ ] Odkazy v patičce fungují
- [ ] Můžete vytvořit nový článek v admin panelu
- [ ] Články se zobrazují na frontendu
- [ ] Obrázky se zobrazují
- [ ] CTA bloky se zobrazují správně
- [ ] Vícejazyčnost funguje (cs, de, en, pl, sk)

---

## 📚 Další dokumentace

- **Obrázky:** `BLOG_IMAGES_GUIDE.md`
- **CTA bloky:** `BLOG_CTA_INSTRUCTIONS.md`
- **Inline CTA snippet:** `INLINE_CTA_SNIPPET.html`

---

## 🎉 Hotovo!

Blog je nyní nasazen a funkční. Můžete začít psát články!

Pro SEO optimalizaci nezapomeňte:

- ✅ Vyplnit meta title a description u každého článku
- ✅ Použít kvalitní featured image (1200×630 px)
- ✅ Vyplnit alt atributy u obrázků
- ✅ Používat kvalitní, unikátní obsah
