<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseFile extends Model
{
    protected $connection = 'cases';
    protected $table = 'case_files';

    protected $fillable = [
        'case_id',
        'update_id',
        'file_path',
        'original_name',
        'mime_type',
    ];
}
