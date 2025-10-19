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
            $table->dropColumn(['description', 'sender_whitelist']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_calendar_connections', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->json('sender_whitelist')->nullable()->after('description');
        });
    }
};
