<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $url;
    protected ?string $token;

    public function __construct()
    {
        $this->url = config('services.whatsapp.url');
        $this->token = config('services.whatsapp.token');
    }

    /**
     * Send WhatsApp message
     */
    public function send(string $phone, string $message): array
    {
        try {
            // Format phone number (remove leading 0, add 62)
            $phone = $this->formatPhone($phone);

            $response = Http::asForm()->post($this->url, [
                'device_id' => $this->token,
                'number' => $phone,
                'message' => $message,
            ]);

            $result = $response->json();

            Log::info('WhatsApp sent', [
                'phone' => $phone,
                'response' => $result,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $result,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send OTP via WhatsApp
     */
    public function sendOtp(string $phone, string $otp): array
    {
        $message = "🔐 *Kode OTP KitorangPeduli*\n\n";
        $message .= "Kode verifikasi Anda adalah:\n\n";
        $message .= "```{$otp}```\n\n";
        $message .= "Kode ini berlaku selama 5 menit.\n";
        $message .= "Jangan bagikan kode ini kepada siapapun.\n\n";
        $message .= '_Pesan ini dikirim otomatis oleh sistem KitorangPeduli._';

        return $this->send($phone, $message);
    }

    /**
     * Send verification status notification
     */
    public function sendVerificationStatus(string $phone, string $name, string $status, ?string $reason = null): array
    {
        if ($status === 'verified') {
            $message = "✅ *Verifikasi Berhasil*\n\n";
            $message .= "Halo {$name},\n\n";
            $message .= "Selamat! Data KTP Anda telah berhasil diverifikasi.\n";
            $message .= "Anda sekarang dapat mengisi kuesioner yang tersedia.\n\n";
            $message .= '_KitorangPeduli_';
        } else {
            $message = "❌ *Verifikasi Ditolak*\n\n";
            $message .= "Halo {$name},\n\n";
            $message .= "Maaf, data KTP Anda tidak dapat diverifikasi.\n";
            if ($reason) {
                $message .= "Alasan: {$reason}\n\n";
            }
            $message .= "Silakan perbaiki data Anda dan upload ulang.\n\n";
            $message .= '_KitorangPeduli_';
        }

        return $this->send($phone, $message);
    }

    /**
     * Format phone number to international format
     * Handles formats: 0812..., 620812..., 6282...
     */
    protected function formatPhone(string $phone): string
    {
        // Remove spaces, dashes, and other characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Handle different formats:
        // 0812... -> 6282...
        // 620812... -> 6282... (remove extra 0)
        // 6282... -> 6282...
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '620')) {
            $phone = '62' . substr($phone, 3);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
