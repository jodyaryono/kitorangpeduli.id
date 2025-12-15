<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Occupation extends Model
{
    protected $table = 'occupations';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'occupation',
    ];

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class);
    }
}
