<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the provider ENUM to include 'apple' and 'caldav'
        DB::statement("ALTER TABLE `calendar_connections` MODIFY COLUMN `provider` ENUM('google', 'microsoft', 'apple', 'caldav') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original ENUM values
        // WARNING: This will fail if there are existing records with 'apple' or 'caldav'
        DB::statement("ALTER TABLE `calendar_connections` MODIFY COLUMN `provider` ENUM('google', 'microsoft') NOT NULL");
    }
};
