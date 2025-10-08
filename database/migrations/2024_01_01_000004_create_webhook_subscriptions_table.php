<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Webhook subscriptions track active watch channels with providers.
     */
    public function up(): void
    {
        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calendar_connection_id')->constrained()->onDelete('cascade');
            
            // Provider-specific subscription ID
            $table->string('provider_subscription_id')->unique();
            $table->string('resource_id'); // Google: resourceId, Microsoft: changeType
            $table->string('calendar_id'); // which calendar is being watched
            
            // Expiration and renewal
            $table->timestamp('expires_at');
            $table->timestamp('renewed_at')->nullable();
            
            // Sync token for delta queries
            $table->text('sync_token')->nullable();
            
            $table->enum('status', ['active', 'expired', 'failed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_subscriptions');
    }
};

