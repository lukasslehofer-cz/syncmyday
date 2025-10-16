<?php
/**
 * Blog Import Script for Production
 * Import blog articles from JSON file to database
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BlogArticle;
use App\Models\BlogCategory;

echo "📥 Importuji blog články...\n";

$file = base_path('blog-export.json');

if (!file_exists($file)) {
    echo "❌ Soubor nenalezen: {$file}\n";
    exit(1);
}

$data = json_decode(file_get_contents($file), true);

if (!$data) {
    echo "❌ Chyba při čtení JSON\n";
    exit(1);
}

// Import kategorií
$categoryCount = 0;
foreach ($data['categories'] as $catData) {
    $category = BlogCategory::updateOrCreate(
        ['slug' => $catData['slug']],
        ['sort_order' => $catData['sort_order']]
    );

    foreach ($catData['translations'] as $trans) {
        $category->translations()->updateOrCreate(
            ['locale' => $trans['locale']],
            [
                'name' => $trans['name'],
                'description' => $trans['description'] ?? null,
            ]
        );
    }
    $categoryCount++;
}

// Import článků
$articleCount = 0;
foreach ($data['articles'] as $artData) {
    $category = BlogCategory::where('slug', $artData['category_slug'])->first();
    
    if (!$category) {
        echo "⚠️  Kategorie nenalezena: {$artData['category_slug']} (přeskakuji článek: {$artData['identifier_slug']})\n";
        continue;
    }

    // Find existing article by matching CS slug in translations
    $existingArticle = BlogArticle::whereHas('translations', function($query) use ($artData) {
        $query->where('locale', 'cs')
              ->where('slug', $artData['identifier_slug']);
    })->where('category_id', $category->id)->first();

    if ($existingArticle) {
        // Update existing article
        $existingArticle->update([
            'featured_image' => $artData['featured_image'],
            'is_published' => $artData['is_published'],
            'published_at' => $artData['published_at'],
        ]);
        $article = $existingArticle;
    } else {
        // Create new article
        $article = BlogArticle::create([
            'category_id' => $category->id,
            'featured_image' => $artData['featured_image'],
            'is_published' => $artData['is_published'],
            'published_at' => $artData['published_at'],
        ]);
    }

    foreach ($artData['translations'] as $trans) {
        $article->translations()->updateOrCreate(
            ['locale' => $trans['locale']],
            [
                'slug' => $trans['slug'],
                'title' => $trans['title'],
                'excerpt' => $trans['excerpt'] ?? null,
                'content' => $trans['content'],
                'meta_title' => $trans['meta_title'] ?? null,
                'meta_description' => $trans['meta_description'] ?? null,
            ]
        );
    }
    $articleCount++;
}

echo "✅ Import dokončen!\n";
echo "   Kategorie: {$categoryCount}\n";
echo "   Články: {$articleCount}\n";

