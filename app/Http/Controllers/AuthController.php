<?php

namespace App\Http\Controllers;

use App\Models\CitizenType;
use App\Models\District;
use App\Models\Education;
use App\Models\Occupation;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Respondent;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
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
        // If already logged in as officer, redirect to officer portal
        if (auth()->check()) {
            return redirect()->route('officer.entry');
        }

        // If already logged in as respondent, redirect to home
        if (session('respondent')) {
            return redirect()->route('home');
        }

        // Store intended destination if provided
        if (request()->has('intended')) {
            session(['intended' => request()->get('intended')]);
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
            'no_hp' => ['required', 'string', 'regex:/^[0-9]{9,13}$/'],
        ], [
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka (9-13 digit).',
        ]);

        // Normalize phone number to 62xxx format
        $no_hp = preg_replace('/[^0-9]/', '', $request->no_hp);
        $no_hp = ltrim($no_hp, '0');
        if (!str_starts_with($no_hp, '62')) {
            $no_hp = '62' . $no_hp;
        }

        // Check if user (officer) exists first
        $user = \App\Models\User::where('phone', $no_hp)->where('is_active', true)->first();
        $respondent = Respondent::where('phone', $no_hp)->first();

        if (!$user && !$respondent) {
            return back()
                ->withErrors(['no_hp' => 'Nomor HP belum terdaftar. Silakan daftar terlebih dahulu.'])
                ->withInput();
        }

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 5 minutes
        Cache::put('otp_' . $no_hp, $otp, now()->addMinutes(5));
        // Store login type (officer has priority)
        Cache::put('login_type_' . $no_hp, $user ? 'officer' : 'respondent', now()->addMinutes(5));

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
        $loginType = Cache::get('login_type_' . $no_hp, 'respondent');

        // For development, accept "123456" as valid OTP
        if ($request->otp !== $cachedOtp && $request->otp !== '123456') {
            return back()
                ->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa.'])
                ->with('no_hp', $no_hp);
        }

        // Clear OTP from cache
        Cache::forget('otp_' . $no_hp);
        Cache::forget('login_type_' . $no_hp);

        // Login as officer (User model)
        if ($loginType === 'officer') {
            $user = \App\Models\User::where('phone', $no_hp)->where('is_active', true)->first();
            if (!$user) {
                return redirect()->route('login')->withErrors(['no_hp' => 'Akun petugas tidak ditemukan atau tidak aktif.']);
            }

            // Laravel's standard auth login
            auth()->login($user);

            // Check if there's an intended destination
            if ($request->get('intended') === 'officer-entry' || session('intended') === 'officer-entry') {
                session()->forget('intended');
                return redirect()->route('officer.entry')
                    ->with('success', 'Selamat datang, ' . $user->name . '! (Petugas OPD)');
            }

            return redirect()->route('officer.entry')
                ->with('success', 'Selamat datang, ' . $user->name . '! (Petugas OPD)');
        }

        // Login as respondent
        $respondent = Respondent::where('phone', $no_hp)->first();
        if (!$respondent) {
            return redirect()->route('register')->withErrors(['no_hp' => 'Nomor HP tidak ditemukan.']);
        }

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
            Log::error('Registration error: ' . $e->getMessage());
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
        // Logout Laravel auth (for officers)
        if (auth()->check()) {
            auth()->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        // Clear respondent session
        session()->forget('respondent');

        return redirect()->route('home')->with('success', 'Anda telah keluar.');
    }

    public function showProfile()
    {
        if (!session('respondent')) {
            return redirect()->route('login')->with('info', 'Silakan masuk terlebih dahulu.');
        }

        $respondent = Respondent::with([
            'village',
            'citizenType',
            'occupation',
            'education',
            'media'
        ])->findOrFail(session('respondent.id'));

        // Load wilayah manually due to type mismatch issue
        if ($respondent->province_id) {
            $respondent->province = Province::where('id', (int)$respondent->province_id)->first();
        }
        if ($respondent->regency_id) {
            $respondent->regency = Regency::where('id', (int)$respondent->regency_id)->first();
        }
        if ($respondent->district_id) {
            $respondent->district = District::where('id', (int)$respondent->district_id)->first();
        }

        $provinces = Province::orderBy('name')->get();
        $educations = Education::orderBy('id')->get();
        $occupations = Occupation::orderBy('id')->get();
        $citizenTypes = CitizenType::orderBy('name')->get();

        return view('profile.show', compact('respondent', 'provinces', 'educations', 'occupations', 'citizenTypes'));
    }

    public function updateProfile(Request $request)
    {
        if (!session('respondent')) {
            return redirect()->route('login')->with('info', 'Silakan masuk terlebih dahulu.');
        }

        $respondent = Respondent::findOrFail(session('respondent.id'));

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:respondents,nik,' . $respondent->id,
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string',
            'golongan_darah' => 'required|string',
            'status_perkawinan' => 'required|string',
            'citizen_type_id' => 'required|exists:citizen_types,id',
            'occupation_id' => 'required|exists:occupations,id',
            'education_id' => 'required|exists:educations,id',
            'phone' => 'required|string|min:9|max:15|unique:respondents,phone,' . $respondent->id,
            'email' => 'nullable|email|max:255',
            'alamat' => 'required|string',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
            'province_id' => 'required|exists:provinces,id',
            'regency_id' => 'required|exists:regencies,id',
            'district_id' => 'required|exists:districts,id',
            'village_id' => 'required|exists:villages,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_ktp' => 'nullable|image|max:2048',
        ], [
            'nik.size' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'phone.unique' => 'Nomor HP sudah terdaftar.',
            'rt.max' => 'RT maksimal 3 digit.',
            'rw.max' => 'RW maksimal 3 digit.',
            'foto_ktp.image' => 'File harus berupa gambar.',
            'foto_ktp.max' => 'Ukuran gambar maksimal 2MB.',
        ]);

        try {
            $respondent->update([
                'nama_lengkap' => $request->nama_lengkap,
                'nik' => $request->nik,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'agama' => $request->agama,
                'golongan_darah' => $request->golongan_darah,
                'status_perkawinan' => $request->status_perkawinan,
                'citizen_type_id' => $request->citizen_type_id,
                'occupation_id' => $request->occupation_id,
                'education_id' => $request->education_id,
                'phone' => $request->phone,
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
            ]);

            // Upload new KTP photo if provided
            if ($request->hasFile('foto_ktp')) {
                // Delete old photo
                $respondent->clearMediaCollection('ktp_image');
                // Add new photo
                $respondent
                    ->addMediaFromRequest('foto_ktp')
                    ->toMediaCollection('ktp_image');
            }

            // Update session data
            session(['respondent' => [
                'id' => $respondent->id,
                'nama_lengkap' => $respondent->nama_lengkap,
                'phone' => $respondent->phone,
            ]]);

            return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profil. Silakan coba lagi.'])->withInput();
        }
    }
}
