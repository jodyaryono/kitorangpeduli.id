<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$questionnaires = App\Models\Questionnaire::withCount([
    'questions',
    'responses' => function ($q) {
        $q->where('status', 'completed');
    }
])->get();

echo 'Total Questionnaires: ' . $questionnaires->count() . "\n";
echo "Meeting criteria (≥10 questions AND ≥80 completed responses):\n\n";

$meetingCriteria = $questionnaires->filter(function ($q) {
    return $q->questions_count >= 10 && $q->responses_count >= 80;
});

echo 'Count: ' . $meetingCriteria->count() . ' / ' . $questionnaires->count() . "\n\n";

foreach ($questionnaires as $q) {
    $status = ($q->questions_count >= 10 && $q->responses_count >= 80) ? '✅' : '❌';
    echo "$status {$q->title}\n";
    echo "   Questions: {$q->questions_count} | Completed Responses: {$q->responses_count}\n\n";
}
