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
            // CalDAV specific fields
            $table->string('caldav_url')->nullable()->after('provider_email');
            $table->string('caldav_username')->nullable()->after('caldav_url');
            $table->text('caldav_password_encrypted')->nullable()->after('caldav_username');
            $table->string('caldav_principal_url')->nullable()->after('caldav_password_encrypted');
            
            // Account email (for CalDAV and general use)
            $table->string('account_email')->nullable()->after('provider_email');
            
            // Sync token for delta sync (used by CalDAV ctag/etag tracking)
            $table->text('sync_token')->nullable()->after('last_sync_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calendar_connections', function (Blueprint $table) {
            $table->dropColumn([
                'caldav_url',
                'caldav_username',
                'caldav_password_encrypted',
                'caldav_principal_url',
                'account_email',
                'sync_token',
            ]);
        });
    }
};
