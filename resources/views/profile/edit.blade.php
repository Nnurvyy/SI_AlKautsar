{{-- Extend Layout Dinamis --}}
@extends($layout)

@section('title', 'Edit Profil')

{{-- 1. LOAD LIBRARY CROPPER (CSS) --}}
@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<style>
    /* Style Profil */
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
        border-radius: 50%; /* Ini yang bikin jadi bulet */
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

    /* Style Khusus Modal Cropper */
    .img-container {
        max-height: 500px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        background-color: #333; /* Background gelap biar fokus */
    }
    .img-container img {
        max-width: 100%;
    }
    /* Opsional: Membuat tampilan cropper guide jadi lingkaran (Visual Only) */
    .cropper-view-box,
    .cropper-face {
        border-radius: 50%;
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
                      method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('PUT')

                    {{-- 2. INPUT HIDDEN UNTUK HASIL CROP --}}
                    {{-- Ini akan berisi string Base64 gambar yang sudah dicrop --}}
                    <input type="hidden" name="cropped_image" id="cropped_image">

                    {{-- Bagian Foto Profil --}}
                    <div class="text-center mb-4">
                        <div class="profile-img-container">
                            <img src="{{ $user->avatar_url }}" alt="Foto Profil" class="profile-img" id="previewAvatar">
                            
                            <label for="avatarInput" class="profile-img-btn" title="Ganti Foto">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            
                            {{-- Input File Asli (Di-handle JS) --}}
                            <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                        </div>
                        <div class="mt-2 text-muted small">Klik ikon kamera untuk mengganti foto</div>
                    </div>

                    {{-- Nama Lengkap --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>

                    {{-- Logika Tampilan Login Google --}}
                    @if($user->google_id)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->email }}" disabled>
                            <div class="mt-2">
                                <span class="google-badge">
                                    <i class="bi bi-google me-2"></i> Terhubung dengan Google
                                </span>
                            </div>
                        </div>
                    @else
                        {{-- Login Manual --}}
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
                        <button type="button" class="btn btn-outline-danger order-last order-md-first" 
                                onclick="document.getElementById('logout-form-profile').submit()">
                            <i class="bi bi-box-arrow-right me-1"></i> Keluar / Logout
                        </button>

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

{{-- 3. MODAL CROPPER (Sama seperti Artikel tapi disesuaikan) --}}
<div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-3 px-4">
                <h5 class="modal-title fw-bold">Sesuaikan Foto Profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 mt-3">
                <div class="img-container">
                    <img id="imageToCrop" style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" id="btnCropImage">
                    <i class="bi bi-check-lg me-1"></i> Potong & Gunakan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- 4. SCRIPT LOGIC CROPPER --}}
@push('scripts')
{{-- Load Library JS Cropper --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Elemen
    const avatarInput = document.getElementById('avatarInput');
    const imageToCrop = document.getElementById('imageToCrop');
    const previewAvatar = document.getElementById('previewAvatar');
    const croppedImageInput = document.getElementById('cropped_image');
    
    // Modal
    const modalCropElement = document.getElementById('modalCrop');
    const modalCrop = new bootstrap.Modal(modalCropElement);
    const btnCropImage = document.getElementById('btnCropImage');
    
    let cropper = null;

    // 1. Saat user memilih file -> Buka Modal
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validasi tipe gambar
            if (!file.type.startsWith('image/')) {
                alert('Harap pilih file gambar.');
                return;
            }

            const reader = new FileReader();
            reader.onload = (ev) => {
                imageToCrop.src = ev.target.result;
                modalCrop.show(); // Tampilkan modal
            }
            reader.readAsDataURL(file);
        }
    });

    // 2. Saat Modal Tampil -> Inisialisasi Cropper
    modalCropElement.addEventListener('shown.bs.modal', () => {
        if(cropper) cropper.destroy(); // Hapus instance lama jika ada
        
        cropper = new Cropper(imageToCrop, {
            aspectRatio: 1 / 1, // RASIO 1:1 (KOTAK) -> CSS membuatnya jadi BULAT
            viewMode: 1,
            autoCropArea: 1,
            dragMode: 'move',
            background: false,
        });
    });

    // 3. Saat Modal Tertutup -> Bersihkan Cropper & Input File
    modalCropElement.addEventListener('hidden.bs.modal', () => {
        if(cropper) {
            cropper.destroy();
            cropper = null;
        }
        // Jangan hapus value avatarInput jika user menekan 'Batal' 
        // agar form masih bisa disubmit tanpa error (opsional), 
        // tapi sebaiknya di-reset kalau batal.
        if (!croppedImageInput.value) {
             avatarInput.value = ''; 
        }
    });

    // 4. Tombol "Potong & Gunakan" diklik
    btnCropImage.addEventListener('click', () => {
        if (!cropper) return;

        // Ambil hasil crop sebagai Data URL (Base64)
        // Kita resize sedikit agar tidak terlalu besar (misal 500x500)
        const canvas = cropper.getCroppedCanvas({
            width: 500,
            height: 500,
        });

        const croppedDataUrl = canvas.toDataURL('image/jpeg'); // atau image/png

        // A. Tampilkan di Preview (Biar user lihat hasilnya bulet)
        previewAvatar.src = croppedDataUrl;

        // B. Masukkan data Base64 ke Input Hidden untuk dikirim ke Controller
        croppedImageInput.value = croppedDataUrl;

        // C. Tutup Modal
        modalCrop.hide();
    });
});
</script>
@endpush
@endsection