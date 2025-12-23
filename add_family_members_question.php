<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;

// Cari section "IV. KETERANGAN ANGGOTA KELUARGA" (Section 4)
$section4 = Question::where('questionnaire_id', 8)
    ->where('is_section', true)
    ->where('question_text', 'LIKE', '%KETERANGAN ANGGOTA KELUARGA%')
    ->first();

if (!$section4) {
    echo "Section IV tidak ditemukan!\n";
    exit;
}

echo "Found Section: {$section4->question_text} (ID: {$section4->id})\n\n";

// Tambahkan pertanyaan family_members di section ini
$question = Question::create([
    'questionnaire_id' => 8,
    'parent_section_id' => $section4->id,
    'question_text' => 'Daftar Anggota Keluarga',
    'question_type' => 'family_members',
    'is_section' => false,
    'is_required' => true,
    'order' => 100,
    'media_type' => 'none',
    'settings' => json_encode([]),
]);

echo "✅ Berhasil menambahkan pertanyaan 'Daftar Anggota Keluarga'\n";
echo "   ID: {$question->id}\n";
echo "   Type: {$question->question_type}\n";
echo "   Parent Section: {$section4->question_text}\n";
