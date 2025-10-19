-- Add grace_period_ends_at column to users table
-- This column is used to track grace period after payment failures
-- Migration: 2025_10_17_194209_add_grace_period_to_users_table.php

ALTER TABLE users 
ADD COLUMN grace_period_ends_at TIMESTAMP NULL 
AFTER subscription_ends_at;

