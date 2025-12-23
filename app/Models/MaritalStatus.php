<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class MaritalStatus extends Model
{
    protected $fillable = [
        'name',
        'code',
    ];

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class, 'marital_status_id');
    }
}
