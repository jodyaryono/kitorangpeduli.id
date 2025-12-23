<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Puskesmas extends Model
{
    protected $table = 'puskesmas';

    protected $fillable = [
        'code',
        'name',
        'regency_id',
        'address',
        'phone',
    ];

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }
}
