@php
    $record = $getRecord();
    $latitude = $record?->latitude ?? -2.5489;
    $longitude = $record?->longitude ?? 140.7183;
    $uniqueId = 'map-' . uniqid();
@endphp

{{-- Load Leaflet CSS --}}
@once
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
    .map-picker-container .leaflet-container {
        z-index: 1 !important;
    }
</style>
@endonce

<div class="map-picker-container space-y-3">
    {{-- Search & Location Controls --}}
    <div class="flex gap-2">
        <input
            type="text"
            id="{{ $uniqueId }}-search"
            placeholder="Cari alamat..."
            class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm px-3 py-2"
        >
        <button
            type="button"
            onclick="mapPicker_{{ str_replace('-', '_', $uniqueId) }}.searchAddress()"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition"
        >
            🔍 Cari
        </button>
        <button
            type="button"
            onclick="mapPicker_{{ str_replace('-', '_', $uniqueId) }}.getCurrentLocation()"
            class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition"
        >
            📍 Lokasi Saya
        </button>
    </div>

    {{-- Map Container --}}
    <div
        id="{{ $uniqueId }}"
        class="w-full rounded-lg border border-gray-300 dark:border-gray-700"
        style="height: 350px; z-index: 1;"
    ></div>

    {{-- Coordinates Display --}}
    <div class="flex gap-4 text-sm flex-wrap">
        <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg">
            <span class="font-medium text-gray-600 dark:text-gray-400">Lat:</span>
            <span id="{{ $uniqueId }}-lat" class="font-mono text-blue-600 dark:text-blue-400">{{ $latitude }}</span>
        </div>
        <div class="flex items-center gap-2 bg-gray-100 dark:bg-gray-800 px-3 py-2 rounded-lg">
            <span class="font-medium text-gray-600 dark:text-gray-400">Lng:</span>
            <span id="{{ $uniqueId }}-lng" class="font-mono text-blue-600 dark:text-blue-400">{{ $longitude }}</span>
        </div>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400">
        💡 Klik pada peta atau seret marker untuk mengubah lokasi. Gunakan tombol "Lokasi Saya" untuk GPS.
    </p>
</div>

<script>
(function() {
    const mapId = '{{ $uniqueId }}';
    const varName = 'mapPicker_{{ str_replace('-', '_', $uniqueId) }}';

    // Wait for DOM and Leaflet
    function initWhenReady() {
        if (typeof L === 'undefined') {
            setTimeout(initWhenReady, 100);
            return;
        }

        const mapElement = document.getElementById(mapId);
        if (!mapElement) {
            setTimeout(initWhenReady, 100);
            return;
        }

        // Skip if already initialized
        if (mapElement._leaflet_id) return;

        let latitude = {{ $latitude }};
        let longitude = {{ $longitude }};

        // Initialize map
        const map = L.map(mapId).setView([latitude, longitude], 15);

        // Define base layers
        const osmStreet = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap',
            maxZoom: 19
        });

        // Google Satellite
        const googleSat = L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            attribution: '© Google',
            maxZoom: 20
        });

        // Google Hybrid (Satellite + Labels)
        const googleHybrid = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            attribution: '© Google',
            maxZoom: 20
        });

        // Google Terrain
        const googleTerrain = L.tileLayer('https://mt1.google.com/vt/lyrs=p&x={x}&y={y}&z={z}', {
            attribution: '© Google',
            maxZoom: 20
        });

        // Esri World Imagery (high quality satellite)
        const esriSat = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: '© Esri',
            maxZoom: 19
        });

        // OpenTopoMap (topographic/contour)
        const openTopo = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenTopoMap',
            maxZoom: 17
        });

        // Add default layer - Satellite + Label
        googleHybrid.addTo(map);

        // Layer control
        const baseLayers = {
            '🛰️ Satellite + Label': googleHybrid,
            '🗺️ Street Map': osmStreet,
            '🛰️ Satellite': googleSat,
            '⛰️ Terrain': googleTerrain,
            '🏔️ Topografi': openTopo,
            '🌍 Esri Satellite': esriSat
        };

        L.control.layers(baseLayers, null, { position: 'topright', collapsed: false }).addTo(map);

        // Add marker
        const marker = L.marker([latitude, longitude], { draggable: true }).addTo(map);

        // Update display function
        function updateDisplay(lat, lng) {
            latitude = lat;
            longitude = lng;
            document.getElementById(mapId + '-lat').textContent = lat.toFixed(8);
            document.getElementById(mapId + '-lng').textContent = lng.toFixed(8);

            // Update Livewire/Filament state without causing re-render
            const wireEl = mapElement.closest('[wire\\:id]');
            if (wireEl) {
                const wireId = wireEl.getAttribute('wire:id');
                const wire = Livewire.find(wireId);
                if (wire) {
                    // Use $set with proper path
                    wire.$set('data.latitude', parseFloat(lat), false);
                    wire.$set('data.longitude', parseFloat(lng), false);
                }
            }
        }

        // Marker drag event
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateDisplay(pos.lat, pos.lng);
        });

        // Map click event
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateDisplay(e.latlng.lat, e.latlng.lng);
        });

        // Expose methods globally
        window[varName] = {
            getCurrentLocation: function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;
                            map.setView([lat, lng], 17);
                            marker.setLatLng([lat, lng]);
                            updateDisplay(lat, lng);
                        },
                        function(error) {
                            alert('Tidak dapat mengakses lokasi: ' + error.message);
                        },
                        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
                    );
                } else {
                    alert('Browser tidak mendukung Geolocation');
                }
            },

            searchAddress: function() {
                const query = document.getElementById(mapId + '-search').value;
                if (!query) return;

                fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query) + '&limit=1')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.length > 0) {
                            const lat = parseFloat(data[0].lat);
                            const lng = parseFloat(data[0].lon);
                            map.setView([lat, lng], 17);
                            marker.setLatLng([lat, lng]);
                            updateDisplay(lat, lng);
                        } else {
                            alert('Alamat tidak ditemukan');
                        }
                    });
            }
        };

        // Fix map size after render
        setTimeout(function() {
            map.invalidateSize();
        }, 200);

        // Store map reference for resize
        window[varName].map = map;
        window[varName].marker = marker;
    }

    // Start initialization
    if (document.readyState === 'complete') {
        setTimeout(initWhenReady, 100);
    } else {
        window.addEventListener('load', function() {
            setTimeout(initWhenReady, 100);
        });
    }

    // Re-init on Livewire updates
    document.addEventListener('livewire:navigated', function() {
        setTimeout(initWhenReady, 100);
    });

    // Handle Livewire morphing - invalidate size
    document.addEventListener('livewire:morph', function() {
        if (window[varName] && window[varName].map) {
            setTimeout(function() {
                window[varName].map.invalidateSize();
            }, 100);
        }
    });
})();
</script>
