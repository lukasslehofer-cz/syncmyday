<?php

return [
    // Authentication
    'registration_success' => 'Registrácia bola úspešná! Vitajte v SyncMyDay.',
    'login_failed' => 'Neplatné prihlasovacie údaje. Skúste to prosím znova.',
    
    // OAuth
    'oauth_state_mismatch' => 'Bezpečnostné overenie zlyhalo. Skúste to prosím znova.',
    'oauth_failed' => 'Pripojenie kalendára sa nepodarilo. Skúste to prosím znova.',
    'oauth_cancelled' => 'Pripojenie kalendára bolo zrušené. Neboli vykonané žiadne zmeny.',
    'calendar_connected' => 'Kalendár bol úspešne pripojený!',
    
    // Connections
    'connection_deleted' => 'Pripojenie kalendára bolo odstránené.',
    'connection_refreshed' => 'Pripojenie kalendára bolo obnovené.',
    'connection_refresh_failed' => 'Obnovenie pripojenia sa nepodarilo.',
    'need_two_calendars' => 'Na vytvorenie synchronizačných pravidiel potrebujete aspoň 2 pripojené kalendáre.',
    
    // Emailové kalendáre
    'email_calendar_created' => 'Emailový kalendár bol úspešne vytvorený! Preposielajte pozvánky na svoju jedinečnú emailovú adresu.',
    'email_calendar_creation_failed' => 'Vytvorenie emailového kalendára sa nepodarilo. Skúste to prosím znova.',
    'email_calendar_deleted' => 'Emailové pripojenie kalendára bolo odstránené.',
    'connection_deleted_failed' => 'Zmazanie pripojenia sa nepodarilo.',
    'email_processed_successfully' => 'Email bol úspešne spracovaný! Synchronizovaných %d udalostí.',
    'email_processing_failed' => 'Spracovanie emailu sa nepodarilo.',
    
    // Sync Rules
    'sync_rule_created' => 'Synchronizačné pravidlo bolo úspešne vytvorené!',
    'sync_rule_updated' => 'Synchronizačné pravidlo bolo aktualizované.',
    'sync_rule_deleted' => 'Synchronizačné pravidlo bolo zmazané.',
    'sync_rule_limit_reached' => 'Dosiahli ste limit pre váš tarif. Prejdite na Pro pre neobmedzené pravidlá.',
    'sync_rule_creation_failed' => 'Vytvorenie pravidla sa nepodarilo. Skúste to prosím znova.',
    
    // Billing
    'subscription_required' => 'Táto funkcia vyžaduje predplatné Pro.',
    'subscription_activated' => 'Predplatné Pro aktivované! Užite si neobmedzené pravidlá.',
    'billing_error' => 'Došlo k chybe. Skúste to prosím znova.',
    'payment_verification_failed' => 'Overenie platby zlyhalo. Kontaktujte prosím podporu.',
    'no_subscription' => 'Nebolo nájdené aktívne predplatné.',
    
    // Onboarding
    'onboarding_complete' => 'Nastavenie dokončené! Vaše kalendáre sa teraz synchronizujú.',
    
    // General
    'success' => 'Úspech!',
    'error' => 'Chyba',
    'warning' => 'Varovanie',
    
    // Chyby pripojenia kalendára (užívateľsky prívetivé)
    'calendar_connection_unauthorized' => 'Nie je možné pripojiť kalendár :provider. Váš účet možno nemá potrebné oprávnenia alebo aktivované služby. Skontrolujte prosím nastavenia účtu :provider a skúste to znova.',
    'calendar_unauthorized_hint' => 'Uistite sa, že máte vo svojom účte povolený prístup ku kalendáru a všetky potrebné služby sú aktívne.',
    'calendar_connection_forbidden' => 'Prístup ku kalendáru :provider bol zamietnutý. :hint',
    'calendar_forbidden_hint' => 'Overte prosím, že váš účet má oprávnenia ku kalendáru a skúste sa znova pripojiť.',
    'calendar_connection_not_found' => 'Služba kalendára :provider je dočasne nedostupná. Skúste to prosím o chvíľu.',
    'calendar_connection_rate_limit' => 'Príliš veľa pokusov o pripojenie. Počkajte prosím chvíľu a skúste to znova.',
    'calendar_connection_server_error' => ':provider práve rieši technické problémy. Skúste to prosím neskôr.',
    'calendar_invalid_credentials' => 'Pripojenie k :provider sa nepodarilo nadviazať. Skontrolujte prosím svoj účet a skúste to znova.',
    'calendar_invalid_grant' => 'Autorizácia vypršala alebo bola odvolaná. Skúste sa prosím pripojiť znova.',
    'calendar_access_denied' => 'Neudelili ste potrebné oprávnenia. Skúste to prosím znova a povoľte prístup ku svojmu kalendáru.',
    'calendar_connection_network_error' => 'Nie je možné sa pripojiť k :provider. Skontrolujte prosím pripojenie k internetu a skúste to znova.',
    'calendar_connection_generic_error' => 'Nie je možné pripojiť kalendár :provider. Skúste to prosím znova. Ak problém pretrváva, kontaktujte podporu.',
];

