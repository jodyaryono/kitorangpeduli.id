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
        // Check if officer-assisted mode
        $isOfficerAssisted = session('officer_assisted', false);
        $respondentData = $isOfficerAssisted
            ? session('officer_respondent')
            : session('respondent');

        // Check if logged in (either as respondent or officer-assisted)
        if (!$respondentData && !auth()->check()) {
            session(['intended_questionnaire' => $id]);
            return redirect()
                ->route('login', ['intended' => 'questionnaire', 'id' => $id])
                ->with('info', 'Silakan masuk atau daftar terlebih dahulu untuk mengisi survey.');
        }

        $questionnaire = Questionnaire::with(['questions' => function ($query) {
            $query->orderBy('order')->with('options');
        }, 'opd'])->findOrFail($id);

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        // Check if already completed
        $existingResponse = Response::where('questionnaire_id', $id)
            ->where('respondent_id', $respondentId)
            ->where('status', 'completed')
            ->first();

        if ($existingResponse) {
            $redirectRoute = $isOfficerAssisted ? 'officer.entry' : 'home';
            return redirect()
                ->route($redirectRoute)
                ->with('error', 'Responden ini sudah mengisi kuesioner ini.');
        }

        // Get or create in-progress response
        $response = Response::where('questionnaire_id', $id)
            ->where('respondent_id', $respondentId)
            ->where('status', 'in_progress')
            ->first();

        if (!$response) {
            $response = Response::create([
                'questionnaire_id' => $id,
                'respondent_id' => $respondentId,
                'status' => 'in_progress',
                'started_at' => now(),
                'entered_by_user_id' => $isOfficerAssisted ? auth()->id() : null,
            ]);
        }

        // Load existing answers for this response
        $existingAnswers = Answer::where('response_id', $response->id)
            ->get()
            ->keyBy('question_id');

        return view('questionnaire.fill', compact('questionnaire', 'response', 'existingAnswers', 'isOfficerAssisted', 'respondentData'));
    }

    public function autosave(Request $request, $id)
    {
        // Check if officer-assisted or regular respondent
        $isOfficerAssisted = session('officer_assisted', false);
        $respondentData = $isOfficerAssisted
            ? session('officer_respondent')
            : session('respondent');

        if (!$respondentData && !auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        try {
            $response = Response::where('questionnaire_id', $id)
                ->where('respondent_id', $respondentId)
                ->where('status', 'in_progress')
                ->first();

            if (!$response) {
                return response()->json(['success' => false, 'message' => 'Response not found'], 404);
            }

            $questionId = $request->question_id;
            $value = $request->answer;

            \Log::info('Autosave received', [
                'question_id' => $questionId,
                'raw_value' => $value,
                'is_string' => is_string($value),
                'is_array' => is_array($value)
            ]);

            // Decode if JSON (for checkboxes)
            if (is_string($value) && json_decode($value) !== null) {
                $decoded = json_decode($value);
                if ($decoded !== null) {
                    $value = $decoded;
                    \Log::info('Decoded JSON value', ['decoded' => $value]);
                }
            }

            $answerData = [
                'response_id' => $response->id,
                'question_id' => $questionId,
            ];

            if (is_array($value)) {
                $answerData['selected_options'] = json_encode($value);
                \Log::info('Saving as selected_options', ['json' => $answerData['selected_options']]);
            } else {
                $answerData['answer_text'] = $value;
                \Log::info('Saving as answer_text', ['text' => $value]);
            }

            Answer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $questionId],
                $answerData
            );

            return response()->json(['success' => true, 'message' => 'Saved']);
        } catch (\Exception $e) {
            \Log::error('Autosave error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function submit(Request $request, $id)
    {
        // Check if officer-assisted or regular respondent
        $isOfficerAssisted = session('officer_assisted', false);
        $respondentData = $isOfficerAssisted
            ? session('officer_respondent')
            : session('respondent');

        if (!$respondentData && !auth()->check()) {
            return redirect()->route('login');
        }

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        $questionnaire = Questionnaire::with('questions')->findOrFail($id);

        // Validate required questions
        $rules = [];
        $customMessages = [];
        foreach ($questionnaire->questions as $question) {
            if ($question->is_required) {
                $rules["answers.{$question->id}"] = 'required';
                $customMessages["answers.{$question->id}.required"] = "Pertanyaan #{$question->order_number}: {$question->question_text} - wajib dijawab.";
            }
        }

        $request->validate($rules, $customMessages);

        DB::beginTransaction();
        try {
            // Get or create response
            $response = Response::where('questionnaire_id', $id)
                ->where('respondent_id', $respondentId)
                ->where('status', 'in_progress')
                ->first();

            if (!$response) {
                $response = Response::create([
                    'questionnaire_id' => $id,
                    'respondent_id' => $respondentId,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'entered_by_user_id' => $isOfficerAssisted ? auth()->id() : null,
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
                    $answerData['selected_options'] = json_encode($value);
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

            // Clear officer-assisted session if applicable
            if ($isOfficerAssisted) {
                session()->forget(['officer_assisted', 'officer_response_id', 'officer_respondent']);
            }

            return redirect()->route('questionnaire.success', $response->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function success($responseId)
    {
        // Check if officer-assisted or regular respondent
        $isOfficerAssisted = session('officer_assisted', false);
        $respondentData = $isOfficerAssisted
            ? session('officer_respondent')
            : session('respondent');

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        $response = Response::with(['questionnaire', 'respondent'])
            ->where('id', $responseId)
            ->firstOrFail();

        // Verify ownership or officer access
        if (!$isOfficerAssisted && $response->respondent_id != $respondentId) {
            abort(403, 'Unauthorized access');
        }

        return view('questionnaire.success', compact('response', 'isOfficerAssisted'));
    }
}
