<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Dashboard Monitoring</h4>
    </x-slot>

    {{-- Pemilih negara --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}">
                <label class="form-label small text-muted">Pilih negara untuk memulai monitoring</label>
                <select name="country" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Pilih negara --</option>
                    @foreach ($countries as $c)
                        <option value="{{ $c->code }}" {{ ($selectedCode ?? '') === $c->code ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    @if (!$country)
        {{-- Belum ada negara dipilih --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Silakan pilih negara di atas untuk melihat hasil monitoring.</p>
            </div>
        </div>
    @else
        {{-- Header negara terpilih --}}
        <div class="d-flex align-items-center gap-3 mb-4">
            @if ($country->flag_url)
                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" style="width: 40px;">
            @endif
            <h4 class="fw-bold mb-0">{{ $country->name }}</h4>
        </div>

        {{-- 5 Card Statistik --}}
        <div class="row row-cols-2 row-cols-md-5 g-3 mb-4">
            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">GDP</small>
                        <h5 class="fw-semibold mb-0">
                            @if ($country->latestEconomicIndicator?->gdp)
                                ${{ number_format($country->latestEconomicIndicator->gdp / 1e9, 1) }}B
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Inflasi</small>
                        <h5 class="fw-semibold mb-0">
                            {{ $country->latestEconomicIndicator?->inflation ?? 'N/A' }}%
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Populasi</small>
                        <h5 class="fw-semibold mb-0">
                            @if ($country->population)
                                {{ number_format($country->population / 1e6, 1) }}M
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </h5>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Cuaca saat ini</small>
                        <h5 class="fw-semibold mb-0">{{ $latestWeather?->temperature ?? 'N/A' }}°C</h5>
                        <small class="text-muted">Angin: {{ $latestWeather?->wind_speed ?? '-' }} km/jam</small>
                    </div>
                </div>
            </div>

            <div class="col">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block mb-1">Mata uang</small>
                        <h5 class="fw-semibold mb-0">{{ $country->currency_code ?? 'N/A' }}</h5>
                        @if ($currencyRate)
                            <small class="text-muted">1 USD = {{ number_format($currencyRate->rate, 2) }}</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Risk Score + Grafik Tren --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Risk score saat ini</h6>

                        @if ($country->latestRiskScore)
                            @php
                                $level = $country->latestRiskScore->risk_level;
                                $badgeClass = match($level) {
                                    'high' => 'danger',
                                    'medium' => 'warning',
                                    default => 'success',
                                };
                            @endphp

                            <h2 class="fw-bold mb-2">
                                {{ $country->latestRiskScore->total_score }}
                                <span class="badge bg-{{ $badgeClass }} fs-6 align-middle">
                                    {{ ucfirst($level) }}
                                </span>
                            </h2>

                            <table class="table table-sm mb-0">
                                <tr>
                                    <td>Weather</td>
                                    <td class="text-end">{{ $country->latestRiskScore->weather_score }}</td>
                                </tr>
                                <tr>
                                    <td>Inflation</td>
                                    <td class="text-end">{{ $country->latestRiskScore->inflation_score }}</td>
                                </tr>
                                <tr>
                                    <td>News</td>
                                    <td class="text-end">{{ $country->latestRiskScore->news_score }}</td>
                                </tr>
                                <tr>
                                    <td>Currency</td>
                                    <td class="text-end">{{ $country->latestRiskScore->exchange_score }}</td>
                                </tr>
                            </table>
                        @else
                            <p class="text-muted">Belum ada data risk score.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Tren risk score</h6>

                        @if ($riskHistory->count() > 1)
                            <canvas id="riskTrendChart" height="100"></canvas>
                        @else
                            <p class="text-muted">Data histori belum cukup untuk menampilkan grafik tren (minimal 2 data).</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

                {{-- Grafik Tren Ekonomi (GDP, Inflasi, Ekspor vs Impor) --}}
        @if ($economicHistory->count() > 1)
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">GDP Trend</h6>
                        <canvas id="gdpChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Inflation Trend</h6>
                        <canvas id="inflationChart" height="150"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Ekspor vs Impor</h6>
                        <canvas id="tradeChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Mini Peta Pelabuhan --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h6 class="fw-semibold mb-3">Status pelabuhan di {{ $country->name }}</h6>

                @if ($ports->isNotEmpty())
                    <div id="portsMiniMap" style="height: 300px; border-radius: 8px;"></div>
                    <small class="text-muted d-block mt-2">{{ $ports->count() }} pelabuhan terdaftar</small>
                @else
                    <p class="text-muted mb-0">Tidak ada data pelabuhan untuk negara ini.</p>
                @endif
            </div>
        </div>

        {{-- Berita terkait --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-semibold mb-0">Berita terkait {{ $country->name }}</h6>
                    <form method="POST" action="{{ route('countries.refresh-news', $country->code) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                            🔄 Refresh Berita
                        </button>
                    </form>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-sm py-2">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-warning alert-sm py-2">{{ session('error') }}</div>
                @endif

                @forelse ($countryNews as $news)
                    <div class="d-flex justify-content-between align-items-start mb-2 pb-2 border-bottom">
                        <div>
                            <a href="{{ $news->url }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                                {{ $news->title }}
                            </a>
                            <br>
                            <small class="text-muted">{{ $news->source }}</small>
                        </div>
                        <span class="badge bg-{{ match($news->sentiment) {
                            'positive' => 'success', 'negative' => 'danger', default => 'secondary',
                        } }}">
                            {{ ucfirst($news->sentiment ?? 'neutral') }}
                        </span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">Belum ada berita spesifik untuk negara ini.</p>
                @endforelse
            </div>
        </div>

        @push('scripts')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

        @if ($riskHistory->count() > 1)
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script>
            new Chart(document.getElementById('riskTrendChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($riskHistory->pluck('calculated_at')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M H:i'))) !!},
                    datasets: [{
                        label: 'Total risk score',
                        data: {!! json_encode($riskHistory->pluck('total_score')) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { min: 0, max: 100 } }
                }
            });
        </script>
        @endif

                @if ($economicHistory->count() > 1)
        <script>
            const econYears = {!! json_encode($economicHistory->pluck('year')) !!};

            new Chart(document.getElementById('gdpChart'), {
                type: 'bar',
                data: {
                    labels: econYears,
                    datasets: [{
                        label: 'GDP (USD)',
                        data: {!! json_encode($economicHistory->pluck('gdp')) !!},
                        backgroundColor: '#0d6efd',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });


            new Chart(document.getElementById('inflationChart'), {
                type: 'line',
                data: {
                    labels: econYears,
                    datasets: [{
                        label: 'Inflasi (%)',
                        data: {!! json_encode($economicHistory->pluck('inflation')) !!},
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220,53,69,0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });


            new Chart(document.getElementById('tradeChart'), {
                type: 'bar',
                data: {
                    labels: econYears,
                    datasets: [
                        {
                            label: 'Ekspor',
                            data: {!! json_encode($economicHistory->pluck('exports')) !!},
                            backgroundColor: '#198754',
                        },
                        {
                            label: 'Impor',
                            data: {!! json_encode($economicHistory->pluck('imports')) !!},
                            backgroundColor: '#ffc107',
                        }
                    ]
                },
                options: {
                    responsive: true
                }
            });

        </script>
        @endif

        @if ($ports->isNotEmpty())
        <script>
            const portsMap = L.map('portsMiniMap').setView([{{ $country->latitude }}, {{ $country->longitude }}], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(portsMap);

            const ports = {!! json_encode($ports->map(fn($p) => [
                'name' => $p->name,
                'lat' => $p->latitude,
                'lng' => $p->longitude,
            ])) !!};

            ports.forEach(port => {
                if (!port.lat || !port.lng) return;
                L.marker([port.lat, port.lng]).bindPopup(`<strong>${port.name}</strong>`).addTo(portsMap);
            });
        </script>
        @endif
        @endpush
    @endif
</x-app-layout>