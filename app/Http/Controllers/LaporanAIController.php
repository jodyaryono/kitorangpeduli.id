<?php

namespace App\Http\Controllers;

use App\Models\Opd;
use App\Models\Questionnaire;
use App\Services\GeminiReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaporanAIController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiReportService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Cache OPD list for 1 hour
        $opds = \Cache::remember('opds_list', 3600, function () {
            return Opd::select('id', 'name', 'code')->orderBy('name')->get();
        });

        $selectedOpd = $user?->opd_id ?: (session('selected_opd') ?? old('opd_id'));
        $selectedQuestionnaireId = old('questionnaire_id') ?? session('selected_questionnaire_id');

        // Filter questionnaires: minimum 10 questions and 40% response rate (80 completed responses)
        // Optimized: Only select needed columns, filter in memory after withCount
        $questionnaireSource = Questionnaire::select('id', 'opd_id', 'title', 'description')
            ->withCount(['questions', 'responses' => fn($q) => $q->where('status', 'completed')])
            ->orderBy('title')
            ->get()
            ->filter(function ($q) {
                return $q->questions_count >= 10 && $q->responses_count >= 80;
            })
            ->unique('id')
            ->values();

        $questionnaires = $selectedOpd
            ? $questionnaireSource->where('opd_id', $selectedOpd)
            : collect();

        // Prepare clean questionnaire data without loaded relationships for JSON encoding
        // IMPORTANT: Do this BEFORE loading any relationships to avoid JSON encoding errors
        $questionnairesForJson = $questionnaireSource->map(function ($q) {
            return [
                'id' => (int) $q->id,
                'opd_id' => (int) $q->opd_id,
                'title' => (string) ($q->title ?? ''),
                'questions_count' => (int) ($q->questions_count ?? 0),
                'responses_count' => (int) ($q->responses_count ?? 0)
            ];
        })->values()->toArray();

        $questionnairesByOpd = $questionnaireSource
            ->groupBy('opd_id')
            ->map(fn($list) => $list->map(function ($q) {
                return [
                    'id' => $q->id,
                    'title' => $q->title,
                    'question_count' => $q->questions_count,
                    'questions' => [], // Empty - will be loaded via API when needed
                ];
            })->values())
            ->toArray();

        return view('laporan-ai-standalone', [
            'opds' => $opds,
            'questionnaires' => $questionnairesForJson,
            'questionnairesByOpd' => $questionnairesByOpd,
        ]);
    }

    /**
     * Generate 10 quick ideas menggunakan AI berdasarkan questionnaire dan data responden
     */
    private function generateQuickIdeas(Questionnaire $questionnaire): array
    {
        try {
            // Load data yang diperlukan untuk AI
            $questionnaire->load([
                'questions' => fn($q) => $q->select('id', 'questionnaire_id', 'question_text', 'question_type', 'order')->orderBy('order'),
                'responses' => fn($q) => $q
                    ->where('status', 'completed')
                    ->with([
                        'respondent:id,nama_lengkap,jenis_kelamin,tanggal_lahir,village_id,occupation_id,education_id',
                        'respondent.occupation:id,occupation',
                        'respondent.education:id,education',
                        'respondent.village:id,name,district_id',
                        'respondent.village.district:id,name',
                        'answers:id,response_id,question_id,answer_text'
                    ])
                    ->limit(100)  // Ambil sample untuk analisis
            ]);

            $responsesCount = $questionnaire->responses->count();

            if ($responsesCount === 0) {
                return [
                    'Belum ada data responden untuk questionnaire ini',
                    'Tunggu hingga ada responden yang mengisi survei',
                    'Data belum cukup untuk analisis',
                    'Minimal 10 responden diperlukan',
                    'Promosikan survei ke target responden',
                    'Pastikan link survei aktif dan mudah diakses',
                    'Monitor tingkat partisipasi secara berkala',
                    'Hubungi tim lapangan untuk follow-up',
                    'Periksa kembali periode survei',
                    'Evaluasi strategi distribusi survei'
                ];
            }

            // Prepare context data untuk AI
            $contextData = $this->prepareContextForAI($questionnaire);

            // Buat prompt untuk AI generate 10 ide analisis
            $prompt = $this->buildQuickIdeasPrompt($questionnaire, $contextData);

            // Call AI untuk generate ideas
            $aiResponse = $this->geminiService->callAPI($prompt);

            Log::info('AI Quick Ideas Response', [
                'questionnaire_id' => $questionnaire->id,
                'response_length' => strlen($aiResponse),
                'response_preview' => substr($aiResponse, 0, 500)
            ]);

            // Parse response dari AI (format: numbered list atau bullet points)
            $ideas = $this->parseAIIdeas($aiResponse);

            Log::info('Parsed Ideas Count', [
                'questionnaire_id' => $questionnaire->id,
                'count' => count($ideas),
                'ideas' => $ideas
            ]);

            // Ensure exactly 10 ideas
            if (count($ideas) < 10) {
                Log::warning('AI generated less than 10 ideas, using fallback', [
                    'questionnaire_id' => $questionnaire->id,
                    'ai_ideas_count' => count($ideas)
                ]);
                // Fallback ke default ideas
                return $this->getFallbackIdeas($questionnaire);
            }

            return array_slice($ideas, 0, 10);
        } catch (\Exception $e) {
            Log::error('Failed to generate quick ideas with AI', [
                'questionnaire_id' => $questionnaire->id,
                'error' => $e->getMessage()
            ]);

            // Fallback to default ideas
            return $this->getFallbackIdeas($questionnaire);
        }
    }

    /**
     * Prepare context data untuk AI
     */
    private function prepareContextForAI(Questionnaire $questionnaire): array
    {
        $responses = $questionnaire->responses;

        // Demographics summary
        $genderCount = $responses->groupBy('respondent.jenis_kelamin')->map->count();
        $ageGroups = $responses->groupBy(function ($r) {
            $age = $r->respondent->tanggal_lahir
                ? now()->diffInYears($r->respondent->tanggal_lahir)
                : null;
            if (!$age)
                return 'Unknown';
            if ($age < 30)
                return '18-29';
            if ($age < 50)
                return '30-49';
            return '50+';
        })->map->count();

        // Location distribution
        $locations = $responses->groupBy('respondent.village.district.name')->map->count();

        // ALL questions dengan detail jawaban (bukan hanya sample 5 pertanyaan)
        $allQuestions = [];
        foreach ($questionnaire->questions as $question) {
            $answers = $responses
                ->flatMap
                ->answers
                ->where('question_id', $question->id);

            $topAnswers = $answers
                ->groupBy('answer_text')
                ->map
                ->count()
                ->sortDesc()
                ->take(5);

            $allQuestions[] = [
                'no' => count($allQuestions) + 1,
                'text' => $question->question_text,
                'type' => $question->question_type,
                'total_answers' => $answers->count(),
                'top_answers' => $topAnswers->toArray()
            ];
        }

        return [
            'total_responses' => $responses->count(),
            'gender_distribution' => $genderCount->toArray(),
            'age_distribution' => $ageGroups->toArray(),
            'location_distribution' => $locations->take(10)->toArray(),
            'all_questions' => $allQuestions,
            'questionnaire_title' => $questionnaire->title,
            'total_questions' => $questionnaire->questions->count()
        ];
    }

    /**
     * Build prompt untuk AI generate quick ideas
     */
    private function buildQuickIdeasPrompt(Questionnaire $questionnaire, array $context): string
    {
        // Format list pertanyaan dengan top answers
        $questionsList = '';
        foreach ($context['all_questions'] as $q) {
            $topAnswers = '';
            if (!empty($q['top_answers'])) {
                $topAnswersArr = array_slice($q['top_answers'], 0, 3, true);
                $topAnswersList = [];
                foreach ($topAnswersArr as $ans => $count) {
                    $topAnswersList[] = "$ans ($count)";
                }
                $topAnswers = implode(', ', $topAnswersList);
            }
            $questionsList .= "Q{$q['no']}: {$q['text']} [Jawaban teratas: {$topAnswers}]\n";
        }

        $lokasiTop3 = implode(', ', array_keys(array_slice($context['location_distribution'], 0, 3)));
        $genderL = $context['gender_distribution']['L'] ?? 0;
        $genderP = $context['gender_distribution']['P'] ?? 0;
        $totalResponses = $context['total_responses'];
        $title = $questionnaire->title;

        return <<<PROMPT
            Kamu adalah data analyst expert. Generate TEPAT 10 ide analisis DATA-DRIVEN yang SINGKAT untuk survei "{$title}".

            DATA KONKRET:
            - Total Responden: {$totalResponses} orang
            - Gender: L={$genderL}, P={$genderP}
            - Lokasi top 3: {$lokasiTop3}

            PERTANYAAN SURVEI:
            {$questionsList}

            ATURAN PENTING:
            1. IDE HARUS SINGKAT (maksimal 1-2 kalimat)
            2. MERUJUK DATA KONKRET dari questionnaire (sebut Q1, Q2, jawaban spesifik, angka)
            3. BERBEDA satu sama lain (demografi, cross-tab, distribusi, korelasi, perbandingan lokasi)
            4. FORMAT: Langsung ide, tanpa penjelasan panjang

            CONTOH IDE YANG BENAR:
            1. Bandingkan jawaban Q1 antara laki-laki dan perempuan, tampilkan persentase
            2. Analisis distribusi Q3 berdasarkan lokasi (JAYAPURA vs ABEPURA)
            3. Cross-tab: Responden yang jawab "Puas" di Q2 vs jawaban mereka di Q5
            4. Tren umur: Bagaimana jawaban Q4 berbeda antar kelompok umur (18-29, 30-49, 50+)
            5. Korelasi Q6 dan Q8: Apakah yang jawab A di Q6 cenderung jawab B di Q8?

            GENERATE 10 IDE SEKARANG (format: 1. ... 2. ... dst, SINGKAT):
            PROMPT;
    }

    /**
     * Parse AI response menjadi array ideas
     */
    private function parseAIIdeas(string $aiResponse): array
    {
        $lines = explode("\n", trim($aiResponse));
        $ideas = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines
            if (empty($line))
                continue;

            // Skip intro/header lines
            if (stripos($line, 'berikut adalah') !== false ||
                stripos($line, 'format output') !== false ||
                stripos($line, 'generate') !== false ||
                stripos($line, 'aturan') !== false ||
                stripos($line, 'contoh ide') !== false ||
                stripos($line, 'ide analisis') === 0 ||
                stripos($line, 'untuk survei') !== false) {
                continue;
            }

            // Remove numbering (1. 2. (1) dll)
            $cleaned = preg_replace('/^[\(\[]?[\d]+[\)\.]\s*/', '', $line);
            $cleaned = preg_replace('/^[\-\*•]\s*/', '', $cleaned);
            $cleaned = trim($cleaned);

            // Skip if still looks like intro
            if (stripos($cleaned, 'berikut') !== false ||
                stripos($cleaned, 'ide analisis') === 0) {
                continue;
            }

            // Potong jika ada ":**" (ambil judul saja)
            if (preg_match('/^([^:]+):\*\*/', $cleaned, $matches)) {
                $cleaned = trim($matches[1]);
            }

            // HANYA ambil jika:
            // - Ada reference Q1, Q2, dll ATAU
            // - Ada kata analisis/bandingkan/distribusi/korelasi/cross
            // - Minimal 20 karakter
            if (strlen($cleaned) >= 20 &&
                (preg_match('/Q\d+/', $cleaned) ||
                 stripos($cleaned, 'bandingkan') !== false ||
                 stripos($cleaned, 'distribusi') !== false ||
                 stripos($cleaned, 'korelasi') !== false ||
                 stripos($cleaned, 'cross') !== false ||
                 stripos($cleaned, 'analisis') !== false ||
                 stripos($cleaned, 'perbandingan') !== false)) {
                $ideas[] = $cleaned;
            }
        }

        return $ideas;
    }

    /**
     * Fallback ideas jika AI gagal - generate berdasarkan pertanyaan aktual
     */
    private function getFallbackIdeas(Questionnaire $questionnaire): array
    {
        $ideas = [];
        $questions = $questionnaire->questions;

        // 1. Distribusi gender
        $ideas[] = "Bandingkan jumlah responden Laki-laki vs Perempuan, tampilkan persentase";

        // 2. Distribusi lokasi
        $ideas[] = 'Distribusi responden per kecamatan, urutkan dari terbanyak';

        // 3-6. Analisis per pertanyaan spesifik (ambil 4 pertanyaan)
        foreach ($questions->take(4) as $idx => $question) {
            $qNum = $idx + 1;
            $shortQ = substr($question->question_text, 0, 50);
            $ideas[] = "Q{$qNum} ({$shortQ}...): Top 3 jawaban terbanyak dengan persentase";
        }

        // 7. Cross-analysis gender
        if ($questions->count() > 0) {
            $ideas[] = "Bandingkan jawaban Q1 antara Laki-laki dan Perempuan";
        }

        // 8. Distribusi umur
        $ideas[] = 'Kelompok umur responden (18-30, 31-50, 50+) dengan jumlah';

        // 9. Cross-tab dua pertanyaan
        if ($questions->count() >= 2) {
            $ideas[] = 'Cross-tab Q1 vs Q2: Korelasi jawaban responden';
        }

        // 10. Distribusi berdasarkan lokasi
        $ideas[] = 'Perbandingan jawaban Q1 antar 3 kecamatan terbanyak';

        // Ensure exactly 10
        return array_slice($ideas, 0, 10);
    }

    /**
     * API endpoint untuk mendapatkan quick ideas
     */
    public function quickIdeas(Request $request)
    {
        try {
            $request->validate([
                'questionnaire_id' => 'required|exists:questionnaires,id',
                'opd_id' => 'required|exists:opds,id',
            ]);

            Log::info('Loading questionnaire for quick ideas', [
                'questionnaire_id' => $request->questionnaire_id,
                'opd_id' => $request->opd_id
            ]);

            $questionnaire = Questionnaire::with([
                'questions' => fn($q) => $q->select('id', 'questionnaire_id', 'question_text', 'question_type', 'order')->orderBy('order'),
                'responses' => fn($q) => $q
                    ->where('status', 'completed')
                    ->with([
                        'respondent:id,nama_lengkap,jenis_kelamin,tanggal_lahir,village_id',
                        'respondent.village:id,name,district_id',
                        'respondent.village.district:id,name',
                        'answers:id,response_id,question_id,answer_text'
                    ])
                    ->limit(100)
            ])->findOrFail($request->questionnaire_id);

            Log::info('Questionnaire loaded', [
                'questionnaire_id' => $questionnaire->id,
                'questions_count' => $questionnaire->questions->count(),
                'responses_count' => $questionnaire->responses->count()
            ]);

            // Validate OPD matches
            if ((int) $questionnaire->opd_id !== (int) $request->opd_id) {
                return response()->json(['error' => 'OPD mismatch'], 400);
            }

            $ideas = $this->generateQuickIdeas($questionnaire);

            Log::info('Ideas generated', ['count' => count($ideas)]);

            return response()->json(['ideas' => $ideas]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in quickIdeas', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation error', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Error in quickIdeas endpoint', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
