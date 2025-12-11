@extends('layouts.app')

@section('title', 'Donasi')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- 1. Load CSS Cropper.js --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

<div class="container-fluid p-4">

    {{-- HEADER & SEARCH --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">
            {{-- Filter Status --}}
            <select class="form-select rounded-pill ps-3" id="statusFilter" style="width: 160px; border-color: #e5e7eb;">
                <option value="aktif" selected>Status: Aktif</option>
                <option value="lewat">Status: Lewat</option>
                <option value="semua">Semua Status</option>
            </select>

            {{-- Search Bar --}}
            <div class="input-group" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill" placeholder="Cari program donasi...">
            </div>
        </div>

        <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" id="btnTambahDonasi">
            <i class="bi bi-plus-lg me-2"></i> Program Baru
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-0">Total Donasi Terkumpul</h5>
                <small class="text-muted">Akumulasi seluruh pemasukan donasi</small>
            </div>
            
            <h3 class="fw-bold text-success mb-0">
                Rp <span id="totalDonasiHeader">{{ number_format($totalDonasi ?? 0, 0, ',', '.') }}</span>
            </h3>
        </div>
    </div>

    {{-- TABEL UTAMA --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelDonasi">
                    <thead>
                        <tr style="height: 50px;">
                            <th class="text-center ps-4 rounded-top-left">No</th>
                            <th class="text-center">Poster</th>
                            <th>Nama Program</th>
                            <th class="text-center cursor-pointer" id="sortMulai">Mulai <i class="bi bi-arrow-down-up small text-muted sort-icon"></i></th>
                            <th class="text-center cursor-pointer" id="sortSelesai">Selesai <i class="bi bi-arrow-down-up small text-muted sort-icon"></i></th>
                            <th class="text-end">Target</th>
                            <th class="text-end">Terkumpul</th>
                            <th class="text-center" style="width: 15%;">Progress</th>
                            <th class="text-center pe-4 rounded-top-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white"></tbody>
                </table>
            </div>
        </div>
        {{-- Pagination --}}
        <div class="card-footer bg-white border-0 py-3">
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center">
                <span id="paginationInfo" class="text-muted small ms-2"></span>
                <nav id="paginationLinks" class="me-2"></nav>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 1: CREATE / EDIT PROGRAM DONASI --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalDonasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div>
                    <h5 class="modal-title fw-bold text-dark" id="modalTitle">Program Donasi</h5>
                    <p class="text-muted small mb-0">Galang dana untuk kemaslahatan umat</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 bg-white">
                <form id="formDonasi" enctype="multipart/form-data">
                    <input type="hidden" id="id_donasi" name="id_donasi">

                    <div class="donation-card-wrapper">
                        
                        {{-- Upload Foto --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Poster / Gambar (Max 2 Mb)</label>
                            <input type="file" class="d-none" id="foto_donasi" name="foto_donasi" accept="image/*">
                            
                            {{-- 1. Tampilan Kosong --}}
                            <label for="foto_donasi" id="uploadPlaceholder" class="file-upload-box cursor-pointer">
                                <div class="text-center">
                                    <div class="icon-circle mb-2 mx-auto">
                                        <i class="bi bi-camera-fill text-success fs-5"></i>
                                    </div>
                                    <span class="text-muted small">Ketuk untuk upload</span>
                                </div>
                            </label>

                            {{-- 2. Tampilan Preview Final --}}
                            <div id="previewContainer" class="position-relative d-none">
                                {{-- Container dibuat relatif agar tombol hapus/edit posisinya pas --}}
                                <div style="width: 100%; aspect-ratio: 1/1; overflow: hidden; border-radius: 12px; position: relative;">
                                    <img id="previewFoto" class="img-fluid w-100 h-100 shadow-sm" 
                                        style="object-fit: cover; display: block;">
                                </div>

                                {{-- Tombol Hapus (Pojok Kanan Atas) --}}
                                <button type="button" id="btnHapusFoto" 
                                        class="btn btn-danger btn-sm position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                        style="top: 10px; right: 10px; width: 32px; height: 32px; padding: 0; z-index: 10;">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                                {{-- Tombol Ubah (Pojok Kanan Bawah) --}}
                                <label for="foto_donasi" 
                                    class="btn btn-light btn-sm position-absolute rounded-pill shadow-sm small fw-bold" 
                                    style="bottom: 10px; right: 10px; z-index: 10; cursor: pointer;">
                                    <i class="bi bi-pencil-fill me-1"></i> Ubah
                                </label>
                            </div>
                        </div>

                        {{-- Nama Program --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Nama Program</label>
                            <input type="text" class="form-control rounded-pill-input" name="nama_donasi" id="nama_donasi" placeholder="Misal: Renovasi Masjid" required>
                        </div>

                        {{-- Target Dana --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Target Dana</label>
                            <div class="input-group rounded-pill-group">
                                <span class="input-group-text bg-white border-end-0 text-success fw-bold ps-3">Rp</span>
                                <input type="text" class="form-control border-start-0 ps-1 fw-bold text-dark" id="display_target_dana" placeholder="0" required>
                                <input type="hidden" name="target_dana" id="target_dana">
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Mulai</label>
                                <input type="date" class="form-control rounded-pill-input" name="tanggal_mulai" id="tanggal_mulai" required value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Selesai</label>
                                <input type="date" class="form-control rounded-pill-input" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Deskripsi</label>
                            <textarea class="form-control rounded-box-input" name="deskripsi" id="deskripsi" rows="2" placeholder="Tulis keterangan singkat..."></textarea>
                        </div>

                        {{-- Tombol --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm" id="btnSimpanDonasi">
                                <i class="bi bi-check-circle me-2"></i> Simpan Program
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 2: CROPPER (BARU) --}}
{{-- ============================================================== --}}
<div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-bottom-0 pb-0 pt-3 px-4">
                <h5 class="modal-title fw-bold">Potong Gambar (1:1)</h5>
                <button type="button" class="btn-close" id="btnCloseCrop"></button>
            </div>
            <div class="modal-body p-0 mt-3">
                <div class="img-container" style="height: 500px; background-color: #333; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                    <img id="imageToCrop" style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" id="btnCancelCrop">Batal</button>
                <button type="button" class="btn btn-gradient-green rounded-pill px-4" id="btnCropImage">
                    <i class="bi bi-scissors me-2"></i>Potong & Gunakan
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL 3 & 4 (DETAIL & INPUT PEMASUKAN - TETAP SAMA) --}}
{{-- ============================================================== --}}
{{-- Saya persingkat bagian ini karena tidak berubah dari kode Anda sebelumnya, 
     pastikan tetap ada di file asli Anda --}}
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg modal-rounded">
            <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                <div><h5 class="modal-title fw-bold text-dark" id="detailTitle">Detail Donasi</h5></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-md-4"><div class="stat-card bg-light-green"><small class="text-success fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Target</small><h5 class="fw-bold text-dark mt-1 mb-0" id="detTarget">Rp 0</h5></div></div>
                    <div class="col-md-4"><div class="stat-card bg-gradient-green text-white shadow-sm"><small class="text-white-50 fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Terkumpul</small><h5 class="fw-bold mt-1 mb-0" id="detTerkumpul">Rp 0</h5></div></div>
                    <div class="col-md-4"><div class="stat-card bg-light-red"><small class="text-danger fw-bold text-uppercase ls-1" style="font-size: 0.7rem;">Kekurangan</small><h5 class="fw-bold text-dark mt-1 mb-0" id="detSisa">Rp 0</h5></div></div>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                    <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-muted"></i>Riwayat Pemasukan</h6>
                    <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalInputPemasukan"><i class="bi bi-plus-lg me-1"></i> Input Manual</button>
                </div>
                <div class="table-responsive rounded-3 border">
                    <table class="table table-striped mb-0 align-middle">
                        <thead class="bg-light"><tr><th class="ps-3">Tgl</th><th>Donatur</th><th class="text-center">Metode</th><th class="text-center">Status</th><th class="text-end">Nominal</th><th class="pe-3 text-center">#</th></tr></thead>
                        <tbody id="tabelRiwayat" class="small"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalInputPemasukan" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-sm"> 
        <div class="modal-content border-0 shadow-lg modal-rounded">
            <div class="modal-header border-0 pb-0 pt-3 px-3 bg-white">
                <h6 class="modal-title fw-bold text-dark">Catat Donasi Masuk</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3 bg-white">
                <form id="formPemasukan">
                    <input type="hidden" name="id_donasi" id="input_id_donasi">
                    <div class="donation-card-wrapper p-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-success small">Nominal (Rp)</label>
                            <input type="text" class="form-control rounded-pill-input text-center fw-bold text-success" style="font-size: 1.1rem;" id="display_nominal" placeholder="0" required>
                            <input type="hidden" name="nominal" id="real_nominal">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold text-success small">Nama Donatur</label>
                            <input type="text" class="form-control rounded-pill-input form-control-sm" name="nama_donatur" placeholder="Hamba Allah" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold text-success small">Metode</label>
                            <select class="form-select rounded-pill-input form-select-sm" name="metode_pembayaran" required>
                                <option value="tunai" selected>Tunai (Cash)</option>
                                <option value="whatsapp">Konfirmasi WA</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold text-success small">Tanggal</label>
                            <input type="date" class="form-control rounded-pill-input form-control-sm" name="tanggal" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <textarea class="form-control rounded-box-input form-control-sm" name="pesan" rows="2" placeholder="Pesan / Doa (Opsional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-gradient-green w-100 rounded-pill fw-bold shadow-sm py-2">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    #tabelDonasi, .card, .modal-content, .donation-card-wrapper { font-family: 'Poppins', sans-serif; }
    .modal-rounded { border-radius: 20px !important; overflow: hidden; }
    .ls-1 { letter-spacing: 0.5px; }
    .btn-gradient-green { background: linear-gradient(135deg, #198754, #20c997); border: none; color: white; transition: all 0.3s; }
    .btn-gradient-green:hover { background: linear-gradient(135deg, #157347, #198754); transform: translateY(-1px); color: white; }
    .donation-card-wrapper { background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 16px; padding: 20px; box-shadow: inset 0 0 15px rgba(34, 197, 94, 0.03); }
    .rounded-pill-input { border-radius: 50px !important; border: 1px solid #d1d5db; padding-left: 15px; font-size: 0.9rem; }
    .rounded-box-input { border-radius: 12px !important; border: 1px solid #d1d5db; padding: 10px; }
    .form-control:focus, .form-select:focus { border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15); }
    .rounded-pill-group .input-group-text { border-top-left-radius: 50px; border-bottom-left-radius: 50px; border: 1px solid #d1d5db; background: white; }
    .rounded-pill-group .form-control { border-top-right-radius: 50px; border-bottom-right-radius: 50px; border: 1px solid #d1d5db; }
    .file-upload-box { background: white; border: 2px dashed #cbd5e1; border-radius: 12px; height: 120px; display: flex; align-items: center; justify-content: center; transition: all 0.3s; }
    .file-upload-box:hover { border-color: #22c55e; background: #fafffc; }
    .icon-circle { width: 40px; height: 40px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
    .stat-card { padding: 15px; border-radius: 16px; text-align: center; }
    .bg-light-green { background-color: #ecfdf5; color: #065f46; }
    .bg-light-red { background-color: #fef2f2; color: #991b1b; }
    .bg-gradient-green { background: linear-gradient(135deg, #10b981, #059669); }
    .rounded-top-left { border-top-left-radius: 10px; }
    .rounded-top-right { border-top-right-radius: 10px; }
</style>

{{-- 2. Load JS Libraries (Cropper) --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. SETUP & VARIABEL GLOBAL
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelDonasi tbody');
    
    // Modal Utama
    const modalDonasiElement = document.getElementById('modalDonasi');
    const modalDonasi = new bootstrap.Modal(modalDonasiElement);
    const formDonasi = document.getElementById('formDonasi');
    
    // Element Foto & UI Baru
    const fotoInput = document.getElementById('foto_donasi');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    const previewFoto = document.getElementById('previewFoto');
    const btnHapusFoto = document.getElementById('btnHapusFoto');

    // Element Modal Crop
    const modalCropElement = document.getElementById('modalCrop');
    const modalCrop = new bootstrap.Modal(modalCropElement);
    const imageToCrop = document.getElementById('imageToCrop');
    const btnCropImage = document.getElementById('btnCropImage');
    const btnCancelCrop = document.getElementById('btnCancelCrop');
    const btnCloseCrop = document.getElementById('btnCloseCrop');
    
    // Variabel Cropping
    let cropper = null;
    let croppedBlob = null; 
    let originalFileName = '';
    
    // Element Rupiah & Lainnya
    const displayTarget = document.getElementById('display_target_dana');
    const realTarget = document.getElementById('target_dana');
    const btnSimpanDonasi = document.getElementById('btnSimpanDonasi');
    const originalButtonText = btnSimpanDonasi.innerHTML;

    // Modal Detail & Input Pemasukan
    const modalDetailEl = document.getElementById('modalDetail');
    const modalDetail = new bootstrap.Modal(modalDetailEl);
    const modalInputPemasukanEl = document.getElementById('modalInputPemasukan');
    const modalInputPemasukan = new bootstrap.Modal(modalInputPemasukanEl);
    const formPemasukan = document.getElementById('formPemasukan');

    // State Management
    let state = { page: 1, search: '', status: 'aktif', sortBy: 'created_at', sortDir: 'desc' };
    let currentDonasiId = null;

    const formatRupiah = (angka) => "Rp " + new Intl.NumberFormat('id-ID').format(angka);
    const formatTanggal = (str) => !str ? '-' : new Date(str).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });

    // ============================================================
    // 2. LOGIC CROPPING & PREVIEW
    // ============================================================

    // A. Saat User Pilih File -> Buka Modal Crop
    fotoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                Swal.fire('Error', 'File harus gambar', 'error');
                this.value = ''; return;
            }
            originalFileName = file.name;
            const reader = new FileReader();
            reader.onload = (ev) => {
                imageToCrop.src = ev.target.result;
                modalDonasi.hide(); // Sembunyikan modal utama
                modalCrop.show();   // Tampilkan modal crop
            }
            reader.readAsDataURL(file);
        }
    });

    // B. Init Cropper saat modal muncul
    modalCropElement.addEventListener('shown.bs.modal', () => {
        if(cropper) cropper.destroy();
        cropper = new Cropper(imageToCrop, {
            aspectRatio: 1 / 1, // KUNCI RASIO 1:1
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            background: false,
        });
    });

    // C. Tombol Potong -> Simpan Blob -> Tampilkan Preview
    btnCropImage.addEventListener('click', () => {
        if (!cropper) return;
        cropper.getCroppedCanvas({ width: 800, height: 800 }).toBlob((blob) => {
            croppedBlob = blob; // Simpan Blob ke variabel

            // Tampilkan Preview Final
            const url = URL.createObjectURL(blob);
            previewFoto.src = url;
            
            // Swap UI: Sembunyikan Placeholder, Munculkan Preview
            uploadPlaceholder.classList.add('d-none');
            previewContainer.classList.remove('d-none');

            // Tutup Crop, Buka Modal Utama
            closeCropModal();
            modalDonasi.show();
        }, 'image/jpeg', 0.9);
    });

    // D. Tombol Batal Crop
    const handleCancelCrop = () => {
        fotoInput.value = ''; // Reset input file
        closeCropModal();
        modalDonasi.show(); // Kembali ke modal utama
    };

    btnCancelCrop.addEventListener('click', handleCancelCrop);
    btnCloseCrop.addEventListener('click', handleCancelCrop);

    function closeCropModal() {
        modalCrop.hide();
        if(cropper) { cropper.destroy(); cropper = null; }
    }

    // E. Hapus Foto (Tombol X di preview)
    btnHapusFoto.addEventListener('click', () => {
        resetFileState();
    });

    function resetFileState() {
        fotoInput.value = '';
        croppedBlob = null;
        originalFileName = '';
        previewFoto.src = '';
        uploadPlaceholder.classList.remove('d-none'); // Munculkan kotak upload
        previewContainer.classList.add('d-none');     // Sembunyikan preview
    }

    // ============================================================
    // 3. LOGIC SUBMIT FORM DONASI (MODIFIED)
    // ============================================================
    formDonasi.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        // Setup Loading Button
        btnSimpanDonasi.disabled = true;
        btnSimpanDonasi.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;

        // Handle Target Dana (Format Rupiah)
        if (!realTarget.value && displayTarget.value) {
            realTarget.value = displayTarget.value.replace(/[^0-9]/g, '');
        }

        const id = document.getElementById('id_donasi').value;
        const formData = new FormData(formDonasi);

        // [PENTING] Ganti file input asli dengan Blob Hasil Crop
        if (croppedBlob) {
            const fname = originalFileName || 'donasi.jpg';
            formData.set('foto_donasi', croppedBlob, fname);
        }

        let url = '/pengurus/donasi';
        if (id) { url += `/${id}`; formData.append('_method', 'PUT'); }

        try {
            const res = await fetch(url, { 
                method: 'POST', 
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, 
                body: formData 
            });
            const data = await res.json();
            
            if (res.ok) {
                modalDonasi.hide();
                Swal.fire({ title: 'Berhasil!', text: data.message, icon: 'success', confirmButtonColor: '#198754' });
                loadDonasi();
            } else {
                throw new Error(data.message || 'Gagal menyimpan data');
            }
        } catch (err) { 
            Swal.fire('Error', err.message, 'error'); 
        } finally {
            btnSimpanDonasi.disabled = false;
            btnSimpanDonasi.innerHTML = originalButtonText;
        }
    });

    // ============================================================
    // 4. CREATE & EDIT UTILS
    // ============================================================

    // Buka Modal Tambah
    document.getElementById('btnTambahDonasi').addEventListener('click', () => {
        formDonasi.reset();
        document.getElementById('id_donasi').value = '';
        realTarget.value = ''; 
        displayTarget.value = '';
        resetFileState(); // Reset Foto
        document.getElementById('modalTitle').innerText = "Program Donasi Baru";
        modalDonasi.show();
    });

    // Buka Modal Edit
    window.editDonasi = async (id) => {
        try {
            const res = await fetch(`/pengurus/donasi/${id}`);
            const data = await res.json();

            document.getElementById('id_donasi').value = data.id_donasi;
            document.getElementById('nama_donasi').value = data.nama_donasi;
            document.getElementById('deskripsi').value = data.deskripsi || '';
            document.getElementById('tanggal_mulai').value = data.tanggal_mulai.split('T')[0];
            if(data.tanggal_selesai) document.getElementById('tanggal_selesai').value = data.tanggal_selesai.split('T')[0];

            realTarget.value = data.target_dana;
            displayTarget.value = new Intl.NumberFormat('id-ID').format(data.target_dana);

            // LOGIC TAMPIL FOTO LAMA
            croppedBlob = null; // Reset blob baru
            if (data.foto_donasi) {
                previewFoto.src = data.foto_url || `/storage/${data.foto_donasi}`;
                uploadPlaceholder.classList.add('d-none');
                previewContainer.classList.remove('d-none');
            } else {
                resetFileState();
            }

            document.getElementById('modalTitle').innerText = "Edit Program Donasi";
            modalDonasi.show();
        } catch (error) {
            Swal.fire('Error', 'Gagal memuat data', 'error');
        }
    };

    // ============================================================
    // 5. HELPER FORMAT RUPIAH (INPUT)
    // ============================================================
    displayTarget.addEventListener('keyup', function(e) {
        let rawValue = this.value.replace(/[^0-9]/g, '');
        realTarget.value = rawValue;
        this.value = rawValue ? new Intl.NumberFormat('id-ID').format(rawValue) : '';
    });

    // ============================================================
    // 6. LOAD DATA (TABLE)
    // ============================================================
    async function loadDonasi() {
        const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success"></div></td></tr>`;
        try {
            const params = new URLSearchParams({ page: state.page, search: state.search, status: state.status, sortBy: state.sortBy, sortDir: state.sortDir });
            const res = await fetch(`/pengurus/donasi-data?${params.toString()}`);
            const response = await res.json();
            renderTable(response.data, response.from || 1);
            renderPagination(response);
            updateSortIcons();
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = '';
        const colCount = document.querySelector('#tabelDonasi thead tr').cells.length;
        
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center py-4 text-muted">Belum ada program donasi.</td></tr>`;
            return;
        }

        data.forEach((item, i) => {
            const target = parseFloat(item.target_dana);
            const terkumpul = parseFloat(item.total_terkumpul);
            const persen = target > 0 ? Math.min(100, (terkumpul / target) * 100).toFixed(1) : 0;
            
            // --- PERBAIKAN URL GAMBAR ---
            // Pastikan item.foto_donasi berisi 'folder/namafile.jpg' 
            // Jika di database tersimpan 'public/foto_donasi/...', kita perlu sesuaikan.
            // Asumsi standar: database menyimpan path relatif dari 'storage/app/public'
            const fotoUrl = item.foto_donasi 
                ? `/storage/${item.foto_donasi}` 
                : 'https://via.placeholder.com/150?text=No+Img';

            const isExpired = item.tanggal_selesai && new Date(item.tanggal_selesai) < new Date().setHours(0,0,0,0);
            
            const row = `
                <tr class="${isExpired ? 'bg-light text-muted' : ''}">
                    <td class="text-center">${startNum + i}</td>
                    
                    {{-- PERBAIKAN TAMPILAN GAMBAR DI TABEL --}}
                    <td class="text-center">
                        <div style="width: 50px; height: 50px; margin: 0 auto; overflow: hidden; border-radius: 8px;">
                            <img src="${fotoUrl}" 
                                 class="shadow-sm" 
                                 alt="Foto"
                                 style="width: 100%; height: 100%; object-fit: cover;"
                                 onerror="this.src='https://via.placeholder.com/50?text=Err'">
                        </div>
                    </td>

                    <td>
                        <div class="fw-bold text-dark">${item.nama_donasi}</div>
                        ${isExpired ? '<span class="badge bg-secondary ms-2">Selesai</span>' : ''}
                    </td>
                    <td class="text-center small">${formatTanggal(item.tanggal_mulai)}</td>
                    <td class="text-center small">${item.tanggal_selesai ? formatTanggal(item.tanggal_selesai) : '<span class="badge bg-success rounded-pill">Seumur Hidup</span>'}</td>
                    <td class="text-end fw-semibold text-secondary">${formatRupiah(target)}</td>
                    <td class="text-end fw-bold text-success">${formatRupiah(terkumpul)}</td>
                    <td class="text-center">
                        <div class="progress" style="height: 8px; border-radius: 4px;">
                            <div class="progress-bar bg-success" style="width: ${persen}%"></div>
                        </div>
                        <small class="text-muted">${persen}% Terkumpul</small>
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-2"> 
                            <button class="btn btn-sm btn-info text-white rounded-3 shadow-sm" onclick="window.bukaDetail('${item.id_donasi}')"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" onclick="window.editDonasi('${item.id_donasi}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger rounded-3 shadow-sm" onclick="window.hapusDonasi('${item.id_donasi}')"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    // ============================================================
    // 7. UTILS & OTHERS (DETAIL, SEARCH, PAGINATION)
    // ============================================================
    // ... Bagian ini sama persis dengan kode Anda sebelumnya ...
    // ... Pastikan fungsi window.hapusDonasi, window.bukaDetail, renderPagination, dll tetap ada ...
    
    // (Agar kode tidak terlalu panjang, saya asumsikan bagian Detail & Input Pemasukan 
    //  sama persis dengan yang Anda kirimkan, karena tidak ada perubahan logic di sana)

    // Fitur Search/Sort/Pagination
    let searchTimeout;
    document.getElementById('searchInput').addEventListener('keyup', (e) => { clearTimeout(searchTimeout); searchTimeout = setTimeout(() => { state.search = e.target.value; state.page = 1; loadDonasi(); }, 300); });
    document.getElementById('statusFilter').addEventListener('change', (e) => { state.status = e.target.value; state.page = 1; loadDonasi(); });
    document.getElementById('sortMulai').addEventListener('click', () => { state.sortBy = 'tanggal_mulai'; state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc'; loadDonasi(); });
    document.getElementById('sortSelesai').addEventListener('click', () => { state.sortBy = 'tanggal_selesai'; state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc'; loadDonasi(); });

    function updateSortIcons() {
        document.querySelectorAll('.sort-icon').forEach(i => i.className = 'bi bi-arrow-down-up small text-muted sort-icon');
        let active = state.sortDir === 'asc' ? 'bi bi-arrow-up text-primary' : 'bi bi-arrow-down text-primary';
        if(state.sortBy === 'tanggal_mulai') document.querySelector('#sortMulai i').className = `${active} small sort-icon`;
        if(state.sortBy === 'tanggal_selesai') document.querySelector('#sortSelesai i').className = `${active} small sort-icon`;
    }

    function renderPagination(response) {
        const nav = document.getElementById('paginationLinks');
        document.getElementById('paginationInfo').textContent = `Menampilkan ${response.from||0} - ${response.to||0} dari ${response.total} data`;
        let html = '<ul class="pagination justify-content-end mb-0 pagination-sm">';
        response.links.forEach(link => {
            html += `<li class="page-item ${link.active?'active':''} ${link.url?'':'disabled'}"><a class="page-link" href="#" data-url="${link.url}">${link.label.replace('&laquo; Previous','<').replace('Next &raquo;','>')}</a></li>`;
        });
        nav.innerHTML = html + '</ul>';
        nav.querySelectorAll('a.page-link').forEach(a => a.addEventListener('click', (e) => {
            e.preventDefault(); if(a.dataset.url && a.dataset.url !== 'null') {
                state.page = new URLSearchParams(a.dataset.url.split('?')[1]).get('page'); loadDonasi();
            }
        }));
    }

    // Detail & Pemasukan (Sama)
    window.hapusDonasi = async (id) => { const c = await Swal.fire({ title: 'Hapus?', text: "Hilang permanen!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33' }); if (c.isConfirmed) { await fetch(`/pengurus/donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } }); Swal.fire('Terhapus!', '', 'success'); loadDonasi(); }};
    window.bukaDetail = async (id) => { currentDonasiId = id; await refreshModalDetail(); modalDetail.show(); };

    async function refreshModalDetail() {
        if(!currentDonasiId) return;
        const res = await fetch(`/pengurus/donasi/${currentDonasiId}`);
        const data = await res.json();
        document.getElementById('detailTitle').textContent = data.nama_donasi;
        document.getElementById('input_id_donasi').value = data.id_donasi;
        const target = parseFloat(data.target_dana); const terkumpul = parseFloat(data.total_terkumpul);
        document.getElementById('detTarget').textContent = formatRupiah(target);
        document.getElementById('detTerkumpul').textContent = formatRupiah(terkumpul);
        document.getElementById('detSisa').textContent = formatRupiah(Math.max(0, target - terkumpul));
        const tbodyRiwayat = document.getElementById('tabelRiwayat'); tbodyRiwayat.innerHTML = '';
        if (data.pemasukan.length === 0) { tbodyRiwayat.innerHTML = `<tr><td colspan="6" class="text-center text-muted">Belum ada donasi masuk.</td></tr>`; } else {
            data.pemasukan.forEach(p => {
                let badgeMetode = p.metode_pembayaran === 'tunai' ? '<span class="badge bg-secondary">Tunai</span>' : '<span class="badge bg-primary">Transfer</span>';
                let badgeStatus = p.status === 'success' ? '<span class="badge bg-success bg-opacity-10 text-success border border-success">Berhasil</span>' : (p.status === 'pending' ? '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning">Pending</span>' : '<span class="badge bg-danger">Gagal</span>');
                tbodyRiwayat.insertAdjacentHTML('beforeend', `<tr><td>${formatTanggal(p.tanggal)}</td><td>${p.nama_donatur}<br><small class="text-muted">"${p.pesan || '-'}"</small></td><td class="text-center">${badgeMetode}</td><td class="text-center">${badgeStatus}</td><td class="text-end fw-bold text-success">${formatRupiah(p.nominal)}</td><td class="text-center"><button class="btn btn-sm btn-outline-danger" onclick="window.hapusPemasukan('${p.id_pemasukan_donasi}')"><i class="bi bi-trash"></i></button></td></tr>`);
            });
        }
    }

    // Logic Input Pemasukan & Delete Pemasukan (Copy dari kode Anda sebelumnya)
    const displayNominal = document.getElementById('display_nominal'); const realNominal = document.getElementById('real_nominal');
    if (displayNominal) { displayNominal.addEventListener('keyup', function(e) { let rawValue = this.value.replace(/[^0-9]/g, ''); realNominal.value = rawValue; this.value = rawValue ? new Intl.NumberFormat('id-ID').format(rawValue) : ''; }); }
    formPemasukan.addEventListener('submit', async (e) => { e.preventDefault(); if (!realNominal.value && displayNominal.value) { realNominal.value = displayNominal.value.replace(/[^0-9]/g, ''); } const formData = new FormData(formPemasukan); try { const res = await fetch('/pengurus/pemasukan-donasi', { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: formData }); if (res.ok) { modalInputPemasukan.hide(); formPemasukan.reset(); displayNominal.value = ''; realNominal.value = ''; Swal.fire({ icon: 'success', title: 'Donasi Masuk!', timer: 1500, showConfirmButton: false }); await refreshModalDetail(); loadDonasi(); } else { Swal.fire('Gagal', 'Cek inputan nominal', 'error'); } } catch (err) { Swal.fire('Gagal', '', 'error'); } });
    window.hapusPemasukan = async (id) => { const c = await Swal.fire({ title: 'Batalkan donasi?', icon: 'warning', showCancelButton: true }); if(c.isConfirmed) { await fetch(`/pengurus/pemasukan-donasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } }); await refreshModalDetail(); loadDonasi(); } };

    loadDonasi();
});
</script>
@endpush
@endsection