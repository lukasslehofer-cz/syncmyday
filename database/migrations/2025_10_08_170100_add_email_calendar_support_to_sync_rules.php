<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add support for email calendars as sources and targets in sync rules.
     */
    public function up(): void
    {
        // Modify sync_rules to support email calendars as source
        Schema::table('sync_rules', function (Blueprint $table) {
            // Make source_connection_id nullable (could be email calendar instead)
            $table->foreignId('source_connection_id')->nullable()->change();
            
            // Add source_email_connection_id for email calendar sources
            $table->foreignId('source_email_connection_id')
                ->nullable()
                ->after('source_connection_id')
                ->constrained('email_calendar_connections')
                ->onDelete('cascade');
            
            // Make source_calendar_id nullable (email calendars don't have provider calendar IDs)
            $table->string('source_calendar_id')->nullable()->change();
            
            // Add constraint: must have either source_connection_id OR source_email_connection_id
            // (This will be validated at application level, not database level)
        });
        
        // Modify sync_rule_targets to support email calendars as targets
        Schema::table('sync_rule_targets', function (Blueprint $table) {
            // Make target_connection_id nullable (could be email calendar instead)
            $table->foreignId('target_connection_id')->nullable()->change();
            
            // Add target_email_connection_id for email calendar targets
            $table->foreignId('target_email_connection_id')
                ->nullable()
                ->after('target_connection_id')
                ->constrained('email_calendar_connections')
                ->onDelete('cascade');
            
            // Make target_calendar_id nullable (email calendars don't have provider calendar IDs)
            $table->string('target_calendar_id')->nullable()->change();
            
            // Add target_email for email calendar targets (where to send iMIP)
            $table->string('target_email')->nullable()->after('target_calendar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_rule_targets', function (Blueprint $table) {
            $table->dropForeign(['target_email_connection_id']);
            $table->dropColumn(['target_email_connection_id', 'target_email']);
        });
        
        Schema::table('sync_rules', function (Blueprint $table) {
            $table->dropForeign(['source_email_connection_id']);
            $table->dropColumn('source_email_connection_id');
        });
    }
};

