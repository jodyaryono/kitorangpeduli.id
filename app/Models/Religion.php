<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class, 'religion_id');
    }
}
