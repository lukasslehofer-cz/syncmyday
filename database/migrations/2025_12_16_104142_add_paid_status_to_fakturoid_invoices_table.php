<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add 'paid' status to the ENUM column to track invoices that have been paid in Fakturoid.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE fakturoid_invoices MODIFY COLUMN status ENUM('pending', 'created', 'paid', 'failed') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First update any 'paid' records to 'created' to avoid data loss
        DB::statement("UPDATE fakturoid_invoices SET status = 'created' WHERE status = 'paid'");
        DB::statement("ALTER TABLE fakturoid_invoices MODIFY COLUMN status ENUM('pending', 'created', 'failed') DEFAULT 'pending'");
    }
};
