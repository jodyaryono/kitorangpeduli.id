<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['name'];

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class);
    }

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }
}
