<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="d-flex align-items-center justify-content-center bg-light" style="min-height: 100vh;">
    <div class="container" style="max-width: 480px;">
        <div class="text-center mb-4">
            <a href="/" class="text-decoration-none">
                <h3 class="fw-bold text-dark">Supply Chain Risk Platform</h3>
            </a>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>