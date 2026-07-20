<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Currency Impact Dashboard</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h6 class="fw-semibold mb-3">Tren kurs: {{ $base }} → {{ $target }}</h6>

            @if ($history->count() > 1)
                <canvas id="currencyChart" height="80"></canvas>
            @else
                <p class="text-muted">Data histori belum cukup untuk grafik.</p>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Mata uang</th>
                        <th class="text-end pe-3">Kurs (1 {{ $base }} =)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($latestRates as $rate)
                        <tr>
                            <td class="ps-3">{{ $rate->target_currency }}</td>
                            <td class="text-end pe-3">{{ number_format($rate->rate, 4) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $latestRates->links() }}
    </div>

    @if ($history->count() > 1)
        @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
        <script>
            new Chart(document.getElementById('currencyChart'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($history->pluck('fetched_at')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))) !!},
                    datasets: [{
                        label: '{{ $base }} to {{ $target }}',
                        data: {!! json_encode($history->pluck('rate')) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.3,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } }
                }
            });
        </script>
        @endpush
    @endif
</x-app-layout>