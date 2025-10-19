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
        Schema::create('fakturoid_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('fakturoid_id')->nullable(); // ID from Fakturoid API
            $table->string('fakturoid_number')->nullable(); // Invoice number (e.g. SMD-2025-001)
            $table->string('stripe_invoice_id')->nullable(); // Reference to Stripe invoice
            $table->decimal('amount', 10, 2); // Invoice amount
            $table->string('currency', 3); // CZK, EUR, PLN, etc.
            $table->string('language', 2); // cz, en, de, pl, sk
            $table->string('description')->nullable(); // Invoice description
            $table->timestamp('issued_at')->nullable(); // When invoice was issued
            $table->string('pdf_url')->nullable(); // Cached PDF URL (optional)
            $table->enum('status', ['pending', 'created', 'failed'])->default('pending'); // Processing status
            $table->text('error_message')->nullable(); // Error message if creation failed
            $table->integer('retry_count')->default(0); // Number of retry attempts
            $table->timestamps();
            
            // Indexes
            $table->index('user_id');
            $table->index('fakturoid_id');
            $table->index('stripe_invoice_id');
            $table->index('status');
            $table->index('issued_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fakturoid_invoices');
    }
};
