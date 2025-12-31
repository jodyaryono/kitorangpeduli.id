@extends('layouts.app')

@section('title', $questionnaire->title . ' - KitorangPeduli.id')

@push('styles')
<!-- Flatpickr for date picker with dd/mm/yyyy format -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }

    @keyframes scaleIn {
        from {
            transform: scale(0.9);
            opacity: 0;
        }
        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }

    .animate-scaleIn {
        animation: scaleIn 0.3s ease-out;
    }
</style>
@endpush

@section('content')
<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-lg p-6 shadow-xl max-w-sm mx-4">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-12 w-12 text-yellow-500 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-700 font-medium text-lg" id="loadingText">Memuat data...</p>
            <p class="text-gray-500 text-sm mt-1">Mohon tunggu sebentar</p>
        </div>
    </div>
</div>

<!-- Sticky Header -->
<div class="sticky top-0 z-50 bg-white shadow-md border-b-2 border-yellow-500">
    <div class="max-w-3xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <a href="{{ auth()->user() ? route('officer.entry') : route('home') }}" class="flex items-center gap-2 text-gray-700 hover:text-yellow-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="font-medium">Kembali</span>
            </a>
            <div class="text-right">
                <div class="text-sm font-bold text-yellow-600" id="stickyQuestionCounter">1-10</div>
                <div class="text-xs text-gray-500">dari {{ $actualQuestions->count() }}</div>
            </div>
        </div>
    </div>
</div>

