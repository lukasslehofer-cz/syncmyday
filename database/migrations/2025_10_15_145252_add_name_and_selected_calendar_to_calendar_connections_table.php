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
            // User-friendly name for the calendar connection
            $table->string('name')->nullable()->after('user_id');
            
            // ID of the selected calendar from available_calendars array
            $table->string('selected_calendar_id')->nullable()->after('available_calendars');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn(['name', 'selected_calendar_id']);
        });
    }
};
