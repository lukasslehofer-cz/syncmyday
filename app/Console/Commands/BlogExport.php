<?php

namespace App\Console\Commands;

use App\Models\BlogArticle;
use App\Models\BlogCategory;
use Illuminate\Console\Command;

class BlogExport extends Command
{
    protected $signature = 'blog:export {--file=storage/blog-export.json}';
    protected $description = 'Export blog articles and categories to JSON';

    public function handle()
    {
        $this->info('📤 Exportuji blog články...');

        $data = [
            'categories' => [],
            'articles' => [],
        ];

        // Export kategorií
        foreach (BlogCategory::with('translations')->get() as $category) {
            $data['categories'][] = [
                'slug' => $category->slug,
                'sort_order' => $category->sort_order,
                'translations' => $category->translations->map(fn($t) => [
                    'locale' => $t->locale,
                    'name' => $t->name,
                    'description' => $t->description,
                ])->toArray(),
            ];
        }

        // Export článků
        foreach (BlogArticle::with('translations')->get() as $article) {
            // Use CS slug as the main identifier for matching during import
            $csTranslation = $article->translations->where('locale', 'cs')->first();
            
            $data['articles'][] = [
                'identifier_slug' => $csTranslation?->slug ?? 'unknown-' . $article->id,
                'category_slug' => $article->category->slug,
                'featured_image' => $article->featured_image,
                'is_published' => $article->is_published,
                'published_at' => $article->published_at?->toDateTimeString(),
                'translations' => $article->translations->map(fn($t) => [
                    'locale' => $t->locale,
                    'slug' => $t->slug,
                    'title' => $t->title,
                    'excerpt' => $t->excerpt,
                    'content' => $t->content,
                    'meta_title' => $t->meta_title,
                    'meta_description' => $t->meta_description,
                ])->toArray(),
            ];
        }

        $file = base_path($this->option('file'));
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->info("✅ Export dokončen: {$file}");
        $this->info("   Kategorie: " . count($data['categories']));
        $this->info("   Články: " . count($data['articles']));

        return 0;
    }
}

