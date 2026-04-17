<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;


class BlogPost extends Model
{
    use Translatable;
    use HasFactory;

    protected $fillable = [
        'title', 'desc', 'image', 'content'
    ];
    protected $translatable = [
        'title', 'desc', 'content'
    ];

}
