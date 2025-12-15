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
        'media_type',
        'media_path',
        'is_required',
        'order',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
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
