<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;

echo "=== FIXING NAMA KEPALA KELUARGA & ALAMAT ===\n\n";

$questionnaireId = 8;

// 1. Set Nama Kepala Keluarga and a-f as readonly/auto-fill
echo "1. Setting Nama Kepala Keluarga and Jumlah Anggota (a-f) as auto-filled...\n";

$autoFillQuestions = [
    'Nama Kepala Keluarga',
    'a. Jumlah Anggota Keluarga',
    'b. Jumlah Anggota Keluarga diwawancara',
    'c. Jumlah Anggota Keluarga dewasa (> 15 thn)',
    'd. Jumlah Anggota Keluarga usia 10 - 54 tahun',
    'e. Jumlah Anggota Keluarga usia 12 - 59 bulan',
    'f. Jumlah Anggota Keluarga usia 0 - 11 bulan',
];

foreach ($autoFillQuestions as $qText) {
    $question = Question::where('questionnaire_id', $questionnaireId)
        ->where('question_text', $qText)
        ->first();

    if ($question) {
        $settings = $question->settings ?? [];
        $settings['readonly'] = true;
        $settings['auto_fill'] = true;
        $question->settings = $settings;
        $question->is_required = false;
        $question->save();
        echo "   ✓ Updated: {$qText}\n";
    } else {
        echo "   ✗ Not found: {$qText}\n";
    }
}

// 2. Move "Alamat" from Section II to Section I
echo "\n2. Moving 'Alamat' from Section II to Section I...\n";

$alamat = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'Alamat')
    ->first();

if ($alamat) {
    echo "   Current: Section {$alamat->parent_section_id}, Order {$alamat->order}\n";

    // Get Section I
    $sectionI = Question::where('questionnaire_id', $questionnaireId)
        ->where('question_text', 'I. PENGENALAN TEMPAT')
        ->first();

    if ($sectionI) {
        // Find the position after "No. Keluarga"
        $noKeluarga = Question::where('questionnaire_id', $questionnaireId)
            ->where('question_text', 'No. Keluarga')
            ->first();

        if ($noKeluarga) {
            // Shift questions after No. Keluarga
            $questionsToShift = Question::where('questionnaire_id', $questionnaireId)
                ->where('order', '>', $noKeluarga->order)
                ->orderBy('order', 'desc')
                ->get();

            echo '   Shifting ' . $questionsToShift->count() . " questions forward...\n";
            foreach ($questionsToShift as $q) {
                $q->order = $q->order + 1;
                $q->save();
            }

            // Move Alamat
            $newOrder = $noKeluarga->order + 1;
            $alamat->parent_section_id = $sectionI->id;
            $alamat->order = $newOrder;
            $alamat->save();

            echo "   ✓ Moved Alamat to Section I, Order {$newOrder}\n";
        } else {
            echo "   ✗ No. Keluarga not found!\n";
        }
    } else {
        echo "   ✗ Section I not found!\n";
    }
} else {
    echo "   ✗ Alamat question not found!\n";
}

// 3. Verify final structure
echo "\n3. Verification:\n";

echo "\n   Section I (PENGENALAN TEMPAT):\n";
$sectionI = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'I. PENGENALAN TEMPAT')
    ->first();

if ($sectionI) {
    $sectionIQuestions = Question::where('questionnaire_id', $questionnaireId)
        ->where('parent_section_id', $sectionI->id)
        ->orderBy('order')
        ->get();

    foreach ($sectionIQuestions as $q) {
        $readonly = isset($q->settings['readonly']) && $q->settings['readonly'] ? ' [AUTO]' : '';
        echo "     Order {$q->order}: {$q->question_text}{$readonly}\n";
    }
}

echo "\n   Section II (KETERANGAN KELUARGA) - First 5 questions:\n";
$sectionII = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'II. KETERANGAN KELUARGA')
    ->first();

if ($sectionII) {
    $sectionIIQuestions = Question::where('questionnaire_id', $questionnaireId)
        ->where('parent_section_id', $sectionII->id)
        ->orderBy('order')
        ->take(10)
        ->get();

    foreach ($sectionIIQuestions as $q) {
        $readonly = isset($q->settings['readonly']) && $q->settings['readonly'] ? ' [AUTO]' : '';
        echo "     Order {$q->order}: {$q->question_text}{$readonly}\n";
    }
}

echo "\n✓ Done!\n";
