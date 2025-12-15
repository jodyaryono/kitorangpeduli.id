<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    public const STATUSES = [
        'in_progress' => 'Sedang Mengisi',
        'completed' => 'Selesai',
        'submitted' => 'Terkirim',
    ];

    protected $fillable = [
        'questionnaire_id',
        'respondent_id',
        'status',
        'is_valid',
        'validation_notes',
        'last_question_id',
        'progress_percentage',
        'latitude',
        'longitude',
        'device_info',
        'ip_address',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_valid' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(Respondent::class);
    }

    public function lastQuestion(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'last_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    // Scopes
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper Methods
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function calculateProgress(): float
    {
        $totalQuestions = $this->questionnaire->questions()->count();

        if ($totalQuestions === 0) {
            return 100;
        }

        $answeredQuestions = $this->answers()->count();

        return round(($answeredQuestions / $totalQuestions) * 100, 2);
    }

    public function updateProgress(): void
    {
        $this->update([
            'progress_percentage' => $this->calculateProgress(),
        ]);
    }

    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'progress_percentage' => 100,
            'completed_at' => now(),
        ]);
    }
}
