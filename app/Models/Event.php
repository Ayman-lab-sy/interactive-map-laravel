<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'news';
    protected $table = 'events';

    protected $fillable = [
        'title', 
        'description', 
        'lat', 
        'lng', 
        'category', 
        'status', 
        'image', 
        'event_date', 
        'governorate', 
        'city', 
        'area',
        'sources_count',
        'sources_diverse',
        'video_url',
        'confidence_score',
        'confidence_level'
    ];
}
