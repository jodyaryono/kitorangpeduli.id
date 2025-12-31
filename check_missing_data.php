<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Answer;
use App\Models\Resident;
use App\Models\Response;

$response = Response::find(7);

echo "=== RESPONSE DATA ===\n";
echo "Response ID: {$response->id}\n";
echo "Status: {$response->status}\n";
echo 'Notes/Catatan: ' . ($response->notes ?? 'NULL') . "\n\n";

echo "=== ANSWERS (Questions 21-23) ===\n";
$answers = Answer::where('response_id', 7)->get();
echo 'Total answers: ' . $answers->count() . "\n\n";

foreach ($answers as $answer) {
    echo "Question ID: {$answer->question_id}\n";
    echo '  Answer Text: ' . ($answer->answer_text ?? 'NULL') . "\n";
    echo '  Selected Options: ' . ($answer->selected_options ?? 'NULL') . "\n";
    echo '  Numeric: ' . ($answer->answer_numeric ?? 'NULL') . "\n";
    echo '  Media Path: ' . ($answer->media_path ?? 'NULL') . "\n\n";
}

echo "=== RESIDENT DATA (BILLY WASOM) ===\n";
$resident = Resident::find(21);
echo "ID: {$resident->id}\n";
echo "NIK: {$resident->nik}\n";
echo "Nama: {$resident->nama_lengkap}\n";
echo 'Relationship ID (Status Keluarga): ' . ($resident->relationship_id ?? 'NULL') . "\n";
echo 'Gender ID (Jenis Kelamin): ' . ($resident->gender_id ?? 'NULL') . "\n";
echo 'Date of Birth: ' . ($resident->date_of_birth ?? 'NULL') . "\n";
echo 'Place of Birth: ' . ($resident->place_of_birth ?? 'NULL') . "\n";
