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
        // First, permanently delete any soft-deleted connections
        DB::table('calendar_connections')
            ->whereNotNull('deleted_at')
            ->delete();
        
        // Then remove the deleted_at column
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};

