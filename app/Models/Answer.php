<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Answer extends Model implements HasMedia
{
    use InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'response_id',
        'family_response_id',
        'resident_id',
        'question_id',
        'answer_text',
        'selected_option_id',
        'selected_options',
        'media_path',
        'latitude',
        'longitude',
        'answered_at',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'answered_at' => 'datetime',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(Response::class);
    }

    public function familyResponse(): BelongsTo
    {
        return $this->belongsTo(FamilyResponse::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    public function getDisplayAnswerAttribute(): string
    {
        $question = $this->question;

        switch ($question->question_type) {
            case 'single_choice':
            case 'dropdown':
                return $this->selectedOption?->option_text ?? '-';

            case 'multiple_choice':
                if (!$this->selected_options) {
                    return '-';
                }
                $options = QuestionOption::whereIn('id', $this->selected_options)->pluck('option_text');
                return $options->implode(', ');

            case 'location':
                if ($this->latitude && $this->longitude) {
                    return "{$this->latitude}, {$this->longitude}";
                }
                return '-';

            case 'file':
            case 'image':
            case 'video':
                return $this->media_path ? 'File uploaded' : '-';

            default:
                return $this->answer_text ?? '-';
        }
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('answer_media')
            ->singleFile();
    }
}
