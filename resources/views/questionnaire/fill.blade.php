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
        <!-- Mini Progress Bar -->
        <div class="mt-2">
            <div class="w-full bg-gray-200 rounded-full h-1.5">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 h-1.5 rounded-full transition-all duration-300"
                     id="stickyProgressBar" style="width: 0%"></div>
            </div>
            <div class="flex items-center justify-between mt-1">
                <span class="text-xs text-gray-500">
                    <span id="stickyAnsweredCount">0</span>/{{ $actualQuestions->count() }} terjawab
                </span>
                <span class="text-xs font-medium text-yellow-600" id="stickyProgressPercent">0%</span>
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

        <!-- Progress Bar -->
        <div class="bg-white rounded-xl shadow px-4 py-3 mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-600">Progres</span>
                <span class="text-sm font-medium text-yellow-600" id="progressPercent">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-400 h-2 rounded-full transition-all duration-300"
                     id="progressBar" style="width: 0%"></div>
            </div>
            <div class="text-xs text-gray-500 mt-1">
                <span id="answeredCount">0</span> dari <span id="totalQuestions">{{ $actualQuestions->count() }}</span> pertanyaan terjawab
            </div>
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
                                                        <div id="family-members-list" class="space-y-2"></div>
                                                        <button type="button" id="add-family-member-btn" onclick="addFamilyMember()" class="hidden w-full px-4 py-2 bg-yellow-500 text-black rounded-lg hover:bg-yellow-600 transition font-medium text-sm">
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
                                                    <div class="image-upload-container" data-question-id="{{ $question->id }}">
                                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-400 transition cursor-pointer image-upload-area" id="imageUploadArea_{{ $question->id }}">
                                                            <input type="file"
                                                                   name="file_answers[{{ $question->id }}]"
                                                                   accept="image/*"
                                                                   class="hidden image-file-input"
                                                                   id="image_{{ $question->id }}"
                                                                   data-question-id="{{ $question->id }}"
                                                                   {{ $question->is_required ? 'required' : '' }}>
                                                            <div class="image-placeholder" id="imagePlaceholder_{{ $question->id }}">
                                                                <div class="text-4xl mb-2">🖼️</div>
                                                                <p class="text-gray-600 font-medium">Klik untuk upload gambar</p>
                                                                <p class="text-gray-400 text-sm mt-1">atau drag & drop</p>
                                                                <p class="text-gray-400 text-xs mt-2">Format: JPG, PNG. Maksimal 2MB</p>
                                                            </div>
                                                            <div class="image-preview hidden" id="imagePreview_{{ $question->id }}">
                                                                <img src="" alt="Preview" class="max-h-64 mx-auto rounded-lg mb-3">
                                                                <p class="text-sm text-gray-600 mb-2" id="imageFileName_{{ $question->id }}"></p>
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
                                                    <div class="file-upload-container" data-question-id="{{ $question->id }}">
                                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-400 transition cursor-pointer file-upload-area" id="fileUploadArea_{{ $question->id }}">
                                                            <input type="file"
                                                                   name="file_answers[{{ $question->id }}]"
                                                                   id="file_{{ $question->id }}"
                                                                   class="hidden file-file-input"
                                                                   data-question-id="{{ $question->id }}"
                                                                   accept="{{ $question->settings['accept'] ?? 'image/*,.pdf' }}"
                                                                   {{ $question->is_required ? 'required' : '' }}>
                                                            <div class="file-placeholder" id="filePlaceholder_{{ $question->id }}">
                                                                <div class="text-4xl mb-2">📎</div>
                                                                <p class="text-gray-600 font-medium">Klik untuk upload file</p>
                                                                <p class="text-gray-400 text-sm mt-1">atau drag & drop</p>
                                                                <p class="text-gray-400 text-xs mt-2">Format: Gambar atau PDF. Maksimal 5MB</p>
                                                            </div>
                                                            <div class="file-preview hidden" id="filePreview_{{ $question->id }}">
                                                                <div class="preview-content mb-3" id="filePreviewContent_{{ $question->id }}">
                                                                    <!-- Preview will be inserted here -->
                                                                </div>
                                                                <p class="text-sm text-gray-600 mb-2" id="fileFileName_{{ $question->id }}"></p>
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

            <!-- Catatan Pencacah -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500 mt-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">📝 Catatan Pencacah (Opsional)</h3>
                <textarea name="officer_notes"
                          rows="4"
                          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Tuliskan catatan tambahan terkait kuesioner ini (misal: kondisi khusus, kendala saat pengisian, dll)"></textarea>
                <p class="text-xs text-gray-500 mt-2">💡 Catatan ini akan tersimpan untuk referensi internal</p>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const totalQuestions = {{ $actualQuestions->count() }};
    const responseId = {{ $response->id }};
    let answeredQuestions = new Set();
    let autoSaveTimeout = null;

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
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const questionCounter = document.getElementById('questionCounter');
    const answeredCount = document.getElementById('answeredCount');

    // Set initial counter
    questionCounter.textContent = '1-' + totalQuestions;
    document.getElementById('stickyQuestionCounter').textContent = '1-' + totalQuestions;

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
        document.querySelectorAll('.answer-input').forEach(input => {
            const questionId = input.getAttribute('data-question-id');

            if (input.type === 'radio' || input.type === 'checkbox') {
                if (input.checked) {
                    answeredQuestions.add(questionId);
                }
            } else if (input.value && input.value.trim() !== '') {
                answeredQuestions.add(questionId);
            }
        });
        updateProgress();
    }

    function updateProgress() {
        const progress = (answeredQuestions.size / totalQuestions) * 100;
        progressBar.style.width = progress + '%';
        progressPercent.textContent = Math.round(progress) + '%';
        answeredCount.textContent = answeredQuestions.size;

        // Update sticky header
        document.getElementById('stickyProgressBar').style.width = progress + '%';
        document.getElementById('stickyProgressPercent').textContent = Math.round(progress) + '%';
        document.getElementById('stickyAnsweredCount').textContent = answeredQuestions.size;

        // Update submit button if 100%
        if (progress >= 100) {
            submitBtn.classList.add('animate-pulse');
        } else {
            submitBtn.classList.remove('animate-pulse');
        }
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
                    console.log('Auto-saved:', questionId);
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
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
                if (this.checked) {
                    answeredQuestions.add(questionId);

                    // For radio, get the value
                    if (this.type === 'radio') {
                        autoSave(questionId, this.value, true); // Save immediately
                    } else {
                        // For checkbox, collect all checked values
                        const checkedValues = Array.from(document.querySelectorAll(`input[data-question-id="${questionId}"]:checked`))
                            .map(cb => cb.value);
                        autoSave(questionId, JSON.stringify(checkedValues), true); // Save immediately
                    }
                } else if (this.type === 'checkbox') {
                    // Check if any checkbox for this question is still checked
                    const anyChecked = document.querySelector(`input[data-question-id="${questionId}"]:checked`);
                    if (!anyChecked) {
                        answeredQuestions.delete(questionId);
                    }

                    const checkedValues = Array.from(document.querySelectorAll(`input[data-question-id="${questionId}"]:checked`))
                        .map(cb => cb.value);
                    autoSave(questionId, JSON.stringify(checkedValues), true); // Save immediately
                }
                updateProgress();
            });
        } else {
            // For text inputs and textareas
            input.addEventListener('input', function() {
                if (this.value && this.value.trim() !== '') {
                    answeredQuestions.add(questionId);
                    autoSave(questionId, this.value, false); // Delay for text inputs
                } else {
                    answeredQuestions.delete(questionId);
                }
                updateProgress();
            });
        }
    });

    // Auto-save for select/dropdown (wilayah, etc)
    document.querySelectorAll('select.answer-input').forEach(select => {
        const questionId = select.getAttribute('data-question-id');
        if (!questionId) return;

        select.addEventListener('change', function() {
            if (this.value && this.value.trim() !== '') {
                answeredQuestions.add(questionId);
                autoSave(questionId, this.value, true); // Save immediately on change
            } else {
                answeredQuestions.delete(questionId);
            }
            updateProgress();
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

    window.addFamilyMember = function() {
        familyMemberCount++;
        const container = document.getElementById('family-members-list');
        const memberDiv = document.createElement('div');
        memberDiv.className = 'border border-gray-300 rounded-lg p-4 space-y-3 bg-white';
        memberDiv.id = `member-${familyMemberCount}`;

        // Build options from master data with code.name format
        let familyRelationOptions = '<option value="">Hubungan Keluarga</option>';
        familyRelationsData.forEach(item => {
            familyRelationOptions += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        let religionOptions = '<option value="">Agama</option>';
        religionsData.forEach(item => {
            religionOptions += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        let maritalStatusOptions = '<option value="">Status Perkawinan</option>';
        maritalStatusesData.forEach(item => {
            maritalStatusOptions += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        let educationOptions = '<option value="">Pendidikan</option>';
        educationsData.forEach(item => {
            educationOptions += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        let occupationOptions = '<option value="">Pekerjaan</option>';
        occupationsData.forEach(item => {
            occupationOptions += `<option value="${item.id}">${formatOptionText(item)}</option>`;
        });

        memberDiv.innerHTML = `
            <div class="flex justify-between items-center mb-2">
                <h4 class="font-semibold text-gray-800 text-sm">Anggota ${familyMemberCount}</h4>
            </div>

            <input type="text" name="family_members[${familyMemberCount}][nik]" placeholder="NIK (16 digit - opsional)" class="uppercase w-full px-3 py-2 text-sm border rounded-lg" maxlength="16" pattern="[0-9]{16}">

            <input type="text" name="family_members[${familyMemberCount}][nama_lengkap]" placeholder="Nama Lengkap" class="uppercase w-full px-3 py-2 text-sm border rounded-lg" required>

            <select name="family_members[${familyMemberCount}][hubungan]" id="hubungan-${familyMemberCount}" class="w-full px-3 py-2 text-sm border rounded-lg" required>
                ${familyRelationOptions}
            </select>

            <input type="text" name="family_members[${familyMemberCount}][tempat_lahir]" placeholder="Tempat Lahir (opsional)" class="uppercase w-full px-3 py-2 text-sm border rounded-lg">

            <div class="flex gap-2">
                <input type="text" name="family_members[${familyMemberCount}][tanggal_lahir]" id="tanggal_lahir_${familyMemberCount}" placeholder="dd/mm/yyyy" class="datepicker-dob flex-1 px-3 py-2 text-sm border rounded-lg" required data-member-id="${familyMemberCount}">
                <input type="number" id="age-${familyMemberCount}" placeholder="Umur" class="w-20 px-3 py-2 text-sm border rounded-lg bg-gray-50" readonly>
            </div>

            <select name="family_members[${familyMemberCount}][jenis_kelamin]" class="w-full px-3 py-2 text-sm border rounded-lg" required>
                <option value="">Jenis Kelamin</option>
                <option value="1">1. Pria</option>
                <option value="2">2. Wanita</option>
            </select>

            <select name="family_members[${familyMemberCount}][status_perkawinan]" id="status_perkawinan-${familyMemberCount}" class="w-full px-3 py-2 text-sm border rounded-lg">
                ${maritalStatusOptions}
            </select>

            <select name="family_members[${familyMemberCount}][agama]" id="agama-${familyMemberCount}" class="w-full px-3 py-2 text-sm border rounded-lg">
                ${religionOptions}
            </select>

            <select name="family_members[${familyMemberCount}][pendidikan]" id="pendidikan-${familyMemberCount}" class="w-full px-3 py-2 text-sm border rounded-lg">
                ${educationOptions}
            </select>

            <select name="family_members[${familyMemberCount}][pekerjaan]" id="pekerjaan-${familyMemberCount}" class="w-full px-3 py-2 text-sm border rounded-lg">
                ${occupationOptions}
            </select>

            <select name="family_members[${familyMemberCount}][golongan_darah]" class="w-full px-3 py-2 text-sm border rounded-lg">
                <option value="">Golongan Darah</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
                <option value="-">Tidak Diketahui</option>
            </select>

            <input type="text" name="family_members[${familyMemberCount}][phone]" placeholder="No. Telepon" class="w-full px-3 py-2 text-sm border rounded-lg">

            <div class="space-y-1">
                <label class="block text-xs font-medium text-gray-600">Upload KTP/KIA (opsional)</label>
                <div class="family-ktp-upload-container" data-member-id="${familyMemberCount}">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-yellow-400 transition cursor-pointer family-ktp-upload-area" id="ktpUploadArea_${familyMemberCount}">
                        <input type="file"
                               name="family_members[${familyMemberCount}][ktp_kia]"
                               accept="image/*,.pdf"
                               class="hidden family-ktp-file-input"
                               id="family_ktp_${familyMemberCount}"
                               data-member-id="${familyMemberCount}">
                        <div class="family-ktp-placeholder" id="ktpPlaceholder_${familyMemberCount}">
                            <div class="text-3xl mb-1">📸</div>
                            <p class="text-gray-600 text-xs font-medium">Klik untuk upload</p>
                            <p class="text-gray-400 text-xs mt-1">atau drag & drop</p>
                            <p class="text-gray-400 text-xs mt-1">JPG, PNG, PDF (Max 2MB)</p>
                        </div>
                        <div class="family-ktp-preview hidden" id="ktpPreview_${familyMemberCount}">
                            <div id="ktpPreviewContent_${familyMemberCount}" class="mb-2">
                                <!-- Preview will be inserted here -->
                            </div>
                            <p class="text-xs text-gray-600 mb-2" id="ktpFileName_${familyMemberCount}"></p>
                            <button type="button" class="change-family-ktp-btn text-yellow-600 text-xs font-medium hover:text-yellow-700" data-member-id="${familyMemberCount}">
                                ⟳ Ganti File
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 mt-4 pt-3 border-t">
                <button type="button" onclick="saveFamilyMember(${familyMemberCount})" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
                    ✓ Simpan Anggota
                </button>
                <button type="button" onclick="removeFamilyMember(${familyMemberCount})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">
                    × Hapus
                </button>
            </div>
        `;
        container.appendChild(memberDiv);

        // Initialize Choices.js for all searchable dropdowns
        const selectsToInitialize = [
            { id: `hubungan-${familyMemberCount}`, placeholder: 'Ketik untuk mencari hubungan...' },
            { id: `agama-${familyMemberCount}`, placeholder: 'Ketik untuk mencari agama...' },
            { id: `status_perkawinan-${familyMemberCount}`, placeholder: 'Ketik untuk mencari status...' },
            { id: `pendidikan-${familyMemberCount}`, placeholder: 'Ketik untuk mencari pendidikan...' },
            { id: `pekerjaan-${familyMemberCount}`, placeholder: 'Ketik untuk mencari pekerjaan...' }
        ];

        selectsToInitialize.forEach(selectConfig => {
            const selectElement = document.getElementById(selectConfig.id);
            if (selectElement) {
                new Choices(selectElement, {
                    searchEnabled: true,
                    searchPlaceholderValue: selectConfig.placeholder,
                    noResultsText: 'Tidak ada hasil',
                    itemSelectText: 'Tekan untuk memilih',
                    removeItemButton: false,
                    shouldSort: false
                });
            }
        });

        // Initialize file upload for family member KTP
        initializeFamilyKtpUpload(familyMemberCount);

        // Initialize Flatpickr for date of birth
        initializeDatePicker(familyMemberCount);

        // Don't generate health questions yet - wait for user to save
        // generateHealthQuestionsForMember(familyMemberCount);
    };

    // Save family member and generate health questions
    window.saveFamilyMember = function(memberId) {
        const memberDiv = document.getElementById(`member-${memberId}`);
        if (!memberDiv) return;

        // Validate required fields
        const requiredFields = memberDiv.querySelectorAll('[required]');
        let allValid = true;

        requiredFields.forEach(field => {
            if (!field.value || field.value.trim() === '') {
                allValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!allValid) {
            showError('Mohon lengkapi semua field yang bertanda bintang (*) terlebih dahulu', 'Data Belum Lengkap');
            return;
        }

        // Get member data
        const nikInput = memberDiv.querySelector('input[name*="[nik]"]');
        const namaInput = memberDiv.querySelector('input[name*="[nama_lengkap]"]');

        if (!nikInput || !namaInput) return;

        const nik = nikInput.value;
        const nama = namaInput.value;

        // Mark as saved and disable editing
        memberDiv.classList.add('bg-green-50', 'border-green-300');
        memberDiv.querySelectorAll('input, select').forEach(field => {
            field.disabled = true;
            field.classList.add('bg-gray-100', 'cursor-not-allowed');
        });

        // Update header
        const header = memberDiv.querySelector('h4');
        if (header) {
            header.innerHTML = `Anggota ${memberId} - ${nama} <span class="text-green-600 text-xs ml-2">� Tersimpan</span>`;
        }

        // Replace buttons with edit/delete
        const buttonContainer = memberDiv.querySelector('.flex.gap-2');
        if (buttonContainer) {
            buttonContainer.innerHTML = `
                <button type="button" onclick="editFamilyMember(${memberId})" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                    ✎ Edit
                </button>
                <button type="button" onclick="removeFamilyMember(${memberId})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">
                    × Hapus
                </button>
            `;
        }

        // Now generate health questions for this member
        generateHealthQuestionsForMember(memberId);

        // Show the "Tambah Anggota Keluarga" button after first save
        const addButton = document.getElementById('add-family-member-btn');
        if (addButton) {
            addButton.classList.remove('hidden');
        }

        showSuccess('Data anggota keluarga berhasil disimpan');
    };

    // Edit family member
    window.editFamilyMember = function(memberId) {
        const memberDiv = document.getElementById(`member-${memberId}`);
        if (!memberDiv) return;

        // Remove saved styling
        memberDiv.classList.remove('bg-green-50', 'border-green-300');

        // Enable editing
        memberDiv.querySelectorAll('input, select').forEach(field => {
            field.disabled = false;
            field.classList.remove('bg-gray-100', 'cursor-not-allowed');
        });

        // Update header
        const header = memberDiv.querySelector('h4');
        if (header) {
            header.innerHTML = `Anggota ${memberId}`;
        }

        // Replace buttons back to save/delete
        const buttonContainer = memberDiv.querySelector('.flex.gap-2');
        if (buttonContainer) {
            buttonContainer.innerHTML = `
                <button type="button" onclick="saveFamilyMember(${memberId})" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
                    ✓ Simpan Anggota
                </button>
                <button type="button" onclick="removeFamilyMember(${memberId})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">
                    × Hapus
                </button>
            `;
        }
    };

    // Remove family member
    window.removeFamilyMember = function(memberId) {
        showConfirm(
            'Data anggota keluarga dan pertanyaan kesehatan terkait akan dihapus permanen. Apakah Anda yakin?',
            () => {
                // Remove member div
                const memberDiv = document.getElementById(`member-${memberId}`);
                if (memberDiv) {
                    memberDiv.remove();
                }

                // Remove health questions for this member
                const healthDiv = document.getElementById(`health-member-${memberId}`);
                if (healthDiv) {
                    healthDiv.remove();
                }

                showSuccess('Anggota keluarga berhasil dihapus');
            },
            'Hapus Anggota Keluarga?'
        );
    };

    // Initialize file upload preview for family member KTP/KIA
    function initializeFamilyKtpUpload(memberId) {
        const container = document.querySelector(`.family-ktp-upload-container[data-member-id="${memberId}"]`);
        if (!container) return;

        const uploadArea = container.querySelector('.family-ktp-upload-area');
        const fileInput = container.querySelector('.family-ktp-file-input');
        const placeholder = container.querySelector('.family-ktp-placeholder');
        const preview = container.querySelector('.family-ktp-preview');
        const previewContent = container.querySelector(`#ktpPreviewContent_${memberId}`);
        const fileNameDisplay = container.querySelector(`#ktpFileName_${memberId}`);
        const changeBtn = container.querySelector('.change-family-ktp-btn');

        // Click to upload
        uploadArea.addEventListener('click', (e) => {
            if (!e.target.closest('.change-family-ktp-btn')) {
                fileInput.click();
            }
        });

        // File input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFamilyKtpFile(file, memberId, placeholder, preview, previewContent, fileNameDisplay);
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

                handleFamilyKtpFile(file, memberId, placeholder, preview, previewContent, fileNameDisplay);
            }
        });
    }

    // Handle family member KTP/KIA file with validation and preview
    function handleFamilyKtpFile(file, memberId, placeholder, preview, previewContent, fileNameDisplay) {
        // Validate file size (2MB)
        if (file.size > 2048000) {
            showNotification('...�� Ukuran file terlalu besar. Maksimal 2MB', 'error');
            document.getElementById(`family_ktp_${memberId}`).value = '';
            return;
        }

        // Show preview based on file type
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContent.innerHTML = `<img src="${e.target.result}" alt="Preview KTP" class="max-h-32 mx-auto rounded-lg">`;
                fileNameDisplay.textContent = `📷 ${file.name} (${formatFileSize(file.size)})`;
                placeholder.classList.add('hidden');
                preview.classList.remove('hidden');
                showNotification('� File berhasil diupload', 'success');
            };
            reader.readAsDataURL(file);
        } else if (file.type === 'application/pdf') {
            previewContent.innerHTML = `
                <div class="text-center">
                    <div class="text-4xl mb-1">📄</div>
                    <p class="text-gray-600 text-xs font-medium">PDF Document</p>
                </div>
            `;
            fileNameDisplay.textContent = `📄 ${file.name} (${formatFileSize(file.size)})`;
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
            showNotification('� File berhasil diupload', 'success');
        } else {
            showNotification('...�� Format file tidak didukung. Gunakan JPG, PNG, atau PDF', 'error');
            document.getElementById(`family_ktp_${memberId}`).value = '';
        }
    }

    // Generate health questions per family member
    async function generateHealthQuestionsForMember(memberId) {
        console.log('generateHealthQuestionsForMember dipanggil untuk member:', memberId);

        // Get member name from the form
        const memberDiv = document.getElementById(`member-${memberId}`);
        let memberName = `Anggota ${memberId}`;
        if (memberDiv) {
            const namaInput = memberDiv.querySelector('input[name*="[nama_lengkap]"]');
            if (namaInput && namaInput.value) {
                memberName = namaInput.value;
            }
        }

        const healthContainer = document.getElementById('health-questions-container');
        console.log('Health container found:', healthContainer);

        if (!healthContainer) {
            console.error('Health container tidak ditemukan!');
            return;
        }

        // Hide the "no family members" notice
        const noMembersNotice = document.getElementById('no-family-members-notice');
        if (noMembersNotice) {
            noMembersNotice.classList.add('hidden');
        }

        // Check if already exists
        const existingDiv = document.getElementById(`health-member-${memberId}`);
        if (existingDiv) {
            console.log('Health questions sudah ada untuk member:', memberId);
            return;
        }

        const memberHealthDiv = document.createElement('div');
        memberHealthDiv.className = 'border border-gray-300 rounded-lg p-4 bg-white';
        memberHealthDiv.id = `health-member-${memberId}`;
        memberHealthDiv.innerHTML = `
            <h4 class="font-semibold text-gray-800 mb-3 pb-2 border-b">${memberName}</h4>

            <div class="space-y-4">
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
        `;

        healthContainer.appendChild(memberHealthDiv);
        console.log('Health questions berhasil ditambahkan untuk member:', memberId);

        // Highlight section yang baru ditambahkan
        memberHealthDiv.classList.add('ring-2', 'ring-green-400');
        setTimeout(() => {
            memberHealthDiv.classList.remove('ring-2', 'ring-green-400');
        }, 2000);
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
                    option.value = province.name || province.nama;
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
                    choicesInstances[select.id].setChoiceByValue(savedValue);

                    // Trigger cascade to load regencies
                    const selectedOption = select.querySelector(`option[value="${savedValue}"]`);
                    if (selectedOption && selectedOption.dataset.code) {
                        const nextRegency = document.querySelector('select[data-type=\"regency\"]');
                        if (nextRegency) {
                            loadRegencies(selectedOption.dataset.code, nextRegency);
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
                option.value = regency.name || regency.nama;
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
                choicesInstances[regencySelect.id].setChoiceByValue(savedValue);

                // Trigger cascade to load districts
                const selectedOption = regencySelect.querySelector(`option[value="${savedValue}"]`);
                if (selectedOption && selectedOption.dataset.code) {
                    const nextDistrict = document.querySelector('select[data-type=\"district\"]');
                    if (nextDistrict) {
                        loadDistricts(selectedOption.dataset.code, nextDistrict);
                    }
                }
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
                option.value = district.name || district.nama;
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
                choicesInstances[districtSelect.id].setChoiceByValue(savedValue);

                // Trigger cascade to load villages
                const selectedOption = districtSelect.querySelector(`option[value="${savedValue}"]`);
                if (selectedOption && selectedOption.dataset.code) {
                    const nextVillage = document.querySelector('select[data-type=\"village\"]');
                    if (nextVillage) {
                        loadVillages(selectedOption.dataset.code, nextVillage);
                    }
                }
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
                option.value = village.name || village.nama;
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
                choicesInstances[villageSelect.id].setChoiceByValue(savedValue);
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
                updateProgress();
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
                updateProgress();
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
                updateProgress();
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
                updateProgress();
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
            const puskesmasList = Array.isArray(result) ? result : [];

            // Populate all puskesmas datalists
            document.querySelectorAll('datalist[id^="puskesmas_list_"]').forEach(datalist => {
                datalist.innerHTML = '';
                puskesmasList.forEach(puskesmas => {
                    const option = document.createElement('option');
                    option.value = puskesmas.name || puskesmas.nama;
                    datalist.appendChild(option);
                });
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
            setTimeout(() => {
                loadingOverlay.style.display = 'none';

                // Auto-add first family member after page loads
                const familyMembersList = document.getElementById('family-members-list');
                if (familyMembersList && familyMembersList.children.length === 0) {
                    addFamilyMember();
                }
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
            updateProgress();

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
            updateProgress();

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
                updateProgress();

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
            updateProgress();

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
            updateProgress();

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
        const formData = new FormData();
        formData.append('question_id', questionId);
        formData.append('file', file);
        formData.append('file_type', fileType);

        fetch('{{ route("questionnaire.autosave", $questionnaire->id) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('File uploaded successfully:', questionId);
            } else {
                console.error('File upload failed:', data.message);
                showNotification('⚠️ Gagal menyimpan file ke server', 'error');
            }
        })
        .catch(error => {
            console.error('File upload error:', error);
            showNotification('⚠️ Gagal menyimpan file ke server', 'error');
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
});
</script>
@endpush
@endsection
