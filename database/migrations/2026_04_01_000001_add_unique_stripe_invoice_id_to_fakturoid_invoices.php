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
        Schema::table('fakturoid_invoices', function (Blueprint $table) {
            // Drop existing non-unique index
            $table->dropIndex(['stripe_invoice_id']);
            // Add unique index to prevent duplicate invoices from Stripe webhook retries
            $table->unique('stripe_invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fakturoid_invoices', function (Blueprint $table) {
            $table->dropUnique(['stripe_invoice_id']);
            $table->index('stripe_invoice_id');
        });
    }
};
