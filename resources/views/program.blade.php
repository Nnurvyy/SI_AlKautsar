@extends('layouts.app')

@section('title', 'Program')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- 1. Load CSS Cropper.js --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <div class="container-fluid p-4">

        {{-- HEADER & SEARCH --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Filter Status --}}
                <select class="form-select rounded-pill ps-3" id="statusFilter" style="width: 170px; border-color: #e5e7eb;">
                    <option value="all" selected>Semua Status</option>
                    <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                    <option value="sedang berjalan">Sedang Berjalan</option>
                    <option value="sudah dijalankan">Sudah Dijalankan</option>
                </select>

                {{-- Search Bar --}}
                <div class="input-group" style="width: 300px;">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3"><i
                            class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchInput" class="form-control border-start-0 rounded-end-pill"
                        placeholder="Cari nama program...">
                </div>
            </div>

            <button class="btn btn-gradient-green rounded-pill px-4 shadow-sm" id="btnTambahProgram">
                <i class="bi bi-plus-lg me-2"></i> Program Baru
            </button>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold text-dark mb-0">Total Program</h5>
                    <small class="text-muted">Jumlah agenda kegiatan terdaftar</small>
                </div>
                <h3 class="fw-bold text-success mb-0" id="totalProgramHeader">
                    {{ number_format($totalProgram ?? 0, 0, ',', '.') }}
                </h3>
            </div>
        </div>

        {{-- TABEL UTAMA --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tabelProgram">
                        <thead class="bg-light">
                            <tr style="height: 50px;">
                                <th class="text-center ps-4 rounded-top-left">No</th>
                                <th class="text-center">Foto</th>
                                <th>Nama Program</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Lokasi</th>
                                <th class="text-center">Status</th>
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
    {{-- MODAL 1: CREATE / EDIT PROGRAM --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="modalProgram" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg modal-rounded">

                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="modalTitle">Program Kegiatan</h5>
                        <p class="text-muted small mb-0">Kelola agenda dan kegiatan masjid</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 bg-white">
                    <form id="formProgram" enctype="multipart/form-data">
                        <input type="hidden" id="id_program" name="id_program">

                        <div class="donation-card-wrapper">

                            {{-- Upload Foto --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Foto Kegiatan (Max
                                    2 Mb)</label>
                                <input type="file" class="d-none" id="foto_program" name="foto_program" accept="image/*">

                                {{-- 1. Tampilan Kosong --}}
                                <label for="foto_program" id="uploadPlaceholder" class="file-upload-box cursor-pointer">
                                    <div class="text-center">
                                        <div class="icon-circle mb-2 mx-auto">
                                            <i class="bi bi-camera-fill text-success fs-5"></i>
                                        </div>
                                        <span class="text-muted small">Ketuk untuk upload</span>
                                    </div>
                                </label>

                                {{-- 2. Tampilan Preview Final (Rasio 1:1) --}}
                                <div id="previewContainer" class="position-relative d-none">
                                    <div
                                        style="width: 100%; aspect-ratio: 1/1; overflow: hidden; border-radius: 12px; position: relative;">
                                        <img id="previewFoto" class="img-fluid w-100 h-100 shadow-sm"
                                            style="object-fit: cover; display: block;">
                                    </div>
                                    <button type="button" id="btnHapusFoto"
                                        class="btn btn-danger btn-sm position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                        style="top: 10px; right: 10px; width: 32px; height: 32px; padding: 0; z-index: 10;"><i
                                            class="bi bi-x-lg"></i></button>
                                    <label for="foto_program"
                                        class="btn btn-light btn-sm position-absolute rounded-pill shadow-sm small fw-bold"
                                        style="bottom: 10px; right: 10px; z-index: 10; cursor: pointer;"><i
                                            class="bi bi-pencil-fill me-1"></i> Ubah</label>
                                </div>
                            </div>

                            {{-- Nama Program --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Nama
                                    Program</label>
                                <input type="text" class="form-control rounded-pill-input" name="nama_program"
                                    id="nama_program" placeholder="Contoh: Santunan Yatim" required>
                            </div>

                            {{-- Penyelenggara & Lokasi --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label
                                        class="form-label fw-bold text-success small text-uppercase ls-1">Penyelenggara</label>
                                    <input type="text" class="form-control rounded-pill-input"
                                        name="penyelenggara_program" id="penyelenggara_program" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Lokasi</label>
                                    <input type="text" class="form-control rounded-pill-input" name="lokasi_program"
                                        id="lokasi_program" required>
                                </div>
                            </div>

                            {{-- Tanggal & Status --}}
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Waktu</label>
                                    <input type="datetime-local" class="form-control rounded-pill-input"
                                        name="tanggal_program" id="tanggal_program" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold text-success small text-uppercase ls-1">Status</label>
                                    <select class="form-select rounded-pill-input" id="status_program"
                                        name="status_program" required>
                                        <option value="belum dilaksanakan">Belum Dilaksanakan</option>
                                        <option value="sedang berjalan">Sedang Berjalan</option>
                                        <option value="sudah dijalankan">Sudah Dijalankan</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Deskripsi</label>
                                <textarea class="form-control rounded-box-input" name="deskripsi_program" id="deskripsi_program" rows="2"
                                    placeholder="Detail kegiatan..."></textarea>
                            </div>

                            {{-- Tombol --}}
                            <div class="d-grid">
                                <button type="submit" class="btn btn-gradient-green rounded-pill py-2 fw-bold shadow-sm"
                                    id="btnSimpanProgram">
                                    <i class="bi bi-check-circle me-2"></i> Simpan Data
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
    <div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0 pt-3 px-4">
                    <h5 class="modal-title fw-bold">Potong Gambar (1:1)</h5>
                    <button type="button" class="btn-close" id="btnCloseCrop"></button>
                </div>
                <div class="modal-body p-0 mt-3">
                    <div class="img-container"
                        style="height: 500px; background-color: #333; display: flex; justify-content: center; align-items: center; overflow: hidden;">
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
    {{-- MODAL 3: DETAIL PROGRAM --}}
    {{-- ============================================================== --}}
    <div class="modal fade" id="modalDetailProgram" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg modal-rounded">
                <div class="modal-header border-0 pb-0 pt-4 px-4 bg-white">
                    <div>
                        <h5 class="modal-title fw-bold text-dark">Detail Program</h5>
                        <p class="text-muted small mb-0">Informasi lengkap kegiatan</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4 align-items-start">
                        <div class="col-md-5">
                            <img id="detailFotoProgram" src=""
                                class="img-fluid rounded-4 shadow-sm w-100 object-fit-cover" style="aspect-ratio: 1/1;">
                        </div>
                        <div class="col-md-7">
                            <span id="d_status" class="badge rounded-pill px-3 py-2 mb-2"></span>
                            <h3 id="d_nama" class="fw-bold text-dark mb-3"></h3>
                            <div class="d-flex flex-column gap-2 mb-4">
                                <div class="d-flex align-items-center text-muted"><i
                                        class="bi bi-calendar-event me-2 text-success"></i><span id="d_tanggal"
                                        class="fw-medium"></span></div>
                                <div class="d-flex align-items-center text-muted"><i
                                        class="bi bi-geo-alt me-2 text-danger"></i><span id="d_lokasi"
                                        class="fw-medium"></span></div>
                                <div class="d-flex align-items-center text-muted"><i
                                        class="bi bi-person-badge me-2 text-primary"></i><span id="d_penyelenggara"
                                        class="fw-medium"></span></div>
                            </div>
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-uppercase text-muted fw-bold ls-1 d-block mb-2">Deskripsi</small>
                                <p id="d_deskripsi" class="text-dark mb-0 small" style="line-height: 1.6;"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- CSS KHUSUS HALAMAN INI --}}
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        #tabelProgram,
        .card,
        .modal-content,
        .donation-card-wrapper {
            font-family: 'Poppins', sans-serif;
        }

        .modal-rounded {
            border-radius: 20px !important;
            overflow: hidden;
        }

        .ls-1 {
            letter-spacing: 0.5px;
        }

        .btn-gradient-green {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            color: white;
            transition: all 0.3s;
        }

        .btn-gradient-green:hover {
            background: linear-gradient(135deg, #157347, #198754);
            transform: translateY(-1px);
            color: white;
        }

        .donation-card-wrapper {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 16px;
            padding: 20px;
            box-shadow: inset 0 0 15px rgba(34, 197, 94, 0.03);
        }

        .rounded-pill-input {
            border-radius: 50px !important;
            border: 1px solid #d1d5db;
            padding-left: 15px;
            font-size: 0.9rem;
        }

        .rounded-box-input {
            border-radius: 12px !important;
            border: 1px solid #d1d5db;
            padding: 10px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }

        .file-upload-box {
            background: white;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .file-upload-box:hover {
            border-color: #22c55e;
            background: #fafffc;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            background: #dcfce7;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .rounded-top-left {
            border-top-left-radius: 10px;
        }

        .rounded-top-right {
            border-top-right-radius: 10px;
        }
    </style>

    {{-- 2. Load JS Libraries --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const token = document.querySelector('meta[name="csrf-token"]').content;
                const tbody = document.querySelector('#tabelProgram tbody');


                const modalProgramEl = document.getElementById('modalProgram');
                const modalProgram = new bootstrap.Modal(modalProgramEl);
                const form = document.getElementById('formProgram');
                const btnSimpanProgram = document.getElementById('btnSimpanProgram');
                const originalButtonText = btnSimpanProgram.innerHTML;


                const modalDetailEl = document.getElementById('modalDetailProgram');
                const modalDetail = new bootstrap.Modal(modalDetailEl);


                const modalCropEl = document.getElementById('modalCrop');
                const modalCrop = new bootstrap.Modal(modalCropEl);
                const imageToCrop = document.getElementById('imageToCrop');
                const btnCropImage = document.getElementById('btnCropImage');
                const btnCancelCrop = document.getElementById('btnCancelCrop');
                const btnCloseCrop = document.getElementById('btnCloseCrop');


                const fotoInput = document.getElementById('foto_program');
                const uploadPlaceholder = document.getElementById('uploadPlaceholder');
                const previewContainer = document.getElementById('previewContainer');
                const preview = document.getElementById('previewFoto');
                const btnHapusFoto = document.getElementById('btnHapusFoto');


                let cropper = null;
                let croppedBlob = null;
                let originalFileName = '';


                const searchInput = document.getElementById('searchInput');
                const statusFilter = document.getElementById('statusFilter');


                let state = {
                    currentPage: 1,
                    status: 'all',
                    search: '',
                    perPage: 10,
                    sortBy: 'tanggal_program',
                    sortDir: 'desc',
                    searchTimeout: null
                };






                if (fotoInput) {
                    fotoInput.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file) {
                            if (!file.type.startsWith('image/')) {
                                Swal.fire('Error', 'File harus gambar', 'error');
                                this.value = '';
                                return;
                            }
                            originalFileName = file.name;
                            const reader = new FileReader();
                            reader.onload = (ev) => {
                                imageToCrop.src = ev.target.result;
                                modalProgram.hide();
                                modalCrop.show();
                            }
                            reader.readAsDataURL(file);
                        }
                    });
                }


                modalCropEl.addEventListener('shown.bs.modal', () => {
                    if (cropper) cropper.destroy();
                    cropper = new Cropper(imageToCrop, {
                        aspectRatio: 1 / 1,
                        viewMode: 1,
                        autoCropArea: 1,
                        responsive: true,
                        background: false,
                    });
                });


                btnCropImage.addEventListener('click', () => {
                    if (!cropper) return;
                    cropper.getCroppedCanvas({
                        width: 800,
                        height: 800
                    }).toBlob((blob) => {
                        croppedBlob = blob;


                        const url = URL.createObjectURL(blob);
                        preview.src = url;


                        uploadPlaceholder.classList.add('d-none');
                        previewContainer.classList.remove('d-none');


                        closeCropModal();
                        modalProgram.show();
                    }, 'image/jpeg', 0.9);
                });


                const handleCancelCrop = () => {
                    fotoInput.value = '';
                    closeCropModal();
                    modalProgram.show();
                };

                btnCancelCrop.addEventListener('click', handleCancelCrop);
                btnCloseCrop.addEventListener('click', handleCancelCrop);

                function closeCropModal() {
                    modalCrop.hide();
                    if (cropper) {
                        cropper.destroy();
                        cropper = null;
                    }
                }


                if (btnHapusFoto) {
                    btnHapusFoto.addEventListener('click', () => resetFileState());
                }

                function resetFileState() {
                    fotoInput.value = '';
                    croppedBlob = null;
                    originalFileName = '';
                    preview.src = '';
                    uploadPlaceholder.classList.remove('d-none');
                    previewContainer.classList.add('d-none');
                }




                form.addEventListener('submit', async e => {
                    e.preventDefault();

                    btnSimpanProgram.disabled = true;
                    btnSimpanProgram.innerHTML =
                        `<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...`;

                    const id = document.getElementById('id_program').value;
                    const formData = new FormData(form);


                    if (croppedBlob) {
                        const fname = originalFileName || 'program.jpg';
                        formData.set('foto_program', croppedBlob, fname);
                    }

                    const url = id ? `/pengurus/program/${id}` : '/pengurus/program';
                    if (id) formData.append('_method', 'PUT');

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const data = await res.json();

                        if (res.ok) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: data.message,
                                icon: 'success',
                                confirmButtonColor: '#198754'
                            });
                            modalProgram.hide();
                            loadProgram();
                        } else {
                            throw new Error(data.message || 'Terjadi kesalahan');
                        }
                    } catch (err) {
                        Swal.fire('Gagal', err.message, 'error');
                    } finally {
                        btnSimpanProgram.disabled = false;
                        btnSimpanProgram.innerHTML = originalButtonText;
                    }
                });






                const btnTambah = document.getElementById('btnTambahProgram');
                if (btnTambah) {
                    btnTambah.addEventListener('click', () => {
                        form.reset();
                        document.getElementById('id_program').value = '';
                        resetFileState();
                        document.getElementById('modalTitle').innerText = "Program Kegiatan Baru";
                        modalProgram.show();
                    });
                }


                modalProgramEl.addEventListener('hidden.bs.modal', function() {
                    if (!document.querySelector('.modal-backdrop')) {

                    }
                });


                window.editProgram = async function(id) {
                    try {
                        const res = await fetch(`/pengurus/program/${id}`);
                        const data = await res.json();

                        document.getElementById('id_program').value = data.id_program;
                        document.getElementById('nama_program').value = data.nama_program;
                        document.getElementById('penyelenggara_program').value = data.penyelenggara_program;
                        document.getElementById('lokasi_program').value = data.lokasi_program;
                        document.getElementById('deskripsi_program').value = data.deskripsi_program;
                        document.getElementById('status_program').value = data.status_program;
                        if (data.tanggal_program) document.getElementById('tanggal_program').value = data
                            .tanggal_program.substring(0, 16);


                        croppedBlob = null;
                        if (data.foto_program) {
                            preview.src = data.foto_url;
                            uploadPlaceholder.classList.add('d-none');
                            previewContainer.classList.remove('d-none');
                        } else {
                            resetFileState();
                        }

                        document.getElementById('modalTitle').innerText = "Edit Program Kegiatan";
                        modalProgram.show();
                    } catch (err) {
                        Swal.fire('Error', 'Gagal memuat data', 'error');
                    }
                };


                async function loadProgram() {
                    let colCount = document.querySelector('#tabelProgram thead tr').cells.length;
                    tbody.innerHTML =
                        `<tr><td colspan="${colCount}" class="text-center py-5"><div class="spinner-border text-success"></div></td></tr>`;
                    const url =
                        `/pengurus/program-data?page=${state.currentPage}&status=${state.status}&search=${state.search}&perPage=${state.perPage}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;
                    try {
                        const res = await fetch(url);
                        const response = await res.json();
                        renderTable(response.data, response.from || 1);
                        renderPagination(response);
                    } catch (err) {
                        tbody.innerHTML =
                            `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
                    }
                }

                function renderTable(data, startNum) {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        let colCount = document.querySelector('#tabelProgram thead tr').cells.length;
                        tbody.innerHTML =
                            `<tr><td colspan="${colCount}" class="text-center py-4 text-muted">Belum ada program.</td></tr>`;
                        return;
                    }
                    data.forEach((item, i) => {
                        let badgeClass = 'bg-secondary';
                        if (item.status_program === 'sedang berjalan') badgeClass = 'bg-warning text-dark';
                        if (item.status_program === 'belum dilaksanakan') badgeClass = 'bg-success';


                        const fotoUrl = item.foto_program ? item.foto_url :
                            'https://via.placeholder.com/150?text=No+Img';

                        const row = `
                <tr>
                    <td class="text-center">${startNum + i}</td>
                    <td class="text-center">
                        <div style="width: 50px; height: 50px; margin: 0 auto; overflow: hidden; border-radius: 8px;">
                            <img src="${fotoUrl}" class="shadow-sm" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </td>
                    <td><div class="fw-bold text-dark">${item.nama_program}</div></td>
                    <td class="text-center small">${formatTanggal(item.tanggal_program)}</td>
                    <td class="text-center small">${item.lokasi_program}</td>
                    <td class="text-center"><span class="badge ${badgeClass} rounded-pill px-3 fw-normal">${item.status_program}</span></td>
                    <td class="text-center">
                         <div class="d-flex justify-content-center gap-2"> 
                            <button class="btn btn-sm btn-info text-white rounded-3 shadow-sm" onclick="window.showDetailProgram('${item.id_program}')"><i class="bi bi-eye"></i></button>
                            <button class="btn btn-sm btn-warning text-white rounded-3 shadow-sm" onclick="window.editProgram('${item.id_program}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm btn-danger rounded-3 shadow-sm" onclick="window.hapusProgram('${item.id_program}')"><i class="bi bi-trash"></i></button>
                        </div>
                    </td>
                </tr>`;
                        tbody.insertAdjacentHTML('beforeend', row);
                    });
                }


                window.hapusProgram = async function(id) {
                    const c = await Swal.fire({
                        title: 'Hapus?',
                        text: 'Data akan hilang permanen!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33'
                    });
                    if (c.isConfirmed) {
                        await fetch(`/pengurus/program/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token
                            }
                        });
                        Swal.fire('Terhapus!', '', 'success');
                        loadProgram();
                    }
                };
                window.showDetailProgram = async function(id) {
                    try {
                        const res = await fetch(`/pengurus/program/${id}`);
                        const data = await res.json();
                        document.getElementById('d_nama').textContent = data.nama_program;
                        document.getElementById('d_penyelenggara').textContent = data.penyelenggara_program;
                        document.getElementById('d_lokasi').textContent = data.lokasi_program;
                        document.getElementById('d_tanggal').textContent = formatTanggal(data.tanggal_program);
                        document.getElementById('d_deskripsi').textContent = data.deskripsi_program;
                        document.getElementById('detailFotoProgram').src = data.foto_url ||
                            'https://via.placeholder.com/400?text=No+Image';
                        let badgeClass = 'bg-secondary';
                        if (data.status_program === 'sedang berjalan') badgeClass = 'bg-warning text-dark';
                        if (data.status_program === 'belum dilaksanakan') badgeClass = 'bg-success';
                        const statusEl = document.getElementById('d_status');
                        statusEl.className = `badge rounded-pill px-3 py-2 mb-2 ${badgeClass}`;
                        statusEl.textContent = data.status_program;
                        modalDetail.show();
                    } catch (err) {
                        Swal.fire('Error', 'Gagal memuat detail', 'error');
                    }
                };

                function formatTanggal(str) {
                    if (!str) return '-';
                    return new Date(str).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }

                function renderPagination(response) {
                    const nav = document.getElementById('paginationLinks');
                    document.getElementById('paginationInfo').textContent =
                        `Menampilkan ${response.from||0} - ${response.to||0} dari ${response.total} data`;
                    let html = '<ul class="pagination justify-content-end mb-0 pagination-sm">';
                    response.links.forEach(link => {
                        html +=
                            `<li class="page-item ${link.active?'active':''} ${link.url?'':'disabled'}"><a class="page-link" href="#" data-url="${link.url}">${link.label.replace('&laquo; Previous','<').replace('Next &raquo;','>')}</a></li>`;
                    });
                    nav.innerHTML = html + '</ul>';
                    nav.querySelectorAll('a.page-link').forEach(a => a.addEventListener('click', (e) => {
                        e.preventDefault();
                        if (a.dataset.url && a.dataset.url !== 'null') {
                            state.currentPage = new URLSearchParams(a.dataset.url.split('?')[1]).get(
                            'page');
                            loadProgram();
                        }
                    }));
                }
                searchInput.addEventListener('keyup', () => {
                    clearTimeout(state.searchTimeout);
                    state.searchTimeout = setTimeout(() => {
                        state.search = searchInput.value;
                        state.currentPage = 1;
                        loadProgram();
                    }, 300);
                });
                statusFilter.addEventListener('change', () => {
                    state.status = statusFilter.value;
                    state.currentPage = 1;
                    loadProgram();
                });

                loadProgram();
            });
        </script>
    @endpush
@endsection
