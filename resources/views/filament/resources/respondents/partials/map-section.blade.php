@php
    $latitude = $record->latitude ?? null;
    $longitude = $record->longitude ?? null;
    $hasLocation = $latitude && $longitude;
    $uniqueId = 'respondent-map-' . $record->id;
@endphp

<div class="mt-6">
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="fi-section-header flex flex-col gap-3 px-6 py-4">
            <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                🗺️ Lokasi Responden
            </h3>
        </div>

        <div class="fi-section-content px-6 pb-6">
            @if($hasLocation)
                {{-- Map Container --}}
                <div
                    id="{{ $uniqueId }}"
                    class="w-full rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden"
                    style="height: 350px;"
                ></div>

                {{-- Action buttons --}}
                <div class="flex gap-2 text-sm mt-3">
                    <a
                        href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                    >
                        🗺️ Buka di Google Maps
                    </a>
                    <a
                        href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}"
                        target="_blank"
                        class="inline-flex items-center gap-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm font-medium"
                    >
                        🚗 Petunjuk Arah
                    </a>
                </div>

                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                (function() {
                    function initRespondentMap() {
                        if (typeof L === 'undefined') {
                            setTimeout(initRespondentMap, 100);
                            return;
                        }

                        const mapElement = document.getElementById('{{ $uniqueId }}');
                        if (!mapElement || mapElement._leaflet_id) return;

                        const lat = {{ $latitude }};
                        const lng = {{ $longitude }};

                        const map = L.map('{{ $uniqueId }}').setView([lat, lng], 16);

                        // Google Hybrid
                        L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                            attribution: '© Google',
                            maxZoom: 20
                        }).addTo(map);

                        // Marker
                        const marker = L.marker([lat, lng]).addTo(map);
                        marker.bindPopup('<strong>{{ $record->nama_lengkap }}</strong><br>{{ $record->alamat ?? "" }}<br><small>📍 ' + lat.toFixed(6) + ', ' + lng.toFixed(6) + '</small>').openPopup();

                        setTimeout(function() { map.invalidateSize(); }, 300);
                    }

                    setTimeout(initRespondentMap, 200);
                })();
                </script>
            @else
                <div class="flex items-center justify-center h-32 bg-gray-100 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                    <div class="text-center text-gray-500 dark:text-gray-400">
                        <p class="font-medium">📍 Lokasi Belum Ditentukan</p>
                        <p class="text-sm">Koordinat GPS belum diisi untuk responden ini</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
