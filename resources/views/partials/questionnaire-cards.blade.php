@foreach($questionnaires as $questionnaire)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 border-t-4 border-yellow-500" data-questionnaire-id="{{ $questionnaire->id }}">
        <div class="p-6">
            <!-- OPD Badge -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex flex-col gap-1">
                    <span class="bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 text-xs font-bold px-3 py-1 rounded-full inline-flex items-center gap-1">
                        🏛️ {{ $questionnaire->opd->name ?? 'Umum' }}
                    </span>
                    @if($questionnaire->opd && $questionnaire->opd->short_name)
                        <span class="text-xs text-gray-500 ml-1">
                            ({{ $questionnaire->opd->short_name }})
                        </span>
                    @endif
                </div>
                <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2 py-1 rounded-full">
                    {{ $questionnaire->questions_count }} pertanyaan
                </span>
            </div>

            <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $questionnaire->title }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $questionnaire->description }}</p>

            @if($questionnaire->start_date || $questionnaire->end_date)
            <div class="flex items-center text-xs text-gray-500 mb-4 bg-gray-50 px-3 py-2 rounded-lg">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $questionnaire->start_date?->format('d M Y') ?? '-' }} s/d {{ $questionnaire->end_date?->format('d M Y') ?? '-' }}
            </div>
            @endif

            @php
                $hasAnswered = false;
                $inProgress = null;
                $progressPercent = 0;
                $answeredCount = 0;

                if (session('respondent')) {
                    $hasAnswered = $questionnaire->responses()
                        ->where('respondent_id', session('respondent.id'))
                        ->where('status', 'completed')
                        ->exists();

                    $inProgress = $questionnaire->responses()
                        ->where('respondent_id', session('respondent.id'))
                        ->where('status', 'in_progress')
                        ->first();

                    if ($inProgress) {
                        $totalQuestions = $questionnaire->questions_count;
                        $answeredCount = $inProgress->answers()->count();
                        $progressPercent = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0;
                    }
                }
            @endphp

            @if($hasAnswered)
                <div class="mb-3 bg-green-50 border border-green-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-green-700">Selesai</span>
                        <span class="text-xs font-bold text-green-600">100%</span>
                    </div>
                    <div class="w-full bg-green-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
                <button disabled class="w-full bg-green-100 text-green-700 py-3 rounded-lg font-medium cursor-not-allowed flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Sudah Diisi
                </button>
            @elseif($inProgress)
                <div class="mb-3 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-medium text-yellow-700">Sedang dikerjakan</span>
                        <span class="text-xs font-bold text-yellow-600">{{ $progressPercent }}%</span>
                    </div>
                    <div class="w-full bg-yellow-200 rounded-full h-2">
                        <div class="bg-yellow-500 h-2 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
                    </div>
                    <div class="text-xs text-yellow-600 mt-1">
                        {{ $answeredCount }} dari {{ $questionnaire->questions_count }} terjawab
                    </div>
                </div>
                <a href="{{ route('questionnaire.start', $questionnaire->id) }}"
                   class="block w-full bg-gradient-to-r from-yellow-500 to-yellow-400 text-black text-center py-3 rounded-lg font-bold hover:from-yellow-600 hover:to-yellow-500 transition shadow-lg hover:shadow-xl">
                    Lanjutkan Survey →
                </a>
            @else
                @if(session('respondent'))
                    <a href="{{ route('questionnaire.start', $questionnaire->id) }}"
                       class="block w-full bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 text-center py-3 rounded-lg font-medium hover:from-black hover:to-gray-800 transition border border-yellow-500/50 shadow-lg hover:shadow-xl">
                        Mulai Isi Survey →
                    </a>
                @else
                    <a href="{{ route('login') }}?intended=questionnaire&id={{ $questionnaire->id }}"
                       class="block w-full bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 text-center py-3 rounded-lg font-medium hover:from-black hover:to-gray-800 transition border border-yellow-500/50 shadow-lg hover:shadow-xl">
                        Mulai Isi Survey →
                    </a>
                @endif
            @endif
        </div>
    </div>
@endforeach
