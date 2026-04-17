<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;


class SitePage extends Model
{
    use Translatable;


    protected $fillable = [
        'title', 'slug', 'image', 'content'
    ];
    protected $translatable = [
        'title', 'content'
    ];
}
