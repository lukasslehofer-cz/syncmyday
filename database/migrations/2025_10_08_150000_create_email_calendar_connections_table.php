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
        Schema::create('email_calendar_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Unique email address for this calendar
            $table->string('email_address')->unique(); // e.g., a7b2c9f4@syncmyday.com
            $table->string('email_token')->unique(); // e.g., a7b2c9f4 (short identifier)
            
            // Calendar info
            $table->string('name'); // e.g., "Work Calendar"
            $table->text('description')->nullable();
            
            // Optional sender whitelist (JSON array of allowed sender emails)
            $table->json('sender_whitelist')->nullable();
            
            // Stats
            $table->integer('emails_received')->default(0);
            $table->integer('events_processed')->default(0);
            $table->timestamp('last_email_at')->nullable();
            
            // Status
            $table->string('status')->default('active'); // active, paused, error
            $table->text('last_error')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('email_token');
            $table->index(['user_id', 'status']);
        });
        
        // Update sync_event_mappings to support email source
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            $table->string('source_type')->default('api')->after('sync_rule_id'); // 'api', 'email', 'ics'
            $table->foreignId('email_connection_id')->nullable()->after('source_type')->constrained('email_calendar_connections')->onDelete('cascade');
            $table->string('original_event_uid')->nullable()->after('source_event_id'); // Original UID from .ics
            
            // Make source_connection_id nullable for email sources
            $table->foreignId('source_connection_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_event_mappings', function (Blueprint $table) {
            $table->dropForeign(['email_connection_id']);
            $table->dropColumn(['source_type', 'email_connection_id', 'original_event_uid']);
        });
        
        Schema::dropIfExists('email_calendar_connections');
    }
};

