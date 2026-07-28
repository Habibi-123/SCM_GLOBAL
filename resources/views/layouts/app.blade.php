<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Globalis — Global Supply Chain Risk Intelligence Platform. Monitor risiko rantai pasok secara real-time dari seluruh dunia.">
    <meta name="theme-color" content="#00c8c8">
    <title>Globalis — @yield('title', 'Supply Chain Risk Intelligence')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="min-vh-100 d-flex flex-column">

        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white border-bottom py-3">
                <div class="container">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="container py-4 flex-grow-1">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 animate-fade-in" role="alert">
                    ✅ {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4 animate-fade-in" role="alert">
                    ⚠️ {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </main>

        <footer class="py-3 mt-auto" style="border-top: 1px solid var(--border-aqua);">
            <div class="container text-center" style="color: var(--text-muted); font-size: 0.8rem;">
                🌍 <span style="color: var(--aqua-primary); font-weight: 600;">Globalis</span>
                — Global Supply Chain Risk Intelligence Platform
                &nbsp;·&nbsp; {{ now()->year }}
            </div>
        </footer>

    </div>
    @stack('scripts')
</body>
</html>