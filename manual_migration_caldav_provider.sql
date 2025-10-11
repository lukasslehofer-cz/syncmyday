-- Manual migration: Add 'caldav' to provider ENUM
-- Run this on production database if migration was already applied

ALTER TABLE calendar_connections 
MODIFY COLUMN provider ENUM('google', 'microsoft', 'caldav') NOT NULL;

-- Verify the change
SHOW COLUMNS FROM calendar_connections LIKE 'provider';

