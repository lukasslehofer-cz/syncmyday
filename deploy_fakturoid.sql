-- ============================================
-- Fakturoid Integration Deployment SQL
-- Created: 2025-10-19
-- ============================================

-- Create fakturoid_invoices table
CREATE TABLE IF NOT EXISTS `fakturoid_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `fakturoid_id` bigint unsigned DEFAULT NULL COMMENT 'ID from Fakturoid API',
  `fakturoid_number` varchar(255) DEFAULT NULL COMMENT 'Invoice number (e.g. SMD-2025-00001)',
  `stripe_invoice_id` varchar(255) DEFAULT NULL COMMENT 'Reference to Stripe invoice',
  `amount` decimal(10,2) NOT NULL COMMENT 'Invoice amount',
  `currency` varchar(3) NOT NULL COMMENT 'CZK, EUR, PLN, etc.',
  `language` varchar(2) NOT NULL COMMENT 'cz, en, de, pl, sk',
  `description` varchar(255) DEFAULT NULL COMMENT 'Invoice description',
  `issued_at` timestamp NULL DEFAULT NULL COMMENT 'When invoice was issued',
  `pdf_url` varchar(255) DEFAULT NULL COMMENT 'Cached PDF URL (optional)',
  `status` enum('pending','created','failed') NOT NULL DEFAULT 'pending' COMMENT 'Processing status',
  `error_message` text DEFAULT NULL COMMENT 'Error message if creation failed',
  `retry_count` int NOT NULL DEFAULT 0 COMMENT 'Number of retry attempts',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fakturoid_invoices_user_id_index` (`user_id`),
  KEY `fakturoid_invoices_fakturoid_id_index` (`fakturoid_id`),
  KEY `fakturoid_invoices_stripe_invoice_id_index` (`stripe_invoice_id`),
  KEY `fakturoid_invoices_status_index` (`status`),
  KEY `fakturoid_invoices_issued_at_index` (`issued_at`),
  CONSTRAINT `fakturoid_invoices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Verification queries (run after deployment)
-- ============================================

-- Check if table was created
-- SELECT COUNT(*) FROM information_schema.tables 
-- WHERE table_schema = DATABASE() 
-- AND table_name = 'fakturoid_invoices';

-- Check table structure
-- DESCRIBE fakturoid_invoices;

-- Check for any invoices (should be empty after fresh install)
-- SELECT COUNT(*) FROM fakturoid_invoices;

