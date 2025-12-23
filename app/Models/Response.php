<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Response extends Model
{
    use SoftDeletes;
    public const STATUSES = [
        'in_progress' => 'Sedang Mengisi',
        'completed' => 'Selesai',
        'submitted' => 'Terkirim',
    ];

    protected $fillable = [
        'questionnaire_id',
        'resident_id',
        'entered_by_user_id',
        'status',
        'is_valid',
        'validation_notes',
        'last_question_id',
        'progress_percentage',
        'latitude',
        'longitude',
        'device_info',
        'ip_address',
        'family_members',
        'health_data',
        'officer_notes',
        'started_at',
        'completed_at',
        'updated_by_user_id',
        'deleted_by_user_id',
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_valid' => 'boolean',
        'family_members' => 'array',
        'health_data' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'resident_id');
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by_user_id');
    }

    public function lastQuestion(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'last_question_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_user_id');
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

    public function isOfficerAssisted(): bool
    {
        return !is_null($this->entered_by_user_id);
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
