@extends('layouts.app')

@section('title', 'Form Artikel')

@section('content')
    {{-- Load CSS Libraries --}}
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">

    <div class="container-fluid p-4" style="font-family: 'Poppins', sans-serif;">

        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('pengurus.artikel.index') }}" class="btn btn-light rounded-circle shadow-sm me-3"
                style="width: 40px; height: 40px; display:flex; align-items:center; justify-content:center;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h3 class="fw-bold mb-0 text-dark">
                {{ isset($artikel) ? 'Ubah Artikel' : 'Tulis Artikel Baru' }}
            </h3>
        </div>

        {{-- Form menggunakan ID untuk dihandle JS --}}
        <form id="artikelForm" enctype="multipart/form-data">
            @csrf
            {{-- Input Hidden ID Artikel (jika edit) --}}
            @if (isset($artikel))
                <input type="hidden" id="id_artikel" name="id_artikel" value="{{ $artikel->id_artikel }}">
                <input type="hidden" name="_method" value="PUT">
            @else
                <input type="hidden" id="id_artikel" name="id_artikel" value="">
            @endif

            <div class="row g-4">

                {{-- KIRI: Editor --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">

                            {{-- Judul --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold text-success small text-uppercase ls-1">Judul
                                    Artikel</label>
                                <input type="text" class="form-control rounded-pill-input fs-5 fw-bold"
                                    name="judul_artikel" id="judul_artikel"
                                    value="{{ old('judul_artikel', $artikel->judul_artikel ?? '') }}"
                                    placeholder="Masukkan Judul Artikel yang Menarik..." required>
                            </div>

                            {{-- Quill Editor --}}
                            <label class="form-label fw-bold text-success small text-uppercase ls-1">Isi Konten</label>
                            <div class="rounded-3 overflow-hidden border">
                                <div id="editor-container"
                                    style="height: 500px; background:#ffffff; font-family: 'Poppins', sans-serif;">
                                    {!! old('isi_artikel', $artikel->isi_artikel ?? '') !!}
                                </div>
                            </div>
                            {{-- Hidden Field untuk Quill --}}
                            <input type="hidden" name="isi_artikel" id="isiArtikelInput">

                        </div>
                    </div>
                </div>

                {{-- KANAN: Sidebar --}}
                <div class="col-lg-4">

                    {{-- Opsi Penerbitan --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark">Opsi Penerbitan</h6>
                        </div>
                        <div class="card-body p-4">

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Status</label>
                                @php $currentStatus = old('status_artikel', $artikel->status_artikel ?? 'draft'); @endphp
                                <select class="form-select rounded-pill-input" name="status_artikel" id="status_artikel"
                                    required>
                                    <option value="draft" {{ $currentStatus == 'draft' ? 'selected' : '' }}>Draft (Konsep)
                                    </option>
                                    <option value="published" {{ $currentStatus == 'published' ? 'selected' : '' }}>
                                        Published (Terbit)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted small">Penulis</label>
                                <input type="text" class="form-control rounded-pill-input" name="penulis_artikel"
                                    id="penulis_artikel"
                                    value="{{ old('penulis_artikel', $artikel->penulis_artikel ?? (auth()->check() ? auth()->user()->name : 'Pengurus Masjid')) }}"
                                    required>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold text-muted small">Tanggal Terbit</label>
                                @php
                                    $tgl = old(
                                        'tanggal_terbit_artikel',
                                        $artikel->tanggal_terbit_artikel ?? date('Y-m-d'),
                                    );
                                    if ($tgl instanceof \Carbon\Carbon) {
                                        $tgl = $tgl->format('Y-m-d');
                                    }
                                @endphp
                                <input type="date" class="form-control rounded-pill-input" name="tanggal_terbit_artikel"
                                    id="tanggal_terbit_artikel" value="{{ $tgl }}" required>
                            </div>

                        </div>
                    </div>

                    {{-- Foto Artikel (Dengan Cropper) --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark">Foto Sampul (16:9)</h6>
                        </div>
                        <div class="card-body p-4">

                            <input type="file" class="d-none" id="foto_artikel" name="foto_artikel" accept="image/*">
                            <input type="hidden" name="hapus_foto" id="hapus_foto_input" value="0">

                            {{-- 1. Upload Placeholder --}}
                            <div class="mb-3 {{ isset($artikel) && $artikel->foto_artikel ? 'd-none' : '' }}"
                                id="uploadContainer">
                                <label for="foto_artikel"
                                    class="btn btn-outline-success w-100 rounded-pill border-dashed py-3"
                                    style="border-style: dashed; border-width: 2px;">
                                    <i class="bi bi-camera-fill me-2"></i> Pilih Foto
                                </label>
                            </div>

                            {{-- 2. Preview Container (Rasio 16:9) --}}
                            <div id="previewContainer"
                                class="position-relative {{ isset($artikel) && $artikel->foto_artikel ? '' : 'd-none' }}">
                                {{-- Container Ratio 16:9 --}}
                                <div
                                    style="width: 100%; aspect-ratio: 16/9; overflow: hidden; border-radius: 12px; position: relative; background: #f0f0f0;">
                                    <img id="fotoPreview" src="{{ isset($artikel) ? $artikel->foto_url : '' }}"
                                        class="w-100 h-100 shadow-sm" style="object-fit: cover; display: block;">
                                </div>

                                {{-- Tombol Hapus --}}
                                <button type="button" id="clearFile"
                                    class="btn btn-danger btn-sm position-absolute rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                                    style="top: 10px; right: 10px; width: 32px; height: 32px; padding: 0; z-index: 10;">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                                {{-- Tombol Ubah (Trigger file input) --}}
                                <label for="foto_artikel"
                                    class="btn btn-light btn-sm position-absolute rounded-pill shadow-sm small fw-bold"
                                    style="bottom: 10px; right: 10px; z-index: 10; cursor: pointer;">
                                    <i class="bi bi-pencil-fill me-1"></i> Ubah
                                </label>
                            </div>
                            <small class="text-muted d-block mt-2 text-center" style="font-size: 0.8rem;">Max 2MB
                                (JPG/PNG)</small>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-grid gap-2">
                        <button type="submit" id="btnSimpan"
                            class="btn btn-gradient-green rounded-pill py-3 fw-bold shadow-sm">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ isset($artikel) ? 'Simpan Perubahan' : 'Terbitkan Artikel' }}
                        </button>
                        <a href="{{ route('pengurus.artikel.index') }}"
                            class="btn btn-light rounded-pill py-2 text-muted fw-bold">
                            Batal
                        </a>
                    </div>

                </div>
            </div>
        </form>
    </div>

    {{-- MODAL CROPPER (Baru) --}}
    <div class="modal fade" id="modalCrop" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0 pt-3 px-4">
                    <h5 class="modal-title fw-bold">Potong Gambar (16:9)</h5>
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
                        <i class="bi bi-scissors me-2"></i> Potong & Gunakan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

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
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3) !important;
        }

        .rounded-pill-input {
            border-radius: 50px !important;
            border: 1px solid #d1d5db;
            padding-left: 20px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15);
        }
    </style>

    {{-- Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

    {{-- SCRIPT LOGIC (Di-embed langsung agar akses variabel blade mudah) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {


            var toolbarOptions = [
                [{
                    'header': [1, 2, 3, false]
                }],
                ['bold', 'italic', 'underline', 'strike'],
                [{
                    'list': 'ordered'
                }, {
                    'list': 'bullet'
                }],
                [{
                    'align': []
                }],
                ['link', 'clean']
            ];
            var quill = new Quill('#editor-container', {
                theme: 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: 'Tulis konten artikel yang menarik di sini...'
            });


            const fotoInput = document.getElementById('foto_artikel');
            const uploadContainer = document.getElementById('uploadContainer');
            const previewContainer = document.getElementById('previewContainer');
            const fotoPreview = document.getElementById('fotoPreview');
            const btnClear = document.getElementById('clearFile');
            const inputHapus = document.getElementById('hapus_foto_input');


            const modalCropElement = document.getElementById('modalCrop');
            const modalCrop = new bootstrap.Modal(modalCropElement);
            const imageToCrop = document.getElementById('imageToCrop');
            const btnCropImage = document.getElementById('btnCropImage');
            const btnCancelCrop = document.getElementById('btnCancelCrop');
            const btnCloseCrop = document.getElementById('btnCloseCrop');

            let cropper = null;
            let croppedBlob = null;
            let originalFileName = 'artikel.jpg';


            if (fotoInput) {
                fotoInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        if (!file.type.startsWith('image/')) {
                            Swal.fire('Error', 'Harap upload file gambar (JPG/PNG)', 'error');
                            this.value = '';
                            return;
                        }
                        originalFileName = file.name;
                        const reader = new FileReader();
                        reader.onload = (ev) => {
                            imageToCrop.src = ev.target.result;
                            modalCrop.show();
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }


            modalCropElement.addEventListener('shown.bs.modal', () => {
                if (cropper) cropper.destroy();
                cropper = new Cropper(imageToCrop, {
                    aspectRatio: 16 / 9,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    background: false,
                });
            });


            btnCropImage.addEventListener('click', () => {
                if (!cropper) return;
                cropper.getCroppedCanvas({
                    width: 1280,
                    height: 720
                }).toBlob((blob) => {
                    croppedBlob = blob;
                    const url = URL.createObjectURL(blob);
                    fotoPreview.src = url;


                    uploadContainer.classList.add('d-none');
                    previewContainer.classList.remove('d-none');
                    inputHapus.value = '0';

                    closeCropModal();
                }, 'image/jpeg', 0.9);
            });


            const handleCancel = () => {
                fotoInput.value = '';
                closeCropModal();
            };
            btnCancelCrop.addEventListener('click', handleCancel);
            btnCloseCrop.addEventListener('click', handleCancel);

            function closeCropModal() {
                modalCrop.hide();
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }


            if (btnClear) {
                btnClear.addEventListener('click', function() {
                    fotoInput.value = '';
                    croppedBlob = null;
                    fotoPreview.src = '';
                    uploadContainer.classList.remove('d-none');
                    previewContainer.classList.add('d-none');
                    inputHapus.value = '1';
                });
            }


            const form = document.getElementById('artikelForm');
            const btnSimpan = document.getElementById('btnSimpan');
            const token = document.querySelector('input[name="_token"]').value;

            form.onsubmit = async function(e) {
                e.preventDefault();


                const isiArtikel = quill.root.innerHTML;
                const textOnly = quill.getText().trim();
                if (textOnly.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Konten Kosong',
                        text: 'Silakan tulis isi artikel terlebih dahulu!'
                    });
                    return;
                }
                document.getElementById('isiArtikelInput').value = isiArtikel;


                btnSimpan.disabled = true;
                btnSimpan.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';

                const formData = new FormData(form);


                if (croppedBlob) {
                    formData.set('foto_artikel', croppedBlob, originalFileName);
                }


                const idArtikel = document.getElementById('id_artikel').value;
                const url = idArtikel ? `/pengurus/artikel/${idArtikel}` : `/pengurus/artikel`;

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
                            icon: 'success',
                            title: 'Berhasil!',
                            text: data.message || 'Artikel berhasil disimpan.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = "{{ route('pengurus.artikel.index') }}";
                        });
                    } else {
                        throw new Error(data.message || 'Terjadi kesalahan validasi.');
                    }
                } catch (err) {
                    Swal.fire('Gagal', err.message, 'error');
                    btnSimpan.disabled = false;
                    btnSimpan.innerHTML = '<i class="bi bi-check-circle me-2"></i> Simpan';
                }
            };
        });
    </script>
@endsection
