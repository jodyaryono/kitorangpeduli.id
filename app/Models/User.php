<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'opd_id',
        'is_active',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isFieldOfficer(): bool
    {
        return $this->role === 'field_officer';
    }

    public function canAccessAllOpds(): bool
    {
        return $this->isAdmin() || is_null($this->opd_id);
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, [
            'admin',
            'opd_admin',
            'viewer',
            'field_officer',
        ]);
    }

    public function isOpdAdmin(): bool
    {
        return $this->role === 'opd_admin';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }
}
