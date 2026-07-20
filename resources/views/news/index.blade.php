<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">News Intelligence</h4>
    </x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <label class="form-label small text-muted">Cari berita berdasarkan negara</label>
            <div class="position-relative">
                <input type="text" id="countrySearchInput" class="form-control"
                       placeholder="Ketik nama negara... contoh: Indonesia"
                       value="{{ $selectedCountry?->name }}" autocomplete="off">
                <div id="countryDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index: 1000; display: none; max-height: 250px; overflow-y: auto;"></div>
            </div>

            @if ($selectedCountry)
                <div class="mt-2">
                    <span class="badge bg-primary">
                        Menampilkan berita: {{ $selectedCountry->name }}
                        <a href="{{ route('news.index') }}" class="text-white text-decoration-none ms-1">✕</a>
                    </span>
                </div>
            @endif
        </div>
    </div>

    @if (!$selectedCountry)
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <h3 class="fw-bold text-success mb-0">{{ $sentimentSummary['positive'] ?? 0 }}</h3>
                        <small class="text-muted">Positive</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <h3 class="fw-bold text-secondary mb-0">{{ $sentimentSummary['neutral'] ?? 0 }}</h3>
                        <small class="text-muted">Neutral</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body">
                        <h3 class="fw-bold text-danger mb-0">{{ $sentimentSummary['negative'] ?? 0 }}</h3>
                        <small class="text-muted">Negative</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('news.index') }}"
                       class="btn btn-sm {{ !$selectedCategory ? 'btn-primary' : 'btn-outline-secondary' }}">
                        Semua
                    </a>
                    @foreach ($categories as $category)
                        <a href="{{ route('news.index', ['category' => $category]) }}"
                           class="btn btn-sm {{ $selectedCategory === $category ? 'btn-primary' : 'btn-outline-secondary' }}">
                            {{ ucfirst($category) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="list-group">
        @forelse ($articles as $article)
            <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer"
               class="list-group-item list-group-item-action">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">{{ $article->title }}</h6>
                        <small class="text-muted">
                            {{ $article->source }} &bull;
                            {{ $article->published_at?->diffForHumans() }} &bull;
                            <span class="badge bg-light text-dark border">{{ ucfirst($article->category) }}</span>
                        </small>
                    </div>
                    <span class="badge bg-{{ match($article->sentiment) {
                        'positive' => 'success',
                        'negative' => 'danger',
                        default => 'secondary',
                    } }}">
                        {{ ucfirst($article->sentiment ?? 'neutral') }}
                    </span>
                </div>
            </a>
        @empty
            <p class="text-muted text-center py-4">
                @if ($selectedCountry)
                    Belum ada berita spesifik untuk {{ $selectedCountry->name }}.
                @else
                    Tidak ada berita untuk kategori ini.
                @endif
            </p>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $articles->links() }}
    </div>

    @push('scripts')
    <script>
        const searchInput = document.getElementById('countrySearchInput');
        const dropdown = document.getElementById('countryDropdown');
        let debounceTimer;

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            const query = searchInput.value.trim();

            if (query.length < 2) {
                dropdown.style.display = 'none';
                return;
            }

            // Debounce 300ms supaya tidak fetch tiap ketikan huruf (hemat request)
            debounceTimer = setTimeout(async () => {
                const response = await fetch(`{{ route('news.search-countries') }}?q=${encodeURIComponent(query)}`);
                const countries = await response.json();

                dropdown.innerHTML = '';

                if (countries.length === 0) {
                    dropdown.style.display = 'none';
                    return;
                }

                countries.forEach(c => {
                    const item = document.createElement('a');
                    item.href = `{{ route('news.index') }}?country=${c.code}`;
                    item.className = 'list-group-item list-group-item-action d-flex align-items-center gap-2';
                    item.innerHTML = `<img src="${c.flag_url}" style="width:20px;"> ${c.name}`;
                    dropdown.appendChild(item);
                });

                dropdown.style.display = 'block';
            }, 300);
        });

        // Sembunyikan dropdown kalau klik di luar area search
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-app-layout>