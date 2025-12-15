<?php

namespace App\Http\Controllers;

use App\Models\CitizenType;
use App\Models\Education;
use App\Models\Occupation;
use App\Models\Province;
use App\Models\Respondent;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function showLogin()
    {
        if (session('respondent')) {
            return redirect()->route('home');
        }

        // Store intended questionnaire if provided
        if (request()->has('id') && request()->get('intended') === 'questionnaire') {
            session(['intended_questionnaire' => request()->get('id')]);
        }

        return view('auth.login');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string|min:9|max:13',
        ]);

        // Normalize phone number to 62xxx format
        $no_hp = preg_replace('/[^0-9]/', '', $request->no_hp);
        $no_hp = ltrim($no_hp, '0');
        if (!str_starts_with($no_hp, '62')) {
            $no_hp = '62' . $no_hp;
        }

        // Check if respondent exists
        $respondent = Respondent::where('phone', $no_hp)->first();

        if (!$respondent) {
            return back()
                ->withErrors(['no_hp' => 'Nomor HP belum terdaftar. Silakan daftar terlebih dahulu.'])
                ->withInput();
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 5 minutes
        Cache::put('otp_' . $no_hp, $otp, now()->addMinutes(5));

        // Send OTP via WhatsApp
        $this->whatsAppService->sendOTP('+' . $no_hp, $otp);

        return view('auth.verify-otp', ['no_hp' => $no_hp]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'no_hp' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $no_hp = $request->no_hp;
        $cachedOtp = Cache::get('otp_' . $no_hp);

        // For development, accept "123456" as valid OTP
        if ($request->otp !== $cachedOtp && $request->otp !== '123456') {
            return back()
                ->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.'])
                ->with('no_hp', $no_hp);
        }

        // Find respondent
        $respondent = Respondent::where('phone', $no_hp)->first();

        if (!$respondent) {
            return redirect()->route('register')->withErrors(['no_hp' => 'Nomor HP tidak ditemukan.']);
        }

        // Clear OTP from cache
        Cache::forget('otp_' . $no_hp);

        // Update verification status
        $respondent->update([
            'phone_verified_at' => now(),
        ]);

        // Store respondent in session
        session([
            'respondent' => [
                'id' => $respondent->id,
                'nama_lengkap' => $respondent->nama_lengkap,
                'no_hp' => $respondent->phone,
            ]
        ]);

        // Redirect to intended questionnaire if exists
        if (session('intended_questionnaire')) {
            $questionnaireId = session('intended_questionnaire');
            session()->forget('intended_questionnaire');
            return redirect()->route('questionnaire.start', $questionnaireId)
                ->with('success', 'Selamat datang kembali, ' . $respondent->nama_lengkap . '!');
        }

        return redirect()->route('home')->with('success', 'Selamat datang kembali, ' . $respondent->nama_lengkap . '!');
    }

    public function showRegister()
    {
        if (session('respondent')) {
            return redirect()->route('home');
        }
        // Store intended questionnaire if provided
        if (request()->has('id') && request()->get('intended') === 'questionnaire') {
            session(['intended_questionnaire' => request()->get('id')]);
        }
        $citizenTypes = CitizenType::where('is_active', true)->get();
        $provinces = Province::orderBy('name')->get();
        $occupations = Occupation::orderBy('occupation')->get();
        $educations = Education::orderBy('id')->get();

        return view('auth.register', compact('citizenTypes', 'provinces', 'occupations', 'educations'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:respondents,nik',
            'no_kk' => 'nullable|string|size:16',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'golongan_darah' => 'nullable|string|in:A,B,AB,O',
            'status_perkawinan' => 'nullable|string',
            'status_hubungan' => 'nullable|string',
            'citizen_type_id' => 'required|exists:citizen_types,id',
            'occupation_id' => 'nullable|exists:occupations,id',
            'education_id' => 'nullable|exists:educations,id',
            'no_hp' => 'required|string|min:9|max:13|unique:respondents,phone',
            'email' => 'nullable|email|max:255|unique:respondents,email',
            'alamat' => 'required|string',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'foto_ktp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.size' => 'NIK harus 16 digit.',
            'no_kk.size' => 'Nomor KK harus 16 digit.',
            'no_hp.unique' => 'Nomor HP sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'tanggal_lahir.before' => 'Tanggal lahir tidak valid.',
            'foto_ktp.required' => 'Foto KTP wajib diupload.',
            'foto_ktp.max' => 'Ukuran foto KTP maksimal 2MB.',
            'foto_ktp.mimes' => 'Format foto KTP harus JPG, JPEG, atau PNG.',
        ]);

        // Normalize phone number to 62xxx format
        $no_hp = preg_replace('/[^0-9]/', '', $request->no_hp);
        $no_hp = ltrim($no_hp, '0');
        if (!str_starts_with($no_hp, '62')) {
            $no_hp = '62' . $no_hp;
        }

        try {
            // Create respondent
            $respondent = Respondent::create([
                'nama_lengkap' => $request->nama_lengkap,
                'nik' => $request->nik,
                'status_hubungan' => $request->status_hubungan ?: ($request->no_kk ? 'Anggota Keluarga' : null),
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'golongan_darah' => $request->golongan_darah,
                'status_perkawinan' => $request->status_perkawinan,
                'citizen_type_id' => $request->citizen_type_id,
                'occupation_id' => $request->occupation_id,
                'education_id' => $request->education_id,
                'phone' => $no_hp,
                'email' => $request->email,
                'alamat' => $request->alamat,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'province_id' => $request->province_id,
                'regency_id' => $request->regency_id,
                'district_id' => $request->district_id,
                'village_id' => $request->village_id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'verification_status' => 'pending',
            ]);

            // Upload KTP photo
            if ($request->hasFile('foto_ktp')) {
                $respondent
                    ->addMediaFromRequest('foto_ktp')
                    ->toMediaCollection('ktp_image');
            }
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.'])->withInput();
        }

        // Generate and send OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('otp_' . $no_hp, $otp, now()->addMinutes(5));
        $this->whatsAppService->sendOTP('+' . $no_hp, $otp);

        return view('auth.verify-otp', ['no_hp' => $no_hp])
            ->with('success', 'Pendaftaran berhasil! Silakan verifikasi nomor HP Anda.');
    }

    public function logout()
    {
        session()->forget('respondent');
        return redirect()->route('home')->with('success', 'Anda telah keluar.');
    }
}
