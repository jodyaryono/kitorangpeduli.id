@extends('layouts.app')

@section('title', 'Portal Officer - KitorangPeduli.id')

@section('content')
<!-- Hero Section -->
<div class="papua-gradient text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 text-9xl">🏢</div>
        <div class="absolute bottom-10 right-10 text-9xl">📋</div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Stats at Top -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-amber-500/50">
                <div class="text-3xl md:text-4xl font-bold text-amber-400">{{ $stats['questionnaires'] }}</div>
                <div class="text-gray-300 text-sm">Kuesioner Tersedia</div>
            </div>
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-white/30">
                <div class="text-lg md:text-xl font-bold text-white line-clamp-2">{{ $stats['opdName'] }}</div>
                <div class="text-gray-300 text-sm">OPD Anda</div>
            </div>
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-green-500/50">
                <div class="text-3xl md:text-4xl font-bold text-green-400">{{ $stats['totalEntered'] }}</div>
                <div class="text-gray-300 text-sm">Entri Selesai</div>
            </div>
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-blue-500/50">
                <div class="text-3xl md:text-4xl font-bold text-blue-400">{{ $stats['totalRespondents'] }}</div>
                <div class="text-gray-300 text-sm">Responden Terisi</div>
            </div>
        </div>

        <!-- Hero Text -->
        <div class="text-center">
            <div class="flex justify-center gap-2 mb-6">
                <span class="w-12 h-2 bg-black rounded"></span>
                <span class="w-12 h-2 bg-amber-500 rounded"></span>
                <span class="w-12 h-2 bg-gray-600 rounded"></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">
                <span class="text-amber-400">Portal</span>
                <span class="text-white">Petugas</span>
                <span class="text-gray-300">OPD</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Halo, <strong>{{ $user->name }}</strong>! Pilih kuesioner di bawah untuk membantu warga mengisi survey.
            </p>
            <p class="text-xs text-gray-300 mt-6">Officer-assisted mode: Anda mengisi untuk responden lain.</p>
        </div>
    </div>
</div>

