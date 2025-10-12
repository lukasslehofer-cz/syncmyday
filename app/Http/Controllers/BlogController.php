<?php

namespace App\Http\Controllers;

use App\Models\BlogArticle;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of blog articles
     */
    public function index()
    {
        $articles = BlogArticle::with(['category', 'translations'])
            ->published()
            ->latest()
            ->paginate(12);

        $categories = BlogCategory::with('translations')
            ->ordered()
            ->get();

        return view('blog.index', compact('articles', 'categories'));
    }

    /**
     * Display articles in a specific category
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        
        $articles = $category->publishedArticles()
            ->with('translations')
            ->paginate(12);

        $categories = BlogCategory::with('translations')
            ->ordered()
            ->get();

        return view('blog.category', compact('category', 'articles', 'categories'));
    }

    /**
     * Display a specific blog article
     */
    public function show($slug)
    {
        $article = BlogArticle::where('slug', $slug)
            ->with(['category', 'translations'])
            ->published()
            ->firstOrFail();

        $categories = BlogCategory::with('translations')
            ->ordered()
            ->get();

        // Get related articles from the same category
        $relatedArticles = BlogArticle::where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->published()
            ->latest()
            ->limit(3)
            ->get();

        return view('blog.show', compact('article', 'categories', 'relatedArticles'));
    }
}

