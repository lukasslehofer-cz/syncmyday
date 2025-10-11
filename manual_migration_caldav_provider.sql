-- Manual migration: Add CalDAV support to calendar_connections
-- Run this on production database if migration was already applied

-- 1. Add 'caldav' to provider ENUM
ALTER TABLE calendar_connections 
MODIFY COLUMN provider ENUM('google', 'microsoft', 'caldav') NOT NULL;

-- 2. Make OAuth tokens nullable (CalDAV doesn't use OAuth)
ALTER TABLE calendar_connections 
MODIFY COLUMN access_token_encrypted TEXT NULL;

-- Verify the changes
SHOW COLUMNS FROM calendar_connections LIKE 'provider';
SHOW COLUMNS FROM calendar_connections LIKE 'access_token_encrypted';

