<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make target_connection_id and target_calendar_id nullable to support email calendar targets
     * 
     * When syncing to email calendars, we don't have:
     * - target_connection_id (no API connection)
     * - target_calendar_id (no API calendar ID, only email address)
     * 
     * Only target_email_connection_id is used for email targets.
     */
    public function up(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['target_connection_id']);
            
            // Make target_connection_id nullable
            $table->foreignId('target_connection_id')
                ->nullable()
                ->change();
            
            // Make target_calendar_id nullable (email calendars don't have calendar IDs)
            $table->string('target_calendar_id')
                ->nullable()
                ->change();
            
            // Re-add the foreign key constraint (now with nullable)
            $table->foreign('target_connection_id')
                ->references('id')
                ->on('calendar_connections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['target_connection_id']);
            
            // Make target_connection_id NOT NULL again
            $table->foreignId('target_connection_id')
                ->nullable(false)
                ->change();
            
            // Make target_calendar_id NOT NULL again
            $table->string('target_calendar_id')
                ->nullable(false)
                ->change();
            
            // Re-add the foreign key constraint
            $table->foreign('target_connection_id')
                ->references('id')
                ->on('calendar_connections')
                ->onDelete('cascade');
        });
    }
};

