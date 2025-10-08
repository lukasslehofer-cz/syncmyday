<?php

return [
    // Authentication
    'registration_success' => 'Rejestracja powiodła się! Witamy w SyncMyDay.',
    'login_failed' => 'Nieprawidłowe dane logowania. Spróbuj ponownie.',
    
    // OAuth
    'oauth_state_mismatch' => 'Weryfikacja bezpieczeństwa nie powiodła się. Spróbuj ponownie.',
    'oauth_failed' => 'Nie udało się połączyć kalendarza. Spróbuj ponownie.',
    'oauth_cancelled' => 'Połączenie kalendarza zostało anulowane. Nie wprowadzono żadnych zmian.',
    'calendar_connected' => 'Kalendarz został pomyślnie połączony!',
    
    // Connections
    'connection_deleted' => 'Połączenie kalendarza zostało usunięte.',
    'connection_refreshed' => 'Połączenie kalendarza zostało odświeżone.',
    'connection_refresh_failed' => 'Odświeżenie połączenia nie powiodło się.',
    'need_two_calendars' => 'Aby utworzyć reguły synchronizacji, potrzebujesz co najmniej 2 połączone kalendarze.',
    
    // Kalendarze emailowe
    'email_calendar_created' => 'Kalendarz emailowy został utworzony! Przekazuj zaproszenia na swój unikalny adres email.',
    'email_calendar_creation_failed' => 'Nie udało się utworzyć kalendarza emailowego. Spróbuj ponownie.',
    'email_calendar_deleted' => 'Połączenie kalendarza emailowego zostało usunięte.',
    'connection_deleted_failed' => 'Nie udało się usunąć połączenia.',
    'email_processed_successfully' => 'Email przetworzony pomyślnie! Zsynchronizowano %d wydarzeń.',
    'email_processing_failed' => 'Przetwarzanie emaila nie powiodło się.',
    
    // Sync Rules
    'sync_rule_created' => 'Reguła synchronizacji została pomyślnie utworzona!',
    'sync_rule_updated' => 'Reguła synchronizacji została zaktualizowana.',
    'sync_rule_deleted' => 'Reguła synchronizacji została usunięta.',
    'sync_rule_limit_reached' => 'Osiągnąłeś limit dla swojego planu. Przejdź na Pro, aby otrzymać nieograniczone reguły.',
    'sync_rule_creation_failed' => 'Utworzenie reguły nie powiodło się. Spróbuj ponownie.',
    
    // Billing
    'subscription_required' => 'Ta funkcja wymaga subskrypcji Pro.',
    'subscription_activated' => 'Subskrypcja Pro aktywowana! Ciesz się nieograniczonymi regułami.',
    'billing_error' => 'Wystąpił błąd. Spróbuj ponownie.',
    'payment_verification_failed' => 'Weryfikacja płatności nie powiodła się. Skontaktuj się z obsługą.',
    'no_subscription' => 'Nie znaleziono aktywnej subskrypcji.',
    
    // Onboarding
    'onboarding_complete' => 'Konfiguracja zakończona! Twoje kalendarze są teraz synchronizowane.',
    
    // General
    'success' => 'Sukces!',
    'error' => 'Błąd',
    'warning' => 'Ostrzeżenie',
    
    // Błędy połączenia kalendarza (przyjazne dla użytkownika)
    'calendar_connection_unauthorized' => 'Nie można połączyć kalendarza :provider. Twoje konto może nie mieć wymaganych uprawnień lub aktywowanych usług. Sprawdź ustawienia konta :provider i spróbuj ponownie.',
    'calendar_unauthorized_hint' => 'Upewnij się, że Twoje konto ma włączony dostęp do kalendarza i wszystkie wymagane usługi są aktywne.',
    'calendar_connection_forbidden' => 'Dostęp do kalendarza :provider został odmówiony. :hint',
    'calendar_forbidden_hint' => 'Sprawdź, czy Twoje konto ma uprawnienia do kalendarza i spróbuj połączyć się ponownie.',
    'calendar_connection_not_found' => 'Usługa kalendarza :provider jest tymczasowo niedostępna. Spróbuj ponownie za chwilę.',
    'calendar_connection_rate_limit' => 'Zbyt wiele prób połączenia. Poczekaj chwilę i spróbuj ponownie.',
    'calendar_connection_server_error' => ':provider ma obecnie problemy techniczne. Spróbuj ponownie później.',
    'calendar_invalid_credentials' => 'Nie udało się nawiązać połączenia z :provider. Sprawdź swoje konto i spróbuj ponownie.',
    'calendar_invalid_grant' => 'Autoryzacja wygasła lub została cofnięta. Spróbuj połączyć się ponownie.',
    'calendar_access_denied' => 'Nie udzieliłeś wymaganych uprawnień. Spróbuj ponownie i zezwól na dostęp do kalendarza.',
    'calendar_connection_network_error' => 'Nie można połączyć się z :provider. Sprawdź połączenie internetowe i spróbuj ponownie.',
    'calendar_connection_generic_error' => 'Nie można połączyć kalendarza :provider. Spróbuj ponownie. Jeśli problem się utrzymuje, skontaktuj się z pomocą techniczną.',
];

