<x-app-layout>
    <x-slot name="header">
        <h4 class="fw-bold mb-0">Admin Dashboard</h4>
    </x-slot>

    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="fw-bold mb-0">{{ $stats['total_countries'] }}</h3>
                    <small class="text-muted">Negara</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="fw-bold mb-0">{{ $stats['total_users'] }}</h3>
                    <small class="text-muted">Users</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="fw-bold mb-0">{{ $stats['total_ports'] }}</h3>
                    <small class="text-muted">Pelabuhan</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="fw-bold mb-0">{{ $stats['total_articles'] }}</h3>
                    <small class="text-muted">Artikel</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="fw-bold mb-0">{{ $stats['total_news'] }}</h3>
                    <small class="text-muted">Berita</small>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="card border-0 shadow-sm text-center bg-danger bg-opacity-10">
                <div class="card-body">
                    <h3 class="fw-bold mb-0 text-danger">{{ $stats['high_risk_countries'] }}</h3>
                    <small class="text-muted">High Risk</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-4">
            <a href="{{ route('admin.users.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body">
                    <h6 class="fw-semibold text-dark mb-1">Kelola Users</h6>
                    <small class="text-muted">Tambah, edit, hapus user & atur role</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.ports.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body">
                    <h6 class="fw-semibold text-dark mb-1">Kelola Pelabuhan</h6>
                    <small class="text-muted">Kelola dataset pelabuhan</small>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.articles.index') }}" class="card border-0 shadow-sm text-decoration-none h-100">
                <div class="card-body">
                    <h6 class="fw-semibold text-dark mb-1">Kelola Artikel</h6>
                    <small class="text-muted">Tulis & kelola artikel analisis</small>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>