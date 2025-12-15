@extends('layouts.app')

@section('title', 'KitorangPeduli.id - Survey Papua')

@section('content')
<!-- Hero Section with Stats -->
<div class="papua-gradient text-white py-12 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 text-9xl">🦅</div>
        <div class="absolute bottom-10 right-10 text-9xl">🏝️</div>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Stats at Top -->
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-yellow-500/50">
                <div class="text-3xl md:text-4xl font-bold text-yellow-400">{{ $stats['questionnaires'] }}</div>
                <div class="text-gray-300 text-sm">Kuesioner Aktif</div>
            </div>
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-red-500/50">
                <div class="text-3xl md:text-4xl font-bold text-red-400">{{ $stats['respondents'] }}</div>
                <div class="text-gray-300 text-sm">Responden Terdaftar</div>
            </div>
            <div class="bg-black/40 backdrop-blur rounded-xl p-4 text-center border border-white/30">
                <div class="text-3xl md:text-4xl font-bold text-white">{{ $stats['responses'] }}</div>
                <div class="text-gray-300 text-sm">Survey Terisi</div>
            </div>
        </div>

        <!-- Hero Text -->
        <div class="text-center">
            <div class="flex justify-center gap-2 mb-6">
                <span class="w-12 h-2 bg-black rounded"></span>
                <span class="w-12 h-2 bg-yellow-500 rounded"></span>
                <span class="w-12 h-2 bg-red-600 rounded"></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold mb-4">
                <span class="text-yellow-400">Suara Anda</span>
                <span class="text-white">Penting untuk</span>
                <span class="text-red-500">Kota Jayapura</span>
            </h1>
            <p class="text-xl text-gray-300 max-w-2xl mx-auto">
                Berpartisipasi dalam survey untuk membantu pembangunan Kota Jayapura yang lebih baik.
                Setiap jawaban Anda berkontribusi untuk kemajuan bersama.
            </p>
            <div class="flex flex-wrap items-center justify-center gap-3 mt-6">
                <a href="#questionnaire-grid" class="px-6 py-3 bg-yellow-500 text-black font-semibold rounded-lg hover:bg-yellow-400 transition shadow-lg">Lihat Kuesioner</a>
                @auth
                    <a href="{{ route('officer.entry') }}" class="px-6 py-3 bg-gray-100 text-gray-900 font-semibold rounded-lg hover:bg-white transition shadow-lg border border-yellow-400/40">Portal Petugas OPD</a>
                @else
                    <a href="{{ route('login', ['intended' => 'officer-entry']) }}" class="px-6 py-3 bg-gray-100 text-gray-900 font-semibold rounded-lg hover:bg-white transition shadow-lg border border-yellow-400/40">Masuk Petugas OPD</a>
                @endauth
            </div>
            <p class="text-xs text-gray-300 mt-2">Petugas OPD wajib login dengan nomor HP terdaftar.</p>
        </div>
    </div>
</div>

<!-- Questionnaires Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    @if(session('respondent'))
    <!-- Greeting Message for Logged In User -->
    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 border-l-4 border-yellow-500 rounded-lg p-6 mb-8 shadow-md">
        <div class="flex items-center gap-3">
            <span class="text-4xl">👋</span>
            <div>
                <h3 class="text-xl font-bold text-gray-800">
                    Halo, {{ session('respondent.nama_lengkap') }}!
                </h3>
                <p class="text-gray-700">
                    Silakan pilih survey yang ingin Anda isi di bawah ini. Setiap partisipasi Anda sangat berarti untuk pembangunan Kota Jayapura.
                </p>
            </div>
        </div>
    </div>
    @endif

    <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">
        <span class="text-yellow-500">📋</span> Kuesioner Tersedia
    </h2>

    <!-- Search & Filter Section -->
    <div class="bg-white rounded-xl shadow-lg p-4 mb-8 border-l-4 border-yellow-500">
        <form id="search-form" action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row gap-4">
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
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition">
            </div>

            <!-- OPD Filter -->
            <div class="md:w-64">
                <select name="opd"
                        id="opd-filter"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition bg-white">
                    <option value="">🏛️ Semua OPD</option>
                    @foreach($opds as $opd)
                        <option value="{{ $opd->id }}" {{ ($opdFilter ?? '') == $opd->id ? 'selected' : '' }}>
                            {{ $opd->short_name ?? $opd->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Button -->
            <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 font-semibold rounded-lg hover:from-black hover:to-gray-800 transition shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Cari
            </button>

            <!-- Reset Button -->
            @if(($search ?? '') || ($opdFilter ?? ''))
                <a href="{{ route('home') }}"
                   class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            @endif
        </form>

        <!-- Active Filters Display -->
        @if(($search ?? '') || ($opdFilter ?? ''))
            <div class="mt-4 flex flex-wrap gap-2 items-center">
                <span class="text-sm text-gray-500">Filter aktif:</span>
                @if($search ?? '')
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                        🔍 "{{ $search }}"
                    </span>
                @endif
                @if($opdFilter ?? '')
                    @php
                        $selectedOpd = $opds->firstWhere('id', $opdFilter);
                    @endphp
                    @if($selectedOpd)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            🏛️ {{ $selectedOpd->short_name ?? $selectedOpd->name }}
                        </span>
                    @endif
                @endif
            </div>
        @endif
    </div>

    @if($questionnaires->isEmpty())
        <div class="text-center py-12 bg-white rounded-xl shadow border-l-4 border-yellow-500">
            <div class="text-6xl mb-4">📭</div>
            @if(($search ?? '') || ($opdFilter ?? ''))
                <p class="text-gray-600 mb-4">Tidak ada kuesioner yang sesuai dengan pencarian.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-yellow-600 hover:text-yellow-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Lihat semua kuesioner
                </a>
            @else
                <p class="text-gray-600">Belum ada kuesioner yang tersedia saat ini.</p>
            @endif
        </div>
    @else
        <div id="questionnaire-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('partials.questionnaire-cards', ['questionnaires' => $questionnaires])
        </div>

        <!-- Load More Button -->
        @if($questionnaires->hasMorePages())
            <div id="load-more-container" class="text-center mt-8">
                <button id="load-more-btn"
                        data-next-page="{{ $questionnaires->currentPage() + 1 }}"
                        class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 font-semibold rounded-full hover:from-black hover:to-gray-800 transition shadow-lg hover:shadow-xl border border-yellow-500/50">
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

    // Get current search and filter params
    const urlParams = new URLSearchParams(window.location.search);
    const currentSearch = urlParams.get('search') || '';
    const currentOpd = urlParams.get('opd') || '';

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            const nextPage = this.dataset.nextPage;

            // Show loading state
            loadMoreIcon.classList.add('hidden');
            loadMoreSpinner.classList.remove('hidden');
            loadMoreBtn.disabled = true;

            // Build URL with all params
            let url = `{{ route('home') }}?page=${nextPage}`;
            if (currentSearch) url += `&search=${encodeURIComponent(currentSearch)}`;
            if (currentOpd) url += `&opd=${encodeURIComponent(currentOpd)}`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Create a temporary container to parse the HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data.html;

                // Get all new cards
                const newCards = tempDiv.querySelectorAll('[data-questionnaire-id]');

                // Get existing questionnaire IDs
                const existingIds = new Set(
                    Array.from(grid.querySelectorAll('[data-questionnaire-id]'))
                        .map(card => card.dataset.questionnaireId)
                );

                // Only append cards that don't already exist
                newCards.forEach(card => {
                    if (!existingIds.has(card.dataset.questionnaireId)) {
                        grid.appendChild(card);
                    }
                });

                // Update showing count
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

    // Auto submit on OPD filter change
    const opdFilter = document.getElementById('opd-filter');
    if (opdFilter) {
        opdFilter.addEventListener('change', function() {
            document.getElementById('search-form').submit();
        });
    }
});
</script>

