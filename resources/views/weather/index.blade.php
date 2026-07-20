<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Global Weather Monitoring</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex gap-2 flex-wrap">
                <span class="small text-muted align-self-center">Tampilkan:</span>
                <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="high">Badai tinggi</button>
                <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="medium">Badai sedang</button>
                <button class="btn btn-sm btn-outline-success filter-btn" data-filter="low">Aman</button>
                <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="all">Semua</button>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="map" style="height: 600px; border-radius: 8px;"></div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <script>
        const map = L.map('map').setView([10, 20], 2);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(map);

        const riskColors = { high: '#dc3545', medium: '#ffc107', low: '#198754' };
        let allData = [];
        let markers = L.layerGroup().addTo(map);

        function renderMarkers(filter = 'all') {
            markers.clearLayers();

            allData
                .filter(c => filter === 'all' || c.storm_risk === filter)
                .forEach(c => {
                    if (!c.latitude || !c.longitude) return;

                    const color = riskColors[c.storm_risk] || '#6c757d';

                    L.circleMarker([c.latitude, c.longitude], {
                        radius: 8,
                        fillColor: color,
                        color: '#fff',
                        weight: 1,
                        fillOpacity: 0.8,
                    })
                    .bindPopup(`
                        <strong>${c.name}</strong><br>
                        Suhu: ${c.temperature ?? 'N/A'}°C<br>
                        Curah hujan: ${c.rainfall ?? 'N/A'} mm<br>
                        Angin: ${c.wind_speed ?? 'N/A'} km/jam<br>
                        Risiko badai: <span style="color:${color}">${c.storm_risk}</span>
                    `)
                    .addTo(markers);
                });
        }

        async function loadWeather() {
            const response = await fetch('{{ route('weather.data') }}');
            allData = await response.json();
            renderMarkers('all');
        }

        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', () => renderMarkers(btn.dataset.filter));
        });

        loadWeather();
    </script>
    @endpush
</x-app-layout>