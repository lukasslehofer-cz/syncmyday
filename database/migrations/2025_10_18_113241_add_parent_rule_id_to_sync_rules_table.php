<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add parent_rule_id to support hierarchical sync rules.
     * This allows reverse rules to be linked to their main rule,
     * enabling UI to show only main rules while keeping reverse rules in background.
     */
    public function up(): void
    {
        Schema::table('sync_rules', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_rule_id')->nullable()->after('user_id');
            $table->foreign('parent_rule_id')
                ->references('id')
                ->on('sync_rules')
                ->onDelete('cascade'); // When parent is deleted, delete children too
            
            $table->index('parent_rule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sync_rules', function (Blueprint $table) {
            $table->dropForeign(['parent_rule_id']);
            $table->dropIndex(['parent_rule_id']);
            $table->dropColumn('parent_rule_id');
        });
    }
};