<!-- Recent Entries Section -->
@if($recentEntries && count($recentEntries) > 0)
<div class="bg-gray-900 border-t border-gray-800 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="text-2xl font-bold text-white">
                <span class="text-blue-400">📝</span> Aktivitas Terbaru
            </h2>
            <span class="bg-blue-500/30 text-blue-300 text-xs font-bold px-3 py-1 rounded-full">{{ count($recentEntries) }} Item</span>
        </div>

        <!-- Search Filter -->
        <div class="mb-6">
            <input type="text"
                   id="activity-search"
                   placeholder="Cari berdasarkan nama responden..."
                   class="w-full px-4 py-2 bg-gray-800 border border-gray-700 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 placeholder-gray-500 transition">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-blue-500/20 border-b border-blue-500/30">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-blue-300">Kuesioner</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-blue-300">Responden</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-blue-300">NIK</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-blue-300">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-blue-300">Waktu</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-blue-300">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentEntries as $index => $entry)
                        <tr class="border-b border-gray-700 hover:bg-gray-800/50 transition {{ $index % 2 === 0 ? 'bg-gray-900/30' : 'bg-gray-800/10' }}">
                            <td class="px-4 py-3 text-sm font-medium text-white">{{ $entry->questionnaire->title }}</td>
                            <td class="px-4 py-3 text-sm text-gray-300">{{ $entry->respondent->nama_lengkap ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-400 font-mono">{{ $entry->respondent->nik ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($entry->status === 'completed')
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-500/30 text-green-300 text-xs font-bold rounded">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Selesai
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500/30 text-amber-300 text-xs font-bold rounded">
                                        <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Proses
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $entry->updated_at?->diffForHumans() ?? 'Baru saja' }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($entry->status === 'in_progress')
                                    <a href="{{ route('questionnaire.start', ['id' => $entry->questionnaire->id]) }}"
                                       class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded transition inline-block">
                                        Lanjutkan
                                    </a>
                                @else
                                    <span class="text-gray-600 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Questionnaires Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
        <span class="text-amber-500">📋</span> Kuesioner untuk OPD Anda
    </h2>

    <!-- Search Section -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-8 border-l-4 border-amber-500">
        <form id="search-form" action="{{ route('officer.entry') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       name="search"
                       id="search-input"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari kuesioner..."
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
            </div>

            <!-- Search Button -->
            <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-amber-400 font-semibold rounded-lg hover:from-black hover:to-gray-800 transition shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>

            <!-- Reset Button -->
            @if($search ?? '')
                <a href="{{ route('officer.entry') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            @endif
        </form>

        @if($search ?? '')
            <div class="mt-4 flex flex-wrap gap-2 items-center">
                <span class="text-sm text-gray-500">Filter aktif:</span>
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-sm">
                    🔍 "{{ $search }}"
                </span>
            </div>
        @endif
    </div>

    @if($questionnaires->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow border-l-4 border-amber-500">
            <div class="text-6xl mb-4">📭</div>
            @if($search ?? '')
                <p class="text-gray-600 mb-4">Tidak ada kuesioner yang sesuai dengan pencarian.</p>
                <a href="{{ route('officer.entry') }}" class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Lihat semua kuesioner
                </a>
            @else
                <p class="text-gray-600">Belum ada kuesioner yang tersedia untuk OPD Anda saat ini.</p>
            @endif
        </div>
    @else
        <div id="questionnaire-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('partials.officer-questionnaire-cards', ['questionnaires' => $questionnaires])
        </div>

        <!-- Load More Button -->
        @if($questionnaires->hasMorePages())
            <div id="load-more-container" class="text-center mt-8">
                <button id="load-more-btn"
                        data-next-page="{{ $questionnaires->currentPage() + 1 }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-amber-400 font-semibold rounded-full hover:from-black hover:to-gray-800 transition shadow-lg hover:shadow-xl border border-amber-500/50">
                    <span>Lihat Lebih Banyak</span>
                    <svg id="load-more-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                    <svg id="load-more-spinner" class="hidden w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        @endif

        <!-- Total Info -->
        <div class="text-center mt-6 text-gray-500 text-sm">
            Menampilkan <span id="showing-count">{{ $questionnaires->count() }}</span> dari <span class="font-semibold">{{ $questionnaires->total() }}</span> kuesioner
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadMoreBtn = document.getElementById('load-more-btn');
    const grid = document.getElementById('questionnaire-grid');
    const loadMoreContainer = document.getElementById('load-more-container');
    const loadMoreIcon = document.getElementById('load-more-icon');
    const loadMoreSpinner = document.getElementById('load-more-spinner');
    const showingCount = document.getElementById('showing-count');

    const urlParams = new URLSearchParams(window.location.search);
    const currentSearch = urlParams.get('search') || '';

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const nextPage = this.dataset.nextPage;

            loadMoreIcon.classList.add('hidden');
            loadMoreSpinner.classList.remove('hidden');
            loadMoreBtn.disabled = true;

            let url = `{{ route('officer.entry') }}?page=${nextPage}`;
            if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;

                const newCards = tempDiv.querySelectorAll('[data-questionnaire-id]');
                const existingIds = new Set(
                    Array.from(grid.querySelectorAll('[data-questionnaire-id]'))
                        .map(card => card.dataset.questionnaireId)
                );

                newCards.forEach(card => {
                    if (!existingIds.has(card.dataset.questionnaireId)) {
                        grid.appendChild(card);
                    }
                });

                const totalCards = grid.querySelectorAll('[data-questionnaire-id]').length;
                if (showingCount) {
                    showingCount.textContent = totalCards;
                }

                if (data.hasMore) {
                    loadMoreBtn.dataset.nextPage = data.nextPage;
                    loadMoreIcon.classList.remove('hidden');
                    loadMoreSpinner.classList.add('hidden');
                    loadMoreBtn.disabled = false;
                } else {
                    loadMoreContainer.innerHTML = '<span class="text-gray-500 text-sm">✓ Semua kuesioner sudah ditampilkan</span>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                loadMoreIcon.classList.remove('hidden');
                loadMoreSpinner.classList.add('hidden');
                loadMoreBtn.disabled = false;
            });
        });
    }

    // Search activity table
    const activitySearch = document.getElementById('activity-search');
    if (activitySearch) {
        activitySearch.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tableBody = document.querySelector('tbody');
            if (tableBody) {
                const rows = tableBody.querySelectorAll('tr');
                rows.forEach(row => {
                    const responden = row.cells[1]?.textContent.toLowerCase() || '';
                    const nik = row.cells[2]?.textContent.toLowerCase() || '';
                    if (responden.includes(searchTerm) || nik.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    }
});
</script>
@endsection
