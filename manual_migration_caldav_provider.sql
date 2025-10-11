-- Manual migration: Add CalDAV support to calendar_connections
-- Run this on production database
-- ⚠️ IMPORTANT: Run ALL commands in order!

-- 1. Add 'caldav' to provider ENUM
ALTER TABLE calendar_connections 
MODIFY COLUMN provider ENUM('google', 'microsoft', 'caldav') NOT NULL;

-- 2. Make OAuth tokens nullable (CalDAV doesn't use OAuth)
ALTER TABLE calendar_connections 
MODIFY COLUMN access_token_encrypted TEXT NULL;

-- 3. Add account_email column (after provider_email)
ALTER TABLE calendar_connections 
ADD COLUMN account_email VARCHAR(255) NULL AFTER provider_email;

-- 4. Add CalDAV specific columns
ALTER TABLE calendar_connections 
ADD COLUMN caldav_url VARCHAR(255) NULL AFTER account_email;

ALTER TABLE calendar_connections 
ADD COLUMN caldav_username VARCHAR(255) NULL AFTER caldav_url;

ALTER TABLE calendar_connections 
ADD COLUMN caldav_password_encrypted TEXT NULL AFTER caldav_username;

ALTER TABLE calendar_connections 
ADD COLUMN caldav_principal_url VARCHAR(255) NULL AFTER caldav_password_encrypted;

-- 5. Add sync_token column (after last_sync_at)
ALTER TABLE calendar_connections 
ADD COLUMN sync_token TEXT NULL AFTER last_sync_at;

-- Verify all changes
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'calendar_connections' 
  AND COLUMN_NAME IN ('provider', 'access_token_encrypted', 'account_email', 'caldav_url', 'caldav_username', 'caldav_password_encrypted', 'caldav_principal_url', 'sync_token')
ORDER BY ORDINAL_POSITION;

