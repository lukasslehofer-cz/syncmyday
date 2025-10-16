-- Clear stuck jobs from queue
-- Run this in phpMyAdmin if you have jobs stuck in queue

-- Delete all pending jobs
DELETE FROM jobs WHERE queue = 'sync';

-- Reset queued_at flags in sync_rules
UPDATE sync_rules SET queued_at = NULL;

-- Check what's in the queue
SELECT * FROM jobs WHERE queue = 'sync';

-- Verify sync_rules are ready
SELECT id, name, is_active, queued_at FROM sync_rules WHERE is_active = 1;

