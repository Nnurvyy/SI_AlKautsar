@extends('layouts.guest')

@section('title', 'Lengkapi Data')

@section('content')
    {{-- CSS agar posisi card ada di tengah (Sama seperti halaman Register) --}}
    <style>
        .auth-screen {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px 0;
        }
        .auth-content {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            padding: 2rem;
        }
    </style>

    <div class="auth-screen">
        <div class="auth-content">
            <div class="text-center mb-4">
                <h1>Lengkapi Data</h1>
                <p class="subtitle text-muted">
                    Halo, akun Google Anda berhasil terhubung. <br>
                    Silakan masukkan nomor WhatsApp untuk melengkapi profil.
                </p>
            </div>

            <form action="{{ route('auth.complete-data.process') }}" method="POST">
                @csrf
                
                {{-- Email (Readonly karena dari Google) --}}
                <div class="form-group mb-3">
                    <label class="form-label small text-muted">Email (dari Google)</label>
                    <input type="email" name="email" class="form-control bg-light" value="{{ $email }}" readonly>
                </div>

                {{-- Input No HP --}}
                <div class="form-group mb-4">
                    <label class="form-label small">Nomor WhatsApp</label>
                    <input type="text" id="no_hp" name="no_hp" class="form-control" 
                           placeholder="Contoh: 08123..." required autofocus
                           inputmode="numeric" pattern="[0-9]*">
                    @error('no_hp')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tombol Simpan & Masuk (Bukan Kirim OTP) --}}
                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    Simpan & Masuk <i class="bi bi-box-arrow-in-right ms-2"></i>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Script agar input No HP hanya bisa angka
        const noHpInput = document.getElementById('no_hp');
        if (noHpInput) {
            noHpInput.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        }
    </script>
@endpush