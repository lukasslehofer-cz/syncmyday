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
        Schema::table('sync_rules', function (Blueprint $table) {
            // Time filter settings
            $table->boolean('time_filter_enabled')->default(false)->after('filters');
            $table->string('time_filter_type')->nullable()->after('time_filter_enabled'); // 'workdays', 'weekends', 'custom'
            $table->time('time_filter_start')->nullable()->after('time_filter_type'); // e.g., 08:00
            $table->time('time_filter_end')->nullable()->after('time_filter_start'); // e.g., 17:00
            $table->json('time_filter_days')->nullable()->after('time_filter_end'); // [1,2,3,4,5] = Mon-Fri
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_rules', function (Blueprint $table) {
            $table->dropColumn([
                'time_filter_enabled',
                'time_filter_type',
                'time_filter_start',
                'time_filter_end',
                'time_filter_days',
            ]);
        });
    }
};

