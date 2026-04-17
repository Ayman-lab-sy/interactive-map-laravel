<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CaseModel extends Model
{
    protected $connection = 'cases';
    protected $table = 'cases';

    protected $fillable = [
        'case_number',
        'followup_token',
        'name_type',
        'full_name',
        'location',
        'phone',
        'email',
        'spouse_name',
        'children',
        'violation_type',
        'is_pattern_case',
        'case_sensitivity',
        'component',
        'direct_threat',
        'threat_description',
        'threat_source',
        'threat_locations',
        'psychological_impact',
        'impact_details',
        'agreed_to_document',
        'agreed_to_share',
        'agreed_to_campaign',
        'status',
        'birth_date',
        'threat_date',
    ];

    protected $casts = [
        'full_name' => 'encrypted',
        'spouse_name' => 'encrypted',
        'children' => 'encrypted',
        'phone' => 'encrypted',
        'email' => 'encrypted',
        'threat_description' => 'encrypted',
        'impact_details' => 'encrypted',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'birth_date' => 'date',
        'threat_date' => 'date',
    ];

    protected static function booted()
    {
        static::saving(function ($case) {
            if ($case->full_name) {
                $normalized = mb_strtolower(trim($case->full_name));
                $case->full_name_hash = hash('sha256', $normalized);
            }
        });
    }

    public function events()
    {
        return $this->hasMany(\App\Models\CaseEvent::class, 'case_id')
                    ->orderBy('created_at', 'desc');
    }
}

