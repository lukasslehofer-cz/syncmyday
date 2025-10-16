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
        // Add slug to blog_article_translations
        Schema::table('blog_article_translations', function (Blueprint $table) {
            $table->string('slug')->after('locale');
            $table->unique(['locale', 'slug']);
        });

        // Remove slug from blog_articles
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->dropUnique(['slug']); // Drop unique constraint first
            $table->dropColumn('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add slug back to blog_articles
        Schema::table('blog_articles', function (Blueprint $table) {
            $table->string('slug')->unique();
        });

        // Remove slug from blog_article_translations
        Schema::table('blog_article_translations', function (Blueprint $table) {
            $table->dropUnique(['locale', 'slug']);
            $table->dropColumn('slug');
        });
    }
};
