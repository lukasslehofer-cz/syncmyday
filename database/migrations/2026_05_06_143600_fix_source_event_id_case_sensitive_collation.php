<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Microsoft Graph (a další provideři) používají v event ID base64-like
        // řetězce, kde je velikost písmen signifikantní. Default MySQL collation
        // utf8mb4_unicode_ci je case-insensitive, což způsobuje, že dva eventy
        // s ID lišícím se pouze velikostí písmen spadnou do stejného řádku
        // unique indexu (mapping_unique) a sdílí jeden blocker — výsledkem
        // je nekonečný ping-pong update na jejich rozdílné časy.
        //
        // utf8mb4_bin = binární porovnání, case-sensitive.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE sync_event_mappings MODIFY source_event_id VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE sync_event_mappings MODIFY source_event_id VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        }
    }
};
