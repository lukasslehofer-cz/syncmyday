-- Manual Migration: Make target_connection_id nullable in sync_event_mappings
-- This allows email calendar targets which don't have API connections

-- Step 1: Drop the foreign key constraint
ALTER TABLE `sync_event_mappings` 
DROP FOREIGN KEY `sync_event_mappings_target_connection_id_foreign`;

-- Step 2: Modify column to be nullable
ALTER TABLE `sync_event_mappings` 
MODIFY COLUMN `target_connection_id` BIGINT UNSIGNED NULL;

-- Step 3: Re-add the foreign key constraint (now with nullable)
ALTER TABLE `sync_event_mappings` 
ADD CONSTRAINT `sync_event_mappings_target_connection_id_foreign` 
FOREIGN KEY (`target_connection_id`) 
REFERENCES `calendar_connections` (`id`) 
ON DELETE CASCADE;

-- Verify the change
DESCRIBE `sync_event_mappings`;

