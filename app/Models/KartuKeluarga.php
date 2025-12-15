<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class KartuKeluarga extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'kartu_keluarga';

    public const VERIFICATION_STATUSES = [
        'pending' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];

    protected $fillable = [
        'no_kk',
        'kepala_keluarga',
        'alamat',
        'rt',
        'rw',
        'kode_pos',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'kk_image_path',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'verification_notes',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Relasi Wilayah
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    // Relasi Verifikasi
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Anggota Keluarga
    public function anggota(): HasMany
    {
        return $this->hasMany(Respondent::class, 'kartu_keluarga_id');
    }

    // Alias for respondents
    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class, 'kartu_keluarga_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->alamat,
            $this->rt ? "RT {$this->rt}" : null,
            $this->rw ? "RW {$this->rw}" : null,
            $this->village?->name,
            $this->district?->name,
            $this->regency?->name,
            $this->province?->name,
            $this->kode_pos,
        ]);

        return implode(', ', $parts);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('kk_image')
            ->singleFile();
    }
}
