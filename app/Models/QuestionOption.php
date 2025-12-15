<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class QuestionOption extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'question_id',
        'option_text',
        'media_type',
        'media_path',
        'order',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function hasOptionMedia(): bool
    {
        return $this->media_type !== 'none';
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('option_media')
            ->singleFile();
    }
}
