<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyHealthResponse extends Model
{
    use HasFactory;

    protected $table = 'family_health_responses';

    protected $fillable = [
        'family_id',
        'response_id',
        'question_code',
        'answer',
    ];

    /**
     * Get the family that owns this health response.
     */
    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    /**
     * Get the questionnaire response that this health response belongs to.
     */
    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    /**
     * Get the health question for this response.
     */
    public function healthQuestion()
    {
        return $this->belongsTo(HealthQuestion::class, 'question_code', 'code');
    }

    /**
     * Get all answers for a specific family and response
     */
    public static function getAnswersForFamily($familyId, $responseId)
    {
        return self::where('family_id', $familyId)
            ->where('response_id', $responseId)
            ->pluck('answer', 'question_code')
            ->map(function ($answer) {
                // Try to decode JSON, return as-is if not JSON
                $decoded = json_decode($answer, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $answer;
            })
            ->toArray();
    }
}
