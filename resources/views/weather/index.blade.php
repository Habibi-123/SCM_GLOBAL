<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Global Weather Monitoring</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Pilih negara</label>
                    <select id="countrySelect" class="form-select">
                        <option value="">-- Lihat semua negara --</option>
                        @foreach ($countries as $c)
                            <option value="{{ $c->code }}" data-lat="{{ $c->latitude }}" data-lng="{{ $c->longitude }}">
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 flex-wrap">
                        <span class="small text-muted align-self-center">Filter risiko:</span>
                        <button class="btn btn-sm btn-outline-danger filter-btn" data-filter="high">Tinggi</button>
                        <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="medium">Sedang</button>
                        <button class="btn btn-sm btn-outline-success filter-btn" data-filter="low">Aman</button>
                        <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="all">Semua</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="selectedCountryCard" class="card border-0 shadow-sm mb-3" style="display: none;">
        <div class="card-body">
            <h6 class="fw-semibold mb-3" id="selectedCountryName"></h6>
            <div class="row text-center">
                <div class="col-3">
                    <small class="text-muted d-block">Suhu</small>
                    <strong id="selectedTemp">-</strong>
                </div>
                <div class="col-3">
                    <small class="text-muted d-block">Curah hujan</small>
                    <strong id="selectedRainfall">-</strong>
                </div>
                <div class="col-3">
                    <small class="text-muted d-block">Angin</small>
                    <strong id="selectedWind">-</strong>
                </div>
                <div class="col-3">
                    <small class="text-muted d-block">Risiko badai</small>
                    <strong id="selectedRisk">-</strong>
                </div>
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
        const riskLabels = { high: 'Tinggi', medium: 'Sedang', low: 'Aman' };
        let allData = [];
        let markers = L.layerGroup().addTo(map);
        let markerByCode = {};

        function renderMarkers(filter = 'all') {
            markers.clearLayers();
            markerByCode = {};

            allData
                .filter(c => filter === 'all' || c.storm_risk === filter)
                .forEach(c => {
                    if (!c.latitude || !c.longitude) return;

                    const color = riskColors[c.storm_risk] || '#6c757d';

                    const marker = L.circleMarker([c.latitude, c.longitude], {
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

                    markerByCode[c.code] = marker;
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

        // Fitur utama: fokus peta ke negara yang dipilih + tampilkan detail cuacanya
        document.getElementById('countrySelect').addEventListener('change', function () {
            const code = this.value;
            const card = document.getElementById('selectedCountryCard');

            if (!code) {
                card.style.display = 'none';
                map.setView([10, 20], 2);
                return;
            }

            const country = allData.find(c => c.code === code);
            if (!country) return;

            // Zoom & fokus peta ke lokasi negara terpilih
            map.flyTo([country.latitude, country.longitude], 5, { duration: 1 });

            // Buka popup marker negara itu (kalau sedang tampil sesuai filter aktif)
            if (markerByCode[code]) {
                markerByCode[code].openPopup();
            }

            // Tampilkan panel detail di atas peta
            document.getElementById('selectedCountryName').textContent = country.name;
            document.getElementById('selectedTemp').textContent = (country.temperature ?? 'N/A') + '°C';
            document.getElementById('selectedRainfall').textContent = (country.rainfall ?? 'N/A') + ' mm';
            document.getElementById('selectedWind').textContent = (country.wind_speed ?? 'N/A') + ' km/jam';
            document.getElementById('selectedRisk').textContent = riskLabels[country.storm_risk] ?? 'N/A';
            card.style.display = 'block';
        });

        loadWeather();
    </script>
    @endpush
</x-app-layout>