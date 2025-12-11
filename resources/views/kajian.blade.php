@extends('layouts.app')

@section('title', 'Kajian')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- 1. Load CSS Cropper.js --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

<div class="container-fluid p-4">

    {{-- Header: Filter, Search, Tombol --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex flex-wrap align-items-center">
            {{-- Filter Status --}}
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="statusFilter" style="width: 150px;">
                    <option value="aktif" selected>Status: Aktif</option>
                    <option value="tidak_aktif">Status: Lewat</option>
                    <option value="semua">Semua Status</option>
                </select>
            </div>

            {{-- Filter Tipe --}}
            <div class="me-2 mb-2 mb-md-0">
                <select class="form-select" id="tipeFilter" style="width: 150px;">
                    <option value="" selected>Semua Tipe</option>
                    <option value="rutin">Kajian Rutin</option>
                    <option value="event">Event Besar</option>
                </select>
            </div>

            {{-- Search Bar --}}
            <div class="input-group search-bar me-2 mb-2 mb-md-0" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Cari penceramah / tema...">
            </div>
        </div>
        
        {{-- Tombol Tambah --}}
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <button class="btn btn-primary d-flex align-items-center" id="btnTambahModal">
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Kajian
            </button>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center p-3 bg-white rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0">Data Kajian</h5>
    </div>

    {{-- Wrapper Tabel --}}
    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKajian">
                    <thead class="table-light">
                        <tr> 
                            <th scope="col" style="width: 5%;" class="text-center">No</th>
                            <th scope="col" style="width: 8%;" class="text-center">Foto</th>
                            <th scope="col" style="width: 10%;" class="text-center">Tipe</th>
                            <th scope="col">Nama Penceramah</th>
                            <th scope="col">Tema Kajian</th>
                            <th scope="col" class="text-center">Waktu</th>
                            <th scope="col" class="text-center" id="sortTanggal" style="cursor:pointer;">
                                Tanggal <i id="sortIcon" class="bi bi-arrow-down"></i>
                            </th>
                            <th scope="col" style="width: 8%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data dimuat lewat JS --}}
                    </tbody>
                </table>
            </div>
            
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo">Menampilkan 0 dari 0 data</span>
                <nav id="paginationLinks"></nav>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKajian" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Jadwal Kajian</h5>
                    <p class="text-muted mb-0 small" style="line-height: 1;">Kelola agenda kajian & event besar</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formKajian" enctype="multipart/form-data" style="display: flex; flex-direction: column; flex-grow: 1; overflow: hidden;">
                
                <input type="hidden" id="id_kajian" name="id_kajian">

                <div class="modal-body">
                    <div class="form-wrapper">
                        <div class="form-section-title">
                            Detail Acara
                        </div>

                        <div class="mb-3">
                            <label for="foto_penceramah" class="form-label">Flyer / Foto Penceramah (Max 2 Mb)</label>
                            
                            <input type="file" class="d-none" id="foto_penceramah" name="foto_penceramah" accept="image/*">
                            
                            <div class="position-relative custom-file-wrapper mb-2">
                                <label for="foto_penceramah" id="foto_penceramah_label" class="form-control d-block text-truncate border cursor-pointer m-0" style="cursor: pointer;">
                                    <span class="text-muted"><i class="bi bi-cloud-upload me-2"></i>Pilih gambar...</span>
                                </label>
                                <button type="button" class="btn position-absolute d-none" id="clearFile" title="Hapus foto" 
                                        style="top: 50%; right: 0.5rem; transform: translateY(-50%); z-index: 5; color: #dc3545; background: transparent; border: 0;">
                                    <i class="bi bi-x-circle-fill fs-5"></i>
                                </button>
                            </div>
                            
                            <div id="previewContainer" class="position-relative d-none mt-3">
                                <img id="previewFoto" class="img-fluid" alt="Preview">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="tipe" class="form-label">Tipe Kajian <span class="text-danger">*</span></label>
                            <select class="form-select" id="tipe" name="tipe" required>
                                <option value="rutin">Kajian Rutin</option>
                                <option value="event">Event Besar</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nama_penceramah" class="form-label">Nama Penceramah <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_penceramah" name="nama_penceramah" placeholder="Contoh: Ust. Adi Hidayat" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="tema_kajian" class="form-label">Tema Kajian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="tema_kajian" name="tema_kajian" placeholder="Judul materi kajian" required>
                        </div>

                        <div class="row g-3">
                            <div class="col-7">
                                <div id="wrapperTanggal" class="d-none">
                                    <label for="tanggal_kajian" class="form-label">Tanggal Event <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="tanggal_kajian" name="tanggal_kajian">
                                </div>

                                <div id="wrapperHari">
                                    <label for="hari" class="form-label">Hari Rutin <span class="text-danger">*</span></label>
                                    <select class="form-select" id="hari" name="hari">
                                        <option value="">Pilih Hari...</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                        <option value="Sabtu">Sabtu</option>
                                        <option value="Ahad">Ahad</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-5">
                                <label for="waktu_kajian" class="form-label">Pukul (WIB)</label>
                                <input type="time" class="form-control" id="waktu_kajian" name="waktu_kajian">
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn-action-primary shadow-sm">
                                Simpan Agenda <i class="bi bi-calendar-check"></i>
                            </button>
                        </div>

                    </div> 
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Potong Gambar (Rasio 1:1)</h5>
                <button type="button" class="btn-close" id="btnCloseCrop" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="img-container" style="height: 500px; background-color: #333; display: flex; justify-content: center; align-items: center; overflow: hidden;">
                    <img id="imageToCrop" style="max-width: 100%; display: block;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btnCancelCrop">Batal</button>
                <button type="button" class="btn btn-primary" id="btnCropImage">
                    <i class="bi bi-scissors me-2"></i>Potong & Gunakan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');
    #modalKajian, #modalCrop { font-family: 'Poppins', sans-serif; }

    /* Modal Content */
    #modalKajian .modal-content {
        border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        height: 90vh; display: flex; flex-direction: column; overflow: hidden; 
    }
    #modalKajian .modal-header {
        border-bottom: 1px solid #f0f0f0; padding: 15px 25px; background: white; z-index: 10; flex-shrink: 0; 
    }
    #modalKajian .modal-body { overflow-y: auto; padding: 20px 25px; flex-grow: 1; }

    /* Wrapper Hijau */
    .form-wrapper { background-color: #f0fdf4; border: 1px solid #dcfce7; border-radius: 15px; padding: 20px; }
    .form-section-title { color: #166534; font-weight: 700; margin-bottom: 15px; font-size: 1.1rem; }

    /* Inputs */
    .form-label { font-weight: 600; font-size: 0.9rem; color: #374151; margin-bottom: 8px; }
    .form-control, .form-select { border-radius: 12px; padding: 12px 15px; border: 1px solid #e5e7eb; font-size: 0.95rem; }
    .form-control:focus, .form-select:focus { border-color: #22c55e; box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1); }

    /* Preview Image */
    #previewFoto { width: 100%; height: auto; max-height: 350px; object-fit: contain; border-radius: 12px; display: block; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }

    /* Button */
    .btn-action-primary { background-color: #198754; color: white; border: none; border-radius: 12px; padding: 14px; width: 100%; font-weight: 700; font-size: 1rem; display: flex; justify-content: center; align-items: center; gap: 8px; margin-top: 20px; }
    .btn-action-primary:hover { background-color: #157347; color: white; }
    
    /* Pagination */
    #paginationLinks .pagination { margin-bottom: 0; }
    #paginationLinks .page-item.disabled .page-link { background-color: #e9ecef; }
    #paginationLinks .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
</style>

{{-- 4. Load JS Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

    // --- A. DEFINISI ELEMEN ---
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // Modal Utama
    const modalKajianElement = document.getElementById('modalKajian');
    const modalKajian = new bootstrap.Modal(modalKajianElement);
    const form = document.getElementById('formKajian');
    const btnTambahModal = document.getElementById('btnTambahModal');

    // Modal Crop
    const modalCropElement = document.getElementById('modalCrop');
    const modalCrop = new bootstrap.Modal(modalCropElement);
    const imageToCrop = document.getElementById('imageToCrop');
    const btnCropImage = document.getElementById('btnCropImage');
    const btnCancelCrop = document.getElementById('btnCancelCrop');
    const btnCloseCrop = document.getElementById('btnCloseCrop');

    // Elemen Input File
    const fotoInput = document.getElementById('foto_penceramah');
    const fotoLabel = document.getElementById('foto_penceramah_label');
    const fotoLabelSpan = fotoLabel.querySelector('span');
    const clearFileBtn = document.getElementById('clearFile');
    const preview = document.getElementById('previewFoto');
    const previewContainer = document.getElementById('previewContainer');

    // Elemen Logic Hari/Tanggal
    const tipeInput = document.getElementById('tipe'); 
    const wrapperHari = document.getElementById('wrapperHari');
    const wrapperTanggal = document.getElementById('wrapperTanggal');
    const inputHari = document.getElementById('hari');
    const inputTanggal = document.getElementById('tanggal_kajian');

    // Filter & Table
    const tbody = document.querySelector('#tabelKajian tbody');
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const tipeFilter = document.getElementById('tipeFilter');
    const paginationContainer = document.getElementById('paginationLinks');
    const paginationInfo = document.getElementById('paginationInfo');
    const sortTanggal = document.getElementById('sortTanggal');
    const sortIcon = document.getElementById('sortIcon');
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;

    // --- B. STATE MANAGEMENT ---
    let state = {
        currentPage: 1, status: 'aktif', tipe: '', search: '',
        perPage: 10, sortBy: 'tanggal_kajian', sortDir: 'desc',        
        searchTimeout: null
    };

    let cropper = null;
    let croppedBlob = null; // Menyimpan Blob hasil crop
    let originalFileName = '';

    // --- C. LOGIKA TAMPILAN (Rutin vs Event) ---
    function toggleInputType() {
        if (!tipeInput) return;
        if (tipeInput.value === 'rutin') {
            wrapperHari.classList.remove('d-none');
            wrapperTanggal.classList.add('d-none');
            if(inputHari) inputHari.setAttribute('required', 'required');
            if(inputTanggal) {
                inputTanggal.removeAttribute('required');
                inputTanggal.value = '';
            }
        } else {
            wrapperHari.classList.add('d-none');
            wrapperTanggal.classList.remove('d-none');
            if(inputTanggal) inputTanggal.setAttribute('required', 'required');
            if(inputHari) {
                inputHari.removeAttribute('required');
                inputHari.value = '';
            }
        }
    }
    
    if (tipeInput) {
        tipeInput.addEventListener('change', toggleInputType);
        toggleInputType(); // Init
    }

    // --- D. LOGIKA CROPPING IMAGE ---
    
    // 1. User Pilih File
    if (fotoInput) {
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
                    modalKajian.hide(); // Sembunyikan modal utama
                    modalCrop.show();   // Tampilkan modal crop
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // 2. Init Cropper saat modal muncul
    modalCropElement.addEventListener('shown.bs.modal', () => {
        if(cropper) cropper.destroy();
        cropper = new Cropper(imageToCrop, {
            aspectRatio: 1 / 1, // KUNCI 1:1
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            background: false,
        });
    });

    // 3. Tombol "Potong & Gunakan"
    btnCropImage.addEventListener('click', () => {
        if (!cropper) return;
        cropper.getCroppedCanvas({ width: 800, height: 800 }).toBlob((blob) => {
            
            croppedBlob = blob; // Simpan blob
            
            // Tampilkan Preview
            const url = URL.createObjectURL(blob);
            preview.src = url;
            previewContainer.classList.remove('d-none');

            // Update Label
            fotoLabelSpan.textContent = "Gambar (1:1) Siap Upload";
            fotoLabelSpan.classList.remove('text-muted');
            clearFileBtn.classList.remove('d-none');

            closeCropModal();
            modalKajian.show(); // Buka lagi modal utama

        }, 'image/jpeg', 0.9);
    });

    // 4. Handle Cancel Crop
    const handleCancelCrop = () => {
        fotoInput.value = ''; // Reset input
        closeCropModal();
        modalKajian.show();
    };

    btnCancelCrop.addEventListener('click', handleCancelCrop);
    btnCloseCrop.addEventListener('click', handleCancelCrop);

    function closeCropModal() {
        modalCrop.hide();
        if(cropper) { cropper.destroy(); cropper = null; }
    }

    // --- E. LOGIKA SUBMIT FORM ---
    form.addEventListener('submit', async e => {
        e.preventDefault();
        setLoading(true);

        const id = document.getElementById('id_kajian').value;
        const formData = new FormData(form);

        // [PENTING] Ganti file input dengan Blob Cropped
        if (croppedBlob) {
            const fname = originalFileName || 'kajian.jpg';
            formData.set('foto_penceramah', croppedBlob, fname);
        }

        const url = id ? `/pengurus/kajian/${id}` : '/pengurus/kajian';
        if (id) formData.append('_method', 'PUT');

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                Swal.fire('Berhasil!', data.message, 'success');
                modalKajian.hide();
                loadKajian();
            } else {
                if (res.status === 422 && data.errors) {
                    let msg = Object.values(data.errors).map(e => e[0]).join('<br>');
                    throw new Error(msg);
                }
                throw new Error(data.message || 'Terjadi kesalahan');
            }
        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        } finally {
            setLoading(false);
        }
    });

    // --- F. RESET & HELPER ---
    
    // Buka Modal Tambah
    if (btnTambahModal) {
        btnTambahModal.addEventListener('click', () => {
            document.getElementById('id_kajian').value = '';
            form.reset();
            resetFileState();
            if(tipeInput) { tipeInput.value = 'rutin'; toggleInputType(); }
            modalKajian.show();
        });
    }

    function resetFileState() {
        croppedBlob = null;
        originalFileName = '';
        fotoInput.value = '';
        preview.src = '';
        previewContainer.classList.add('d-none');
        clearFileBtn.classList.add('d-none');
        fotoLabelSpan.textContent = "Pilih gambar...";
        fotoLabelSpan.classList.add('text-muted');
    }

    clearFileBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        resetFileState();
    });

    function setLoading(isLoading) {
        if(isLoading) {
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner-border spinner-border-sm"></span> Menyimpan...`;
        } else {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        }
    }

    // --- G. FUNGSI EDIT & DELETE ---
    window.editKajian = async function(id) {
        try {
            const res = await fetch(`/pengurus/kajian/${id}`);
            if (!res.ok) throw new Error('Gagal mengambil data');
            const data = await res.json();

            document.getElementById('id_kajian').value = data.id_kajian;
            
            // Set Tipe & Trigger Toggle
            if(tipeInput) {
                tipeInput.value = data.tipe; 
                toggleInputType(); 
            }

            document.getElementById('nama_penceramah').value = data.nama_penceramah;
            document.getElementById('tema_kajian').value = data.tema_kajian;
            if (data.waktu_kajian) document.getElementById('waktu_kajian').value = data.waktu_kajian.substring(0, 5);

            // Isi hari/tanggal
            if (data.tipe === 'rutin') {
                if(inputHari) inputHari.value = data.hari;
            } else {
                if (data.tanggal_kajian && inputTanggal) inputTanggal.value = data.tanggal_kajian.split('T')[0];
            }

            // Reset blob, tampilkan foto lama
            croppedBlob = null; 
            if (data.foto_penceramah) {
                fotoLabelSpan.textContent = data.foto_penceramah.split('/').pop();
                fotoLabelSpan.classList.remove('text-muted');
                clearFileBtn.classList.remove('d-none');
                preview.src = data.foto_url;
                previewContainer.classList.remove('d-none');
            } else {
                resetFileState();
            }

            modalKajian.show();
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    }

    window.hapusKajian = async function(id) {
        const c = await Swal.fire({
            title: 'Hapus?', text: "Data hilang permanen!", icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Hapus'
        });
        if (c.isConfirmed) {
            try {
                const fd = new FormData(); fd.append('_method', 'DELETE');
                const res = await fetch(`/pengurus/kajian/${id}`, {
                    method: 'POST', headers: { 'X-CSRF-TOKEN': token }, body: fd
                });
                if (res.ok) {
                    Swal.fire('Terhapus!', '', 'success');
                    loadKajian();
                } else throw new Error('Gagal menghapus');
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        }
    }

    // --- H. LOAD DATA & FILTER (Sama seperti sebelumnya) ---
    async function loadKajian() {
        let colCount = tbody.closest('table').querySelector('thead tr').cells.length;
        tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center"><div class="spinner-border text-primary"></div></td></tr>`;
        const url = `/pengurus/kajian-data?page=${state.currentPage}&status=${state.status}&tipe=${state.tipe}&search=${state.search}&sortBy=${state.sortBy}&sortDir=${state.sortDir}`;
        try {
            const res = await fetch(url);
            const response = await res.json(); 
            renderTable(response.data, response.from || 1);
            renderPagination(response);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="${colCount}" class="text-center text-danger">Gagal memuat data</td></tr>`;
        }
    }

    function renderTable(data, startNum) {
        tbody.innerHTML = ''; 
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center">Belum ada data.</td></tr>`;
            return;
        }
        data.forEach((item, i) => {
            let tipeBadge = item.tipe === 'rutin' 
                ? '<span class="badge bg-info text-dark">Rutin</span>' 
                : '<span class="badge bg-warning text-dark">Event</span>';

            let infoJadwal = item.tipe === 'rutin' 
                ? `<span class="fw-bold">Setiap ${item.hari || '-'}</span>` 
                : (item.tanggal_kajian ? new Date(item.tanggal_kajian).toLocaleDateString('id-ID') : '-');

            const row = `
            <tr>
                <td class="text-center">${startNum + i}</td>
                <td class="text-center"><img src="${item.foto_url}" class="rounded" style="width:50px;height:50px;object-fit:cover;"></td>
                <td class="text-center">${tipeBadge}</td> 
                <td>${item.nama_penceramah}</td>
                <td>${item.tema_kajian}</td>
                <td class="text-center">${item.waktu_kajian ? item.waktu_kajian.substring(0, 5) : '-'}</td>
                <td class="text-center">${infoJadwal}</td>
                <td class="text-center">
                    <button class="btn btn-warning btn-sm" onclick="editKajian('${item.id_kajian}')"><i class="bi bi-pencil"></i></button>
                    <button class="btn btn-danger btn-sm" onclick="hapusKajian('${item.id_kajian}')"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(response) {
        paginationInfo.textContent = `Menampilkan ${response.from||0} - ${response.to||0} dari ${response.total} data`;
        let linksHtml = '<ul class="pagination justify-content-center mb-0">';
        response.links.forEach(link => {
            let label = link.label.replace('&laquo; Previous', '<').replace('Next &raquo;', '>');
            let active = link.active ? 'active' : '';
            let disabled = !link.url ? 'disabled' : '';
            linksHtml += `<li class="page-item ${active} ${disabled}"><a class="page-link" href="${link.url}">${label}</a></li>`;
        });
        linksHtml += '</ul>';
        paginationContainer.innerHTML = linksHtml;
    }

    // Listeners Filter
    searchInput.addEventListener('keyup', () => {
        clearTimeout(state.searchTimeout);
        state.searchTimeout = setTimeout(() => { state.search = searchInput.value; state.currentPage = 1; loadKajian(); }, 300);
    });
    [statusFilter, tipeFilter].forEach(el => {
        if(el) el.addEventListener('change', () => { 
            state[el.id.replace('Filter','')] = el.value; 
            state.currentPage = 1; 
            loadKajian(); 
        });
    });
    if(sortTanggal) sortTanggal.addEventListener('click', () => {
        state.sortDir = state.sortDir === 'asc' ? 'desc' : 'asc';
        sortIcon.className = state.sortDir === 'asc' ? 'bi bi-arrow-up' : 'bi bi-arrow-down';
        loadKajian();
    });
    paginationContainer.addEventListener('click', e => {
        e.preventDefault();
        if(e.target.classList.contains('page-link') && e.target.href) {
            state.currentPage = new URL(e.target.href).searchParams.get('page');
            loadKajian();
        }
    });

    loadKajian(); // Run
});
</script>
@endsection