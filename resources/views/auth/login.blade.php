<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-control @error('email') is-invalid @enderror" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label">Ingat saya</label>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
                <a class="text-decoration-none small" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
            <button type="submit" class="btn btn-primary">
                Login
            </button>
        </div>

        <hr class="my-4">
        <p class="text-center small mb-0">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-decoration-none">Daftar</a>
        </p>
    </form>
</x-guest-layout>