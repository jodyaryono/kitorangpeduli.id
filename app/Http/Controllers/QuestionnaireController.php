<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Family;
use App\Models\FamilyHealthResponse;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Response;
use App\Models\Resident;
use App\Models\ResidentHealthResponse;
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
            : session('resident');

        // Check if logged in (either as respondent or officer-assisted)
        if (!$respondentData && !auth()->check()) {
            session(['intended_questionnaire' => $id]);
            return redirect()
                ->route('login', ['intended' => 'questionnaire', 'id' => $id])
                ->with('info', 'Silakan masuk atau daftar terlebih dahulu untuk mengisi survey.');
        }

        $questionnaire = Questionnaire::with([
            'questions' => function ($query) {
                $query->whereNull('parent_section_id')
                    ->orderBy('order')
                    ->with([
                        'options',
                        'childQuestions' => function ($q) {
                            $q->orderBy('order')->with('options');
                        },
                    ]);
            },
            'opd'
        ])->findOrFail($id);

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        // For officer_assisted with target_type=family, resident_id can be null initially
        if ($questionnaire->visibility === 'officer_assisted' && $questionnaire->target_type === 'family') {
            // Check if response_id is provided in query parameter (from "Lanjutkan" button)
            $responseId = request('response_id');

            if ($responseId) {
                // IMPORTANT: When "Lanjutkan" button is clicked, MUST use the existing response
                // DO NOT create new response
                $response = Response::where('id', $responseId)
                    ->where('questionnaire_id', $id)
                    ->where('entered_by_user_id', auth()->id())
                    ->where('status', 'in_progress')
                    ->first();

                if (!$response) {
                    // Response not found or not accessible - redirect back with error
                    \Log::error('Response not found or not accessible', [
                        'response_id' => $responseId,
                        'questionnaire_id' => $id,
                        'user_id' => auth()->id()
                    ]);

                    return redirect()->route('officer.entry')
                        ->with('error', 'Response tidak ditemukan atau sudah tidak valid. Silakan mulai entry baru.');
                }

                \Log::info('✅ Continuing existing response (from Lanjutkan button)', [
                    'response_id' => $response->id,
                    'resident_id' => $response->resident_id
                ]);
            } else {
                // No response_id provided - this is a NEW entry (NOT from "Lanjutkan" button)
                // ALWAYS create a NEW response for NEW entry
                $response = Response::create([
                    'questionnaire_id' => $id,
                    'resident_id' => null,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'entered_by_user_id' => auth()->id(),
                ]);

                \Log::info('✅ Created NEW response (starting fresh)', [
                    'response_id' => $response->id,
                    'questionnaire_id' => $id,
                    'user_id' => auth()->id()
                ]);
            }
        } else {
            // Regular flow with resident_id
            // Check if already completed
            $existingResponse = Response::where('questionnaire_id', $id)
                ->where('resident_id', $respondentId)
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
                ->where('resident_id', $respondentId)
                ->where('status', 'in_progress')
                ->first();

            if (!$response) {
                $response = Response::create([
                    'questionnaire_id' => $id,
                    'resident_id' => $respondentId,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'entered_by_user_id' => $isOfficerAssisted ? auth()->id() : null,
                ]);
            }
        }

        // Flatten all questions for counting actual questions (non-sections)
        $actualQuestions = collect();
        foreach ($questionnaire->questions as $section) {
            foreach ($section->childQuestions as $child) {
                if (!$child->is_section) {
                    $actualQuestions->push($child);
                }
            }
        }

        // Load existing answers for this response
        $existingAnswers = Answer::where('response_id', $response->id)
            ->get()
            ->keyBy('question_id');

        // Load saved residents data (all fields needed for form display)
        // For officer-assisted family questionnaires, we need to find family_id differently
        $savedResidents = [];
        $savedFamily = null;
        $familyId = null;

        // Try to get family_id from response->resident
        if ($response->resident && $response->resident->family_id) {
            $familyId = $response->resident->family_id;
        } else {
            // If resident_id is NULL, try to find family_id from any resident linked to this response
            // through resident_health_responses table
            $healthResponse = \App\Models\ResidentHealthResponse::where('response_id', $response->id)
                ->with('resident')
                ->first();

            if ($healthResponse && $healthResponse->resident && $healthResponse->resident->family_id) {
                $familyId = $healthResponse->resident->family_id;
            }
        }

        if ($familyId) {
            // Load family data (no_kk, alamat, kk_image, wilayah, etc.)
            $family = \App\Models\Family::find($familyId);
            if ($family) {
                $savedFamily = [
                    'id' => $family->id,
                    'no_kk' => $family->no_kk,
                    'kepala_keluarga' => $family->kepala_keluarga,
                    'alamat' => $family->alamat,
                    'rt' => $family->rt,
                    'rw' => $family->rw,
                    'no_bangunan' => $family->no_bangunan,
                    'kode_pos' => $family->kode_pos,
                    'kk_image_path' => $family->kk_image_path,
                    'province_id' => $family->province_id,
                    'regency_id' => $family->regency_id,
                    'district_id' => $family->district_id,
                    'village_id' => $family->village_id,
                    'puskesmas_id' => $family->puskesmas_id,
                ];
            }

            // Load residents data
            $savedResidents = \App\Models\Resident::where('family_id', $familyId)
                ->get()
                ->map(function($resident, $index) {
                    return [
                        'id' => $resident->id,
                        'nik' => $resident->nik,
                        'citizen_type_id' => $resident->citizen_type_id,
                        'nama_lengkap' => $resident->nama_lengkap,
                        'hubungan' => $resident->family_relation_id, // family relationship
                        'tempat_lahir' => $resident->tempat_lahir,
                        'tanggal_lahir' => $resident->tanggal_lahir ? \Carbon\Carbon::parse($resident->tanggal_lahir)->format('d/m/Y') : null,
                        'jenis_kelamin' => $resident->jenis_kelamin, // '1' or '2'
                        'status_perkawinan' => $resident->marital_status_id,
                        'agama' => $resident->religion_id,
                        'pendidikan' => $resident->education_id,
                        'pekerjaan' => $resident->occupation_id,
                        'golongan_darah' => $resident->golongan_darah,
                        'phone' => $resident->phone,
                        'ktp_image_path' => $resident->ktp_image_path,
                        'ktp_kia_path' => $resident->ktp_kia_path,
                        'umur' => $resident->tanggal_lahir ? \Carbon\Carbon::parse($resident->tanggal_lahir)->age : null,
                    ];
                })
                ->values()
                ->toArray();
        }

        // Load saved health data Section V (per-person) from resident_health_responses table
        $savedHealthData = [];
        if ($familyId) {
            $residents = \App\Models\Resident::where('family_id', $familyId)->get();

            foreach ($residents as $index => $resident) {
                $memberId = $index + 1; // Frontend uses 1-based member IDs

                // Get all health responses for this resident
                $healthResponses = \App\Models\ResidentHealthResponse::where('resident_id', $resident->id)
                    ->where('response_id', $response->id)
                    ->get()
                    ->keyBy('question_code');

                // Format as expected by frontend
                if ($healthResponses->isNotEmpty()) {
                    $savedHealthData[$memberId] = [];
                    foreach ($healthResponses as $code => $healthResponse) {
                        $savedHealthData[$memberId][$code] = $healthResponse->answer;
                    }
                }
            }
        }

        // Load saved Section VI data (per-family) from family_health_responses table
        $savedSectionVIData = [];
        if ($familyId) {
            $familyHealthResponses = FamilyHealthResponse::where('family_id', $familyId)
                ->where('response_id', $response->id)
                ->get()
                ->keyBy('question_code');

            foreach ($familyHealthResponses as $code => $healthResponse) {
                // Try to decode JSON answers (for checkbox/table types)
                $answer = $healthResponse->answer;
                
                // Clean the answer to prevent JavaScript syntax errors
                if (is_string($answer)) {
                    // Remove any potential problematic characters
                    $answer = str_replace(["\r\n", "\r", "\n"], ' ', $answer);
                }
                
                $decoded = json_decode($answer, true);
                $savedSectionVIData[$code] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $answer;
            }
        }

        // Ensure all data is JSON-safe
        $savedResidents = collect($savedResidents)->map(function($resident) {
            if (is_array($resident) || is_object($resident)) {
                return collect($resident)->map(function($value) {
                    return is_string($value) ? str_replace(["\r\n", "\r", "\n"], ' ', $value) : $value;
                })->toArray();
            }
            return $resident;
        })->toArray();

        return view('questionnaire.fill', compact('questionnaire', 'response', 'existingAnswers', 'isOfficerAssisted', 'respondentData', 'actualQuestions', 'savedResidents', 'savedHealthData', 'savedSectionVIData', 'savedFamily'));
    }

    public function autosave(Request $request, $id)
    {
        try {
            // Try to get response directly from response_id in request
            $responseId = $request->response_id;

            if ($responseId) {
                // Direct lookup by response_id (for officer-assisted entries)
                $response = Response::where('id', $responseId)
                    ->where('questionnaire_id', $id)
                    ->where('status', 'in_progress')
                    ->first();
            } else {
                // Fallback to session-based lookup
                $isOfficerAssisted = session('officer_assisted', false);
                $respondentData = $isOfficerAssisted
                    ? session('officer_respondent')
                    : session('resident');

                if (!$respondentData && !auth()->check()) {
                    return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
                }

                $respondentId = $respondentData['id'] ?? session('respondent.id');

                $response = Response::where('questionnaire_id', $id)
                    ->where('resident_id', $respondentId)
                    ->where('status', 'in_progress')
                    ->first();
            }

            if (!$response) {
                \Log::error('Autosave failed: Response not found', [
                    'questionnaire_id' => $id,
                    'response_id' => $responseId ?? null,
                    'request' => $request->all()
                ]);
                return response()->json(['success' => false, 'message' => 'Response not found'], 404);
            }

            $questionId = $request->question_id;

            // Handle officer_notes (special case - not a question)
            if ($questionId === 'officer_notes') {
                $response->update([
                    'officer_notes' => $request->answer
                ]);
                \Log::info('Officer notes saved', ['response_id' => $response->id]);
                return response()->json(['success' => true, 'message' => 'Officer notes saved']);
            }

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $fileType = $request->file_type ?? 'file';

                // Store file
                $path = $file->store('questionnaire_uploads', 'public');

                // Save file path as answer
                $answerData = [
                    'response_id' => $response->id,
                    'question_id' => $questionId,
                    'answer_text' => $file->getClientOriginalName(),
                    'media_path' => $path,
                ];

                Answer::updateOrCreate(
                    ['response_id' => $response->id, 'question_id' => $questionId],
                    $answerData
                );

                \Log::info('File uploaded', [
                    'question_id' => $questionId,
                    'path' => $path,
                    'type' => $fileType,
                    'original_name' => $file->getClientOriginalName()
                ]);

                // Sync to families table if this is a family-related answer
                $this->syncFamilyData($response);

                return response()->json(['success' => true, 'message' => 'File saved', 'path' => $path]);
            }

            // Handle regular answer
            $value = $request->answer;

            \Log::info('Autosave received', [
                'question_id' => $questionId,
                'raw_value' => $value,
                'is_string' => is_string($value),
                'is_array' => is_array($value)
            ]);

            // Decode if JSON (for checkboxes and location data)
            if (is_string($value)) {
                $decoded = json_decode($value, true); // Decode as associative array
                if ($decoded !== null && json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                    \Log::info('Decoded JSON value', ['decoded' => $value]);
                }
            }

            $answerData = [
                'response_id' => $response->id,
                'question_id' => $questionId,
            ];

            if (is_array($value)) {
                // Check if it's an associative array (like location data with lat/lng)
                if (array_keys($value) !== range(0, count($value) - 1)) {
                    // Associative array - save as JSON text
                    $answerData['answer_text'] = json_encode($value);
                    \Log::info('Saving associative array as answer_text', ['json' => $answerData['answer_text']]);
                } else {
                    // Indexed array - save as selected_options
                    $answerData['selected_options'] = json_encode($value);
                    \Log::info('Saving indexed array as selected_options', ['json' => $answerData['selected_options']]);
                }
            } else {
                $answerData['answer_text'] = $value;
                \Log::info('Saving as answer_text', ['text' => $value]);
            }

            Answer::updateOrCreate(
                ['response_id' => $response->id, 'question_id' => $questionId],
                $answerData
            );

            // Sync to families table if this is a family-related answer
            $this->syncFamilyData($response);

            return response()->json(['success' => true, 'message' => 'Saved']);
        } catch (\Exception $e) {
            \Log::error('Autosave error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save health data for family members
     */
    public function saveHealthData(Request $request, $id)
    {
        try {
            \Log::info('saveHealthData called', ['request' => $request->all()]);

            $responseId = $request->input('response_id');
            $healthData = $request->input('health');

            if (!$responseId) {
                return response()->json(['success' => false, 'message' => 'Response ID required'], 400);
            }

            $response = Response::findOrFail($responseId);

            if (is_array($healthData)) {
                foreach ($healthData as $memberId => $data) {
                    // Get family_id from response's resident
                    $familyId = null;
                    if ($response->resident && $response->resident->family_id) {
                        $familyId = $response->resident->family_id;
                    } else {
                        // If no resident linked, try to find via resident_health_responses
                        $existingHealthResponse = ResidentHealthResponse::where('response_id', $responseId)
                            ->with('resident')
                            ->first();
                        if ($existingHealthResponse && $existingHealthResponse->resident) {
                            $familyId = $existingHealthResponse->resident->family_id;
                        }
                    }

                    if (!$familyId) {
                        \Log::warning('Cannot find family_id for health data save', [
                            'response_id' => $responseId,
                            'member_id' => $memberId
                        ]);
                        continue;
                    }

                    // Find residents for this family
                    $residents = Resident::where('family_id', $familyId)
                        ->orderBy('id')
                        ->get();

                    // Get resident by index (memberId - 1 because memberId starts at 1)
                    $residentIndex = intval($memberId) - 1;

                    if (isset($residents[$residentIndex])) {
                        $resident = $residents[$residentIndex];

                        // Save each health answer to resident_health_responses table
                        foreach ($data as $questionCode => $answer) {
                            if (!empty($answer)) {
                                ResidentHealthResponse::updateOrCreate(
                                    [
                                        'resident_id' => $resident->id,
                                        'response_id' => $responseId,
                                        'question_code' => $questionCode,
                                    ],
                                    [
                                        'answer' => $answer,
                                    ]
                                );
                            }
                        }
                        \Log::info('Health data saved to resident_health_responses', [
                            'resident_id' => $resident->id,
                            'member_id' => $memberId,
                            'data' => $data
                        ]);
                    } else {
                        \Log::warning('Resident not found for health data', [
                            'member_id' => $memberId,
                            'total_residents' => $residents->count()
                        ]);
                    }
                }
            }

            \Log::info('Health data saved to resident_health_responses table', ['response_id' => $responseId]);

            return response()->json([
                'success' => true,
                'message' => 'Health data saved'
            ]);
        } catch (\Exception $e) {
            \Log::error('Save health data error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Save Section VI (Kuesioner Tambahan) health data for FAMILY (per-keluarga)
     * This saves data from the dynamic health questions loaded from health_questions table
     * Section VI is per-family, not per-member
     */
    public function saveHealthVI(Request $request, $id)
    {
        try {
            \Log::info('saveHealthVI called', ['request' => $request->all()]);

            $responseId = $request->input('response_id');
            $sectionVIData = json_decode($request->input('section_vi_data'), true);

            if (!$responseId) {
                return response()->json(['success' => false, 'message' => 'Response ID required'], 400);
            }

            $response = Response::findOrFail($responseId);

            // Get family_id from response's resident
            $familyId = null;
            if ($response->resident && $response->resident->family_id) {
                $familyId = $response->resident->family_id;
            } else {
                // If no resident linked, try to find via family_health_responses
                $existingHealthResponse = FamilyHealthResponse::where('response_id', $responseId)->first();
                if ($existingHealthResponse) {
                    $familyId = $existingHealthResponse->family_id;
                }
            }

            // If still no family_id, create/get from first resident
            if (!$familyId) {
                $existingResident = ResidentHealthResponse::where('response_id', $responseId)
                    ->with('resident')
                    ->first();
                if ($existingResident && $existingResident->resident) {
                    $familyId = $existingResident->resident->family_id;
                }
            }

            if (!$familyId) {
                \Log::warning('Cannot find family_id for Section VI data save', [
                    'response_id' => $responseId
                ]);
                return response()->json(['success' => false, 'message' => 'Family not found'], 400);
            }

            // Save each Section VI answer to family_health_responses table (per-family)
            if (is_array($sectionVIData)) {
                foreach ($sectionVIData as $questionCode => $answer) {
                    if (!empty($answer) || $answer === '0' || $answer === 0) {
                        // If answer is an array (checkbox or table), store as JSON
                        $answerValue = is_array($answer) ? json_encode($answer) : $answer;

                        FamilyHealthResponse::updateOrCreate(
                            [
                                'family_id' => $familyId,
                                'response_id' => $responseId,
                                'question_code' => $questionCode,
                            ],
                            [
                                'answer' => $answerValue,
                            ]
                        );
                    }
                }

                \Log::info('Section VI data saved to family_health_responses', [
                    'family_id' => $familyId,
                    'response_id' => $responseId,
                    'question_count' => count($sectionVIData)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Section VI data saved'
            ]);
        } catch (\Exception $e) {
            \Log::error('Save Section VI data error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function saveFamilyMembers(Request $request, $id)
    {
        try {
            \Log::info('saveFamilyMembers called', ['request_keys' => array_keys($request->all())]);

            $responseId = $request->input('response_id');
            $familyMembersData = json_decode($request->input('family_members'), true);

            // Handle uploaded KTP files
            $ktpFiles = [];
            foreach ($request->allFiles() as $key => $file) {
                if (str_starts_with($key, 'ktp_')) {
                    $memberId = str_replace('ktp_', '', $key);
                    $ktpFiles[$memberId] = $file;
                    \Log::info('Found KTP file', ['member_id' => $memberId, 'filename' => $file->getClientOriginalName()]);
                }
            }

            \Log::info('Parsed data', ['response_id' => $responseId, 'members_count' => count($familyMembersData ?? []), 'ktp_files' => count($ktpFiles)]);

            if (!$responseId || !$familyMembersData) {
                \Log::error('Invalid data', ['response_id' => $responseId, 'has_members' => !empty($familyMembersData)]);
                return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
            }

            $response = Response::findOrFail($responseId);

            // Get family - try multiple ways
            $family = null;

            // Method 1: Via resident (if response has resident_id)
            $resident = $response->resident;
            \Log::info('Method 1 - Via resident', ['has_resident' => !is_null($resident), 'family_id' => $resident?->family_id]);

            if ($resident && $resident->family_id) {
                $family = Family::find($resident->family_id);
                \Log::info('Found family via resident', ['family_id' => $family?->id]);
            }

            // Method 2: Find family by no_kk from answers (for officer-assisted entries)
            if (!$family) {
                \Log::info('Method 2 - Trying to find family by no_kk');

                // Get question IDs for no_kk (Q268 or Q223)
                $noKkQuestionIds = [268, 223]; // Based on previous mapping

                $noKkAnswer = Answer::where('response_id', $response->id)
                    ->whereIn('question_id', $noKkQuestionIds)
                    ->whereNotNull('answer_text')
                    ->first();

                \Log::info('no_kk answer', ['answer' => $noKkAnswer?->answer_text]);

                if ($noKkAnswer && !empty($noKkAnswer->answer_text)) {
                    $family = Family::where('no_kk', $noKkAnswer->answer_text)->first();
                    \Log::info('Found family by no_kk', ['family_id' => $family?->id, 'no_kk' => $noKkAnswer->answer_text]);
                }
            }

            // Method 3: Create new family if not found (for initial officer-assisted entries)
            if (!$family) {
                \Log::info('Method 3 - Creating new family for response', ['response_id' => $response->id]);

                // Get wilayah data from answers
                $wilayahData = $this->getWilayahFromAnswers($response->id);

                // Get no_kk and alamat from answers if available
                $noKkQuestionIds = [268, 223];
                $alamatQuestionIds = [270, 224]; // Adjust based on your question IDs

                $noKkAnswer = Answer::where('response_id', $response->id)
                    ->whereIn('question_id', $noKkQuestionIds)
                    ->whereNotNull('answer_text')
                    ->first();

                $alamatAnswer = Answer::where('response_id', $response->id)
                    ->whereIn('question_id', $alamatQuestionIds)
                    ->whereNotNull('answer_text')
                    ->first();

                $family = Family::create([
                    'province_id' => $wilayahData['province_id'] ?? null,
                    'regency_id' => $wilayahData['regency_id'] ?? null,
                    'district_id' => $wilayahData['district_id'] ?? null,
                    'village_id' => $wilayahData['village_id'] ?? null,
                    'no_kk' => $noKkAnswer?->answer_text ?? 'TEMP-' . $response->id,
                    'alamat' => $alamatAnswer?->answer_text ?? '',
                    'rt' => null,
                    'rw' => null,
                    'no_bangunan' => null,
                ]);

                \Log::info('Created new family', ['family_id' => $family->id, 'no_kk' => $family->no_kk]);
            }

            if ($family) {
                \Log::info('Updating/Creating residents for family', ['family_id' => $family->id, 'members_count' => count($familyMembersData)]);

                // Get existing residents for this family
                $existingResidents = \App\Models\Resident::where('family_id', $family->id)
                    ->orderBy('id')
                    ->get()
                    ->keyBy('id');

                $existingResidentIds = $existingResidents->pluck('id')->toArray();
                $updatedResidentIds = [];

                // Update or create resident records
                $memberIndex = 0;
                foreach ($familyMembersData as $memberKey => $memberData) {
                    $memberIndex++;

                    // Parse tanggal_lahir from d/m/Y format
                    $tanggalLahir = null;
                    if (isset($memberData['tanggal_lahir']) && !empty($memberData['tanggal_lahir'])) {
                        try {
                            $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', $memberData['tanggal_lahir'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            \Log::warning("Invalid date format for family member: " . $memberData['tanggal_lahir']);
                        }
                    }

                    // Get wilayah data from answers
                    $wilayahData = $this->getWilayahFromAnswers($response->id);

                    // jenis_kelamin: keep as '1' or '2' (no conversion needed)
                    $jenisKelamin = isset($memberData['jenis_kelamin']) ? trim($memberData['jenis_kelamin']) : null;

                    // Auto-calculate umur (age) from tanggal_lahir if available
                    $umur = null;
                    if ($tanggalLahir) {
                        try {
                            $umur = \Carbon\Carbon::parse($tanggalLahir)->age;
                        } catch (\Exception $e) {
                            \Log::warning('Failed to calculate age', ['date' => $tanggalLahir]);
                        }
                    }

                    // Helper function to convert empty string to null for integer fields
                    $toIntOrNull = function($value) {
                        if ($value === '' || $value === null) return null;
                        return is_numeric($value) ? (int)$value : null;
                    };

                    // Handle KTP file upload for this member
                    $ktpImagePath = null;
                    if (isset($ktpFiles[$memberKey])) {
                        $ktpFile = $ktpFiles[$memberKey];
                        $ktpImagePath = $ktpFile->store('ktp_images', 'public');
                        \Log::info('Stored KTP file', ['member_key' => $memberKey, 'path' => $ktpImagePath]);
                    }

                    // Prepare resident data
                    $residentData = [
                        'family_id' => $family->id,
                        'province_id' => $wilayahData['province_id'],
                        'regency_id' => $wilayahData['regency_id'],
                        'district_id' => $wilayahData['district_id'],
                        'village_id' => $wilayahData['village_id'],
                        'nama_lengkap' => strtoupper($memberData['nama_lengkap'] ?? '') ?: null,
                        'nik' => $memberData['nik'] ?: null,
                        'citizen_type_id' => $toIntOrNull($memberData['citizen_type_id'] ?? null),
                        'tempat_lahir' => strtoupper($memberData['tempat_lahir'] ?? '') ?: null,
                        'tanggal_lahir' => $tanggalLahir,
                        'umur' => $umur ?? $toIntOrNull($memberData['umur'] ?? null),
                        'jenis_kelamin' => $jenisKelamin,
                        'golongan_darah' => $memberData['golongan_darah'] ?: null,
                        'phone' => $memberData['phone'] ?: null,
                        'family_relation_id' => $toIntOrNull($memberData['hubungan'] ?? null),
                        'religion_id' => $toIntOrNull($memberData['agama'] ?? null),
                        'marital_status_id' => $toIntOrNull($memberData['status_perkawinan'] ?? null),
                        'education_id' => $toIntOrNull($memberData['pendidikan'] ?? null),
                        'occupation_id' => $toIntOrNull($memberData['pekerjaan'] ?? null),
                        'hubungan_keluarga' => $memberData['hubungan'] ?? null,
                        'agama' => $memberData['agama'] ?? null,
                        'status_kawin' => $memberData['status_perkawinan'] ?? null,
                        'pendidikan' => $memberData['pendidikan'] ?? null,
                        'pekerjaan' => $memberData['pekerjaan'] ?? null,
                    ];

                    // Only update ktp_image_path if a new file was uploaded
                    if ($ktpImagePath) {
                        $residentData['ktp_image_path'] = $ktpImagePath;
                    }

                    // Try to find existing resident by NIK or by position in family
                    $existingResident = null;

                    // First try by NIK if provided
                    if (!empty($memberData['nik'])) {
                        $existingResident = $existingResidents->first(function($r) use ($memberData) {
                            return $r->nik === $memberData['nik'];
                        });
                    }

                    // If not found by NIK, try to match by position (memberIndex)
                    if (!$existingResident && isset($existingResidentIds[$memberIndex - 1])) {
                        $existingResident = $existingResidents->get($existingResidentIds[$memberIndex - 1]);
                    }

                    // Update existing or create new resident
                    if ($existingResident) {
                        $existingResident->update($residentData);
                        $resident = $existingResident;
                        $updatedResidentIds[] = $resident->id;
                        \Log::info('Updated resident', [
                            'id' => $resident->id,
                            'nama' => $resident->nama_lengkap,
                        ]);
                    } else {
                        $resident = \App\Models\Resident::create($residentData);
                        $updatedResidentIds[] = $resident->id;
                        \Log::info('Created new resident', [
                            'id' => $resident->id,
                            'nama' => $resident->nama_lengkap,
                        ]);
                    }
                }

                // Delete residents that are no longer in the family (removed by user)
                $residentsToDelete = array_diff($existingResidentIds, $updatedResidentIds);
                if (!empty($residentsToDelete)) {
                    \App\Models\Resident::whereIn('id', $residentsToDelete)->delete();
                    \Log::info('Deleted removed residents', ['count' => count($residentsToDelete), 'ids' => $residentsToDelete]);
                }

                $residentsCount = \App\Models\Resident::where('family_id', $family->id)->count();
                \Log::info('Residents created successfully', ['count' => $residentsCount]);

                // Update response.resident_id with kepala keluarga (family_relation_id = 1)
                $kepalaKeluarga = \App\Models\Resident::where('family_id', $family->id)
                    ->where('family_relation_id', 1)
                    ->first();

                if ($kepalaKeluarga && !$response->resident_id) {
                    $response->update(['resident_id' => $kepalaKeluarga->id]);
                    \Log::info('Updated response.resident_id', [
                        'response_id' => $response->id,
                        'resident_id' => $kepalaKeluarga->id,
                        'nama' => $kepalaKeluarga->nama_lengkap
                    ]);
                }

                // Sync family data (wilayah, etc.) from questionnaire answers
                $this->syncFamilyData($response);

                // Load updated residents to return
                $residents = \App\Models\Resident::where('family_id', $family->id)
                    ->get()
                    ->map(function($resident, $index) {
                        return [
                            'id' => $resident->id,
                            'nik' => $resident->nik,
                            'citizen_type_id' => $resident->citizen_type_id,
                            'nama_lengkap' => $resident->nama_lengkap,
                            'hubungan' => $resident->family_relation_id,
                            'tempat_lahir' => $resident->tempat_lahir,
                            'tanggal_lahir' => $resident->tanggal_lahir ? \Carbon\Carbon::parse($resident->tanggal_lahir)->format('d/m/Y') : null,
                            'jenis_kelamin' => $resident->jenis_kelamin,
                            'status_perkawinan' => $resident->marital_status_id,
                            'agama' => $resident->religion_id,
                            'pendidikan' => $resident->education_id,
                            'pekerjaan' => $resident->occupation_id,
                            'golongan_darah' => $resident->golongan_darah,
                            'phone' => $resident->phone,
                            'ktp_image_path' => $resident->ktp_image_path,
                            'ktp_kia_path' => $resident->ktp_kia_path,
                            'umur' => $resident->tanggal_lahir ? \Carbon\Carbon::parse($resident->tanggal_lahir)->age : null,
                        ];
                    })
                    ->values()
                    ->toArray();

                return response()->json([
                    'success' => true,
                    'message' => 'Family members saved successfully',
                    'residents_count' => $residentsCount,
                    'family_id' => $family->id,
                    'residents' => $residents
                ]);
            } else {
                // Family not found yet, just save to response
                \Log::warning('Family not found for response', ['response_id' => $response->id]);
                return response()->json([
                    'success' => true,
                    'message' => 'Family members saved to response (family record will be created on KK upload)',
                    'residents_count' => 0
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Save family members error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function submit(Request $request, $id)
    {
        // Check if officer-assisted or regular respondent
        $isOfficerAssisted = session('officer_assisted', false);
        $respondentData = $isOfficerAssisted
            ? session('officer_respondent')
            : session('resident');

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
                ->where('resident_id', $respondentId)
                ->where('status', 'in_progress')
                ->first();

            if (!$response) {
                $response = Response::create([
                    'questionnaire_id' => $id,
                    'resident_id' => $respondentId,
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

            // Handle family members data
            if ($request->has('family_members')) {
                $familyMembersData = [];

                foreach ($request->family_members as $index => $memberData) {
                    $member = $memberData;

                    // Handle KTP/KIA file upload
                    if ($request->hasFile("family_members.{$index}.ktp_kia")) {
                        $file = $request->file("family_members.{$index}.ktp_kia");
                        $filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('ktp_kia', $filename, 'public');
                        $member['ktp_kia_path'] = $path;
                    }

                    $familyMembersData[] = $member;
                }

                // Create residents records from family members
                // Get family via resident (families don't have response_id)
                $resident = $response->resident;
                $family = null;

                if ($resident && $resident->family_id) {
                    $family = \App\Models\Family::find($resident->family_id);
                }

                if ($family) {
                    // Delete existing residents for this family to avoid duplicates
                    \App\Models\Resident::where('family_id', $family->id)->delete();

                    foreach ($familyMembersData as $memberData) {
                        // Parse tanggal_lahir from d/m/Y format
                        $tanggalLahir = null;
                        if (isset($memberData['tanggal_lahir']) && !empty($memberData['tanggal_lahir'])) {
                            try {
                                $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', $memberData['tanggal_lahir'])->format('Y-m-d');
                            } catch (\Exception $e) {
                                // If format is invalid, leave as null
                                \Log::warning("Invalid date format for family member: " . $memberData['tanggal_lahir']);
                            }
                        }

                        // Map jenis_kelamin: '1' or '2' from form -> 'L' or 'P' for database
                        $jenisKelamin = null;
                        if (isset($memberData['jenis_kelamin'])) {
                            $jk = trim($memberData['jenis_kelamin']);
                            if ($jk === '1') {
                                $jenisKelamin = 'L';  // 1 = Pria = L
                            } elseif ($jk === '2') {
                                $jenisKelamin = 'P';  // 2 = Wanita = P
                            } else {
                                // Fallback for text values
                                $jkLower = strtolower($jk);
                                if (str_contains($jkLower, 'laki') || str_contains($jkLower, 'pria') || $jkLower === 'l') {
                                    $jenisKelamin = 'L';
                                } elseif (str_contains($jkLower, 'perempuan') || str_contains($jkLower, 'wanita') || $jkLower === 'p') {
                                    $jenisKelamin = 'P';
                                }
                            }
                        }

                        // Auto-calculate umur (age) from tanggal_lahir if available
                        $umur = null;
                        if ($tanggalLahir) {
                            try {
                                $umur = \Carbon\Carbon::parse($tanggalLahir)->age;
                            } catch (\Exception $e) {
                                \Log::warning('Failed to calculate age', ['date' => $tanggalLahir]);
                            }
                        }

                        // Helper function to convert empty string to null for integer fields
                        $toIntOrNull = function($value) {
                            if ($value === '' || $value === null) return null;
                            return is_numeric($value) ? (int)$value : null;
                        };

                        // Get wilayah data from questionnaire answers
                        $wilayahData = $this->getWilayahFromAnswers($response->id);

                        // Create resident record with ALL fields (both ID and text columns for compatibility)
                        \App\Models\Resident::create([
                            'family_id' => $family->id,
                            'province_id' => $wilayahData['province_id'],
                            'regency_id' => $wilayahData['regency_id'],
                            'district_id' => $wilayahData['district_id'],
                            'village_id' => $wilayahData['village_id'],
                            'nama_lengkap' => strtoupper($memberData['nama_lengkap'] ?? '') ?: null,
                            'nik' => $memberData['nik'] ?: null,
                            'citizen_type_id' => $toIntOrNull($memberData['citizen_type_id'] ?? null),
                            'tempat_lahir' => strtoupper($memberData['tempat_lahir'] ?? '') ?: null,
                            'tanggal_lahir' => $tanggalLahir,
                            'umur' => $umur ?? $toIntOrNull($memberData['umur'] ?? null),
                            'jenis_kelamin' => $jenisKelamin,
                            'golongan_darah' => $memberData['golongan_darah'] ?: null,
                            'phone' => $memberData['phone'] ?: null,
                            // ID-based foreign key columns
                            'family_relation_id' => $toIntOrNull($memberData['hubungan'] ?? null),
                            'religion_id' => $toIntOrNull($memberData['agama'] ?? null),
                            'marital_status_id' => $toIntOrNull($memberData['status_perkawinan'] ?? null),
                            'education_id' => $toIntOrNull($memberData['pendidikan'] ?? null),
                            'occupation_id' => $toIntOrNull($memberData['pekerjaan'] ?? null),
                            // Text columns for backward compatibility
                            'hubungan_keluarga' => $memberData['hubungan'] ?? null,
                            'agama' => $memberData['agama'] ?? null,
                            'status_kawin' => $memberData['status_perkawinan'] ?? null,
                            'pendidikan' => $memberData['pendidikan'] ?? null,
                            'pekerjaan' => $memberData['pekerjaan'] ?? null,
                        ]);
                    }
                }
            }

            // Handle officer notes
            if ($request->has('officer_notes')) {
                $response->update([
                    'officer_notes' => $request->officer_notes
                ]);
            }

            // Mark as completed
            $response->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Sync family data one final time
            $this->syncFamilyData($response);

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
            : session('resident');

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        $response = Response::with(['questionnaire', 'resident'])
            ->where('id', $responseId)
            ->firstOrFail();

        // Verify ownership or officer access
        if (!$isOfficerAssisted && $response->resident_id != $respondentId) {
            abort(403, 'Unauthorized access');
        }

        return view('questionnaire.success', compact('response', 'isOfficerAssisted'));
    }

    /**
     * Sync family data from questionnaire answers to families table
     */
    private function syncFamilyData($response)
    {
        // Question IDs for family data (from questionnaire 8)
        $familyQuestionMap = [
            214 => 'province_id',      // Provinsi
            215 => 'regency_id',       // Kabupaten/Kota
            216 => 'district_id',      // Kecamatan
            217 => 'village_id',       // Desa/Kelurahan
            220 => 'rt',               // RT
            219 => 'rw',               // RW
            221 => 'no_bangunan',      // No. Bangunan
            225 => 'alamat',           // Alamat
            268 => 'no_kk',            // No. Keluarga (actually Nomor KK)
            223 => 'no_kk',            // Nomor Kartu Keluarga (KK) - fallback
            266 => 'kk_image_path',    // Upload Kartu Keluarga
            // Note: kepala_keluarga (Q269) not needed - will be in family members
        ];

        // Get all answers for this response
        $answers = Answer::where('response_id', $response->id)
            ->whereIn('question_id', array_keys($familyQuestionMap))
            ->get()
            ->keyBy('question_id');

        // Prepare family data
        $familyData = [];

        foreach ($familyQuestionMap as $questionId => $column) {
            $answer = $answers->get($questionId);
            if (!$answer) continue;

            if ($questionId == 266) {
                // File upload - use media_path
                if ($answer->media_path) {
                    $familyData[$column] = $answer->media_path;
                }
            } else {
                // Text/numeric answers
                $value = $answer->answer_text ?? $answer->answer_numeric;
                if ($value) {
                    $familyData[$column] = $value;
                }
            }
        }

        // Only proceed if we have at least no_kk or kepala_keluarga
        if (empty($familyData['no_kk']) && empty($familyData['kepala_keluarga'])) {
            \Log::info('No KK data to sync', ['response_id' => $response->id]);
            return;
        }

        // Get resident
        $resident = $response->resident;
        if (!$resident) {
            \Log::warning('No resident found for response', ['response_id' => $response->id]);
            return;
        }

        // Find or create family record
        $family = null;

        if ($resident->family_id) {
            // Update existing family
            $family = Family::find($resident->family_id);
        } elseif (!empty($familyData['no_kk'])) {
            // Try to find by no_kk
            $family = Family::where('no_kk', $familyData['no_kk'])->first();
        }

        if ($family) {
            // Update existing family
            $family->update($familyData);
            \Log::info('Updated family record', ['family_id' => $family->id, 'data' => $familyData]);
        } else {
            // Create new family
            $family = Family::create($familyData);
            \Log::info('Created new family record', ['family_id' => $family->id, 'data' => $familyData]);
        }

        // Link resident to family if not already linked
        if (!$resident->family_id || $resident->family_id != $family->id) {
            $resident->update(['family_id' => $family->id]);
            \Log::info('Linked resident to family', ['resident_id' => $resident->id, 'family_id' => $family->id]);
        }
    }

    /**
     * Get wilayah data from questionnaire answers
     * Handles both ID and text values
     * Returns values as strings to match database column types
     */
    private function getWilayahFromAnswers($responseId): array
    {
        $answers = Answer::where('response_id', $responseId)
            ->whereIn('question_id', [214, 215, 216, 217])
            ->get()
            ->keyBy('question_id');

        // Get province (usually already stored as ID/code like '94')
        $provinceValue = $answers->get(214)?->answer_text;
        $provinceId = null;
        if ($provinceValue && is_numeric($provinceValue)) {
            // Province is stored as 2-digit code like '94'
            $provinceId = str_pad($provinceValue, 2, '0', STR_PAD_LEFT);
        }

        // Get regency - might be text, need to lookup
        $regencyValue = $answers->get(215)?->answer_text;
        $regencyId = null;
        if ($regencyValue) {
            if (is_numeric($regencyValue)) {
                // Already an ID like '9471'
                $regencyId = (string)$regencyValue;
            } else {
                // Lookup by name (case insensitive)
                $regency = \DB::table('regencies')
                    ->whereRaw("UPPER(name) = ?", [strtoupper($regencyValue)])
                    ->first();
                // regencies.id is int4, but residents.regency_id is char(4)
                $regencyId = $regency ? (string)$regency->id : null;
            }
        }

        // Get district - might be text, need to lookup
        $districtValue = $answers->get(216)?->answer_text;
        $districtId = null;
        if ($districtValue) {
            if (is_numeric($districtValue)) {
                // Already an ID like '9471040'
                $districtId = (string)$districtValue;
            } else {
                // Lookup by name (within regency if available)
                $query = \DB::table('districts')->whereRaw("UPPER(name) = ?", [strtoupper($districtValue)]);
                if ($regencyId) {
                    $query->where('regency_id', (int)$regencyId);
                }
                $district = $query->first();
                // districts.id is int4, but residents.district_id is char(7)
                $districtId = $district ? (string)$district->id : null;
            }
        }

        // Get village - might be text, need to lookup
        $villageValue = $answers->get(217)?->answer_text;
        $villageId = null;
        if ($villageValue) {
            if (is_numeric($villageValue)) {
                // Already an ID
                $villageId = (int)$villageValue;
            } else {
                // Lookup by name (within district if available)
                $query = \DB::table('villages')->whereRaw("UPPER(name) = ?", [strtoupper($villageValue)]);
                if ($districtId) {
                    $query->where('district_id', (int)$districtId);
                }
                $village = $query->first();
                // villages.id is bigint and residents.village_id is bigint
                $villageId = $village ? (int)$village->id : null;
            }
        }

        \Log::info('Wilayah lookup result', [
            'raw_answers' => [
                'province' => $provinceValue,
                'regency' => $regencyValue,
                'district' => $districtValue,
                'village' => $villageValue,
            ],
            'resolved' => [
                'province_id' => $provinceId,
                'regency_id' => $regencyId,
                'district_id' => $districtId,
                'village_id' => $villageId,
            ]
        ]);

        return [
            'province_id' => $provinceId,
            'regency_id' => $regencyId,
            'district_id' => $districtId,
            'village_id' => $villageId,
        ];
    }
}
