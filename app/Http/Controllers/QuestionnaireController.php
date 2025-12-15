<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuestionnaireController extends Controller
{
    public function start($id)
    {
        // Check if logged in
        if (!session('respondent')) {
            session(['intended_questionnaire' => $id]);
            return redirect()
                ->route('login', ['intended' => 'questionnaire', 'id' => $id])
                ->with('info', 'Silakan masuk atau daftar terlebih dahulu untuk mengisi survey.');
        }

        $questionnaire = Questionnaire::with(['questions' => function ($query) {
            $query->orderBy('order')->with('options');
        }, 'opd'])->findOrFail($id);

        // Check if already completed
        $existingResponse = Response::where('questionnaire_id', $id)
            ->where('respondent_id', session('respondent.id'))
            ->where('status', 'completed')
            ->first();

        if ($existingResponse) {
            return redirect()
                ->route('home')
                ->with('error', 'Anda sudah mengisi kuesioner ini.');
        }

        // Get or create draft response
        $response = Response::firstOrCreate([
            'questionnaire_id' => $id,
            'respondent_id' => session('respondent.id'),
            'status' => 'draft',
        ], [
            'started_at' => now(),
        ]);

        // Load existing answers for this response
        $existingAnswers = Answer::where('response_id', $response->id)
            ->get()
            ->keyBy('question_id');

        return view('questionnaire.fill', compact('questionnaire', 'response', 'existingAnswers'));
    }

    public function autosave(Request $request, $id)
    {
        if (!session('respondent')) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        try {
            $response = Response::where('questionnaire_id', $id)
                ->where('respondent_id', session('respondent.id'))
                ->where('status', 'draft')
                ->first();

            if (!$response) {
                return response()->json(['success' => false, 'message' => 'Response not found'], 404);
            }

            $questionId = $request->question_id;
            $value = $request->answer;

            // Decode if JSON (for checkboxes)
            if (is_string($value) && json_decode($value) !== null) {
                $value = json_decode($value);
            }

            $answerData = [
                'response_id' => $response->id,
                'question_id' => $questionId,
            ];

            if (is_array($value)) {
                $answerData['answer_value'] = json_encode($value);
            } else {
                $answerData['answer_text'] = $value;
            }

            Answer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $questionId],
                $answerData
            );

            return response()->json(['success' => true, 'message' => 'Saved']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function submit(Request $request, $id)
    {
        if (!session('respondent')) {
            return redirect()->route('login');
        }

        $questionnaire = Questionnaire::with('questions')->findOrFail($id);

        // Validate required questions
        $rules = [];
        foreach ($questionnaire->questions as $question) {
            if ($question->is_required) {
                $rules["answers.{$question->id}"] = 'required';
            }
        }

        $request->validate($rules, [
            'answers.*.required' => 'Pertanyaan ini wajib dijawab.',
        ]);

        DB::beginTransaction();
        try {
            // Get or create response
            $response = Response::where('questionnaire_id', $id)
                ->where('respondent_id', session('respondent.id'))
                ->where('status', 'draft')
                ->first();

            if (!$response) {
                $response = Response::create([
                    'questionnaire_id' => $id,
                    'respondent_id' => session('respondent.id'),
                    'status' => 'draft',
                    'started_at' => now(),
                ]);
            }

            // Save GPS location if provided
            if ($request->has('latitude') && $request->has('longitude')) {
                $response->update([
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ]);
            }

            // Save answers
            foreach ($request->answers ?? [] as $questionId => $value) {
                $question = Question::find($questionId);
                if (!$question)
                    continue;

                $answerData = [
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                ];

                if (is_array($value)) {
                    // Multiple choice (checkbox)
                    $answerData['answer_value'] = json_encode($value);
                } elseif ($question->type === 'number' || $question->type === 'rating') {
                    $answerData['answer_numeric'] = $value;
                } else {
                    $answerData['answer_text'] = $value;
                }

                // Check if answer exists and update, otherwise create
                Answer::updateOrCreate(
                    ['response_id' => $response->id, 'question_id' => $questionId],
                    $answerData
                );
            }

            // Handle file uploads
            if ($request->hasFile('file_answers')) {
                foreach ($request->file('file_answers') as $questionId => $file) {
                    $answer = Answer::firstOrCreate([
                        'response_id' => $response->id,
                        'question_id' => $questionId,
                    ]);

                    $answer->addMedia($file)->toMediaCollection('answer_files');
                }
            }

            // Mark as completed
            $response->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('questionnaire.success', $response->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function success($responseId)
    {
        $response = Response::with(['questionnaire', 'respondent'])
            ->where('respondent_id', session('respondent.id'))
            ->findOrFail($responseId);

        return view('questionnaire.success', compact('response'));
    }
}
