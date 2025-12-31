<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "📊 TABEL PENYIMPANAN JAWABAN\n";
echo str_repeat('=', 60) . "\n\n";

// 1. TABEL ANSWERS - Jawaban Pertanyaan Reguler
echo "1️⃣  TABEL: answers\n";
echo "   Deskripsi: Menyimpan jawaban untuk pertanyaan reguler\n";
echo "   Kolom:\n";

if (Schema::hasTable('answers')) {
    $columns = DB::select("SELECT column_name, data_type, is_nullable
                          FROM information_schema.columns
                          WHERE table_name = 'answers'
                          AND table_schema = current_schema()
                          ORDER BY ordinal_position");

    foreach ($columns as $col) {
        $nullable = $col->is_nullable === 'YES' ? '(nullable)' : '(required)';
        echo "     - {$col->column_name} ({$col->data_type}) {$nullable}\n";
    }

    $count = DB::table('answers')->count();
    echo "\n   📈 Total records: {$count}\n";

    if ($count > 0) {
        echo "\n   Contoh data:\n";
        $samples = DB::table('answers')
            ->select('id', 'response_id', 'question_id', 'answer_text', 'selected_options')
            ->limit(3)
            ->get();

        foreach ($samples as $sample) {
            echo "     - ID: {$sample->id}, Response: {$sample->response_id}, Question: {$sample->question_id}\n";
            if ($sample->answer_text)
                echo '       Text: ' . substr($sample->answer_text, 0, 50) . "\n";
            if ($sample->selected_options)
                echo "       Options: {$sample->selected_options}\n";
        }
    }
} else {
    echo "   ❌ Tabel tidak ditemukan\n";
}

echo "\n" . str_repeat('-', 60) . "\n\n";

// 2. TABEL RESIDENT_HEALTH_RESPONSES - Jawaban Kesehatan per Anggota
echo "2️⃣  TABEL: resident_health_responses\n";
echo "   Deskripsi: Menyimpan jawaban kesehatan per anggota keluarga\n";
echo "   Kolom:\n";

if (Schema::hasTable('resident_health_responses')) {
    $columns = DB::select("SELECT column_name, data_type, is_nullable
                          FROM information_schema.columns
                          WHERE table_name = 'resident_health_responses'
                          AND table_schema = current_schema()
                          ORDER BY ordinal_position");

    foreach ($columns as $col) {
        $nullable = $col->is_nullable === 'YES' ? '(nullable)' : '(required)';
        echo "     - {$col->column_name} ({$col->data_type}) {$nullable}\n";
    }

    $count = DB::table('resident_health_responses')->count();
    echo "\n   📈 Total records: {$count}\n";

    if ($count > 0) {
        echo "\n   Contoh data:\n";
        $samples = DB::table('resident_health_responses')
            ->select('id', 'response_id', 'resident_id', 'question_code', 'answer')
            ->limit(3)
            ->get();

        foreach ($samples as $sample) {
            echo "     - ID: {$sample->id}, Response: {$sample->response_id}, Resident: {$sample->resident_id}\n";
            echo "       Question: {$sample->question_code}, Answer: {$sample->answer}\n";
        }
    }
} else {
    echo "   ❌ Tabel tidak ditemukan\n";
}

echo "\n" . str_repeat('-', 60) . "\n\n";

// 3. TABEL RESPONSES - Metadata Response
echo "3️⃣  TABEL: responses\n";
echo "   Deskripsi: Metadata untuk setiap submission kuesioner\n";
echo "   Kolom:\n";

if (Schema::hasTable('responses')) {
    $columns = DB::select("SELECT column_name, data_type, is_nullable
                          FROM information_schema.columns
                          WHERE table_name = 'responses'
                          AND table_schema = current_schema()
                          ORDER BY ordinal_position");

    foreach ($columns as $col) {
        $nullable = $col->is_nullable === 'YES' ? '(nullable)' : '(required)';
        echo "     - {$col->column_name} ({$col->data_type}) {$nullable}\n";
    }

    $count = DB::table('responses')->count();
    echo "\n   📈 Total records: {$count}\n";
}

echo "\n" . str_repeat('=', 60) . "\n";

echo "\n📌 RINGKASAN:\n\n";
echo "1. answers                   → Jawaban pertanyaan reguler\n";
echo "2. resident_health_responses → Jawaban kesehatan per anggota\n";
echo "3. responses                 → Metadata submission\n";
echo "\n" . str_repeat('=', 60) . "\n";
