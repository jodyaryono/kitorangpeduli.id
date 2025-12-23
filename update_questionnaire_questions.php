<?php

/**
 * Script to update questionnaire #8:
 * 1. Remove "Nama Kepala Keluarga" question
 * 2. Add "Upload Kartu Keluarga (KK)" question
 *
 * Run: php update_questionnaire_questions.php
 */
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;

echo "🔄 Updating Questionnaire #8 Questions...\n\n";

// Find and remove "Nama Kepala Keluarga" question
$namaKepalaKeluargaQuestion = Question::where('question_text', 'Nama Kepala Keluarga')
    ->where('questionnaire_id', 8)
    ->first();

if ($namaKepalaKeluargaQuestion) {
    echo "❌ Removing: {$namaKepalaKeluargaQuestion->question_text} (ID: {$namaKepalaKeluargaQuestion->id})\n";

    // Delete associated answers first
    $answersDeleted = \App\Models\Answer::where('question_id', $namaKepalaKeluargaQuestion->id)->delete();
    echo "   Deleted {$answersDeleted} associated answers\n";

    // Delete the question
    $namaKepalaKeluargaQuestion->delete();
    echo "   ✅ Question deleted\n\n";
} else {
    echo "ℹ️  'Nama Kepala Keluarga' question not found\n\n";
}

// Find "Nomor Kartu Keluarga (KK)" to get section info
$kkQuestion = Question::where('question_text', 'Nomor Kartu Keluarga (KK)')
    ->where('questionnaire_id', 8)
    ->first();

if ($kkQuestion) {
    // Check if "Upload Kartu Keluarga" already exists
    $uploadKKQuestion = Question::where('question_text', 'Upload Kartu Keluarga (KK)')
        ->where('questionnaire_id', 8)
        ->first();

    if (!$uploadKKQuestion) {
        echo "➕ Adding: Upload Kartu Keluarga (KK)\n";

        $newQuestion = Question::create([
            'questionnaire_id' => 8,
            'parent_section_id' => $kkQuestion->parent_section_id,
            'question_text' => 'Upload Kartu Keluarga (KK)',
            'question_type' => 'file',
            'order' => $kkQuestion->order + 1,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        echo "   ✅ Question created (ID: {$newQuestion->id})\n\n";

        // Reorder subsequent questions
        Question::where('questionnaire_id', 8)
            ->where('parent_section_id', $kkQuestion->parent_section_id)
            ->where('order', '>', $kkQuestion->order)
            ->where('id', '!=', $newQuestion->id)
            ->increment('order');

        echo "   ✅ Subsequent questions reordered\n";
    } else {
        echo "ℹ️  'Upload Kartu Keluarga (KK)' already exists (ID: {$uploadKKQuestion->id})\n\n";
    }
} else {
    echo "❌ 'Nomor Kartu Keluarga (KK)' not found - cannot determine section\n\n";
}

echo "\n✅ Update completed!\n";
echo "\n📝 Note: Family member KTP/KIA upload field is added in the frontend form.\n";
echo "   No database changes needed as it's stored in family_members JSON field.\n";
