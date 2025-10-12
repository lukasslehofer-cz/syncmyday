<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogCategoryTranslation extends Model
{
    use HasFactory;

    protected $table = 'blog_category_translations';

    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'description',
    ];

    /**
     * Get the category that owns the translation
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }
}

