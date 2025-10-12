# 🤖 Prompt pro AI při tvorbě blogových článků

## Základní prompt

```
Napiš blog článek o [TÉMA] pro aplikaci SyncMyDay (nástroj pro synchronizaci kalendářů).

POŽADAVKY:
- Délka: 800-1500 slov
- Jazyk: čeština, profesionální ale přístupný styl
- Struktura: úvodní odstavec + 3-5 hlavních sekcí s h2 nadpisy
- Formát: HTML (viz přiložený seznam tagů)
- SEO: Klíčové slovo "[KLÍČOVÉ SLOVO]" v prvním odstavci a v h2 nadpisech
- Vložit 1 inline CTA blok po polovině článku (varianta centrovaná)

STRUKTURA ČLÁNKU:
1. Úvodní odstavec (p class="text-lg font-semibold") - 2-3 věty, obsahuje klíčové slovo
2. Hlavní sekce s h2 nadpisy (3-5 sekcí)
3. Každá sekce: 2-4 odstavce + seznam nebo tabulka nebo blok upozornění
4. Inline CTA uprostřed článku
5. Závěrečný odstavec (bez velkého CTA - ten se přidá automaticky)

POUŽIJ TYTO HTML TAGY:
[VLOŽ OBSAH Z BLOG_HTML_TAGS.html]

META INFORMACE:
- Meta title (50-60 znaků): [vytvoř]
- Meta description (150-160 znaků): [vytvoř]
- Excerpt (1-2 věty): [vytvoř]
```

---

## Příklad konkrétního promptu

```
Napiš blog článek o "Jak synchronizovat Google Calendar s Apple iCloud" pro aplikaci SyncMyDay.

POŽADAVKY:
- Délka: 1200 slov
- Jazyk: čeština, profesionální ale přístupný styl
- Klíčové slovo: "synchronizace kalendářů Google Apple"
- Zaměření: Praktický návod pro uživatele, kteří používají oba kalendáře

STRUKTURA:
1. Úvodní odstavec - proč je synchronizace užitečná
2. "Co je synchronizace kalendářů a jak funguje" (h2)
3. "Proč synchronizovat Google a Apple kalendář" (h2) - použij seznam výhod
4. "Jak na to s SyncMyDay - krok za krokem" (h2) - použij kroky s čísly
   [zde inline CTA centrovaný]
5. "Tipy pro efektivní synchronizaci" (h2) - použij info bloky
6. "Časté problémy a řešení" (h2) - použij tabulku
7. Závěr

POUŽIJ:
- Minimálně 1 tabulku (srovnání řešení)
- Minimálně 2 seznamy (výhody, tipy)
- 2-3 bloky upozornění (tipy, varování)
- Kroky s čísly pro návod
- 1 inline CTA (centrovaný) po 4. sekci

HTML TAGY: [VLOŽ Z BLOG_HTML_TAGS.html]

META:
Vytvoř meta title, meta description a excerpt optimalizované pro SEO.
```

---

## Prompt pro různé typy článků

### 📰 Novinky / Oznámení

```
Typ článku: Novinky
Téma: [TÉMA]
Tón: Nadšený, informativní
Délka: 600-800 slov
Struktura:
- Co je nového
- Jak to funguje
- Výhody pro uživatele
- Jak začít používat
Speciální: Použij zelené "Dobrá praxe" bloky pro zvýraznění výhod
```

### 📚 Návody / Tutoriály

```
Typ článku: Návod
Téma: [TÉMA]
Tón: Přátelský, instruktážní
Délka: 1000-1500 slov
Struktura:
- Úvod - co se naučíte
- Požadavky / Co budete potřebovat
- Krok za krokem (číslované kroky)
- Tipy pro pokročilé
- Závěr + další kroky
Speciální: Použij kroky s čísly, screenshoty (placeholder), info bloky
```

### 💡 Tipy a triky

```
Typ článku: Tips & Tricks
Téma: [TÉMA]
Tón: Užitečný, praktický
Délka: 800-1200 slov
Struktura:
- Úvod
- 5-10 tipů (h3 pro každý tip)
- Bonus tipy (seznam)
- Závěr
Speciální: Každý tip = h3 + 2-3 odstavce, použij modré info bloky
```

### 🆚 Srovnání / Porovnání

```
Typ článku: Srovnání
Téma: [TÉMA] vs [TÉMA]
Tón: Objektivní, analytický
Délka: 1200-1800 slov
Struktura:
- Úvod - co budeme srovnávat
- Stručný přehled obou řešení
- Detailní srovnání (tabulka)
- Výhody a nevýhody (dvousloupcový layout)
- Pro koho je co vhodné
- Závěr + doporučení
Speciální: Velká srovnávací tabulka, dvousloupcový layout pro výhody/nevýhody
```

---

## Checklist před odesláním článku AI

- [ ] Klíčové slovo je v prvním odstavci
- [ ] Minimálně 3 h2 nadpisy obsahují související termíny
- [ ] Použit min. 1 seznam (ul/ol)
- [ ] Použit min. 1 blok upozornění (tip/varování)
- [ ] Inline CTA je uprostřed článku
- [ ] Všechny obrázky mají alt text
- [ ] Meta title má 50-60 znaků
- [ ] Meta description má 150-160 znaků
- [ ] Excerpt je 1-2 věty
- [ ] Článek má 800+ slov
- [ ] Závěr shrnuje hlavní body (bez velkého CTA)

---

## Kde použít jednotlivé HTML elementy

| Element                  | Kdy použít                     |
| ------------------------ | ------------------------------ |
| **h2**                   | Hlavní sekce článku            |
| **h3**                   | Podsekce v rámci h2            |
| **ul/ol**                | Výčet položek, kroky           |
| **Tabulka**              | Srovnání, parametry, přehled   |
| **Info blok (modrý)**    | Užitečné tipy                  |
| **Varování (žlutý)**     | Důležitá upozornění            |
| **Dobrá praxe (zelený)** | Doporučení, best practices     |
| **Chyba (červený)**      | Časté chyby, co nedělat        |
| **Kroky s čísly**        | Postupy, návody krok za krokem |
| **Dvousloupcový layout** | Výhody vs nevýhody, srovnání   |
| **Inline CTA**           | Po 30-50% článku, kde má smysl |
| **Figure + figcaption**  | Důležité screenshoty, diagramy |

---

## Rychlý šablonovací prompt

```
Vytvoř HTML obsah blogu podle této šablony:

TÉMA: [téma článku]
KLÍČOVÉ SLOVO: [hlavní klíčové slovo]
TYP: [Novinky / Návod / Tipy / Srovnání]
DÉLKA: [počet slov]

POUŽIJ TYTO HTML TAGY:
[vložit BLOG_HTML_TAGS.html]

VRAŤ:
1. HTML obsah článku (pro pole "Content")
2. Meta title (50-60 znaků)
3. Meta description (150-160 znaků)
4. Excerpt (1-2 věty)
```

Pak stačí výstup zkopírovat přímo do admin panelu! 🎉
