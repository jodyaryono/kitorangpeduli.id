<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Question extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'questionnaire_id',
        'question_text',
        'question_type',
        'is_section',
        'parent_section_id',
        'media_type',
        'media_path',
        'is_required',
        'is_repeatable',
        'applies_to',
        'order',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_repeatable' => 'boolean',
        'is_section' => 'boolean',
        'settings' => 'array',
    ];

    public const TYPES = [
        'text' => 'Teks Singkat',
        'textarea' => 'Teks Panjang',
        'single_choice' => 'Pilihan Tunggal',
        'multiple_choice' => 'Pilihan Ganda',
        'dropdown' => 'Dropdown',
        'scale' => 'Skala',
        'date' => 'Tanggal',
        'file' => 'Upload File',
        'image' => 'Upload Gambar',
        'video' => 'Upload Video',
        'location' => 'Lokasi GPS',
        'province' => '🗺️ Provinsi',
        'regency' => '🏙️ Kabupaten/Kota',
        'district' => '🏘️ Kecamatan',
        'village' => '🏠 Kelurahan/Desa',
        'puskesmas' => '🏥 Puskesmas (Lookup)',
        'field_officer' => '👮 Petugas Lapangan (Lookup)',
        'lookup' => '🔍 Lookup (Creatable)',
        'family_members' => '👨‍👩‍👧‍👦 Anggota Keluarga (Table)',
        'health_per_member' => '🩺 Pertanyaan Kesehatan Per Anggota',
    ];

    public const MEDIA_TYPES = [
        'none' => 'Tanpa Media',
        'image' => 'Gambar',
        'video' => 'Video',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function parentSection(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'parent_section_id');
    }

    public function childQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'parent_section_id')->orderBy('order');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function hasOptions(): bool
    {
        return in_array($this->question_type, ['single_choice', 'multiple_choice', 'dropdown']);
    }

    public function isWilayahType(): bool
    {
        return in_array($this->question_type, ['province', 'regency', 'district', 'village']);
    }

    public function isLookupType(): bool
    {
        return in_array($this->question_type, ['puskesmas', 'field_officer', 'lookup']);
    }

    public function hasQuestionMedia(): bool
    {
        return $this->media_type !== 'none';
    }

    public function getTypeNameAttribute(): string
    {
        return self::TYPES[$this->question_type] ?? $this->question_type;
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('question_media')
            ->singleFile();
    }
}
