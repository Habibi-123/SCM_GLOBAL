<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                @if ($country->flag_url)
                    <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" style="width: 40px;">
                @endif
                <h4 class="fw-bold mb-0">{{ $country->name }}</h4>
            </div>

            @auth
                @if (auth()->user()->watchedCountries->contains($country->id))
                    <form method="POST" action="{{ route('watchlist.destroy', $country->code) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            ★ Hapus dari watchlist
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('watchlist.store', $country->code) }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm">
                            ☆ Tambah ke watchlist
                        </button>
                    </form>
                @endif
            @endauth
        </div>
    </x-slot>

    {{-- 5 Card Statistik: GDP, Inflasi, Populasi, Cuaca, Mata uang --}}
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
                    <h5 class="fw-semibold mb-0">
                        {{ $latestWeather?->temperature ?? 'N/A' }}°C
                    </h5>
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
    <div class="row g-3">
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

    {{-- Berita terkait negara ini --}}
    <div class="card border-0 shadow-sm mt-4">
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

    @if ($riskHistory->count() > 1)
        @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script>
            const ctx = document.getElementById('riskTrendChart');
            new Chart(ctx, {
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
        @endpush
    @endif
</x-app-layout>