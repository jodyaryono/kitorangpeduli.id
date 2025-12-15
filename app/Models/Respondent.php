<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Respondent extends Model implements HasMedia
{
    use HasApiTokens, InteractsWithMedia;

    public const VERIFICATION_STATUSES = [
        'pending' => 'Menunggu Verifikasi',
        'verified' => 'Terverifikasi',
        'rejected' => 'Ditolak',
    ];

    public const GENDERS = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];

    public const BLOOD_TYPES = [
        'A' => 'A',
        'B' => 'B',
        'AB' => 'AB',
        'O' => 'O',
        '-' => 'Tidak Diketahui',
    ];

    public const RELIGIONS = [
        'Islam' => 'Islam',
        'Kristen' => 'Kristen',
        'Katolik' => 'Katolik',
        'Hindu' => 'Hindu',
        'Buddha' => 'Buddha',
        'Konghucu' => 'Konghucu',
        'Lainnya' => 'Lainnya',
    ];

    public const MARITAL_STATUSES = [
        'Belum Kawin' => 'Belum Kawin',
        'Kawin' => 'Kawin',
        'Cerai Hidup' => 'Cerai Hidup',
        'Cerai Mati' => 'Cerai Mati',
    ];

    public const FAMILY_RELATIONS = [
        'Kepala Keluarga' => 'Kepala Keluarga',
        'Istri' => 'Istri',
        'Anak' => 'Anak',
        'Menantu' => 'Menantu',
        'Cucu' => 'Cucu',
        'Orang Tua' => 'Orang Tua',
        'Mertua' => 'Mertua',
        'Famili Lain' => 'Famili Lain',
        'Pembantu' => 'Pembantu',
        'Lainnya' => 'Lainnya',
    ];

    public const EDUCATIONS = [
        'Tidak/Belum Sekolah' => 'Tidak/Belum Sekolah',
        'SD/Sederajat' => 'SD/Sederajat',
        'SMP/Sederajat' => 'SMP/Sederajat',
        'SMA/Sederajat' => 'SMA/Sederajat',
        'D1/D2/D3' => 'D1/D2/D3',
        'S1/D4' => 'S1/D4',
        'S2' => 'S2',
        'S3' => 'S3',
    ];

    public const NATIONALITIES = [
        'WNI' => 'Warga Negara Indonesia',
        'WNA' => 'Warga Negara Asing',
    ];

    protected $fillable = [
        'kartu_keluarga_id',
        'citizen_type_id',
        'status_hubungan',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'golongan_darah',
        'agama',
        'status_perkawinan',
        'pekerjaan',
        'pendidikan',
        'occupation_id',
        'education_id',
        'kewarganegaraan',
        'alamat',
        'rt',
        'rw',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'latitude',
        'longitude',
        'phone',
        'email',
        'otp_code',
        'otp_expires_at',
        'phone_verified_at',
        'ktp_image_path',
        'selfie_ktp_path',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'verification_notes',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'otp_expires_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    protected $hidden = [
        'otp_code',
    ];

    // Relasi KK
    public function kartuKeluarga(): BelongsTo
    {
        return $this->belongsTo(KartuKeluarga::class, 'kartu_keluarga_id');
    }

    // Relasi Jenis Warga
    public function citizenType(): BelongsTo
    {
        return $this->belongsTo(CitizenType::class);
    }

    // Relasi Wilayah
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id')
            ->where(function($query) {
                // Cast both sides to ensure compatibility
            });
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    // Relasi Occupation & Education
    public function occupation(): BelongsTo
    {
        return $this->belongsTo(Occupation::class);
    }

    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class);
    }

    // Relasi Verifikasi
    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Alias untuk form Filament
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Relasi Responses
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
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

    public function scopePhoneVerified($query)
    {
        return $query->whereNotNull('phone_verified_at');
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

    public function isPhoneVerified(): bool
    {
        return $this->phone_verified_at !== null;
    }

    public function canAnswerQuestionnaire(): bool
    {
        return $this->isVerified() && $this->isPhoneVerified();
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
        ]);

        return implode(', ', $parts);
    }

    public function getAgeAttribute(): ?int
    {
        return $this->tanggal_lahir?->age;
    }

    public function generateOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);
        return $otp;
    }

    public function verifyOtp(string $otp): bool
    {
        if ($this->otp_code !== $otp) {
            return false;
        }

        if ($this->otp_expires_at < now()) {
            return false;
        }

        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
            'phone_verified_at' => now(),
        ]);

        return true;
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('ktp_image')
            ->useDisk('public')
            ->singleFile();
    }
}
