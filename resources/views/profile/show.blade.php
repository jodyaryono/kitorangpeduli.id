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
                    <!-- Progress Steps -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between max-w-2xl mx-auto">
                            <div class="flex-1 flex items-center">
                                <div class="step-circle active" data-step="1">1</div>
                                <div class="step-line" data-step="1"></div>
                            </div>
                            <div class="flex-1 flex items-center">
                                <div class="step-circle" data-step="2">2</div>
                                <div class="step-line" data-step="2"></div>
                            </div>
                            <div class="flex-1 flex items-center">
                                <div class="step-circle" data-step="3">3</div>
                                <div class="step-line" data-step="3"></div>
                            </div>
                            <div class="step-circle" data-step="4">4</div>
                        </div>
                        <div class="flex items-center justify-between max-w-2xl mx-auto mt-2 text-xs text-gray-600">
                            <span class="w-24 text-center">Data Pribadi</span>
                            <span class="w-24 text-center">Kontak</span>
                            <span class="w-24 text-center">Alamat</span>
                            <span class="w-24 text-center">Verifikasi</span>
                        </div>
                    </div>

                    <style>
                        .step-circle {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 40px;
                            height: 40px;
                            background-color: #D1D5DB;
                            color: #4B5563;
                            border-radius: 50%;
                            font-weight: bold;
                        }
                        .step-circle.active {
                            background-color: #EAB308;
                            color: black;
                        }
                        .step-line {
                            flex: 1;
                            height: 4px;
                            background-color: #D1D5DB;
                        }
                        .step-line.active {
                            background-color: #EAB308;
                        }
                        .step-content {
                            display: none;
                        }
                        .step-content.active {
                            display: block;
                        }
                        .edit-step-circle {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            width: 40px;
                            height: 40px;
                            background-color: #D1D5DB;
                            color: #4B5563;
                            border-radius: 50%;
                            font-weight: bold;
                        }
                        .edit-step-circle.active {
                            background-color: #EAB308;
                            color: black;
                        }
                        .edit-step-line {
                            flex: 1;
                            height: 4px;
                            background-color: #D1D5DB;
                        }
                        .edit-step-line.active {
                            background-color: #EAB308;
                        }
                        .edit-step-content {
                            display: none;
                        }
                        .edit-step-content.active {
                            display: block;
                        }
                    </style>

                    <!-- Edit Button -->
                    <div class="text-right mb-6">
                        <button onclick="toggleEditMode()" class="bg-yellow-500 text-black px-6 py-3 rounded-lg font-medium hover:bg-yellow-400 transition">
                            ✏️ Edit Profil
                        </button>
                    </div>

                    <!-- Step 1: Data Pribadi -->
                    <div class="step-content active" id="step1">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">👤</span> Data Pribadi
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Nama Lengkap</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->nama_lengkap ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">NIK</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->nik ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Tempat, Tanggal Lahir</p>
                                <p class="text-gray-800 font-medium">
                                    {{ $respondent->tempat_lahir ?? 'N/A' }},
                                    {{ $respondent->tanggal_lahir ? \Carbon\Carbon::parse($respondent->tanggal_lahir)->format('d F Y') : 'N/A' }}
                                </p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Jenis Kelamin</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->jenis_kelamin == 'L' ? '👨 Laki-laki' : '👩 Perempuan' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Agama</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->agama ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Golongan Darah</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->golongan_darah ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Status Perkawinan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->status_perkawinan ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Status Kewarganegaraan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->citizenType->name ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Pendidikan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->education->education ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Pekerjaan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->occupation->occupation ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="button" onclick="goToStep(2)" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Kontak -->
                    <div class="step-content" id="step2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">📱</span> Informasi Kontak
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Nomor HP</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Email</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="goToStep(1)" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                ← Sebelumnya
                            </button>
                            <button type="button" onclick="goToStep(3)" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Alamat -->
                    <div class="step-content" id="step3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">📍</span> Informasi Alamat
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                                <p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->alamat ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">RT/RW</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->rt ?? '-' }}/{{ $respondent->rw ?? '-' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Provinsi</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->province->name ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Kabupaten/Kota</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->regency->name ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Kecamatan</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->district->name ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Kelurahan/Desa</p>
                                <p class="text-gray-800 font-medium">{{ $respondent->village->name ?? 'N/A' }}</p>
                            </div>
                            @if($respondent->latitude && $respondent->longitude)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">Koordinat GPS</p>
                                <p class="text-gray-800 font-medium text-sm">{{ $respondent->latitude }}, {{ $respondent->longitude }}</p>
                            </div>
                            @endif
                        </div>
                        @if($respondent->latitude && $respondent->longitude)
                        <div class="mt-4">
                            <div id="map" style="height: 250px; border-radius: 8px;"></div>
                        </div>
                        @endif
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="goToStep(2)" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                ← Sebelumnya
                            </button>
                            <button type="button" onclick="goToStep(4)" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                Selanjutnya →
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Verifikasi -->
                    <div class="step-content" id="step4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b flex items-center">
                            <span class="text-2xl mr-2">🪪</span> Foto KTP & Status Verifikasi
                        </h3>
                        @php
                            $ktpUrl = $respondent->getFirstMediaUrl('ktp_image');
                        @endphp
                        @if($ktpUrl && $ktpUrl !== '')
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <p class="text-xs text-gray-500 mb-2">Foto KTP</p>
                                <img src="{{ $ktpUrl }}" alt="Foto KTP" class="w-full max-w-md rounded-lg shadow-md">
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-lg text-center text-gray-500 mb-4">
                                <p class="mb-2">📄 Belum ada foto KTP</p>
                                <p class="text-xs">Upload foto KTP saat registrasi atau hubungi admin untuk update</p>
                            </div>
                        @endif
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-xs text-gray-500 mb-1">Status Verifikasi</p>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'verified' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => '⏳ Menunggu Verifikasi',
                                    'verified' => '✅ Terverifikasi',
                                    'rejected' => '❌ Ditolak',
                                ];
                            @endphp
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$respondent->verification_status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$respondent->verification_status] ?? $respondent->verification_status }}
                            </span>
                        </div>
                        <div class="flex justify-between mt-6">
                            <button type="button" onclick="goToStep(3)" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                ← Sebelumnya
                            </button>
                            <a href="{{ route('home') }}" class="px-8 py-3 bg-green-500 text-white rounded-lg font-bold hover:bg-green-400 transition">
                                🏠 Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div id="editMode" class="hidden">
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="profileForm">
                        @csrf

                        <!-- Edit Progress Steps -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between max-w-2xl mx-auto">
                                <div class="flex-1 flex items-center">
                                    <div class="edit-step-circle active" data-step="1">1</div>
                                    <div class="edit-step-line" data-step="1"></div>
                                </div>
                                <div class="flex-1 flex items-center">
                                    <div class="edit-step-circle" data-step="2">2</div>
                                    <div class="edit-step-line" data-step="2"></div>
                                </div>
                                <div class="flex-1 flex items-center">
                                    <div class="edit-step-circle" data-step="3">3</div>
                                    <div class="edit-step-line" data-step="3"></div>
                                </div>
                                <div class="edit-step-circle" data-step="4">4</div>
                            </div>
                            <div class="flex items-center justify-between max-w-2xl mx-auto mt-2 text-xs text-gray-600">
                                <span class="w-24 text-center">Data Pribadi</span>
                                <span class="w-24 text-center">Kontak</span>
                                <span class="w-24 text-center">Alamat</span>
                                <span class="w-24 text-center">Simpan</span>
                            </div>
                        </div>

                        <!-- Cancel Button -->
                        <div class="text-right mb-6">
                            <button type="button" onclick="toggleEditMode()" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-400 transition">
                                ❌ Batal
                            </button>
                        </div>

                        <!-- Edit Step 1 -->
                        <div class="edit-step-content active" id="editStep1">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">👤 Edit Data Pribadi</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $respondent->nama_lengkap) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                                    <input type="text" name="nik" value="{{ old('nik', $respondent->nik) }}" maxlength="16" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $respondent->tempat_lahir) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $respondent->tanggal_lahir?->format('Y-m-d')) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="L" {{ $respondent->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ $respondent->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                                    <select name="agama" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        @foreach(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'Lainnya'] as $agama)
                                            <option value="{{ $agama }}" {{ $respondent->agama == $agama ? 'selected' : '' }}>{{ $agama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Golongan Darah</label>
                                    <select name="golongan_darah" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        @foreach(['A', 'B', 'AB', 'O', 'Tidak Tahu'] as $gol)
                                            <option value="{{ $gol }}" {{ $respondent->golongan_darah == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Perkawinan</label>
                                    <select name="status_perkawinan" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        @foreach(['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $status)
                                            <option value="{{ $status }}" {{ $respondent->status_perkawinan == $status ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kewarganegaraan</label>
                                    <select name="citizen_type_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Kewarganegaraan</option>
                                        @foreach($citizenTypes as $ct)
                                            <option value="{{ $ct->id }}" {{ $respondent->citizen_type_id == $ct->id ? 'selected' : '' }}>{{ $ct->citizen_type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                                    <select name="education_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Pendidikan</option>
                                        @foreach($educations as $edu)
                                            <option value="{{ $edu->id }}" {{ $respondent->education_id == $edu->id ? 'selected' : '' }}>{{ $edu->education }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                                    <select name="occupation_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Pekerjaan</option>
                                        @foreach($occupations as $occ)
                                            <option value="{{ $occ->id }}" {{ $respondent->occupation_id == $occ->id ? 'selected' : '' }}>{{ $occ->occupation }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-end mt-6">
                                <button type="button" onclick="goToEditStep(2)" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>

                        <!-- Edit Step 2 -->
                        <div class="edit-step-content" id="editStep2">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">📱 Edit Informasi Kontak</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor HP (WhatsApp)</label>
                                    <input type="text" name="phone" value="{{ old('phone', $respondent->phone) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="6281234567890">
                                    <p class="text-xs text-gray-500 mt-1">Format: 62xxxxxxxxx</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $respondent->email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="goToEditStep(1)" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ← Sebelumnya
                                </button>
                                <button type="button" onclick="goToEditStep(3)" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>

                        <!-- Edit Step 3 -->
                        <div class="edit-step-content" id="editStep3">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">📍 Edit Alamat</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                                    <textarea name="alamat" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg">{{ old('alamat', $respondent->alamat) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                                    <input type="text" name="rt" value="{{ old('rt', $respondent->rt) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" maxlength="3">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                                    <input type="text" name="rw" value="{{ old('rw', $respondent->rw) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg" maxlength="3">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                                    <select name="province_id" id="province_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Provinsi</option>
                                        @foreach($provinces as $prov)
                                            <option value="{{ $prov->id }}" {{ $respondent->province_id == $prov->id ? 'selected' : '' }}>{{ $prov->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten/Kota</label>
                                    <select name="regency_id" id="regency_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Kabupaten/Kota</option>
                                        @if($respondent->regency)
                                            <option value="{{ $respondent->regency_id }}" selected>{{ $respondent->regency->name }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
                                    <select name="district_id" id="district_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Kecamatan</option>
                                        @if($respondent->district)
                                            <option value="{{ $respondent->district_id }}" selected>{{ $respondent->district->name }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kelurahan/Desa</label>
                                    <select name="village_id" id="village_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="">Pilih Kelurahan/Desa</option>
                                        @if($respondent->village)
                                            <option value="{{ $respondent->village_id }}" selected>{{ $respondent->village->name }}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="goToEditStep(2)" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ← Sebelumnya
                                </button>
                                <button type="button" onclick="goToEditStep(4)" class="px-8 py-3 bg-yellow-500 text-black rounded-lg font-bold hover:bg-yellow-400 transition">
                                    Selanjutnya →
                                </button>
                            </div>
                        </div>

                        <!-- Edit Step 4 -->
                        <div class="edit-step-content" id="editStep4">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">✅ Konfirmasi & Simpan</h3>
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-6">
                                <p class="text-yellow-800">Pastikan semua data sudah benar sebelum menyimpan.</p>
                            </div>
                            <div class="flex justify-between mt-6">
                                <button type="button" onclick="goToEditStep(3)" class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                                    ← Sebelumnya
                                </button>
                                <button type="submit" class="px-8 py-3 bg-green-500 text-white rounded-lg font-bold hover:bg-green-400 transition">
                                    💾 Simpan Perubahan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let currentStep = 1;

function goToStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.remove('active'));
    document.getElementById('step' + step).classList.add('active');
    document.querySelectorAll('.step-circle').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.toggle('active', s <= step);
    });
    document.querySelectorAll('.step-line').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.toggle('active', s < step);
    });
    currentStep = step;
    if (step === 3) initMap();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

let currentEditStep = 1;

function goToEditStep(step) {
    document.querySelectorAll('.edit-step-content').forEach(el => el.classList.remove('active'));
    document.getElementById('editStep' + step).classList.add('active');
    document.querySelectorAll('.edit-step-circle').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.toggle('active', s <= step);
    });
    document.querySelectorAll('.edit-step-line').forEach(el => {
        const s = parseInt(el.dataset.step);
        el.classList.toggle('active', s < step);
    });
    currentEditStep = step;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function toggleEditMode() {
    document.getElementById('viewMode').classList.toggle('hidden');
    document.getElementById('editMode').classList.toggle('hidden');
    if (!document.getElementById('editMode').classList.contains('hidden')) {
        goToEditStep(1);
    } else {
        goToStep(1);
    }
}

let map = null;
function initMap() {
    @if($respondent->latitude && $respondent->longitude)
    if (map) return;
    map = L.map('map').setView([{{ $respondent->latitude }}, {{ $respondent->longitude }}], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([{{ $respondent->latitude }}, {{ $respondent->longitude }}]).addTo(map).bindPopup('Lokasi Anda').openPopup();
    @endif
}

document.getElementById('province_id')?.addEventListener('change', async function() {
    const regencySelect = document.getElementById('regency_id');
    regencySelect.innerHTML = '<option value="">Memuat...</option>';
    if (this.value) {
        const response = await fetch('/api/wilayah/regencies/' + this.value);
        const data = await response.json();
        regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        data.forEach(item => regencySelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
    }
    document.getElementById('district_id').innerHTML = '<option value="">Pilih Kecamatan</option>';
    document.getElementById('village_id').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
});

document.getElementById('regency_id')?.addEventListener('change', async function() {
    const districtSelect = document.getElementById('district_id');
    districtSelect.innerHTML = '<option value="">Memuat...</option>';
    if (this.value) {
        const response = await fetch('/api/wilayah/districts/' + this.value);
        const data = await response.json();
        districtSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        data.forEach(item => districtSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
    }
    document.getElementById('village_id').innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
});

document.getElementById('district_id')?.addEventListener('change', async function() {
    const villageSelect = document.getElementById('village_id');
    villageSelect.innerHTML = '<option value="">Memuat...</option>';
    if (this.value) {
        const response = await fetch('/api/wilayah/villages/' + this.value);
        const data = await response.json();
        villageSelect.innerHTML = '<option value="">Pilih Kelurahan/Desa</option>';
        data.forEach(item => villageSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`);
    }
});
</script>
@endsection
