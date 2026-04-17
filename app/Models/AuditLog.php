<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $connection = 'cases';
    protected $table = 'audit_logs';

    public $timestamps = false;

    protected $fillable = [
        'case_id',
        'user_id',
        'user_role',
        'action_type',
        'action_context',
        'previous_value',
        'new_value',
        'reason',
        'created_at',
    ];

    protected $casts = [
        'action_context' => 'array',
    ];

    public function case()
    {
        return $this->belongsTo(CaseModel::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
