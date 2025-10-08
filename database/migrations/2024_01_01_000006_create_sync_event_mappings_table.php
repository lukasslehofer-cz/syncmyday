<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sync_event_mappings', function (Blueprint $table) {
            $table->id();
            
            // Which sync rule created this mapping
            $table->foreignId('sync_rule_id')->constrained()->onDelete('cascade');
            
            // Source event info
            $table->foreignId('source_connection_id')->constrained('calendar_connections')->onDelete('cascade');
            $table->string('source_calendar_id');
            $table->string('source_event_id');
            
            // Target blocker info
            $table->foreignId('target_connection_id')->constrained('calendar_connections')->onDelete('cascade');
            $table->string('target_calendar_id');
            $table->string('target_event_id'); // The blocker we created
            
            // Event metadata (for updates)
            $table->timestamp('event_start')->nullable();
            $table->timestamp('event_end')->nullable();
            
            $table->timestamps();
            
            // Indexes for fast lookups
            $table->unique([
                'sync_rule_id',
                'source_event_id',
                'target_connection_id',
                'target_calendar_id'
            ], 'mapping_unique');
            
            $table->index(['source_event_id']);
            $table->index(['target_event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sync_event_mappings');
    }
};

