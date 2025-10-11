# OAuth Session Cookie Fix

## Problém

Po OAuth callbacku (Google/Microsoft) se uživatel vrací na login formulář místo dashboardu.

### Příčina

Když Google přesměruje zpět na tvůj callback URL, prohlížeč považuje tento request za "cross-site" a kvůli `SameSite=lax` cookie policy **neposílá session cookie**. Proto:
1. Session state není dostupný
2. State verification selže
3. OAuth callback skončí chybou "state mismatch"
4. Uživatel je přesměrován zpět na login

## Řešení A: SameSite='none' (jednodušší)

Změnit SameSite policy na 'none', což umožní posílání cookies i z cross-site requestů (OAuth callbacků).

**Nevýhoda**: Snížení bezpečnosti proti CSRF útokům.

**Implementace**:

Přidej do `.env`:
```env
SESSION_SAME_SITE=none
SESSION_SECURE_COOKIE=true
```

## Řešení B: Cache místo Session pro OAuth state (bezpečnější) ✅

Používat Laravel Cache místo Session pro ukládání OAuth state. Cache je sdílená napříč všemi requesty a není závislá na cookies.

**Výhody**:
- ✅ Bezpečnější (zachová SameSite=lax)
- ✅ Funguje pro všechny domény
- ✅ Automatické vypršení (TTL)
- ✅ Žádné cross-domain cookie problémy

**Implementace**: Upravit SocialAuthController a OAuthController

---

## Doporučení

**Použij Řešení B (Cache)** - je bezpečnější a elegantní.

