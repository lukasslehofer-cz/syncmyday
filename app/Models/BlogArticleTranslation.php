<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogArticleTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_article_translations';

    protected $fillable = [
        'article_id',
        'locale',
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
    ];

    /**
     * Get the article that owns the translation
     */
    public function article()
    {
        return $this->belongsTo(BlogArticle::class, 'article_id');
    }
}

