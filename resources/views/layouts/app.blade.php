<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <div class="min-vh-100 d-flex flex-column">

        @include('layouts.navigation')

        @if (isset($header))
            <header class="bg-white border-bottom shadow-sm py-3">
                <div class="container">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main class="container py-4 flex-grow-1">
            {{ $slot }}
        </main>

    </div>
    @stack('scripts')
</body>
</html>