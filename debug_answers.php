<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n=== DEBUG: CHECKING ANSWERS SAVED FOR QUESTIONS 21-23 ===\n\n";

$response = DB::table('responses')->where('id', 7)->first();
echo "Response ID: 7\n";
echo 'Status: ' . $response->status . "\n\n";

// Check actual answers for questions 21-23
echo "ANSWERS FOR QUESTIONS 21-23 (PASTE2):\n\n";

$questions = DB::table('questions')
    ->whereIn('id', [21, 22, 23])
    ->orderBy('id')
    ->get();

foreach ($questions as $q) {
    echo "Question {$q->id}: {$q->question_text}\n";

    $answer = DB::table('answers')
        ->where('response_id', 7)
        ->where('question_id', $q->id)
        ->first();

    if ($answer) {
        echo "  ✅ ANSWER FOUND:\n";
        echo '    answer_text: ' . ($answer->answer_text ?? 'NULL') . "\n";
        echo '    answer_numeric: ' . ($answer->answer_numeric ?? 'NULL') . "\n";
        echo '    selected_option_id: ' . ($answer->selected_option_id ?? 'NULL') . "\n";
        echo '    selected_options: ' . ($answer->selected_options ?? 'NULL') . "\n";
        echo '    answered_at: ' . ($answer->answered_at ?? 'NULL') . "\n";
    } else {
        echo "  ❌ NO ANSWER YET\n";
    }
    echo "\n";
}

// Check wilayah questions (questions 1-5)
echo "\n=== WILAYAH ANSWERS (Questions 1-5) ===\n\n";

$wilayahQuestions = DB::table('questions')
    ->whereIn('id', [1, 2, 3, 4, 5])
    ->orderBy('id')
    ->get();

foreach ($wilayahQuestions as $q) {
    echo "Question {$q->id} ({$q->question_type}): {$q->question_text}\n";

    $answer = DB::table('answers')
        ->where('response_id', 7)
        ->where('question_id', $q->id)
        ->first();

    if ($answer) {
        echo '  ✅ ANSWER: ' . ($answer->answer_text ?? $answer->answer_numeric ?? 'NULL') . "\n";

        // Lookup nama
        if ($answer->answer_text) {
            $id = $answer->answer_text;
            $name = null;

            switch ($q->question_type) {
                case 'province':
                    $rec = DB::table('provinces')->where('id', $id)->first();
                    $name = $rec->name ?? 'NOT FOUND';
                    break;
                case 'regency':
                    $rec = DB::table('regencies')->where('id', $id)->first();
                    $name = $rec->name ?? 'NOT FOUND';
                    break;
                case 'district':
                    $rec = DB::table('districts')->where('id', $id)->first();
                    $name = $rec->name ?? 'NOT FOUND';
                    break;
                case 'village':
                    $rec = DB::table('villages')->where('id', $id)->first();
                    $name = $rec->name ?? 'NOT FOUND';
                    break;
            }

            if ($name) {
                echo "    Nama: {$name}\n";
            }
        }
    } else {
        echo "  ❌ NO ANSWER - Should auto-fill from families table\n";
    }
    echo "\n";
}

echo "\n=== EXPECTED BEHAVIOR ===\n\n";
echo "1. Wilayah dropdowns (Q1-5) should show selected values from families table\n";
echo "   BUT answers table may be empty (that's OK - values shown via savedFamily)\n\n";

echo "2. When officer fills Q21-23 and submits, answers should be saved\n";
echo "   Check if form submission works correctly\n\n";

echo "3. If answers still empty after submit:\n";
echo "   - Check browser console for JS errors\n";
echo "   - Check network tab for failed POST request\n";
echo "   - Verify form validation passes\n";
