@extends('layouts.public')

@section('title', 'Donasi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; }

    /* --- GLOBAL RATIO 4:3 SETTING --- */
    .ratio-4-3 { aspect-ratio: 4/3 !important; object-fit: cover; width: 100%; }

    /* --- Card List Style --- */
    .donation-list-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); margin-bottom: 1.5rem; overflow: hidden; transition: transform 0.3s ease; height: 100%; }
    .donation-list-card:hover { transform: translateY(-5px); }
    .donation-list-card .col-4 { display: flex; align-items: stretch; padding: 0; }
    
    .donation-list-card .card-img { 
        border-radius: 12px 0 0 12px !important; 
        height: 100%; 
        width: 100%;
        aspect-ratio: 4/3 !important; 
        object-fit: cover;
    }
    
    .donation-list-card .card-body { padding: 1rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    .donation-list-card .card-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.3; }
    
    .btn-detail-donasi { background-color: #fff; border: 1px solid #1abc9c; color: #1abc9c; border-radius: 50px; padding: 0.3rem 0.8rem; font-size: 0.75rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
    .btn-detail-donasi:hover { background-color: #1abc9c; color: white; }

    /* --- Slider Style --- */
    .swiper-container-wrapper { position: relative; padding: 0; }
    .donation-slide { 
        position: relative; 
        width: 100%; 
        border-radius: 16px; 
        overflow: hidden; 
        aspect-ratio: 4/3 !important; 
    }
    .donation-slide-img { width: 100%; height: 100%; object-fit: cover; }
    .donation-slide-overlay { position: absolute; bottom: 0; left: 0; width: 100%; height: 70%; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); padding: 20px; display: flex; flex-direction: column; justify-content: flex-end; color: white; }

    /* --- MODAL STYLE --- */
    .modal-narrow { max-width: 500px; margin: 1.75rem auto; }

    .modal-header-img { 
        width: 100%; 
        aspect-ratio: 4/3 !important; 
        object-fit: cover; 
        border-bottom: 1px solid #eee;
        height: auto; 
    }

    /* Form Pembayaran */
    .box-payment { 
        background-color: #f0fdf4; 
        border: 1px solid #bbf7d0; 
        border-radius: 12px; 
        padding: 20px; 
        margin-bottom: 20px;
    }
    
    /* Input Nominal (Standard Style) */
    .input-nominal { 
        font-size: 1rem; 
        font-weight: 400; 
        color: #333; 
        background-color: white;
    }
    .input-nominal::placeholder {
        color: #6c757d;
        opacity: 1;
    }

    /* List Komentar */
    .donatur-item { border-bottom: 1px dashed #eee; padding: 12px 0; }
    .donatur-item:last-child { border-bottom: none; }
    .donatur-avatar { 
        width: 40px; height: 40px; 
        background: #e2e8f0; color: #64748b;
        border-radius: 50%; 
        display: flex; align-items: center; justify-content: center; 
        font-weight: bold; font-size: 1.1rem; 
        margin-right: 12px; flex-shrink: 0;
    }

    /* Style untuk Baca Selengkapnya */
    .desc-clamped {
        display: -webkit-box;
        -webkit-line-clamp: 5; 
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .btn-read-more {
        color: #198754; 
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        display: inline-block;
        margin-bottom: 1rem;
    }
    .btn-read-more:hover { text-decoration: underline; }

    @media (min-width: 768px) { .donation-list-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; } }
    @media (min-width: 992px) { .donation-list-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')

{{-- Meta CSRF Token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container pt-4 pb-3">
    <h2 class="donasi-title-heading">Mari Berdonasi</h2>
    <p class="donasi-title-sub">Setiap donasi Anda membawa harapan baru.</p>
</div>

{{-- LOGIKA UTAMA: Cek apakah ada program donasi aktif --}}
@if($programDonasi->isEmpty())

    {{-- TAMPILAN JIKA KOSONG (Div Biru Muda) --}}
    <div class="container mb-5">
        <div class="alert text-center py-5 rounded-4 shadow-sm border-0" style="background-color: #dbeafe; color: #1e40af;">
            <i class="bi bi-info-circle mb-3 d-block" style="font-size: 3rem; color: #3b82f6;"></i>
            <h5 class="fw-bold" style="color: #1e3a8a;">Belum ada Donasi</h5>
            <p class="mb-0" style="color: #1e40af;">Saat ini belum ada program donasi yang aktif atau tersedia.</p>
        </div>
    </div>

@else

    {{-- TAMPILAN JIKA ADA DATA (Slider & List) --}}

    {{-- 1. SLIDER --}}
    <div class="container mb-5">
        <div class="swiper-container-wrapper">
            <div class="swiper donasi-swiper">
                <div class="swiper-wrapper">
                    @foreach($programDonasi as $program)
                    <div class="swiper-slide">
                        <div class="donation-slide">
                            <img src="{{ $program->gambar_url }}" class="donation-slide-img" alt="{{ $program->nama_donasi }}">
                            <div class="donation-slide-overlay">
                                <h5 class="fw-bold">{{ $program->nama_donasi }}</h5>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Terkumpul: Rp {{ number_format($program->dana_terkumpul, 0, ',', '.') }}</span>
                                    <span>{{ $program->persentase_asli }}%</span>
                                </div>
                                <div class="progress mb-3" style="height: 5px; background: rgba(255,255,255,0.3);">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $program->persentase }}%"></div>
                                </div>
                                <button onclick="openDonasiModal('{{ $program->id_donasi }}')" class="btn btn-light btn-sm w-100 rounded-pill fw-bold text-success">Lihat Detail</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="donasi-button-next"></div>
                <div class="donasi-button-prev"></div>
            </div>
        </div>
    </div>

    {{-- 2. LIST CARD --}}
    <div class="container mt-3 mb-5"> 
        <h4 class="fw-bold mb-4 text-dark">Daftar Donasi</h4>
        <div class="donation-list-grid">
            @foreach($programDonasi as $program)
            <div class="card donation-list-card">
                <div class="row g-0 h-100">
                    <div class="col-4">
                        <img src="{{ $program->gambar_url }}" class="card-img" alt="{{ $program->nama_donasi }}">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <div>
                                <h5 class="card-title text-dark">{{ $program->nama_donasi }}</h5>
                                <div class="mb-2">
                                    @if($program->tanggal_selesai)
                                        @php $sisa = \Carbon\Carbon::now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($program->tanggal_selesai)->startOfDay(), false); @endphp
                                        
                                        @if($sisa > 0) 
                                            <small class="text-warning fw-bold"><i class="bi bi-clock"></i> Sisa {{ ceil($sisa) }} hari</small>
                                        @elseif($sisa == 0) 
                                            <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> Berakhir Hari Ini</small>
                                        @else 
                                            {{-- Seharusnya tidak muncul karena difilter controller, tapi untuk jaga-jaga --}}
                                            <small class="text-muted fw-bold"><i class="bi bi-x-circle"></i> Berakhir</small> 
                                        @endif
                                    @else
                                        <small class="text-success fw-bold"><i class="bi bi-infinity"></i> Unlimited</small>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <span class="small text-muted">Terkumpul</span>
                                    <span class="fw-bold text-success small">Rp {{ number_format($program->dana_terkumpul, 0, ',', '.') }}</span>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $program->persentase }}%"></div>
                                </div>
                                <div class="text-end">
                                    <button onclick="openDonasiModal('{{ $program->id_donasi }}')" class="btn-detail-donasi">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

