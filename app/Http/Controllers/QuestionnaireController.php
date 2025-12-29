<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Family;
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

        // For officer_assisted with target_type=family, resident_id can be null
        if ($questionnaire->visibility === 'officer_assisted' && $questionnaire->target_type === 'family') {
            // Check for existing in-progress response for this officer
            $response = Response::where('questionnaire_id', $id)
                ->where('entered_by_user_id', auth()->id())
                ->where('status', 'in_progress')
                ->whereNull('resident_id')
                ->first();

            if (!$response) {
                $response = Response::create([
                    'questionnaire_id' => $id,
                    'resident_id' => null,
                    'status' => 'in_progress',
                    'started_at' => now(),
                    'entered_by_user_id' => auth()->id(),
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

        return view('questionnaire.fill', compact('questionnaire', 'response', 'existingAnswers', 'isOfficerAssisted', 'respondentData', 'actualQuestions'));
    }

    public function autosave(Request $request, $id)
    {
        // Check if officer-assisted or regular respondent
        $isOfficerAssisted = session('officer_assisted', false);
        $respondentData = $isOfficerAssisted
            ? session('officer_respondent')
            : session('resident');

        if (!$respondentData && !auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $respondentId = $respondentData['id'] ?? session('respondent.id');

        try {
            $response = Response::where('questionnaire_id', $id)
                ->where('resident_id', $respondentId)
                ->where('status', 'in_progress')
                ->first();

            if (!$response) {
                return response()->json(['success' => false, 'message' => 'Response not found'], 404);
            }

            $questionId = $request->question_id;

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

    public function saveFamilyMembers(Request $request, $id)
    {
        try {
            $responseId = $request->input('response_id');
            $familyMembersData = json_decode($request->input('family_members'), true);

            if (!$responseId || !$familyMembersData) {
                return response()->json(['success' => false, 'message' => 'Invalid data'], 400);
            }

            $response = Response::findOrFail($responseId);

            // Save family members to response
            $response->update([
                'family_members' => json_encode($familyMembersData)
            ]);

            // Create/update residents records from family members
            $family = Family::where('response_id', $response->id)->first();

            if ($family) {
                // Delete existing residents for this family (we'll recreate them)
                \App\Models\Resident::where('family_id', $family->id)->delete();

                // Create new resident records
                foreach ($familyMembersData as $memberData) {
                    // Parse tanggal_lahir from d/m/Y format
                    $tanggalLahir = null;
                    if (isset($memberData['tanggal_lahir']) && !empty($memberData['tanggal_lahir'])) {
                        try {
                            $tanggalLahir = \Carbon\Carbon::createFromFormat('d/m/Y', $memberData['tanggal_lahir'])->format('Y-m-d');
                        } catch (\Exception $e) {
                            \Log::warning("Invalid date format for family member: " . $memberData['tanggal_lahir']);
                        }
                    }

                    // Create resident record
                    \App\Models\Resident::create([
                        'family_id' => $family->id,
                        'nama_lengkap' => $memberData['nama_lengkap'] ?? null,
                        'nik' => $memberData['nik'] ?? null,
                        'hubungan_keluarga' => $memberData['hubungan'] ?? null,
                        'tempat_lahir' => $memberData['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $tanggalLahir,
                        'umur' => $memberData['umur'] ?? null,
                        'jenis_kelamin' => $memberData['jenis_kelamin'] ?? null,
                        'status_kawin' => $memberData['status_perkawinan'] ?? null,
                        'agama' => $memberData['agama'] ?? null,
                        'pendidikan' => $memberData['pendidikan'] ?? null,
                        'pekerjaan' => $memberData['pekerjaan'] ?? null,
                        'golongan_darah' => $memberData['golongan_darah'] ?? null,
                        'phone' => $memberData['phone'] ?? null,
                    ]);
                }

                $residentsCount = \App\Models\Resident::where('family_id', $family->id)->count();
                return response()->json([
                    'success' => true,
                    'message' => 'Family members saved successfully',
                    'residents_count' => $residentsCount
                ]);
            } else {
                // Family not found yet, just save to response
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

                // Save family members to response
                $response->update([
                    'family_members' => json_encode($familyMembersData)
                ]);

                // Create residents records from family members
                // First, get the family_id from this response
                $family = \App\Models\Family::where('response_id', $response->id)->first();

                if ($family) {
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

                        // Create resident record
                        \App\Models\Resident::create([
                            'family_id' => $family->id,
                            'nama_lengkap' => $memberData['nama_lengkap'] ?? null,
                            'nik' => $memberData['nik'] ?? null,
                            'hubungan_keluarga' => $memberData['hubungan'] ?? null,
                            'tempat_lahir' => $memberData['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $tanggalLahir,
                            'umur' => $memberData['umur'] ?? null,
                            'jenis_kelamin' => $memberData['jenis_kelamin'] ?? null,
                            'status_kawin' => $memberData['status_perkawinan'] ?? null,
                            'agama' => $memberData['agama'] ?? null,
                            'pendidikan' => $memberData['pendidikan'] ?? null,
                            'pekerjaan' => $memberData['pekerjaan'] ?? null,
                            'golongan_darah' => $memberData['golongan_darah'] ?? null,
                            'phone' => $memberData['phone'] ?? null,
                            'ktp_kia_path' => $memberData['ktp_kia_path'] ?? null,
                        ]);
                    }
                }
            }

            // Handle health data per member
            if ($request->has('health')) {
                $response->update([
                    'health_data' => json_encode($request->health)
                ]);
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
}
