<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'opd_id',
        'questionnaire_id',
        'title',
        'user_prompt',
        'ai_response',
        'raw_data',
        'chart_data',
        'map_data',
        'input_type',
        'voice_transcript',
        'api_tokens_used',
        'api_cost',
        'status',
        'error_message',
    ];

    protected $casts = [
        'raw_data' => 'json',
        'chart_data' => 'json',
        'map_data' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opd(): BelongsTo
    {
        return $this->belongsTo(Opd::class);
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
