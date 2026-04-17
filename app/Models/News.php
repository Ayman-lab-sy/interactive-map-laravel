<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    // ربط هذا المودل بقاعدة الأخبار
    protected $connection = 'news';

    protected $table = 'news';

    protected $fillable = [
        'title',
        'title_en',
        'date',
        'summary',
        'summary_en',
        'content',
        'content_en',
        'image',
        'slug',
        'published',
    ];

    protected static function booted()
    {
        static::creating(function ($news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title) . '-' . time();
            }
        });
    }
}
