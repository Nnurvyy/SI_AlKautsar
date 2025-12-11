@extends('layouts.guest')

@section('title', 'Sign In')

@section('content')
    <a href="{{ route('auth.welcome') }}" class="back-link" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div class="auth-screen" id="login-screen">
        <div class="auth-content">
            <h1>Sign In</h1>
            <p class="subtitle">Sign In untuk dapat mengakses fitur tabungan hewan qurban</p>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <input type="email" id="email" name="email" class="form-control" placeholder="Email Address"
                        value="{{ old('email') }}" required autofocus>

                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="password-wrapper" style="position: relative;">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                            required>

                        <span id="password-toggle" class="password-toggle-icon">
                            <i id="icon-eye-slash" class="bi bi-eye-slash-fill"></i>
                            <i id="icon-eye" class="bi bi-eye-fill" style="display: none;"></i>
                        </span>
                    </div>
                </div>

                <div class="form-options">
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary">SIGN IN</button>
            </form>

            <div class="divider">atau</div>

            {{ route('auth.google.redirect') }}" -->
            <a href="{{ route('auth.google.redirect') }}" class="btn btn-social">
                <i class="bi bi-google"></i>
                Sign In with Google
            </a>

        </div>

        <footer class="auth-footer">
            <p>Don't have an account? <a href="{{ route('register') }}" class="link">Sign Up</a></p>
        </footer>
    </div>
@endsection

@push('scripts')
    {{-- Script password toggle --}}
    <script>
        const toggleButton = document.getElementById('password-toggle');
        const passwordInput = document.getElementById('password');
        const iconEye = document.getElementById('icon-eye');
        const iconEyeSlash = document.getElementById('icon-eye-slash');

        if (toggleButton) {
            toggleButton.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    iconEye.style.display = 'inline-block';
                    iconEyeSlash.style.display = 'none';
                } else {
                    passwordInput.type = 'password';
                    iconEye.style.display = 'none';
                    iconEyeSlash.style.display = 'inline-block';
                }
            });
        }
    </script>
@endpush
