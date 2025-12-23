<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;

// 1. Hapus placeholder text di Section IV (ID 236)
$placeholderQuestion = Question::find(236);
if ($placeholderQuestion) {
    echo "Menghapus placeholder: {$placeholderQuestion->question_text}\n";
    $placeholderQuestion->delete();
}

// 2. Update pertanyaan family_members (ID 265) - pindahkan ke Section IV
$familyMembersQ = Question::find(265);
if ($familyMembersQ) {
    $familyMembersQ->update([
        'parent_section_id' => 235,  // Section IV
        'order' => 24,
    ]);
    echo "✅ Pertanyaan 'Daftar Anggota Keluarga' dipindahkan ke Section IV\n";
}

// 3. Verifikasi
$section4 = Question::find(235);
echo "\n=== Section IV: {$section4->question_text} ===\n";
$children = Question::where('parent_section_id', 235)->get();
foreach ($children as $child) {
    echo "  └─ [{$child->question_type}] {$child->question_text}\n";
}
