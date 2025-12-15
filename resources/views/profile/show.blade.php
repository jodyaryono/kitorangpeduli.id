@extends('layouts.app')

@section('title', 'Profil Saya - KitorangPeduli.id')

@section('content')
<div class="py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border-t-4 border-yellow-500">
            <div class="papua-gradient px-8 py-6">
                <h2 class="text-2xl font-bold text-yellow-400 text-center">Profil Saya</h2>
                <p class="text-gray-300 text-center text-sm mt-1">Lihat dan perbarui informasi profil Anda</p>
            </div>

            <div class="p-8">
                @if(session('success'))
                    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <p class="text-sm">{{ session('success') }}</p>
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- View Mode -->
                <div id="viewMode">
                    <!-- Personal Info Card -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between mb-4 pb-2 border-b">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <span class="text-2xl mr-2">👤</span> Data Pribadi
                            </h3>
                            <button onclick="toggleEditMode()" class="bg-yellow-500 text-black px-4 py-2 rounded-lg text-sm font-medium hover:bg-yellow-400 transition">
                                ✏️ Edit Profil
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->nama_lengkap }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">NIK</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->nik }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Tempat, Tanggal Lahir</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->tempat_lahir }}, {{ \Carbon\Carbon::parse($respondent->tanggal_lahir)->format('d F Y') }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Jenis Kelamin</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->jenis_kelamin == 'L' ? '👨 Laki-laki' : '👩 Perempuan' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Agama</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->agama }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Golongan Darah</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->golongan_darah }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Status Perkawinan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->status_perkawinan }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Status Kewarganegaraan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->citizenType->name ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Pendidikan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->education->education ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Pekerjaan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->occupation->occupation ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info Card -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">📱</span> Informasi Kontak
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Nomor HP</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->phone }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Email</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->email ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Address Info Card -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">📍</span> Alamat
                        </h3>

                        <div class="grid grid-cols-1 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->alamat }}</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-xs text-gray-500 mb-1">RT / RW</p>
                                    <p class="text-gray-800 font-medium">{{ $respondent->rt }} / {{ $respondent->rw }}</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Kelurahan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->village->name ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Distrik</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->district->name ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Kota/Kabupaten</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->regency->name ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Provinsi</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->province->name ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- GPS Location Card -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">📍</span> Lokasi GPS
                        </h3>

                        @if($respondent->latitude && $respondent->longitude)
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-xs text-gray-500 mb-1">Latitude</p>
                                        <p class="text-gray-800 font-medium">{{ $respondent->latitude }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <p class="text-xs text-gray-500 mb-1">Longitude</p>
                                        <p class="text-gray-800 font-medium">{{ $respondent->longitude }}</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Peta Lokasi</p>
                                    <div id="map" class="w-full h-64 rounded-lg shadow-md"></div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                                Koordinat GPS tidak tersedia
                            </div>
                        @endif
                    </div>

                    <!-- KTP Photo Card -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">🪪</span> Foto KTP
                        </h3>

                        @if($respondent->getFirstMediaUrl('ktp_image'))
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <img src="{{ $respondent->getFirstMediaUrl('ktp_image') }}" alt="Foto KTP" class="w-full max-w-md rounded-lg shadow-md">
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500">
                                Tidak ada foto KTP
                            </div>
                        @endif
                    </div>

                    <!-- Status Card -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">ℹ️</span>
                            <div>
                                <p class="text-sm font-medium text-blue-800">Status Verifikasi</p>
                                <p class="text-xs text-blue-600">
                                    @if($respondent->verification_status == 'verified')
                                        <span class="text-green-600 font-semibold">✅ Terverifikasi</span>
                                    @elseif($respondent->verification_status == 'rejected')
                                        <span class="text-red-600 font-semibold">❌ Ditolak</span>
                                    @else
                                        <span class="text-yellow-600 font-semibold">⏳ Menunggu Verifikasi</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div id="editMode" class="hidden">
                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between max-w-2xl mx-auto">
                            <div class="flex-1 flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-yellow-500 text-black rounded-full font-bold edit-step-indicator active" data-step="1">1</div>
                                <div class="flex-1 h-1 bg-gray-300 edit-step-line" data-step="1"></div>
                            </div>
                            <div class="flex-1 flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold edit-step-indicator" data-step="2">2</div>
                                <div class="flex-1 h-1 bg-gray-300 edit-step-line" data-step="2"></div>
                            </div>
                            <div class="flex-1 flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold edit-step-indicator" data-step="3">3</div>
                                <div class="flex-1 h-1 bg-gray-300 edit-step-line" data-step="3"></div>
                            </div>
                            <div class="flex items-center justify-center w-10 h-10 bg-gray-300 text-gray-600 rounded-full font-bold edit-step-indicator" data-step="4">4</div>
                        </div>
                        <div class="flex items-center justify-between max-w-2xl mx-auto mt-2 text-xs text-gray-600">
                            <span class="w-24 text-center">Data Pribadi</span>
                            <span class="w-24 text-center">Kontak</span>
                            <span class="w-24 text-center">Alamat</span>
                            <span class="w-24 text-center">Verifikasi</span>
                        </div>
                    </div>

                    <style>
                        .edit-step-indicator.active {
                            background-color: #EAB308 !important;
                            color: black !important;
                        }
                        .edit-step-line.active {
                            background-color: #EAB308 !important;
                        }
                    </style>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                        @csrf

                        <!-- Step 1: Personal Info -->
                        <div class="edit-form-step active" data-step="1">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                    <span class="text-2xl mr-2">👤</span> Data Pribadi
                                </h3>

                                <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Nama Lengkap *</label>
                                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $respondent->nama_lengkap) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition uppercase"
                                           style="text-transform: uppercase;"
                                           required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">NIK *</label>
                                        <input type="text" name="nik" value="{{ old('nik', $respondent->nik) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               maxlength="16" pattern="[0-9]{16}"
                                               required>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Tempat Lahir *</label>
                                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $respondent->tempat_lahir) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Tanggal Lahir *</label>
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $respondent->tanggal_lahir ? \Carbon\Carbon::parse($respondent->tanggal_lahir)->format('Y-m-d') : '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                           required>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Jenis Kelamin *</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="flex items-center justify-center px-4 py-3 border-2 rounded-lg cursor-pointer transition hover:border-yellow-500 {{ old('jenis_kelamin', $respondent->jenis_kelamin) == 'L' ? 'border-yellow-500 bg-yellow-50' : '' }}">
                                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $respondent->jenis_kelamin) == 'L' ? 'checked' : '' }} class="hidden" required>
                                            <span class="flex items-center gap-2">
                                                <span class="text-xl">👨</span>
                                                <span class="font-medium">Laki-laki</span>
                                            </span>
                                        </label>
                                        <label class="flex items-center justify-center px-4 py-3 border-2 rounded-lg cursor-pointer transition hover:border-yellow-500 {{ old('jenis_kelamin', $respondent->jenis_kelamin) == 'P' ? 'border-yellow-500 bg-yellow-50' : '' }}">
                                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $respondent->jenis_kelamin) == 'P' ? 'checked' : '' }} class="hidden" required>
                                            <span class="flex items-center gap-2">
                                                <span class="text-xl">👩</span>
                                                <span class="font-medium">Perempuan</span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Agama *</label>
                                        <select name="agama" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                            <option value="">Pilih Agama...</option>
                                            <option value="Islam" {{ old('agama', $respondent->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                                            <option value="Kristen" {{ old('agama', $respondent->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                            <option value="Katolik" {{ old('agama', $respondent->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                                            <option value="Hindu" {{ old('agama', $respondent->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                            <option value="Buddha" {{ old('agama', $respondent->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                                            <option value="Konghucu" {{ old('agama', $respondent->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Golongan Darah *</label>
                                        <select name="golongan_darah" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                            <option value="">Pilih...</option>
                                            <option value="A" {{ old('golongan_darah', $respondent->golongan_darah) == 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ old('golongan_darah', $respondent->golongan_darah) == 'B' ? 'selected' : '' }}>B</option>
                                            <option value="AB" {{ old('golongan_darah', $respondent->golongan_darah) == 'AB' ? 'selected' : '' }}>AB</option>
                                            <option value="O" {{ old('golongan_darah', $respondent->golongan_darah) == 'O' ? 'selected' : '' }}>O</option>
                                            <option value="-" {{ old('golongan_darah', $respondent->golongan_darah) == '-' ? 'selected' : '' }}>Tidak Diketahui</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Status Perkawinan *</label>
                                    <select name="status_perkawinan" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                        <option value="">Pilih...</option>
                                        <option value="Belum Kawin" {{ old('status_perkawinan', $respondent->status_perkawinan) == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                                        <option value="Kawin" {{ old('status_perkawinan', $respondent->status_perkawinan) == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                                        <option value="Cerai Hidup" {{ old('status_perkawinan', $respondent->status_perkawinan) == 'Cerai Hidup' ? 'selected' : '' }}>Cerai Hidup</option>
                                        <option value="Cerai Mati" {{ old('status_perkawinan', $respondent->status_perkawinan) == 'Cerai Mati' ? 'selected' : '' }}>Cerai Mati</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Status Kewarganegaraan *</label>
                                        <select name="citizen_type_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                            <option value="">Pilih...</option>
                                            @foreach($citizenTypes as $type)
                                                <option value="{{ $type->id }}" {{ old('citizen_type_id', $respondent->citizen_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Pendidikan *</label>
                                        <select name="education_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                            <option value="">Pilih...</option>
                                            @foreach($educations as $edu)
                                                <option value="{{ $edu->id }}" {{ old('education_id', $respondent->education_id) == $edu->id ? 'selected' : '' }}>{{ $edu->education }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Pekerjaan *</label>
                                        <select name="occupation_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                            <option value="">Pilih...</option>
                                            @foreach($occupations as $occ)
                                                <option value="{{ $occ->id }}" {{ old('occupation_id', $respondent->occupation_id) == $occ->id ? 'selected' : '' }}>{{ $occ->occupation }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-end gap-3 mt-6">
                                <button type="button" onclick="toggleEditMode()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ❌ Batal
                                </button>
                                <button type="button" onclick="nextEditStep()" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Contact Info -->
                        <div class="edit-form-step" data-step="2" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                    <span class="text-2xl mr-2">📱</span> Informasi Kontak
                                </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Nomor HP *</label>
                                    <input type="text" name="phone" value="{{ old('phone', $respondent->phone) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                           maxlength="15" pattern="[0-9+]+"
                                           placeholder="62xxx" required>
                                    <p class="text-xs text-gray-500 mt-1">Format: 628xxxxxxxxx</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Email (Opsional)</label>
                                    <input type="email" name="email" value="{{ old('email', $respondent->email) }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                           placeholder="email@example.com">
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between gap-3 mt-6">
                                <button type="button" onclick="prevEditStep()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ← Sebelumnya
                                </button>
                                <button type="button" onclick="nextEditStep()" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Address Info -->
                        <div class="edit-form-step" data-step="3" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                    <span class="text-2xl mr-2">📍</span> Alamat
                                </h3>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Alamat Lengkap *</label>
                                        <textarea name="alamat" rows="3"
                                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                                  required>{{ old('alamat', $respondent->alamat) }}</textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">RT *</label>
                                        <input type="text" name="rt" value="{{ old('rt', $respondent->rt) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               maxlength="3"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">RW *</label>
                                        <input type="text" name="rw" value="{{ old('rw', $respondent->rw) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               maxlength="3"
                                               required>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Provinsi *</label>
                                    <select name="province_id" id="province_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                        <option value="">Pilih Provinsi...</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}" {{ old('province_id', $respondent->province_id) == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Kota/Kabupaten *</label>
                                    <select name="regency_id" id="regency_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                        <option value="{{ $respondent->regency_id }}">{{ $respondent->regency->name ?? 'Pilih Kota/Kabupaten...' }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Distrik *</label>
                                    <select name="district_id" id="district_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                        <option value="{{ $respondent->district_id }}">{{ $respondent->district->name ?? 'Pilih Distrik...' }}</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Kelurahan *</label>
                                    <select name="village_id" id="village_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition" required>
                                        <option value="{{ $respondent->village_id }}">{{ $respondent->village->name ?? 'Pilih Kelurahan...' }}</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Latitude (Opsional)</label>
                                        <input type="text" name="latitude" id="edit_latitude" value="{{ old('latitude', $respondent->latitude) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               placeholder="-2.5333" readonly>
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-2">Longitude (Opsional)</label>
                                        <input type="text" name="longitude" id="edit_longitude" value="{{ old('longitude', $respondent->longitude) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition"
                                               placeholder="140.7167" readonly>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <p class="text-sm font-medium text-gray-700 mb-2">📍 Klik pada peta untuk mengatur lokasi</p>
                                    <div id="editMap" class="w-full h-64 rounded-lg shadow-md mb-2"></div>
                                    <button type="button" onclick="getMyLocation()" class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-600 transition">
                                        📍 Gunakan Lokasi Saya Saat Ini
                                    </button>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between gap-3 mt-6">
                                <button type="button" onclick="prevEditStep()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ← Sebelumnya
                                </button>
                                <button type="button" onclick="nextEditStep()" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Verification -->
                        <div class="edit-form-step" data-step="4" style="display: none;">
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                                    <span class="text-2xl mr-2">🪪</span> Foto KTP
                                </h3>

                                @if($respondent->getFirstMediaUrl('ktp_image'))
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-2">Foto KTP saat ini:</p>
                                        <img src="{{ $respondent->getFirstMediaUrl('ktp_image') }}" alt="Foto KTP" class="w-full max-w-md rounded-lg shadow-md">
                                    </div>
                                @endif

                                <div>
                                    <label class="block text-gray-700 text-sm font-medium mb-2">Upload Foto KTP Baru (Opsional)</label>
                                    <input type="file" name="foto_ktp" accept="image/*"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
                                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maksimal 2MB. Kosongkan jika tidak ingin mengganti.</p>
                                </div>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between gap-3 mt-6">
                                <button type="button" onclick="prevEditStep()" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ← Sebelumnya
                                </button>
                                <button type="submit" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    💾 Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Back to Home Button -->
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="inline-block bg-gray-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-700 transition">
                ← Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<script>
// Handle profile form submission with loading popup
document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    // Show loading popup
    const loadingPopup = document.createElement('div');
    loadingPopup.id = 'loadingPopup';
    loadingPopup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
    loadingPopup.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm mx-4">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-yellow-500 mb-4"></div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Menyimpan Perubahan...</h3>
                <p class="text-gray-600 text-center">Mohon tunggu, profil Anda sedang diperbarui</p>
            </div>
        </div>
    `;
    document.body.appendChild(loadingPopup);

    // Disable submit button
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
    }

    // Submit the form
    this.submit();
});

// Load regencies when province changes
document.getElementById('province_id')?.addEventListener('change', async function() {
    const provinceId = this.value;
    const regencySelect = document.getElementById('regency_id');
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');

    regencySelect.innerHTML = '<option value="">Loading...</option>';
    districtSelect.innerHTML = '<option value="">Pilih Distrik...</option>';
    villageSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';

    if (provinceId) {
        try {
            const response = await fetch(`/api/wilayah/regencies/${provinceId}`);
            const regencies = await response.json();

            regencySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten...</option>';
            regencies.forEach(regency => {
                regencySelect.innerHTML += `<option value="${regency.id}">${regency.name}</option>`;
            });
        } catch (error) {
            console.error('Error loading regencies:', error);
            regencySelect.innerHTML = '<option value="">Error loading data</option>';
        }
    }
});

// Load districts when regency changes
document.getElementById('regency_id')?.addEventListener('change', async function() {
    const regencyId = this.value;
    const districtSelect = document.getElementById('district_id');
    const villageSelect = document.getElementById('village_id');

    districtSelect.innerHTML = '<option value="">Loading...</option>';
    villageSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';

    if (regencyId) {
        try {
            const response = await fetch(`/api/wilayah/districts/${regencyId}`);
            const districts = await response.json();

            districtSelect.innerHTML = '<option value="">Pilih Distrik...</option>';
            districts.forEach(district => {
                districtSelect.innerHTML += `<option value="${district.id}">${district.name}</option>`;
            });
        } catch (error) {
            console.error('Error loading districts:', error);
            districtSelect.innerHTML = '<option value="">Error loading data</option>';
        }
    }
});

// Load villages when district changes
document.getElementById('district_id')?.addEventListener('change', async function() {
    const districtId = this.value;
    const villageSelect = document.getElementById('village_id');

    villageSelect.innerHTML = '<option value="">Loading...</option>';

    if (districtId) {
        try {
            const response = await fetch(`/api/wilayah/villages/${districtId}`);
            const villages = await response.json();

            villageSelect.innerHTML = '<option value="">Pilih Kelurahan...</option>';
            villages.forEach(village => {
                villageSelect.innerHTML += `<option value="${village.id}">${village.name}</option>`;
            });
        } catch (error) {
            console.error('Error loading villages:', error);
            villageSelect.innerHTML = '<option value="">Error loading data</option>';
        }
    }
});

// Initialize map if coordinates exist
@if($respondent->latitude && $respondent->longitude)
document.addEventListener('DOMContentLoaded', function() {
    // Load Leaflet CSS
    const leafletCSS = document.createElement('link');
    leafletCSS.rel = 'stylesheet';
    leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(leafletCSS);

    // Load Leaflet JS
    const leafletJS = document.createElement('script');
    leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    leafletJS.onload = function() {
        const lat = {{ $respondent->latitude }};
        const lng = {{ $respondent->longitude }};

        const map = L.map('map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup('<b>Lokasi Anda</b><br>{{ $respondent->alamat }}')
            .openPopup();
    };
    document.head.appendChild(leafletJS);
});
@endif

// Initialize edit map
let editMap = null;
let editMarker = null;

function initEditMap() {
    if (!document.getElementById('editMap')) return;

    // Load Leaflet if not loaded
    if (typeof L === 'undefined') {
        const leafletCSS = document.createElement('link');
        leafletCSS.rel = 'stylesheet';
        leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        document.head.appendChild(leafletCSS);

        const leafletJS = document.createElement('script');
        leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        leafletJS.onload = function() {
            setupEditMap();
        };
        document.head.appendChild(leafletJS);
    } else {
        setupEditMap();
    }
}

function setupEditMap() {
    const lat = {{ $respondent->latitude ?? -2.5333 }};
    const lng = {{ $respondent->longitude ?? 140.7167 }};

    editMap = L.map('editMap').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(editMap);

    // Add marker if coordinates exist
    @if($respondent->latitude && $respondent->longitude)
    editMarker = L.marker([lat, lng], {draggable: true}).addTo(editMap);
    editMarker.on('dragend', function(e) {
        const position = editMarker.getLatLng();
        document.getElementById('edit_latitude').value = position.lat.toFixed(6);
        document.getElementById('edit_longitude').value = position.lng.toFixed(6);
    });
    @endif

    // Click to add/move marker
    editMap.on('click', function(e) {
        if (editMarker) {
            editMarker.setLatLng(e.latlng);
        } else {
            editMarker = L.marker(e.latlng, {draggable: true}).addTo(editMap);
            editMarker.on('dragend', function(e) {
                const position = editMarker.getLatLng();
                document.getElementById('edit_latitude').value = position.lat.toFixed(6);
                document.getElementById('edit_longitude').value = position.lng.toFixed(6);
            });
        }
        document.getElementById('edit_latitude').value = e.latlng.lat.toFixed(6);
        document.getElementById('edit_longitude').value = e.latlng.lng.toFixed(6);
    });
}

function getMyLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('edit_latitude').value = lat.toFixed(6);
            document.getElementById('edit_longitude').value = lng.toFixed(6);

            if (editMap) {
                editMap.setView([lat, lng], 15);
                if (editMarker) {
                    editMarker.setLatLng([lat, lng]);
                } else {
                    editMarker = L.marker([lat, lng], {draggable: true}).addTo(editMap);
                    editMarker.on('dragend', function(e) {
                        const position = editMarker.getLatLng();
                        document.getElementById('edit_latitude').value = position.lat.toFixed(6);
                        document.getElementById('edit_longitude').value = position.lng.toFixed(6);
                    });
                }
            }
        }, function(error) {
            alert('Tidak dapat mengakses lokasi Anda: ' + error.message);
        });
    } else {
        alert('Browser Anda tidak mendukung geolocation');
    }
}

// Multi-step form navigation
let currentEditStep = 1;

function nextEditStep() {
    if (currentEditStep < 4) {
        currentEditStep++;
        updateEditStepIndicators();
        showEditStep(currentEditStep);
    }
}

function prevEditStep() {
    if (currentEditStep > 1) {
        currentEditStep--;
        updateEditStepIndicators();
        showEditStep(currentEditStep);
    }
}

function showEditStep(stepNumber) {
    document.querySelectorAll('.edit-form-step').forEach(step => {
        step.style.display = 'none';
    });
    const targetStep = document.querySelector(`.edit-form-step[data-step="${stepNumber}"]`);
    if (targetStep) {
        targetStep.style.display = 'block';
    }
    
    // Initialize map when showing step 3 (Address)
    if (stepNumber === 3) {
        setTimeout(initEditMap, 100);
    }
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function updateEditStepIndicators() {
    document.querySelectorAll('.edit-step-indicator').forEach(indicator => {
        const stepNum = parseInt(indicator.dataset.step);
        if (stepNum <= currentEditStep) {
            indicator.classList.add('active');
        } else {
            indicator.classList.remove('active');
        }
    });
    
    document.querySelectorAll('.edit-step-line').forEach(line => {
        const stepNum = parseInt(line.dataset.step);
        if (stepNum < currentEditStep) {
            line.classList.add('active');
        } else {
            line.classList.remove('active');
        }
    });
}

// Initialize edit map when switching to edit mode
function toggleEditMode() {
    const viewMode = document.getElementById('viewMode');
    const editMode = document.getElementById('editMode');

    if (viewMode.classList.contains('hidden')) {
        viewMode.classList.remove('hidden');
        editMode.classList.add('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        viewMode.classList.add('hidden');
        editMode.classList.remove('hidden');
        
        // Reset to step 1
        currentEditStep = 1;
        showEditStep(1);
        updateEditStepIndicators();
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}
</script>
@endsection
