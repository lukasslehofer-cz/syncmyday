<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fix unique constraint to include target_email_connection_id
     * 
     * The old constraint only covered (sync_rule_id, source_event_id, target_connection_id, target_calendar_id)
     * This caused duplicate key errors for email targets where target_connection_id is NULL.
     * 
     * The new constraint includes target_email_connection_id to properly handle:
     * - API targets: (rule_id, event_id, connection_id, calendar_id, NULL)
     * - Email targets: (rule_id, event_id, NULL, NULL, email_connection_id)
     */
    public function up(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique('mapping_unique');
            
            // Create new unique constraint that includes target_email_connection_id
            $table->unique([
                'sync_rule_id',
                'source_event_id',
                'target_connection_id',
                'target_calendar_id',
                'target_email_connection_id'
            ], 'mapping_unique_v2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('mapping_unique_v2');
            
            // Restore the old constraint (will fail if there are email targets with duplicates)
            $table->unique([
                'sync_rule_id',
                'source_event_id',
                'target_connection_id',
                'target_calendar_id'
            ], 'mapping_unique');
        });
    }
};

