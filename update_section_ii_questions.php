<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;
use App\Models\QuestionOption;

echo "=== UPDATING SECTION II QUESTIONS ===\n\n";

$questionnaireId = 8;

// Get Section II
$sectionII = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'II. KETERANGAN KELUARGA')
    ->first();

if (!$sectionII) {
    echo "ERROR: Section II not found!\n";
    exit(1);
}

echo "Section II ID: {$sectionII->id}\n\n";

// Delete old questions in Section II (except KK fields and Alamat)
echo "1. Deleting old incorrect questions...\n";
$toDelete = Question::where('questionnaire_id', $questionnaireId)
    ->where('parent_section_id', $sectionII->id)
    ->whereIn('question_text', [
        'Kode Pos',
        'Apakah sumber air minum utama yang digunakan keluarga adalah air perpipaan?',
        'Apakah keluarga memiliki dan menggunakan jamban keluarga?',
        'Apakah keluarga menjadi peserta Jaminan Kesehatan Nasional (JKN)?',
        'Apakah keluarga sudah melakukan Pengelolaan Sampah dengan baik?',
        'Apakah keluarga sudah menerapkan Gerakan Masyarakat Hidup Sehat (GERMAS)?'
    ])
    ->get();

foreach ($toDelete as $q) {
    echo "   Deleting: {$q->question_text}\n";
    // Delete options first
    $q->options()->delete();
    // Delete answers
    DB::table('answers')->where('question_id', $q->id)->delete();
    // Delete question
    $q->delete();
}

// Add Nama Kepala Keluarga (auto-filled field)
echo "\n2. Adding 'Nama Kepala Keluarga' (auto-filled from family members)...\n";
$existingKepala = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'Nama Kepala Keluarga')
    ->first();

if (!$existingKepala) {
    Question::create([
        'questionnaire_id' => $questionnaireId,
        'parent_section_id' => $sectionII->id,
        'question_text' => 'Nama Kepala Keluarga',
        'question_type' => 'text',
        'order' => 11,
        'is_required' => false,
        'applies_to' => 'family',
        'settings' => ['readonly' => true, 'auto_fill' => 'family_head'],
    ]);
    echo "   ✓ Created\n";
} else {
    echo "   Already exists\n";
}

// Add Jumlah Anggota Keluarga questions
echo "\n3. Adding 'Jumlah Anggota Keluarga' questions...\n";
$jumlahAnggota = [
    ['text' => 'a. Jumlah Anggota Keluarga', 'order' => 12],
    ['text' => 'b. Jumlah Anggota Keluarga diwawancara', 'order' => 12],
    ['text' => 'c. Jumlah Anggota Keluarga dewasa (> 15 thn)', 'order' => 12],
    ['text' => 'd. Jumlah Anggota Keluarga usia 10 - 54 tahun', 'order' => 12],
    ['text' => 'e. Jumlah Anggota Keluarga usia 12 - 59 bulan', 'order' => 12],
    ['text' => 'f. Jumlah Anggota Keluarga usia 0 - 11 bulan', 'order' => 12],
];

foreach ($jumlahAnggota as $ja) {
    $existing = Question::where('questionnaire_id', $questionnaireId)
        ->where('question_text', $ja['text'])
        ->first();

    if (!$existing) {
        Question::create([
            'questionnaire_id' => $questionnaireId,
            'parent_section_id' => $sectionII->id,
            'question_text' => $ja['text'],
            'question_type' => 'text',
            'order' => $ja['order'],
            'is_required' => false,
            'applies_to' => 'family',
            'settings' => ['input_type' => 'number'],
        ]);
        echo "   ✓ Created: {$ja['text']}\n";
    } else {
        echo "   Exists: {$ja['text']}\n";
    }
}

// Add new questions 3-9
echo "\n4. Adding questions 3-9...\n";

