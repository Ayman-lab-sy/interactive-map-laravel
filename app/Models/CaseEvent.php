<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseEvent extends Model
{
    protected $connection = 'cases'; // 👈 مهم جداً لأنه نفس قاعدة الحالات

    protected $table = 'case_events';

    protected $fillable = [
        'case_id',
        'user_id',
        'event_type',
        'status_before',
        'status_after',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public $timestamps = true;

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
