<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // OAuth provider info
            $table->string('oauth_provider')->nullable()->after('email');
            $table->string('oauth_provider_id')->nullable()->after('oauth_provider');
            $table->string('oauth_provider_email')->nullable()->after('oauth_provider_id');
            
            // Make password nullable for OAuth users
            $table->string('password')->nullable()->change();
            
            // Add unique index for oauth provider + id combination
            $table->unique(['oauth_provider', 'oauth_provider_id'], 'oauth_provider_unique');
        });
        
        // Update email_verified_at for OAuth users (they're pre-verified)
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('email_verified_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('oauth_provider_unique');
            $table->dropColumn(['oauth_provider', 'oauth_provider_id', 'oauth_provider_email']);
            $table->string('password')->nullable(false)->change();
        });
    }
};

