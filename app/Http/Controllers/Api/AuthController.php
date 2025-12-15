<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Respondent;
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

        // Find or check if respondent exists
        $respondent = Respondent::where('phone', $phone)->first();

        if (!$respondent) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak terdaftar. Silakan registrasi terlebih dahulu.',
                'registered' => false,
            ], 404);
        }

        // Generate OTP
        $otp = $respondent->generateOtp();

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

        $respondent = Respondent::where('phone', $request->phone)->first();

        if (!$respondent) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor HP tidak terdaftar',
            ], 404);
        }

        if (!$respondent->verifyOtp($request->otp)) {
            return response()->json([
                'success' => false,
                'message' => 'OTP salah atau sudah kadaluarsa',
            ], 401);
        }

        // Create Sanctum token
        $token = $respondent->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'token' => $token,
                'respondent' => [
                    'id' => $respondent->id,
                    'nik' => $respondent->nik,
                    'nama_lengkap' => $respondent->nama_lengkap,
                    'phone' => $respondent->phone,
                    'verification_status' => $respondent->verification_status,
                    'can_answer_questionnaire' => $respondent->canAnswerQuestionnaire(),
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

        $respondent = Respondent::where('phone', $request->phone)->first();

        return response()->json([
            'success' => true,
            'registered' => $respondent !== null,
            'verification_status' => $respondent?->verification_status,
        ]);
    }
}
