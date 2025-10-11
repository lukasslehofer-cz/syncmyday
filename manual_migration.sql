-- Manual Migration: Make target_connection_id and target_calendar_id nullable
-- This allows email calendar targets which don't have API connections or calendar IDs

-- Step 1: Drop the foreign key constraint
ALTER TABLE `sync_event_mappings` 
DROP FOREIGN KEY `sync_event_mappings_target_connection_id_foreign`;

-- Step 2: Make target_connection_id nullable
ALTER TABLE `sync_event_mappings` 
MODIFY COLUMN `target_connection_id` BIGINT UNSIGNED NULL;

-- Step 3: Make target_calendar_id nullable (email calendars don't have calendar IDs)
ALTER TABLE `sync_event_mappings` 
MODIFY COLUMN `target_calendar_id` VARCHAR(255) NULL;

-- Step 4: Re-add the foreign key constraint (now with nullable)
ALTER TABLE `sync_event_mappings` 
ADD CONSTRAINT `sync_event_mappings_target_connection_id_foreign` 
FOREIGN KEY (`target_connection_id`) 
REFERENCES `calendar_connections` (`id`) 
ON DELETE CASCADE;

-- Verify the changes
DESCRIBE `sync_event_mappings`;

