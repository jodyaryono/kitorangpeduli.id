<?php

namespace App\Filament\Resources\Respondents\Schemas;

use App\Models\CitizenType;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Respondent;
use App\Models\Village;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RespondentForm
{
    public static function parseNik(string $nik): array
    {
        if (strlen($nik) !== 16 || !ctype_digit($nik)) {
            return [];
        }

        $provinceCode = substr($nik, 0, 2);
        $regencyCode = substr($nik, 0, 4);
        $districtCode = substr($nik, 0, 6);
        $birthDatePart = substr($nik, 6, 6);
        $registrationNumber = substr($nik, 12, 4);

        // Parse tanggal lahir (DDMMYY)
        $day = (int) substr($birthDatePart, 0, 2);
        $month = (int) substr($birthDatePart, 2, 2);
        $yearPart = (int) substr($birthDatePart, 4, 2);

        // Tentukan jenis kelamin: perempuan = hari + 40
        $gender = 'L';
        if ($day > 40) {
            $gender = 'P';
            $day -= 40;
        }

        // Validasi hari dan bulan
        if ($day < 1 || $day > 31 || $month < 1 || $month > 12) {
            return [];
        }

        // Tentukan tahun penuh (asumsi: 00-25 = 2000-2025, 26-99 = 1926-1999)
        $currentYear = (int) date('Y');
        $currentCentury = (int) floor($currentYear / 100) * 100;
        $cutoffYear = $currentYear - $currentCentury;

        if ($yearPart <= $cutoffYear) {
            $fullYear = $currentCentury + $yearPart;
        } else {
            $fullYear = ($currentCentury - 100) + $yearPart;
        }

        // Buat tanggal lahir
        $birthDate = null;
        try {
            if (checkdate($month, $day, $fullYear)) {
                $birthDate = Carbon::createFromDate($fullYear, $month, $day)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            $birthDate = null;
        }

        return [
            'province_code' => $provinceCode,
            'regency_code' => $regencyCode,
            'district_code' => $districtCode,
            'birth_date' => $birthDate,
            'gender' => $gender,
            'registration_number' => $registrationNumber,
            'is_valid' => $birthDate !== null,
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ==================== NIK SECTION ====================
                Section::make('🪪 Verifikasi NIK')
                    ->description('NIK wajib diisi pertama kali. Data akan otomatis terisi dari NIK.')
                    ->schema([
                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Kependudukan)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->regex('/^[0-9]{16}$/')
                            ->mask('9999999999999999')
                            ->placeholder('Contoh: 3578125704850007')
                            ->extraInputAttributes([
                                'maxlength' => 16,
                                'inputmode' => 'numeric',
                                'style' => 'font-size: 1.1rem; letter-spacing: 2px; font-family: monospace; font-weight: bold;',
                            ])
                            ->live(debounce: 300)
                            ->hint(function (Get $get) {
                                $nik = $get('nik') ?? '';
                                $len = strlen($nik);
                                if ($len === 0)
                                    return '0/16 digit';
                                if ($len < 16)
                                    return "{$len}/16 digit ⚠️";
                                if ($len === 16)
                                    return '16/16 digit ✅';
                                return 'Terlalu panjang! ❌';
                            })
                            ->hintColor(function (Get $get) {
                                $len = strlen($get('nik') ?? '');
                                if ($len === 16)
                                    return 'success';
                                if ($len > 16)
                                    return 'danger';
                                return 'warning';
                            })
                            ->helperText(function (Get $get) {
                                $nik = $get('nik') ?? '';
                                if (strlen($nik) !== 16) {
                                    return 'Masukkan tepat 16 digit angka NIK';
                                }

                                $parsed = self::parseNik($nik);
                                if (empty($parsed) || !($parsed['is_valid'] ?? false)) {
                                    return '❌ Format NIK tidak valid - tanggal lahir tidak bisa diparse';
                                }

                                $gender = $parsed['gender'] === 'P' ? 'Perempuan' : 'Laki-laki';
                                $birth = $parsed['birth_date']
                                    ? Carbon::parse($parsed['birth_date'])->format('d/m/Y')
                                    : 'Tidak valid';

                                return "✅ NIK Valid | Wilayah: {$parsed['province_code']}.{$parsed['regency_code']}.{$parsed['district_code']} | {$gender} | Lahir: {$birth}";
                            })
                            ->afterStateUpdated(function (?string $state, Set $set, $record) {
                                if (!$state || strlen($state) !== 16)
                                    return;

                                // Check duplicate
                                $query = Respondent::where('nik', $state);
                                if ($record)
                                    $query->where('id', '!=', $record->id);
                                $existing = $query->first();

                                if ($existing) {
                                    Notification::make()
                                        ->title('❌ NIK Sudah Terdaftar!')
                                        ->body("NIK ini sudah terdaftar atas nama: {$existing->nama_lengkap}")
                                        ->danger()
                                        ->persistent()
                                        ->send();
                                    return;
                                }

                                // Parse and auto-fill
                                $parsed = self::parseNik($state);
                                if (!empty($parsed) && ($parsed['is_valid'] ?? false)) {
                                    $set('jenis_kelamin', $parsed['gender']);
                                    $set('tanggal_lahir', $parsed['birth_date']);

                                    $province = Province::where('code', $parsed['province_code'])->first();
                                    if ($province) {
                                        $set('province_id', $province->id);
                                    }

                                    $genderText = $parsed['gender'] === 'P' ? 'Perempuan' : 'Laki-laki';
                                    $birthText = Carbon::parse($parsed['birth_date'])->format('d/m/Y');

                                    Notification::make()
                                        ->title('✅ NIK Valid!')
                                        ->body("Jenis Kelamin: {$genderText}, Tanggal Lahir: {$birthText}")
                                        ->success()
                                        ->send();
                                }
                            })
                            ->validationMessages([
                                'regex' => 'NIK harus tepat 16 digit angka',
                                'unique' => 'NIK sudah terdaftar dalam sistem',
                            ]),
                    ])
                    ->columnSpanFull(),
                // ==================== DATA KELUARGA ====================
                Section::make('👨‍👩‍👧‍👦 Data Keluarga')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('kartu_keluarga_id')
                                ->label('Kartu Keluarga')
                                ->relationship('kartuKeluarga', 'no_kk')
                                ->searchable()
                                ->preload()
                                ->placeholder('Pilih No. KK'),
                            Select::make('citizen_type_id')
                                ->label('Tipe Warga')
                                ->options(CitizenType::pluck('name', 'id'))
                                ->required()
                                ->searchable()
                                ->placeholder('Pilih tipe warga'),
                            Select::make('status_hubungan')
                                ->label('Status dalam Keluarga')
                                ->options(Respondent::FAMILY_RELATIONS)
                                ->searchable()
                                ->placeholder('Pilih status'),
                        ]),
                    ])
                    ->columnSpanFull(),
                // ==================== DATA PRIBADI ====================
                Section::make('👤 Data Pribadi')
                    ->schema([
                        TextInput::make('nama_lengkap')
                            ->label('Nama Lengkap (sesuai KTP)')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan nama lengkap')
                            ->columnSpanFull(),
                        Grid::make(3)->schema([
                            TextInput::make('tempat_lahir')
                                ->label('Tempat Lahir')
                                ->placeholder('Kota kelahiran'),
                            DatePicker::make('tanggal_lahir')
                                ->label('Tanggal Lahir')
                                ->displayFormat('d/m/Y')
                                ->native(false)
                                ->helperText('Auto dari NIK'),
                            Select::make('jenis_kelamin')
                                ->label('Jenis Kelamin')
                                ->options(Respondent::GENDERS)
                                ->required()
                                ->helperText('Auto dari NIK'),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('agama')
                                ->label('Agama')
                                ->options(Respondent::RELIGIONS)
                                ->required()
                                ->searchable(),
                            Select::make('status_perkawinan')
                                ->label('Status Perkawinan')
                                ->options(Respondent::MARITAL_STATUSES)
                                ->searchable(),
                            Select::make('golongan_darah')
                                ->label('Golongan Darah')
                                ->options(Respondent::BLOOD_TYPES),
                        ]),
                        Grid::make(3)->schema([
                            Select::make('pendidikan')
                                ->label('Pendidikan Terakhir')
                                ->options(Respondent::EDUCATIONS)
                                ->searchable(),
                            TextInput::make('pekerjaan')
                                ->label('Pekerjaan')
                                ->placeholder('Jenis pekerjaan'),
                            Select::make('kewarganegaraan')
                                ->label('Kewarganegaraan')
                                ->options(Respondent::NATIONALITIES)
                                ->default('WNI')
                                ->required(),
                        ]),
                    ])
                    ->columnSpanFull(),
                // ==================== ALAMAT ====================
                Section::make('📍 Alamat')
                    ->schema([
                        Textarea::make('alamat')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->placeholder('Jalan, Gang, No. Rumah, dll.')
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            TextInput::make('rt')
                                ->label('RT')
                                ->maxLength(3)
                                ->placeholder('000'),
                            TextInput::make('rw')
                                ->label('RW')
                                ->maxLength(3)
                                ->placeholder('000'),
                        ]),
                        // Map Picker
                        ViewField::make('map')
                            ->label('📍 Pilih Lokasi di Peta')
                            ->view('filament.forms.components.map-picker')
                            ->columnSpanFull(),
                        Grid::make(2)->schema([
                            TextInput::make('latitude')
                                ->label('Latitude')
                                ->required()
                                ->numeric()
                                ->inputMode('decimal')
                                ->placeholder('-2.5489')
                                ->helperText('Koordinat dari peta di atas'),
                            TextInput::make('longitude')
                                ->label('Longitude')
                                ->required()
                                ->numeric()
                                ->inputMode('decimal')
                                ->placeholder('140.7183')
                                ->helperText('Koordinat dari peta di atas'),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('province_id')
                                ->label('Provinsi')
                                ->options(Province::orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->live()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('regency_id', null);
                                    $set('district_id', null);
                                    $set('village_id', null);
                                }),
                            Select::make('regency_id')
                                ->label('Kabupaten/Kota')
                                ->options(function (Get $get) {
                                    if (!$get('province_id'))
                                        return [];
                                    return Regency::where('province_id', $get('province_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(function (Set $set) {
                                    $set('district_id', null);
                                    $set('village_id', null);
                                }),
                        ]),
                        Grid::make(2)->schema([
                            Select::make('district_id')
                                ->label('Kecamatan')
                                ->options(function (Get $get) {
                                    if (!$get('regency_id'))
                                        return [];
                                    return District::where('regency_id', $get('regency_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id');
                                })
                                ->searchable()
                                ->live()
                                ->afterStateUpdated(fn(Set $set) => $set('village_id', null)),
                            Select::make('village_id')
                                ->label('Kelurahan/Desa')
                                ->options(function (Get $get) {
                                    if (!$get('district_id'))
                                        return [];
                                    return Village::where('district_id', $get('district_id'))
                                        ->orderBy('name')
                                        ->pluck('name', 'id');
                                })
                                ->searchable(),
                        ]),
                    ])
                    ->columnSpanFull(),
                // ==================== KONTAK ====================
                Section::make('📞 Kontak')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('No. HP (WhatsApp)')
                                ->tel()
                                ->prefix('+62')
                                ->placeholder('81234567890')
                                ->maxLength(15),
                            TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->placeholder('email@example.com'),
                        ]),
                    ])
                    ->columnSpanFull(),
                // ==================== DOKUMEN ====================
                Section::make('📄 Dokumen')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('ktp_image_path')
                                ->label('Foto KTP')
                                ->image()
                                ->imageEditor()
                                ->directory('ktp')
                                ->maxSize(2048)
                                ->helperText('Upload foto KTP (max 2MB)'),
                            FileUpload::make('selfie_ktp_path')
                                ->label('Selfie dengan KTP')
                                ->image()
                                ->imageEditor()
                                ->directory('selfie-ktp')
                                ->maxSize(2048)
                                ->helperText('Upload selfie memegang KTP (max 2MB)'),
                        ]),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
                // ==================== VERIFIKASI ====================
                Section::make('✅ Verifikasi')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('verification_status')
                                ->label('Status Verifikasi')
                                ->options(Respondent::VERIFICATION_STATUSES)
                                ->default('pending')
                                ->required()
                                ->live(),
                            Select::make('verified_by')
                                ->label('Diverifikasi Oleh')
                                ->relationship('verifier', 'name')
                                ->searchable()
                                ->visible(fn(Get $get) => $get('verification_status') === 'verified'),
                        ]),
                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->rows(2)
                            ->visible(fn(Get $get) => $get('verification_status') === 'rejected')
                            ->columnSpanFull(),
                        Textarea::make('verification_notes')
                            ->label('Catatan Verifikasi')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull()
                    ->collapsed(),
            ]);
    }
}
