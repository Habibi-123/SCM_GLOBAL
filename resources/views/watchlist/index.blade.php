<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Favorite Monitoring List</h4>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
        @forelse ($watchedCountries as $country)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            @if ($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}" style="width: 32px;">
                            @endif
                            <h6 class="fw-semibold mb-0">{{ $country->name }}</h6>
                        </div>

                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Inflasi</small>
                                <strong>{{ $country->latestEconomicIndicator?->inflation ?? 'N/A' }}%</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Risk Score</small>
                                @if ($country->latestRiskScore)
                                    <span class="badge bg-{{ match($country->latestRiskScore->risk_level) {
                                        'high' => 'danger', 'medium' => 'warning', default => 'success',
                                    } }}">
                                        {{ $country->latestRiskScore->total_score }}
                                    </span>
                                @else
                                    <strong>N/A</strong>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('countries.show', $country->code) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                Detail
                            </a>
                            <form method="POST" action="{{ route('watchlist.destroy', $country->code) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted text-center py-4">
                    Belum ada negara di watchlist. Kunjungi
                    <a href="{{ route('countries.index') }}">Country Dashboard</a>
                    untuk menambahkan.
                </p>
            </div>
        @endforelse
    </div>
</x-app-layout>