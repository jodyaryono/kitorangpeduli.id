<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use Illuminate\Http\Request;

class QuestionnairePreviewController extends Controller
{
    public function show(Questionnaire $questionnaire)
    {
        // Load questionnaire with sections (parent questions with is_section=true)
        // and child questions, properly hierarchical
        $questionnaire->load(['questions' => function ($query) {
            $query
                ->whereNull('parent_section_id')  // Only root level (sections or standalone questions)
                ->orderBy('order')
                ->with(['childQuestions' => function ($childQuery) {
                    $childQuery
                        ->orderBy('order')
                        ->with(['options', 'childQuestions.options']);  // Nested subsections
                }, 'options']);
        }, 'opd']);

        return view('questionnaire.preview', compact('questionnaire'));
    }
}
