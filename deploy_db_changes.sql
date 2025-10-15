-- ==================================================================
-- Database Changes for Calendar Name & Selected Calendar Features
-- Date: 2025-10-15
-- ==================================================================

-- 1. Add name and selected_calendar_id to calendar_connections table
-- This allows users to give custom names to their calendar connections
-- and store which specific calendar they selected from available_calendars
ALTER TABLE `calendar_connections`
ADD COLUMN `name` VARCHAR(255) NULL AFTER `user_id`,
ADD COLUMN `selected_calendar_id` VARCHAR(255) NULL AFTER `available_calendars`;

-- 2. Add name to sync_rules table
-- This allows users to give custom names to their sync rules
ALTER TABLE `sync_rules`
ADD COLUMN `name` VARCHAR(255) NULL AFTER `user_id`;

-- ==================================================================
-- Optional: Add indexes for better query performance
-- ==================================================================
-- These are optional but recommended for production
-- ALTER TABLE `calendar_connections` ADD INDEX `idx_name` (`name`);
-- ALTER TABLE `sync_rules` ADD INDEX `idx_name` (`name`);

-- ==================================================================
-- Notes:
-- - All new columns are nullable to maintain compatibility with existing data
-- - Existing calendar connections and sync rules will have NULL names initially
-- - Users can update these names through the UI after deployment
-- ==================================================================
