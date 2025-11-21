{{-- Extend Layout Dinamis berdasarkan variabel dari Controller --}}
@extends($layout)

@section('title', 'Edit Profil')

@push('styles')
<style>
    .profile-img-container {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto;
    }
    .profile-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .profile-img-btn {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #0d6efd;
        color: white;
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.3s;
    }
    .profile-img-btn:hover { background: #0b5ed7; }
    .form-section {
        background: #fff;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 2px 15px rgba(0,0,0,0.05);
    }
    .google-badge {
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        color: #6c757d;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <h3 class="fw-bold mb-4 text-center">Pengaturan Profil</h3>

            {{-- Alert Sukses/Error --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-section">
                {{-- Form Update --}}
                <form action="{{ $guard == 'pengurus' ? route('pengurus.profile.update') : route('jamaah.profile.update') }}" 
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Bagian Foto Profil --}}
                    <div class="text-center mb-4">
                        <div class="profile-img-container">
                            {{-- Gunakan Accessor foto_url atau avatar_url --}}
                            <img src="{{ $user->avatar_url }}" alt="Foto Profil" class="profile-img" id="previewAvatar">
                            <label for="avatar" class="profile-img-btn" title="Ganti Foto">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="avatar" id="avatar" class="d-none" accept="image/*" onchange="previewImage(this)">
                        </div>
                        <div class="mt-2 text-muted small">Klik ikon kamera untuk mengganti foto</div>
                    </div>

                    {{-- Nama Lengkap (Selalu Bisa Diedit) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    {{-- Logika Tampilan jika Login Google --}}
                    @if($user->google_id)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <div class="mt-2">
                                <span class="google-badge">
                                    <i class="bi bi-google me-2"></i> Terhubung dengan Google
                                </span>
                                <small class="d-block mt-1 text-muted fst-italic">
                                    Email dan Password dikelola oleh Google dan tidak dapat diubah di sini.
                                </small>
                            </div>
                        </div>
                    @else
                        {{-- Jika Login Manual: Tampilkan Input Email & Password --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <hr class="my-4">
                        <h5 class="fw-bold mb-3">Ganti Password</h5>
                        <p class="text-muted small">Kosongkan jika tidak ingin mengubah password.</p>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-3 d-md-flex justify-content-md-between mt-5">
                        
                        {{-- Tombol Logout --}}
                        {{-- order-last order-md-first: Di HP tampil di bawah, Di Desktop tampil di kiri --}}
                        <button type="button" class="btn btn-outline-danger order-last order-md-first" 
                                onclick="document.getElementById('logout-form-profile').submit()">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar / Logout
                        </button>

                        {{-- Tombol Simpan --}}
                        <button type="submit" class="btn btn-primary px-md-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Hidden Logout Form --}}
<form id="logout-form-profile" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<script>
    // Script sederhana untuk preview gambar sebelum upload
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('previewAvatar').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection