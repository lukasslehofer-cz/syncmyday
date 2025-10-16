<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogArticle;
use App\Models\BlogArticleTranslation;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogAdminController extends Controller
{
    /**
     * Display a listing of all articles
     */
    public function index()
    {
        $articles = BlogArticle::with(['category', 'translations'])
            ->latest('created_at')
            ->paginate(20);

        return view('admin.blog.index', compact('articles'));
    }

    /**
     * Show the form for creating a new article
     */
    public function create()
    {
        $categories = BlogCategory::with('translations')->ordered()->get();
        $locales = ['cs', 'de', 'en', 'pl', 'sk'];

        return view('admin.blog.create', compact('categories', 'locales'));
    }

    /**
     * Store a newly created article
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:blog_categories,id',
            'is_published' => 'boolean',
            'featured_image' => 'nullable|string',
        ]);

        // Validate slugs for uniqueness per locale
        $locales = ['cs', 'de', 'en', 'pl', 'sk'];
        foreach ($locales as $locale) {
            if ($request->has("slug_{$locale}") && $request->input("slug_{$locale}")) {
                $request->validate([
                    "slug_{$locale}" => 'unique:blog_article_translations,slug,NULL,id,locale,' . $locale,
                ]);
            }
        }

        // Set published_at if article is being published
        if ($request->is_published) {
            $validated['published_at'] = now();
        }

        $article = BlogArticle::create($validated);

        // Save translations
        foreach ($locales as $locale) {
            if ($request->has("title_{$locale}") && $request->input("title_{$locale}")) {
                BlogArticleTranslation::create([
                    'article_id' => $article->id,
                    'locale' => $locale,
                    'slug' => $request->input("slug_{$locale}"),
                    'title' => $request->input("title_{$locale}"),
                    'excerpt' => $request->input("excerpt_{$locale}"),
                    'content' => $request->input("content_{$locale}"),
                    'meta_title' => $request->input("meta_title_{$locale}"),
                    'meta_description' => $request->input("meta_description_{$locale}"),
                ]);
            }
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Článek byl úspěšně vytvořen!');
    }

    /**
     * Show the form for editing an article
     */
    public function edit($id)
    {
        $article = BlogArticle::with(['category', 'translations'])->findOrFail($id);
        $categories = BlogCategory::with('translations')->ordered()->get();
        $locales = ['cs', 'de', 'en', 'pl', 'sk'];

        return view('admin.blog.edit', compact('article', 'categories', 'locales'));
    }

    /**
     * Update the specified article
     */
    public function update(Request $request, $id)
    {
        $article = BlogArticle::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:blog_categories,id',
            'is_published' => 'boolean',
            'featured_image' => 'nullable|string',
        ]);

        // Validate slugs for uniqueness per locale (excluding current article's translations)
        $locales = ['cs', 'de', 'en', 'pl', 'sk'];
        foreach ($locales as $locale) {
            if ($request->has("slug_{$locale}") && $request->input("slug_{$locale}")) {
                $existingTranslation = $article->translations()->where('locale', $locale)->first();
                $ignoreId = $existingTranslation ? $existingTranslation->id : 'NULL';
                
                $request->validate([
                    "slug_{$locale}" => 'unique:blog_article_translations,slug,' . $ignoreId . ',id,locale,' . $locale,
                ]);
            }
        }

        // Set published_at if article is being published for the first time
        if ($request->is_published && !$article->published_at) {
            $validated['published_at'] = now();
        } elseif (!$request->is_published) {
            $validated['published_at'] = null;
        }

        $article->update($validated);

        // Update translations
        foreach ($locales as $locale) {
            if ($request->has("title_{$locale}") && $request->input("title_{$locale}")) {
                BlogArticleTranslation::updateOrCreate(
                    [
                        'article_id' => $article->id,
                        'locale' => $locale,
                    ],
                    [
                        'slug' => $request->input("slug_{$locale}"),
                        'title' => $request->input("title_{$locale}"),
                        'excerpt' => $request->input("excerpt_{$locale}"),
                        'content' => $request->input("content_{$locale}"),
                        'meta_title' => $request->input("meta_title_{$locale}"),
                        'meta_description' => $request->input("meta_description_{$locale}"),
                    ]
                );
            }
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Článek byl úspěšně aktualizován!');
    }

    /**
     * Remove the specified article
     */
    public function destroy($id)
    {
        $article = BlogArticle::findOrFail($id);
        $article->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'Článek byl smazán!');
    }

    /**
     * Toggle article published status
     */
    public function togglePublish($id)
    {
        $article = BlogArticle::findOrFail($id);
        
        $article->is_published = !$article->is_published;
        
        if ($article->is_published && !$article->published_at) {
            $article->published_at = now();
        }
        
        $article->save();

        return back()->with('success', 'Stav publikace byl změněn!');
    }
}

