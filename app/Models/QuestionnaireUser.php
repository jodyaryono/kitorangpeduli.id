<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class QuestionnaireUser extends Model
{
    protected $table = 'questionnaire_users';

    protected $fillable = [
        'questionnaire_id',
        'user_id',
        'role',
        'assigned_by_user_id',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    // Helper methods
    public function isFieldOfficer(): bool
    {
        return in_array($this->role, ['field_officer', 'both']);
    }

    public function isSupervisor(): bool
    {
        return in_array($this->role, ['supervisor', 'both']);
    }
}
