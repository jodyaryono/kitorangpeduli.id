@foreach($questionnaires as $questionnaire)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition duration-300 border-t-4 border-amber-500" data-questionnaire-id="{{ $questionnaire->id }}">
        <div class="p-6">
            <!-- OPD Badge -->
            <div class="flex items-start justify-between mb-3">
                <div class="flex flex-col gap-1">
                    <span class="bg-gradient-to-r from-gray-900 to-gray-700 text-amber-400 text-xs font-bold px-3 py-1 rounded-full inline-flex items-center gap-1">
                        🏛️ {{ $questionnaire->opd->name ?? 'Umum' }}
                    </span>
                    @if($questionnaire->opd && $questionnaire->opd->short_name)
                        <span class="text-xs text-gray-500 ml-1">
                            ({{ $questionnaire->opd->short_name }})
                        </span>
                    @endif
                </div>
                <span class="bg-amber-100 text-amber-800 text-xs font-medium px-2 py-1 rounded-full">
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

            <!-- Responden Count -->
            <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-blue-700">Responden Terisi</span>
                    <span class="text-lg font-bold text-blue-600">{{ $questionnaire->completed_count ?? 0 }}</span>
                </div>
            </div>

            <a href="{{ route('officer.entry.questionnaire', $questionnaire->id) }}"
               class="block w-full bg-gradient-to-r from-amber-500 to-amber-400 text-black text-center py-3 rounded-lg font-bold hover:from-amber-600 hover:to-amber-500 transition shadow-lg hover:shadow-xl">
                Pilih Kuesioner & Input NIK →
            </a>
        </div>
    </div>
@endforeach
