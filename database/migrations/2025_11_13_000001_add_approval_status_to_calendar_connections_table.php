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
        Schema::table('calendar_connections', function (Blueprint $table) {
            // Add approval status for handling admin consent requirements
            $table->enum('approval_status', ['approved', 'pending', 'rejected'])
                  ->default('approved')
                  ->after('status');
            
            // Reason for pending status (e.g., 'admin_consent_required')
            $table->string('pending_reason')->nullable()->after('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'pending_reason']);
        });
    }
};

