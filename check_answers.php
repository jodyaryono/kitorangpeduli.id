<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Verifikasi Variasi Jawaban Responden\n";
echo "======================================\n\n";

$questionnaire = App\Models\Questionnaire::with('questions')->first();
echo "Questionnaire: {$questionnaire->title}\n";
echo "Total Questions: {$questionnaire->questions->count()}\n\n";

// Check first 3 questions
foreach ($questionnaire->questions->take(3) as $idx => $question) {
    echo 'Question ' . ($idx + 1) . ": {$question->question_text}\n";
    echo "Type: {$question->question_type}\n";

    $answers = App\Models\Answer::where('question_id', $question->id)
        ->whereNotNull('answer_text')
        ->take(10)
        ->get();

    if ($answers->count() > 0) {
        echo "Sample Answers:\n";
        foreach ($answers as $i => $answer) {
            $text = substr($answer->answer_text, 0, 100);
            if (strlen($answer->answer_text) > 100)
                $text .= '...';
            echo '  ' . ($i + 1) . ". {$text}\n";
        }

        // Check uniqueness
        $uniqueAnswers = $answers->pluck('answer_text')->unique();
        $percentage = round(($uniqueAnswers->count() / $answers->count()) * 100);
        echo "Variasi: {$uniqueAnswers->count()}/{$answers->count()} jawaban unik ({$percentage}%)\n";
    } else {
        // Check for option-based answers
        $optionAnswers = App\Models\Answer::where('question_id', $question->id)
            ->whereNotNull('selected_option_id')
            ->with('selectedOption')
            ->take(10)
            ->get();

        if ($optionAnswers->count() > 0) {
            echo "Sample Option Answers:\n";
            $answerCounts = [];
            foreach ($optionAnswers as $answer) {
                $optionText = $answer->selectedOption->option_text ?? 'Unknown';
                if (!isset($answerCounts[$optionText])) {
                    $answerCounts[$optionText] = 0;
                }
                $answerCounts[$optionText]++;
            }

            foreach ($answerCounts as $option => $count) {
                echo "  - {$option}: {$count}x\n";
            }
        } else {
            echo "No answers found\n";
        }
    }

    echo "\n";
}

// Check N/A count
$totalTextAnswers = App\Models\Answer::whereNotNull('answer_text')->count();
$naAnswers = App\Models\Answer::whereNotNull('answer_text')
    ->where('answer_text', 'LIKE', '%N/A%')
    ->count();

echo "\n=== Overall Statistics ===\n";
echo "Total text answers: {$totalTextAnswers}\n";
echo "N/A answers: {$naAnswers}\n";
if ($totalTextAnswers > 0) {
    $naPercentage = round(($naAnswers / $totalTextAnswers) * 100, 2);
    echo "N/A percentage: {$naPercentage}%\n";
}
