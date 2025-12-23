<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;

echo "=== FIXING FIELD_OFFICER & DATE FIELDS ===\n\n";

$questionnaireId = 8;

// 1. Update Nama Pencacah to have default value settings
echo "1. Updating 'Nama Pencacah' field...\n";
$namaPencacah = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'Nama Pencacah')
    ->first();

if ($namaPencacah) {
    $settings = $namaPencacah->settings ?? [];
    $settings['auto_fill'] = true;
    $settings['readonly'] = true;
    $settings['default_value'] = 'current_user';
    $namaPencacah->settings = $settings;
    $namaPencacah->save();
    echo "   ✓ Updated: auto-fill dengan current logged-in user\n";
} else {
    echo "   ✗ Not found\n";
}

// 2. Update Tanggal Pendataan to have default today
echo "\n2. Updating 'Tanggal Pendataan' field...\n";
$tanggalPendataan = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'Tanggal Pendataan')
    ->first();

if ($tanggalPendataan) {
    $settings = $tanggalPendataan->settings ?? [];
    $settings['default_value'] = 'today';
    $settings['readonly'] = false;  // Allow editing if needed
    $tanggalPendataan->settings = $settings;
    $tanggalPendataan->save();
    echo "   ✓ Updated: default value today\n";
} else {
    echo "   ✗ Not found\n";
}

// 3. Check and clear any existing answer for Nama Kepala Keluarga if no family members
echo "\n3. Checking 'Nama Kepala Keluarga' answers...\n";
$namaKepala = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'Nama Kepala Keluarga')
    ->first();

if ($namaKepala) {
    // Get all answers for this question
    $answers = DB::table('answers')
        ->where('question_id', $namaKepala->id)
        ->get();

    echo '   Found ' . $answers->count() . " answer(s)\n";

    foreach ($answers as $answer) {
        // Check if response has family members
        $response = DB::table('responses')->where('id', $answer->response_id)->first();

        if ($response) {
            $familyMembers = json_decode($response->family_members ?? '[]', true);

            if (empty($familyMembers) && !empty($answer->answer_text)) {
                echo "   Response #{$response->id}: Has answer '{$answer->answer_text}' but no family members - clearing...\n";
                DB::table('answers')->where('id', $answer->id)->update(['answer_text' => null]);
            } else {
                echo "   Response #{$response->id}: " . count($familyMembers) . " family member(s), answer: '{$answer->answer_text}'\n";
            }
        }
    }
} else {
    echo "   ✗ Nama Kepala Keluarga not found\n";
}

echo "\n✓ Done!\n";
echo "\nChanges:\n";
echo "- Nama Pencacah: Auto-fill dengan user yang login\n";
echo "- Tanggal Pendataan: Default ke hari ini\n";
echo "- Nama Kepala Keluarga: Cleared jika belum ada anggota keluarga\n";
