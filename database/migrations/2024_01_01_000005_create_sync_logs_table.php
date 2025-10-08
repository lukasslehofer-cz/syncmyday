<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sync logs store minimal information about sync operations.
     * We avoid storing event details for privacy reasons.
     */
    public function up(): void
    {
        Schema::create('sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('sync_rule_id')->nullable()->constrained()->onDelete('set null');
            
            // What happened
            $table->enum('action', ['created', 'updated', 'deleted', 'skipped', 'error']);
            $table->enum('direction', ['source_to_target', 'target_to_source'])->nullable();
            
            // Minimal event data (no sensitive info)
            $table->string('source_event_id')->nullable();
            $table->string('target_event_id')->nullable();
            $table->timestamp('event_start')->nullable();
            $table->timestamp('event_end')->nullable();
            
            // Error details (if any)
            $table->text('error_message')->nullable();
            
            // Transaction ID to prevent loops
            $table->string('transaction_id')->nullable()->index();
            
            $table->timestamp('created_at');
            
            // Add index for user queries
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_logs');
    }
};

