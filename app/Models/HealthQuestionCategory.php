<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class HealthQuestionCategory extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'order',
        'is_active',
        'target_criteria',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'target_criteria' => 'array',
    ];

    public function questions(): HasMany
    {
        return $this
            ->hasMany(HealthQuestion::class, 'category_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function appliesToResident($resident): bool
    {
        if (!$this->target_criteria) {
            return true;
        }

        $criteria = $this->target_criteria;

        if (isset($criteria['min_age']) && $resident->umur < $criteria['min_age']) {
            return false;
        }
        if (isset($criteria['max_age']) && $resident->umur > $criteria['max_age']) {
            return false;
        }

        if (isset($criteria['gender']) && $criteria['gender'] !== 'all') {
            if ($resident->jenis_kelamin !== $criteria['gender']) {
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
