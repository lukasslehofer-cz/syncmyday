-- ============================================================================
-- PERFORMANCE OPTIMIZATION SQL - SyncMyDay
-- ============================================================================
-- Spusťte tento skript na produkční databázi pro optimalizaci výkonu
-- Čas běhu: ~10-30 sekund (závisí na velikosti dat)
-- BACKUP: Před spuštěním vytvořte zálohu databáze!
-- ============================================================================

-- ----------------------------------------------------------------------------
-- 1. PŘIDÁNÍ NOVÝCH SLOUPCŮ PRO QUEUE SYSTÉM
-- ----------------------------------------------------------------------------

-- Přidat sloupce pro queue management do sync_rules
ALTER TABLE `sync_rules` 
ADD COLUMN `queued_at` TIMESTAMP NULL AFTER `last_triggered_at`,
ADD COLUMN `queue_priority` INT DEFAULT 0 NOT NULL AFTER `queued_at`,
ADD COLUMN `last_sync_duration_ms` INT NULL AFTER `queue_priority`,
ADD COLUMN `sync_error_count` INT DEFAULT 0 NOT NULL AFTER `last_sync_duration_ms`;

-- Přidat monitoring sloupce do calendar_connections
ALTER TABLE `calendar_connections` 
ADD COLUMN `sync_error_count` INT DEFAULT 0 NOT NULL AFTER `last_sync_at`,
ADD COLUMN `last_sync_duration_ms` INT NULL AFTER `sync_error_count`;

-- ----------------------------------------------------------------------------
-- 2. KRITICKÉ INDEXY PRO SYNC_EVENT_MAPPINGS (největší tabulka)
-- ----------------------------------------------------------------------------

-- Index pro anti-loop dotazy (používá se velmi často)
ALTER TABLE `sync_event_mappings` 
ADD INDEX `idx_source_conn` (`source_connection_id`);

-- Composite index pro časté lookups
ALTER TABLE `sync_event_mappings` 
ADD INDEX `idx_rule_source` (`sync_rule_id`, `source_event_id`);

-- Index pro target calendar lookups
ALTER TABLE `sync_event_mappings` 
ADD INDEX `idx_target_composite` (`target_connection_id`, `target_calendar_id`);

-- Index pro čištění starých záznamů
ALTER TABLE `sync_event_mappings` 
ADD INDEX `idx_created` (`created_at`);

-- Index pro email connection lookups
ALTER TABLE `sync_event_mappings` 
ADD INDEX `idx_email_conn` (`email_connection_id`);

-- ----------------------------------------------------------------------------
-- 3. INDEXY PRO CALENDAR_CONNECTIONS
-- ----------------------------------------------------------------------------

-- Composite index pro filtrování aktivních připojení uživatele
ALTER TABLE `calendar_connections` 
ADD INDEX `idx_user_status` (`user_id`, `status`);

-- Index pro monitoring a webhook renewal
ALTER TABLE `calendar_connections` 
ADD INDEX `idx_last_sync` (`last_sync_at`);

-- Index pro provider queries
ALTER TABLE `calendar_connections` 
ADD INDEX `idx_provider_status` (`provider`, `status`);

-- ----------------------------------------------------------------------------
-- 4. INDEXY PRO SYNC_RULES
-- ----------------------------------------------------------------------------

-- Composite index pro aktivní pravidla s připojením
ALTER TABLE `sync_rules` 
ADD INDEX `idx_active_source` (`is_active`, `source_connection_id`);

-- Index pro queue processing
ALTER TABLE `sync_rules` 
ADD INDEX `idx_queued` (`queued_at`);

-- Index pro priority-based processing
ALTER TABLE `sync_rules` 
ADD INDEX `idx_queue_priority` (`queue_priority`, `is_active`);

-- Index pro email source rules
ALTER TABLE `sync_rules` 
ADD INDEX `idx_email_source` (`source_email_connection_id`);

-- ----------------------------------------------------------------------------
-- 5. INDEXY PRO SYNC_LOGS (rostoucí tabulka)
-- ----------------------------------------------------------------------------

-- Index pro automatické čištění
ALTER TABLE `sync_logs` 
ADD INDEX `idx_created_cleanup` (`created_at`, `action`);

