<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Tambah Pelabuhan</h4>
    </x-slot>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ports.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Negara</label>
                    <select name="country_id" class="form-select @error('country_id') is-invalid @enderror">
                        <option value="">-- Pilih negara --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Pelabuhan</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">UN/LOCODE (opsional)</label>
                    <input type="text" name="unlocode" value="{{ old('unlocode') }}"
                           class="form-control @error('unlocode') is-invalid @enderror">
                    @error('unlocode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Latitude</label>
                        <input type="number" step="any" name="latitude" value="{{ old('latitude') }}"
                               class="form-control @error('latitude') is-invalid @enderror">
                        @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Longitude</label>
                        <input type="number" step="any" name="longitude" value="{{ old('longitude') }}"
                               class="form-control @error('longitude') is-invalid @enderror">
                        @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.ports.index') }}" class="btn btn-outline-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>