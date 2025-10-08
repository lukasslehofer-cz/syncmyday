<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add sequence tracking and email target support to sync_event_mappings
     */
    public function up(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            // Add sequence number for iMIP updates
            $table->integer('sequence')->default(0)->after('target_event_id');
            
            // Add target_email_connection_id for email calendar targets
            $table->foreignId('target_email_connection_id')
                ->nullable()
                ->after('target_connection_id')
                ->constrained('email_calendar_connections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            $table->dropForeign(['target_email_connection_id']);
            $table->dropColumn(['sequence', 'target_email_connection_id']);
        });
    }
};

