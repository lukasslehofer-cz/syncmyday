-- SQL příkaz pro produkční databázi
-- Odstraňuje sloupce description a sender_whitelist z tabulky email_calendar_connections
-- Spouštět po nasazení nového kódu na produkci

-- Odstranit sloupce z email_calendar_connections
ALTER TABLE `email_calendar_connections` 
DROP COLUMN `description`,
DROP COLUMN `sender_whitelist`;

-- Konec

