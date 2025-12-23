<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Question;

// Ubah nama Section V
$sectionV = Question::find(237);
if ($sectionV) {
    $sectionV->update([
        'question_text' => 'V. GANGGUAN KESEHATAN PER ANGGOTA KELUARGA'
    ]);
    echo "✅ Section V diubah: {$sectionV->question_text}\n\n";
}

// Hapus subsection lama (V.A, V.B, V.C, V.D)
$oldSubsections = Question::where('parent_section_id', 237)
    ->where('is_section', true)
    ->get();

echo "Menghapus subsection lama:\n";
foreach ($oldSubsections as $sub) {
    echo "  - {$sub->question_text}\n";

    // Hapus juga child questions dari subsection ini
    Question::where('parent_section_id', $sub->id)->delete();

    $sub->delete();
}

echo "\n✅ Struktur Section V dibersihkan\n";
echo "Sekarang bisa tambahkan pertanyaan conditional per anggota keluarga\n";
