@extends('layouts.guest')

@section('title', 'Sign Up')

@section('content')
    <a href="{{ route('auth.welcome') }}" class="back-link" title="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>

    <div class="auth-screen" id="signup-screen">
        <div class="auth-content">
            <h1>Sign Up</h1>
            <p class="subtitle">Register sekarang untuk dapat mengakses fitur tabungan hewan qurban</p>

            <form action="{{ route('register') }}" method="POST">
                @csrf 

                <div class="form-group">
                    <input type="text" id="name" name="name" class="form-control" placeholder="Name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <input type="email" id="signup-email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="password-wrapper" style="position: relative;">
                        <input type="password" id="signup-password" name="password" class="form-control" placeholder="Password" required>
                        <span id="signup-password-toggle" class="password-toggle-icon">
                            <i id="signup-icon-eye-slash" class="bi bi-eye-slash-fill"></i>
                            <i id="signup-icon-eye" class="bi bi-eye-fill" style="display: none;"></i>
                        </span>
                    </div>
                    @error('password')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <div class="password-wrapper" style="position: relative;">
                         <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                         <span id="confirm-password-toggle" class="password-toggle-icon">
                            <i id="confirm-icon-eye-slash" class="bi bi-eye-slash-fill"></i>
                            <i id="confirm-icon-eye" class="bi bi-eye-fill" style="display: none;"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">SIGNUP</button>
            </form>
            
            <div class="divider">atau</div>
            
            <a href="#" class="btn btn-social">
                <i class="bi bi-google"></i>
                Sign Up with Google
            </a>

        </div>
        
        <footer class="auth-footer">
            <p>Already have an account? <a href="{{ route('login') }}" class="link">Sign In</a></p>
        </footer>
    </div>
@endsection

@push('scripts')
    <script>
        // Helper function generik untuk toggle password
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

        // Setup untuk password pertama
        setupPasswordToggle('signup-password-toggle', 'signup-password', 'signup-icon-eye-slash', 'signup-icon-eye');
        
        // Setup untuk password konfirmasi
        setupPasswordToggle('confirm-password-toggle', 'password_confirmation', 'confirm-icon-eye-slash', 'confirm-icon-eye');
    </script>
@endpush