<div>
@php
    $record = $getRecord();
    $latitude = $record?->latitude ?? null;
    $longitude = $record?->longitude ?? null;
    $uniqueId = 'map-view-' . uniqid();
    $hasLocation = $latitude && $longitude;
@endphp

@if($hasLocation)
    <div class="space-y-2">
        {{-- Map Container --}}
        <div
            id="{{ $uniqueId }}"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-700 overflow-hidden"
            style="height: 300px;"
        ></div>

        {{-- Action buttons --}}
        <div class="flex gap-2 text-sm">
            <a
                href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}"
                target="_blank"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
            >
                🗺️ Buka di Google Maps
            </a>
            <a
                href="https://www.google.com/maps/dir/?api=1&destination={{ $latitude }},{{ $longitude }}"
                target="_blank"
                class="inline-flex items-center gap-1 px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition"
            >
                🚗 Petunjuk Arah
            </a>
        </div>
    </div>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    (function() {
        const mapId = '{{ $uniqueId }}';
        const latitude = {{ $latitude }};
        const longitude = {{ $longitude }};
        const respondentName = @js($record->nama_lengkap ?? 'Responden');
        const respondentAddress = @js($record->alamat ?? '');

        function initMap() {
            if (typeof L === 'undefined') {
                setTimeout(initMap, 100);
                return;
            }

            const mapElement = document.getElementById(mapId);
            if (!mapElement || mapElement._leaflet_id) return;

            const map = L.map(mapId).setView([latitude, longitude], 16);

            L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
                attribution: '© Google',
                maxZoom: 20
            }).addTo(map);

            const marker = L.marker([latitude, longitude]).addTo(map);

            const popupContent = '<div style="min-width: 150px;"><strong>' + respondentName + '</strong>' +
                (respondentAddress ? '<br><small>' + respondentAddress + '</small>' : '') +
                '<br><small>📍 ' + latitude.toFixed(6) + ', ' + longitude.toFixed(6) + '</small></div>';

            marker.bindPopup(popupContent).openPopup();

            setTimeout(function() { map.invalidateSize(); }, 200);
        }

        if (document.readyState === 'complete') {
            setTimeout(initMap, 100);
        } else {
            window.addEventListener('load', function() { setTimeout(initMap, 100); });
        }
    })();
    </script>
@else
    <div class="flex items-center justify-center h-32 bg-gray-100 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
        <div class="text-center text-gray-500 dark:text-gray-400">
            <p class="font-medium">📍 Lokasi Belum Ditentukan</p>
            <p class="text-sm">Koordinat GPS belum diisi</p>
        </div>
    </div>
@endif
</div>
