<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class HealthQuestion extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'question_text',
        'question_note',
        'input_type',
        'order',
        'is_required',
        'is_active',
        'show_conditions',
        'validation_rules',
        'settings',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'show_conditions' => 'array',
        'validation_rules' => 'array',
        'settings' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(HealthQuestionCategory::class, 'category_id');
    }

    public function options(): HasMany
    {
        return $this
            ->hasMany(HealthQuestionOption::class, 'question_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function tableRows(): HasMany
    {
        return $this
            ->hasMany(HealthQuestionTableRow::class, 'question_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Check if question should be shown based on conditions
     */
    public function shouldShow($resident, $answers = []): bool
    {
        if (!$this->show_conditions) {
            return true;
        }

        $conditions = $this->show_conditions;

        // Check age
        if (isset($conditions['min_age']) && $resident->umur < $conditions['min_age']) {
            return false;
        }
        if (isset($conditions['max_age']) && $resident->umur > $conditions['max_age']) {
            return false;
        }

        // Check gender
        if (isset($conditions['gender']) && $conditions['gender'] !== 'all') {
            if ($resident->jenis_kelamin !== $conditions['gender']) {
                return false;
            }
        }

        // Check dependency on previous answer
        if (isset($conditions['depends_on']) && isset($conditions['depends_value'])) {
            $dependsCode = $conditions['depends_on'];
            $requiredValue = $conditions['depends_value'];

            if (!isset($answers[$dependsCode]) || $answers[$dependsCode] !== $requiredValue) {
                return false;
            }
        }

        return true;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