-- Composite index pro user dashboard queries
ALTER TABLE `sync_logs` 
ADD INDEX `idx_user_action` (`user_id`, `action`, `created_at`);

-- ----------------------------------------------------------------------------
-- 6. INDEXY PRO WEBHOOK_SUBSCRIPTIONS
-- ----------------------------------------------------------------------------

-- Index pro renewal job (expirující webhooky)
ALTER TABLE `webhook_subscriptions` 
ADD INDEX `idx_expires` (`expires_at`, `status`);

-- Composite index pro calendar lookups
ALTER TABLE `webhook_subscriptions` 
ADD INDEX `idx_calendar_status` (`calendar_connection_id`, `calendar_id`, `status`);

-- ----------------------------------------------------------------------------
-- 7. INDEXY PRO EMAIL_CALENDAR_CONNECTIONS
-- ----------------------------------------------------------------------------

-- Index pro inbound email processing
ALTER TABLE `email_calendar_connections` 
ADD INDEX `idx_status_active` (`status`, `last_email_at`);

-- ----------------------------------------------------------------------------
-- 8. INDEXY PRO SYNC_RULE_TARGETS
-- ----------------------------------------------------------------------------

-- Index pro rychlé načítání targetů pro rule
ALTER TABLE `sync_rule_targets` 
ADD INDEX `idx_sync_rule` (`sync_rule_id`);

-- Index pro reverse lookups (které rules targetují toto připojení)
ALTER TABLE `sync_rule_targets` 
ADD INDEX `idx_target_conn` (`target_connection_id`);

-- Index pro email target lookups
ALTER TABLE `sync_rule_targets` 
ADD INDEX `idx_email_target` (`target_email_connection_id`);

-- ----------------------------------------------------------------------------
-- 9. INDEXY PRO USERS
-- ----------------------------------------------------------------------------

-- Index pro subscription queries
ALTER TABLE `users` 
ADD INDEX `idx_subscription` (`subscription_tier`, `subscription_ends_at`);

-- Index pro trial expiry notifications
ALTER TABLE `users` 
ADD INDEX `idx_trial_expiry` (`subscription_tier`, `subscription_ends_at`, `email_verified_at`);

-- ----------------------------------------------------------------------------
-- 10. OPTIMALIZACE TABULKY PRO InnoDB
-- ----------------------------------------------------------------------------

-- Analyzovat a optimalizovat všechny hlavní tabulky
ANALYZE TABLE `sync_event_mappings`;
ANALYZE TABLE `sync_logs`;
ANALYZE TABLE `calendar_connections`;
ANALYZE TABLE `sync_rules`;
ANALYZE TABLE `webhook_subscriptions`;

-- Optimalizovat fragmentované tabulky
OPTIMIZE TABLE `sync_event_mappings`;
OPTIMIZE TABLE `sync_logs`;

-- ----------------------------------------------------------------------------
-- VÝSTUP A VERIFIKACE
-- ----------------------------------------------------------------------------

-- Zobrazit statistiky tabulek
SELECT 
    TABLE_NAME,
    TABLE_ROWS,
    ROUND(DATA_LENGTH / 1024 / 1024, 2) AS 'Data MB',
    ROUND(INDEX_LENGTH / 1024 / 1024, 2) AS 'Index MB',
    ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) AS 'Total MB'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME IN (
        'sync_event_mappings', 
        'sync_logs', 
        'calendar_connections', 
        'sync_rules',
        'webhook_subscriptions',
        'users'
    )
ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;

-- Zobrazit všechny indexy na hlavních tabulkách
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) AS 'Columns',
    INDEX_TYPE,
    NON_UNIQUE
FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME IN ('sync_event_mappings', 'sync_rules', 'calendar_connections')
GROUP BY TABLE_NAME, INDEX_NAME, INDEX_TYPE, NON_UNIQUE
ORDER BY TABLE_NAME, INDEX_NAME;

-- ============================================================================
-- HOTOVO! 
-- Po spuštění byste měli vidět výrazné zlepšení výkonu.
-- Doporučené další kroky:
-- 1. Otestovat sync performance
-- 2. Monitorovat EXPLAIN na pomalých dotazech
-- 3. Nastavit automatické čištění sync_logs
-- ============================================================================

