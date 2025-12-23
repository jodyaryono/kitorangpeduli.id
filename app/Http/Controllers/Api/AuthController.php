<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Services\WhatsAppService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected WhatsAppService $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    /**
     * Request OTP via WhatsApp
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $phone = $request->phone;

        // Find or check if resident exists
        $resident = Resident::where('phone', $phone)->first();

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak terdaftar. Silakan registrasi terlebih dahulu.',
                'registered' => false,
            ], 404);
        }

        // Generate OTP
        $otp = $resident->generateOtp();

        // Send OTP via WhatsApp
        $result = $this->whatsAppService->sendOtp($phone, $otp);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim OTP. Silakan coba lagi.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP telah dikirim ke WhatsApp Anda',
            'expires_in' => 300,  // 5 minutes
        ]);
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resident = Resident::where('phone', $request->phone)->first();

        if (!$resident) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak terdaftar',
            ], 404);
        }

        if (!$resident->verifyOtp($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP salah atau sudah kadaluarsa',
            ], 401);
        }

        // Create Sanctum token
        $token = $resident->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'resident' => [
                    'id' => $resident->id,
                    'nik' => $resident->nik,
                    'nama_lengkap' => $resident->nama_lengkap,
                    'phone' => $resident->phone,
                    'verification_status' => $resident->verification_status,
                    'can_answer_questionnaire' => $resident->canAnswerQuestionnaire(),
                ],
            ],
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Check phone registration status
     */
    public function checkPhone(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        $resident = Resident::where('phone', $request->phone)->first();

        return response()->json([
            'success' => true,
            'registered' => $resident !== null,
            'verification_status' => $resident?->verification_status,
        ]);
    }
}