<!-- How It Works -->
<div class="bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-yellow-400 mb-8 text-center">🚀 Cara Berpartisipasi</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">📱</span>
                </div>
                <h3 class="font-semibold text-white">1. Daftar</h3>
                <p class="text-gray-400 text-sm">Masukkan nomor HP dan data diri Anda</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">🔐</span>
                </div>
                <h3 class="font-semibold text-white">2. Verifikasi OTP</h3>
                <p class="text-gray-400 text-sm">Masukkan kode OTP yang dikirim via WhatsApp</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">📋</span>
                </div>
                <h3 class="font-semibold text-white">3. Pilih Survey</h3>
                <p class="text-gray-400 text-sm">Pilih kuesioner yang ingin Anda isi</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-2xl">✅</span>
                </div>
                <h3 class="font-semibold text-white">4. Selesai</h3>
                <p class="text-gray-400 text-sm">Jawaban Anda tercatat untuk pembangunan Papua</p>
            </div>
        </div>
    </div>
</div>

<!-- Auth Modal -->
<div id="authModal" class="hidden fixed inset-0 z-[9999]" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="bg-white rounded-2xl max-w-md w-full p-8 relative shadow-2xl">
            <button onclick="closeAuthModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-gradient-to-r from-yellow-400 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">🔐</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">Masuk atau Daftar</h3>
                <p class="text-gray-600">Silakan masuk atau daftar terlebih dahulu untuk mengisi survey</p>
            </div>

        <div class="space-y-3">
            <a href="{{ route('login') }}?intended=questionnaire&id="
               id="loginLink"
               class="block w-full bg-gradient-to-r from-gray-900 to-gray-700 text-yellow-400 text-center py-3 rounded-lg font-medium hover:from-black hover:to-gray-800 transition border border-yellow-500/50 shadow-lg hover:shadow-xl">
                🔑 Masuk dengan Nomor HP
            </a>

            <a href="{{ route('register') }}?intended=questionnaire&id="
               id="registerLink"
               class="block w-full bg-gradient-to-r from-yellow-500 to-red-500 text-white text-center py-3 rounded-lg font-medium hover:from-yellow-600 hover:to-red-600 transition shadow-lg hover:shadow-xl">
                📝 Daftar Sekarang
            </button>
        </div>

        <p class="text-xs text-gray-500 text-center mt-6">
            Dengan mendaftar, Anda membantu pembangunan Kota Jayapura
        </p>
        </div>
    </div>
</div>

<script>
// Define functions globally
window.showAuthModal = function(questionnaireId) {
    // Update link hrefs with questionnaire ID
    const loginLink = document.getElementById('loginLink');
    const registerLink = document.getElementById('registerLink');

    if (loginLink) {
        loginLink.href = '{{ route("login") }}?intended=questionnaire&id=' + questionnaireId;
    }
    if (registerLink) {
        registerLink.href = '{{ route("register") }}?intended=questionnaire&id=' + questionnaireId;
    }

    // Show modal
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

window.closeAuthModal = function() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAuthModal();
        }
    });

    // Close modal on background click
    const authModal = document.getElementById('authModal');
    if (authModal) {
        authModal.addEventListener('click', function(event) {
            // Only close if clicking the backdrop, not the modal content
            if (event.target === this) {
                closeAuthModal();
            }
        });
    }
});
</script>
@endsection
