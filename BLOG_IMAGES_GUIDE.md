# 🖼️ Návod na obrázky v blogu

## Kde nahrát obrázky

Všechny obrázky pro blog nahrajte do adresáře:

```
/public/images/blog/
```

Tento adresář je veřejně přístupný přes web.

---

## Typy obrázků

### 1. Hlavní obrázek článku (Featured Image)

**Kam nahrát:** `/public/images/blog/`

**Jak nastavit:**

1. Otevřete admin panel → `/admin/blog`
2. Vytvořte nebo upravte článek
3. Do pole **"Featured Image URL"** zadejte cestu pomocí jedné z těchto variant:

**Varianta 1: Plná cesta (s lomítkem)**

```
/images/blog/nazev-obrazku.jpg
```

**Varianta 2: Jen název souboru (bez lomítka)**

```
nazev-obrazku.jpg
```

**Příklad:**

```
/images/blog/calendar-sync-guide.jpg
```

nebo

```
calendar-sync-guide.jpg
```

**Doporučené rozměry:** 1200×630 px (ideální pro sdílení na sociálních sítích)

---

### 2. Obrázky v obsahu článku

**Kam nahrát:** `/public/images/blog/`

**Jak vložit do článku:**

V admin panelu do pole "Obsah (HTML)" vložte:

```html
<img
  src="/images/blog/nazev-obrazku.jpg"
  alt="Popis obrázku"
  class="rounded-lg shadow-lg my-6"
/>
```

**Doporučené třídy pro styling:**

- `rounded-lg` - zaoblené rohy
- `shadow-lg` - stín
- `my-6` - vertikální mezery
- `max-w-full` - responzivní šířka
- `mx-auto` - centrování

**Příklad s popiskem:**

```html
<figure class="my-8">
  <img
    src="/images/blog/screenshot-dashboard.jpg"
    alt="Dashboard SyncMyDay"
    class="rounded-lg shadow-lg w-full"
  />
  <figcaption class="text-center text-sm text-gray-600 mt-2">
    Přehledný dashboard aplikace SyncMyDay
  </figcaption>
</figure>
```

---

## Jak nahrát obrázek na server

### Varianta 1: FTP/SFTP

1. Připojte se k serveru přes FTP/SFTP klienta (např. FileZilla)
2. Přejděte do složky `/public_html/images/blog/` (nebo `/public/images/blog/`)
3. Nahrajte soubor

### Varianta 2: SSH (přes terminál)

```bash
# Z vašeho počítače nahrajte soubor
scp obrazek.jpg uzivatel@server:/cesta/k/public/images/blog/

# Nebo přímo na serveru vytvořte soubor
ssh uzivatel@server
cd /cesta/k/public/images/blog/
# nahrajte soubor pomocí rz, wget, curl apod.
```

### Varianta 3: cPanel File Manager

1. Přihlaste se do cPanelu
2. Otevřete File Manager
3. Přejděte do `public_html/images/blog/`
4. Klikněte na "Upload" a nahrajte soubor

---

## Doporučení pro obrázky

### Formáty

- ✅ **JPG/JPEG** - pro fotografie (nejmenší velikost)
- ✅ **PNG** - pro screenshoty, loga, grafy
- ✅ **WebP** - moderní formát (menší než JPG, ale novější prohlížeče)
- ❌ **BMP, TIFF** - příliš velké

### Optimalizace

Před nahráním obrázky zkomprimujte pomocí:

- [TinyPNG](https://tinypng.com/) - online komprese PNG a JPG
- [Squoosh](https://squoosh.app/) - Google nástroj pro kompresi
- Photoshop → Export → "Save for Web"

**Cílová velikost:** < 200 KB na obrázek

### Pojmenování souborů

- ✅ Používejte anglické znaky a pomlčky: `calendar-sync-guide.jpg`
- ✅ Malá písmena: `dashboard-screenshot.png`
- ❌ Mezery: `muj obrazek.jpg` ❌
- ❌ Diakritika: `průvodce-kalendářem.jpg` ❌
- ❌ Velká písmena: `MujObrazek.JPG` ❌

### SEO optimalizace

Vždy vyplňte `alt` atribut:

```html
<!-- ❌ Špatně -->
<img src="/images/blog/obrazek.jpg" />

<!-- ✅ Správně -->
<img
  src="/images/blog/obrazek.jpg"
  alt="Synchronizace Google kalendáře s Apple iCloud"
/>
```

---

## Příklad kompletního článku s obrázky

```html
<p>V tomto návodu si ukážeme, jak nastavit synchronizaci kalendářů.</p>

<figure class="my-8">
  <img
    src="/images/blog/dashboard-overview.jpg"
    alt="Přehled dashboard SyncMyDay"
    class="rounded-lg shadow-lg w-full"
  />
  <figcaption class="text-center text-sm text-gray-600 mt-2">
    Dashboard aplikace SyncMyDay
  </figcaption>
</figure>

<p>První krok je připojit váš kalendář...</p>

<img
  src="/images/blog/connect-calendar-step1.jpg"
  alt="Krok 1: Připojení kalendáře"
  class="rounded-lg shadow-md my-6 max-w-2xl mx-auto"
/>

<p>Poté pokračujte dalším krokem...</p>
```

---

## Řešení problémů

### Obrázek se nezobrazuje

1. Zkontrolujte cestu - musí začínat `/images/blog/`
2. Ověřte, že soubor existuje na serveru
3. Zkontrolujte práva k souboru (mělo by být 644)

### Obrázek je příliš velký

1. Zmenšete rozměry obrázku (např. na max. 1920px šířku)
2. Zkomprimujte obrázek pomocí TinyPNG
3. Zvažte převod z PNG na JPG (pokud není třeba průhlednost)

### Obrázek nefunguje po nahrání

```bash
# Nastavte správná práva k souboru
chmod 644 /cesta/k/public/images/blog/obrazek.jpg
```

---

## 📁 Struktura adresáře

```
public/
└── images/
    └── blog/
        ├── featured-homepage-hero.jpg        (hlavní obrázek)
        ├── calendar-sync-step1.png           (obrázek v obsahu)
        ├── calendar-sync-step2.png
        ├── dashboard-screenshot.jpg
        └── ...
```

---

## Tip: Používejte konzistentní názvy

Pro lepší organizaci používejte předpony:

```
featured-calendar-sync.jpg          (hlavní obrázek článku)
step1-connect-google.png            (kroky v návodu)
step2-choose-calendars.png
screenshot-dashboard.jpg            (screenshoty)
icon-calendar.png                   (ikony)
```

---

Hotovo! Nyní můžete nahrávat a používat obrázky ve vašich blogových článcích. 🎉
