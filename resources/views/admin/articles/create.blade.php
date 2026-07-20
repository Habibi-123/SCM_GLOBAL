<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Tulis Artikel</h4>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.articles.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Isi Artikel</label>
                    <textarea name="content" rows="8"
                              class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                    @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal Publikasi (opsional, kosongkan jika draft)</label>
                    <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
                           class="form-control @error('published_at') is-invalid @enderror">
                    @error('published_at') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>