<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sync rules define how events should be synchronized between calendars.
     */
    public function up(): void
    {
        Schema::create('sync_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Source calendar
            $table->foreignId('source_connection_id')->constrained('calendar_connections')->onDelete('cascade');
            $table->string('source_calendar_id'); // calendar ID from provider
            
            // Target calendar(s) - we'll use a pivot table for many-to-many
            // This table just defines the source
            
            // Sync direction
            $table->enum('direction', ['one_way', 'two_way'])->default('one_way');
            
            // Filters (stored as JSON for flexibility)
            $table->json('filters')->nullable(); // e.g., {"busy_only": true, "ignore_all_day": false, "work_hours": "09:00-17:00"}
            
            // Blocker template
            $table->string('blocker_title')->default('Busy — Sync');
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Pivot table for rule targets
        Schema::create('sync_rule_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sync_rule_id')->constrained()->onDelete('cascade');
            $table->foreignId('target_connection_id')->constrained('calendar_connections')->onDelete('cascade');
            $table->string('target_calendar_id');
            $table->timestamps();
            
            $table->unique(['sync_rule_id', 'target_connection_id', 'target_calendar_id'], 'sync_rule_target_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_rule_targets');
        Schema::dropIfExists('sync_rules');
    }
};

