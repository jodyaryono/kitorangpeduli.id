<x-filament-panels::page>
    <div class="space-y-4">
        <div class="flex gap-4 flex-wrap">
            <x-filament::button
                id="btn-responses"
                wire:click="$dispatch('showResponses')"
                color="primary"
            >
                Lokasi Survey
            </x-filament::button>
            <x-filament::button
                id="btn-respondents"
                wire:click="$dispatch('showRespondents')"
                color="success"
            >
                Lokasi Responden
            </x-filament::button>
        </div>

        <div id="map" style="height: 600px; width: 100%; border-radius: 0.5rem;"></div>

        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
            <h3 class="font-semibold mb-2">Keterangan:</h3>
            <div class="flex gap-6 flex-wrap">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-blue-500"></span>
                    <span>Lokasi Survey</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-green-500"></span>
                    <span>Lokasi Responden (OAP)</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-cyan-500"></span>
                    <span>Portnumbay</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-amber-500"></span>
                    <span>WNA</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded-full bg-gray-500"></span>
                    <span>Pendatang</span>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize map centered on Papua
            const map = L.map('map').setView([-2.5, 140.7], 7);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            const responses = @json($responses);
            const respondents = @json($respondents);

            const responseMarkers = L.markerClusterGroup();
            const respondentMarkers = L.markerClusterGroup();

            // Response markers (blue)
            responses.forEach(function(response) {
                const marker = L.circleMarker([response.lat, response.lng], {
                    radius: 8,
                    fillColor: '#3b82f6',
                    color: '#1e40af',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                });

                marker.bindPopup(`
                    <div class="p-2">
                        <h3 class="font-bold">${response.name}</h3>
                        <p><strong>Kuesioner:</strong> ${response.questionnaire}</p>
                        <p><strong>Waktu:</strong> ${response.date}</p>
                    </div>
                `);

                responseMarkers.addLayer(marker);
            });

            // Respondent markers (color by citizen type)
            const citizenColors = {
                'OAP': '#22c55e',
                'Portnumbay': '#06b6d4',
                'WNA': '#f59e0b',
                'Pendatang': '#6b7280'
            };

            respondents.forEach(function(respondent) {
                const color = citizenColors[respondent.citizenType] || '#6b7280';

                const marker = L.circleMarker([respondent.lat, respondent.lng], {
                    radius: 8,
                    fillColor: color,
                    color: '#1f2937',
                    weight: 2,
                    opacity: 1,
                    fillOpacity: 0.8
                });

                marker.bindPopup(`
                    <div class="p-2">
                        <h3 class="font-bold">${respondent.name}</h3>
                        <p><strong>Jenis Warga:</strong> ${respondent.citizenType}</p>
                        <p><strong>Kelurahan:</strong> ${respondent.village}</p>
                    </div>
                `);

                respondentMarkers.addLayer(marker);
            });

            // Add both layers by default
            map.addLayer(responseMarkers);
            map.addLayer(respondentMarkers);

            // Layer toggle
            document.getElementById('btn-responses')?.addEventListener('click', function() {
                if (map.hasLayer(responseMarkers)) {
                    map.removeLayer(responseMarkers);
                } else {
                    map.addLayer(responseMarkers);
                }
            });

            document.getElementById('btn-respondents')?.addEventListener('click', function() {
                if (map.hasLayer(respondentMarkers)) {
                    map.removeLayer(respondentMarkers);
                } else {
                    map.addLayer(respondentMarkers);
                }
            });

            // Fit bounds if we have markers
            if (responses.length > 0 || respondents.length > 0) {
                const allPoints = [...responses, ...respondents].map(p => [p.lat, p.lng]);
                if (allPoints.length > 0) {
                    map.fitBounds(allPoints);
                }
            }
        });
    </script>
    @endpush
</x-filament-panels::page>
