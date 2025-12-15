<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $fillable = ['code', 'name'];

    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class);
    }

    public function kartuKeluarga(): HasMany
    {
        return $this->hasMany(KartuKeluarga::class);
    }

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class);
    }
}
