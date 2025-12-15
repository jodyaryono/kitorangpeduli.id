<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
    protected $fillable = ['district_id', 'code', 'name'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
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
