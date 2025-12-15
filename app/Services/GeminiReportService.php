<?php

namespace App\Services;

use App\Models\Questionnaire;
use App\Models\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiReportService
{
    private string $apiKey;
    private string $model = 'gemini-2.0-flash-exp';
    private string $apiUrl;
    private bool $useDirectGemini;

    public function __construct()
    {
        // Check if using direct Gemini API or OpenRouter
        $this->useDirectGemini = config('services.gemini.enabled', false);

        if ($this->useDirectGemini) {
            // Direct Gemini API
            $this->apiKey = config('services.gemini.api_key') ?? '';
            $this->apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/' . $this->model . ':generateContent';
        } else {
            // OpenRouter API
            $this->apiKey = config('services.openrouter.api_key') ?? '';
            $this->apiUrl = config('services.openrouter.api_url') ?? 'https://openrouter.ai/api/v1/chat/completions';
        }
    }

    /**
     * Generate report berdasarkan user prompt
     */
    public function generateReport(
        Questionnaire $questionnaire,
        string $userPrompt,
        ?int $opdId = null
    ): array {
        try {
            // 1. Build context dari questionnaire
            $context = $this->buildContext($questionnaire);

            // 2. Build prompt untuk Gemini
            $fullPrompt = $this->buildPrompt($context, $userPrompt);

            // 3. Call Gemini API
            $aiResponse = $this->callGeminiAPI($fullPrompt);

            // 4. Parse response
            $parsedResponse = $this->parseResponse($aiResponse);

            \Log::info('AI Response:', ['raw' => $aiResponse, 'parsed' => $parsedResponse]);

            // 5. Ambil responses dari questionnaire - OPTIMIZED with specific columns
            $responses = $questionnaire
                ->responses()
                ->where('status', 'completed')
                ->with([
                    'answers:id,response_id,question_id,selected_option_id,answer_text',
                    'answers.selectedOption:id,question_id,option_text',
                    'respondent:id,nama_lengkap,jenis_kelamin,tanggal_lahir,latitude,longitude,village_id,district_id,regency_id,citizen_type_id,occupation_id,education_id',
                    'respondent.occupation:id,occupation',
                    'respondent.education:id,education',
                    'respondent.village:id,name',
                    'respondent.district:id,name',
                    'respondent.regency:id,name',
                    'respondent.citizenType:id,name'
                ])
                ->select('id', 'questionnaire_id', 'respondent_id', 'status', 'completed_at')
                ->get();

            // 6. Generate data untuk 3 tabs
            $data = [
                'text' => $parsedResponse['answer'] ?? $aiResponse,  // Text untuk tab Teks
                'data_driven' => $parsedResponse['data_driven'] ?? true,  // Default to true untuk backward compatibility
                'raw_data' => $this->generateRawData($questionnaire, $responses, $parsedResponse),
                'chart_data' => $this->generateChartData($questionnaire, $responses, $parsedResponse),
                'map_data' => $this->generateMapData($responses),
                'ai_response' => $aiResponse,
                'status' => 'completed',
                'parsed' => $parsedResponse,
            ];

            return $data;
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build context dari questionnaire - OPTIMIZED version
     */
    private function buildContext(Questionnaire $questionnaire): string
    {
        $context = "QUESTIONNAIRE INFO:\n";
        $context .= "Title: {$questionnaire->title}\n";
        $context .= "Description: {$questionnaire->description}\n";
        $context .= 'Total Questions: ' . $questionnaire->questions->count() . "\n";

        // OPTIMIZED: Just count responses, don't load all details for context
        $totalResponses = $questionnaire->responses()->where('status', 'completed')->count();
        $context .= "Total Responses: {$totalResponses}\n\n";

        // Get aggregate statistics instead of individual records
        $responses = $questionnaire
            ->responses()
            ->where('status', 'completed')
            ->with(['respondent:id,jenis_kelamin,tanggal_lahir,village_id', 'respondent.village:id,name'])
            ->select('id', 'questionnaire_id', 'respondent_id')
            ->limit(50)  // LIMIT to 50 sample responses for context
            ->get();

        // Demographics summary
        $maleCount = $responses->filter(fn($r) => $r->respondent?->jenis_kelamin === 'L')->count();
        $femaleCount = $responses->filter(fn($r) => $r->respondent?->jenis_kelamin === 'P')->count();

        $context .= "DEMOGRAPHICS:\n";
        $context .= "- Male: {$maleCount}\n";
        $context .= "- Female: {$femaleCount}\n\n";

        $context .= "QUESTIONS:\n";
        foreach ($questionnaire->questions as $question) {
            $context .= "Q{$question->order}: {$question->question_text} (Type: {$question->question_type})\n";
            if ($question->options) {
                foreach ($question->options as $option) {
                    $context .= "  - {$option->option_text}\n";
                }
            }
        }

        // Add note about available data
        $context .= "\n\nNOTE: You have access to {$totalResponses} total responses with complete demographic and answer data.\n";
        $context .= "Use the data to generate insights, charts, and analysis as requested.\n";

        return $context;
    }

    /**
     * Build prompt untuk Gemini
     */
    private function buildPrompt(string $context, string $userPrompt): string
    {
        return <<<PROMPT
            Anda adalah data analyst ahli yang memahami survey data dan dapat memberikan insights mendalam dalam Bahasa Indonesia.

            CONTEXT DATA SURVEY:
            {$context}

            PERTANYAAN USER:
            {$userPrompt}

            INSTRUKSI PENTING:
            1. HANYA gunakan data yang ada di CONTEXT di atas. JANGAN membuat data fiktif atau asumsi.
            2. Hitung dan analisis berdasarkan data REAL dari responden yang tercantum.
            3. Jika pertanyaan meminta breakdown (gender, umur, lokasi), hitung dari data yang ada.
            4. Setiap angka dan persentase HARUS berdasarkan data aktual, bukan estimasi.
            5. Jawaban harus dalam Bahasa Indonesia yang jelas dan profesional.
            6. Format output: JSON ONLY (tanpa markdown, tanpa backticks)

            FORMAT OUTPUT:
            {
              "answer": "Jawaban dalam 2-3 paragraf dengan gaya percakapan natural. Contoh: Nah, kalau kita lihat dari survei pelayanan KTP ini, ada total 17 orang yang ikut mengisi. Dari jumlah tersebut, yang laki-laki cukup banyak yaitu 11 orang atau sekitar 65 persen, sedangkan perempuan ada 6 orang atau 35 persen. Jadi memang responden laki-laki lebih dominan di survei ini. Kalau soal waktu pembuatan KTP, menariknya jawaban cukup bervariasi. Ada 7 orang yang bilang prosesnya butuh 3-7 hari, 5 orang bilang lebih dari 2 minggu...",
              "data_driven": true,
              "map_relevant": true,
              "insights": [
                "Fakta 1 dengan angka pasti",
                "Fakta 2 dengan angka pasti",
                "Fakta 3 dengan angka pasti"
              ],
              "chart_specs": [
                {"type": "gender", "chart_type": "pie", "title": "Distribusi Jenis Kelamin"},
                {"type": "age_group", "chart_type": "vertical_bar", "title": "Distribusi Kelompok Umur"},
                {"type": "citizen_type", "chart_type": "pie", "title": "Distribusi Jenis Warga"},
                {"type": "question", "question_index": 0, "chart_type": "vertical_bar", "title": "Top 10: Pertanyaan 1"},
                {"type": "location", "chart_type": "horizontal_bar", "title": "Top 10 Lokasi Responden"}
              ]
            }

            CONTOH JAWABAN YANG BENAR (NATURAL & MENGALIR):
            \"Nah, dari survei ini ternyata ada 17 orang yang ikut lho. Kalau dilihat dari jenis kelaminnya, yang laki-laki lebih banyak yaitu 11 orang atau sekitar 65 persen, sedangkan perempuan ada 6 orang atau 35 persen dari total responden.\"

            PENTING:
            - Jawaban harus mengalir seperti cerita dalam paragraf, BUKAN bullet points atau list
            - JANGAN tambahkan saran, rekomendasi, atau kesimpulan di akhir
            - JANGAN gunakan kata "mungkin", "kira-kira", "estimasi"
            - Set "data_driven" ke true jika pertanyaan meminta analisis data dari survei (statistik, persentase, distribusi, dll)
            - Set "data_driven" ke false jika pertanyaan hanya menanyakan hal umum tidak terkait data (misal: "apa itu survei?", "bagaimana cara mengisi?", dll)
            - Fokus HANYA menjawab apa yang ditanya
            - Output PURE JSON tanpa ```json wrapper

            CHART SPECIFICATIONS (chart_specs):
            WAJIB GENERATE CHART yang relevan dengan pertanyaan! GUNAKAN CHART TYPE YANG VARIATIF!

            ATURAN CHART (WAJIB DIIKUTI):
            1. Jika user bertanya "berapa total responden" atau "breakdown" → WAJIB: gender (pie), age_group (vertical_bar), citizen_type (pie)
            2. Jika user menyebutkan "jenis kelamin" atau "gender" → WAJIB: gender (pie), age_group (vertical_bar)
            3. Jika user menyebutkan "umur" atau "usia" → WAJIB: age_group (vertical_bar), gender (pie)
            4. Jika user menyebutkan "lokasi", "daerah", "kelurahan", "desa" → WAJIB: location (horizontal_bar), gender (pie)
            5. Jika user menyebutkan "pendidikan" atau "education" → WAJIB TAMBAHKAN: occupation (horizontal_bar) karena pendidikan belum ada datanya
            6. Jika user menyebutkan "pekerjaan" atau "occupation" atau "profesi" → WAJIB TAMBAHKAN: occupation (horizontal_bar)
            7. Jika user tanya tentang jawaban suatu pertanyaan (Q1, Q2, dll) → TAMBAHKAN: question dengan index sesuai (vertical_bar atau horizontal_bar)
            8. Jika user minta "overview" atau "ringkasan lengkap" → BERIKAN SEMUA: gender (pie), age_group (vertical_bar), citizen_type (pie), occupation (horizontal_bar), location (horizontal_bar), question 0-2

            PENTING CHART TYPE:
            - GUNAKAN "pie" untuk: gender, citizen_type
            - GUNAKAN "vertical_bar" untuk: age_group, question (pertanyaan dengan 3-5 opsi)
            - GUNAKAN "horizontal_bar" untuk: occupation, location, question (pertanyaan dengan banyak opsi/top 10)
            - VARIASIKAN chart type - JANGAN semua bar!

            TIPE CHART YANG TERSEDIA (pilih yang sesuai dengan data):
            - "pie" → untuk distribusi kategorikal dengan 2-4 kategori (gender, citizen_type, dll)
            - "vertical_bar" → untuk perbandingan kategori sedang (umur 5 grup, question dengan 3-5 opsi)
            - "horizontal_bar" → untuk top 10, ranking, atau banyak kategori (lokasi, pekerjaan, question dengan 10+ opsi)
            - "line" → untuk trend atau urutan waktu
            - "stacked_bar" → untuk perbandingan breakdown multi-dimensi

            CONTOH REAL (PERHATIKAN CHART TYPE YANG VARIATIF):
            - Pertanyaan: "Berapa total responden breakdown gender dan umur"
              chart_specs: [
                {"type": "gender", "chart_type": "pie", "title": "Distribusi Jenis Kelamin"},
                {"type": "age_group", "chart_type": "vertical_bar", "title": "Distribusi Kelompok Umur"},
                {"type": "citizen_type", "chart_type": "pie", "title": "Jenis Warga"}
              ]

            - Pertanyaan: "identifikasi alasan pindah dan breakdown pekerjaan responden"
              chart_specs: [
                {"type": "gender", "chart_type": "pie", "title": "Distribusi Jenis Kelamin"},
                {"type": "age_group", "chart_type": "vertical_bar", "title": "Distribusi Kelompok Umur"},
                {"type": "occupation", "chart_type": "horizontal_bar", "title": "Top 10 Pekerjaan"},
                {"type": "question", "question_index": 7, "chart_type": "horizontal_bar", "title": "Alasan Pindah (Q8)"}
              ]

            - Pertanyaan: "Berapa lama pendatang tinggal di Jayapura dan breakdown lokasi serta pekerjaan"
              chart_specs: [
                {"type": "gender", "chart_type": "pie", "title": "Distribusi Jenis Kelamin"},
                {"type": "age_group", "chart_type": "vertical_bar", "title": "Distribusi Kelompok Umur"},
                {"type": "occupation", "chart_type": "horizontal_bar", "title": "Top 10 Pekerjaan"},
                {"type": "location", "chart_type": "horizontal_bar", "title": "Distribusi Lokasi Responden"},
                {"type": "question", "question_index": 0, "chart_type": "vertical_bar", "title": "Lama Tinggal (Q1)"}
              ]

            MINIMUM: Selalu generate minimal 3 chart dengan TYPE YANG BERBEDA-BEDA
            WAJIB: Jika user minta "pekerjaan" atau "occupation" → HARUS ada occupation chart!
            MAKSIMUM: 8 chart
            MAKSIMUM: 8 chart

            PROMPT;
    }

    /**
     * Public method untuk call AI dengan prompt
     */
    public function callAPI(string $prompt): string
    {
        return $this->callGeminiAPI($prompt);
    }

    /**
     * Call Gemini API dengan HTTP Client
     */
    private function callGeminiAPI(string $prompt): string
    {
        try {
            if ($this->useDirectGemini) {
                return $this->callDirectGeminiAPI($prompt);
            } else {
                return $this->callOpenRouterAPI($prompt);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Call Direct Google Gemini API
     */
    private function callDirectGeminiAPI(string $prompt): string
    {
        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'topK' => 40,
                'topP' => 0.95,
                'maxOutputTokens' => 8192,
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->apiUrl . '?key=' . $this->apiKey, $payload);

        if ($response->failed()) {
            throw new \Exception('Google Gemini API Error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        // Extract text from Gemini response
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if (empty($text)) {
            throw new \Exception('Empty response from Google Gemini API');
        }

        return $text;
    }

    /**
     * Call OpenRouter API
     */
    private function callOpenRouterAPI(string $prompt): string
    {
        $payload = [
            'model' => 'google/gemini-2.5-flash',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ];

        $response = Http::timeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json'
            ])
            ->post($this->apiUrl, $payload);

        if ($response->failed()) {
            throw new \Exception('OpenRouter API Error: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();

        // Extract text dari response structure OpenAI-compatible
        $text = $data['choices'][0]['message']['content'] ?? '';

        if (empty($text)) {
            throw new \Exception('Empty response from OpenRouter API');
        }

        return $text;
    }

    /**
     * Parse AI response
     */
    private function parseResponse(string $response): array
    {
        try {
            // Try parse as JSON
            $parsed = json_decode($response, true);

            if (is_array($parsed)) {
                return $parsed;
            }

            // Fallback: return raw
            return ['raw' => $response];
        } catch (\Exception $e) {
            return ['raw' => $response, 'parse_error' => $e->getMessage()];
        }
    }

    /**
     * Generate raw data untuk Tab 1
     */
    private function generateRawData(
        Questionnaire $questionnaire,
        $responses,
        array $aiResponse
    ): array {
        $totalResponses = $responses->count();
        $answersCount = 0;

        // Build detail responden untuk tab Data
        $respondentDetails = [];

        foreach ($responses as $response) {
            $answersCount += $response->answers->count();
            $respondent = $response->respondent;

            if (!$respondent)
                continue;

            $answers = [];
            foreach ($response->answers as $answer) {
                $answers[] = [
                    'question' => $answer->question->question_text,
                    'answer' => $answer->selectedOption?->option_text ?? $answer->answer_text ?? 'N/A'
                ];
            }

            // Calculate age from tanggal_lahir
            $age = 'N/A';
            if ($respondent->tanggal_lahir) {
                $birthDate = \Carbon\Carbon::parse($respondent->tanggal_lahir);
                $age = $birthDate->age;
            }

            $respondentDetails[] = [
                'nama' => $respondent->nama_lengkap,
                'jenis_kelamin' => $respondent->jenis_kelamin ?? 'N/A',
                'umur' => $age,
                'desa' => $respondent->village?->name ?? 'N/A',
                'kecamatan' => $respondent->district?->name ?? 'N/A',
                'kabupaten' => $respondent->regency?->name ?? 'N/A',
                'latitude' => $respondent->latitude,
                'longitude' => $respondent->longitude,
                'jawaban' => $answers
            ];
        }

        return [
            'questionnaire_title' => $questionnaire->title,
            'total_responses' => $totalResponses,
            'total_questions' => $questionnaire->questions->count(),
            'total_answers' => $answersCount,
            'ai_analysis' => $aiResponse['answer'] ?? 'No analysis available',
            'insights' => $aiResponse['insights'] ?? [],
            'respondent_details' => $respondentDetails,  // Data detail responden
        ];
    }

    /**
     * Generate chart data untuk Tab 2 - Multiple charts dashboard
     * Menggunakan chart_specs dari AI untuk menentukan chart yang relevan
     */
    private function generateChartData(
        Questionnaire $questionnaire,
        $responses,
        array $aiResponse
    ): array {
        $charts = [];

        // Ambil chart_specs dari AI response
        $chartSpecs = $aiResponse['chart_specs'] ?? [];

        // Jika tidak ada chart_specs (backward compatibility), gunakan default dengan variasi
        if (empty($chartSpecs)) {
            $chartSpecs = [
                ['type' => 'gender', 'chart_type' => 'pie'],
                ['type' => 'age_group', 'chart_type' => 'vertical_bar'],
                ['type' => 'citizen_type', 'chart_type' => 'pie'],
                ['type' => 'occupation', 'chart_type' => 'horizontal_bar'],
                ['type' => 'education', 'chart_type' => 'pie'],
                ['type' => 'question', 'question_index' => 0, 'chart_type' => 'vertical_bar'],
                ['type' => 'question', 'question_index' => 1, 'chart_type' => 'horizontal_bar'],
                ['type' => 'location', 'chart_type' => 'horizontal_bar'],
            ];
        }

        // Generate charts berdasarkan specs dari AI
        foreach ($chartSpecs as $spec) {
            $chartData = $this->getChartByType($spec, $questionnaire, $responses);
            if (!empty($chartData) && !empty($chartData['labels'])) {
                $charts[] = $chartData;
            }
        }

        return $charts;
    }

    /**
     * Get chart berdasarkan type specification
     */
    private function getChartByType(array $spec, Questionnaire $questionnaire, $responses): array
    {
        $type = $spec['type'] ?? '';
        $chartType = $spec['chart_type'] ?? 'vertical_bar'; // Default chart type

        $chartData = [];
        switch ($type) {
            case 'gender':
                $chartData = $this->getGenderDistribution($responses);
                break;

            case 'age_group':
                $chartData = $this->getAgeGroupDistribution($responses);
                break;

            case 'citizen_type':
                $chartData = $this->getCitizenTypeDistribution($responses);
                break;

            case 'location':
                $chartData = $this->getLocationDistribution($responses);
                break;

            case 'education':
                $chartData = $this->getEducationDistribution($responses);
                break;

            case 'occupation':
                $chartData = $this->getOccupationDistribution($responses);
                break;

            case 'question':
                $questionIndex = $spec['question_index'] ?? 0;
                $question = $questionnaire->questions()->with('options')->skip($questionIndex)->first();
                if ($question) {
                    $chartData = $this->getQuestionAnswerDistribution($question, $responses);
                }
                break;

            default:
                return [];
        }

        // Override chart type from spec
        if (!empty($chartData) && isset($spec['chart_type'])) {
            $chartData['type'] = $spec['chart_type'];
        }

        // Override title from spec
        if (!empty($chartData) && isset($spec['title'])) {
            $chartData['title'] = $spec['title'];
        }

        return $chartData;
    }

    /**
     * Gender distribution chart
     */
    private function getGenderDistribution($responses): array
    {
        $genderCounts = ['Laki-laki' => 0, 'Perempuan' => 0];

        foreach ($responses as $response) {
            $gender = $response->respondent?->jenis_kelamin ?? '';
            if ($gender === 'L') {
                $genderCounts['Laki-laki']++;
            } elseif ($gender === 'P') {
                $genderCounts['Perempuan']++;
            }
        }

        return [
            'type' => 'pie',
            'title' => 'Distribusi Jenis Kelamin',
            'labels' => array_keys($genderCounts),
            'series' => array_values($genderCounts),
        ];
    }

    /**
     * Age group distribution chart
     */
    private function getAgeGroupDistribution($responses): array
    {
        $ageGroups = [
            '< 20' => 0,
            '20-30' => 0,
            '31-40' => 0,
            '41-50' => 0,
            '> 50' => 0,
        ];

        foreach ($responses as $response) {
            // Calculate age from tanggal_lahir
            $age = 0;
            if ($response->respondent && $response->respondent->tanggal_lahir) {
                $birthDate = \Carbon\Carbon::parse($response->respondent->tanggal_lahir);
                $age = $birthDate->age;
            }

            if ($age < 20) {
                $ageGroups['< 20']++;
            } elseif ($age <= 30) {
                $ageGroups['20-30']++;
            } elseif ($age <= 40) {
                $ageGroups['31-40']++;
            } elseif ($age <= 50) {
                $ageGroups['41-50']++;
            } else {
                $ageGroups['> 50']++;
            }
        }

        return [
            'type' => 'bar',
            'title' => 'Distribusi Kelompok Umur',
            'labels' => array_keys($ageGroups),
            'series' => array_values($ageGroups),
        ];
    }

    /**
     * Citizen type distribution chart
     */
    private function getCitizenTypeDistribution($responses): array
    {
        $citizenTypeCounts = [];

        foreach ($responses as $response) {
            $citizenType = $response->respondent?->citizenType?->name ?? 'Tidak Diketahui';
            if (!isset($citizenTypeCounts[$citizenType])) {
                $citizenTypeCounts[$citizenType] = 0;
            }
            $citizenTypeCounts[$citizenType]++;
        }

        // Sort by count desc
        arsort($citizenTypeCounts);

        return [
            'type' => 'bar',
            'title' => 'Distribusi Jenis Warga',
            'labels' => array_keys($citizenTypeCounts),
            'series' => array_values($citizenTypeCounts),
        ];
    }

    /**
     * Question answer distribution chart (Top 10)
     */
    private function getQuestionAnswerDistribution($question, $responses): array
    {
        if (!$question || !$question->options || $question->options->count() === 0) {
            return [];
        }

        $answerCounts = [];

        foreach ($question->options as $option) {
            $count = $responses->sum(function ($response) use ($option) {
                return $response
                    ->answers
                    ->where('question_id', $option->question_id)
                    ->where('selected_option_id', $option->id)
                    ->count();
            });

            if ($count > 0) {
                $answerCounts[$option->option_text] = $count;
            }
        }

        // Sort by count desc and take top 10
        arsort($answerCounts);
        $answerCounts = array_slice($answerCounts, 0, 10, true);

        return [
            'type' => 'bar',
            'title' => 'Top 10: ' . $question->question_text,
            'labels' => array_keys($answerCounts),
            'series' => array_values($answerCounts),
        ];
    }

    /**
     * Location distribution chart (Top 10)
     */
    private function getLocationDistribution($responses): array
    {
        $locationCounts = [];

        foreach ($responses as $response) {
            $location = $response->respondent?->district?->name ?? 'Unknown';
            if (!isset($locationCounts[$location])) {
                $locationCounts[$location] = 0;
            }
            $locationCounts[$location]++;
        }

        // Sort by count desc and take top 10
        arsort($locationCounts);
        $locationCounts = array_slice($locationCounts, 0, 10, true);

        return [
            'type' => 'horizontal_bar',
            'title' => 'Top 10 Lokasi Responden',
            'labels' => array_keys($locationCounts),
            'series' => array_values($locationCounts),
        ];
    }

    /**
     * Education distribution chart
     */
    private function getEducationDistribution($responses): array
    {
        $educationCounts = [];

        foreach ($responses as $response) {
            // Use education relationship
            $education = $response->respondent?->education?->education ?? 'Tidak Diketahui';

            // Skip if empty or null
            if (empty(trim($education))) {
                $education = 'Tidak Diketahui';
            }

            if (!isset($educationCounts[$education])) {
                $educationCounts[$education] = 0;
            }
            $educationCounts[$education]++;
        }

        // Sort by count desc
        arsort($educationCounts);

        return [
            'type' => 'pie',
            'title' => 'Distribusi Pendidikan Responden',
            'labels' => array_keys($educationCounts),
            'series' => array_values($educationCounts),
        ];
    }

    /**
     * Occupation distribution chart
     */
    private function getOccupationDistribution($responses): array
    {
        $occupationCounts = [];

        foreach ($responses as $response) {
            // Use occupation relationship
            $occupation = $response->respondent?->occupation?->occupation ?? 'Tidak Diketahui';

            // Skip if empty or null
            if (empty(trim($occupation))) {
                $occupation = 'Tidak Diketahui';
            }

            if (!isset($occupationCounts[$occupation])) {
                $occupationCounts[$occupation] = 0;
            }
            $occupationCounts[$occupation]++;
        }

        // Sort by count desc and take top 10
        arsort($occupationCounts);
        $occupationCounts = array_slice($occupationCounts, 0, 10, true);

        return [
            'type' => 'horizontal_bar',
            'title' => 'Top 10 Pekerjaan Responden',
            'labels' => array_keys($occupationCounts),
            'series' => array_values($occupationCounts),
        ];
    }

    /**
     * Generate map data untuk Tab 3
     */
    private function generateMapData($responses): array
    {
        $markers = [];
        $validMarkers = 0;

        foreach ($responses as $response) {
            $respondent = $response->respondent;

            // Ambil GPS dari respondent, bukan dari response
            if ($respondent && !empty($respondent->latitude) && !empty($respondent->longitude)) {
                // Calculate age from tanggal_lahir
                $age = '-';
                if ($respondent->tanggal_lahir) {
                    $birthDate = \Carbon\Carbon::parse($respondent->tanggal_lahir);
                    $age = $birthDate->age;
                }

                $popup = '<div class="p-2">';
                $popup .= '<strong>' . ($respondent->nama_lengkap ?? 'Unknown') . '</strong><br>';
                $popup .= 'Gender: ' . ($respondent->jenis_kelamin ?? '-') . '<br>';
                $popup .= 'Umur: ' . $age . ' tahun<br>';
                $popup .= 'Lokasi: ' . ($respondent->village?->name ?? '-') . ', ' . ($respondent->district?->name ?? '-');
                $popup .= '</div>';

                $markers[] = [
                    'lat' => (float) $respondent->latitude,
                    'lng' => (float) $respondent->longitude,
                    'popup' => $popup,
                ];
                $validMarkers++;
            }
        }

        // Default to Jayapura center if no markers
        $center = [-2.5359, 140.7186];  // Jayapura, Papua

        // Calculate center from markers if available
        if ($validMarkers > 0) {
            $avgLat = array_sum(array_column($markers, 'lat')) / $validMarkers;
            $avgLng = array_sum(array_column($markers, 'lng')) / $validMarkers;
            $center = [$avgLat, $avgLng];
        }

        return [
            'center' => $center,
            'zoom' => $validMarkers > 0 ? 12 : 11,
            'markers' => $markers,
            'total_markers' => $validMarkers,
        ];
    }
}
