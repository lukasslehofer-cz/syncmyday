-- Add stripe_currency column to users table
-- This column stores the currency used when first creating Stripe customer
-- Prevents currency conflicts when user switches domains/locales
-- Migration: 2025_10_19_154201_add_stripe_currency_to_users_table.php

ALTER TABLE users 
ADD COLUMN stripe_currency VARCHAR(3) NULL 
AFTER stripe_subscription_id;

-- Optional: Set currency for existing users based on their locale
-- Only run this if you want to backfill existing users
-- UPDATE users SET stripe_currency = 'CZK' WHERE locale = 'cs' AND stripe_customer_id IS NOT NULL;
-- UPDATE users SET stripe_currency = 'EUR' WHERE locale IN ('en', 'sk', 'de') AND stripe_customer_id IS NOT NULL;
-- UPDATE users SET stripe_currency = 'PLN' WHERE locale = 'pl' AND stripe_customer_id IS NOT NULL;

