<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Calendar connections store OAuth tokens and calendar details.
     * Tokens are encrypted at rest using TokenEncryptionService.
     */
    public function up(): void
    {
        Schema::create('calendar_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Provider info
            $table->enum('provider', ['google', 'microsoft']);
            $table->string('provider_account_id'); // unique ID from provider
            $table->string('provider_email')->nullable();
            
            // OAuth tokens (encrypted)
            $table->text('access_token_encrypted');
            $table->text('refresh_token_encrypted')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            
            // Available calendars on this connection (JSON array)
            $table->json('available_calendars')->nullable();
            
            // Connection status
            $table->enum('status', ['active', 'expired', 'revoked', 'error'])->default('active');
            $table->text('last_error')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Unique constraint: one connection per provider per user
            $table->unique(['user_id', 'provider', 'provider_account_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_connections');
    }
};

