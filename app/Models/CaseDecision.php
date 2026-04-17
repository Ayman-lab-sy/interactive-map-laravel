<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseDecision extends Model
{
    protected $connection = 'cases';
    protected $table = 'case_decisions';

    public $timestamps = false;

    protected $fillable = [
        'case_id',
        'legal_violation_type',
        'decision_type',
        'decision_notes',
        'decision_payload',
        'decision_priority',
        'assistant_version',
        'decided_by',
        'requires_committee',
        'decided_at',
    ];

    protected $casts = [
        'decision_payload' => 'array',
        'decision_inputs'  => 'array',
        'decided_at' => 'datetime',
    ];
}
