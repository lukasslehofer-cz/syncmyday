<?php

return [
    // Authentication
    'registration_success' => 'Registrace byla úspěšná! Vítejte v SyncMyDay.',
    'login_failed' => 'Neplatné přihlašovací údaje. Zkuste to prosím znovu.',
    
    // OAuth
    'oauth_state_mismatch' => 'Bezpečnostní ověření selhalo. Zkuste to prosím znovu.',
    'oauth_failed' => 'Připojení kalendáře se nezdařilo. Zkuste to prosím znovu.',
    'oauth_cancelled' => 'Připojení kalendáře bylo zrušeno. Nebyly provedeny žádné změny.',
    'calendar_connected' => 'Kalendář byl úspěšně připojen!',
    
    // Connections
    'connection_deleted' => 'Připojení kalendáře bylo odstraněno.',
    'connection_refreshed' => 'Připojení kalendáře bylo obnoveno.',
    'connection_refresh_failed' => 'Obnovení připojení se nezdařilo.',
    'need_two_calendars' => 'Pro vytvoření synchronizačních pravidel potřebujete alespoň 2 připojené kalendáře.',
    
    // Emailové kalendáře
    'email_calendar_created' => 'Emailový kalendář byl úspěšně vytvořen! Přeposílejte pozvánky na svou jedinečnou emailovou adresu.',
    'email_calendar_creation_failed' => 'Vytvoření emailového kalendáře se nezdařilo. Zkuste to prosím znovu.',
    'email_calendar_deleted' => 'Emailové připojení kalendáře bylo odstraněno.',
    'connection_deleted_failed' => 'Smazání připojení se nezdařilo.',
    'email_processed_successfully' => 'Email byl úspěšně zpracován! Synchronizováno %d událostí.',
    'email_processing_failed' => 'Zpracování emailu se nezdařilo.',
    
    // Sync Rules
    'sync_rule_created' => 'Synchronizační pravidlo bylo úspěšně vytvořeno!',
    'sync_rule_updated' => 'Synchronizační pravidlo bylo aktualizováno.',
    'sync_rule_deleted' => 'Synchronizační pravidlo bylo smazáno.',
    'sync_rule_limit_reached' => 'Dosáhli jste limitu pro váš tarif. Přejděte na Pro pro neomezená pravidla.',
    'sync_rule_creation_failed' => 'Vytvoření pravidla se nezdařilo. Zkuste to prosím znovu.',
    
    // Billing
    'subscription_required' => 'Tato funkce vyžaduje předplatné Pro.',
    'subscription_activated' => 'Předplatné Pro aktivováno! Užijte si neomezená pravidla.',
    'billing_error' => 'Došlo k chybě. Zkuste to prosím znovu.',
    'payment_verification_failed' => 'Ověření platby selhalo. Kontaktujte prosím podporu.',
    'no_subscription' => 'Nebylo nalezeno aktivní předplatné.',
    
    // Onboarding
    'onboarding_complete' => 'Nastavení dokončeno! Vaše kalendáře se nyní synchronizují.',
    
    // General
    'success' => 'Úspěch!',
    'error' => 'Chyba',
    'warning' => 'Varování',
    
    // Chyby připojení kalendáře (uživatelsky přívětivé)
    'calendar_connection_unauthorized' => 'Nelze připojit kalendář :provider. Váš účet možná nemá potřebná oprávnění nebo aktivované služby. Zkontrolujte prosím nastavení účtu :provider a zkuste to znovu.',
    'calendar_unauthorized_hint' => 'Ujistěte se, že máte ve svém účtu povolen přístup ke kalendáři a všechny potřebné služby jsou aktivní.',
    'calendar_connection_forbidden' => 'Přístup ke kalendáři :provider byl zamítnut. :hint',
    'calendar_forbidden_hint' => 'Ověřte prosím, že váš účet má oprávnění ke kalendáři a zkuste se znovu připojit.',
    'calendar_connection_not_found' => 'Služba kalendáře :provider je dočasně nedostupná. Zkuste to prosím za chvíli.',
    'calendar_connection_rate_limit' => 'Příliš mnoho pokusů o připojení. Počkejte prosím chvíli a zkuste to znovu.',
    'calendar_connection_server_error' => ':provider právě řeší technické potíže. Zkuste to prosím později.',
    'calendar_invalid_credentials' => 'Připojení k :provider se nepodařilo navázat. Zkontrolujte prosím svůj účet a zkuste to znovu.',
    'calendar_invalid_grant' => 'Autorizace vypršela nebo byla odvolána. Zkuste se prosím připojit znovu.',
    'calendar_access_denied' => 'Neudělili jste potřebná oprávnění. Zkuste to prosím znovu a povolte přístup ke svému kalendáři.',
    'calendar_connection_network_error' => 'Nelze se připojit k :provider. Zkontrolujte prosím připojení k internetu a zkuste to znovu.',
    'calendar_connection_generic_error' => 'Nelze připojit kalendář :provider. Zkuste to prosím znovu. Pokud problém přetrvává, kontaktujte podporu.',
];

