@extends('layouts.app')

@section('title', $questionnaire->title . ' - KitorangPeduli.id')

@section('content')
<!-- Sticky Header -->
<div class="sticky top-0 z-50 bg-white shadow-md border-b-2 border-yellow-500">
    <div class="max-w-3xl mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-700 hover:text-yellow-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="font-medium">Kembali</span>
            </a>
            <div class="text-right">
                <div class="text-sm font-bold text-yellow-600" id="stickyQuestionCounter">1-10</div>
                <div class="text-xs text-gray-500">dari {{ $questionnaire->questions->count() }}</div>
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
                    <span id="stickyAnsweredCount">0</span>/{{ $questionnaire->questions->count() }} terjawab
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
                        <div class="text-sm text-gray-400">dari {{ $questionnaire->questions->count() }}</div>
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
                <span id="answeredCount">0</span> dari <span id="totalQuestions">{{ $questionnaire->questions->count() }}</span> pertanyaan terjawab
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
                $questionsPerPage = 10;
                $totalPages = ceil($questionnaire->questions->count() / $questionsPerPage);
            @endphp

            @for($page = 0; $page < $totalPages; $page++)
                <div class="question-page {{ $page > 0 ? 'hidden' : '' }}" data-page="{{ $page + 1 }}">
                    @foreach($questionnaire->questions->slice($page * $questionsPerPage, $questionsPerPage) as $question)
                        @php
                            $globalIndex = ($page * $questionsPerPage) + $loop->index;
                            $existingAnswer = $existingAnswers->get($question->id);
                            // Prioritize selected_options for checkbox/multiple_choice, then answer_text, then answer_numeric
                            $savedValue = $existingAnswer ? ($existingAnswer->selected_options ?? $existingAnswer->answer_text ?? $existingAnswer->answer_numeric) : null;
                        @endphp
                        <div class="bg-white rounded-2xl shadow-lg p-8 mb-6">

                            <div class="flex items-start gap-4 mb-6">
                                <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-black font-bold">{{ $globalIndex + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800">
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
                                <div class="mb-6">
                                    @foreach($question->getMedia('question_media') as $media)
                                        @if(str_starts_with($media->mime_type, 'image'))
                                            <img src="{{ $media->getUrl() }}" alt="Media" class="rounded-lg max-h-64 mx-auto">
                                        @elseif(str_starts_with($media->mime_type, 'video'))
                                            <video controls class="rounded-lg max-h-64 mx-auto">
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
                                               class="answer-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                               data-question-id="{{ $question->id }}"
                                               placeholder="Ketik jawaban Anda..."
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('textarea')
                                        <textarea name="answers[{{ $question->id }}]"
                                                  rows="4"
                                                  class="answer-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                                  data-question-id="{{ $question->id }}"
                                                  placeholder="Ketik jawaban Anda..."
                                                  {{ $question->is_required ? 'required' : '' }}>{{ old('answers.' . $question->id, $savedValue) }}</textarea>
                                        @break

                                    @case('number')
                                        <input type="number"
                                               name="answers[{{ $question->id }}]"
                                               value="{{ old('answers.' . $question->id, $savedValue) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                               placeholder="Masukkan angka..."
                                               {{ $question->is_required ? 'required' : '' }}>
                                        @break

                                    @case('date')
                                        <input type="date"
                                               name="answers[{{ $question->id }}]"
                                               value="{{ old('answers.' . $question->id, $savedValue) }}"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                               {{ $question->is_required ? 'required' : '' }}>
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
                                                    ⚠️ Tidak ada opsi jawaban untuk pertanyaan ini. Silakan hubungi administrator.
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
                                                    ⚠️ Tidak ada opsi jawaban untuk pertanyaan ini. Silakan hubungi administrator.
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
                                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-yellow-400 transition">
                                            <input type="file"
                                                   name="file_answers[{{ $question->id }}]"
                                                   id="file_{{ $question->id }}"
                                                   class="hidden"
                                                   accept="{{ $question->settings['accept'] ?? 'image/*,.pdf' }}"
                                                   {{ $question->is_required ? 'required' : '' }}>
                                            <label for="file_{{ $question->id }}" class="cursor-pointer">
                                                <div class="text-4xl mb-2">📎</div>
                                                <p class="text-gray-600">Klik untuk upload file</p>
                                                <p class="text-gray-400 text-sm mt-1">atau drag & drop</p>
                                            </label>
                                        </div>
                                        @break

                                @endswitch
                            </div>
                        </div>
                    @endforeach
                </div>
            @endfor

            <!-- Navigation Buttons -->
            <div class="flex items-center justify-between">
                <button type="button"
                        id="prevBtn"
                        class="hidden px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Sebelumnya
                </button>

                <button type="button"
                        id="nextBtn"
                        class="ml-auto px-6 py-3 bg-gray-900 text-yellow-400 rounded-lg hover:bg-black transition flex items-center border border-yellow-500">
                    Selanjutnya
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button type="submit"
                        id="submitBtn"
                        class="hidden ml-auto px-8 py-3 bg-gradient-to-r from-green-600 to-green-500 text-white rounded-lg hover:from-green-700 hover:to-green-600 transition flex items-center shadow-lg">
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
    const pages = document.querySelectorAll('.question-page');
    const totalPages = pages.length;
    const totalQuestions = {{ $questionnaire->questions->count() }};
    const questionsPerPage = {{ $questionsPerPage }};
    const responseId = {{ $response->id }};
    let currentPage = 1;
    let answeredQuestions = new Set();
    let autoSaveTimeout = null;

    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const progressBar = document.getElementById('progressBar');
    const progressPercent = document.getElementById('progressPercent');
    const questionCounter = document.getElementById('questionCounter');
    const answeredCount = document.getElementById('answeredCount');

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

        // Update submit button text if 100%
        if (progress >= 100) {
            submitBtn.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Selesai - Kirim Jawaban
            `;
            submitBtn.classList.remove('hidden');
            submitBtn.classList.add('animate-pulse');
        }
    }

    function updateUI() {
        // Calculate question range for current page
        const startQuestion = ((currentPage - 1) * questionsPerPage) + 1;
        const endQuestion = Math.min(currentPage * questionsPerPage, totalQuestions);

        // Update question counter to show range
        const counterText = startQuestion === endQuestion ? startQuestion : startQuestion + '-' + endQuestion;
        questionCounter.textContent = counterText;
        document.getElementById('stickyQuestionCounter').textContent = counterText;

        // Show/hide buttons
        prevBtn.classList.toggle('hidden', currentPage === 1);
        nextBtn.classList.toggle('hidden', currentPage === totalPages);

        // Submit button only shows when progress is 100%
        const progress = (answeredQuestions.size / totalQuestions) * 100;
        if (progress < 100) {
            submitBtn.classList.add('hidden');
        }

        // Show current page
        pages.forEach((page, index) => {
            page.classList.toggle('hidden', index + 1 !== currentPage);
        });

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
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

    // Listen to all answer inputs
    document.querySelectorAll('.answer-input').forEach(input => {
        const questionId = input.getAttribute('data-question-id');

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

    prevBtn.addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            updateUI();
        }
    });

    nextBtn.addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            updateUI();
        }
    });

    // Prevent form submission if not 100%
    document.getElementById('surveyForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default first

        const progress = (answeredQuestions.size / totalQuestions) * 100;
        if (progress < 100) {
            alert('Mohon jawab semua pertanyaan terlebih dahulu!');
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
    updateUI();

    // Get GPS location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        });
    }
});
</script>
@endpush
@endsection
