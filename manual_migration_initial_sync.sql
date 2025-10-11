-- Migration: Add initial_sync_completed to sync_rules table
-- This prevents flooding user's inbox with hundreds of emails on first sync
-- Date: 2025-10-11

-- Add the column
ALTER TABLE `sync_rules` 
ADD COLUMN `initial_sync_completed` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_active`;

-- Set all existing rules to completed (they've already done their initial sync)
UPDATE `sync_rules` SET `initial_sync_completed` = 1 WHERE `id` > 0;

