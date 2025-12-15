<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Questionnaire extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'opd_id',
        'title',
        'description',
        'cover_image_path',
        'cover_video_path',
        'start_date',
        'end_date',
        'is_active',
        'requires_location',
        'requires_verified_respondent',
        'max_responses',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'requires_location' => 'boolean',
        'requires_verified_respondent' => 'boolean',
    ];

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query
            ->active()
            ->where(function ($q) {
                $q
                    ->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    // Helper Methods
    public function isAvailable(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->start_date && $this->start_date > now()) {
            return false;
        }

        if ($this->end_date && $this->end_date < now()) {
            return false;
        }

        if ($this->max_responses && $this->responses()->completed()->count() >= $this->max_responses) {
            return false;
        }

        return true;
    }

    public function getCompletedResponsesCountAttribute(): int
    {
        return $this->responses()->where('status', 'completed')->count();
    }

    public function getInProgressResponsesCountAttribute(): int
    {
        return $this->responses()->where('status', 'in_progress')->count();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('cover_image')
            ->singleFile();

        $this
            ->addMediaCollection('cover_video')
            ->singleFile();
    }
}
