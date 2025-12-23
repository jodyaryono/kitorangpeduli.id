<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\Resident;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RespondentController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Register new respondent
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|unique:respondents,nik',
            'no_kk' => 'required|string|size:16',
            'citizen_type_id' => 'required|exists:citizen_types,id',
            'nama_lengkap' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah' => 'nullable|string|max:3',
            'agama' => 'required|string|max:20',
            'status_perkawinan' => 'required|string|max:20',
            'pekerjaan' => 'nullable|string|max:100',
            'kewarganegaraan' => 'nullable|string|max:3',
            'alamat' => 'required|string',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'phone' => 'required|string|min:10|max:15|unique:respondents,phone',
            'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check or create KK
        $kk = Family::where('no_kk', $request->no_kk)->first();
        if (!$kk) {
            return response()->json([
                'success' => false,
                'message' => 'KK tidak ditemukan. Silakan daftarkan KK terlebih dahulu.',
            ], 404);
        }

        // Create respondent
        $respondent = Resident::create([
            'families_id' => $kk->id,
            'citizen_type_id' => $request->citizen_type_id,
            'nik' => $request->nik,
            'nama_lengkap' => $request->nama_lengkap,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'golongan_darah' => $request->golongan_darah,
            'agama' => $request->agama,
            'status_perkawinan' => $request->status_perkawinan,
            'pekerjaan' => $request->pekerjaan,
            'kewarganegaraan' => $request->kewarganegaraan ?? 'WNI',
            'alamat' => $request->alamat,
            'rt' => $request->rt,
            'rw' => $request->rw,
            'province_id' => $request->province_id,
            'regency_id' => $request->regency_id,
            'district_id' => $request->district_id,
            'village_id' => $request->village_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'phone' => $request->phone,
        ]);

        // Handle KTP image upload
        if ($request->hasFile('ktp_image')) {
            $path = $request->file('ktp_image')->store('ktp', 'public');
            $respondent->update(['ktp_image_path' => $path]);
        }

        // Generate and send OTP
        $otp = $respondent->generateOtp();
        $this->whatsAppService->sendOtp($request->phone, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. OTP telah dikirim ke WhatsApp Anda.',
            'data' => [
                'id' => $respondent->id,
                'nik' => $respondent->nik,
                'nama_lengkap' => $respondent->nama_lengkap,
                'verification_status' => $respondent->verification_status,
            ],
        ], 201);
    }

    /**
     * Get current respondent profile
     */
    public function profile(Request $request): JsonResponse
    {
        $respondent = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $respondent->id,
                'nik' => $respondent->nik,
                'nama_lengkap' => $respondent->nama_lengkap,
                'tempat_lahir' => $respondent->tempat_lahir,
                'tanggal_lahir' => $respondent->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $respondent->jenis_kelamin,
                'agama' => $respondent->agama,
                'pekerjaan' => $respondent->pekerjaan,
                'alamat' => $respondent->full_address,
                'phone' => $respondent->phone,
                'citizen_type' => $respondent->citizenType?->name,
                'kk' => [
                    'no_kk' => $respondent->kartuKeluarga?->no_kk,
                    'kepala_keluarga' => $respondent->kartuKeluarga?->kepala_keluarga,
                ],
                'location' => [
                    'latitude' => $respondent->latitude,
                    'longitude' => $respondent->longitude,
                ],
                'verification_status' => $respondent->verification_status,
                'kk_verification_status' => $respondent->kartuKeluarga?->verification_status,
                'can_answer_questionnaire' => $respondent->canAnswerQuestionnaire(),
            ],
        ]);
    }

    /**
     * Update location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $request->user()->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lokasi berhasil diperbarui',
        ]);
    }
}
