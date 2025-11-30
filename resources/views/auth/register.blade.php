@extends('layouts.guest')

@section('title', 'Sign Up')

@section('content')
    {{-- CSS Tambahan untuk layout yang lebih proporsional --}}
    <style>
        /* Pastikan content ada di tengah secara vertikal */
        .auth-screen {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0; /* Tambah padding atas bawah agar tidak mentok saat di mobile */
        }
        
        /* Atur lebar dan spacing card */
        .auth-content {
            width: 100%;
            max-width: 400px; /* Batasi lebar agar rapi */
            margin: 0 auto;
            padding: 2rem;
            /* Jika background putih card diperlukan, tambahkan bg-white & shadow */
        }

        /* Jarak antar form group */
        .form-group {
            margin-bottom: 1rem; /* Perkecil sedikit margin bottom default jika terlalu renggang */
        }
        
        h1 { margin-bottom: 0.5rem; }
        .subtitle { margin-bottom: 1.5rem; font-size: 0.9rem; }
    </style>

    <a href="{{ route('auth.welcome') }}" class="back-link" title="Kembali" style="position: absolute; top: 20px; left: 20px; z-index: 10;">
        <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
    </a>

    <div class="auth-screen" id="signup-screen">
        <div class="auth-content">
            <div class="text-center mb-4">
                <h1>Sign Up</h1>
                <p class="subtitle text-muted">Register sekarang untuk dapat mengakses fitur tabungan hewan qurban</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                    <div class="error-message text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <input type="email" id="signup-email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required>
                    @error('email')
                    <div class="error-message text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    {{-- Tambahkan id, inputmode="numeric", dan pattern --}}
                    <input type="text" id="no_hp" name="no_hp" class="form-control" 
                        placeholder="Nomor WhatsApp (Contoh: 08123...)" 
                        value="{{ old('no_hp') }}" required 
                        inputmode="numeric" pattern="[0-9]*">
                    @error('no_hp')
                    <div class="error-message text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <div class="password-wrapper position-relative">
                        <input type="password" id="signup-password" name="password" class="form-control" placeholder="Password" required>
                        <span id="signup-password-toggle" class="password-toggle-icon position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
                            <i id="signup-icon-eye-slash" class="bi bi-eye-slash-fill text-muted"></i>
                            <i id="signup-icon-eye" class="bi bi-eye-fill text-muted" style="display: none;"></i>
                        </span>
                    </div>
                    @error('password')
                    <div class="error-message text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <div class="password-wrapper position-relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                        <span id="confirm-password-toggle" class="password-toggle-icon position-absolute top-50 end-0 translate-middle-y me-3" style="cursor: pointer;">
                            <i id="confirm-icon-eye-slash" class="bi bi-eye-slash-fill text-muted"></i>
                            <i id="confirm-icon-eye" class="bi bi-eye-fill text-muted" style="display: none;"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">SIGN UP</button>
            </form>

            <div class="divider my-3 text-center text-muted small">atau</div>

            <a href="{{ route('auth.google.redirect') }}" class="btn btn-social">
                <i class="bi bi-google"></i>
                Sign Up with Google
            </a>

            <footer class="auth-footer mt-4 text-center">
                <p class="mb-0 text-muted">Already have an account? <a href="{{ route('login') }}" class="link fw-bold text-decoration-none">Sign In</a></p>
            </footer>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const noHpInput = document.getElementById('no_hp');
        if (noHpInput) {
            noHpInput.addEventListener('input', function(e) {
                // Hapus karakter apa pun yang BUKAN angka 0-9
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }

        function setupPasswordToggle(toggleId, inputId, iconSlashId, iconEyeId) {
            const toggleButton = document.getElementById(toggleId);
            const passwordInput = document.getElementById(inputId);
            const iconEye = document.getElementById(iconEyeId);
            const iconEyeSlash = document.getElementById(iconSlashId);

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
        }

        setupPasswordToggle('signup-password-toggle', 'signup-password', 'signup-icon-eye-slash', 'signup-icon-eye');
        setupPasswordToggle('confirm-password-toggle', 'password_confirmation', 'confirm-icon-eye-slash', 'confirm-icon-eye');
    </script>
@endpush