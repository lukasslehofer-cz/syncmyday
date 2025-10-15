-- ==================================================================
-- ROLLBACK Script for Calendar Name & Selected Calendar Features
-- Date: 2025-10-15
-- Use only if you need to revert the database changes
-- ==================================================================

-- Remove columns in reverse order of deployment

-- 1. Remove name from sync_rules table
ALTER TABLE `sync_rules`
DROP COLUMN `name`;

-- 2. Remove name and selected_calendar_id from calendar_connections table
ALTER TABLE `calendar_connections`
DROP COLUMN `selected_calendar_id`,
DROP COLUMN `name`;

-- ==================================================================
-- WARNING: This will permanently delete all custom names and 
-- selected calendar settings. Make sure to backup before running!
-- ==================================================================
