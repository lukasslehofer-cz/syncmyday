<?php

namespace App\Console\Commands;

use App\Models\BlogArticle;
use App\Models\BlogCategory;
use Illuminate\Console\Command;

class BlogImport extends Command
{
    protected $signature = 'blog:import {--file=storage/blog-export.json}';
    protected $description = 'Import blog articles and categories from JSON';

    public function handle()
    {
        $file = base_path($this->option('file'));

        if (!file_exists($file)) {
            $this->error("❌ Soubor nenalezen: {$file}");
            return 1;
        }

        $this->info('📥 Importuji blog články...');

        $data = json_decode(file_get_contents($file), true);

        // Import kategorií
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
                        'description' => $trans['description'],
                    ]
                );
            }
        }

        // Import článků
        foreach ($data['articles'] as $artData) {
            $category = BlogCategory::where('slug', $artData['category_slug'])->first();
            
            if (!$category) {
                $this->warn("⚠️  Kategorie nenalezena: {$artData['category_slug']} (přeskakuji článek)");
                continue;
            }

            $article = BlogArticle::updateOrCreate(
                ['slug' => $artData['slug']],
                [
                    'category_id' => $category->id,
                    'featured_image' => $artData['featured_image'],
                    'is_published' => $artData['is_published'],
                    'published_at' => $artData['published_at'],
                ]
            );

            foreach ($artData['translations'] as $trans) {
                $article->translations()->updateOrCreate(
                    ['locale' => $trans['locale']],
                    [
                        'title' => $trans['title'],
                        'excerpt' => $trans['excerpt'],
                        'content' => $trans['content'],
                        'meta_title' => $trans['meta_title'],
                        'meta_description' => $trans['meta_description'],
                    ]
                );
            }
        }

        $this->info("✅ Import dokončen!");
        $this->info("   Kategorie: " . count($data['categories']));
        $this->info("   Články: " . count($data['articles']));

        return 0;
    }
}

