<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('registration_domain', 50)
                  ->default('syncmyday.cz')
                  ->after('locale')
                  ->comment('Domain where user registered (determines email FROM domain)');
        });
        
        // Backfill existing users based on their locale
        DB::statement("
            UPDATE users 
            SET registration_domain = CASE locale
                WHEN 'cs' THEN 'syncmyday.cz'
                WHEN 'sk' THEN 'syncmyday.sk'
                WHEN 'pl' THEN 'syncmyday.pl'
                WHEN 'de' THEN 'syncmyday.de'
                WHEN 'en' THEN 'syncmyday.eu'
                ELSE 'syncmyday.cz'
            END
            WHERE registration_domain IS NULL OR registration_domain = 'syncmyday.cz'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('registration_domain');
        });
    }
};

