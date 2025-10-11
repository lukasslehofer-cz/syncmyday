<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Make target_email required for email calendars.
     * This email serves as both the source identifier and target for blockers.
     */
    public function up(): void
    {
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            // Make target_email NOT NULL
            $table->string('target_email')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->string('target_email')->nullable()->change();
        });
    }
};

