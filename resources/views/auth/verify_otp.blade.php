@extends('layouts.guest')

@section('title', 'Verifikasi OTP')

@section('content')
    {{-- 1. Kita copy Style dari halaman Register agar tampilan konsisten --}}
    <style>
        .auth-screen {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
            position: relative;
        }

        .auth-content {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        h1 {
            margin-bottom: 0.5rem;
        }

        .subtitle {
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .back-link {
            color: #6c757d;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #0d6efd;
        }
    </style>

    {{-- 2. Tombol Back Sederhana (Kembali ke halaman Register kosong) --}}
    <a href="{{ route('register') }}" class="back-link" title="Kembali ke Register"
        style="position: absolute; top: 20px; left: 20px; z-index: 10;">
        <i class="bi bi-arrow-left" style="font-size: 1.5rem;"></i>
    </a>

    <div class="auth-screen">
        <div class="auth-content">
            <div class="text-center">
                <h1>Verifikasi Akun</h1>
                <p class="subtitle text-muted">Masukkan kode OTP yang dikirim ke Email/WhatsApp {{ $email }}</p>
            </div>

            <form action="{{ route('auth.verify.process') }}" method="POST">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <input type="text" name="otp" class="form-control text-center" placeholder="XXXXXX"
                        maxlength="6" required style="letter-spacing: 5px; font-size: 1.5rem;">
                    @error('otp')
                        <div class="error-message text-danger text-center mt-2 small">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-3">Verifikasi</button>
            </form>

            <div class="mt-3 text-center">
                <form action="{{ route('auth.otp.resend') }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    {{-- Tombol Submit didesain agar terlihat seperti Link biasa --}}
                    <button type="submit"
                        class="btn btn-link small text-muted text-decoration-none p-0 border-0 bg-transparent">
                        Kirim Ulang OTP
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
