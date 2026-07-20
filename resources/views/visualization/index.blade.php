<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Data Visualization Dashboard</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="d-flex gap-2 align-items-end">
                <div class="flex-grow-1">
                    <label class="form-label small text-muted">Pilih negara</label>
                    <select name="country" class="form-select" onchange="this.form.submit()">
                        @foreach ($countries as $c)
                            <option value="{{ $c->code }}" {{ $selectedCode === $c->code ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    @if ($country)
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">GDP Trend — {{ $country->name }}</h6>
                        @if ($economicHistory->count() > 1)
                            <canvas id="gdpChart" height="150"></canvas>
                        @else
                            <p class="text-muted">Data belum cukup (minimal 2 tahun) untuk grafik tren.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Inflation Trend — {{ $country->name }}</h6>
                        @if ($economicHistory->count() > 1)
                            <canvas id="inflationChart" height="150"></canvas>
                        @else
                            <p class="text-muted">Data belum cukup (minimal 2 tahun) untuk grafik tren.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Ekspor vs Impor — {{ $country->name }}</h6>
                        @if ($economicHistory->count() > 1)
                            <canvas id="tradeChart" height="150"></canvas>
                        @else
                            <p class="text-muted">Data belum cukup untuk grafik.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Risk Trend — {{ $country->name }}</h6>
                        @if ($riskHistory->count() > 1)
                            <canvas id="riskChart" height="150"></canvas>
                        @else
                            <p class="text-muted">Data histori risk score belum cukup.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($country && $economicHistory->count() > 1)
        @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script>
            const years = {!! json_encode($economicHistory->pluck('year')) !!};

            new Chart(document.getElementById('gdpChart'), {
                type: 'bar',
                data: {
                    labels: years,
                    datasets: [{
                        label: 'GDP (USD)',
                        data: {!! json_encode($economicHistory->pluck('gdp')) !!},
                        backgroundColor: '#0d6efd',
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            new Chart(document.getElementById('inflationChart'), {
                type: 'line',
                data: {
                    labels: years,
                    datasets: [{
                        label: 'Inflasi (%)',
                        data: {!! json_encode($economicHistory->pluck('inflation')) !!},
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } } }
            });

            new Chart(document.getElementById('tradeChart'), {
                type: 'bar',
                data: {
                    labels: years,
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
                options: { responsive: true }
            });
        </script>
        @endpush
    @endif

    @if ($country && $riskHistory->count() > 1)
        @push('scripts')
        <script>
            new Chart(document.getElementById('riskChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($riskHistory->pluck('calculated_at')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M H:i'))) !!},
                    datasets: [{
                        label: 'Risk Score',
                        data: {!! json_encode($riskHistory->pluck('total_score')) !!},
                        borderColor: '#6610f2',
                        backgroundColor: 'rgba(102, 16, 242, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { min: 0, max: 100 } } }
            });
        </script>
        @endpush
    @endif
</x-app-layout>