<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use HasFactory;

    protected $table = 'blog_categories';

    protected $fillable = [
        'slug',
        'sort_order',
    ];

    /**
     * Get the translations for the category
     */
    public function translations()
    {
        return $this->hasMany(BlogCategoryTranslation::class, 'category_id');
    }

    /**
     * Get the translation for a specific locale
     */
    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }

    /**
     * Get translated name
     */
    public function getName($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->name : $this->slug;
    }

    /**
     * Get translated description
     */
    public function getDescription($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->description : '';
    }

    /**
     * Get articles in this category
     */
    public function articles()
    {
        return $this->hasMany(BlogArticle::class, 'category_id');
    }

    /**
     * Get published articles in this category
     */
    public function publishedArticles()
    {
        return $this->articles()->where('is_published', true)->orderBy('published_at', 'desc');
    }

    /**
     * Scope to order by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}

