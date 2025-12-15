<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔍 Mencari question_options dengan option_value kosong...\n\n";

$emptyOptions = DB::table('question_options')
    ->whereNull('option_value')
    ->orWhere('option_value', '')
    ->get();

echo "Ditemukan {$emptyOptions->count()} opsi dengan value kosong:\n\n";

if ($emptyOptions->count() > 0) {
    foreach ($emptyOptions as $option) {
        echo "ID: {$option->id}, Question: {$option->question_id}, Text: {$option->option_text}, Value: '{$option->option_value}'\n";
    }

    echo "\n🔧 Memperbaiki data...\n\n";

    $updated = 0;
    foreach ($emptyOptions as $option) {
        // Generate value dari option_text
        $value = strtolower(str_replace(' ', '_', $option->option_text));

        DB::table('question_options')
            ->where('id', $option->id)
            ->update([
                'option_value' => $value,
                'updated_at' => now()
            ]);

        echo "✅ Updated ID {$option->id}: '{$option->option_text}' → value: '{$value}'\n";
        $updated++;
    }

    echo "\n✅ Selesai! {$updated} opsi berhasil diperbaiki.\n";
} else {
    echo "✅ Semua opsi sudah memiliki value!\n";
}

echo "\n🔍 Memeriksa question dengan type kosong...\n\n";

$emptyTypes = DB::table('questions')
    ->whereNull('type')
    ->orWhere('type', '')
    ->get();

echo "Ditemukan {$emptyTypes->count()} pertanyaan dengan type kosong:\n\n";

if ($emptyTypes->count() > 0) {
    foreach ($emptyTypes as $question) {
        echo "ID: {$question->id}, Text: " . substr($question->question_text, 0, 50) . "...\n";

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

        echo "✅ Updated type to: {$type}\n\n";
    }
}

echo "\n✅ Semua data sudah diperbaiki!\n";
