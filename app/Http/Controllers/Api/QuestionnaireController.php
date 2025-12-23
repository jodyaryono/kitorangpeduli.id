<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionnaireController extends Controller
{
    /**
     * Get list of available questionnaires
     */
    public function index(Request $request): JsonResponse
    {
        $respondent = $request->user();

        $questionnaires = Questionnaire::with('opd:id,name,logo_path')
            ->available()
            ->get()
            ->map(function ($q) use ($respondent) {
                $response = $q->responses()->where('resident_id', $respondent->id)->first();

                return [
                    'id' => $q->id,
                    'title' => $q->title,
                    'description' => $q->description,
                    'opd' => $q->opd->name,
                    'opd_logo' => $q->opd->logo_path,
                    'cover_image' => $q->cover_image_path,
                    'questions_count' => $q->questions()->count(),
                    'start_date' => $q->start_date?->format('Y-m-d'),
                    'end_date' => $q->end_date?->format('Y-m-d'),
                    'requires_location' => $q->requires_location,
                    'status' => $response ? $response->status : 'not_started',
                    'progress' => $response ? $response->progress_percentage : 0,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $questionnaires,
        ]);
    }

    /**
     * Get questionnaire detail with questions
     */
    public function show(Request $request, Questionnaire $questionnaire): JsonResponse
    {
        if (!$questionnaire->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner tidak tersedia',
            ], 404);
        }

        $respondent = $request->user();

        // Check if respondent can answer
        if ($questionnaire->requires_verified_respondent && !$respondent->canAnswerQuestionnaire()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terverifikasi untuk mengisi kuesioner ini',
            ], 403);
        }

        // Get existing response if any
        $response = $questionnaire
            ->responses()
            ->where('resident_id', $respondent->id)
            ->first();

        $questions = $questionnaire
            ->questions()
            ->with('options')
            ->orderBy('order')
            ->get()
            ->map(function ($q) use ($response) {
                $answer = null;
                if ($response) {
                    $answer = $response->answers()->where('question_id', $q->id)->first();
                }

                return [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'question_type' => $q->question_type,
                    'media_type' => $q->media_type,
                    'media_path' => $q->media_path,
                    'is_required' => $q->is_required,
                    'settings' => $q->settings,
                    'options' => $q->options->map(fn($o) => [
                        'id' => $o->id,
                        'option_text' => $o->option_text,
                        'media_type' => $o->media_type,
                        'media_path' => $o->media_path,
                    ]),
                    'answer' => $answer ? [
                        'answer_text' => $answer->answer_text,
                        'selected_option_id' => $answer->selected_option_id,
                        'selected_options' => $answer->selected_options,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $questionnaire->id,
                'title' => $questionnaire->title,
                'description' => $questionnaire->description,
                'opd' => $questionnaire->opd->name,
                'cover_image' => $questionnaire->cover_image_path,
                'cover_video' => $questionnaire->cover_video_path,
                'requires_location' => $questionnaire->requires_location,
                'questions' => $questions,
                'response' => $response ? [
                    'id' => $response->id,
                    'status' => $response->status,
                    'progress' => $response->progress_percentage,
                    'last_question_id' => $response->last_question_id,
                ] : null,
            ],
        ]);
    }

    /**
     * Start or resume questionnaire
     */
    public function start(Request $request, Questionnaire $questionnaire): JsonResponse
    {
        if (!$questionnaire->isAvailable()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner tidak tersedia',
            ], 404);
        }

        $respondent = $request->user();

        if ($questionnaire->requires_verified_respondent && !$respondent->canAnswerQuestionnaire()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus terverifikasi untuk mengisi kuesioner ini',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get or create response
        $response = Response::firstOrCreate(
            [
                'questionnaire_id' => $questionnaire->id,
                'resident_id' => $respondent->id,
            ],
            [
                'status' => 'in_progress',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'device_info' => $request->header('User-Agent'),
                'ip_address' => $request->ip(),
                'started_at' => now(),
            ]
        );

        if ($response->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah menyelesaikan kuesioner ini',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'response_id' => $response->id,
                'status' => $response->status,
                'progress' => $response->progress_percentage,
                'last_question_id' => $response->last_question_id,
            ],
        ]);
    }

    /**
     * Auto-save answer
     */
    public function saveAnswer(Request $request, Response $response): JsonResponse
    {
        if ($response->resident_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($response->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner sudah selesai',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'answer_text' => 'nullable|string',
            'selected_option_id' => 'nullable|exists:question_options,id',
            'selected_options' => 'nullable|array',
            'selected_options.*' => 'exists:question_options,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $question = Question::findOrFail($request->question_id);

        // Validate answer based on question type
        if ($question->is_required) {
            $hasAnswer = $request->answer_text ||
                $request->selected_option_id ||
                !empty($request->selected_options) ||
                ($request->latitude && $request->longitude);

            if (!$hasAnswer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pertanyaan ini wajib dijawab',
                ], 422);
            }
        }

        // Save or update answer
        $answer = Answer::updateOrCreate(
            [
                'response_id' => $response->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer_text' => $request->answer_text,
                'selected_option_id' => $request->selected_option_id,
                'selected_options' => $request->selected_options,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'answered_at' => now(),
            ]
        );

        // Update response progress
        $response->update([
            'last_question_id' => $request->question_id,
        ]);
        $response->updateProgress();

        return response()->json([
            'success' => true,
            'message' => 'Jawaban tersimpan',
            'data' => [
                'answer_id' => $answer->id,
                'progress' => $response->fresh()->progress_percentage,
            ],
        ]);
    }

    /**
     * Complete questionnaire
     */
    public function complete(Request $request, Response $response): JsonResponse
    {
        if ($response->resident_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($response->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Kuesioner sudah selesai',
            ], 400);
        }

        // Validate all required questions are answered
        $questionnaire = $response->questionnaire;
        $requiredQuestions = $questionnaire->questions()->where('is_required', true)->pluck('id');
        $answeredQuestions = $response->answers()->pluck('question_id');

        $unanswered = $requiredQuestions->diff($answeredQuestions);
        if ($unanswered->isNotEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Masih ada pertanyaan wajib yang belum dijawab',
                'unanswered_questions' => $unanswered->values(),
            ], 422);
        }

        // Update location if provided
        if ($request->latitude && $request->longitude) {
            $response->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]);
        }

        $response->markAsCompleted();

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Kuesioner berhasil diselesaikan.',
            'data' => [
                'response_id' => $response->id,
                'completed_at' => $response->completed_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
