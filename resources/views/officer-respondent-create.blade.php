@extends('layouts.app')

@section('title', 'Daftarkan Responden - Officer Entry')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('officer.entry.questionnaire', $questionnaire_id) }}" class="text-gray-600 hover:text-gray-800 flex items-center gap-2">
                <span>←</span> Kembali
            </a>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between max-w-2xl mx-auto">
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-yellow-500 text-black rounded-full font-bold step-indicator" data-step="1">1</div>
                    <div class="flex-1 h-1 bg-yellow-500 step-line" data-step="1"></div>
                </div>
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold step-indicator" data-step="2">2</div>
                    <div class="flex-1 h-1 bg-gray-300 step-line" data-step="2"></div>
                </div>
                <div class="flex-1 flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold step-indicator" data-step="3">3</div>
                    <div class="flex-1 h-1 bg-gray-300 step-line" data-step="3"></div>
                </div>
                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold step-indicator" data-step="4">4</div>
            </div>
            <div class="flex items-center justify-between max-w-2xl mx-auto mt-2 text-xs text-gray-600">
                <span class="w-24 text-center">Data Pribadi</span>
                <span class="w-24 text-center">Kontak</span>
                <span class="w-24 text-center">Alamat</span>
                <span class="w-24 text-center">Verifikasi</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-yellow-500">
            <div class="papua-gradient px-8 py-6">
                <h2 class="text-2xl font-bold text-yellow-400 text-center">Daftarkan Responden Baru</h2>
                <p class="text-gray-300 text-center text-sm mt-1">Lengkapi data diri untuk berpartisipasi dalam survey</p>
            </div>

            <div class="p-8">
                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('officer.respondent.store') }}" method="POST" enctype="multipart/form-data" id="registrationForm">
                    @csrf
                    <input type="hidden" name="questionnaire_id" value="{{ $questionnaire_id }}">

                    <!-- Step 1: Data Pribadi -->
                    <div class="form-step active" data-step="1">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                <span class="text-2xl mr-2">👤</span> Data Pribadi
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap (sesuai KTP) *</label>
                                    <input type="text" name="nama_lengkap" id="nama_lengkap" value="{{ old('nama_lengkap') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition uppercase"
                                           placeholder="Masukkan nama lengkap sesuai KTP"
                                           style="text-transform: uppercase;"
                                           required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">NIK (16 digit) *</label>
                                        <input type="text" name="nik" value="{{ old('nik') }}" id="nik"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               maxlength="16" pattern="[0-9]{16}"
                                               placeholder="1234567890123456"
                                               required>
                                        <p class="text-xs text-gray-500 mt-1" id="nikStatus">16 digit angka - Data akan terisi otomatis</p>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Nomor KK (16 digit)</label>
                                        <input type="text" name="no_kk" value="{{ old('no_kk') }}" id="no_kk"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               maxlength="16" pattern="[0-9]{16}"
                                               placeholder="1234567890123456">
                                        <p class="text-xs text-gray-500 mt-1">Opsional</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Tanggal Lahir *</label>
                                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" id="tanggal_lahir"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition bg-gray-50"
                                               readonly required>
                                        <p class="text-xs text-gray-500 mt-1">Otomatis dari NIK</p>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Tempat Lahir *</label>
                                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               placeholder="Kota/Kabupaten"
                                               required>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Jenis Kelamin *</label>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="flex items-center justify-center px-4 py-3 border-2 rounded-lg cursor-pointer transition hover:border-yellow-500 gender-option" id="gender-l-label">
                                                <input type="radio" name="jenis_kelamin" value="L" id="gender-l" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} class="hidden" required disabled>
                                                <span class="flex items-center gap-2">
                                                    <span class="text-xl">👨</span>
                                                    <span class="font-medium">Laki-laki</span>
                                                </span>
                                            </label>
                                            <label class="flex items-center justify-center px-4 py-3 border-2 rounded-lg cursor-pointer transition hover:border-yellow-500 gender-option" id="gender-p-label">
                                                <input type="radio" name="jenis_kelamin" value="P" id="gender-p" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} class="hidden" required disabled>
                                                <span class="flex items-center gap-2">
                                                    <span class="text-xl">👩</span>
                                                    <span class="font-medium">Perempuan</span>
                                                </span>
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Otomatis dari NIK</p>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Agama *</label>
                                        <select name="agama"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                                required>
                                            <option value="">Pilih Agama...</option>
                                            <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                            <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                            <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                            <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                            <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Golongan Darah</label>
                                        <select name="golongan_darah"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                            <option value="">Pilih...</option>
                                            <option value="A" {{ old('golongan_darah') == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ old('golongan_darah') == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="AB" {{ old('golongan_darah') == 'AB' ? 'selected' : '' }}>AB</option>
                                            <option value="O" {{ old('golongan_darah') == 'O' ? 'selected' : '' }}>O</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Status Perkawinan</label>
                                        <select name="status_perkawinan"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                            <option value="">Pilih...</option>
                                            <option value="Belum Kawin" {{ old('status_perkawinan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                            <option value="Kawin" {{ old('status_perkawinan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                            <option value="Cerai Hidup" {{ old('status_perkawinan') == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                            <option value="Cerai Mati" {{ old('status_perkawinan') == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Status Hubungan</label>
                                        <select name="status_hubungan"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                            <option value="">Pilih...</option>
                                            <option value="Kepala Keluarga" {{ old('status_hubungan') == 'Kepala Keluarga' ? 'selected' : '' }}>Kepala Keluarga</option>
                                            <option value="Suami" {{ old('status_hubungan') == 'Suami' ? 'selected' : '' }}>Suami</option>
                                            <option value="Istri" {{ old('status_hubungan') == 'Istri' ? 'selected' : '' }}>Istri</option>
                                            <option value="Anak" {{ old('status_hubungan') == 'Anak' ? 'selected' : '' }}>Anak</option>
                                            <option value="Menantu" {{ old('status_hubungan') == 'Menantu' ? 'selected' : '' }}>Menantu</option>
                                            <option value="Cucu" {{ old('status_hubungan') == 'Cucu' ? 'selected' : '' }}>Cucu</option>
                                            <option value="Orang Tua" {{ old('status_hubungan') == 'Orang Tua' ? 'selected' : '' }}>Orang Tua</option>
                                            <option value="Mertua" {{ old('status_hubungan') == 'Mertua' ? 'selected' : '' }}>Mertua</option>
                                            <option value="Famili Lain" {{ old('status_hubungan') == 'Famili Lain' ? 'selected' : '' }}>Famili Lain</option>
                                            <option value="Pembantu" {{ old('status_hubungan') == 'Pembantu' ? 'selected' : '' }}>Pembantu</option>
                                            <option value="Lainnya" {{ old('status_hubungan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Tipe Warga *</label>
                                        <select name="citizen_type_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                                required>
                                            <option value="">Pilih Tipe Warga...</option>
                                            @foreach($citizenTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('citizen_type_id') == $type->id ? 'selected' : '' }}>
                                                    {{ $type->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Pendidikan</label>
                                        <select name="education_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                            <option value="">Pilih Pendidikan...</option>
                                            @foreach($educations as $edu)
                                                <option value="{{ $edu->id }}" {{ old('education_id') == $edu->id ? 'selected' : '' }}>
                                                    {{ $edu->education }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Pekerjaan</label>
                                    <select name="occupation_id"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                        <option value="">Pilih Pekerjaan...</option>
                                        @foreach($occupations as $occ)
                                            <option value="{{ $occ->id }}" {{ old('occupation_id') == $occ->id ? 'selected' : '' }}>
                                                {{ $occ->occupation }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="button" class="next-step px-6 py-3 bg-yellow-500 text-black font-bold rounded-lg hover:bg-yellow-400 transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Kontak -->
                    <div class="form-step" data-step="2" style="display: none;">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                <span class="text-2xl mr-2">📱</span> Informasi Kontak
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Nomor HP (WhatsApp) *</label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">+62</span>
                                        <input type="tel" name="no_hp" value="{{ old('no_hp') }}" id="no_hp"
                                               class="w-full pl-14 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               placeholder="81234567890"
                                               pattern="[0-9]{9,13}" required>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Contoh: 81234567890 (tanpa 0 di depan)</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Email (Opsional)</label>
                                    <input type="email" name="email" value="{{ old('email') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                           placeholder="nama@email.com">
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" class="prev-step px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                                ← Sebelumnya
                            </button>
                            <button type="button" class="next-step px-6 py-3 bg-yellow-500 text-black font-bold rounded-lg hover:bg-yellow-400 transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Alamat -->
                    <div class="form-step" data-step="3" style="display: none;">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                <span class="text-2xl mr-2">🏠</span> Alamat
                            </h3>

                            <div class="space-y-4">
                                <!-- Wilayah sesuai KTP -->
                                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
                                    <p class="text-sm text-blue-800 font-medium">📍 Alamat sesuai KTP (Otomatis terisi dari NIK)</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Provinsi *</label>
                                        <select name="province_id" id="province_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition bg-gray-50"
                                                required>
                                            <option value="">Pilih Provinsi...</option>
                                            @foreach($provinces as $province)
                                                <option value="{{ $province->id }}" data-code="{{ $province->code }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>
                                                    {{ $province->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Kabupaten/Kota *</label>
                                        <select name="regency_id" id="regency_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition bg-gray-50"
                                                required>
                                            <option value="">Pilih Kabupaten/Kota...</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Kecamatan *</label>
                                        <select name="district_id" id="district_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition bg-gray-50"
                                                required>
                                            <option value="">Pilih Kecamatan...</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Kelurahan/Desa *</label>
                                        <select name="village_id" id="village_id"
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition bg-gray-50"
                                                required>
                                            <option value="">Pilih Kelurahan/Desa...</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="md:col-span-2">
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Alamat Lengkap *</label>
                                        <textarea name="alamat" rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                                  placeholder="Jalan, Nama Jalan, Nomor Rumah"
                                                  required>{{ old('alamat') }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">RT *</label>
                                        <input type="text" name="rt" value="{{ old('rt') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               placeholder="001"
                                               maxlength="3"
                                               pattern="[0-9]{1,3}"
                                               required>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">RW *</label>
                                        <input type="text" name="rw" value="{{ old('rw') }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               placeholder="001"
                                               maxlength="3"
                                               pattern="[0-9]{1,3}"
                                               required>
                                    </div>
                                </div>

                                <!-- GPS Location Picker -->
                                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                                    <p class="text-sm text-green-800 font-medium mb-2">📍 Lokasi GPS Rumah</p>
                                    <p class="text-xs text-green-700">Klik pada peta untuk menandai lokasi rumah Anda (default: lokasi KTP)</p>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    <div id="map" class="w-full h-96 rounded-lg border-2 border-gray-300"></div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Latitude *</label>
                                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                                   readonly
                                                   required>
                                        </div>

                                        <div>
                                            <label class="block text-gray-700 text-sm font-medium mb-2">Longitude *</label>
                                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50"
                                                   readonly
                                                   required>
                                        </div>
                                    </div>

                                    <button type="button" id="getCurrentLocation" class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition">
                                        <span>📍</span>
                                        <span>Gunakan Lokasi Saat Ini</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" class="prev-step px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                                ← Sebelumnya
                            </button>
                            <button type="button" class="next-step px-6 py-3 bg-yellow-500 text-black font-bold rounded-lg hover:bg-yellow-400 transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Upload KTP -->
                    <div class="form-step" data-step="4" style="display: none;">
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                <span class="text-2xl mr-2">📷</span> Verifikasi Identitas
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Upload Foto KTP *</label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-yellow-500 transition cursor-pointer" id="ktpUploadArea">
                                        <input type="file" name="foto_ktp" id="foto_ktp" accept="image/*" class="hidden" required>
                                        <div id="ktpPlaceholder">
                                            <div class="text-5xl mb-3">📸</div>
                                            <p class="text-gray-700 font-medium mb-1">Klik untuk upload foto KTP</p>
                                            <p class="text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
                                        </div>
                                        <div id="ktpPreview" class="hidden">
                                            <img src="" alt="Preview KTP" class="max-h-64 mx-auto rounded-lg mb-3">
                                            <button type="button" id="changeKtpBtn" class="text-yellow-600 text-sm font-medium hover:text-yellow-700">
                                                Ganti Foto
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="font-medium text-blue-900 mb-2 flex items-center">
                                        <span class="mr-2">ℹ️</span> Tips Foto KTP:
                                    </h4>
                                    <ul class="text-sm text-blue-800 space-y-1">
                                        <li>✓ Pastikan foto jelas dan tidak blur</li>
                                        <li>✓ Semua teks dapat terbaca dengan baik</li>
                                        <li>✓ Tidak ada refleksi atau bayangan</li>
                                        <li>✓ KTP dalam keadaan asli (bukan fotokopi)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between">
                            <button type="button" class="prev-step px-6 py-3 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">
                                ← Sebelumnya
                            </button>
                            <button type="submit" id="submitBtn"
                                    class="px-6 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 font-bold rounded-lg hover:from-black hover:to-gray-800 transition shadow-lg border border-yellow-500">
                                <span class="flex items-center gap-2">
                                    <span id="submitText">Daftarkan Responden</span>
                                    <svg id="submitIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <svg id="loadingIcon" class="w-5 h-5 animate-spin hidden" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('officer.entry.questionnaire', $questionnaire_id) }}" class="text-gray-600 text-sm hover:text-gray-800">
                ← Kembali ke Daftar Responden
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Multi-step form functionality
    let currentStep = 1;
    const totalSteps = 4;

    function updateStepIndicators() {
        document.querySelectorAll('.step-indicator').forEach((indicator, index) => {
            const step = index + 1;
            if (step < currentStep) {
                indicator.classList.remove('bg-gray-300', 'text-gray-600');
                indicator.classList.add('bg-green-500', 'text-white');
            } else if (step === currentStep) {
                indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-green-500');
                indicator.classList.add('bg-yellow-500', 'text-black');
            } else {
                indicator.classList.remove('bg-yellow-500', 'text-black', 'bg-green-500', 'text-white');
                indicator.classList.add('bg-gray-300', 'text-gray-600');
            }
        });

        document.querySelectorAll('.step-line').forEach((line, index) => {
            const step = index + 1;
            if (step < currentStep) {
                line.classList.remove('bg-gray-300');
                line.classList.add('bg-green-500');
            } else if (step === currentStep) {
                line.classList.remove('bg-gray-300', 'bg-green-500');
                line.classList.add('bg-yellow-500');
            } else {
                line.classList.remove('bg-yellow-500', 'bg-green-500');
                line.classList.add('bg-gray-300');
            }
        });
    }

    function showStep(step) {
        document.querySelectorAll('.form-step').forEach(s => s.style.display = 'none');
        const targetStep = document.querySelector(`.form-step[data-step="${step}"]`);
        if (targetStep) {
            targetStep.style.display = 'block';
            currentStep = step;
            updateStepIndicators();
            window.scrollTo({ top: 0, behavior: 'smooth' });

            // Refresh map when Step 3 (Alamat) becomes visible
            if (step === 3 && map) {
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            }
        }
    }

    function validateStep(step) {
        const stepElement = document.querySelector(`.form-step[data-step="${step}"]`);
        const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');

        for (let input of inputs) {
            if (!input.value || (input.type === 'radio' && !stepElement.querySelector(`input[name="${input.name}"]:checked`))) {
                input.focus();
                input.classList.add('border-red-500');
                setTimeout(() => input.classList.remove('border-red-500'), 2000);
                return false;
            }
        }
        return true;
    }

    document.querySelectorAll('.next-step').forEach(btn => {
        btn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    showStep(currentStep + 1);
                }
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', function() {
            if (currentStep > 1) {
                showStep(currentStep - 1);
            }
        });
    });

    // Gender selection styling
    document.querySelectorAll('.gender-option').forEach(label => {
        const radio = label.querySelector('input[type="radio"]');
        if (radio.checked) {
            label.classList.add('border-yellow-500', 'bg-yellow-50');
        }

        label.addEventListener('click', function() {
            document.querySelectorAll('.gender-option').forEach(l => {
                l.classList.remove('border-yellow-500', 'bg-yellow-50');
            });
            this.classList.add('border-yellow-500', 'bg-yellow-50');
        });
    });

    // KTP Upload with preview
    const ktpUploadArea = document.getElementById('ktpUploadArea');
    const ktpInput = document.getElementById('foto_ktp');
    const ktpPlaceholder = document.getElementById('ktpPlaceholder');
    const ktpPreview = document.getElementById('ktpPreview');
    const changeKtpBtn = document.getElementById('changeKtpBtn');

    ktpUploadArea.addEventListener('click', () => ktpInput.click());

    ktpInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (file.size > 2048000) {
                alert('Ukuran file terlalu besar. Maksimal 2MB');
                ktpInput.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                ktpPreview.querySelector('img').src = e.target.result;
                ktpPlaceholder.classList.add('hidden');
                ktpPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    changeKtpBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        ktpInput.value = '';
        ktpPlaceholder.classList.remove('hidden');
        ktpPreview.classList.add('hidden');
    });

    // Prevent form resubmission
    const form = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const submitIcon = document.getElementById('submitIcon');
    const loadingIcon = document.getElementById('loadingIcon');

    form.addEventListener('submit', function(e) {
        if (!validateStep(4)) {
            e.preventDefault();
            return;
        }

        submitBtn.disabled = true;
        submitText.textContent = 'Mendaftarkan...';
        submitIcon.classList.add('hidden');
        loadingIcon.classList.remove('hidden');
    });

    // Wilayah cascade selects
    const provinceSelect = document.getElementById('province_id');
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');

    // Use jQuery event listener to work with Select2 trigger
    $(provinceSelect).on('change', function() {
        const provinceId = this.value;
        console.log('Province changed to:', provinceId);

        regencySelect.innerHTML = '<option value="">Memuat...</option>';
        districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
        villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';

        if (provinceId) {
            console.log('Fetching regencies for province:', provinceId);
            const apiUrl = `/api/wilayah/regencies/${provinceId}`;
            console.log('API URL:', apiUrl);

            fetch(apiUrl)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data);
                    console.log('Regencies loaded:', data.data.length, 'items');
                    regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota...</option>';
                    data.data.forEach(item => {
                        regencySelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    // Reinitialize Select2 after updating options
                    $(regencySelect).select2('destroy');
                    $(regencySelect).select2({
                        placeholder: 'Pilih Kabupaten/Kota...',
                        allowClear: false,
                        width: '100%'
                    });
                })
                .catch(error => {
                    console.error('Error loading regencies:', error);
                    regencySelect.innerHTML = '<option value="">Error memuat data</option>';
                });
        } else {
            regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota...</option>';
            $(regencySelect).select2('destroy');
            $(regencySelect).select2({
                placeholder: 'Pilih Kabupaten/Kota...',
                allowClear: false,
                width: '100%'
            });
        }
    });

    // Force uppercase nama lengkap
    const namaLengkapInput = document.getElementById('nama_lengkap');
    if (namaLengkapInput) {
        namaLengkapInput.addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });
    }

    // NIK and No KK numeric only
    ['nik', 'no_kk', 'no_hp'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    });

    // Auto-detect from NIK
    const nikInput = document.getElementById('nik');
    const tanggalLahirInput = document.getElementById('tanggal_lahir');
    const nikStatus = document.getElementById('nikStatus');
    const genderL = document.getElementById('gender-l');
    const genderP = document.getElementById('gender-p');
    const genderLLabel = document.getElementById('gender-l-label');
    const genderPLabel = document.getElementById('gender-p-label');

    nikInput.addEventListener('input', function() {
        const nik = this.value;

        if (nik.length === 16) {
            try {
                // Extract province code (2 digits)
                const provinceCode = nik.substring(0, 2);

                // Extract date of birth (digits 7-12: DDMMYY)
                let day = parseInt(nik.substring(6, 8));
                const month = parseInt(nik.substring(8, 10));
                const year = parseInt(nik.substring(10, 12));

                // Determine gender (if day > 40, it's female)
                let gender = 'L';
                if (day > 40) {
                    day = day - 40;
                    gender = 'P';
                }

                // Convert 2-digit year to 4-digit (assume 1900-2099)
                const fullYear = year >= 0 && year <= 24 ? 2000 + year : 1900 + year;

                // Format date for input (YYYY-MM-DD)
                const formattedDate = `${fullYear}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                // Set birth date
                tanggalLahirInput.value = formattedDate;

                // Set gender
                genderL.disabled = false;
                genderP.disabled = false;

                document.querySelectorAll('.gender-option').forEach(l => {
                    l.classList.remove('border-yellow-500', 'bg-yellow-50');
                });

                if (gender === 'L') {
                    genderL.checked = true;
                    genderLLabel.classList.add('border-yellow-500', 'bg-yellow-50');
                } else {
                    genderP.checked = true;
                    genderPLabel.classList.add('border-yellow-500', 'bg-yellow-50');
                }

                // Try to auto-select province
                // Now province_id uses code (CHAR), so we can directly match
                console.log('Looking for province code:', provinceCode);

                // Province value is now the code itself
                provinceSelect.value = provinceCode;
                $(provinceSelect).val(provinceCode).trigger('change');
                console.log('Province auto-selected, code:', provinceCode);

                // Load regencies using province code
                const apiUrl = `/api/wilayah/regencies/${provinceCode}`;
                console.log('API URL:', apiUrl);

                regencySelect.innerHTML = '<option value="">Memuat...</option>';

                fetch(apiUrl)
                    .then(response => {
                        console.log('Response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('API Response:', data);
                        console.log('Regencies loaded:', data.data.length, 'items');
                        regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota...</option>';
                        data.data.forEach(item => {
                            regencySelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                        });
                        // Reinitialize Select2
                        $(regencySelect).select2('destroy');
                        $(regencySelect).select2({
                            placeholder: 'Pilih Kabupaten/Kota...',
                            allowClear: false,
                            width: '100%'
                        });
                    })
                    .catch(error => {
                        console.error('Error loading regencies:', error);
                        regencySelect.innerHTML = '<option value="">Error memuat data</option>';
                    });

                nikStatus.textContent = '✓ Data berhasil dideteksi dari NIK';
                nikStatus.classList.remove('text-gray-500');
                nikStatus.classList.add('text-green-600', 'font-medium');

            } catch (error) {
                console.error('Error parsing NIK:', error);
                nikStatus.textContent = '⚠ NIK tidak valid, periksa kembali';
                nikStatus.classList.remove('text-green-600');
                nikStatus.classList.add('text-red-600');
            }
        } else {
            nikStatus.textContent = '16 digit angka - Data akan terisi otomatis';
            nikStatus.classList.remove('text-green-600', 'text-red-600');
            nikStatus.classList.add('text-gray-500');
        }
    });

    $(regencySelect).on('change', function() {
        const regencyId = this.value;
        districtSelect.innerHTML = '<option value="">⏳ Memuat kecamatan...</option>';
        districtSelect.disabled = true;
        villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';

        if (regencyId) {
            fetch(`/api/wilayah/districts/${regencyId}`)
                .then(response => response.json())
                .then(data => {
                    districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                    data.data.forEach(item => {
                        districtSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    districtSelect.disabled = false;
                    // Reinitialize Select2
                    $(districtSelect).select2('destroy');
                    $(districtSelect).select2({
                        placeholder: 'Pilih Kecamatan...',
                        allowClear: false,
                        width: '100%'
                    });
                })
                .catch(() => {
                    districtSelect.innerHTML = '<option value="">❌ Error memuat data</option>';
                    districtSelect.disabled = false;
                });
        } else {
            districtSelect.disabled = false;
        }
    });

    $(districtSelect).on('change', function() {
        const districtId = this.value;
        villageSelect.innerHTML = '<option value="">⏳ Memuat kelurahan/desa...</option>';
        villageSelect.disabled = true;

        if (districtId) {
            fetch(`/api/wilayah/villages/${districtId}`)
                .then(response => response.json())
                .then(data => {
                    villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa...</option>';
                    data.data.forEach(item => {
                        villageSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
                    });
                    villageSelect.disabled = false;
                    // Reinitialize Select2
                    $(villageSelect).select2('destroy');
                    $(villageSelect).select2({
                        placeholder: 'Pilih Kelurahan/Desa...',
                        allowClear: false,
                        width: '100%'
                    });
                })
                .catch(() => {
                    villageSelect.innerHTML = '<option value="">❌ Error memuat data</option>';
                    villageSelect.disabled = false;
                });
        } else {
            villageSelect.disabled = false;
        }
    });

    // Initialize first step
    showStep(1);

    // Initialize Map for GPS Location
    let map;
    let marker;
    const defaultLat = -2.5489; // Papua default
    const defaultLng = 140.7168;

    function initMap(lat = defaultLat, lng = defaultLng) {
        if (map) {
            map.remove();
        }

        map = L.map('map').setView([lat, lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);

        // Update coordinates when marker is dragged
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat.toFixed(8);
            document.getElementById('longitude').value = position.lng.toFixed(8);
        });

        // Update coordinates and marker when map is clicked
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat.toFixed(8);
            document.getElementById('longitude').value = e.latlng.lng.toFixed(8);
        });

        // Set initial coordinates
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
    }

    // Initialize map on page load
    initMap();

    // Get current location button
    document.getElementById('getCurrentLocation').addEventListener('click', function() {
        if (navigator.geolocation) {
            this.disabled = true;
            this.innerHTML = '<span>⏳</span><span>Mendapatkan lokasi...</span>';

            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    initMap(lat, lng);

                    this.disabled = false;
                    this.innerHTML = '<span>✓</span><span>Lokasi berhasil didapat!</span>';

                    setTimeout(() => {
                        this.innerHTML = '<span>📍</span><span>Gunakan Lokasi Saat Ini</span>';
                    }, 2000);
                },
                (error) => {
                    alert('Tidak dapat mengambil lokasi. Pastikan GPS dan izin lokasi aktif.');
                    this.disabled = false;
                    this.innerHTML = '<span>📍</span><span>Gunakan Lokasi Saat Ini</span>';
                }
            );
        } else {
            alert('Browser Anda tidak mendukung Geolocation');
        }
    });
});
</script>

<!-- Leaflet CSS & JS for Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Select2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
/* Custom Select2 styling to match form design */
.select2-container--default .select2-selection--single {
    height: 48px;
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 32px;
    color: #374151;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #eab308;
    box-shadow: 0 0 0 2px rgba(234, 179, 8, 0.2);
}

.select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #eab308 !important;
    color: #000;
}
</style>

<script>
$(document).ready(function() {
    // Initialize Select2 for all select elements
    $('select[name="citizen_type_id"]').select2({
        placeholder: 'Pilih Tipe Warga...',
        allowClear: false,
        width: '100%'
    });

    $('select[name="education_id"]').select2({
        placeholder: 'Pilih Pendidikan...',
        allowClear: true,
        width: '100%'
    });

    $('select[name="occupation_id"]').select2({
        placeholder: 'Pilih Pekerjaan...',
        allowClear: true,
        width: '100%'
    }).val('').trigger('change'); // Reset to placeholder

    $('select[name="agama"]').select2({
        placeholder: 'Pilih Agama...',
        allowClear: false,
        width: '100%'
    });

    $('select[name="golongan_darah"]').select2({
        placeholder: 'Pilih Golongan Darah...',
        allowClear: true,
        width: '100%'
    });

    $('select[name="status_perkawinan"]').select2({
        placeholder: 'Pilih Status Perkawinan...',
        allowClear: true,
        width: '100%'
    });

    $('select[name="status_hubungan"]').select2({
        placeholder: 'Pilih Status Hubungan...',
        allowClear: true,
        width: '100%'
    });

    $('select[name="province_id"]').select2({
        placeholder: 'Pilih Provinsi...',
        allowClear: false,
        width: '100%'
    });

    $('select[name="regency_id"]').select2({
        placeholder: 'Pilih Kabupaten/Kota...',
        allowClear: false,
        width: '100%'
    });

    $('select[name="district_id"]').select2({
        placeholder: 'Pilih Kecamatan...',
        allowClear: false,
        width: '100%'
    });

    $('select[name="village_id"]').select2({
        placeholder: 'Pilih Kelurahan/Desa...',
        allowClear: false,
        width: '100%'
    });
});
</script>
@endpush
@endsection
