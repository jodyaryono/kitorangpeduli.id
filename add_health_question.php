<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get questionnaire ID
$q = App\Models\Questionnaire::first();
echo "Questionnaire ID: " . $q->id . "\n\n";

// List all sections
echo "All Sections:\n";
$sections = App\Models\Question::where('questionnaire_id', $q->id)
    ->where('is_section', true)
    ->get(['id', 'question_text']);

foreach ($sections as $s) {
    echo "  ID {$s->id}: {$s->question_text}\n";
}

// Find Section V (GANGGUAN KESEHATAN)
$sectionV = App\Models\Question::where('questionnaire_id', $q->id)
    ->where('is_section', true)
    ->where('question_text', 'like', '%GANGG%')
    ->first();

if (!$sectionV) {
    echo "\nSection V not found!\n";
    exit;
}

echo "\nSection V found:\n";
echo "  ID: {$sectionV->id}\n";
echo "  Text: {$sectionV->question_text}\n";

// Check if health_per_member already exists
$existing = App\Models\Question::where('questionnaire_id', $q->id)
    ->where('question_type', 'health_per_member')
    ->first();

if ($existing) {
    echo "\nhealth_per_member question already exists with ID: {$existing->id}\n";
    exit;
}

// Create health_per_member question
$healthQuestion = App\Models\Question::create([
    'questionnaire_id' => $q->id,
    'parent_id' => $sectionV->id,
    'question_text' => 'Gangguan Kesehatan Anggota Keluarga',
    'question_type' => 'health_per_member',
    'order' => 1,
    'is_required' => false,
    'is_section' => false,
]);

echo "\nCreated health_per_member question with ID: {$healthQuestion->id}\n";
