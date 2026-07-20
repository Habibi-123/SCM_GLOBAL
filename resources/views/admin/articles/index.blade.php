<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0">Kelola Artikel</h4>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-primary btn-sm">+ Tulis Artikel</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="list-group">
        @forelse ($articles as $article)
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">{{ $article->title }}</h6>
                        <small class="text-muted">
                            Oleh {{ $article->user->name ?? '-' }} &bull;
                            {{ $article->published_at ? $article->published_at->format('d M Y') : 'Belum dipublikasikan' }}
                        </small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form method="POST" action="{{ route('admin.articles.destroy', $article) }}"
                              onsubmit="return confirm('Yakin hapus artikel ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted text-center py-4">Belum ada artikel.</p>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $articles->links() }}
    </div>
</x-app-layout>