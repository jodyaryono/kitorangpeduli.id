<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Memperbaiki question type yang kosong...\n\n";

$emptyTypes = DB::table('questions')
    ->whereNull('type')
    ->orWhere('type', '')
    ->get();

echo "Ditemukan {$emptyTypes->count()} pertanyaan dengan type kosong.\n\n";

if ($emptyTypes->count() > 0) {
    $updated = 0;

    foreach ($emptyTypes as $question) {
        // Cek apakah ada options
        $hasOptions = DB::table('question_options')
            ->where('question_id', $question->id)
            ->exists();

        $type = $hasOptions ? 'radio' : 'text';

        DB::table('questions')
            ->where('id', $question->id)
            ->update([
                'type' => $type,
                'updated_at' => now()
            ]);

        $questionPreview = substr($question->question_text, 0, 60);
        echo "✅ Q{$question->id}: {$questionPreview}... → {$type}\n";
        $updated++;
    }

    echo "\n✅ Selesai! {$updated} pertanyaan berhasil diperbaiki.\n";
} else {
    echo "✅ Semua pertanyaan sudah memiliki type!\n";
}
