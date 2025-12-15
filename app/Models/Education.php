<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Education extends Model
{
    protected $table = 'educations';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'education',
    ];

    public function respondents(): HasMany
    {
        return $this->hasMany(Respondent::class);
    }
}
