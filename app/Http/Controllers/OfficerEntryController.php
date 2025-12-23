<?php

namespace App\Http\Controllers;

use App\Models\Questionnaire;
use App\Models\Resident;
use App\Models\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OfficerEntryController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin', 'field_officer'])) {
            abort(403);
        }

        $opdId = $user && method_exists($user, 'canAccessAllOpds') && !$user->canAccessAllOpds()
            ? $user->opd_id
            : null;

        $perPage = 6;
        $search = $request->get('search');

        $query = Questionnaire::with(['opd', 'questions'])
            ->forOfficers($opdId)
            ->available()
            ->withCount('questions');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q
                    ->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $questionnaires = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // Get response completion stats for each questionnaire
        $questionnaires->getCollection()->transform(function ($q) {
            $q->completed_count = Response::where('questionnaire_id', $q->id)
                ->where('status', 'completed')
                ->distinct('resident_id')
                ->count();
            return $q;
        });

        // Get recent entries by this officer (last 10 - both completed and in-progress)
        $recentEntries = Response::with(['questionnaire', 'resident'])
            ->where('entered_by_user_id', $user->id)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $stats = [
            'questionnaires' => Questionnaire::forOfficers($opdId)->available()->count(),
            'opdName' => $user->opd->name ?? 'Semua OPD',
            'totalEntered' => Response::where('entered_by_user_id', $user->id)->where('status', 'completed')->count(),
            'totalRespondents' => Response::where('entered_by_user_id', $user->id)->where('status', 'completed')->distinct('resident_id')->count(),
        ];

        // If AJAX request, return partial view
        if ($request->ajax()) {
            // Get response completion stats for each questionnaire
            $questionnaires->getCollection()->transform(function ($q) {
                $q->completed_count = Response::where('questionnaire_id', $q->id)
                    ->where('status', 'completed')
                    ->distinct('resident_id')
                    ->count();
                return $q;
            });

            return response()->json([
                'html' => view('partials.officer-questionnaire-cards', compact('questionnaires'))->render(),
                'hasMore' => $questionnaires->hasMorePages(),
                'nextPage' => $questionnaires->currentPage() + 1,
                'total' => $questionnaires->total(),
                'showing' => $questionnaires->count() + (($questionnaires->currentPage() - 1) * $perPage),
            ]);
        }

        return view('officer-entry', [
            'user' => $user,
            'questionnaires' => $questionnaires,
            'stats' => $stats,
            'search' => $search,
            'recentEntries' => $recentEntries,
        ]);
    }

    public function selectQuestionnaire(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin', 'field_officer'])) {
            abort(403);
        }

        $opdId = $user && method_exists($user, 'canAccessAllOpds') && !$user->canAccessAllOpds()
            ? $user->opd_id
            : null;

        $questionnaire = Questionnaire::with('opd')
            ->forOfficers($opdId)
            ->available()
            ->findOrFail($id);

        // For officer_assisted questionnaires, skip NIK entry and go directly to form
        if ($questionnaire->visibility === 'officer_assisted') {
            session([
                'officer_assisted' => true,
                'officer_questionnaire_id' => $questionnaire->id,
            ]);

            return redirect()
                ->route('questionnaire.start', ['id' => $questionnaire->id])
                ->with('success', 'Mulai mengisi kuesioner ' . $questionnaire->name);
        }

        // For other visibility types, show NIK entry screen
        $nikOptions = Resident::query()
            ->select(['nik', 'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir', 'updated_at'])
            ->orderByDesc('updated_at')
            ->limit(5)
            ->get()
            ->map(function ($r) {
                $age = $r->tanggal_lahir ? $r->tanggal_lahir->age : null;
                $gender = $r->jenis_kelamin;
                $meta = implode(' • ', array_filter([
                    $r->nama_lengkap,
                    $gender,
                    $age ? ($age . ' th') : null,
                ]));
                $subtitle = $r->updated_at ? ('Update ' . $r->updated_at->diffForHumans()) : null;

                return [
                    'value' => $r->nik,
                    'label' => trim($r->nik . ' — ' . $meta),
                    'meta' => $meta ?: 'NIK terdaftar',
                    'subtitle' => $subtitle,
                ];
            });

        // Get top respondents who filled this questionnaire with officer help
        $search = $request->get('search', '');
        $topRespondentsQuery = Response::with(['resident', 'questionnaire'])
            ->where('questionnaire_id', $questionnaire->id)
            ->where('entered_by_user_id', $user->id)
            ->orderByDesc('updated_at');

        if ($search) {
            $topRespondentsQuery->whereHas('resident', function ($q) use ($search) {
                $q
                    ->where('nik', 'ilike', "%{$search}%")
                    ->orWhere('nama_lengkap', 'ilike', "%{$search}%");
            });
        }

        $topRespondents = $topRespondentsQuery->limit(20)->get();

        return view('officer-nik-entry', [
            'user' => $user,
            'questionnaire' => $questionnaire,
            'nikOptions' => $nikOptions,
            'topRespondents' => $topRespondents,
            'search' => $search,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Manual validation to avoid HTML5 required popup
        $questionnaireId = $request->integer('questionnaire_id');
        $nik = $request->nik;

        if (!$questionnaireId) {
            return back()->withErrors(['questionnaire_id' => 'Kuesioner wajib dipilih.'])->withInput();
        }

        if (!$nik || strlen($nik) !== 16) {
            return back()->withErrors(['nik' => 'NIK wajib diisi dan harus 16 digit.'])->withInput();
        }

        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin', 'field_officer'])) {
            abort(403);
        }

        $opdId = method_exists($user, 'canAccessAllOpds') && !$user->canAccessAllOpds()
            ? $user->opd_id
            : null;

        $questionnaire = Questionnaire::forOfficers($opdId)
            ->available()
            ->find($questionnaireId);

        if (!$questionnaire) {
            return back()->withErrors(['questionnaire_id' => 'Kuesioner tidak diizinkan untuk OPD Anda.'])->withInput();
        }

        $respondent = Resident::where('nik', $nik)->first();
        if (!$respondent) {
            return back()->withErrors(['nik' => 'NIK tidak ditemukan. Daftarkan responden terlebih dahulu.'])->withInput();
        }

        // Check if response already exists
        $existing = Response::where('questionnaire_id', $questionnaire->id)
            ->where('resident_id', $respondent->id)
            ->first();

        if ($existing && $existing->status === 'completed') {
            return back()->withErrors(['nik' => 'Responden ini sudah mengisi kuesioner ini.'])->withInput();
        }

        // Get or create response
        $response = $existing ?: Response::create([
            'questionnaire_id' => $questionnaire->id,
            'resident_id' => $respondent->id,
            'entered_by_user_id' => $user->id,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Set temporary session to identify this is officer-assisted entry
        session([
            'officer_assisted' => true,
            'officer_response_id' => $response->id,
            'officer_respondent' => [
                'id' => $respondent->id,
                'nama_lengkap' => $respondent->nama_lengkap,
                'nik' => $respondent->nik,
            ],
        ]);

        // Redirect to questionnaire start route (same as regular respondent flow)
        return redirect()
            ->route('questionnaire.start', ['id' => $questionnaire->id])
            ->with('success', 'Mulai mengisi kuesioner untuk ' . $respondent->nama_lengkap);
    }

    public function createRespondent(Request $request): View
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin', 'field_officer'])) {
            abort(403);
        }

        // Get lookup data needed for the form
        $citizenTypes = \App\Models\CitizenType::all();
        $educations = \App\Models\Education::all();
        $occupations = \App\Models\Occupation::all();
        $provinces = \App\Models\Province::all();

        $questionnaire_id = $request->get('questionnaire_id');

        return view('officer-respondent-create', [
            'user' => $user,
            'questionnaire_id' => $questionnaire_id,
            'citizenTypes' => $citizenTypes,
            'educations' => $educations,
            'occupations' => $occupations,
            'provinces' => $provinces,
        ]);
    }

    public function storeRespondent(Request $request): RedirectResponse
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'opd_admin', 'field_officer'])) {
            abort(403);
        }

        // Comprehensive validation for multi-step form
        $validated = $request->validate([
            // Step 1: Data Pribadi
            'nik' => 'required|digits:16|unique:respondents,nik',
            'nama_lengkap' => 'required|string|max:255',
            'no_kk' => 'nullable|digits:16',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'status_perkawinan' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'status_hubungan' => 'nullable|in:Kepala Keluarga,Suami,Istri,Anak,Menantu,Cucu,Orang Tua,Mertua,Famili Lain,Pembantu,Lainnya',
            'citizen_type_id' => 'required|exists:citizen_types,id',
            'education_id' => 'nullable|exists:educations,id',
            'occupation_id' => 'nullable|exists:occupations,id',
            // Step 2: Kontak
            'no_hp' => 'required|digits_between:9,13',
            'email' => 'nullable|email|max:255',
            // Step 3: Alamat
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'alamat' => 'required|string|max:500',
            'rt' => 'required|digits_between:1,3',
            'rw' => 'required|digits_between:1,3',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            // Step 4: Verifikasi
            'foto_ktp' => 'required|image|max:2048',
        ], [
            'nik.unique' => 'NIK ini sudah terdaftar dalam sistem.',
            'citizen_type_id.required' => 'Tipe warga wajib dipilih.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'agama.required' => 'Agama wajib dipilih.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'foto_ktp.required' => 'Foto KTP wajib diunggah.',
        ]);

        try {
            // Format phone number (add 62 prefix if needed)
            if (!str_starts_with($validated['no_hp'], '62')) {
                $validated['phone'] = '62' . $validated['no_hp'];
            } else {
                $validated['phone'] = $validated['no_hp'];
            }

            // Upload KTP image if provided
            if ($request->hasFile('foto_ktp')) {
                $file = $request->file('foto_ktp');
                $path = $file->store('ktp-images', 'public');
                $validated['ktp_image_path'] = $path;
            }

            // Remove no_hp from validated data (we use phone instead)
            unset($validated['no_hp']);

            // Create respondent with all validated data
            $respondent = Resident::create($validated);

            // Get questionnaire_id from request to redirect back
            $questionnaire_id = $request->get('questionnaire_id');

            return redirect()
                ->route('officer.nik-entry', ['questionnaire_id' => $questionnaire_id])
                ->with('success', 'Responden ' . $respondent->nama_lengkap . ' berhasil didaftarkan. Silakan pilih responden dari daftar untuk melanjutkan.');
        } catch (\Exception $e) {
            \Log::error('Gagal mendaftarkan responden: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->withErrors(['error' => 'Gagal mendaftarkan responden: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
