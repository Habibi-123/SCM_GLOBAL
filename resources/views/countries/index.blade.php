<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Global Country Dashboard</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('countries.index') }}" class="d-flex gap-2">
                <input type="text" name="search" value="{{ $search }}"
                       class="form-control" placeholder="Cari negara... contoh: Indonesia">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
        </div>
    </div>

    <div class="row g-3">
        @forelse ($countries as $country)
            <div class="col-md-3 col-sm-6">
                <a href="{{ route('countries.show', $country->code) }}" class="text-decoration-none">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            @if ($country->flag_url)
                                <img src="{{ $country->flag_url }}" alt="{{ $country->name }}"
                                     class="mb-2" style="width: 60px; height: 40px; object-fit: cover;">
                            @endif
                            <h6 class="fw-semibold text-dark mb-1">{{ $country->name }}</h6>
                            <small class="text-muted">{{ $country->region }}</small>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted text-center py-4">Tidak ada negara ditemukan.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $countries->links() }}
    </div>
</x-app-layout>