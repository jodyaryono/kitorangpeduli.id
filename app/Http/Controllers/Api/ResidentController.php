<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Family;
use App\Models\Resident;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResidentController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Register new resident
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|size:16|unique:residents,nik',
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
            'phone' => 'required|string|min:10|max:15|unique:residents,phone',
            'ktp_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check or create Family
        $family = Family::where('no_kk', $request->no_kk)->first();
        if (!$family) {
            return response()->json([
                'success' => false,
                'message' => 'KK tidak ditemukan. Silakan daftarkan KK terlebih dahulu.',
            ], 404);
        }

        // Create resident
        $resident = Resident::create([
            'family_id' => $family->id,
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
            $resident->update(['ktp_image_path' => $path]);
        }

        // Generate and send OTP
        $otp = $resident->generateOtp();
        $this->whatsAppService->sendOtp($request->phone, $otp);

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. OTP telah dikirim ke WhatsApp Anda.',
            'data' => [
                'id' => $resident->id,
                'nik' => $resident->nik,
                'nama_lengkap' => $resident->nama_lengkap,
                'verification_status' => $resident->verification_status,
            ],
        ], 201);
    }

    /**
     * Get current resident profile
     */
    public function profile(Request $request): JsonResponse
    {
        $resident = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $resident->id,
                'nik' => $resident->nik,
                'nama_lengkap' => $resident->nama_lengkap,
                'tempat_lahir' => $resident->tempat_lahir,
                'tanggal_lahir' => $resident->tanggal_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $resident->jenis_kelamin,
                'agama' => $resident->agama,
                'pekerjaan' => $resident->pekerjaan,
                'alamat' => $resident->full_address,
                'phone' => $resident->phone,
                'citizen_type' => $resident->citizenType?->name,
                'family' => [
                    'no_kk' => $resident->family?->no_kk,
                    'kepala_keluarga' => $resident->family?->kepala_keluarga,
                ],
                'location' => [
                    'latitude' => $resident->latitude,
                    'longitude' => $resident->longitude,
                ],
                'verification_status' => $resident->verification_status,
                'family_verification_status' => $resident->family?->verification_status,
                'can_answer_questionnaire' => $resident->canAnswerQuestionnaire(),
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
