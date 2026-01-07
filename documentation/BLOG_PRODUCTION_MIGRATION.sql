-- ============================================
-- BLOG SYSTEM - Production Database Migration
-- ============================================
-- Tento soubor obsahuje SQL pro vytvoření blog systému
-- na produkční databázi.
-- 
-- Spusťte tento SQL v phpMyAdmin nebo přímo přes MySQL:
-- mysql -u username -p database_name < BLOG_PRODUCTION_MIGRATION.sql
-- ============================================

-- 1. Vytvoření tabulky blog_categories
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Vytvoření tabulky blog_category_translations
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_category_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(5) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_category_translations_category_id_locale_unique` (`category_id`,`locale`),
  KEY `blog_category_translations_category_id_foreign` (`category_id`),
  CONSTRAINT `blog_category_translations_category_id_foreign` 
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Vytvoření tabulky blog_articles
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint(20) unsigned NOT NULL,
  `slug` varchar(255) NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 0,
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_articles_slug_unique` (`slug`),
  KEY `blog_articles_category_id_foreign` (`category_id`),
  CONSTRAINT `blog_articles_category_id_foreign` 
    FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Vytvoření tabulky blog_article_translations
-- ============================================
CREATE TABLE IF NOT EXISTS `blog_article_translations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` bigint(20) unsigned NOT NULL,
  `locale` varchar(5) NOT NULL,
  `title` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blog_article_translations_article_id_locale_unique` (`article_id`,`locale`),
  KEY `blog_article_translations_article_id_foreign` (`article_id`),
  CONSTRAINT `blog_article_translations_article_id_foreign` 
    FOREIGN KEY (`article_id`) REFERENCES `blog_articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- 5. Vložení základních kategorií (volitelné)
-- ============================================
-- Odkomentujte následující řádky, pokud chcete vytvořit základní kategorie

-- Kategorie 1: Novinky / News
INSERT INTO `blog_categories` (`id`, `slug`, `sort_order`, `created_at`, `updated_at`) 
VALUES (1, 'novinky', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Překlady kategorie Novinky
INSERT INTO `blog_category_translations` (`category_id`, `locale`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'cs', 'Novinky', 'Aktuální novinky a oznámení o aplikaci SyncMyDay', NOW(), NOW()),
(1, 'de', 'Nachrichten', 'Aktuelle Nachrichten und Ankündigungen über SyncMyDay', NOW(), NOW()),
(1, 'en', 'News', 'Latest news and announcements about SyncMyDay', NOW(), NOW()),
(1, 'pl', 'Aktualności', 'Najnowsze wiadomości i ogłoszenia o SyncMyDay', NOW(), NOW()),
(1, 'sk', 'Novinky', 'Aktuálne novinky a oznámenia o aplikácii SyncMyDay', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `updated_at` = NOW();

-- Kategorie 2: Návody / Tutorials
INSERT INTO `blog_categories` (`id`, `slug`, `sort_order`, `created_at`, `updated_at`) 
VALUES (2, 'navody', 2, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Překlady kategorie Návody
INSERT INTO `blog_category_translations` (`category_id`, `locale`, `name`, `description`, `created_at`, `updated_at`) VALUES
(2, 'cs', 'Návody', 'Podrobné návody a postupy pro efektivní využití aplikace', NOW(), NOW()),
(2, 'de', 'Anleitungen', 'Detaillierte Anleitungen zur effektiven Nutzung der App', NOW(), NOW()),
(2, 'en', 'Tutorials', 'Detailed guides and tutorials for effective app usage', NOW(), NOW()),
(2, 'pl', 'Poradniki', 'Szczegółowe przewodniki do efektywnego korzystania z aplikacji', NOW(), NOW()),
(2, 'sk', 'Návody', 'Podrobné návody a postupy pre efektívne využitie aplikácie', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `updated_at` = NOW();

-- Kategorie 3: Tipy a triky / Tips and Tricks
INSERT INTO `blog_categories` (`id`, `slug`, `sort_order`, `created_at`, `updated_at`) 
VALUES (3, 'tipy-a-triky', 3, NOW(), NOW())
ON DUPLICATE KEY UPDATE `updated_at` = NOW();

-- Překlady kategorie Tipy a triky
INSERT INTO `blog_category_translations` (`category_id`, `locale`, `name`, `description`, `created_at`, `updated_at`) VALUES
(3, 'cs', 'Tipy a triky', 'Užitečné tipy a triky pro lepší produktivitu', NOW(), NOW()),
(3, 'de', 'Tipps und Tricks', 'Nützliche Tipps und Tricks für bessere Produktivität', NOW(), NOW()),
(3, 'en', 'Tips and Tricks', 'Useful tips and tricks for better productivity', NOW(), NOW()),
(3, 'pl', 'Wskazówki i triki', 'Przydatne wskazówki i triki dla lepszej produktywności', NOW(), NOW()),
(3, 'sk', 'Tipy a triky', 'Užitočné tipy a triky pre lepšiu produktivitu', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `description` = VALUES(`description`),
  `updated_at` = NOW();

-- ============================================
-- HOTOVO!
-- ============================================
-- Tabulky byly úspěšně vytvořeny.
-- Můžete začít vytvářet blogové články v admin panelu:
-- https://vase-domena.cz/admin/blog
-- ============================================

