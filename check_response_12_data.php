<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== RESPONSE #12 DATA CHECK ===\n\n";

$response = \App\Models\Response::with('resident')->find(12);
if (!$response) {
    die("Response #12 tidak ditemukan\n");
}

echo "Response ID: {$response->id}\n";
echo 'Resident ID: ' . ($response->resident_id ?? 'NULL') . "\n";
if ($response->resident) {
    echo "Resident Name: {$response->resident->nama_lengkap}\n";
    echo "Family ID: {$response->resident->family_id}\n";
}
echo "Status: {$response->status}\n\n";

// Check Answers
$answers = \App\Models\Answer::where('response_id', 12)->get();
echo "=== ANSWERS (Jawaban Pertanyaan Biasa) ===\n";
echo 'Total: ' . $answers->count() . "\n";
foreach ($answers->take(5) as $answer) {
    echo "- Question ID {$answer->question_id}: {$answer->answer}\n";
}
if ($answers->count() > 5)
    echo '... dan ' . ($answers->count() - 5) . " lainnya\n";

// Check Resident Health Responses (Section V - per person)
$healthResponses = \App\Models\ResidentHealthResponse::where('response_id', 12)->get();
echo "\n=== RESIDENT HEALTH RESPONSES (Section V - Per Orang) ===\n";
echo 'Total: ' . $healthResponses->count() . "\n";
foreach ($healthResponses->take(5) as $hr) {
    $resident = \App\Models\Resident::find($hr->resident_id);
    echo "- {$resident->nama_lengkap} ({$hr->question_code}): {$hr->answer}\n";
}
if ($healthResponses->count() > 5)
    echo '... dan ' . ($healthResponses->count() - 5) . " lainnya\n";

// Check Family Health Responses (Section VI - per family)
$familyHealthResponses = \App\Models\FamilyHealthResponse::where('response_id', 12)->get();
echo "\n=== FAMILY HEALTH RESPONSES (Section VI - Per Keluarga) ===\n";
echo 'Total: ' . $familyHealthResponses->count() . "\n";
foreach ($familyHealthResponses->take(5) as $fhr) {
    echo "- Family {$fhr->family_id} ({$fhr->question_code}): {$fhr->answer}\n";
}
if ($familyHealthResponses->count() > 5)
    echo '... dan ' . ($familyHealthResponses->count() - 5) . " lainnya\n";

// Check family members
if ($response->resident && $response->resident->family_id) {
    $familyId = $response->resident->family_id;
    $members = \App\Models\Resident::where('family_id', $familyId)->get();
    echo "\n=== ANGGOTA KELUARGA (Family ID: {$familyId}) ===\n";
    echo 'Total: ' . $members->count() . "\n";
    foreach ($members as $member) {
        echo "- ID {$member->id}: {$member->nama_lengkap} (NIK: {$member->nik})\n";
    }
}
