<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyResponse extends Model
{
    use SoftDeletes;

    protected $table = 'family_responses';

    public const STATUSES = [
        'in_progress' => 'Sedang Mengisi',
        'completed' => 'Selesai',
        'submitted' => 'Terkirim',
    ];

    protected $fillable = [
        'questionnaire_id',
        'family_id',
        'entered_by_user_id',
        'status',
        'last_question_id',
        'current_resident_id',
        'latitude',
        'longitude',
        'device_info',
        'ip_address',
        'started_at',
        'completed_at',
        'updated_by_user_id',
        'deleted_by_user_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by_user_id');
    }

    public function lastQuestion(): BelongsTo
    {
        return $this->belongsTo(Question::class, 'last_question_id');
    }

    public function currentResident(): BelongsTo
    {
        return $this->belongsTo(Resident::class, 'current_resident_id');
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
        return $this->hasMany(Answer::class, 'family_response_id');
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

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    // Helper methods
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }
}
