<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0">Kelola Pelabuhan</h4>
            <a href="{{ route('admin.ports.create') }}" class="btn btn-primary btn-sm">+ Tambah Pelabuhan</a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Cari nama pelabuhan...">
                <button type="submit" class="btn btn-outline-primary">Cari</button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">Nama</th>
                        <th>Negara</th>
                        <th>Koordinat</th>
                        <th class="text-end pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ports as $port)
                        <tr>
                            <td class="ps-3">{{ $port->name }}</td>
                            <td>{{ $port->country->name ?? '-' }}</td>
                            <td><small class="text-muted">{{ $port->latitude }}, {{ $port->longitude }}</small></td>
                            <td class="text-end pe-3">
                                <a href="{{ route('admin.ports.edit', $port) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST" action="{{ route('admin.ports.destroy', $port) }}" class="d-inline"
                                      onsubmit="return confirm('Yakin hapus pelabuhan {{ $port->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $ports->links() }}
    </div>
</x-app-layout>