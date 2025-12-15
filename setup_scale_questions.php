<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "🔧 Mengupdate settings untuk pertanyaan tipe scale...\n\n";

$scaleQuestions = DB::table('questions')
    ->where('question_type', 'scale')
    ->get();

echo "Ditemukan {$scaleQuestions->count()} pertanyaan scale.\n\n";

foreach ($scaleQuestions as $q) {
    $settings = $q->settings ? json_decode($q->settings, true) : [];

    // Set default if not exists
    if (!isset($settings['min'])) {
        $settings['min'] = 1;
        $settings['max'] = 5;
        $settings['min_label'] = 'Sangat Buruk';
        $settings['max_label'] = 'Sangat Baik';

        DB::table('questions')
            ->where('id', $q->id)
            ->update([
                'settings' => json_encode($settings),
                'updated_at' => now()
            ]);

        echo "✅ Updated Q{$q->id}: " . substr($q->question_text, 0, 60) . "...\n";
        echo "   Settings: {$settings['min']}-{$settings['max']} ({$settings['min_label']} - {$settings['max_label']})\n\n";
    } else {
        echo "✓ Q{$q->id} sudah ada settings\n\n";
    }
}

echo "✅ Selesai!\n";
