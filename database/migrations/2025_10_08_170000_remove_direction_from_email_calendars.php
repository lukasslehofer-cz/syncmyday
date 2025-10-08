<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Remove direction and target_email from email_calendar_connections.
     * Email calendars are now always bidirectional - sync rules define the direction.
     */
    public function up(): void
    {
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->dropIndex(['direction']);
            $table->dropColumn(['direction', 'target_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->string('direction')->default('incoming')->after('name');
            $table->string('target_email')->nullable()->after('email_token');
            $table->index('direction');
        });
    }
};