<div class="py-8 px-4">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 border-t-4 border-yellow-500">
            <div class="papua-gradient px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="bg-yellow-500 text-black text-xs font-bold px-3 py-1 rounded-full">
                            {{ $questionnaire->opd->nama ?? 'Survey Umum' }}
                        </span>
                        <h1 class="text-2xl font-bold text-yellow-400 mt-2">{{ $questionnaire->title }}</h1>
                    </div>
                    <div class="text-right text-white">
                        <div class="text-3xl font-bold text-yellow-400" id="questionCounter">1</div>
                        <div class="text-sm text-gray-400">dari {{ $actualQuestions->count() }}</div>
                    </div>
                </div>
            </div>
            @if($questionnaire->description)
                <div class="px-8 py-4 bg-gray-100 border-b">
                    <p class="text-gray-700 text-sm">{{ $questionnaire->description }}</p>
                </div>
            @endif
        </div>

        <!-- Form -->
        <form action="{{ route('questionnaire.submit', $questionnaire->id) }}" method="POST" enctype="multipart/form-data" id="surveyForm">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6">
                    <p class="font-medium">Mohon lengkapi pertanyaan berikut:</p>
                    <ul class="list-disc list-inside text-sm mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Questions -->
            @php
                $questionNumber = 0; // Counter for actual questions only
            @endphp

            <div class="space-y-6">
                @foreach($questionnaire->questions as $section)
                    @if($section->is_section)
                        {{-- Skip Section V - will be rendered dynamically below --}}
                        @if(stripos($section->question_text, 'V. GANGGUAN KESEHATAN') !== false)
                            @continue
                        @endif

                        {{-- Section Header with Collapse/Expand --}}
                        <div x-data="{ open: true }" class="border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                            <div @click="open = !open"
                                 class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-t-xl p-6 text-white cursor-pointer hover:from-yellow-600 hover:to-yellow-700 transition-all">
                                <h2 class="text-xl font-bold flex items-center justify-between gap-3">
                                    <span class="flex items-center gap-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        {{ $section->question_text }}
                                    </span>
                                    <div class="flex items-center gap-2">
                                        @if($section->description)
                                            <span class="text-yellow-100 text-sm mr-4">{{ $section->description }}</span>
                                        @endif
                                        <svg x-show="!open" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="open" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </div>
                                </h2>
                            </div>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-300"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-200"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 class="bg-gray-50 p-4 space-y-4">

                                {{-- Child Questions in this Section --}}
                                @foreach($section->childQuestions as $question)
                                    @php
                                        $questionNumber++;

                                        // Skip questions 12-19 for cleaner display
                                        if ($questionNumber >= 12 && $questionNumber <= 19) {
                                            continue;
                                        }

                                        $existingAnswer = $existingAnswers->get($question->id);
                                        $savedValue = $existingAnswer ? ($existingAnswer->selected_options ?? $existingAnswer->answer_text ?? $existingAnswer->answer_numeric) : null;

                                        // Debug log for troubleshooting
                                        if ($savedValue && config('app.debug')) {
                                            \Log::info("Loading Q{$question->id}: savedValue = {$savedValue}");
                                        }

                                        // Auto-fill from savedFamily if available
                                        if (isset($savedFamily) && !$savedValue) {
                                            // Match question type for wilayah dropdowns
                                            if ($question->question_type === 'province') {
                                                $savedValue = $savedFamily['province_id'] ?? null;
                                            } elseif ($question->question_type === 'regency') {
                                                $savedValue = $savedFamily['regency_id'] ?? null;
                                            } elseif ($question->question_type === 'district') {
                                                $savedValue = $savedFamily['district_id'] ?? null;
                                            } elseif ($question->question_type === 'village') {
                                                $savedValue = $savedFamily['village_id'] ?? null;
                                            } elseif ($question->question_type === 'puskesmas') {
                                                $savedValue = $savedFamily['puskesmas_id'] ?? null;
                                            }

                                            // Match question text for text fields (RT, RW, etc.)
                                            $questionText = strtolower($question->question_text);
                                            if (str_contains($questionText, 'rt') && !str_contains($questionText, 'kartu')) {
                                                $savedValue = $savedFamily['rt'] ?? null;
                                            } elseif (str_contains($questionText, 'rw')) {
                                                $savedValue = $savedFamily['rw'] ?? null;
                                            } elseif (str_contains($questionText, 'no. bangunan') || str_contains($questionText, 'bangunan')) {
                                                $savedValue = $savedFamily['no_bangunan'] ?? null;
                                            } elseif (str_contains($questionText, 'no. keluarga') || str_contains($questionText, 'no keluarga')) {
                                                $savedValue = $savedFamily['no_kk'] ?? null;
                                            } elseif (str_contains($questionText, 'alamat')) {
                                                $savedValue = $savedFamily['alamat'] ?? null;
                                            }
                                        }
                                    @endphp

                                    <div class="bg-white rounded-xl shadow-md p-6">

                                        <div class="flex items-start gap-3 mb-4">
                                            <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                                                <span class="text-black font-bold text-sm">{{ $questionNumber }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-base font-semibold text-gray-800">
                                                    {{ $question->question_text }}
                                                    @if($question->is_required)
                                                        <span class="text-red-500">*</span>
                                                    @endif
                                                </h3>
                                                @if($question->description)
                                                    <p class="text-gray-500 text-sm mt-1">{{ $question->description }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        @if($question->hasQuestionMedia())
                                            <div class="mb-4">
                                                @foreach($question->getMedia('question_media') as $media)
                                                    @if(str_starts_with($media->mime_type, 'image'))
                                                        <img src="{{ $media->getUrl() }}" alt="Media" class="rounded-lg max-h-48 mx-auto">
                                                    @elseif(str_starts_with($media->mime_type, 'video'))
                                                        <video controls class="rounded-lg max-h-48 mx-auto">
                                                            <source src="{{ $media->getUrl() }}" type="{{ $media->mime_type }}">
                                                        </video>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="answer-input">
                                            @switch($question->question_type)
                                                @case('text')
                                                    <input type="text"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                           class="answer-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 uppercase {{ isset($question->settings['readonly']) && $question->settings['readonly'] ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                                           data-question-id="{{ $question->id }}"
                                                           placeholder="{{ isset($question->settings['readonly']) && $question->settings['readonly'] ? 'Otomatis terisi dari data anggota keluarga' : 'Ketik jawaban Anda...' }}"
                                                           {{ $question->is_required ? 'required' : '' }}
                                                           {{ isset($question->settings['readonly']) && $question->settings['readonly'] ? 'readonly' : '' }}>
                                                    @break

                                                @case('textarea')
                                                    <textarea name="answers[{{ $question->id }}]"
                                                              rows="3"
                                                              class="answer-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 uppercase {{ isset($question->settings['readonly']) && $question->settings['readonly'] ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                                              data-question-id="{{ $question->id }}"
                                                              placeholder="{{ isset($question->settings['readonly']) && $question->settings['readonly'] ? 'Otomatis terisi' : 'Ketik jawaban Anda...' }}"
                                                              {{ $question->is_required ? 'required' : '' }}
                                                              {{ isset($question->settings['readonly']) && $question->settings['readonly'] ? 'readonly' : '' }}>{{ old('answers.' . $question->id, $savedValue) }}</textarea>
                                                    @break

                                                @case('number')
                                                    <input type="number"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                           placeholder="Masukkan angka..."
                                                           {{ $question->is_required ? 'required' : '' }}>
                                                    @break

                                                @case('date')
                                                    @php
                                                        // Default to today if settings specify
                                                        $defaultDate = $savedValue;
                                                        if (!$defaultDate && isset($question->settings['default_value']) && $question->settings['default_value'] === 'today') {
                                                            $defaultDate = date('Y-m-d');
                                                        }
                                                    @endphp
                                                    <input type="date"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ old('answers.' . $question->id, $defaultDate) }}"
                                                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                           {{ $question->is_required ? 'required' : '' }}>
                                                    @break

                                                @case('province')
                                                    <select name="answers[{{ $question->id }}]"
                                                            id="province_{{ $question->id }}"
                                                            class="answer-input wilayah-cascade w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                            data-question-id="{{ $question->id }}"
                                                            data-type="province"
                                                            data-saved-value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                            {{ $question->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih Provinsi...</option>
                                                    </select>
                                                    @break

                                                @case('regency')
                                                    <select name="answers[{{ $question->id }}]"
                                                            id="regency_{{ $question->id }}"
                                                            class="answer-input wilayah-cascade w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                            data-question-id="{{ $question->id }}"
                                                            data-type="regency"
                                                            data-saved-value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                            {{ $question->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih Kabupaten/Kota...</option>
                                                    </select>
                                                    @break

                                                @case('district')
                                                    <select name="answers[{{ $question->id }}]"
                                                            id="district_{{ $question->id }}"
                                                            class="answer-input wilayah-cascade w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                            data-question-id="{{ $question->id }}"
                                                            data-type="district"
                                                            data-saved-value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                            {{ $question->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih Kecamatan...</option>
                                                    </select>
                                                    @break

                                                @case('village')
                                                    <select name="answers[{{ $question->id }}]"
                                                            id="village_{{ $question->id }}"
                                                            class="answer-input wilayah-cascade w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                            data-question-id="{{ $question->id }}"
                                                            data-type="village"
                                                            data-saved-value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                            {{ $question->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih Desa/Kelurahan...</option>
                                                    </select>
                                                    @break

                                                @case('puskesmas')
                                                    <div class="space-y-2">
                                                        <input type="text"
                                                               name="answers[{{ $question->id }}]"
                                                               id="puskesmas_input_{{ $question->id }}"
                                                               value="{{ old('answers.' . $question->id, $savedValue) }}"
                                                               class="answer-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 uppercase"
                                                               data-question-id="{{ $question->id }}"
                                                               placeholder="Ketik nama puskesmas atau pilih dari list"
                                                               list="puskesmas_list_{{ $question->id }}"
                                                               {{ $question->is_required ? 'required' : '' }}>
                                                        <datalist id="puskesmas_list_{{ $question->id }}">
                                                            {{-- Will be populated via API --}}
                                                        </datalist>
                                                        <p class="text-xs text-gray-500">💡 Ketik nama puskesmas. Jika tidak ada di list, ketik manual untuk menambah yang baru.</p>
                                                    </div>
                                                    @break

                                                @case('field_officer')
                                                    @php
                                                        // Auto-fill with logged-in user's name (officer or respondent data)
                                                        $displayValue = $savedValue;
                                                        if (!$displayValue) {
                                                            if (isset($isOfficerAssisted) && $isOfficerAssisted && auth()->check()) {
                                                                // Officer-assisted mode - use officer's name
                                                                $displayValue = auth()->user()->name;
                                                            } elseif (isset($respondentData) && isset($respondentData['nama_lengkap'])) {
                                                                // Regular mode - use respondent's name
                                                                $displayValue = $respondentData['nama_lengkap'];
                                                            } elseif (auth()->check()) {
                                                                // Fallback - use authenticated user's name
                                                                $displayValue = auth()->user()->name;
                                                            }
                                                        }
                                                        $displayValue = old('answers.' . $question->id, $displayValue);
                                                    @endphp
                                                    <input type="text"
                                                           name="answers[{{ $question->id }}]"
                                                           value="{{ $displayValue }}"
                                                           readonly
                                                           class="answer-input w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-100 text-gray-700 cursor-not-allowed"
                                                           data-question-id="{{ $question->id }}"
                                                           placeholder="Otomatis terisi dengan nama pencacah yang login">
                                                    @break

                                                @case('lookup')
                                                    <select name="answers[{{ $question->id }}]"
                                                            class="answer-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                            data-question-id="{{ $question->id }}"
                                                            {{ $question->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih...</option>
                                                        {{-- Will be populated via API --}}
                                                    </select>
                                                    @break

                                                @case('family_members')
                                                    <div class="space-y-3">
                                                        <!-- Tabel List Anggota Keluarga -->
                                                        <div id="family-members-table-container" class="overflow-x-auto">
                                                            <table class="w-full text-sm border-collapse" id="family-members-table">
                                                                <thead class="bg-gray-100">
                                                                    <tr>
                                                                        <th class="border px-2 py-2 text-left">No</th>
                                                                        <th class="border px-2 py-2 text-left">NIK</th>
                                                                        <th class="border px-2 py-2 text-left">Nama</th>
                                                                        <th class="border px-2 py-2 text-left">Status Keluarga</th>
                                                                        <th class="border px-2 py-2 text-left">Jenis Kelamin</th>
                                                                        <th class="border px-2 py-2 text-left">Umur</th>
                                                                        <th class="border px-2 py-2 text-center">Aksi</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody id="family-members-tbody">
                                                                    <tr id="no-members-row">
                                                                        <td colspan="7" class="border px-2 py-4 text-center text-gray-500">Belum ada anggota keluarga</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <!-- Hidden container for form data -->
                                                        <div id="family-members-list" class="hidden"></div>

                                                        <!-- Tombol Tambah -->
                                                        <button type="button" onclick="openFamilyMemberModal()" class="w-full px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-yellow-600 transition font-medium text-sm">
                                                            + Tambah Anggota Keluarga
                                                        </button>
                                                    </div>
                                                    @break

                                                @case('health_per_member')
                                                    <div id="health-per-member-{{ $question->id }}" class="space-y-4">
                                                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800">
                                                            Pertanyaan ini berlaku untuk SEMUA UMUR. Isi untuk setiap anggota keluarga yang sudah didaftarkan.
                                                        </div>
                                                        <div id="health-questions-container" class="space-y-4"></div>
                                                        <div id="no-family-members-notice" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-800">
                                                            Belum ada anggota keluarga. Silakan tambahkan anggota keluarga di bagian IV terlebih dahulu.
                                                        </div>
                                                    </div>
                                                    @break

                                                @case('location')
                                                    <div class="space-y-4">
                                                        <!-- GPS Controls -->
                                                        <div class="flex gap-2">
                                                            <button type="button" onclick="detectGPS({{ $question->id }})" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm flex items-center justify-center gap-2">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                </svg>
                                                                Deteksi GPS Otomatis
                                                            </button>
                                                            <button type="button" onclick="resetMapToWilayah({{ $question->id }})" class="px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                                </svg>
                                                            </button>
                                                        </div>

                                                        <!-- Coordinate Inputs -->
                                                        <div class="grid grid-cols-2 gap-3">
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">Latitude</label>
                                                                <input type="text"
                                                                       name="answers[{{ $question->id }}][latitude]"
                                                                       id="lat_{{ $question->id }}"
                                                                       placeholder="-2.5489"
                                                                       class="answer-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                                                       data-question-id="{{ $question->id }}"
                                                                       readonly
                                                                       {{ $question->is_required ? 'required' : '' }}>
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-medium text-gray-600 mb-1">Longitude</label>
                                                                <input type="text"
                                                                       name="answers[{{ $question->id }}][longitude]"
                                                                       id="lng_{{ $question->id }}"
                                                                       placeholder="140.7003"
                                                                       class="answer-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                                                       data-question-id="{{ $question->id }}"
                                                                       readonly
                                                                       {{ $question->is_required ? 'required' : '' }}>
                                                            </div>
                                                        </div>

                                                        <!-- Map Container -->
                                                        <div id="map_{{ $question->id }}" class="h-80 rounded-lg border-2 border-gray-300 shadow-inner" style="z-index: 1;"></div>

                                                        <p class="text-xs text-gray-500">
                                                            💡 <strong>Tips:</strong> Klik tombol hijau untuk deteksi GPS otomatis, atau klik pada peta untuk memilih lokasi secara manual. Peta akan menampilkan lokasi awal berdasarkan wilayah yang dipilih.
                                                        </p>
                                                    </div>
                                                    @break

                                                @case('image')
                                                    @php
                                                        $hasExistingImage = $existingAnswer && $existingAnswer->media_path;
                                                    @endphp
                                                    <div class="image-upload-container" data-question-id="{{ $question->id }}">
                                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-400 transition cursor-pointer image-upload-area" id="imageUploadArea_{{ $question->id }}">
                                                            <input type="file"
                                                                   name="file_answers[{{ $question->id }}]"
                                                                   accept="image/*"
                                                                   class="hidden image-file-input"
                                                                   id="image_{{ $question->id }}"
                                                                   data-question-id="{{ $question->id }}"
                                                                   {{ $question->is_required ? 'required' : '' }}>
                                                            <div class="image-placeholder {{ $hasExistingImage ? 'hidden' : '' }}" id="imagePlaceholder_{{ $question->id }}">
                                                                <div class="text-4xl mb-2">🖼️</div>
                                                                <p class="text-gray-600 font-medium">Klik untuk upload gambar</p>
                                                                <p class="text-gray-400 text-sm mt-1">atau drag & drop</p>
                                                                <p class="text-gray-400 text-xs mt-2">Format: JPG, PNG. Maksimal 2MB</p>
                                                            </div>
                                                            <div class="image-preview {{ $hasExistingImage ? '' : 'hidden' }}" id="imagePreview_{{ $question->id }}">
                                                                <img src="{{ $hasExistingImage ? asset('storage/' . $existingAnswer->media_path) : '' }}" alt="Preview" class="max-h-64 mx-auto rounded-lg mb-3">
                                                                <p class="text-sm text-gray-600 mb-2" id="imageFileName_{{ $question->id }}">
                                                                    {{ $hasExistingImage ? '📷 ' . ($existingAnswer->answer_text ?? 'Uploaded Image') : '' }}
                                                                </p>
                                                                <button type="button" class="change-image-btn text-yellow-600 text-sm font-medium hover:text-yellow-700" data-question-id="{{ $question->id }}">
                                                                    🔄 Ganti Gambar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @break

                                                @case('video')
                                                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-400 transition">
                                                        <input type="file"
                                                               name="file_answers[{{ $question->id }}]"
                                                               accept="video/*"
                                                               class="hidden"
                                                               id="video_{{ $question->id }}"
                                                               {{ $question->is_required ? 'required' : '' }}>
                                                        <label for="video_{{ $question->id }}" class="cursor-pointer">
                                                            <div class="text-4xl mb-2">🎥</div>
                                                            <p class="text-gray-600">Klik untuk upload video</p>
                                                        </label>
                                                    </div>
                                                    @break

                                                @case('single_choice')
                                                    <div class="space-y-3">
                                                        @if($question->options->count() > 0)
                                                            @foreach($question->options as $option)
                                                                @php
                                                                    $optionValue = $option->option_value ?? $option->value ?? $option->option_text;
                                                                @endphp
                                                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-yellow-50 hover:border-yellow-400 transition has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-500">
                                                                    <input type="radio"
                                                                           name="answers[{{ $question->id }}]"
                                                                           value="{{ $optionValue }}"
                                                                           class="answer-input w-5 h-5 text-yellow-600 focus:ring-yellow-500"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ old('answers.' . $question->id, $savedValue) == $optionValue ? 'checked' : '' }}
                                                                           {{ $question->is_required ? 'required' : '' }}>
                                                                    <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                                                </label>
                                                            @endforeach
                                                        @else
                                                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                                                ...��️ Tidak ada opsi jawaban untuk pertanyaan ini. Silakan hubungi administrator.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @break

                                                @case('multiple_choice')
                                                    <div class="space-y-3">
                                                        @if($question->options->count() > 0)
                                                            @foreach($question->options as $option)
                                                                @php
                                                                    $savedArray = $savedValue ? json_decode($savedValue, true) : [];
                                                                    $oldArray = old('answers.' . $question->id, $savedArray);
                                                                    $optionValue = $option->option_value ?? $option->value ?? $option->option_text;
                                                                    $isChecked = is_array($oldArray) && in_array($optionValue, $oldArray);
                                                                @endphp
                                                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-yellow-50 hover:border-yellow-400 transition has-[:checked]:bg-yellow-50 has-[:checked]:border-yellow-500">
                                                                    <input type="checkbox"
                                                                           name="answers[{{ $question->id }}][]"
                                                                           value="{{ $optionValue }}"
                                                                           class="answer-input w-5 h-5 text-yellow-600 rounded focus:ring-yellow-500"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ $isChecked ? 'checked' : '' }}>
                                                                    <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                                                </label>
                                                            @endforeach
                                                        @else
                                                            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                                                                ...��️ Tidak ada opsi jawaban untuk pertanyaan ini. Silakan hubungi administrator.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    @break

                                                @case('dropdown')
                                                    <select name="answers[{{ $question->id }}]"
                                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                            {{ $question->is_required ? 'required' : '' }}>
                                                        <option value="">Pilih jawaban...</option>
                                                        @foreach($question->options as $option)
                                                            <option value="{{ $option->option_value }}"
                                                                    {{ old('answers.' . $question->id, $savedValue) == $option->option_value ? 'selected' : '' }}>
                                                                {{ $option->option_text }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @break

                                                @case('scale')
                                                @case('rating')
                                                    @php
                                                        $settings = is_string($question->settings) ? json_decode($question->settings, true) : $question->settings;
                                                        $minValue = $settings['min'] ?? 1;
                                                        $maxValue = $settings['max'] ?? 5;
                                                        $minLabel = $settings['min_label'] ?? 'Sangat Buruk';
                                                        $maxLabel = $settings['max_label'] ?? 'Sangat Baik';
                                                    @endphp
                                                    <div class="space-y-4">
                                                        <div class="flex items-center justify-between gap-4">
                                                            @for($i = $minValue; $i <= $maxValue; $i++)
                                                                <label class="flex-1 cursor-pointer">
                                                                    <input type="radio"
                                                                           name="answers[{{ $question->id }}]"
                                                                           value="{{ $i }}"
                                                                           class="answer-input sr-only peer"
                                                                           data-question-id="{{ $question->id }}"
                                                                           {{ old('answers.' . $question->id, $savedValue) == $i ? 'checked' : '' }}
                                                                           {{ $question->is_required ? 'required' : '' }}>
                                                                    <div class="text-center p-4 border-2 border-gray-200 rounded-lg peer-checked:border-yellow-400 peer-checked:bg-yellow-50 hover:border-yellow-300 transition">
                                                                        <div class="text-2xl font-bold text-gray-700 peer-checked:text-yellow-600">{{ $i }}</div>
                                                                    </div>
                                                                </label>
                                                            @endfor
                                                        </div>
                                                        <div class="flex items-center justify-between text-sm text-gray-500">
                                                            <span>{{ $minLabel }}</span>
                                                            <span>{{ $maxLabel }}</span>
                                                        </div>
                                                    </div>
                                                    @break

                                                @case('file')
                                                    @php
                                                        $hasExistingFile = $existingAnswer && $existingAnswer->media_path;

                                                        // Check if this is KK upload and we have savedFamily data
                                                        $questionText = strtolower($question->question_text);
                                                        $isFamilyKKUpload = str_contains($questionText, 'kartu keluarga') || str_contains($questionText, 'upload kk');

                                                        if (!$hasExistingFile && $isFamilyKKUpload && isset($savedFamily['kk_image_path']) && $savedFamily['kk_image_path']) {
                                                            $hasExistingFile = true;
                                                            $existingKKPath = $savedFamily['kk_image_path'];
                                                        } else {
                                                            $existingKKPath = null;
                                                        }
                                                    @endphp
                                                    <div class="file-upload-container" data-question-id="{{ $question->id }}">
                                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-400 transition cursor-pointer file-upload-area" id="fileUploadArea_{{ $question->id }}">
                                                            <input type="file"
                                                                   name="file_answers[{{ $question->id }}]"
                                                                   id="file_{{ $question->id }}"
                                                                   class="hidden file-file-input"
                                                                   data-question-id="{{ $question->id }}"
                                                                   accept="{{ $question->settings['accept'] ?? 'image/*,.pdf' }}"
                                                                   {{ $question->is_required && !$hasExistingFile ? 'required' : '' }}>
                                                            <div class="file-placeholder {{ $hasExistingFile ? 'hidden' : '' }}" id="filePlaceholder_{{ $question->id }}">
                                                                <div class="text-4xl mb-2">📎</div>
                                                                <p class="text-gray-600 font-medium">Klik untuk upload file</p>
                                                                <p class="text-gray-400 text-sm mt-1">atau drag & drop</p>
                                                                <p class="text-gray-400 text-xs mt-2">Format: Gambar atau PDF. Maksimal 5MB</p>
                                                            </div>
                                                            <div class="file-preview {{ $hasExistingFile ? '' : 'hidden' }}" id="filePreview_{{ $question->id }}">
                                                                <div class="preview-content mb-3" id="filePreviewContent_{{ $question->id }}">
                                                                    @if($existingKKPath)
                                                                        @php
                                                                            $fileExt = pathinfo($existingKKPath, PATHINFO_EXTENSION);
                                                                        @endphp
                                                                        @if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif']))
                                                                            <img src="{{ asset('storage/' . $existingKKPath) }}" alt="Kartu Keluarga" class="max-h-64 mx-auto rounded-lg">
                                                                        @else
                                                                            <div class="text-6xl">📄</div>
                                                                            <p class="text-sm text-gray-600 mt-2">Kartu Keluarga</p>
                                                                        @endif
                                                                    @elseif($hasExistingFile && $existingAnswer)
                                                                        @php
                                                                            $fileExt = pathinfo($existingAnswer->media_path, PATHINFO_EXTENSION);
                                                                        @endphp
                                                                        @if(in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif']))
                                                                            <img src="{{ asset('storage/' . $existingAnswer->media_path) }}" alt="Preview" class="max-h-64 mx-auto rounded-lg">
                                                                        @elseif(strtolower($fileExt) === 'pdf')
                                                                            <div class="text-6xl">📄</div>
                                                                            <p class="text-sm text-gray-600 mt-2">File PDF</p>
                                                                        @else
                                                                            <div class="text-6xl">📎</div>
                                                                            <p class="text-sm text-gray-600 mt-2">File {{ strtoupper($fileExt) }}</p>
                                                                        @endif
                                                                    @endif
                                                                </div>
                                                                <p class="text-sm text-gray-600 mb-2" id="fileFileName_{{ $question->id }}">
                                                                    @if($existingKKPath)
                                                                        📎 Kartu Keluarga (sudah diupload)
                                                                    @else
                                                                        {{ $hasExistingFile && $existingAnswer ? '📎 ' . ($existingAnswer->answer_text ?? 'Uploaded File') : '' }}
                                                                    @endif
                                                                </p>
                                                                <button type="button" class="change-file-btn text-yellow-600 text-sm font-medium hover:text-yellow-700" data-question-id="{{ $question->id }}">
                                                                    🔄 Ganti File
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @break

                                            @endswitch
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Section V: Gangguan Kesehatan Per Anggota Keluarga -->
            <div id="dynamic-health-section" class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-green-500 mt-6">
                <div class="bg-gradient-to-r from-green-600 to-green-500 px-6 py-4 text-white cursor-pointer" onclick="toggleSectionV()">
                    <h3 class="text-lg font-bold flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="text-2xl">💚</span>
                            V. GANGGUAN KESEHATAN PER ANGGOTA KELUARGA
                        </span>
                        <svg id="section-v-arrow" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </h3>
                    <p class="text-green-100 text-sm mt-1">Klik tombol "V" pada tabel anggota keluarga untuk mengisi</p>
                </div>
                <div id="section-v-content" class="p-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800 mb-4">
                        ℹ️ Pertanyaan ini berlaku untuk SEMUA UMUR. Isi untuk setiap anggota keluarga yang sudah didaftarkan.
                    </div>
                    <div id="dynamic-health-container" class="space-y-4"></div>
                </div>
            </div>

            <!-- Section VI: Kuesioner Tambahan (Pertanyaan Per KELUARGA) -->
            <div id="section-vi-container" class="mt-6">
                <div class="bg-white rounded-lg shadow-md overflow-hidden border-l-4 border-purple-500">
                    <div class="bg-gradient-to-r from-purple-600 to-purple-500 px-6 py-4 text-white cursor-pointer" onclick="toggleSectionVI()">
                        <h3 class="text-lg font-bold flex items-center justify-between">
                            <span class="flex items-center gap-2">
                                <span class="text-2xl">📋</span>
                                VI. KUESIONER TAMBAHAN
                            </span>
                            <svg id="section-vi-arrow" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </h3>
                        <p class="text-purple-100 text-sm mt-1">Pertanyaan ini untuk seluruh keluarga</p>
                    </div>
                    <div id="section-vi-content" class="p-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm text-blue-800 mb-4">
                            ℹ️ <strong>Pertanyaan ini untuk seluruh keluarga.</strong> Beberapa pertanyaan akan muncul/hilang otomatis berdasarkan jawaban sebelumnya. Semua jawaban tersimpan secara otomatis.
                        </div>
                        <div id="section-vi-loading" class="flex items-center justify-center py-8">
                            <svg class="animate-spin h-8 w-8 text-purple-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-gray-600">Memuat pertanyaan tambahan...</span>
                        </div>
                        <div id="section-vi-questions" class="space-y-6 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Catatan Pencacah -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📝 Catatan Pencacah (Opsional)</h3>
                <textarea name="officer_notes"
                          id="officer_notes"
                          rows="4"
                          class="answer-input w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          data-question-id="officer_notes"
                          placeholder="Tuliskan catatan tambahan terkait kuesioner ini (misal: kondisi khusus, kendala saat pengisian, dll)">{{ $response->notes ?? $response->officer_notes ?? '' }}</textarea>
                <p class="text-xs text-gray-500 mt-2">💡 Catatan ini akan tersimpan otomatis untuk referensi internal</p>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-center mt-8">
                <button type="submit"
                        id="submitBtn"
                        class="px-8 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white rounded-lg hover:from-green-700 hover:to-green-600 transition flex items-center shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Selesai - Kirim Jawaban
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah/Edit Anggota Keluarga -->
<div id="familyMemberModal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeFamilyMemberModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto pointer-events-auto">
            <div class="sticky top-0 bg-white border-b px-4 py-3 flex items-center justify-between">
                <h3 id="familyModalTitle" class="text-lg font-bold text-gray-800">Tambah Anggota Keluarga</h3>
                <button type="button" onclick="closeFamilyMemberModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4 space-y-3">
                <input type="hidden" id="editMemberId" value="">

                <input type="text" id="modal_nik" placeholder="NIK (16 digit - opsional)" class="uppercase w-full px-3 py-2 text-sm border rounded-lg" maxlength="16">

                <select id="modal_citizen_type_id" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Jenis Warga</option>
                </select>

                <input type="text" id="modal_nama_lengkap" placeholder="Nama Lengkap *" class="uppercase w-full px-3 py-2 text-sm border rounded-lg" required>

                <select id="modal_hubungan" class="w-full px-3 py-2 text-sm border rounded-lg" required>
                    <option value="">Hubungan Keluarga *</option>
                </select>

                <input type="text" id="modal_tempat_lahir" placeholder="Tempat Lahir" class="uppercase w-full px-3 py-2 text-sm border rounded-lg">

                <div class="flex gap-2">
                    <input type="text" id="modal_tanggal_lahir" placeholder="dd/mm/yyyy *" class="flex-1 px-3 py-2 text-sm border rounded-lg" required>
                    <input type="number" id="modal_umur" placeholder="Umur" class="w-20 px-3 py-2 text-sm border rounded-lg bg-gray-50" readonly>
                </div>

                <select id="modal_jenis_kelamin" class="w-full px-3 py-2 text-sm border rounded-lg" required>
                    <option value="">Jenis Kelamin *</option>
                    <option value="1">1. Laki-laki</option>
                    <option value="2">2. Perempuan</option>
                </select>

                <select id="modal_status_perkawinan" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Status Perkawinan</option>
                </select>

                <select id="modal_agama" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Agama</option>
                </select>

                <select id="modal_pendidikan" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Pendidikan</option>
                </select>

                <select id="modal_pekerjaan" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Pekerjaan</option>
                </select>

                <select id="modal_golongan_darah" class="w-full px-3 py-2 text-sm border rounded-lg">
                    <option value="">Golongan Darah</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="AB">AB</option>
                    <option value="O">O</option>
                    <option value="-">Tidak Diketahui</option>
                </select>

                <input type="text" id="modal_phone" placeholder="No. Telepon" class="w-full px-3 py-2 text-sm border rounded-lg">

                <div class="space-y-1">
                    <label class="block text-xs font-medium text-gray-600">Upload KTP/KIA (opsional)</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-yellow-400 transition cursor-pointer" onclick="document.getElementById('modal_ktp_file').click()">
                        <input type="file" id="modal_ktp_file" class="hidden" accept="image/*,.pdf">
                        <div id="modal_ktp_placeholder">
                            <div class="text-3xl mb-1">📷</div>
                            <p class="text-gray-600 text-sm">Klik untuk upload</p>
                            <p class="text-gray-400 text-xs">JPG, PNG, PDF (Max 2MB)</p>
                        </div>
                        <div id="modal_ktp_preview" class="hidden">
                            <img id="modal_ktp_image" class="max-h-24 mx-auto rounded">
                            <p id="modal_ktp_filename" class="text-xs text-gray-600 mt-1"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sticky bottom-0 bg-white border-t px-4 py-3 flex gap-2">
                <button type="button" onclick="closeFamilyMemberModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Batal
                </button>
                <button type="button" onclick="saveFamilyMemberFromModal()" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const responseId = {{ $response->id }};
    let autoSaveTimeout = null;

    // Saved family members data from residents table (not from responses.family_members JSON)
    let savedFamilyMembers = {};

    // Convert savedResidents array to object with sequential keys for compatibility
    @if(!empty($savedResidents))
        @foreach($savedResidents as $index => $resident)
            savedFamilyMembers[{{ $index + 1 }}] = @json($resident);
        @endforeach
    @endif

    // Load saved health data from resident_health_responses table (not from response.health_data JSON)
    const savedHealthData = @json($savedHealthData ?? []);
    const savedResidents = @json($savedResidents ?? []);

    console.log('Saved family members:', savedFamilyMembers);
    console.log('Saved health data:', savedHealthData);
    console.log('Saved residents (with KTP):', savedResidents);

    // ==================== PROFESSIONAL MODAL SYSTEM ====================
    function showModal(options) {
        const {
            title = 'Pemberitahuan',
            message = '',
            type = 'info', // 'info', 'success', 'error', 'warning', 'confirm'
            confirmText = 'OK',
            cancelText = 'Batal',
            onConfirm = null,
            onCancel = null
        } = options;

        // Remove existing modal if any
        const existingModal = document.getElementById('customModal');
        if (existingModal) existingModal.remove();

        // Create modal
        const modal = document.createElement('div');
        modal.id = 'customModal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] animate-fadeIn';

        // Icon and color based on type
        let iconHtml = '';
        let iconBgColor = '';
        let iconColor = '';

        switch(type) {
            case 'success':
                iconBgColor = 'bg-green-100';
                iconColor = 'text-green-600';
                iconHtml = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`;
                break;
            case 'error':
                iconBgColor = 'bg-red-100';
                iconColor = 'text-red-600';
                iconHtml = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>`;
                break;
            case 'warning':
                iconBgColor = 'bg-yellow-100';
                iconColor = 'text-yellow-600';
                iconHtml = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>`;
                break;
            case 'confirm':
                iconBgColor = 'bg-blue-100';
                iconColor = 'text-blue-600';
                iconHtml = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
                break;
            default:
                iconBgColor = 'bg-blue-100';
                iconColor = 'text-blue-600';
                iconHtml = `<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
        }

        const buttonsHtml = type === 'confirm'
            ? `<div class="flex gap-3 w-full">
                <button type="button" id="modalCancel" class="flex-1 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition">
                    ${cancelText}
                </button>
                <button type="button" id="modalConfirm" class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                    ${confirmText}
                </button>
            </div>`
            : `<button type="button" id="modalConfirm" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition">
                ${confirmText}
            </button>`;

        modal.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md mx-4 animate-scaleIn">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 ${iconBgColor} rounded-full flex items-center justify-center mb-4 ${iconColor}">
                        ${iconHtml}
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">${title}</h3>
                    <p class="text-gray-600 mb-6">${message}</p>
                    ${buttonsHtml}
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Handle confirm button
        const confirmBtn = document.getElementById('modalConfirm');
        confirmBtn.addEventListener('click', () => {
            modal.remove();
            if (onConfirm) onConfirm();
        });

        // Handle cancel button (if exists)
        const cancelBtn = document.getElementById('modalCancel');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                modal.remove();
                if (onCancel) onCancel();
            });
        }

        // Close on backdrop click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.remove();
                if (onCancel) onCancel();
            }
        });
    }

    // Shorthand functions
    window.showSuccess = (message, title = 'Berhasil!') => {
        showModal({ type: 'success', title, message });
    };

    window.showError = (message, title = 'Terjadi Kesalahan') => {
        showModal({ type: 'error', title, message });
    };

    window.showWarning = (message, title = 'Perhatian') => {
        showModal({ type: 'warning', title, message });
    };

    window.showInfo = (message, title = 'Informasi') => {
        showModal({ type: 'info', title, message });
    };

    window.showConfirm = (message, onConfirm, title = 'Konfirmasi', onCancel = null) => {
        showModal({ type: 'confirm', title, message, onConfirm, onCancel, confirmText: 'Ya', cancelText: 'Tidak' });
    };

    const submitBtn = document.getElementById('submitBtn');
    const questionCounter = document.getElementById('questionCounter');

    // Set initial counter (total questions)
    questionCounter.textContent = '1-' + {{ $actualQuestions->count() }};
    document.getElementById('stickyQuestionCounter').textContent = '1-' + {{ $actualQuestions->count() }};

    // Validate Nomor KK (16 digit)
    document.addEventListener('input', function(e) {
        if (e.target.placeholder && e.target.placeholder.includes('Nomor Kartu Keluarga')) {
            const value = e.target.value;
            if (value.length > 0 && value.length < 16) {
                e.target.setCustomValidity('Nomor KK harus 16 digit');
            } else if (value.length === 16 && !/^\d{16}$/.test(value)) {
                e.target.setCustomValidity('Nomor KK harus berupa angka');
            } else {
                e.target.setCustomValidity('');
            }
        }
    });

    // Auto-fill tanggal pendataan
    const tanggalPendataanInputs = document.querySelectorAll('input[type="datetime-local"], input[type="date"]');
    tanggalPendataanInputs.forEach(input => {
        if (input.placeholder && input.placeholder.toLowerCase().includes('tanggal pendataan')) {
            const now = new Date();
            const isoString = now.toISOString().slice(0, 16);
            input.value = isoString;
            input.setAttribute('readonly', 'true');
            input.classList.add('bg-gray-50', 'text-gray-700');
        }
    });

    // Cek jawaban yang sudah ada
    function checkExistingAnswers() {
        // No longer tracking progress
    }

    // Auto-save function
    function autoSave(questionId, value, immediate = false) {
        clearTimeout(autoSaveTimeout);

        const saveFunction = () => {
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('question_id', questionId);
            formData.append('answer', value);
            formData.append('response_id', responseId);

            console.log('🔄 Autosaving...', {questionId, value, responseId});

            fetch('{{ route("questionnaire.autosave", $questionnaire->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('✅ Auto-saved:', questionId, value);
                } else {
                    console.error('❌ Autosave failed:', data.message);
                }
            })
            .catch(error => {
                console.error('❌ Autosave error:', error);
            });
        };

        if (immediate) {
            // For radio and checkbox, save immediately
            saveFunction();
        } else {
            // For text inputs, delay 1 second
            autoSaveTimeout = setTimeout(saveFunction, 1000);
        }
    }

    // Listen to all answer inputs (exclude family_members and health data)
    document.querySelectorAll('.answer-input').forEach(input => {
        const questionId = input.getAttribute('data-question-id');

        // Skip if no questionId (like family_members inputs)
        if (!questionId) return;

        if (input.type === 'radio' || input.type === 'checkbox') {
            input.addEventListener('change', function() {
                // For radio, get the value
                if (this.type === 'radio') {
                    autoSave(questionId, this.value, true); // Save immediately
                } else if (this.type === 'checkbox') {
                    // For checkbox, collect all checked values
                    const checkedValues = Array.from(document.querySelectorAll(`input[data-question-id="${questionId}"]:checked`))
                        .map(cb => cb.value);
                    autoSave(questionId, JSON.stringify(checkedValues), true); // Save immediately
                }
            });
        } else {
            // For text inputs and textareas
            input.addEventListener('input', function() {
                autoSave(questionId, this.value, false); // Delay for text inputs
            });
        }
    });

    // Auto-save for select/dropdown (wilayah, etc)
    document.querySelectorAll('select.answer-input').forEach(select => {
        const questionId = select.getAttribute('data-question-id');
        if (!questionId) return;

        select.addEventListener('change', function() {
            autoSave(questionId, this.value, true); // Save immediately on change

        });
    });

    // Prevent form submission if not 100%
    document.getElementById('surveyForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default first

        const progress = (answeredQuestions.size / totalQuestions) * 100;
        if (progress < 100) {
            showWarning('Mohon jawab semua pertanyaan terlebih dahulu!', 'Pertanyaan Belum Lengkap');
            return false;
        }

        // Show confirmation popup
        const confirmPopup = document.createElement('div');
        confirmPopup.id = 'confirmPopup';
        confirmPopup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
        confirmPopup.innerHTML = `
            <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md mx-4">
                <div class="flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Apakah Anda Yakin?</h3>
                    <p class="text-gray-600 mb-6">Kuesioner yang sudah terkirim tidak bisa diubah lagi setelah dikirim.</p>
                    <div class="flex gap-3 w-full">
                        <button type="button" id="cancelSubmit" class="flex-1 px-6 py-3 bg-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-400 transition">
                            Tidak
                        </button>
                        <button type="button" id="confirmSubmit" class="flex-1 px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                            Ya, Kirim
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(confirmPopup);

        // Handle cancel
        document.getElementById('cancelSubmit').addEventListener('click', function() {
            confirmPopup.remove();
        });

        // Handle confirm
        document.getElementById('confirmSubmit').addEventListener('click', function() {
            confirmPopup.remove();

            // Show loading popup
            const loadingPopup = document.createElement('div');
            loadingPopup.id = 'loadingPopup';
            loadingPopup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]';
            loadingPopup.innerHTML = `
                <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-sm mx-4">
                    <div class="flex flex-col items-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-yellow-500 mb-4"></div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Mengirim Jawaban...</h3>
                        <p class="text-gray-600 text-center">Mohon tunggu, jawaban Anda sedang diproses</p>
                    </div>
                </div>
            `;
            document.body.appendChild(loadingPopup);

            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
            }

            // Submit the form
            document.getElementById('surveyForm').submit();
        });
    });

    // Initial setup
    checkExistingAnswers();

    // Get GPS location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        });
    }

    // Initialize Choices.js instances storage
    const choicesInstances = {};

    // Family Members Management
    let familyMemberCount = 0;
    let occupationsData = [];
    let educationsData = [];
    let familyRelationsData = [];
    let citizenTypesData = [];
    let maritalStatusesData = [];
    let religionsData = [];

    // Load all master data
    async function loadOccupations() {
        try {
            const response = await fetch('/api/occupations');
            occupationsData = await response.json();
        } catch (error) {
            console.error('Error loading occupations:', error);
        }
    }

    async function loadEducations() {
        try {
            const response = await fetch('/api/educations');
            educationsData = await response.json();
        } catch (error) {
            console.error('Error loading educations:', error);
        }
    }

    async function loadFamilyRelations() {
        try {
            const response = await fetch('/api/family-relations');
            familyRelationsData = await response.json();
        } catch (error) {
            console.error('Error loading family relations:', error);
        }
    }

    async function loadMaritalStatuses() {
        try {
            const response = await fetch('/api/marital-statuses');
            maritalStatusesData = await response.json();
        } catch (error) {
            console.error('Error loading marital statuses:', error);
        }
    }

    async function loadReligions() {
        try {
            const response = await fetch('/api/religions');
            religionsData = await response.json();
        } catch (error) {
            console.error('Error loading religions:', error);
        }
    }

    async function loadCitizenTypes() {
        try {
            const response = await fetch('/api/citizen-types');
            const result = await response.json();
            citizenTypesData = result.data || result;
        } catch (error) {
            console.error('Error loading citizen types:', error);
        }
    }

    // Helper function to format option text with code
    function formatOptionText(item) {
        if (item.code) {
            return `${item.code}. ${item.name}`;
        }
        return item.name;
    }

    // Calculate age from birth date
    function calculateAge(birthDate) {
        const today = new Date();
        const birth = new Date(birthDate);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        return age;
    }

    // Update age when birth date changes (receives date in yyyy-mm-dd format from flatpickr)
    window.updateAge = function(memberId, dateStr) {
        const ageInput = document.getElementById(`age-${memberId}`);

        if (ageInput && dateStr) {
            const age = calculateAge(dateStr);
            ageInput.value = age >= 0 ? age : '';
        }
    };

    // Initialize Flatpickr date picker for family member
    function initializeDatePicker(memberId) {
        const dateInput = document.getElementById(`tanggal_lahir_${memberId}`);
        if (dateInput) {
            flatpickr(dateInput, {
                dateFormat: "d/m/Y",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "id",
                allowInput: true,
                maxDate: "today",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        // Format to yyyy-mm-dd for calculation
                        const date = selectedDates[0];
                        const isoDate = date.toISOString().split('T')[0];
                        updateAge(memberId, isoDate);
                    }
                }
            });
        }
    }

    // Load saved family members from database - now renders to table
    async function loadSavedFamilyMembers() {
        try {
            console.log('🔵 loadSavedFamilyMembers called');
            console.log('🔵 savedFamilyMembers:', savedFamilyMembers);
            console.log('🔵 typeof savedFamilyMembers:', typeof savedFamilyMembers);
            console.log('🔵 Object.keys(savedFamilyMembers):', Object.keys(savedFamilyMembers));

            if (!savedFamilyMembers || Object.keys(savedFamilyMembers).length === 0) {
                console.log('⚠️ No saved family members to load');
                return;
            }

            // Clear table first
            const tbody = document.getElementById('family-members-tbody');
            if (!tbody) {
                console.error('❌ Table tbody not found!');
                return;
            }
            tbody.innerHTML = '';
            console.log('✅ Table cleared');

            // savedFamilyMembers is an object with keys like "1", "2", etc.
            const memberKeys = Object.keys(savedFamilyMembers).sort((a, b) => parseInt(a) - parseInt(b));
            console.log('🔵 Member keys:', memberKeys);

            // Update familyMemberCount to the highest key so new members get unique IDs
            if (memberKeys.length > 0) {
                familyMemberCount = Math.max(...memberKeys.map(k => parseInt(k)));
                console.log('🔵 familyMemberCount updated to:', familyMemberCount);
            }

            memberKeys.forEach((key, index) => {
                console.log(`🔵 Processing member ${key}:`, savedFamilyMembers[key]);
                const memberData = savedFamilyMembers[key];
                addMemberToTable(key, memberData, index + 1);
                console.log(`✅ Added member ${key} to table`);
            });

            console.log('✅ Finished loading family members to table. familyMemberCount =', familyMemberCount);

            // Auto-generate health questions for members who have saved health data
            if (savedHealthData && Object.keys(savedHealthData).length > 0) {
                console.log('🔵 Found saved health data, will auto-generate health questions...');

                try {
                    for (const memberId of Object.keys(savedHealthData)) {
                        console.log(`🔵 Generating health questions for member ${memberId}...`);

                        // Check if member exists in savedFamilyMembers
                        if (!savedFamilyMembers[memberId]) {
                            console.warn(`⚠️ Member ${memberId} not found in savedFamilyMembers, skipping`);
                            continue;
                        }

                        try {
                            await generateHealthQuestionsForMember(parseInt(memberId));

                            // Small delay to ensure DOM is ready
                            await new Promise(resolve => setTimeout(resolve, 100));
                        } catch (genError) {
                            console.error(`❌ Error generating health questions for member ${memberId}:`, genError);
                            // Continue with next member
                        }
                    }

                    console.log('🔵 Now loading saved health data into forms...');
                    await loadSavedHealthData();
                } catch (healthError) {
                    console.error('❌ Error in health data auto-load:', healthError);
                    // Don't block page load if health data fails
                }
            }
        } catch (error) {
            console.error('❌ Error in loadSavedFamilyMembers:', error);
            console.error('❌ Stack:', error.stack);
        }
    }

    // Add member row to table
    function addMemberToTable(memberId, memberData, rowNumber) {
        try {
            console.log(`🟢 addMemberToTable called: memberId=${memberId}, rowNumber=${rowNumber}`);
            console.log(`🟢 memberData:`, memberData);

            const tbody = document.getElementById('family-members-tbody');
            if (!tbody) {
                console.error('❌ Table tbody not found in addMemberToTable!');
                return;
            }

            // Remove "no members" row if exists
            const noMembersRow = document.getElementById('no-members-row');
            if (noMembersRow) {
                noMembersRow.remove();
                console.log('✅ Removed no-members-row');
            }

            // Get display values from master data
            console.log(`🟢 familyRelationsData:`, familyRelationsData);
            const hubunganText = getOptionTextById(familyRelationsData, memberData.hubungan);
            // jenis_kelamin is now stored as '1' or '2' in database
            const jenisKelaminText =
                memberData.jenis_kelamin === '1' ? 'L' :
                memberData.jenis_kelamin === '2' ? 'P' : '-';

            // Calculate age
            let umur = memberData.umur || '-';
            if (memberData.tanggal_lahir && !memberData.umur) {
                let dateStr = memberData.tanggal_lahir;
                if (dateStr.includes('/')) {
                    const [d, m, y] = dateStr.split('/');
                    dateStr = `${y}-${m}-${d}`;
                }
                const age = calculateAge(dateStr);
                umur = age >= 0 ? age : '-';
            }

            console.log(`🟢 Display values: hubungan=${hubunganText}, jenis_kelamin=${jenisKelaminText}, umur=${umur}`);

            const row = document.createElement('tr');
            row.id = `member-row-${memberId}`;
            row.innerHTML = `
                <td class="border px-2 py-2">${rowNumber}</td>
                <td class="border px-2 py-2 text-xs">${memberData.nik || '-'}</td>
                <td class="border px-2 py-2 font-medium">${memberData.nama_lengkap || '-'}</td>
                <td class="border px-2 py-2">${hubunganText}</td>
                <td class="border px-2 py-2 text-center">${jenisKelaminText}</td>
                <td class="border px-2 py-2 text-center">${umur}</td>
                <td class="border px-2 py-2 text-center">
                    <div class="flex gap-1 justify-center flex-wrap">
                        <button type="button" onclick="selectMemberForHealth(${memberId})" class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600" title="Pilih untuk pertanyaan kesehatan V">V</button>
                        <button type="button" onclick="editFamilyMemberModal(${memberId})" class="px-2 py-1 bg-blue-500 text-white rounded text-xs hover:bg-blue-600">Edit</button>
                        <button type="button" onclick="deleteFamilyMember(${memberId})" class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">Hapus</button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
            console.log(`✅ Row appended to table for member ${memberId}`);

            // Store data in hidden form
            storeMemberData(memberId, memberData);
            console.log(`✅ Member data stored for member ${memberId}`);
        } catch (error) {
            console.error(`❌ Error in addMemberToTable for member ${memberId}:`, error);
            console.error('❌ Stack:', error.stack);
        }
    }

    // Get option text by ID from master data
    function getOptionTextById(dataArray, id) {
        if (!id || !dataArray) return '-';
        const item = dataArray.find(d => d.id == id);
        return item ? (item.code ? `${item.code}. ${item.name}` : item.name) : '-';
    }

    // Store member data in hidden form for submission
    function storeMemberData(memberId, memberData) {
        const container = document.getElementById('family-members-list');

        // Remove existing hidden inputs for this member
        container.querySelectorAll(`input[name^="family_members[${memberId}]"]`).forEach(el => el.remove());

        // Create hidden inputs
        const fields = ['nik', 'citizen_type_id', 'nama_lengkap', 'hubungan', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'status_perkawinan', 'agama', 'pendidikan', 'pekerjaan', 'golongan_darah', 'phone'];
        fields.forEach(field => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `family_members[${memberId}][${field}]`;
            input.value = memberData[field] || '';
            container.appendChild(input);
        });
    }

    // Open modal for adding new member
    window.openFamilyMemberModal = function(editId = null) {
        const modal = document.getElementById('familyMemberModal');
        const title = document.getElementById('familyModalTitle');

        // Populate select options
        populateModalSelects();

        // Clear form
        clearModalForm();

        if (editId) {
            title.textContent = 'Edit Anggota Keluarga';
            document.getElementById('editMemberId').value = editId;
            // Fill form with existing data
            const memberData = savedFamilyMembers[editId];
            if (memberData) {
                fillModalForm(memberData);
            }
        } else {
            title.textContent = 'Tambah Anggota Keluarga';
            document.getElementById('editMemberId').value = '';
        }

        modal.classList.remove('hidden');

        // Initialize date picker for modal
        initializeModalDatePicker();
    };

    // Close modal
    window.closeFamilyMemberModal = function() {
        document.getElementById('familyMemberModal').classList.add('hidden');
    };

    // Populate modal select options
    function populateModalSelects() {
        // Citizen Type
        const citizenSelect = document.getElementById('modal_citizen_type_id');
        citizenSelect.innerHTML = '<option value="">Jenis Warga</option>';
        if (Array.isArray(citizenTypesData)) {
            citizenTypesData.forEach(item => {
                citizenSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        }

        // Hubungan Keluarga
        const hubunganSelect = document.getElementById('modal_hubungan');
        hubunganSelect.innerHTML = '<option value="">Hubungan Keluarga *</option>';
        familyRelationsData.forEach(item => {
            hubunganSelect.innerHTML += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        // Status Perkawinan
        const statusSelect = document.getElementById('modal_status_perkawinan');
        statusSelect.innerHTML = '<option value="">Status Perkawinan</option>';
        maritalStatusesData.forEach(item => {
            statusSelect.innerHTML += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        // Agama
        const agamaSelect = document.getElementById('modal_agama');
        agamaSelect.innerHTML = '<option value="">Agama</option>';
        religionsData.forEach(item => {
            agamaSelect.innerHTML += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        // Pendidikan
        const pendidikanSelect = document.getElementById('modal_pendidikan');
        pendidikanSelect.innerHTML = '<option value="">Pendidikan</option>';
        educationsData.forEach(item => {
            pendidikanSelect.innerHTML += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        // Pekerjaan
        const pekerjaanSelect = document.getElementById('modal_pekerjaan');
        pekerjaanSelect.innerHTML = '<option value="">Pekerjaan</option>';
        occupationsData.forEach(item => {
            pekerjaanSelect.innerHTML += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });
    }

    // Clear modal form
    function clearModalForm() {
        const fields = ['nik', 'citizen_type_id', 'nama_lengkap', 'hubungan', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'status_perkawinan', 'agama', 'pendidikan', 'pekerjaan', 'golongan_darah', 'phone'];
        fields.forEach(field => {
            const el = document.getElementById(`modal_${field}`);
            if (el) el.value = '';
        });
        document.getElementById('modal_umur').value = '';
        document.getElementById('modal_ktp_file').value = '';
        document.getElementById('modal_ktp_placeholder').classList.remove('hidden');
        document.getElementById('modal_ktp_preview').classList.add('hidden');
    }

    // Fill modal form with data
    function fillModalForm(memberData) {
        console.log('🔍 fillModalForm called with data:', memberData);

        const fields = ['nik', 'citizen_type_id', 'nama_lengkap', 'hubungan', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'status_perkawinan', 'agama', 'pendidikan', 'pekerjaan', 'golongan_darah', 'phone'];
        fields.forEach(field => {
            const el = document.getElementById(`modal_${field}`);
            if (el && memberData[field]) {
                el.value = memberData[field];
                console.log(`✅ Set ${field} =`, memberData[field]);
            } else if (el) {
                console.log(`⚠️ ${field} is empty or null`);
            } else {
                console.log(`❌ Element modal_${field} not found`);
            }
        });

        // Calculate and show age
        if (memberData.tanggal_lahir) {
            let dateStr = memberData.tanggal_lahir;
            if (dateStr.includes('/')) {
                const [d, m, y] = dateStr.split('/');
                dateStr = `${y}-${m}-${d}`;
            }
            const age = calculateAge(dateStr);
            document.getElementById('modal_umur').value = age >= 0 ? age : '';
        }

        // Show KTP preview if exists
        const ktpPath = memberData.ktp_image_path || memberData.ktp_kia_path;
        if (ktpPath) {
            const ktpImage = document.getElementById('modal_ktp_image');
            const ktpFilename = document.getElementById('modal_ktp_filename');
            const ktpPlaceholder = document.getElementById('modal_ktp_placeholder');
            const ktpPreview = document.getElementById('modal_ktp_preview');

            // Set image source (ensure proper path)
            const imagePath = ktpPath.startsWith('/storage') ? ktpPath : `/storage/${ktpPath}`;
            ktpImage.src = imagePath;

            // Extract filename from path
            const filename = ktpPath.split('/').pop();
            ktpFilename.textContent = filename;

            // Show preview, hide placeholder
            ktpPlaceholder.classList.add('hidden');
            ktpPreview.classList.remove('hidden');
        }
    }

    // Initialize date picker for modal
    function initializeModalDatePicker() {
        const dateInput = document.getElementById('modal_tanggal_lahir');
        if (dateInput && !dateInput._flatpickr) {
            flatpickr(dateInput, {
                dateFormat: "d/m/Y",
                altInput: true,
                altFormat: "d/m/Y",
                locale: "id",
                allowInput: true,
                maxDate: "today",
                onChange: function(selectedDates) {
                    if (selectedDates.length > 0) {
                        const date = selectedDates[0];
                        const isoDate = date.toISOString().split('T')[0];
                        const age = calculateAge(isoDate);
                        document.getElementById('modal_umur').value = age >= 0 ? age : '';
                    }
                }
            });
        }
    }

    // Save family member from modal
    window.saveFamilyMemberFromModal = function() {
        // Validate required fields
        const namaLengkap = document.getElementById('modal_nama_lengkap').value;
        const hubungan = document.getElementById('modal_hubungan').value;
        const tanggalLahir = document.getElementById('modal_tanggal_lahir').value;
        const jenisKelamin = document.getElementById('modal_jenis_kelamin').value;

        if (!namaLengkap || !hubungan || !tanggalLahir || !jenisKelamin) {
            showError('Mohon lengkapi field yang wajib diisi (Nama, Hubungan, Tanggal Lahir, Jenis Kelamin)');
            return;
        }

        // Collect data from modal
        const memberData = {
            nik: document.getElementById('modal_nik').value,
            citizen_type_id: document.getElementById('modal_citizen_type_id').value,
            nama_lengkap: document.getElementById('modal_nama_lengkap').value.toUpperCase(),
            hubungan: document.getElementById('modal_hubungan').value,
            tempat_lahir: document.getElementById('modal_tempat_lahir').value.toUpperCase(),
            tanggal_lahir: document.getElementById('modal_tanggal_lahir').value,
            umur: document.getElementById('modal_umur').value,
            jenis_kelamin: document.getElementById('modal_jenis_kelamin').value,
            status_perkawinan: document.getElementById('modal_status_perkawinan').value,
            agama: document.getElementById('modal_agama').value,
            pendidikan: document.getElementById('modal_pendidikan').value,
            pekerjaan: document.getElementById('modal_pekerjaan').value,
            golongan_darah: document.getElementById('modal_golongan_darah').value,
            phone: document.getElementById('modal_phone').value,
        };

        const editId = document.getElementById('editMemberId').value;
        let memberId;

        if (editId) {
            // Edit existing
            memberId = editId;
            savedFamilyMembers[memberId] = memberData;
        } else {
            // Add new
            familyMemberCount++;
            memberId = familyMemberCount;
            savedFamilyMembers[memberId] = memberData;
        }

        // Get KTP file if uploaded
        const ktpFileInput = document.getElementById('modal_ktp_file');
        const ktpFile = ktpFileInput && ktpFileInput.files.length > 0 ? ktpFileInput.files[0] : null;

        // Save to server with KTP file
        saveFamilyMembersToServer(memberId, ktpFile, memberData.hubungan);

        // Refresh table
        refreshFamilyMembersTable();

        // Close modal
        closeFamilyMemberModal();

        // Generate health questions for this member
        generateHealthQuestionsForMember(parseInt(memberId));

        // If this is kepala keluarga (hubungan = 1), reload page after saving
        // to update Nama Responden and NIK fields
        if (memberData.hubungan == '1') {
            showSuccess('Kepala keluarga berhasil disimpan. Halaman akan di-refresh...');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showSuccess('Anggota keluarga berhasil disimpan');
        }
    };

    // Select member for health questions - navigate to health section
    window.selectMemberForHealth = function(memberId) {
        console.log('selectMemberForHealth called for member:', memberId);
        generateHealthQuestionsForMember(memberId);
    };

    // Toggle tensi input visibility based on ukur_tensi answer
    window.toggleTensiInput = function(memberId, show) {
        const tensiDiv = document.getElementById(`health_q10b_${memberId}`);
        if (tensiDiv) {
            if (show) {
                tensiDiv.classList.remove('hidden');
            } else {
                tensiDiv.classList.add('hidden');
            }
        }
    };

    // Edit family member - open modal with data
    window.editFamilyMemberModal = function(memberId) {
        openFamilyMemberModal(memberId);
    };

    // Delete family member
    window.deleteFamilyMember = function(memberId) {
        showConfirm('Yakin ingin menghapus anggota keluarga ini?', () => {
            delete savedFamilyMembers[memberId];

            // Remove from table
            const row = document.getElementById(`member-row-${memberId}`);
            if (row) row.remove();

            // Remove hidden inputs
            const container = document.getElementById('family-members-list');
            container.querySelectorAll(`input[name^="family_members[${memberId}]"]`).forEach(el => el.remove());

            // Remove health section
            const healthDiv = document.getElementById(`health-member-${memberId}`);
            if (healthDiv) healthDiv.remove();

            // Save to server
            saveFamilyMembersToServer();

            // Check if table is empty
            const tbody = document.getElementById('family-members-tbody');
            if (tbody.children.length === 0) {
                tbody.innerHTML = '<tr id="no-members-row"><td colspan="7" class="border px-2 py-4 text-center text-gray-500">Belum ada anggota keluarga</td></tr>';
            }

            showSuccess('Anggota keluarga berhasil dihapus');
        }, 'Hapus Anggota Keluarga?');
    };

    // Refresh table display
    function refreshFamilyMembersTable() {
        const tbody = document.getElementById('family-members-tbody');
        tbody.innerHTML = '';

        const memberKeys = Object.keys(savedFamilyMembers).sort((a, b) => parseInt(a) - parseInt(b));

        if (memberKeys.length === 0) {
            tbody.innerHTML = '<tr id="no-members-row"><td colspan="7" class="border px-2 py-4 text-center text-gray-500">Belum ada anggota keluarga</td></tr>';
            return;
        }

        memberKeys.forEach((key, index) => {
            addMemberToTable(key, savedFamilyMembers[key], index + 1);
        });
    }

    // Save family members to server via AJAX
    function saveFamilyMembersToServer(memberId = null, ktpFile = null, hubungan = null) {
        console.log('🔄 Saving family members to server...');
        console.log('   Member ID:', memberId);
        console.log('   Hubungan:', hubungan);
        console.log('   savedFamilyMembers:', savedFamilyMembers);

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('response_id', responseId);
        formData.append('family_members', JSON.stringify(savedFamilyMembers));

        // Append KTP file if provided
        if (ktpFile && memberId) {
            formData.append(`ktp_${memberId}`, ktpFile);
            console.log(`   Appending KTP file for member ${memberId}:`, ktpFile.name);
        }

        fetch('{{ route("questionnaire.save-family-members", $questionnaire->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('✅ Family members saved successfully!');
            console.log('   Server response:', data);

            // Update savedFamilyMembers with server response if it contains updated data
            if (data.residents) {
                console.log('   Updating savedFamilyMembers with server data:', data.residents);
                // Map server residents to savedFamilyMembers format
                data.residents.forEach((resident, index) => {
                    const key = index + 1;
                    savedFamilyMembers[key] = resident;
                });
                console.log('   Updated savedFamilyMembers:', savedFamilyMembers);
            }
        })
        .catch(error => {
            console.error('❌ Error saving family members:', error);
        });
    }

    // KTP file handling in modal
    document.getElementById('modal_ktp_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('modal_ktp_image').src = e.target.result;
                document.getElementById('modal_ktp_filename').textContent = file.name;
                document.getElementById('modal_ktp_placeholder').classList.add('hidden');
                document.getElementById('modal_ktp_preview').classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Load saved health data from database
    async function loadSavedHealthData() {
        console.log('loadSavedHealthData called with:', savedHealthData);

        if (!savedHealthData || Object.keys(savedHealthData).length === 0) {
            return;
        }

        // Create health section if needed
        const healthKeys = Object.keys(savedHealthData);

        for (const memberId of healthKeys) {
            const healthData = savedHealthData[memberId];
            console.log(`Loading health data for member ${memberId}:`, healthData);

            // Generate health questions for this member
            await generateHealthQuestionsForMember(parseInt(memberId));

            // Wait for DOM update
            await new Promise(resolve => setTimeout(resolve, 100));

            // Find and fill the health data
            const healthMemberDiv = document.getElementById(`health-member-${memberId}`);
            if (healthMemberDiv) {
                // All radio button fields to restore (including new conditional questions)
                const radioFields = ['jkn', 'merokok', 'jamban', 'air_bersih', 'tb_paru', 'obat_tbc', 'gejala_tb', 'hipertensi', 'obat_hipertensi', 'ukur_tensi', 'kontrasepsi', 'melahirkan_faskes', 'asi_eksklusif', 'imunisasi_lengkap', 'pemantauan_balita'];

                radioFields.forEach(field => {
                    if (healthData[field]) {
                        const radio = healthMemberDiv.querySelector(`input[name="health[${memberId}][${field}]"][value="${healthData[field]}"]`);
                        if (radio) {
                            radio.checked = true;
                            console.log(`Set ${field} = ${healthData[field]} for member ${memberId}`);

                            // Trigger change event for conditional fields
                            radio.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                });

                // Number input fields (sistolik, diastolik)
                const numberFields = ['sistolik', 'diastolik'];
                numberFields.forEach(field => {
                    if (healthData[field]) {
                        const input = healthMemberDiv.querySelector(`input[name="health[${memberId}][${field}]"]`);
                        if (input) {
                            input.value = healthData[field];
                            console.log(`Set ${field} = ${healthData[field]} for member ${memberId}`);
                        }
                    }
                });

                // Show tensi input if ukur_tensi = 1
                if (healthData.ukur_tensi === '1' || healthData.ukur_tensi === 1) {
                    const tensiDiv = document.getElementById(`health_q10b_${memberId}`);
                    if (tensiDiv) {
                        tensiDiv.classList.remove('hidden');
                    }
                }
            }
        }

        console.log('Finished loading health data');
    }

    // Generate health questions per family member
    async function generateHealthQuestionsForMember(memberId) {
        console.log('generateHealthQuestionsForMember dipanggil untuk member:', memberId);

        // Get member data from savedFamilyMembers
        const memberData = savedFamilyMembers[memberId];
        let memberName = memberData?.nama_lengkap || `Anggota ${memberId}`;
        let memberAge = parseInt(memberData?.umur) || 0;

        // Calculate age from tanggal_lahir if umur not set
        if (!memberAge && memberData?.tanggal_lahir) {
            let dateStr = memberData.tanggal_lahir;
            if (dateStr.includes('/')) {
                const [d, m, y] = dateStr.split('/');
                dateStr = `${y}-${m}-${d}`;
            }
            memberAge = calculateAge(dateStr);
        }

        console.log(`Member ${memberId}: ${memberName}, Umur: ${memberAge}`);

        // Get the container that already exists in HTML
        const healthContainer = document.getElementById('dynamic-health-container');

        if (!healthContainer) {
            console.error('FATAL: dynamic-health-container tidak ditemukan');
            alert('Error: Tidak dapat menampilkan pertanyaan kesehatan. Silakan refresh halaman.');
            return;
        }

        console.log('Using health container:', healthContainer);

        // Check if already exists for this member
        const existingDiv = document.getElementById(`health-member-${memberId}`);
        if (existingDiv) {
            console.log('Health questions sudah ada untuk member:', memberId);
            existingDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            existingDiv.classList.add('ring-2', 'ring-yellow-400');
            setTimeout(() => existingDiv.classList.remove('ring-2', 'ring-yellow-400'), 2000);
            return;
        }

        // Build questions HTML based on age
        let questionsHTML = `
            <h4 class="font-semibold text-gray-800 mb-3 pb-2 border-b flex items-center gap-2">
                <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded">ID: ${memberId}</span>
                ${memberName}
                <span class="text-sm font-normal text-gray-500">(Umur: ${memberAge} tahun)</span>
            </h4>

            <div class="space-y-4">
                <!-- Pertanyaan untuk SEMUA UMUR -->
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs font-semibold text-gray-500 mb-3">📋 Pertanyaan untuk Semua Umur</p>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">1. Apakah mempunyai kartu jaminan kesehatan atau JKN?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][jkn]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][jkn]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">2. Apakah Saudara merokok?</label>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][merokok]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya (setiap hari, sering/kadang-kadang)</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][merokok]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak (tidak/sudah berhenti)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
        `;

        // Pertanyaan untuk umur >= 15 tahun
        if (memberAge >= 15) {
            questionsHTML += `
                <div class="bg-blue-50 p-3 rounded-lg border border-blue-200">
                    <p class="text-xs font-semibold text-blue-600 mb-3">📋 Pertanyaan untuk Umur ≥ 15 Tahun</p>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">3. Apakah Saudara biasa buang air besar di jamban?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][jamban]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][jamban]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">4. Apakah Saudara biasa menggunakan air bersih?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][air_bersih]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][air_bersih]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">5. Apakah Saudara pernah didiagnosis menderita tuberkulosis (TB) paru?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][tb_paru]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][tb_paru]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak → P.7</span>
                                </label>
                            </div>
                        </div>

                        <div id="health_q6_${memberId}" class="pl-4 border-l-2 border-blue-300">
                            <label class="block text-sm font-medium text-gray-700 mb-2">6. Bila ya, apakah meminum obat TBC secara teratur (selama 6 bulan)?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][obat_tbc]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya → P.8</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][obat_tbc]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak → P.8</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">7. Apakah Saudara pernah menderita batuk berdahak > 2 minggu disertai satu atau lebih gejala: dahak bercampur darah/batuk berdarah, berat badan menurun, berkeringat malam hari tanpa kegiatan fisik, dan demam > 1 bulan?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][gejala_tb]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][gejala_tb]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">8. Apakah Saudara pernah didiagnosis menderita tekanan darah tinggi/hipertensi?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][hipertensi]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][hipertensi]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak → P.10a</span>
                                </label>
                            </div>
                        </div>

                        <div id="health_q9_${memberId}" class="pl-4 border-l-2 border-blue-300">
                            <label class="block text-sm font-medium text-gray-700 mb-2">9. Bila ya, apakah selama ini Saudara meminum obat tekanan darah tinggi/hipertensi secara teratur?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][obat_hipertensi]" value="1" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">1. Ya → P.11</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][obat_hipertensi]" value="2" class="w-4 h-4 text-yellow-500">
                                    <span class="text-sm">2. Tidak → P.11</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">10a. Apakah dilakukan pengukuran tekanan darah?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][ukur_tensi]" value="1" class="w-4 h-4 text-yellow-500" onchange="toggleTensiInput(${memberId}, true)">
                                    <span class="text-sm">1. Ya</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="health[${memberId}][ukur_tensi]" value="2" class="w-4 h-4 text-yellow-500" onchange="toggleTensiInput(${memberId}, false)">
                                    <span class="text-sm">2. Tidak → P.11</span>
                                </label>
                            </div>
                        </div>

                        <div id="health_q10b_${memberId}" class="pl-4 border-l-2 border-blue-300 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">10b. Hasil pengukuran tekanan darah</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-500">b.1. Sistolik (mmHg)</label>
                                    <input type="number" name="health[${memberId}][sistolik]" class="w-full px-3 py-2 text-sm border rounded-lg" placeholder="mmHg">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">b.2. Diastolik (mmHg)</label>
                                    <input type="number" name="health[${memberId}][diastolik]" class="w-full px-3 py-2 text-sm border rounded-lg" placeholder="mmHg">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Get member gender for conditional questions
        const memberGender = memberData?.jenis_kelamin; // 1 = Laki-laki, 2 = Perempuan

        // Pertanyaan 11: Untuk wanita 10-54 tahun (tidak hamil) atau laki-laki >= 10 tahun
        if ((memberGender === '2' && memberAge >= 10 && memberAge <= 54) ||
            (memberGender === '1' && memberAge >= 10)) {
            const genderLabel = memberGender === '2' ? 'wanita berstatus menikah (usia 10-54 tahun) dan tidak hamil' : 'laki-laki berstatus menikah (usia ≥ 10 tahun)';
            questionsHTML += `
                <div class="bg-purple-50 p-3 rounded-lg border border-purple-200">
                    <p class="text-xs font-semibold text-purple-600 mb-3">📋 Untuk Anggota Keluarga ${genderLabel}</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">11. Apakah Saudara menggunakan alat kontrasepsi atau ikut program Keluarga Berencana?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][kontrasepsi]" value="1" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">1. Ya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][kontrasepsi]" value="2" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">2. Tidak</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Pertanyaan 12: Untuk ibu yang memiliki anak < 12 bulan
        // Note: Ini perlu data tambahan dari user tentang apakah punya bayi < 12 bulan
        if (memberGender === '2' && memberAge >= 15 && memberAge <= 54) {
            questionsHTML += `
                <div class="bg-pink-50 p-3 rounded-lg border border-pink-200">
                    <p class="text-xs font-semibold text-pink-600 mb-3">📋 Untuk Ibu yang memiliki Anggota Keluarga berumur < 12 bulan</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">12. Apakah saat Ibu melahirkan [NAMA] bersalin di fasilitas pelayanan kesehatan?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][melahirkan_faskes]" value="1" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">1. Ya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][melahirkan_faskes]" value="2" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">2. Tidak</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][melahirkan_faskes]" value="3" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">3. Tidak Ada (jika tidak punya anak < 12 bulan)</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Pertanyaan 13: Untuk bayi 0-6 bulan - ASI eksklusif
        if (memberAge >= 0 && memberAge < 1) { // Bayi 0-6 bulan (belum 1 tahun)
            questionsHTML += `
                <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                    <p class="text-xs font-semibold text-green-600 mb-3">📋 Untuk Anggota Keluarga berumur 0-6 bulan</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">13. Apakah bayi ini pada waktu usia 0-6 bulan hanya diberi ASI eksklusif?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][asi_eksklusif]" value="1" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">1. Ya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][asi_eksklusif]" value="2" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">2. Tidak</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Pertanyaan 14: Untuk bayi 0-11 bulan - imunisasi
        if (memberAge < 1) {
            questionsHTML += `
                <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                    <p class="text-xs font-semibold text-yellow-600 mb-3">📋 Untuk Anggota Keluarga berumur 0-11 bulan</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">14. Apakah selama bayi usia 0-11 bulan diberikan imunisasi lengkap?<br><span class="text-xs text-gray-500">(HB0, BCG, DPT-HB1, PT-HB2, DPT-HB3, Polio1, Polio2, Polio3, Polio4, Campak)</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][imunisasi_lengkap]" value="1" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">1. Ya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][imunisasi_lengkap]" value="2" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">2. Tidak</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }

        // Pertanyaan 15: Untuk anak 2-59 bulan (2 bulan - 4 tahun 11 bulan) - pemantauan balita
        if (memberAge >= 0 && memberAge < 5) {
            questionsHTML += `
                <div class="bg-orange-50 p-3 rounded-lg border border-orange-200">
                    <p class="text-xs font-semibold text-orange-600 mb-3">📋 Untuk Anggota Keluarga berumur 2-59 bulan</p>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">15. Apakah dalam 1 bulan terakhir dilakukan pemantauan pertumbuhan balita?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][pemantauan_balita]" value="1" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">1. Ya</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="health[${memberId}][pemantauan_balita]" value="2" class="w-4 h-4 text-yellow-500">
                                <span class="text-sm">2. Tidak</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;
        }

        questionsHTML += `</div>`;

        // Create health question card for this member
        const memberHealthDiv = document.createElement('div');
        memberHealthDiv.className = 'border border-gray-300 rounded-lg p-4 bg-white';
        memberHealthDiv.id = `health-member-${memberId}`;
        memberHealthDiv.style.cssText = 'display: block !important; visibility: visible !important;';
        memberHealthDiv.innerHTML = questionsHTML;

        healthContainer.appendChild(memberHealthDiv);
        console.log('Health questions berhasil ditambahkan untuk member:', memberId);

        // Add auto-save listeners to health radio buttons
        memberHealthDiv.querySelectorAll('input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                autoSaveHealthData(memberId);
            });
        });

        // Add auto-save listeners to number inputs (sistolik, diastolik) with debounce
        memberHealthDiv.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('input', debounce(function() {
                autoSaveHealthData(memberId);
            }, 500));
        });

        // Scroll ke section yang baru ditambahkan
        setTimeout(() => {
            memberHealthDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            // Highlight section yang baru ditambahkan
            memberHealthDiv.classList.add('ring-2', 'ring-green-400');
            setTimeout(() => {
                memberHealthDiv.classList.remove('ring-2', 'ring-green-400');
            }, 3000);
        }, 300);
    }

    // Auto-save health data for a member
    function autoSaveHealthData(memberId) {
        const healthMemberDiv = document.getElementById(`health-member-${memberId}`);
        if (!healthMemberDiv) {
            console.error('❌ Health member div not found for member:', memberId);
            return;
        }

        // Collect ALL health data for this member
        const healthData = {};

        // All radio button fields (including new conditional questions)
        const radioFields = ['jkn', 'merokok', 'jamban', 'air_bersih', 'tb_paru', 'obat_tbc', 'gejala_tb', 'hipertensi', 'obat_hipertensi', 'ukur_tensi', 'kontrasepsi', 'melahirkan_faskes', 'asi_eksklusif', 'imunisasi_lengkap', 'pemantauan_balita'];
        radioFields.forEach(field => {
            const radio = healthMemberDiv.querySelector(`input[name="health[${memberId}][${field}]"]:checked`);
            if (radio) {
                healthData[field] = radio.value;
            }
        });

        // Number input fields (sistolik, diastolik)
        const numberFields = ['sistolik', 'diastolik'];
        numberFields.forEach(field => {
            const input = healthMemberDiv.querySelector(`input[name="health[${memberId}][${field}]"]`);
            if (input && input.value) {
                healthData[field] = input.value;
            }
        });

        console.log('🔄 Auto-saving health data for member', memberId, healthData);

        // Send to server
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('response_id', responseId);

        // Append all health data fields
        Object.keys(healthData).forEach(key => {
            formData.append(`health[${memberId}][${key}]`, healthData[key]);
        });

        fetch('{{ route("questionnaire.save-health-data", $questionnaire->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('✅ Health data saved successfully:', data);
                // Show subtle indicator
                const indicator = document.createElement('span');
                indicator.className = 'text-green-500 text-xs ml-2';
                indicator.textContent = '✓ Tersimpan';
                const header = healthMemberDiv.querySelector('h4');
                if (header) {
                    const existingIndicator = header.querySelector('.text-green-500');
                    if (existingIndicator) existingIndicator.remove();
                    header.appendChild(indicator);
                    setTimeout(() => indicator.remove(), 2000);
                }
            } else {
                console.error('❌ Health data save failed:', data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('❌ Error saving health data:', error);
        });
    }

    // Debounce function for input fields
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Toggle health details
    window.toggleHealthDetails = function(memberId, value) {
        const detailsDiv = document.getElementById(`health-details-${memberId}`);
        if (detailsDiv) {
            detailsDiv.style.display = value === 'ya' ? 'block' : 'none';
        }
    };

    // Wilayah Cascade Logic
    async function loadProvinces() {
        const provinceSelects = document.querySelectorAll('select[data-type="province"]');

        try {
            const response = await fetch('/api/wilayah/provinces');
            const result = await response.json();
            const provinces = result.data || result;

            provinceSelects.forEach(select => {
                // Destroy existing Choices instance if any
                if (choicesInstances[select.id]) {
                    choicesInstances[select.id].destroy();
                }

                // Get saved value if exists
                const savedValue = select.dataset.savedValue || '';

                // Clear and populate
                select.innerHTML = '<option value="">Pilih Provinsi...</option>';
                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.id || province.code || province.kode;
                    option.textContent = province.name || province.nama;
                    option.dataset.code = province.id || province.code || province.kode;
                    select.appendChild(option);
                });

                // Reinitialize Choices
                choicesInstances[select.id] = new Choices(select, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Ketik untuk mencari...',
                    noResultsText: 'Tidak ada hasil',
                    itemSelectText: 'Tekan untuk memilih',
                    removeItemButton: false,
                    shouldSort: false
                });

                // Restore saved value if exists
                if (savedValue) {
                    console.log('Province savedValue:', savedValue);
                    choicesInstances[select.id].setChoiceByValue(savedValue);

                    // Trigger cascade to load regencies
                    const selectedOption = select.querySelector(`option[value="${savedValue}"]`);
                    console.log('Selected province option:', selectedOption);
                    if (selectedOption && selectedOption.dataset.code) {
                        const provinceCode = selectedOption.dataset.code;
                        console.log('Loading regencies for province code:', provinceCode);
                        const nextRegency = document.querySelector('select[data-type=\"regency\"]');
                        if (nextRegency) {
                            loadRegencies(provinceCode, nextRegency);
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error loading provinces:', error);
        }
    }

    async function loadRegencies(provinceCode, regencySelect) {
        // Destroy existing Choices instance
        if (choicesInstances[regencySelect.id]) {
            choicesInstances[regencySelect.id].destroy();
        }

        regencySelect.innerHTML = '<option value="">...�� Memuat kabupaten/kota...</option>';

        try {
            const response = await fetch(`/api/wilayah/regencies/${provinceCode}`);
            const result = await response.json();
            const regencies = result.data || result;

            // Get saved value if exists
            const savedValue = regencySelect.dataset.savedValue || '';

            regencySelect.innerHTML = '<option value="">Pilih Kabupaten/Kota...</option>';
            regencies.forEach(regency => {
                const option = document.createElement('option');
                option.value = regency.id || regency.code || regency.kode;
                option.textContent = regency.name || regency.nama;
                option.dataset.code = regency.id || regency.code || regency.kode;
                regencySelect.appendChild(option);
            });

            // Reinitialize Choices
            choicesInstances[regencySelect.id] = new Choices(regencySelect, {
                searchEnabled: true,
                searchPlaceholderValue: 'Ketik untuk mencari...',
                noResultsText: 'Tidak ada hasil',
                itemSelectText: 'Tekan untuk memilih',
                removeItemButton: false,
                shouldSort: false
            });

            // Restore saved value if exists
            if (savedValue) {
                // Wait for Choices.js to finish rendering
                setTimeout(() => {
                    console.log('Regency savedValue:', savedValue);
                    const options = Array.from(regencySelect.options);
                    console.log('Available regency options:', options.map(o => ({value: o.value, text: o.textContent.trim()})));

                    // Try to set by value (ID) first
                    let matched = false;
                    let matchedValue = null;

                    // Try direct ID match
                    const optById = options.find(opt => opt.value === savedValue);
                    if (optById) {
                        matchedValue = optById.value;
                        matched = true;
                        console.log('Matched regency by ID:', savedValue);
                    }

                    // Try text match (case insensitive, trimmed)
                    if (!matched) {
                        console.log('Trying to match by text:', savedValue);
                        const savedValueUpper = savedValue.toString().trim().toUpperCase();
                        const optByText = options.find(opt =>
                            opt.value !== '' && opt.textContent.trim().toUpperCase() === savedValueUpper
                        );
                        if (optByText) {
                            matchedValue = optByText.value;
                            matched = true;
                            console.log('Matched regency by text:', optByText.textContent.trim(), 'ID:', optByText.value);
                        }
                    }

                    // Set the matched value
                    if (matched && matchedValue) {
                        choicesInstances[regencySelect.id].setChoiceByValue(matchedValue);

                        // Wait a bit then trigger cascade
                        setTimeout(() => {
                            const selectedOption = regencySelect.options[regencySelect.selectedIndex];
                            console.log('Selected regency after set:', selectedOption ? selectedOption.textContent : null, selectedOption ? selectedOption.value : null);
                            if (selectedOption && selectedOption.value && selectedOption.dataset.code) {
                                const regencyCode = selectedOption.dataset.code;
                                console.log('Loading districts for regency code:', regencyCode);
                                const nextDistrict = document.querySelector('select[data-type=\"district\"]');
                                if (nextDistrict) {
                                    loadDistricts(regencyCode, nextDistrict);
                                }
                            }
                        }, 50);
                    } else {
                        console.log('NO MATCH FOUND for:', savedValue);
                    }
                }, 100);
            }
        } catch (error) {
            console.error('Error loading regencies:', error);
            regencySelect.innerHTML = '<option value="">Error loading data</option>';
        }
    }

    async function loadDistricts(regencyCode, districtSelect) {
        // Destroy existing Choices instance
        if (choicesInstances[districtSelect.id]) {
            choicesInstances[districtSelect.id].destroy();
        }

        districtSelect.innerHTML = '<option value="">...�� Memuat kecamatan...</option>';

        try {
            const response = await fetch(`/api/wilayah/districts/${regencyCode}`);
            const result = await response.json();
            const districts = result.data || result;

            // Get saved value if exists
            const savedValue = districtSelect.dataset.savedValue || '';

            districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
            districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district.id || district.code || district.kode;
                option.textContent = district.name || district.nama;
                option.dataset.code = district.id || district.code || district.kode;
                districtSelect.appendChild(option);
            });

            // Reinitialize Choices
            choicesInstances[districtSelect.id] = new Choices(districtSelect, {
                searchEnabled: true,
                searchPlaceholderValue: 'Ketik untuk mencari...',
                noResultsText: 'Tidak ada hasil',
                itemSelectText: 'Tekan untuk memilih',
                removeItemButton: false,
                shouldSort: false
            });

            // Restore saved value if exists
            if (savedValue) {
                setTimeout(() => {
                    console.log('District savedValue:', savedValue);
                    const options = Array.from(districtSelect.options);
                    console.log('Available district options:', options.map(o => ({value: o.value, text: o.textContent.trim()})));

                    let matched = false;
                    let matchedValue = null;

                    // Try direct ID match
                    const optById = options.find(opt => opt.value === savedValue);
                    if (optById) {
                        matchedValue = optById.value;
                        matched = true;
                        console.log('Matched district by ID:', savedValue);
                    }

                    // Try text match
                    if (!matched) {
                        console.log('Trying to match district by text:', savedValue);
                        const savedValueUpper = savedValue.toString().trim().toUpperCase();
                        const optByText = options.find(opt =>
                            opt.value !== '' && opt.textContent.trim().toUpperCase() === savedValueUpper
                        );
                        if (optByText) {
                            matchedValue = optByText.value;
                            matched = true;
                            console.log('Matched district by text:', optByText.textContent.trim(), 'ID:', optByText.value);
                        }
                    }

                    if (matched && matchedValue) {
                        choicesInstances[districtSelect.id].setChoiceByValue(matchedValue);

                        setTimeout(() => {
                            const selectedOption = districtSelect.options[districtSelect.selectedIndex];
                            console.log('Selected district after set:', selectedOption ? selectedOption.textContent : null, selectedOption ? selectedOption.value : null);
                            if (selectedOption && selectedOption.value && selectedOption.dataset.code) {
                                const districtCode = selectedOption.dataset.code;
                                console.log('Loading villages for district code:', districtCode);
                                const nextVillage = document.querySelector('select[data-type=\"village\"]');
                                if (nextVillage) {
                                    loadVillages(districtCode, nextVillage);
                                }
                            }
                        }, 50);
                    } else {
                        console.log('NO MATCH FOUND for district:', savedValue);
                    }
                }, 100);
            }
        } catch (error) {
            console.error('Error loading districts:', error);
            districtSelect.innerHTML = '<option value="">Error loading data</option>';
        }
    }

    async function loadVillages(districtCode, villageSelect) {
        // Destroy existing Choices instance
        if (choicesInstances[villageSelect.id]) {
            choicesInstances[villageSelect.id].destroy();
        }

        villageSelect.innerHTML = '<option value="">...�� Memuat desa/kelurahan...</option>';

        try {
            const response = await fetch(`/api/wilayah/villages/${districtCode}`);
            const result = await response.json();
            const villages = result.data || result;

            // Get saved value if exists
            const savedValue = villageSelect.dataset.savedValue || '';

            villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan...</option>';
            villages.forEach(village => {
                const option = document.createElement('option');
                option.value = village.id || village.code || village.kode;
                option.textContent = village.name || village.nama;
                option.dataset.code = village.id || village.code || village.kode;
                villageSelect.appendChild(option);
            });

            // Reinitialize Choices
            choicesInstances[villageSelect.id] = new Choices(villageSelect, {
                searchEnabled: true,
                searchPlaceholderValue: 'Ketik untuk mencari...',
                noResultsText: 'Tidak ada hasil',
                itemSelectText: 'Tekan untuk memilih',
                removeItemButton: false,
                shouldSort: false
            });

            // Restore saved value if exists
            if (savedValue) {
                setTimeout(() => {
                    console.log('Village savedValue:', savedValue);
                    const options = Array.from(villageSelect.options);
                    console.log('Available village options (first 10):', options.slice(0, 10).map(o => ({value: o.value, text: o.textContent.trim()})));

                    let matched = false;
                    let matchedValue = null;

                    // Try direct ID match
                    const optById = options.find(opt => opt.value === savedValue);
                    if (optById) {
                        matchedValue = optById.value;
                        matched = true;
                        console.log('Matched village by ID:', savedValue);
                    }

                    // Try text match
                    if (!matched) {
                        console.log('Trying to match village by text:', savedValue);
                        const savedValueUpper = savedValue.toString().trim().toUpperCase();
                        const optByText = options.find(opt =>
                            opt.value !== '' && opt.textContent.trim().toUpperCase() === savedValueUpper
                        );
                        if (optByText) {
                            matchedValue = optByText.value;
                            matched = true;
                            console.log('Matched village by text:', optByText.textContent.trim(), 'ID:', optByText.value);
                        }
                    }

                    if (matched && matchedValue) {
                        choicesInstances[villageSelect.id].setChoiceByValue(matchedValue);
                        console.log('Village selected successfully');
                    } else {
                        console.log('NO MATCH FOUND for village:', savedValue);
                    }
                }, 100);
            }
        } catch (error) {
            console.error('Error loading villages:', error);
            villageSelect.innerHTML = '<option value="">Error loading data</option>';
        }
    }

    // Setup cascade listeners
    document.querySelectorAll('select[data-type="province"]').forEach(provinceSelect => {
        provinceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const provinceCode = selectedOption.dataset.code;
            const questionId = this.getAttribute('data-question-id');

            // Auto-save province selection
            if (questionId && this.value) {
                answeredQuestions.add(questionId);
                autoSave(questionId, this.value, true);

            }

            if (provinceCode) {
                // Find next regency select
                const nextRegency = document.querySelector('select[data-type="regency"]');
                if (nextRegency) {
                    loadRegencies(provinceCode, nextRegency);

                    // Reset district and village
                    const districtSelect = document.querySelector('select[data-type="district"]');
                    const villageSelect = document.querySelector('select[data-type="village"]');
                    if (districtSelect) districtSelect.innerHTML = '<option value="">Pilih Kecamatan...</option>';
                    if (villageSelect) villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan...</option>';
                }

                // Update map coordinates
                updateWilayahCoordinates();
            }
        });
    });

    document.querySelectorAll('select[data-type="regency"]').forEach(regencySelect => {
        regencySelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const regencyCode = selectedOption.dataset.code;
            const questionId = this.getAttribute('data-question-id');

            // Auto-save regency selection
            if (questionId && this.value) {
                answeredQuestions.add(questionId);
                autoSave(questionId, this.value, true);

            }

            if (regencyCode) {
                const nextDistrict = document.querySelector('select[data-type="district"]');
                if (nextDistrict) {
                    loadDistricts(regencyCode, nextDistrict);

                    // Reset village
                    const villageSelect = document.querySelector('select[data-type="village"]');
                    if (villageSelect) villageSelect.innerHTML = '<option value="">Pilih Desa/Kelurahan...</option>';
                }

                // Update map coordinates
                updateWilayahCoordinates();
            }
        });
    });

    document.querySelectorAll('select[data-type="district"]').forEach(districtSelect => {
        districtSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const districtCode = selectedOption.dataset.code;
            const questionId = this.getAttribute('data-question-id');

            // Auto-save district selection
            if (questionId && this.value) {
                answeredQuestions.add(questionId);
                autoSave(questionId, this.value, true);

            }

            if (districtCode) {
                const nextVillage = document.querySelector('select[data-type="village"]');
                if (nextVillage) {
                    loadVillages(districtCode, nextVillage);
                }

                // Update map coordinates
                updateWilayahCoordinates();
            }
        });
    });

    document.querySelectorAll('select[data-type="village"]').forEach(villageSelect => {
        villageSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const questionId = this.getAttribute('data-question-id');

            // Auto-save village selection
            if (questionId && this.value) {
                answeredQuestions.add(questionId);
                autoSave(questionId, this.value, true);

            }

            // Update map coordinates
            updateWilayahCoordinates();
        });
    });

    // Load Puskesmas List for datalist
    async function loadPuskesmasList() {
        try {
            const response = await fetch('/api/puskesmas');
            const result = await response.json();

            // Extract data from API response structure
            const puskesmasList = result.success && Array.isArray(result.data) ? result.data : [];

            console.log('Loaded puskesmas:', puskesmasList.length);

            // Populate all puskesmas datalists
            document.querySelectorAll('datalist[id^="puskesmas_list_"]').forEach(datalist => {
                datalist.innerHTML = '';
                puskesmasList.forEach(puskesmas => {
                    const option = document.createElement('option');
                    option.value = puskesmas.name;
                    datalist.appendChild(option);
                });
                console.log(`Populated datalist ${datalist.id} with ${puskesmasList.length} options`);
            });
        } catch (error) {
            console.error('Error loading puskesmas list:', error);
        }
    }

    // Load provinces on page load
    async function initializePageData() {
        const loadingOverlay = document.getElementById('loadingOverlay');
        const loadingText = document.getElementById('loadingText');

        try {
            // Update loading text
            loadingText.textContent = 'Memuat data provinsi...';
            await loadProvinces();

            loadingText.textContent = 'Memuat data master...';
            // Load all master data in parallel
            await Promise.all([
                loadOccupations(),
                loadEducations(),
                loadFamilyRelations(),
                loadCitizenTypes(),
                loadMaritalStatuses(),
                loadReligions()
            ]);

            loadingText.textContent = 'Memuat data puskesmas...';
            await loadPuskesmasList();

            // Initialize Choices.js after a short delay
            loadingText.textContent = 'Menyelesaikan...';
            await new Promise(resolve => setTimeout(resolve, 300));
            initializeChoices();

            // Hide loading overlay
            loadingOverlay.style.opacity = '0';
            setTimeout(async () => {
                loadingOverlay.style.display = 'none';

                // Load saved family members if any exist
                if (savedFamilyMembers && Object.keys(savedFamilyMembers).length > 0) {
                    console.log('Loading saved family members...');
                    await loadSavedFamilyMembers();
                }
                // No need to auto-add first member - user clicks "+ Tambah Anggota Keluarga" button
            }, 300);
        } catch (error) {
            console.error('Error initializing page data:', error);
            loadingText.textContent = 'Terjadi kesalahan. Silakan refresh halaman.';
        }
    }

    initializePageData();

    // Map instances storage
    const mapInstances = {};
    const mapMarkers = {};
    let wilayahCoordinates = {
        lat: -2.5489,  // Papua center
        lng: 140.7003
    };

    // Initialize maps for location questions
    function initializeMaps() {
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet.js not loaded yet');
            setTimeout(initializeMaps, 500);
            return;
        }

        document.querySelectorAll('[id^="map_"]').forEach(mapElement => {
            try {
                const questionId = mapElement.id.replace('map_', '');

                // Skip if already initialized
                if (mapInstances[questionId]) {
                    return;
                }

                // Initialize Leaflet map
                const map = L.map(mapElement).setView([wilayahCoordinates.lat, wilayahCoordinates.lng], 13);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '...� OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                // Add marker
                const marker = L.marker([wilayahCoordinates.lat, wilayahCoordinates.lng], {
                    draggable: true
                }).addTo(map);

                // Update inputs when marker is dragged
                marker.on('dragend', function(e) {
                    const position = e.target.getLatLng();
                    updateLocationInputs(questionId, position.lat, position.lng);
                });

                // Update marker when map is clicked
                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    updateLocationInputs(questionId, e.latlng.lat, e.latlng.lng);
                });

                mapInstances[questionId] = map;
                mapMarkers[questionId] = marker;

                console.log('Map initialized for question:', questionId);
            } catch (error) {
                console.error('Error initializing map:', error);
            }
        });
    }

    // Update location inputs and trigger auto-save
    function updateLocationInputs(questionId, lat, lng) {
        const latInput = document.getElementById(`lat_${questionId}`);
        const lngInput = document.getElementById(`lng_${questionId}`);

        console.log('updateLocationInputs called:', {
            questionId,
            lat: lat.toFixed(6),
            lng: lng.toFixed(6),
            latInput: !!latInput,
            lngInput: !!lngInput
        });

        if (latInput && lngInput) {
            latInput.value = lat.toFixed(6);
            lngInput.value = lng.toFixed(6);

            // Trigger auto-save with composite location value
            const locationValue = JSON.stringify({
                latitude: lat.toFixed(6),
                longitude: lng.toFixed(6)
            });

            console.log('Saving location:', locationValue);

            // Mark as answered and save
            answeredQuestions.add(questionId);
            autoSave(questionId, locationValue, true);


            // Show brief confirmation
            showLocationSaved();
        }
    }

    // Show location saved indicator
    function showLocationSaved() {
        const existingIndicator = document.getElementById('location-saved-indicator');
        if (existingIndicator) existingIndicator.remove();

        const indicator = document.createElement('div');
        indicator.id = 'location-saved-indicator';
        indicator.className = 'fixed top-20 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 text-sm flex items-center gap-2';
        indicator.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Lokasi tersimpan
        `;
        document.body.appendChild(indicator);

        setTimeout(() => {
            indicator.style.opacity = '0';
            indicator.style.transition = 'opacity 0.3s';
            setTimeout(() => indicator.remove(), 300);
        }, 2000);
    }

    // Detect GPS from device
    window.detectGPS = function(questionId) {
        if (!navigator.geolocation) {
            showError('GPS tidak didukung oleh browser Anda. Silakan gunakan browser modern seperti Chrome atau Firefox.', 'GPS Tidak Tersedia');
            return;
        }

        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                updateLocationInputs(questionId, lat, lng);

                if (mapInstances[questionId]) {
                    mapInstances[questionId].setView([lat, lng], 16);
                    mapMarkers[questionId].setLatLng([lat, lng]);
                }

                button.disabled = false;
                button.innerHTML = originalText;

                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                successMsg.textContent = '� Lokasi GPS berhasil terdeteksi!';
                document.body.appendChild(successMsg);
                setTimeout(() => successMsg.remove(), 3000);
            },
            function(error) {
                button.disabled = false;
                button.innerHTML = originalText;
                showError(
                    'Gagal mendeteksi lokasi GPS. Pastikan Anda telah memberikan izin lokasi dan GPS aktif. Error: ' + error.message,
                    'GPS Error'
                );
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    };

    // Reset map to wilayah coordinates
    window.resetMapToWilayah = function(questionId) {
        if (mapInstances[questionId]) {
            mapInstances[questionId].setView([wilayahCoordinates.lat, wilayahCoordinates.lng], 13);
            mapMarkers[questionId].setLatLng([wilayahCoordinates.lat, wilayahCoordinates.lng]);
            updateLocationInputs(questionId, wilayahCoordinates.lat, wilayahCoordinates.lng);
        }
    };

    // Update wilayah coordinates when cascade changes
    async function updateWilayahCoordinates() {
        const provinceSelect = document.querySelector('select[data-type="province"]');
        const regencySelect = document.querySelector('select[data-type="regency"]');
        const districtSelect = document.querySelector('select[data-type="district"]');
        const villageSelect = document.querySelector('select[data-type="village"]');

        // Default coordinates for Papua regions
        const papuaCoordinates = {
            'papua': { lat: -4.2699, lng: 138.0804 },
            'papua barat': { lat: -1.3361, lng: 133.1747 },
            'papua tengah': { lat: -3.3694, lng: 136.5984 },
            'papua pegunungan': { lat: -4.0667, lng: 140.5167 },
            'papua selatan': { lat: -5.4500, lng: 140.5167 },
            'papua barat daya': { lat: -1.8833, lng: 132.2833 }
        };

        let coordinates = { lat: -2.5489, lng: 140.7003 }; // Default Papua center

        // Use province-specific coordinates if available
        if (provinceSelect && provinceSelect.value) {
            const provinceName = provinceSelect.value.toLowerCase();
            if (papuaCoordinates[provinceName]) {
                coordinates = papuaCoordinates[provinceName];
                console.log('Using province coordinates:', provinceName, coordinates);
            }
        }

        wilayahCoordinates = coordinates;

        // Update all map instances
        Object.keys(mapInstances).forEach(questionId => {
            if (mapInstances[questionId]) {
                mapInstances[questionId].setView([coordinates.lat, coordinates.lng], 13);
                mapMarkers[questionId].setLatLng([coordinates.lat, coordinates.lng]);
                updateLocationInputs(questionId, coordinates.lat, coordinates.lng);
            }
        });
    }

    // Initialize maps after page load
    setTimeout(() => {
        initializeMaps();
    }, 1000);

    // Initialize Choices.js for searchable dropdowns after page load
    function initializeChoices() {
        // Initialize for wilayah dropdowns
        document.querySelectorAll('select[data-type="province"], select[data-type="regency"], select[data-type="district"], select[data-type="village"]').forEach(select => {
            if (!choicesInstances[select.id]) {
                choicesInstances[select.id] = new Choices(select, {
                    searchEnabled: true,
                    searchPlaceholderValue: 'Ketik untuk mencari...',
                    noResultsText: 'Tidak ada hasil',
                    itemSelectText: 'Tekan untuk memilih',
                    removeItemButton: false,
                    shouldSort: false
                });
            }
        });
    }

    // Initialize after DOM is ready
    setTimeout(initializeChoices, 500);

    // ==================== FILE UPLOAD FUNCTIONALITY ====================
    // Handle image uploads with preview and drag-drop
    document.querySelectorAll('.image-upload-container').forEach(container => {
        const questionId = container.dataset.questionId;
        const uploadArea = container.querySelector('.image-upload-area');
        const fileInput = container.querySelector('.image-file-input');
        const placeholder = container.querySelector('.image-placeholder');
        const preview = container.querySelector('.image-preview');
        const previewImg = preview.querySelector('img');
        const fileNameDisplay = container.querySelector(`#imageFileName_${questionId}`);
        const changeBtn = container.querySelector('.change-image-btn');

        // Click to upload
        uploadArea.addEventListener('click', (e) => {
            if (!e.target.closest('.change-image-btn')) {
                fileInput.click();
            }
        });

        // File input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleImageFile(file, questionId, placeholder, preview, previewImg, fileNameDisplay);
            }
        });

        // Change button
        if (changeBtn) {
            changeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                placeholder.classList.remove('hidden');
                preview.classList.add('hidden');
            });
        }

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('border-yellow-500', 'bg-yellow-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('border-yellow-500', 'bg-yellow-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('border-yellow-500', 'bg-yellow-50');

            const file = e.dataTransfer.files[0];
            if (file && file.type.startsWith('image/')) {
                // Create a new FileList-like object
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                handleImageFile(file, questionId, placeholder, preview, previewImg, fileNameDisplay);
            } else {
                showNotification('...�� File harus berupa gambar (JPG/PNG)', 'error');
            }
        });
    });

    // Handle file uploads (generic files including PDF) with preview and drag-drop
    document.querySelectorAll('.file-upload-container').forEach(container => {
        const questionId = container.dataset.questionId;
        const uploadArea = container.querySelector('.file-upload-area');
        const fileInput = container.querySelector('.file-file-input');
        const placeholder = container.querySelector('.file-placeholder');
        const preview = container.querySelector('.file-preview');
        const previewContent = container.querySelector(`#filePreviewContent_${questionId}`);
        const fileNameDisplay = container.querySelector(`#fileFileName_${questionId}`);
        const changeBtn = container.querySelector('.change-file-btn');

        // Click to upload
        uploadArea.addEventListener('click', (e) => {
            if (!e.target.closest('.change-file-btn')) {
                fileInput.click();
            }
        });

        // File input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleGenericFile(file, questionId, placeholder, preview, previewContent, fileNameDisplay);
            }
        });

        // Change button
        if (changeBtn) {
            changeBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                placeholder.classList.remove('hidden');
                preview.classList.add('hidden');
            });
        }

        // Drag and drop
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.add('border-yellow-500', 'bg-yellow-50');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('border-yellow-500', 'bg-yellow-50');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            uploadArea.classList.remove('border-yellow-500', 'bg-yellow-50');

            const file = e.dataTransfer.files[0];
            if (file) {
                // Create a new FileList-like object
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                fileInput.files = dataTransfer.files;

                handleGenericFile(file, questionId, placeholder, preview, previewContent, fileNameDisplay);
            }
        });
    });

    // Handle image file with validation and preview
    function handleImageFile(file, questionId, placeholder, preview, previewImg, fileNameDisplay) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            showNotification('...�� File harus berupa gambar (JPG, PNG, GIF, dll)', 'error');
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2048000) {
            showNotification('...�� Ukuran file terlalu besar. Maksimal 2MB', 'error');
            document.getElementById(`image_${questionId}`).value = '';
            return;
        }

        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            fileNameDisplay.textContent = `📷 ${file.name} (${formatFileSize(file.size)})`;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');

            // Mark as answered
            answeredQuestions.add(questionId.toString());


            // Auto-save file upload
            uploadFileToServer(file, questionId, 'image');

            showNotification('✅ Gambar berhasil diupload', 'success');
        };
        reader.readAsDataURL(file);
    }

    // Handle generic file (including PDF) with validation and preview
    function handleGenericFile(file, questionId, placeholder, preview, previewContent, fileNameDisplay) {
        // Validate file size (5MB for generic files)
        if (file.size > 5242880) {
            showNotification('...�� Ukuran file terlalu besar. Maksimal 5MB', 'error');
            document.getElementById(`file_${questionId}`).value = '';
            return;
        }

        // Show preview based on file type
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContent.innerHTML = `<img src="${e.target.result}" alt="Preview" class="max-h-64 mx-auto rounded-lg">`;
                fileNameDisplay.textContent = `🖼️ ${file.name} (${formatFileSize(file.size)})`;
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');

                // Mark as answered
                answeredQuestions.add(questionId.toString());


                // Auto-save file upload
                uploadFileToServer(file, questionId, 'file');

                showNotification('� File berhasil diupload', 'success');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            previewContent.innerHTML = `
                <div class="text-center">
                    <div class="text-6xl mb-2">📄</div>
                    <p class="text-gray-600 font-medium">PDF Document</p>
                </div>
            `;
            fileNameDisplay.textContent = `📄 ${file.name} (${formatFileSize(file.size)})`;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');

            // Mark as answered
            answeredQuestions.add(questionId.toString());


            // Auto-save file upload
            uploadFileToServer(file, questionId, 'file');

            showNotification('✅ File berhasil diupload', 'success');
        } else {
            previewContent.innerHTML = `
                <div class="text-center">
                    <div class="text-6xl mb-2">📎</div>
                    <p class="text-gray-600 font-medium">File Uploaded</p>
                </div>
            `;
            fileNameDisplay.textContent = `📎 ${file.name} (${formatFileSize(file.size)})`;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');

            // Mark as answered
            answeredQuestions.add(questionId.toString());


            // Auto-save file upload
            uploadFileToServer(file, questionId, 'file');

            showNotification('✅ File berhasil diupload', 'success');
        }
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }

    // Upload file to server
    function uploadFileToServer(file, questionId, fileType) {
        console.log('uploadFileToServer called:', { file: file.name, questionId, fileType });

        const formData = new FormData();
        formData.append('question_id', questionId);
        formData.append('file', file);
        formData.append('file_type', fileType);

        console.log('Sending to:', '{{ route("questionnaire.autosave", $questionnaire->id) }}');
        console.log('FormData entries:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ':', pair[1]);
        }

        showNotification('⏳ Menyimpan file ke server...', 'info');

        fetch('{{ route("questionnaire.autosave", $questionnaire->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                console.log('File uploaded successfully:', questionId);
                showNotification('✅ File berhasil disimpan ke database', 'success');
            } else {
                console.error('File upload failed:', data.message);
                showNotification('⚠️ Gagal menyimpan file: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('File upload error:', error);
            showNotification('⚠️ Error: ' + error.message, 'error');
        });
    }

    // Show notification
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 text-white font-medium transition-opacity duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            'bg-blue-500'
        }`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // =====================================
    // SECTION VI: KUESIONER TAMBAHAN (Pertanyaan Per KELUARGA)
    // =====================================
    let healthQuestionsData = [];
    let savedSectionVIAnswers = @json($savedSectionVIData ?? []);

    console.log('🔵 Loaded savedSectionVIData:', savedSectionVIAnswers);
    console.log('🔵 Response ID:', responseId);

    // Load health questions from API
    async function loadHealthQuestions() {
        try {
            const response = await fetch('/api/health-questions');
            healthQuestionsData = await response.json();
            console.log('Health questions loaded:', healthQuestionsData);
            return healthQuestionsData;
        } catch (error) {
            console.error('Error loading health questions:', error);
            return [];
        }
    }

    // Check if question should show based on conditions (for per-family questions)
    // Check if question should show based on conditions (for per-family questions)
    function questionShouldShowFamily(question, answers) {
        const conditions = question.show_conditions || {};

        // Check depends_on (conditional based on previous answer)
        if (conditions.depends_on) {
            const dependentAnswer = answers[conditions.depends_on];
            const shouldShow = dependentAnswer && String(dependentAnswer) === String(conditions.depends_value);

            if (!shouldShow) {
                console.log(`🔒 Hiding ${question.code} - depends on ${conditions.depends_on}=${conditions.depends_value}, got: ${dependentAnswer}`);
            } else {
                console.log(`🔓 Showing ${question.code} - condition met`);
            }

            return shouldShow;
        }

        return true;
    }

    // Render Section VI for the entire family
    function renderSectionVI() {
        const container = document.getElementById('section-vi-questions');
        const loading = document.getElementById('section-vi-loading');

        if (!container) return;

        let html = '';
        let questionNumber = 0;

        // Iterate through categories
        healthQuestionsData.forEach(category => {
            const categoryColors = {
                'ptm': { bg: 'gray-50', border: 'gray-200', text: 'gray-700', accent: 'gray-500' },
                'ibu_hamil': { bg: 'pink-50', border: 'pink-200', text: 'pink-700', accent: 'pink-500' },
                'ibu_melahirkan': { bg: 'purple-50', border: 'purple-200', text: 'purple-700', accent: 'purple-500' },
                'bayi': { bg: 'green-50', border: 'green-200', text: 'green-700', accent: 'green-500' }
            };
            const colors = categoryColors[category.code] || categoryColors['ptm'];

            html += `
                <div class="bg-${colors.bg} rounded-lg border border-${colors.border} overflow-hidden">
                    <div class="bg-${colors.accent} px-4 py-3 text-white">
                        <h4 class="font-semibold flex items-center gap-2">
                            📋 ${category.name}
                        </h4>
                        ${category.description ? `<p class="text-sm opacity-90 mt-1">${category.description}</p>` : ''}
                    </div>
                    <div class="p-4 space-y-4">
            `;

            category.questions.forEach(question => {
                questionNumber++;
                const savedAnswer = savedSectionVIAnswers[question.code] || '';
                const shouldShow = questionShouldShowFamily(question, savedSectionVIAnswers);
                const displayStyle = shouldShow ? '' : 'display: none;';

                html += renderHealthQuestionFamily(question, questionNumber, savedAnswer, displayStyle);
            });

            html += `
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        container.classList.remove('hidden');
        loading.classList.add('hidden');

        // Setup event listeners
        setupSectionVIListeners();
    }

    // Render a single health question for family
    function renderHealthQuestionFamily(question, number, savedAnswer, displayStyle) {
        const inputName = `health_vi[${question.code}]`;
        let html = `<div class="question-item bg-white rounded-lg p-4 border" data-question-code="${question.code}" style="${displayStyle}">`;

        // Debug log
        if (savedAnswer) {
            console.log(`🔄 Rendering ${question.code} with saved answer:`, savedAnswer);
        }

        // Question text
        html += `<label class="block text-sm font-medium text-gray-700 mb-2">${number}. ${question.question_text}</label>`;

        // Note if exists
        if (question.question_note) {
            html += `<p class="text-xs text-gray-500 mb-2 italic">${question.question_note}</p>`;
        }

        // Render based on input type
        switch (question.input_type) {
            case 'radio':
                html += `<div class="flex flex-wrap gap-4">`;
                question.options.forEach(opt => {
                    // Convert both to string for comparison
                    const checked = String(savedAnswer) === String(opt.value) ? 'checked' : '';
                    if (checked) {
                        console.log(`  ✓ Checking radio option ${opt.value} for ${question.code}`);
                    }
                    html += `
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                            <input type="radio" name="${inputName}" value="${opt.value}"
                                   class="w-4 h-4 text-purple-500 health-vi-input"
                                   data-question-code="${question.code}"
                                   ${checked}>
                            <span class="text-sm">${opt.value}. ${opt.label}</span>
                        </label>
                    `;
                });
                html += `</div>`;
                break;

            case 'checkbox':
                const savedValues = savedAnswer ? (Array.isArray(savedAnswer) ? savedAnswer : savedAnswer.split(',')) : [];
                html += `<div class="grid grid-cols-1 md:grid-cols-2 gap-2">`;
                question.options.forEach(opt => {
                    const checked = savedValues.includes(String(opt.value)) || savedValues.includes(opt.value) ? 'checked' : '';
                    html += `
                        <label class="flex items-center gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                            <input type="checkbox" name="${inputName}[]" value="${opt.value}"
                                   class="w-4 h-4 text-purple-500 health-vi-input"
                                   data-question-code="${question.code}"
                                   ${checked}>
                            <span class="text-sm">${opt.label}</span>
                        </label>
                    `;
                });
                html += `</div>`;
                break;

            case 'number':
                const unit = question.settings?.unit || '';
                html += `
                    <div class="flex items-center gap-2">
                        <input type="number" name="${inputName}" value="${savedAnswer || ''}"
                               class="w-32 px-3 py-2 text-sm border rounded-lg health-vi-input focus:ring-2 focus:ring-purple-300"
                               data-question-code="${question.code}"
                               placeholder="0"
                               ${question.validation_rules?.min ? `min="${question.validation_rules.min}"` : ''}
                               ${question.validation_rules?.max ? `max="${question.validation_rules.max}"` : ''}
                               ${question.validation_rules?.decimal ? `step="0.${'0'.repeat(question.validation_rules.decimal - 1)}1"` : ''}>
                        ${unit ? `<span class="text-sm text-gray-500">${unit}</span>` : ''}
                    </div>
                `;
                break;

            case 'text':
                html += `
                    <input type="text" name="${inputName}" value="${savedAnswer || ''}"
                           class="w-full px-3 py-2 text-sm border rounded-lg health-vi-input focus:ring-2 focus:ring-purple-300"
                           data-question-code="${question.code}">
                `;
                break;

            case 'textarea':
                html += `
                    <textarea name="${inputName}"
                              class="w-full px-3 py-2 text-sm border rounded-lg health-vi-input focus:ring-2 focus:ring-purple-300"
                              data-question-code="${question.code}"
                              rows="3">${savedAnswer || ''}</textarea>
                `;
                break;

            case 'table':
                html += renderTableQuestionFamily(question, savedAnswer);
                break;

            case 'table_radio':
                html += renderTableRadioQuestionFamily(question, savedAnswer);
                break;

            case 'table_checkbox':
                html += renderTableCheckboxQuestionFamily(question, savedAnswer);
                break;

            case 'info':
                // Just display info text
                html += `<div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800">${question.question_note || ''}</div>`;
                break;

            case 'calculated':
                // Calculated field with SRQ score
                html += `<div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                    <span class="text-sm font-medium">Skor Total:</span>
                    <span id="score-${question.code}" class="text-lg font-bold text-yellow-700 ml-2">-</span>
                </div>`;
                break;
        }

        html += `</div>`;
        return html;
    }

    // Render table question for family
    function renderTableQuestionFamily(question, savedAnswer) {
        const savedValues = typeof savedAnswer === 'object' ? savedAnswer : {};

        let html = `<div class="overflow-x-auto"><table class="w-full text-sm border-collapse">`;
        html += `<thead><tr class="bg-gray-100">
            <th class="border px-3 py-2 text-left">Jenis Pemeriksaan</th>
            <th class="border px-3 py-2 text-center">Hasil</th>
            <th class="border px-3 py-2 text-left">Nilai Normal</th>
        </tr></thead><tbody>`;

        question.table_rows.forEach(row => {
            const inputName = `health_vi[${question.code}][${row.row_code}]`;
            const savedRowValue = savedValues[row.row_code] || '';
            html += `
                <tr>
                    <td class="border px-3 py-2">${row.row_label}</td>
                    <td class="border px-3 py-2">
                        <input type="number" name="${inputName}" value="${savedRowValue}"
                               class="w-24 px-2 py-1 text-sm border rounded health-vi-input"
                               data-question-code="${question.code}"
                               data-row-code="${row.row_code}"
                               placeholder="0">
                        ${row.unit ? `<span class="text-xs text-gray-500 ml-1">${row.unit}</span>` : ''}
                    </td>
                    <td class="border px-3 py-2 text-xs text-gray-500">${row.reference_value || '-'}</td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        return html;
    }

    // Calculate total AKS score helper
    function calculateAKSTotalScore(savedValues) {
        let total = 0;
        for (let i = 1; i <= 10; i++) {
            const value = savedValues['aks_' + i];
            if (value) {
                total += parseInt(value) || 0;
            }
        }
        return total;
    }

    // Get status kemandirian based on total score helper
    function getStatusKemandirian(totalScore) {
        if (totalScore === 20) return 'Mandiri (A) - 20';
        if (totalScore >= 12 && totalScore <= 19) return 'Ketergantungan ringan (B) - 12 – 19';
        if (totalScore >= 9 && totalScore <= 11) return 'Ketergantungan sedang (C) - 9 – 11';
        if (totalScore >= 5 && totalScore <= 8) return 'Ketergantungan berat (D) - 5 – 8';
        if (totalScore >= 0 && totalScore <= 4) return 'Ketergantungan total (E) - 0 – 4';
        return 'Belum lengkap';
    }

    // Render table_radio question for family
    function renderTableRadioQuestionFamily(question, savedAnswer) {
        const savedValues = typeof savedAnswer === 'object' ? savedAnswer : {};

        // Check if this is a measurement table (has numeric input) or just radio selection
        const hasMeasurement = question.code === 'H3'; // Hasil pemeriksaan darah

        let html = `<div class="overflow-x-auto"><table class="w-full text-sm border-collapse">`;

        if (hasMeasurement) {
            // Table for pemeriksaan darah with input field
            html += `<thead><tr class="bg-gray-100">
                <th class="border px-3 py-2 text-center w-12">No.</th>
                <th class="border px-3 py-2 text-left">Jenis Pemeriksaan</th>
                <th class="border px-3 py-2 text-center">Hasil Pemeriksaan</th>
                <th class="border px-3 py-2 text-left">Nilai Rujukan</th>
                <th class="border px-3 py-2 text-center">Pilih</th>
            </tr></thead><tbody>`;

            question.table_rows.forEach(row => {
                const inputNameValue = `health_vi[${question.code}][${row.row_code}_value]`;
                const inputNameChoice = `health_vi[${question.code}][${row.row_code}]`;
                const savedValue = savedValues[row.row_code + '_value'] || '';
                const savedChoice = savedValues[row.row_code] || '';

                html += `<tr>
                    <td class="border px-3 py-2 text-center">${row.order}</td>
                    <td class="border px-3 py-2">${row.row_label}</td>
                    <td class="border px-3 py-2 text-center">
                        <input type="number" name="${inputNameValue}" value="${savedValue}"
                               class="w-32 px-2 py-1 text-sm border rounded health-vi-input"
                               data-question-code="${question.code}"
                               data-row-code="${row.row_code}_value"
                               placeholder="0" step="0.1">
                        <span class="text-xs text-gray-500 ml-1">mg/dl</span>
                    </td>
                    <td class="border px-3 py-2 text-xs text-gray-600">${row.reference_value || row.unit || ''}</td>
                    <td class="border px-3 py-2">
                        <div class="flex flex-col gap-1">`;

                // Radio buttons for each option
                question.options.forEach(opt => {
                    const checked = String(savedChoice) === String(opt.value) ? 'checked' : '';
                    html += `
                        <label class="flex items-center gap-1 cursor-pointer hover:bg-gray-50 px-1 py-0.5 rounded">
                            <input type="radio" name="${inputNameChoice}" value="${opt.value}"
                                   class="w-3 h-3 text-purple-500 health-vi-input"
                                   data-question-code="${question.code}"
                                   data-row-code="${row.row_code}"
                                   ${checked}>
                            <span class="text-xs">${opt.label}</span>
                        </label>
                    `;
                });

                html += `    </div>
                    </td>
                </tr>`;
            });
        } else {
            // Simple table with just radio options (e.g., AKS)
            html += `<thead><tr class="bg-gray-100">
                <th class="border px-3 py-2 text-left">FUNGSI</th>
                <th class="border px-3 py-2 text-center">SKOR</th>
                <th class="border px-3 py-2 text-left">KETERANGAN</th>
                <th class="border px-3 py-2 text-center">HASIL</th>
            </tr></thead><tbody>`;

            question.table_rows.forEach((row, idx) => {
                const inputName = `health_vi[${question.code}][${row.row_code}]`;
                const savedValue = savedValues[row.row_code] || '';

                // Parse note to show KETERANGAN details and count how many scores exist
                const keteranganParts = row.note ? row.note.split('|') : [];
                const keterangan = keteranganParts.map((text, i) => {
                    return `<div class="mb-1"><strong>${i}:</strong> ${text.trim()}</div>`;
                }).join('');

                // Only show options up to the number of keterangan entries
                const maxScore = keteranganParts.length;
                const availableOptions = question.options.filter((opt, i) => i < maxScore);
                const skorLabels = availableOptions.map(o => o.label).join(', ');

                html += `<tr>
                    <td class="border px-3 py-2 align-top">${idx + 1}. ${row.row_label}</td>
                    <td class="border px-3 py-2 text-center text-xs text-gray-600 align-top">${skorLabels}</td>
                    <td class="border px-3 py-2 text-xs text-gray-600 align-top">${keterangan}</td>
                    <td class="border px-3 py-2 align-top">
                        <div class="flex gap-2 justify-center flex-wrap">`;

                // Radio buttons only for available options based on keterangan count
                availableOptions.forEach(opt => {
                    const checked = String(savedValue) === String(opt.value) ? 'checked' : '';
                    html += `
                        <label class="flex items-center gap-1 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                            <input type="radio" name="${inputName}" value="${opt.value}"
                                   class="w-4 h-4 text-purple-500 health-vi-input aks-score-radio"
                                   data-question-code="${question.code}"
                                   data-row-code="${row.row_code}"
                                   ${checked}>
                            <span class="text-sm font-medium">${opt.label}</span>
                        </label>
                    `;
                });

                html += `    </div>
                    </td>
                </tr>`;
            });

            // Add total score and status kemandirian row for H4 (AKS)
            if (question.code === 'H4') {
                const totalScore = calculateAKSTotalScore(savedValues);
                const status = getStatusKemandirian(totalScore);

                html += `
                <tr class="bg-blue-50 font-bold">
                    <td colspan="3" class="border px-3 py-2 text-right">TOTAL SKOR:</td>
                    <td class="border px-3 py-2 text-center">
                        <span id="aks-total-score" class="text-lg text-blue-600">${totalScore}</span>
                    </td>
                </tr>
                <tr class="bg-green-50 font-bold">
                    <td colspan="3" class="border px-3 py-2 text-right">STATUS KEMANDIRIAN LANSIA:</td>
                    <td class="border px-3 py-2 text-center">
                        <span id="aks-status" class="text-lg text-green-600">${status}</span>
                        <input type="hidden" name="health_vi[H5][status]" id="aks-status-input" value="${status}">
                    </td>
                </tr>`;
            }
        }

        html += `</tbody></table></div>`;
        return html;
    }

    // Render table_checkbox question for family (SKILAS)
    function renderTableCheckboxQuestionFamily(question, savedAnswer) {
        const savedValues = typeof savedAnswer === 'object' ? savedAnswer : {};

        let html = `<div class="overflow-x-auto"><table class="w-full text-sm border-collapse">`;
        html += `<thead><tr class="bg-gray-100">
            <th class="border px-3 py-2 text-left">Kondisi prioritas terkait penurunan kapasitas intrinsik</th>
            <th class="border px-3 py-2 text-left">Pertanyaan</th>
            <th class="border px-3 py-2 text-center">Hasil (berikan tanda centang sesuai hasil pemeriksaan)</th>
        </tr></thead><tbody>`;

        question.table_rows.forEach(row => {
            const inputName = `health_vi[${question.code}][${row.row_code}]`;
            const savedValue = savedValues[row.row_code] || '';

            let resultCell = '';

            // Check row type
            if (row.input_type === 'text') {
                // Header row - no input, just display text
                resultCell = '';
            } else if (row.reference_value && row.reference_value.includes('|')) {
                // Radio options from reference_value
                const options = row.reference_value.split('|');
                resultCell = '<div class="flex flex-col gap-2">';
                options.forEach((opt, idx) => {
                    const optValue = opt.trim();
                    const checked = String(savedValue) === String(optValue) ? 'checked' : '';
                    resultCell += `
                        <label class="flex items-start gap-2 cursor-pointer hover:bg-gray-50 px-2 py-1 rounded">
                            <input type="radio" name="${inputName}" value="${optValue}"
                                   class="mt-1 w-4 h-4 text-purple-500 health-vi-input"
                                   data-question-code="${question.code}"
                                   data-row-code="${row.row_code}"
                                   ${checked}>
                            <span class="text-xs flex-1">${optValue}</span>
                        </label>
                    `;
                });
                resultCell += '</div>';
            } else if (row.reference_value) {
                // Checkbox with label from reference_value
                const checked = savedValue === 'ya' ? 'checked' : '';
                resultCell = `
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="${inputName}" value="ya"
                               class="w-4 h-4 text-purple-500 health-vi-input"
                               data-question-code="${question.code}"
                               data-row-code="${row.row_code}"
                               ${checked}>
                        <span class="text-xs">${row.reference_value}</span>
                    </div>
                `;
            } else {
                // Simple checkbox without label
                const checked = savedValue === 'ya' ? 'checked' : '';
                resultCell = `
                    <input type="checkbox" name="${inputName}" value="ya"
                           class="w-4 h-4 text-purple-500 health-vi-input"
                           data-question-code="${question.code}"
                           data-row-code="${row.row_code}"
                           ${checked}>
                `;
            }

            html += `
                <tr>
                    <td class="border px-3 py-2 align-top">${row.row_label}</td>
                    <td class="border px-3 py-2 text-xs text-gray-600 align-top">${row.note || ''}</td>
                    <td class="border px-3 py-2 text-center align-top">${resultCell}</td>
                </tr>
            `;
        });

        html += `</tbody></table></div>`;
        return html;
    }

    // Setup event listeners for Section VI
    function setupSectionVIListeners() {
        const container = document.getElementById('section-vi-questions');
        if (!container) {
            console.warn('⚠️ section-vi-questions container not found for listeners');
            return;
        }

        const inputs = container.querySelectorAll('.health-vi-input');
        console.log(`🎯 Setting up listeners for ${inputs.length} Section VI inputs`);

        inputs.forEach(input => {
            const eventType = input.type === 'radio' || input.type === 'checkbox' ? 'change' : 'input';

            // Update saved answers and check conditionals
            input.addEventListener('change', function() {
                const questionCode = this.dataset.questionCode;
                const value = this.type === 'checkbox'
                    ? Array.from(container.querySelectorAll(`input[data-question-code="${questionCode}"]:checked`)).map(cb => cb.value)
                    : this.value;

                console.log(`📝 Answer changed for ${questionCode}:`, value);
                savedSectionVIAnswers[questionCode] = value;
                updateConditionalQuestionsFamily();

                // Calculate AKS total if it's an AKS question
                if (this.classList.contains('aks-score-radio')) {
                    calculateAndUpdateAKSTotal();
                }

                // Calculate SRQ score if applicable
                calculateSRQScore();
            });

            // Auto-save on change
            input.addEventListener(eventType, debounce(function() {
                console.log('💾 Triggering auto-save...');
                autoSaveSectionVIDataFamily();
            }, 500));
        });

        console.log('✅ Section VI listeners setup complete');
    }

    // Update visibility of conditional questions for family
    function updateConditionalQuestionsFamily() {
        const container = document.getElementById('section-vi-questions');
        if (!container) return;

        healthQuestionsData.forEach(category => {
            category.questions.forEach(question => {
                if (question.show_conditions?.depends_on) {
                    const questionDiv = container.querySelector(`[data-question-code="${question.code}"]`);
                    if (questionDiv) {
                        const shouldShow = questionShouldShowFamily(question, savedSectionVIAnswers);
                        questionDiv.style.display = shouldShow ? 'block' : 'none';
                    }
                }
            });
        });
    }

    // Calculate and update AKS total score and status
    function calculateAndUpdateAKSTotal() {
        let totalScore = 0;

        // Sum all AKS scores (aks_1 to aks_10)
        for (let i = 1; i <= 10; i++) {
            const aksCode = `aks_${i}`;
            const checkedRadio = document.querySelector(`input[data-row-code="${aksCode}"]:checked`);
            if (checkedRadio) {
                totalScore += parseInt(checkedRadio.value) || 0;
            }
        }

        // Update total score display
        const totalDisplay = document.getElementById('aks-total-score');
        if (totalDisplay) {
            totalDisplay.textContent = totalScore;
        }

        // Determine status
        let status = 'Belum lengkap';
        if (totalScore === 20) status = 'Mandiri (A) - 20';
        else if (totalScore >= 12 && totalScore <= 19) status = 'Ketergantungan ringan (B) - 12 – 19';
        else if (totalScore >= 9 && totalScore <= 11) status = 'Ketergantungan sedang (C) - 9 – 11';
        else if (totalScore >= 5 && totalScore <= 8) status = 'Ketergantungan berat (D) - 5 – 8';
        else if (totalScore >= 0 && totalScore <= 4) status = 'Ketergantungan total (E) - 0 – 4';

        // Update status display
        const statusDisplay = document.getElementById('aks-status');
        if (statusDisplay) {
            statusDisplay.textContent = status;
        }

        // Update hidden input for saving
        const statusInput = document.getElementById('aks-status-input');
        if (statusInput) {
            statusInput.value = status;
        }

        console.log(`🧮 AKS Total Score: ${totalScore} - Status: ${status}`);
    }

    // Calculate SRQ-20 score
    function calculateSRQScore() {
        const container = document.getElementById('section-vi-questions');
        if (!container) return;

        // Count all SRQ questions answered with '1' (Ya)
        let score = 0;
        const srqQuestions = container.querySelectorAll('[data-question-code^="srq_"]:not([data-question-code="srq_total"])');
        srqQuestions.forEach(div => {
            const checkedInput = div.querySelector('input[type="radio"]:checked');
            if (checkedInput && checkedInput.value === '1') {
                score++;
            }
        });

        // Update score display
        const scoreDisplay = container.querySelector('#score-srq_total');
        if (scoreDisplay) {
            scoreDisplay.textContent = score;
            // Change color based on score threshold (>= 6 is concerning)
            if (score >= 6) {
                scoreDisplay.className = 'text-lg font-bold text-red-600 ml-2';
            } else {
                scoreDisplay.className = 'text-lg font-bold text-green-600 ml-2';
            }
        }
    }

    // Auto-save Section VI data for family
    function autoSaveSectionVIDataFamily() {
        const container = document.getElementById('section-vi-questions');
        if (!container) {
            console.warn('⚠️ Container not found for auto-save');
            return;
        }

        // Collect all answers
        const healthData = {};

        container.querySelectorAll('.health-vi-input').forEach(input => {
            const code = input.dataset.questionCode;
            const rowCode = input.dataset.rowCode;

            if (input.type === 'radio' && input.checked) {
                healthData[code] = input.value;
            } else if (input.type === 'checkbox' && input.checked) {
                if (!healthData[code]) healthData[code] = [];
                healthData[code].push(input.value);
            } else if (input.type !== 'radio' && input.type !== 'checkbox') {
                if (rowCode) {
                    if (!healthData[code]) healthData[code] = {};
                    healthData[code][rowCode] = input.value;
                } else {
                    healthData[code] = input.value;
                }
            }
        });

        console.log('💾 Auto-saving Section VI data for family', healthData);
        console.log('💾 Response ID:', responseId);

        // Send to server
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('response_id', responseId);
        formData.append('section_vi_data', JSON.stringify(healthData));

        fetch('{{ route("questionnaire.save-health-vi", $questionnaire->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('💾 Save response:', data);
            if (data.success) {
                console.log('✅ Section VI data saved successfully');
                showSaveIndicator();
            } else {
                console.error('❌ Save failed:', data.message);
            }
        })
        .catch(error => {
            console.error('❌ Error saving Section VI:', error);
        });
    }

    // Show save indicator
    function showSaveIndicator() {
        const header = document.querySelector('#section-vi-container .text-white h3');
        if (header) {
            let indicator = header.querySelector('.save-indicator');
            if (!indicator) {
                indicator = document.createElement('span');
                indicator.className = 'save-indicator text-sm font-normal bg-green-400 text-white px-2 py-1 rounded ml-2';
            }
            indicator.textContent = '✓ Tersimpan';
            header.appendChild(indicator);
            setTimeout(() => indicator.remove(), 2000);
        }
    }

    // Initialize Section VI
    async function initSectionVI() {
        const container = document.getElementById('section-vi-container');
        const loading = document.getElementById('section-vi-loading');

        if (!container) return;

        // Load health questions
        await loadHealthQuestions();

        if (healthQuestionsData.length === 0) {
            loading.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-yellow-800">
                    <p>Tidak ada pertanyaan tambahan untuk kuesioner ini.</p>
                </div>
            `;
            return;
        }

        // Render Section VI questions for family
        renderSectionVI();
    }

    // Toggle Section V visibility
    window.toggleSectionV = function() {
        const content = document.getElementById('section-v-content');
        const arrow = document.getElementById('section-v-arrow');
        if (content.style.display === 'none') {
            content.style.display = 'block';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            content.style.display = 'none';
            arrow.style.transform = 'rotate(-90deg)';
        }
    };

    // Toggle Section VI visibility
    window.toggleSectionVI = function() {
        const content = document.getElementById('section-vi-content');
        const arrow = document.getElementById('section-vi-arrow');
        if (content.style.display === 'none') {
            content.style.display = 'block';
            arrow.style.transform = 'rotate(0deg)';
        } else {
            content.style.display = 'none';
            arrow.style.transform = 'rotate(-90deg)';
        }
    };

    // Initialize Section VI when page loads
    initSectionVI();
});
</script>
@endpush
@endsection
