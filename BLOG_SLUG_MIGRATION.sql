-- ============================================
-- Blog Slug Translation Migration
-- ============================================
-- This migration moves slug from blog_articles to blog_article_translations
-- to support translated slugs for each language
-- 
-- Run this on production AFTER pulling the latest code from GitHub

-- Step 0: Delete existing blog data (will be re-imported with new slugs)
DELETE FROM `blog_article_translations`;
DELETE FROM `blog_articles`;
DELETE FROM `blog_category_translations`;
DELETE FROM `blog_categories`;

-- Step 1: Add slug column to blog_article_translations
ALTER TABLE `blog_article_translations` 
ADD COLUMN `slug` VARCHAR(255) NOT NULL AFTER `locale`;

-- Step 2: Add unique index for locale + slug
ALTER TABLE `blog_article_translations` 
ADD UNIQUE INDEX `blog_article_translations_locale_slug_unique` (`locale`, `slug`);

-- Step 3: Drop unique index from blog_articles.slug
ALTER TABLE `blog_articles` 
DROP INDEX `blog_articles_slug_unique`;

-- Step 4: Drop slug column from blog_articles
ALTER TABLE `blog_articles` 
DROP COLUMN `slug`;

-- ============================================
-- IMPORTANT: After running this migration
-- ============================================
-- 1. All existing blog articles will be deleted (if any exist)
-- 2. Run: bash blog-import.sh to import articles from blog-export.json
-- 3. The import script will now handle translated slugs correctly

