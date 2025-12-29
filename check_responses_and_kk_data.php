<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking responses and answers\n";
echo str_repeat('=', 80) . "\n\n";

$totalResponses = App\Models\Response::count();
echo "Total responses in database: {$totalResponses}\n\n";

$responses = App\Models\Response::with('resident')
    ->latest()
    ->limit(3)
    ->get();

if ($responses->count() > 0) {
    echo "Latest responses:\n";
    echo str_repeat('-', 80) . "\n";

    foreach ($responses as $response) {
        echo "Response ID: {$response->id}\n";
        echo "Questionnaire ID: {$response->questionnaire_id}\n";
        echo "Status: {$response->status}\n";
        echo 'Resident: ' . ($response->resident ? $response->resident->nama_lengkap : 'N/A') . " (ID: {$response->resident_id})\n";
        echo "Started: {$response->started_at}\n";

        // Check if this response has KK-related answers
        $kkAnswers = App\Models\Answer::where('response_id', $response->id)
            ->whereIn('question_id', [214, 215, 216, 217, 219, 220, 223, 225, 266, 269])
            ->get();

        echo "KK-related answers: {$kkAnswers->count()}\n";
        foreach ($kkAnswers as $answer) {
            $questionIds = [
                214 => 'Provinsi',
                215 => 'Kabupaten',
                216 => 'Kecamatan',
                217 => 'Desa/Kel',
                219 => 'RW',
                220 => 'RT',
                223 => 'No KK',
                225 => 'Alamat',
                266 => 'Upload KK',
                269 => 'Kepala Keluarga',
            ];

            $label = $questionIds[$answer->question_id] ?? "Q{$answer->question_id}";
            $value = $answer->media_path ?? $answer->answer_text ?? $answer->answer_numeric ?? '-';
            echo "  - {$label}: {$value}\n";
        }

        echo str_repeat('-', 80) . "\n\n";
    }
} else {
    echo "No responses found.\n";
}

echo "\nDone!\n";
