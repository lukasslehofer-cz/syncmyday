<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add target_email back to email_calendar_connections
     * Remove target_email from sync_rule_targets
     */
    public function up(): void
    {
        // Add target_email to email_calendar_connections
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->string('target_email')->nullable()->after('email_token');
        });
        
        // Remove target_email from sync_rule_targets
        Schema::table('sync_rule_targets', function (Blueprint $table) {
            $table->dropColumn('target_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_rule_targets', function (Blueprint $table) {
            $table->string('target_email')->nullable()->after('target_calendar_id');
        });
        
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->dropColumn('target_email');
        });
    }
};