$newQuestions = [
    [
        'order' => 13,
        'text' => 'Apakah tersedia sarana air bersih di lingkungan rumah?',
        'options' => [
            ['text' => 'Ya', 'value' => '1'],
            ['text' => 'Tidak', 'value' => '2', 'skip_to' => 'P.5'],
        ],
    ],
    [
        'order' => 14,
        'text' => 'Bila ya, apa jenis sumber airnya terlindung? (PDAM, sumur pompa, sumur gali terlindung, mata air terlindung)',
        'options' => [
            ['text' => 'Ya', 'value' => '1'],
            ['text' => 'Tidak (sumur terbuka, air sungai, danau/telaga, dll)', 'value' => '2'],
        ],
    ],
    [
        'order' => 15,
        'text' => 'Apakah tersedia jamban keluarga?',
        'options' => [
            ['text' => 'Ya', 'value' => '1'],
            ['text' => 'Tidak', 'value' => '2', 'skip_to' => 'P.7'],
        ],
    ],
    [
        'order' => 16,
        'text' => 'Bila ya, apakah jenis jambannya saniter? (kloset/leher angsa/plengsengan)',
        'options' => [
            ['text' => 'Ya', 'value' => '1'],
            ['text' => 'Tidak (Cemplung)', 'value' => '2'],
        ],
    ],
    [
        'order' => 17,
        'text' => 'Apakah ada Anggota Keluarga yang pernah didiagnosis menderita gangguan jiwa berat (Schizoprenia)?',
        'options' => [
            ['text' => 'Ya', 'value' => '1'],
            ['text' => 'Tidak', 'value' => '2', 'skip_to' => 'P.9'],
        ],
    ],
    [
        'order' => 18,
        'text' => 'Bila ya, apakah selama ini penderita tersebut meminum obat gangguan jiwa berat secara teratur?',
        'options' => [
            ['text' => 'Ya', 'value' => '1', 'skip_to' => 'BLOK III'],
            ['text' => 'Tidak', 'value' => '2', 'skip_to' => 'BLOK III'],
        ],
    ],
    [
        'order' => 19,
        'text' => 'Apakah ada Anggota Keluarga yang dipasang?',
        'options' => [
            ['text' => 'Ya', 'value' => '1'],
            ['text' => 'Tidak', 'value' => '2'],
        ],
    ],
];

foreach ($newQuestions as $nq) {
    $existing = Question::where('questionnaire_id', $questionnaireId)
        ->where('question_text', $nq['text'])
        ->first();

    if ($existing) {
        echo "   Exists: {$nq['text']}\n";
        continue;
    }

    $question = Question::create([
        'questionnaire_id' => $questionnaireId,
        'parent_section_id' => $sectionII->id,
        'question_text' => $nq['text'],
        'question_type' => 'single_choice',
        'order' => $nq['order'],
        'is_required' => true,
        'applies_to' => 'family',
    ]);

    foreach ($nq['options'] as $opt) {
        $settings = [];
        if (isset($opt['skip_to'])) {
            $settings['skip_to'] = $opt['skip_to'];
        }

        QuestionOption::create([
            'question_id' => $question->id,
            'option_text' => $opt['text'],
            'option_value' => $opt['value'],
            'order' => 1,
            'settings' => $settings,
        ]);
    }

    echo "   ✓ Created: {$nq['text']}\n";
}

// Re-number all questions properly
echo "\n5. Re-ordering all questions...\n";
$allQuestions = Question::where('questionnaire_id', $questionnaireId)
    ->where('parent_section_id', null)
    ->orWhere(function ($q) use ($questionnaireId) {
        $q
            ->where('questionnaire_id', $questionnaireId)
            ->whereNotNull('parent_section_id');
    })
    ->orderBy('order')
    ->orderBy('id')
    ->get();

$order = 1;
foreach ($allQuestions as $q) {
    if ($q->order != $order) {
        $q->order = $order;
        $q->save();
    }
    $order++;
}

echo "   ✓ Re-ordered\n";

echo "\n✓ Done! Section II updated successfully.\n";
echo "\nSilakan refresh browser dan cek Section II: KETERANGAN KELUARGA\n";
