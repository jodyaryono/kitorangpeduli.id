@extends('layouts.app')

@section('title', 'Preview: ' . $questionnaire->title)

@section('content')
<div class="py-8 px-4 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        <!-- Preview Notice -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg shadow-sm">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                <div>
                    <p class="font-semibold text-yellow-800">Mode Preview</p>
                    <p class="text-sm text-yellow-700">Ini adalah tampilan pratinjau kuesioner. Data tidak akan tersimpan.</p>
                </div>
            </div>
        </div>

        <!-- Header -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6 border-t-4 border-yellow-500">
            <div class="papua-gradient px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="bg-yellow-500 text-black text-xs font-bold px-3 py-1 rounded-full">
                            {{ $questionnaire->opd->name ?? 'Survey Umum' }}
                        </span>
                        <h1 class="text-2xl font-bold text-yellow-400 mt-2">{{ $questionnaire->title }}</h1>
                        <p class="text-sm text-gray-300 mt-1">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Total {{ $questionnaire->questions->count() }} Pertanyaan
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            @if($questionnaire->description)
                <div class="px-8 py-4 bg-gray-50 border-b">
                    <p class="text-gray-700 text-sm">{{ $questionnaire->description }}</p>
                </div>
            @endif
        </div>

        <!-- Questions Preview with Sections -->
        <div class="space-y-6" x-data="{ openSections: {} }">
            @php $questionNumber = 0; @endphp
            @foreach($questionnaire->questions as $item)
                @if($item->is_section)
                    <!-- Section Header with Accordion -->
                    <div x-data="{ open: true }" class="border border-gray-200 rounded-xl shadow-lg overflow-hidden">
                        <div @click="open = !open"
                             class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-xl p-6 text-white cursor-pointer hover:from-blue-700 hover:to-blue-800 transition-all">
                            <h2 class="text-xl font-bold flex items-center justify-between gap-3">
                                <span class="flex items-center gap-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ $item->question_text }}
                                </span>
                                <svg x-show="!open" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                                <svg x-show="open" class="w-6 h-6 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                </svg>
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

                    <!-- Child Questions in this Section -->
                    @foreach($item->childQuestions as $question)
                        @php $questionNumber++; @endphp

                        @if($question->is_section)
                            <!-- Subsection Header (nested) with Accordion -->
                            <div x-data="{ openSub: true }" class="border border-gray-300 rounded-lg overflow-hidden shadow">
                                <div @click="openSub = !openSub"
                                     class="bg-gradient-to-r from-indigo-500 to-indigo-600 p-5 text-white cursor-pointer hover:from-indigo-600 hover:to-indigo-700 transition-all">
                                    <h3 class="text-lg font-semibold flex items-center justify-between gap-2">
                                        <span class="flex items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                            </svg>
                                            {{ $question->question_text }}
                                        </span>
                                        <svg x-show="!openSub" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="openSub" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </h3>
                                </div>

                                <div x-show="openSub"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     class="bg-white p-4 space-y-4">
                            <!-- Questions in Subsection -->
                            @foreach($question->childQuestions as $subQuestion)
                                @php $questionNumber++; @endphp
                                @include('questionnaire.partials.question-preview', ['question' => $subQuestion, 'questionNumber' => $questionNumber, 'indent' => ''])
                            @endforeach
                                </div>
                            </div>
                        @else
                            @include('questionnaire.partials.question-preview', ['question' => $question, 'questionNumber' => $questionNumber, 'indent' => ''])
                        @endif
                    @endforeach
                        </div>
                    </div>
                @else
                    <!-- Standalone Question (no section) -->
                    @php $questionNumber++; @endphp
                    @include('questionnaire.partials.question-preview', ['question' => $item, 'questionNumber' => $questionNumber, 'indent' => ''])
                @endif
            @endforeach
        </div>

        <!-- Back to Admin Button -->
        <div class="mt-8 text-center">
            <a href="javascript:window.close()"
               class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tutup Preview
            </a>
        </div>
    </div>
</div>

<style>
    .papua-gradient {
        background: linear-gradient(135deg, #1e3a8a 0%, #312e81 100%);
    }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endsection
