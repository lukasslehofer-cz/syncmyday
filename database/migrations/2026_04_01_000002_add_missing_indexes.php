<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Prevent duplicate webhook subscriptions for the same calendar
        Schema::table('webhook_subscriptions', function (Blueprint $table) {
            $table->unique(
                ['calendar_connection_id', 'calendar_id'],
                'webhook_connection_calendar_unique'
            );
        });

        // Index for email mapping lookups in SyncEngine
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            $table->index(
                ['sync_rule_id', 'source_event_id', 'target_email_connection_id'],
                'mapping_email_target_index'
            );
        });
    }

    public function down(): void
    {
        Schema::table('webhook_subscriptions', function (Blueprint $table) {
            $table->dropUnique('webhook_connection_calendar_unique');
        });

        Schema::table('sync_event_mappings', function (Blueprint $table) {
            $table->dropIndex('mapping_email_target_index');
        });
    }
};
