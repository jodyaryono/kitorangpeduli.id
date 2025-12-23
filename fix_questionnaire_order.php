<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Question;

echo "Fixing questionnaire order...\n\n";

// Get questionnaire ID 8
$questionnaireId = 8;

// 1. Swap RT and RW order
echo "1. Swapping RT and RW order...\n";
$rw = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'RW')
    ->first();

$rt = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'RT')
    ->first();

if ($rw && $rt) {
    echo "   Current: RW (order {$rw->order}), RT (order {$rt->order})\n";

    // Swap orders
    $tempOrder = $rw->order;
    $rw->order = $rt->order;
    $rt->order = $tempOrder;

    $rw->save();
    $rt->save();

    echo "   Updated: RT (order {$rt->order}), RW (order {$rw->order})\n";
} else {
    echo "   ERROR: RT or RW not found!\n";
}

// 2. Add "No. Keluarga" after "No. Bangunan"
echo "\n2. Adding 'No. Keluarga' question...\n";

$noBangunan = Question::where('questionnaire_id', $questionnaireId)
    ->where('question_text', 'No. Bangunan')
    ->first();

if ($noBangunan) {
    $sectionI = $noBangunan->parent_section_id;
    echo "   No. Bangunan found (order {$noBangunan->order})\n";

    // Check if No. Keluarga already exists
    $noKeluarga = Question::where('questionnaire_id', $questionnaireId)
        ->where('question_text', 'No. Keluarga')
        ->first();

    if ($noKeluarga) {
        echo "   No. Keluarga already exists (order {$noKeluarga->order})\n";
    } else {
        // Shift all questions after No. Bangunan by 1
        $questionsToShift = Question::where('questionnaire_id', $questionnaireId)
            ->where('order', '>', $noBangunan->order)
            ->orderBy('order', 'desc')
            ->get();

        echo '   Shifting ' . $questionsToShift->count() . " questions...\n";
        foreach ($questionsToShift as $q) {
            $q->order = $q->order + 1;
            $q->save();
        }

        // Create No. Keluarga question
        $newOrder = $noBangunan->order + 1;
        Question::create([
            'questionnaire_id' => $questionnaireId,
            'parent_section_id' => $sectionI,
            'question_text' => 'No. Keluarga',
            'question_type' => 'text',
            'order' => $newOrder,
            'is_required' => false,
            'applies_to' => 'family',
        ]);

        echo "   Created 'No. Keluarga' (order {$newOrder})\n";
    }
} else {
    echo "   ERROR: No. Bangunan not found!\n";
}

// 3. Show final order
echo "\n3. Final order for Section I:\n";
$sectionIQuestions = Question::where('questionnaire_id', $questionnaireId)
    ->whereHas('parentSection', function ($q) {
        $q->where('question_text', 'I. PENGENALAN TEMPAT');
    })
    ->orderBy('order')
    ->get();

foreach ($sectionIQuestions as $q) {
    echo "   Order {$q->order}: {$q->question_text}\n";
}

// 4. Check if Imbi exists in villages
echo "\n4. Checking if kelurahan Imbi exists...\n";
$imbi = \App\Models\Village::where('name', 'IMBI')->first();
if ($imbi) {
    echo "   ✓ Kelurahan Imbi found (code: {$imbi->code}, district: {$imbi->district_code})\n";
    $district = \App\Models\District::where('code', $imbi->district_code)->first();
    if ($district) {
        echo "   ✓ District: {$district->name}\n";
    }
} else {
    echo "   ✗ Kelurahan Imbi NOT found in database!\n";
}

echo "\nDone!\n";
