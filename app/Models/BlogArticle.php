<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogArticle extends Model
{
    use HasFactory;

    protected $table = 'blog_articles';

    protected $fillable = [
        'category_id',
        'featured_image',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Get the category
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the translations for the article
     */
    public function translations()
    {
        return $this->hasMany(BlogArticleTranslation::class, 'article_id');
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
     * Get translated title
     */
    public function getTitle($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->title : '';
    }

    /**
     * Get translated slug
     */
    public function getSlug($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->slug : '';
    }

    /**
     * Get translated excerpt
     */
    public function getExcerpt($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->excerpt : '';
    }

    /**
     * Get translated content
     */
    public function getContent($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation ? $translation->content : '';
    }

    /**
     * Get translated meta title
     */
    public function getMetaTitle($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation && $translation->meta_title ? $translation->meta_title : $this->getTitle($locale);
    }

    /**
     * Get translated meta description
     */
    public function getMetaDescription($locale = null)
    {
        $translation = $this->translation($locale);
        return $translation && $translation->meta_description ? $translation->meta_description : $this->getExcerpt($locale);
    }

    /**
     * Scope to get only published articles
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }

    /**
     * Scope to order by published date
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('published_at', 'desc');
    }

    /**
     * Get the URL to the article
     */
    public function getUrl($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        $slug = $this->getSlug($locale);
        return route('blog.show', ['slug' => $slug, 'locale' => $locale]);
    }

    /**
     * Get the featured image URL or a placeholder
     */
    public function getFeaturedImageUrl()
    {
        if ($this->featured_image) {
            // If path starts with /, use as-is, otherwise prepend images/blog/
            if (str_starts_with($this->featured_image, '/')) {
                return asset(ltrim($this->featured_image, '/'));
            }
            return asset('images/blog/' . $this->featured_image);
        }
        return asset('syncmyday-logo.png'); // fallback
    }
}

