<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends \TCG\Voyager\Models\User
{
    protected $connection = 'mysql';

    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * ✅ النظام الرسمي الموحد للأدوار
     */
    public function systemRole(): string
    {
        return config('roles')[$this->role_id] ?? 'viewer';
    }

    /**
     * 🔐 صلاحيات عامة
     */
    public function isAdmin(): bool
    {
        return $this->systemRole() === 'admin';
    }

    public function isCaseWorker(): bool
    {
        return in_array($this->systemRole(), [
            'admin',
            'case_worker',
        ]);
    }

    public function isReferralOfficer(): bool
    {
        return in_array($this->systemRole(), [
            'admin',
            'referral_officer',
        ]);
    }

    public function isExportOfficer(): bool
    {
        return in_array($this->systemRole(), [
            'admin',
            'export_officer',
        ]);
    }

    public function canRevealSensitiveFields(): bool
    {
        return in_array($this->systemRole(), [
            'admin',
            'case_worker',
        ]);
    }

    public function canReview(): bool
    {
        return in_array($this->systemRole(), [
            'admin',
            'case_worker',
        ]);
    }

    public function canExport(): bool
    {
        return in_array($this->systemRole(), [
            'admin',
            'export_officer',
        ]);
    }
}
