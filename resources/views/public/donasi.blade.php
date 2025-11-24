@extends('layouts.public')

@section('title', 'Donasi')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; }

    /* --- Card List Style --- */
    .donation-list-card { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); margin-bottom: 1.5rem; overflow: hidden; transition: transform 0.3s ease; height: 100%; }
    .donation-list-card:hover { transform: translateY(-5px); }
    
    .donation-list-card .col-4 { display: flex; align-items: stretch; }
    .donation-list-card .card-img { object-fit: cover; width: 100%; height: 100%; aspect-ratio: 4/5; border-radius: 12px 0 0 12px !important; }

    .donation-list-card .card-body { padding: 1rem; display: flex; flex-direction: column; justify-content: space-between; height: 100%; }
    .donation-list-card .card-title { font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; line-height: 1.3; }
    
    .btn-detail-donasi { background-color: #fff; border: 1px solid #1abc9c; color: #1abc9c; border-radius: 50px; padding: 0.3rem 0.8rem; font-size: 0.75rem; font-weight: 600; text-decoration: none; transition: all 0.2s; }
    .btn-detail-donasi:hover { background-color: #1abc9c; color: white; }

    /* Slider Style */
    .swiper-container-wrapper { position: relative; padding: 0; }
    .donation-slide { position: relative; height: 350px; width: 100%; border-radius: 16px; overflow: hidden; }
    .donation-slide-img { width: 100%; height: 100%; object-fit: cover; }
    .donation-slide-overlay { position: absolute; bottom: 0; left: 0; width: 100%; height: 70%; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); padding: 20px; display: flex; flex-direction: column; justify-content: flex-end; color: white; }

    /* Modal Styles */
    .modal-donasi-img { width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem; }
    .payment-option { border: 1px solid #eee; border-radius: 8px; padding: 10px; margin-bottom: 10px; display: flex; align-items: center; cursor: pointer; transition: background 0.2s; }
    .payment-option:hover { background-color: #f8f9fa; }
    .payment-icon { width: 40px; height: 40px; object-fit: contain; margin-right: 10px; }
    
    .donatur-msg-box { background: #f8f9fa; border-radius: 8px; padding: 10px; margin-bottom: 10px; font-size: 0.9rem; }
    .donatur-name { font-weight: 600; color: #333; margin-bottom: 2px; display: block;}
    .donatur-text { color: #555; font-style: italic; }

    @media (min-width: 768px) { .donation-list-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; } }
    @media (min-width: 992px) { .donation-list-grid { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')

{{-- Meta CSRF Token untuk AJAX --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container pt-4 pb-3">
    <h2 class="donasi-title-heading">Mari Berdonasi</h2>
    <p class="donasi-title-sub">Setiap donasi Anda membawa harapan baru.</p>
</div>

{{-- SLIDER --}}
<div class="container mb-5">
    @if(!$programDonasi->isEmpty())
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
            </div>
        </div>
    @endif
</div>

{{-- LIST CARD --}}
<div class="container mt-3"> 
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
                                    @php 
                                        $sisa = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($program->tanggal_selesai), false);
                                    @endphp
                                    @if($sisa > 0)
                                        <small class="text-warning fw-bold"><i class="bi bi-clock"></i> Sisa {{ ceil($sisa) }} hari</small>
                                    @elseif($sisa == 0)
                                        <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle"></i> Berakhir Hari Ini</small>
                                    @else
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

{{-- MODAL DETAIL DONASI --}}
<div class="modal fade" id="modalDonasiDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable"> 
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                
                {{-- Loading Spinner --}}
                <div id="loadingDetail" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                {{-- Konten Detail --}}
                <div id="contentDetail" class="d-none">
                    <img id="mFoto" src="" class="modal-donasi-img" alt="Donasi">
                    
                    <h4 class="fw-bold mb-2" id="mNama">Nama Program</h4>
                    
                    {{-- Stats Bar --}}
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-muted">Terkumpul</span>
                            <span class="fw-bold text-success" id="mTerkumpul">Rp 0</span>
                        </div>
                        <div class="progress mb-2" style="height: 8px;">
                            <div id="mProgress" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-muted">
                            <span id="mTarget">Target: Rp 0</span>
                            <span id="mSisaHari">0 Hari Lagi</span>
                        </div>
                    </div>

                    <p class="text-muted small mb-4" id="mDeskripsi">Deskripsi program...</p>

                    {{-- (BARU) FORM DONASI ONLINE MIDTRANS --}}
                    <div class="card border-success border-opacity-25 bg-success bg-opacity-10 mb-4">
                        <div class="card-body p-3">
                            <h6 class="fw-bold text-success mb-3"><i class="bi bi-lightning-charge-fill"></i> Donasi Instan (QRIS/E-Wallet)</h6>
                            
                            <form id="formDonasiOnline">
                                <input type="hidden" id="pay_id_donasi" name="id_donasi">
                                
                                <div class="mb-2">
                                    <label class="small fw-bold text-muted">Nominal Donasi</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="pay_nominal" name="nominal" placeholder="Min. 1.000" required min="1000">
                                    </div>
                                </div>
                                
                                <div class="row g-2 mb-2">
                                    <div class="col-6">
                                        <input type="text" class="form-control form-control-sm" id="pay_nama" name="nama" placeholder="Nama Anda (Hamba Allah)" required>
                                    </div>
                                    <div class="col-6">
                                        <input type="email" class="form-control form-control-sm" id="pay_email" name="email" placeholder="Email (Untuk Bukti)" required>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <textarea class="form-control form-control-sm" id="pay_pesan" name="pesan" rows="1" placeholder="Doa / Pesan (Opsional)"></textarea>
                                </div>

                                <button type="submit" class="btn btn-success w-100 btn-sm fw-bold shadow-sm" id="btnPay">
                                    Bayar Sekarang <i class="bi bi-arrow-right"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    {{-- AKHIR FORM DONASI ONLINE --}}

                    {{-- Metode Donasi Manual (Collapse) --}}
                    <div class="accordion accordion-flush mb-4" id="accordionManual">
                        <div class="accordion-item border rounded overflow-hidden">
                            <h2 class="accordion-header" id="flush-headingOne">
                                <button class="accordion-button collapsed small py-2 bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
                                    <i class="bi bi-bank me-2"></i> Transfer Manual / Tunai
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionManual">
                                <div class="accordion-body small p-3">
                                    
                                    {{-- Opsi 1: Transfer Bank --}}
                                    <div class="payment-option" onclick="copyToClipboard('1234567890', 'Rekening BSI')">
                                        <img src="{{ asset('images/icons/bsi.png') }}" class="payment-icon" onerror="this.src='/images/icons/donasi.png'">
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small">Bank Syariah Indonesia (BSI)</div>
                                            <div class="small text-muted">1234 5678 90 a.n Masjid Al-Kautsar</div>
                                        </div>
                                        <i class="bi bi-files text-primary"></i>
                                    </div>

                                    {{-- Opsi 3: Konfirmasi WA --}}
                                    <a href="https://wa.me/6281234567890?text=Saya%20ingin%20donasi%20untuk%20program..." target="_blank" class="payment-option text-decoration-none text-dark mb-0">
                                        <i class="bi bi-whatsapp text-success fs-4 me-3"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small">Konfirmasi WhatsApp</div>
                                            <div class="small text-muted">Kirim bukti transfer</div>
                                        </div>
                                        <i class="bi bi-arrow-right-short"></i>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pesan Donatur --}}
                    <h6 class="fw-bold border-bottom pb-2 mb-3 mt-4">Doa & Dukungan</h6>
                    <div id="donaturList">
                        {{-- Diisi via JS --}}
                    </div>
                    
                    {{-- Tombol Load More Donatur --}}
                    <div class="text-center mt-2">
                        <button id="btnLoadMore" class="btn btn-link btn-sm text-decoration-none d-none">Lihat Lebih Banyak</button>
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

{{-- (BARU) Script Midtrans Snap --}}
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
    // --- Swiper Init ---
    document.addEventListener('DOMContentLoaded', function() {
        if (document.querySelector('.donasi-swiper')) {
            new Swiper('.donasi-swiper', { 
                slidesPerView: 1, spaceBetween: 15, loop: true, 
                navigation: { nextEl: '.donasi-button-next', prevEl: '.donasi-button-prev' },
                breakpoints: { 768: { slidesPerView: 2 }, 992: { slidesPerView: 3 } }
            }); 
        }
    });

    // --- Modal Detail Logic ---
    const modalDetail = new bootstrap.Modal(document.getElementById('modalDonasiDetail'));
    let currentDonaturPage = 1;
    let currentDonasiId = null;

    function openDonasiModal(id) {
        currentDonasiId = id;
        currentDonaturPage = 1;
        
        // (BARU) Set ID ke form payment hidden
        document.getElementById('pay_id_donasi').value = id; 
        
        // Reset Tampilan
        document.getElementById('loadingDetail').classList.remove('d-none');
        document.getElementById('contentDetail').classList.add('d-none');
        document.getElementById('donaturList').innerHTML = '';
        document.getElementById('btnLoadMore').classList.add('d-none');
        
        // Reset Form Payment
        document.getElementById('formDonasiOnline').reset();
        document.getElementById('btnPay').innerHTML = 'Bayar Sekarang <i class="bi bi-arrow-right"></i>';
        document.getElementById('btnPay').disabled = false;

        modalDetail.show();

        fetchDataDonasi(id);
    }

    function fetchDataDonasi(id) {
        fetch(`/donasi/detail/${id}?page=${currentDonaturPage}`)
            .then(res => res.json())
            .then(data => {
                // Isi Data Utama (Hanya saat page 1)
                if (currentDonaturPage === 1) {
                    document.getElementById('mFoto').src = data.foto_url;
                    document.getElementById('mNama').textContent = data.nama_donasi;
                    document.getElementById('mDeskripsi').innerHTML = data.deskripsi ? data.deskripsi.replace(/\n/g, '<br>') : 'Tidak ada deskripsi.';
                    
                    // Stats
                    document.getElementById('mTerkumpul').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.terkumpul);
                    document.getElementById('mTarget').textContent = 'Target: Rp ' + new Intl.NumberFormat('id-ID').format(data.target_dana);
                    document.getElementById('mProgress').style.width = data.persentase + '%';
                    
                    let sisaText = '';
                    if(data.sisa_hari === null) sisaText = '∞ Unlimited';
                    else if(data.sisa_hari > 0) sisaText = data.sisa_hari + ' Hari Lagi';
                    else sisaText = 'Berakhir';
                    document.getElementById('mSisaHari').textContent = sisaText;

                    document.getElementById('loadingDetail').classList.add('d-none');
                    document.getElementById('contentDetail').classList.remove('d-none');
                }

                // Render Donatur (Append)
                const listContainer = document.getElementById('donaturList');
                if (data.donatur.data.length > 0) {
                    data.donatur.data.forEach(d => {
                        const item = `
                            <div class="donatur-msg-box">
                                <span class="donatur-name">${d.nama_donatur || 'Hamba Allah'}</span>
                                <span class="donatur-text">"${d.pesan}"</span>
                            </div>
                        `;
                        listContainer.insertAdjacentHTML('beforeend', item);
                    });
                } else if (currentDonaturPage === 1) {
                    listContainer.innerHTML = '<p class="text-center text-muted small">Belum ada pesan dukungan.</p>';
                }

                // Handle Pagination Button
                const btnLoadMore = document.getElementById('btnLoadMore');
                if (data.donatur.next_page_url) {
                    btnLoadMore.classList.remove('d-none');
                    btnLoadMore.onclick = () => {
                        currentDonaturPage++;
                        fetchDataDonasi(currentDonasiId); // Panggil lagi untuk page selanjutnya
                    };
                } else {
                    btnLoadMore.classList.add('d-none');
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal memuat data donasi', 'error');
                modalDetail.hide();
            });
    }

    // (BARU) Logic Submit Form Donasi Online (Midtrans)
    document.getElementById('formDonasiOnline').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btnPay');
        const originalBtnText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        btn.disabled = true;

        // Ambil Data Form
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        try {
            // Request Token ke Backend
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
                // Munculkan Popup Midtrans
                window.snap.pay(result.snap_token, {
                    onSuccess: function(result){
                        Swal.fire('Terima Kasih!', 'Donasi Anda berhasil diterima.', 'success').then(() => {
                            modalDetail.hide();
                            location.reload(); // Reload untuk update progress bar
                        });
                    },
                    onPending: function(result){
                        Swal.fire('Menunggu Pembayaran', 'Silakan selesaikan pembayaran Anda via ' + result.payment_type, 'info');
                    },
                    onError: function(result){
                        Swal.fire('Gagal', 'Pembayaran gagal.', 'error');
                    },
                    onClose: function(){
                        btn.innerHTML = originalBtnText;
                        btn.disabled = false;
                    }
                });
            } else {
                Swal.fire('Gagal', result.message || 'Terjadi kesalahan saat memproses donasi.', 'error');
                btn.innerHTML = originalBtnText;
                btn.disabled = false;
            }

        } catch (error) {
            console.error(error);
            Swal.fire('Error', 'Terjadi kesalahan koneksi.', 'error');
            btn.innerHTML = originalBtnText;
            btn.disabled = false;
        }
    });

    // Copy to Clipboard Helper
    function copyToClipboard(text, label) {
        navigator.clipboard.writeText(text).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Disalin!',
                text: `Nomor ${label} berhasil disalin.`,
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
</script>
@endpush