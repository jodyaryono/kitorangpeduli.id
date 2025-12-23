<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Question;

$questionnaire = \App\Models\Questionnaire::latest()->first();

if (!$questionnaire) {
    echo "⚠️  No questionnaire found\n";
    exit;
}

echo "📋 Fixing parent_section_id for questionnaire: {$questionnaire->title}\n\n";

// Define section ranges based on order
$sectionRanges = [
    ['name' => 'I. PENGENALAN TEMPAT', 'order' => 1, 'range' => [2, 9]],
    ['name' => 'II. KETERANGAN KELUARGA', 'order' => 10, 'range' => [11, 19]],
    ['name' => 'III. KETERANGAN PENCACAH', 'order' => 20, 'range' => [21, 22]],
    ['name' => 'IV. DAFTAR ANGGOTA KELUARGA', 'order' => 23, 'range' => [24, 24]],
    ['name' => 'V. KETERANGAN ANGGOTA KELUARGA', 'order' => 25, 'range' => [26, 52]],
];

foreach ($sectionRanges as $range) {
    $section = Question::where('questionnaire_id', $questionnaire->id)
        ->where('order', $range['order'])
        ->first();

    if (!$section) {
        echo "⚠️  Section not found: {$range['name']}\n";
        continue;
    }

    // Update section to mark as section
    $section->update(['is_section' => true]);

    // Update all questions in range
    $updated = Question::where('questionnaire_id', $questionnaire->id)
        ->whereBetween('order', $range['range'])
        ->whereNull('parent_section_id')
        ->update(['parent_section_id' => $section->id]);

    echo "✅ Section: {$range['name']}\n";
    echo "   Updated {$updated} questions (order {$range['range'][0]}-{$range['range'][1]})\n\n";
}

// Check for subsections within Section V
$subsections = [
    ['name' => 'V.A. IDENTITAS', 'order' => 26, 'range' => [27, 35]],
    ['name' => 'V.B. STATUS KESEHATAN', 'order' => 36, 'range' => [37, 42]],
    ['name' => 'V.C. KHUSUS UNTUK IBU HAMIL', 'order' => 43, 'range' => [44, 46]],
    ['name' => 'V.D. KHUSUS UNTUK BALITA', 'order' => 47, 'range' => [48, 52]],
];

$sectionV = Question::where('questionnaire_id', $questionnaire->id)
    ->where('order', 25)
    ->first();

foreach ($subsections as $sub) {
    $subsection = Question::where('questionnaire_id', $questionnaire->id)
        ->where('order', $sub['order'])
        ->first();

    if (!$subsection) {
        echo "⚠️  Subsection not found: {$sub['name']}\n";
        continue;
    }

    // Mark as section and set parent to Section V
    $subsection->update([
        'is_section' => true,
        'parent_section_id' => $sectionV->id
    ]);

    // Update questions in this subsection
    $updated = Question::where('questionnaire_id', $questionnaire->id)
        ->whereBetween('order', $sub['range'])
        ->where('is_section', false)
        ->update(['parent_section_id' => $subsection->id]);

    echo "✅ Subsection: {$sub['name']}\n";
    echo "   Updated {$updated} questions (order {$sub['range'][0]}-{$sub['range'][1]})\n\n";
}

echo "\n🎉 Done! Run check_sections.php to verify.\n";
