<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class CitizenType extends Model
{
    protected $fillable = ['code', 'name', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
