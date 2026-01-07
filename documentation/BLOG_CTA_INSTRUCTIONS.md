# 📣 Návod na použití CTA bloků v blogu

## Automatický CTA blok na konci článků

✅ **Tento blok se přidává automaticky** na konec každého blogového článku!

Vykresluje se v šabloně `resources/views/blog/show.blade.php` a obsahuje:

- Výrazný gradient pozadí (indigo → purple → pink)
- Dvě CTA tlačítka: "Vyzkoušet zdarma" a "Zobrazit funkce"
- Vícejazyčnou podporu (cs, de, en, pl, sk)
- Responzivní design
- Ikona kalendáře ve čtverci se zaoblenými rohy

**Není třeba nic dělat** - zobrazuje se automaticky! 🎉

---

## Inline CTA blok (pro vložení do článku)

Menší CTA blok, který můžete vložit přímo do obsahu článku tam, kde chcete.

### Jak použít:

1. **Otevřete admin panel** → `/admin/blog`
2. **Editujte článek** nebo vytvořte nový
3. **Do pole "Obsah (HTML)"** vložte jeden z těchto HTML kódů:

### Varianta 1: Float (obtéká text zprava)

```html
<div
  class="my-8 p-6 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 border-2 border-indigo-200 rounded-xl shadow-lg float-right ml-6 mb-6"
  style="width: 100%; max-width: 350px;"
>
  <div class="text-center">
    <div
      class="inline-flex items-center justify-center w-12 h-12 rounded-lg mb-4 shadow-md"
      style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
    >
      <svg
        class="w-6 h-6 text-white"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2">Vyzkoušejte SyncMyDay</h3>
    <p class="text-sm text-gray-600 mb-4">
      Zdarma na 14 dní. Bez platební karty.
    </p>
    <a
      href="/register"
      class="block w-full px-6 py-3 font-semibold rounded-lg hover:opacity-90 shadow-md transform hover:scale-105 transition mb-2"
      style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none !important;"
    >
      Začít zdarma
    </a>
    <a
      href="/#features"
      class="block w-full px-6 py-2 text-sm font-semibold hover:text-indigo-700 transition"
      style="color: #6366f1 !important; text-decoration: none !important;"
    >
      Zjistit více →
    </a>
  </div>
</div>
```

**Použití:** Ideální pro delší články - text obtéká CTA zprava

---

### Varianta 2: Centrovaný blok (přerušuje text)

```html
<div
  class="my-8 p-6 bg-gradient-to-br from-indigo-100 via-purple-100 to-pink-100 border-2 border-indigo-200 rounded-xl shadow-lg max-w-md mx-auto"
>
  <div class="text-center">
    <div
      class="inline-flex items-center justify-center w-12 h-12 rounded-lg mb-4 shadow-md"
      style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"
    >
      <svg
        class="w-6 h-6 text-white"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
        />
      </svg>
    </div>
    <h3 class="text-xl font-bold text-gray-900 mb-2">Vyzkoušejte SyncMyDay</h3>
    <p class="text-sm text-gray-600 mb-4">
      Zdarma na 14 dní. Bez platební karty.
    </p>
    <a
      href="/register"
      class="inline-block px-6 py-3 font-semibold rounded-lg hover:opacity-90 shadow-md transform hover:scale-105 transition mr-2"
      style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white !important; text-decoration: none !important;"
    >
      Začít zdarma
    </a>
    <a
      href="/#features"
      class="inline-block px-6 py-3 text-sm font-semibold hover:text-indigo-700 transition"
      style="color: #6366f1 !important; text-decoration: none !important;"
    >
      Zjistit více →
    </a>
  </div>
</div>
```

**Použití:** Lepší pro kratší články nebo jako samostatný blok mezi sekcemi

---

## 💡 Tipy pro nejlepší výsledky

1. **Umístění:** Ideální pozice je po 2-3 odstavcích nebo po polovině článku
2. **Nepoužívejte příliš často:** Max. 1 inline CTA na článek (+ automatický na konci)
3. **Kontext:** Umístěte CTA tam, kde má smysl v kontextu článku
4. **Testování:** Po přidání si článek prohlédněte na frontendu a zkontrolujte vzhled

---

## 📋 Ukázka v seederu

Podívejte se na článek "Jak nastavit synchronizaci kalendářů" v `database/seeders/BlogSeeder.php` - obsahuje ukázkový inline CTA blok uprostřed obsahu!

---

## 🎨 Přizpůsobení

### Barvy a design

- Ikona kalendáře je ve **čtverci se zaoblenými rohy** (rounded-lg), ne v kruhu
- Gradient pozadí: `#667eea` → `#764ba2` (stejný jako "Start 14 days free" button na homepage)
- Všechny odkazy mají `!important` styly, aby fungovaly i v `.help-content` oblasti

### Text CTA bloků

Pokud chcete změnit text, upravte překlady v:

- `lang/cs/messages.php` (+ de, en, pl, sk)
- Klíče: `blog_cta_title`, `blog_cta_subtitle`, `blog_cta_button_primary`, `blog_cta_button_secondary`, `blog_cta_footnote`

---

## 🔄 Aktualizace seederů

Pokud chcete znovu načíst ukázkové články včetně inline CTA:

```bash
php artisan db:seed --class=BlogSeeder
```

⚠️ **Pozor:** Toto smaže existující články a vytvoří nové!
