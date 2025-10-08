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
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            // Direction: incoming (receive emails) / outgoing (send emails) / both
            $table->string('direction')->default('incoming')->after('name');
            
            // Target email address for outgoing direction
            $table->string('target_email')->nullable()->after('email_token');
            
            // Index for performance
            $table->index('direction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->dropIndex(['direction']);
            $table->dropColumn(['direction', 'target_email']);
        });
    }
};

