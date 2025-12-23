<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;

// Cari semua sections
$sections = Question::where('questionnaire_id', 8)
    ->where('is_section', true)
    ->whereNull('parent_section_id')
    ->orderBy('order')
    ->get(['id', 'question_text', 'order']);

echo "=== SECTIONS di Questionnaire 8 ===\n\n";
foreach ($sections as $section) {
    echo "ID: {$section->id} | Order: {$section->order} | {$section->question_text}\n";

    // Cek child questions
    $children = Question::where('parent_section_id', $section->id)
        ->orderBy('order')
        ->get(['id', 'question_text', 'question_type', 'order']);

    foreach ($children as $child) {
        echo "  └─ [{$child->question_type}] {$child->question_text}\n";
    }
    echo "\n";
}

// Cek pertanyaan family_members yang baru ditambahkan
$familyMembersQ = Question::where('questionnaire_id', 8)
    ->where('question_type', 'family_members')
    ->first();

if ($familyMembersQ) {
    echo "\n=== Pertanyaan Family Members ===\n";
    echo "ID: {$familyMembersQ->id}\n";
    echo "Text: {$familyMembersQ->question_text}\n";
    echo "Parent Section ID: {$familyMembersQ->parent_section_id}\n";
}
