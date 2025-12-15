<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    protected $fillable = ['regency_id', 'code', 'name'];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function villages(): HasMany
    {
        return $this->hasMany(Village::class);
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
