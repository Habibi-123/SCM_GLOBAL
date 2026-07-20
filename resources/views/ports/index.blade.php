<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Port Location Dashboard</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" id="searchInput" class="form-control"
                           placeholder="Cari nama pelabuhan... contoh: Jakarta">
                </div>

                <div class="col-md-4">
                    <select id="countryFilter" class="form-select">
                        <option value="">Semua negara</option>

                        @foreach ($countries as $c)
                            <option value="{{ $c->code }}">
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button id="searchBtn" class="btn btn-primary w-100">
                        Cari
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div id="map" style="height: 600px; border-radius: 8px;"></div>
        </div>
    </div>

    <p class="text-muted small mt-2">
        Menampilkan maksimal 500 pelabuhan sekaligus. Gunakan pencarian untuk mempersempit hasil.
    </p>

    @push('scripts')
        <link rel="stylesheet"
              href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

        <script>
            const map = L.map('map').setView([0, 110], 3);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            let markers = L.layerGroup().addTo(map);

            async function loadPorts(params = {}) {
                markers.clearLayers();

                const query = new URLSearchParams(params).toString();

                const response = await fetch(`{{ route('ports.data') }}?${query}`);
                const ports = await response.json();

                ports.forEach(port => {
                    if (!port.latitude || !port.longitude) return;

                    const countryName = port.country
                        ? port.country.name
                        : 'Tidak diketahui';

                    L.marker([port.latitude, port.longitude])
                        .bindPopup(`
                            <strong>${port.name}</strong><br>
                            ${countryName}
                        `)
                        .addTo(markers);
                });

                document.querySelector('.text-muted.small').textContent =
                    `Menampilkan ${ports.length} pelabuhan.`;
            }

            // Load data awal
            loadPorts();

            // Tombol Cari
            document.getElementById('searchBtn').addEventListener('click', () => {
                loadPorts({
                    search: document.getElementById('searchInput').value,
                    country_code: document.getElementById('countryFilter').value
                });
            });

            // Tekan Enter
            document.getElementById('searchInput').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    document.getElementById('searchBtn').click();
                }
            });
        </script>
    @endpush
</x-app-layout>