@endif

{{-- ========================================== --}}
{{-- MODAL DETAIL DONASI (Tetap Load di Luar IF) --}}
{{-- ========================================== --}}
{{-- Saran: Pindahkan kode modal panjang tadi ke file terpisah atau biarkan di sini tapi pastikan di luar blok @if($programDonasi->isEmpty()) agar JS tidak error --}}

{{-- Kode Modal kamu yg asli taruh disini (di bawah ini) --}}
<div class="modal fade" id="modalDonasiDetail" tabindex="-1" aria-hidden="true">
    {{-- ... (Isi modal sama persis seperti kode kamu sebelumnya) ... --}}
    <div class="modal-dialog modal-narrow"> 
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            
            {{-- Header Image --}}
            <div class="position-relative bg-dark">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 bg-white p-2 rounded-circle shadow-sm" style="z-index: 10; opacity: 1;" data-bs-dismiss="modal"></button>
                <img id="mFoto" src="" class="modal-header-img" alt="Cover Donasi">
            </div>

            <div class="modal-body p-4">
                
                {{-- Judul & Progress --}}
                <h3 class="fw-bold mb-3" id="mNama">Judul Program</h3>
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <div>
                            <span class="text-muted small">Terkumpul</span><br>
                            <span class="fw-bold text-success fs-4" id="mTerkumpul">Rp 0</span>
                        </div>
                        <div class="text-end">
                            <span class="text-muted small" id="mSisaHari">0 Hari Lagi</span><br>
                            <span class="small text-muted">Target: <span id="mTarget" class="fw-bold">Rp 0</span></span>
                        </div>
                    </div>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div id="mProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                    </div>
                </div>

                {{-- LAYOUT STACK (Ke Bawah) --}}
                <div class="d-flex flex-column">
                    
                    {{-- 1. Form Pembayaran --}}
                    <div class="box-payment">
                        <h5 class="fw-bold mb-3 text-success">Donasi Sekarang</h5>
                        
                        <form id="formDonasiOnline">
                            <input type="hidden" id="pay_id_donasi" name="id_donasi">

                            {{-- Input Nominal --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Nominal Donasi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
                                    <input type="text" inputmode="numeric" class="form-control border-start-0 input-nominal" id="pay_nominal" name="nominal" placeholder="1.000" autocomplete="off" required>
                                </div>
                                <small class="text-danger d-none" id="nominalError" style="font-size: 0.75rem;">Minimal Rp 1.000</small>
                            </div>

                            {{-- Input Data Diri --}}
                            <div class="mb-2">
                                <input type="text" class="form-control mb-2" id="pay_nama" name="nama" placeholder="Nama (Opsional)">
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="anonimCheck">
                                <label class="form-check-label small text-muted" for="anonimCheck">
                                    Sembunyikan nama saya (Orang Baik)
                                </label>
                            </div>

                            {{-- Input Pesan --}}
                            <div class="mb-3">
                                <textarea class="form-control" id="pay_pesan" name="pesan" rows="2" placeholder="Tulis doa atau dukungan (Opsional)"></textarea>
                            </div>

                            {{-- Tombol Bayar --}}
                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 fs-6 shadow-sm" id="btnPay">
                                Bayar Sekarang <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                            
                            <div class="text-center mt-3 pt-2 border-top border-success border-opacity-25">
                                <small class="text-muted d-block mb-2" style="font-size: 0.7rem;">Transfer Manual / Tanya Admin?</small>
                                <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success btn-sm w-100">
                                    <i class="bi bi-whatsapp me-1"></i> Hubungi WhatsApp
                                </a>
                            </div>
                        </form>
                    </div>

                    {{-- 2. Deskripsi & Komentar --}}
                    <div>
                        <h6 class="fw-bold mb-2">Tentang Program</h6>
                        
                        <p class="text-muted small mb-1 desc-clamped" id="mDeskripsi" style="white-space: pre-line; line-height: 1.6;">Memuat deskripsi...</p>
                        <a id="btnBacaSelengkapnya" class="btn-read-more d-none">Baca Selengkapnya...</a>

                        <hr class="border-secondary-subtle">

                        <h6 class="fw-bold mb-3">Doa & Dukungan</h6>
                        
                        <div id="donaturList" class="mb-3"></div>
                        
                        <div class="text-center">
                            <button id="btnLoadMore" class="btn btn-sm btn-outline-secondary rounded-pill d-none px-4">Lihat Lebih Banyak</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    // --- Init Swiper ---
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.donasi-swiper')) {
            const slideCount = document.querySelectorAll('.swiper-slide').length;
            new Swiper('.donasi-swiper', { 
                slidesPerView: 1, spaceBetween: 15, 
                loop: slideCount > 3, 
                navigation: { nextEl: '.donasi-button-next', prevEl: '.donasi-button-prev' },
                breakpoints: { 768: { slidesPerView: 2 }, 992: { slidesPerView: 3 } }
            }); 
        }
        
        // --- REVISI: FORMAT RUPIAH DI INPUT ---
        const nominalInput = document.getElementById('pay_nominal');
        if(nominalInput) {
            nominalInput.addEventListener('input', function(e) {
                // 1. Ambil value saat ini dan hapus semua karakter non-angka
                let rawValue = this.value.replace(/[^0-9]/g, '');
                
                // 2. Jika ada isinya, format jadi ribuan
                if (rawValue) {
                    this.value = new Intl.NumberFormat('id-ID').format(rawValue);
                } else {
                    this.value = '';
                }
            });
        }
    });

    // --- VARIABLES ---
    let modalDetail = null; 
    let currentDonaturPage = 1;
    let currentDonasiId = null;

    // --- OPEN MODAL FUNCTION ---
    function openDonasiModal(id) {
        if (!modalDetail) {
            modalDetail = new bootstrap.Modal(document.getElementById('modalDonasiDetail'));
        }

        currentDonasiId = id;
        currentDonaturPage = 1;

        // Set ID Donasi
        const inputId = document.getElementById('pay_id_donasi');
        if(inputId) inputId.value = id; 
        
        // Reset UI
        document.getElementById('donaturList').innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';
        document.getElementById('btnLoadMore').classList.add('d-none');
        document.getElementById('formDonasiOnline').reset();
        document.getElementById('pay_nama').readOnly = false;
        document.getElementById('btnPay').innerHTML = 'Bayar Sekarang <i class="bi bi-arrow-right ms-1"></i>';
        document.getElementById('btnPay').disabled = false;
        document.getElementById('nominalError').classList.add('d-none');
        
        // Reset Read More
        const descEl = document.getElementById('mDeskripsi');
        const btnRead = document.getElementById('btnBacaSelengkapnya');
        descEl.classList.add('desc-clamped'); 
        btnRead.classList.add('d-none'); 
        btnRead.textContent = 'Baca Selengkapnya...';

        // Show Modal
        modalDetail.show();

        // Fetch Data
        fetchDataDonasi(id);
    }

    function fetchDataDonasi(id) {
        fetch(`/donasi/detail/${id}?page=${currentDonaturPage}`)
            .then(res => res.json())
            .then(data => {
                // Update Info Program (Page 1)
                if (currentDonaturPage === 1) {
                    document.getElementById('mFoto').src = data.foto_url;
                    document.getElementById('mNama').textContent = data.nama_donasi;
                    
                    // --- LOGIC BACA SELENGKAPNYA ---
                    const descEl = document.getElementById('mDeskripsi');
                    const btnRead = document.getElementById('btnBacaSelengkapnya');
                    const deskripsi = data.deskripsi || 'Tidak ada deskripsi.';
                    
                    descEl.textContent = deskripsi;

                    if (deskripsi.length > 250) {
                        btnRead.classList.remove('d-none');
                        btnRead.onclick = function() {
                            if (descEl.classList.contains('desc-clamped')) {
                                descEl.classList.remove('desc-clamped');
                                this.textContent = 'Tutup';
                            } else {
                                descEl.classList.add('desc-clamped');
                                this.textContent = 'Baca Selengkapnya...';
                            }
                        };
                    } else {
                        btnRead.classList.add('d-none');
                    }

                    document.getElementById('mTerkumpul').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.terkumpul);
                    document.getElementById('mTarget').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.target_dana);
                    document.getElementById('mProgress').style.width = data.persentase + '%';
                    
                    let sisaText = '∞ Unlimited';
                    if(data.sisa_hari !== null) {
                        sisaText = data.sisa_hari > 0 ? data.sisa_hari + ' Hari Lagi' : 'Berakhir';
                    }
                    document.getElementById('mSisaHari').textContent = sisaText;
                    
                    document.getElementById('donaturList').innerHTML = ''; // Clear loading
                }

                // Update List Donatur
                const listContainer = document.getElementById('donaturList');
                
                if (data.donatur.data.length > 0) {
                    let pesanCount = 0;
                    data.donatur.data.forEach(d => {
                        if(d.pesan && d.pesan.trim() !== "" && d.pesan !== "-") {
                            pesanCount++;
                            const avatarLetter = d.nama_donatur ? d.nama_donatur.charAt(0).toUpperCase() : 'O'; 
                            const nama = d.nama_donatur || 'Orang Baik'; 
                            const nominal = new Intl.NumberFormat('id-ID').format(d.nominal);
                            
                            const item = `
                                <div class="d-flex align-items-start donatur-item animate__animated animate__fadeIn">
                                    <div class="donatur-avatar">${avatarLetter}</div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold small text-dark">${nama}</div>
                                            <small class="text-muted" style="font-size: 0.7rem;">Rp ${nominal}</small>
                                        </div>
                                        <div class="bg-light p-2 mt-1 rounded small fst-italic text-secondary">
                                            "${d.pesan}"
                                        </div>
                                    </div>
                                </div>
                            `;
                            listContainer.insertAdjacentHTML('beforeend', item);
                        }
                    });

                    if (pesanCount === 0 && listContainer.innerHTML === '') {
                         listContainer.innerHTML = '<div class="text-center small text-muted py-3">Belum ada doa/dukungan.</div>';
                    }
                } 
                else if (currentDonaturPage === 1) {
                    listContainer.innerHTML = '<div class="text-center small text-muted py-3">Belum ada doa/dukungan. Jadilah yang pertama!</div>';
                }

                // Pagination Button
                const btnLoadMore = document.getElementById('btnLoadMore');
                if (data.donatur.next_page_url) {
                    btnLoadMore.classList.remove('d-none');
                    btnLoadMore.onclick = () => {
                        currentDonaturPage++;
                        fetchDataDonasi(id);
                    };
                } else {
                    btnLoadMore.classList.add('d-none');
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('donaturList').innerHTML = '<small class="text-danger">Gagal memuat data.</small>';
            });
    }

    // --- LOGIC FORM DONASI ---
    
    // Checkbox Hamba Allah
    document.getElementById('anonimCheck').addEventListener('change', function() {
        const namaInput = document.getElementById('pay_nama');
        if(this.checked) {
            namaInput.value = 'Orang Baik'; 
            namaInput.readOnly = true;
        } else {
            namaInput.value = '';
            namaInput.readOnly = false;
        }
    });

    // Submit Form Payment
    document.getElementById('formDonasiOnline').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // --- REVISI: Bersihkan titik sebelum validasi dan kirim data ---
        // Ambil string mentah (misal "100.000"), hapus titik jadi "100000", lalu parse ke integer
        const nominalStr = document.getElementById('pay_nominal').value.replace(/\./g, '');
        const nominal = parseInt(nominalStr);
        
        if (!nominal || nominal < 1000) {
            document.getElementById('nominalError').classList.remove('d-none');
            return;
        }
        document.getElementById('nominalError').classList.add('d-none');

        // Default Value Nama
        if(!document.getElementById('pay_nama').value) document.getElementById('pay_nama').value = 'Orang Baik';
        
        const btn = document.getElementById('btnPay');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        btn.disabled = true;

        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        // --- PENTING: Update data nominal di object agar yang dikirim Angka Bersih ---
        data.nominal = nominal; 
        
        // --- INJECT EMAIL DUMMY OTOMATIS ---
        data.email = 'guest@example.com'; 

        try {
            const response = await fetch('/donasi/checkout', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.status === 'success') {
                window.snap.pay(result.snap_token, {
                    onSuccess: function(result){
                        Swal.fire('Alhamdulillah!', 'Donasi berhasil diterima.', 'success').then(() => {
                            modalDetail.hide();
                            location.reload();
                        });
                    },
                    onPending: function(result){
                        modalDetail.hide(); 
                    },
                    onError: function(result){
                        Swal.fire('Gagal', 'Pembayaran gagal atau dibatalkan.', 'error');
                    },
                    onClose: function(){
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    }
                });
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error(error);
            Swal.fire('Gagal', error.message || 'Terjadi kesalahan sistem.', 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>
@endpush