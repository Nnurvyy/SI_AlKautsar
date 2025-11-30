@extends('layouts.public')

@section('title', 'Tabungan Qurban Saya')

@push('styles')
<style>
    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; margin-bottom: 0.5rem; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; margin-bottom: 1rem; }
    
    /* Card Summary Styles */
    .user-summary-card { background: linear-gradient(45deg, #198754, #20c997); border: none; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(25, 135, 84, 0.2); }
    .user-summary-card .total-label { font-size: 0.9rem; font-weight: 300; opacity: 0.9; margin-bottom: 0; }
    .user-summary-card .total-amount { font-size: 2.25rem; font-weight: 700; letter-spacing: -1px; }
    .user-summary-card .user-name { font-size: 1.2rem; font-weight: 600; }
    
    /* Card Tabungan Styles */
    .card-tabungan { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.2s; border: 1px solid #f0f0f0; }
    .card-tabungan:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    .status-badge { font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
    
    .list-hewan-item { font-size: 0.9rem; color: #555; padding: 4px 0; border-bottom: 1px dashed #eee; }
    .list-hewan-item:last-child { border-bottom: none; }
    
    .hewan-row { background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #e9ecef; }

    /* Payment Box Styles */
    .box-payment { background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 20px; }
    .input-nominal { font-size: 1.2rem; font-weight: 600; color: #333; }

    /* Hover Effect untuk Judul Tabungan */
    .hover-primary { cursor: pointer; transition: color 0.2s; }
    .hover-primary:hover { color: #198754 !important; text-decoration: underline; }

    /* --- [BARU] FIX MOBILE & TABLET RESPONSIVE --- */
    /* Menggunakan 991px agar Tablet juga terkena dampaknya */
    @media (max-width: 991px) {
        
        /* 1. Kecilkan font Dropdown Metode Pembayaran */
        #pay_method_tabungan {
            font-size: 0.85rem !important; /* Ukuran font lebih kecil */
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        /* Opsional: Mencoba mengecilkan opsi di dalamnya (Browser support bervariasi) */
        #pay_method_tabungan option, 
        #pay_method_tabungan optgroup {
            font-size: 0.85rem; 
        }

        /* 2. Style responsif lainnya yang sudah ada */
        .user-summary-card .total-amount {
            font-size: 1.75rem; 
        }
        .donasi-title-heading {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- BAGIAN 1: JIKA BELUM LOGIN --}}
@if(!Auth::guard('jamaah')->check())
    
    <div style="filter: blur(5px); pointer-events: none; user-select: none; min-height: 60vh;">
        <div class="container pt-5">
            <h2 class="donasi-title-heading">Tabungan Qurban Saya</h2>
            <p class="donasi-title-sub">Fitur khusus untuk Jamaah terdaftar.</p>
        </div>
    </div>

    {{-- Modal Login Required --}}
    <div class="modal fade show" id="modalLoginRequired" tabindex="-1" aria-modal="true" role="dialog" style="display: block; background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center p-4 border-0 shadow rounded-4">
                <div class="mb-3">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-lock-fill fs-1 text-success"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-2">Akses Terbatas</h4>
                <p class="text-muted mb-4">Fitur Tabungan Qurban hanya dapat diakses oleh Jamaah yang sudah masuk (Login).</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('login') }}" class="btn btn-success btn-lg fw-semibold">Login Sekarang</a>
                    <a href="{{ route('public.landing') }}" class="btn btn-outline-secondary btn-lg fw-semibold">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- BAGIAN 2: JIKA SUDAH LOGIN --}}

    {{-- [DIUBAH] Class container disesuaikan: pt-4 untuk mobile (naik), py-lg-5 untuk desktop (tetap) --}}
    <div class="container pt-4 pb-5 py-lg-5"> 
        {{-- Header & Summary --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="donasi-title-heading mb-1">Tabungan Qurban Saya</h2> 
                <p class="text-muted">Pantau ibadah qurban Anda dengan mudah.</p>
            </div>
            
            <div class="col-md-4 text-md-end d-none d-md-block">
                <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBukaTabungan">
                    <i class="bi bi-plus-lg me-2"></i> Buka Tabungan Baru
                </button>
            </div>
        </div>

        {{-- Kartu Total Aset --}}
        <div class="card user-summary-card p-4 mb-5"> 
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="d-block text-white-50 mb-1">Assalamu'alaikum,</span>
                    <span class="user-name">{{ $user->name }}</span>
                </div>
                <div class="text-end">
                    <span class="total-label d-block">Total Aset Qurban</span>
                    {{-- Font size angka ini otomatis mengecil di mobile berkat CSS di atas --}}
                    <span class="total-amount">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- List Tabungan --}}
        @if($tabungans->isEmpty())
            <div class="text-center py-5">
                <div class="mb-3 text-muted opacity-50">
                    <i class="bi bi-wallet2" style="font-size: 4rem;"></i>
                </div>
                <h5 class="fw-bold text-secondary">Belum Ada Tabungan</h5>
                <p class="text-muted">Anda belum memiliki tabungan qurban aktif.</p>
                <button class="btn btn-outline-success rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#modalBukaTabungan">
                    Mulai Menabung Sekarang
                </button>
            </div>
        @else
            <div class="row g-4 mb-4">
                @foreach($tabungans as $t)
                    @php
                        // Filter hanya transaksi sukses agar tampilan card akurat
                        $terkumpul = $t->pemasukanTabunganQurban->where('status', 'success')->sum('nominal');
                        $target = $t->total_harga_hewan_qurban;
                        $persen = ($target > 0) ? min(100, round(($terkumpul / $target) * 100)) : 0;
                        $sisa = max(0, $target - $terkumpul);

                        // --- LOGIKA PEMBULATAN CICILAN ---
                        $cicilanPerBulan = 0;
                        if($t->saving_type == 'cicilan' && $t->duration_months > 0) {
                            // Rumus: ceil(Harga / Bulan / 100) * 100
                            $rawCicilan = $target / $t->duration_months;
                            $cicilanPerBulan = ceil($rawCicilan / 100) * 100;
                        }
                    @endphp

                    <div class="col-lg-6">
                        <div class="card card-tabungan h-100">
                            <div class="card-body p-4">
                                {{-- Header Card (JUDUL BISA DIKLIK) --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <a href="javascript:void(0)" onclick="openDetailModal('{{ $t->id_tabungan_hewan_qurban }}')" class="text-decoration-none text-dark">
                                            <h6 class="fw-bold mb-1 hover-primary">
                                                Tabungan #{{ substr($t->id_tabungan_hewan_qurban, 0, 8) }}
                                                <i class="bi bi-info-circle-fill text-muted ms-1" style="font-size: 0.8rem;"></i>
                                            </h6>
                                        </a>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($t->created_at)->format('d M Y') }}</small>
                                    </div>
                                    @if($t->status == 'menunggu')
                                        <span class="status-badge bg-warning text-dark">Menunggu</span>
                                    @elseif($t->status == 'disetujui')
                                        <span class="status-badge bg-success text-white">Aktif</span>
                                    @elseif($t->status == 'ditolak')
                                        <span class="status-badge bg-danger text-white">Ditolak</span>
                                    @else
                                        <span class="status-badge bg-secondary text-white">{{ ucfirst($t->status) }}</span>
                                    @endif
                                </div>

                                {{-- Alert Info Jika Menunggu --}}
                                @if($t->status == 'menunggu')
                                    <div class="alert alert-warning py-2 px-3 small mb-3 border-0 bg-warning bg-opacity-10 text-warning-emphasis">
                                        <i class="bi bi-hourglass-split me-1"></i> Menunggu persetujuan pengurus.
                                    </div>
                                @endif

                                {{-- Rincian Hewan --}}
                                <div class="bg-light p-3 rounded mb-3">
                                    <h6 class="small fw-bold text-muted text-uppercase mb-2" style="letter-spacing: 1px;">Rencana Qurban</h6>
                                    @foreach($t->details as $detail)
                                        <div class="list-hewan-item d-flex justify-content-between">
                                            <span>
                                                <strong>{{ $detail->jumlah_hewan }}</strong> ekor {{ ucfirst($detail->hewan->nama_hewan) }}
                                                <small class="text-muted">({{ ucfirst($detail->hewan->kategori_hewan) }})</small>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Progress Bar Keuangan --}}
                                <div class="mb-2 d-flex justify-content-between small fw-bold">
                                    <span class="text-success">Terkumpul: Rp {{ number_format($terkumpul, 0, ',', '.') }}</span>
                                    <span class="text-muted">Target: Rp {{ number_format($target, 0, ',', '.') }}</span>
                                </div>
                                <div class="progress mb-4" style="height: 10px; border-radius: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persen }}%"></div>
                                </div>

                                {{-- TOMBOL AKSI: SETOR & DETAIL --}}
                                <div class="row g-2">
                                    <div class="col-8">
                                        {{-- HANYA BISA SETOR JIKA STATUS DISETUJUI --}}
                                        @if($t->status == 'disetujui' && $sisa > 0)
                                            <button class="btn btn-success w-100 fw-bold" 
                                                {{-- Pass Cicilan Per Bulan & Tipe ke Fungsi JS --}}
                                                onclick="openPaymentModal('{{ $t->id_tabungan_hewan_qurban }}', {{ $sisa }}, {{ $cicilanPerBulan }}, '{{ $t->saving_type }}')">
                                                <i class="bi bi-qr-code-scan me-1"></i> Setor Tabungan
                                            </button>
                                        @elseif($sisa <= 0)
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="bi bi-check-circle-fill me-1"></i> Lunas
                                            </button>
                                        @else
                                            <button class="btn btn-secondary w-100" disabled>
                                                <i class="bi bi-lock-fill me-1"></i> Belum Aktif
                                            </button>
                                        @endif
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-outline-secondary w-100" onclick="openDetailModal('{{ $t->id_tabungan_hewan_qurban }}')">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOMBOL MOBILE: TAMBAH TABUNGAN --}}
            <div class="d-grid gap-2 mb-5 d-md-none">
                <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBukaTabungan">
                    <i class="bi bi-plus-lg me-2"></i> Buka Tabungan Baru
                </button>
            </div>
        @endif
        
        {{-- TOMBOL BANTUAN WA --}}
            <div class="text-center mt-4 mb-5 pb-5"> 
                <div class="card border-0 bg-light p-4 rounded-4 d-inline-block shadow-sm" style="max-width: 500px;">
                    <h6 class="fw-bold mb-2">Butuh Bantuan?</h6>
                    <p class="text-muted small mb-3">Hubungi admin untuk pertanyaan seputar tabungan.</p>
                    <a href="https://wa.me/{{ $masjidSettings->social_whatsapp }}" target="_blank" class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="bi bi-whatsapp me-2"></i> Chat Admin
                    </a>
                </div>
            </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 1: BUKA TABUNGAN BARU --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalBukaTabungan" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Buka Tabungan Qurban</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formCreateTabungan">
                        <p class="text-muted small mb-3">Pilih hewan qurban yang ingin Anda tabung.</p>
                        <div id="hewanContainer">
                            <div class="hewan-row row g-2 align-items-center">
                                <div class="col-8">
                                    <label class="form-label small fw-bold mb-1">Jenis Hewan</label>
                                    <select class="form-select select-hewan" required onchange="calculateGrandTotal()">
                                        <option value="" data-harga="0">-- Pilih Hewan --</option>
                                        @foreach($masterHewan as $h)
                                            <option value="{{ $h->id_hewan_qurban }}" data-harga="{{ $h->harga_hewan }}">
                                                {{ ucfirst($h->nama_hewan) }} {{ ucfirst($h->kategori_hewan) }} - Rp {{ number_format($h->harga_hewan, 0, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <label class="form-label small fw-bold mb-1">Jumlah</label>
                                    <input type="number" class="form-control input-qty" value="1" min="1" required oninput="calculateGrandTotal()">
                                </div>
                                <div class="col-1 d-flex align-items-end">
                                    <button type="button" class="btn btn-danger btn-sm w-100 mt-4 btn-remove" disabled><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addHewanRow()">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Hewan Lain
                        </button>

                        <div class="mt-4 p-3 bg-light rounded text-end border">
                            <small class="text-muted d-block">Estimasi Total Target Tabungan</small>
                            <h3 class="fw-bold text-success mb-0" id="displayGrandTotal">Rp 0</h3>
                        </div>
                        <hr class="my-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Metode Menabung</label>
                                <select name="saving_type" id="savingType" class="form-select">
                                    <option value="cicilan">Cicilan Rutin (Bulanan)</option>
                                    <option value="bebas">Tabungan Bebas (Fleksibel)</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="divDuration">
                                <label class="form-label fw-bold">Rencana Durasi (Bulan)</label>
                                <input type="number" name="duration_months" class="form-control" value="12" min="1">
                                <div class="form-text small">Est. cicilan: <span id="estCicilan" class="fw-bold text-dark">-</span> /bulan (Dibulatkan)</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success px-4" onclick="submitTabungan()">Simpan Tabungan</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 2: BAYAR / SETOR TABUNGAN --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalBayarTabungan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i>Setor Tabungan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="box-payment mb-4">
                        <div class="d-flex align-items-center mb-3">
                            @if(Auth::guard('jamaah')->user()->avatar)
                                <img src="{{ asset('storage/' . Auth::guard('jamaah')->user()->avatar) }}" class="rounded-circle me-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center me-3 fs-4 fw-bold border" style="width: 50px; height: 50px;">
                                    {{ substr(Auth::guard('jamaah')->user()->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <small class="text-muted d-block">Penyetor</small>
                                <span class="fw-bold text-dark">{{ Auth::guard('jamaah')->user()->name }}</span>
                            </div>
                        </div>

                        <form id="formSetorTabungan">
                            <input type="hidden" id="pay_id_tabungan" name="id_tabungan_hewan_qurban">
                            
                            {{-- Input Nominal --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Nominal Setoran</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 fw-bold text-success">Rp</span>
                                    <input type="text" inputmode="numeric" class="form-control border-start-0 input-nominal" id="pay_nominal_tabungan" name="nominal" placeholder="Contoh: 100.000" required>
                                </div>
                                <small class="text-muted" id="infoSisaTarget">Sisa target: -</small>
                                <small class="text-danger d-none" id="errorNominalTabungan">Minimal Rp 10.000</small>
                            </div>

                            {{-- Input Metode --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-dark">Metode Pembayaran</label>
                                <select class="form-select" name="method" id="pay_method_tabungan" required>
                                    <option value="" selected disabled>-- Pilih Metode --</option>
                                    <optgroup label="E-Wallet (QRIS)">
                                        <option value="QRIS">QRIS (OVO, Dana, ShopeePay)</option>
                                    </optgroup>
                                    <optgroup label="Virtual Account">
                                        <option value="BRIVA">BRI Virtual Account</option>
                                        <option value="MANDIRIVA">Mandiri Virtual Account</option>
                                        <option value="BNIVA">BNI Virtual Account</option>
                                        <option value="BCAVA">BCA Virtual Account</option>
                                    </optgroup>
                                    <optgroup label="Minimarket">
                                        <option value="ALFAMART">Alfamart</option>
                                        <option value="INDOMARET">Indomaret</option>
                                    </optgroup>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm" id="btnSubmitSetoran">
                                Bayar Sekarang <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- MODAL 3: DETAIL TABUNGAN (DASHBOARD) --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalDetailTabungan" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg"> 
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white">
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="detailTitle">Detail Tabungan</h5>
                        <small class="opacity-75" id="detailId">ID: -</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div id="loadingDetail" class="text-center py-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="text-muted mt-2 small">Memuat data...</p>
                    </div>
                    <div id="contentDetail" style="display: none;">
                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Informasi Paket</h6>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="text-muted small">Tipe Tabungan</span>
                                            <span class="fw-bold text-success text-capitalize" id="dTipe">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="text-muted small">Target Waktu</span>
                                            <span class="fw-bold text-dark" id="dDurasi">- Bulan</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 pb-2">
                                            <span class="text-muted small">Est. Cicilan/Bulan</span>
                                            <span class="fw-bold text-dark" id="dCicilan">-</span>
                                        </div>
                                        <div class="alert alert-info py-2 px-3 small mb-0 mt-2">
                                            <i class="bi bi-calendar-check me-1"></i> Mulai: <span id="dTglMulai">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="card border-0 shadow-sm h-100 bg-white">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Ringkasan Dana</h6>
                                        <div class="row g-2 text-center">
                                            <div class="col-4">
                                                <div class="p-2 rounded bg-light border">
                                                    <small class="d-block text-muted" style="font-size: 0.7rem;">Target</small>
                                                    <span class="d-block fw-bold text-dark small" id="dTarget">Rp 0</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 rounded bg-success bg-opacity-10 border border-success">
                                                    <small class="d-block text-success" style="font-size: 0.7rem;">Terkumpul</small>
                                                    <span class="d-block fw-bold text-success small" id="dTerkumpul">Rp 0</span>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="p-2 rounded bg-danger bg-opacity-10 border border-danger">
                                                    <small class="d-block text-danger" style="font-size: 0.7rem;">Kurang</small>
                                                    <span class="d-block fw-bold text-danger small" id="dSisa">Rp 0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between small mb-1">
                                                <span class="text-muted">Progres</span>
                                                <span class="fw-bold text-success" id="dPersen">0%</span>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div id="dProgressBar" class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="fw-bold small text-muted border-bottom pb-2">Rincian Hewan</h6>
                            <div id="dListHewan"></div>
                        </div>
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold small text-muted mb-0">Riwayat Transaksi</h6>
                                <span class="badge bg-light text-secondary border" id="dTotalTrans">0 Transaksi</span>
                            </div>
                            <div class="table-responsive border rounded" style="max-height: 250px;">
                                <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Ref/Metode</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-end">Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // --- UTILS: FORMAT RUPIAH ---
    function formatRupiahInput(e) {
        let rawValue = e.target.value.replace(/[^0-9]/g, '');
        if (rawValue) {
            e.target.value = new Intl.NumberFormat('id-ID').format(rawValue);
        } else {
            e.target.value = '';
        }
    }

    // --- 1. LOGIK FORM MULTI HEWAN (Buka Tabungan) ---
    const hewanContainer = document.getElementById('hewanContainer');
    
    function addHewanRow() {
        const firstRow = document.querySelector('.hewan-row');
        const clone = firstRow.cloneNode(true);
        clone.querySelector('select').value = "";
        clone.querySelector('input').value = 1;
        
        const btnRemove = clone.querySelector('.btn-remove');
        btnRemove.disabled = false;
        btnRemove.onclick = function() {
            this.closest('.hewan-row').remove();
            calculateGrandTotal();
        };

        clone.querySelector('select').onchange = calculateGrandTotal;
        clone.querySelector('input').oninput = calculateGrandTotal;
        hewanContainer.appendChild(clone);
    }

    // [UPDATED] Calculate Grand Total with Rounding Up (100)
    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.hewan-row').forEach(row => {
            const select = row.querySelector('.select-hewan');
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            const option = select.options[select.selectedIndex];
            const harga = parseFloat(option ? option.getAttribute('data-harga') : 0);
            total += (harga * qty);
        });

        document.getElementById('displayGrandTotal').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(total);
        
        const durasi = parseFloat(document.querySelector('input[name="duration_months"]').value) || 1;
        
        // Logika Pembulatan: Harga / Durasi / 100 -> Ceil -> * 100
        const rawEst = (durasi > 0) ? (total / durasi) : 0;
        const est = Math.ceil(rawEst / 100) * 100;

        document.getElementById('estCicilan').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(est);
    }

    const savingTypeEl = document.getElementById('savingType');
    const divDurasiEl = document.getElementById('divDuration');
    if(savingTypeEl) {
        savingTypeEl.addEventListener('change', function() {
            divDurasiEl.style.display = (this.value === 'bebas') ? 'none' : 'block';
        });
    }

    const durationInput = document.querySelector('input[name="duration_months"]');
    if(durationInput) {
        durationInput.addEventListener('input', calculateGrandTotal);
    }

    function submitTabungan() {
        let items = [];
        let valid = true;

        document.querySelectorAll('.hewan-row').forEach(row => {
            const id = row.querySelector('.select-hewan').value;
            const qty = row.querySelector('.input-qty').value;
            if(!id || qty < 1) valid = false;
            if(id) items.push({ id_hewan: id, qty: qty });
        });

        if(!valid || items.length === 0) {
            Swal.fire('Error', 'Mohon lengkapi data hewan dan jumlahnya.', 'error');
            return;
        }

        const type = document.getElementById('savingType').value;
        const duration = document.querySelector('input[name="duration_months"]').value;

        Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });

        fetch("{{ route('jamaah.qurban.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ saving_type: type, duration_months: duration, items: items })
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) throw new Error(data.message || 'Terjadi kesalahan');
            return data;
        })
        .then(data => {
            Swal.fire({ icon: 'success', title: 'Berhasil!', text: data.message, confirmButtonColor: '#198754' })
            .then(() => location.reload());
        })
        .catch(err => Swal.fire('Gagal', err.message, 'error'));
    }


    // --- 2. LOGIK PEMBAYARAN TRIPAY (SETORAN) ---
    
    const modalBayarEl = document.getElementById('modalBayarTabungan');
    const modalBayar = modalBayarEl ? new bootstrap.Modal(modalBayarEl) : null;
    const inputNominalTabungan = document.getElementById('pay_nominal_tabungan');

    if(inputNominalTabungan) {
        inputNominalTabungan.addEventListener('input', formatRupiahInput);
    }

    // [UPDATED] Menerima Parameter Cicilan & Tipe untuk Auto-Fill
    function openPaymentModal(id, sisaTarget, cicilanPerBulan, tipeTabungan) {
        if(!modalBayar) return;

        // Reset Form
        document.getElementById('formSetorTabungan').reset();
        document.getElementById('pay_id_tabungan').value = id;
        document.getElementById('errorNominalTabungan').classList.add('d-none');
        
        // Auto Fill jika Cicilan
        if(tipeTabungan === 'cicilan' && cicilanPerBulan > 0) {
            // Jika cicilan lebih besar dari sisa, pakai sisa saja (pelunasan akhir)
            const nominalAuto = (cicilanPerBulan > sisaTarget) ? sisaTarget : cicilanPerBulan;
            inputNominalTabungan.value = new Intl.NumberFormat('id-ID').format(nominalAuto);
        } else {
            inputNominalTabungan.value = ''; // Kosongkan jika bebas
        }
        
        // Tampilkan Info Sisa Target
        const formattedSisa = new Intl.NumberFormat('id-ID').format(sisaTarget);
        document.getElementById('infoSisaTarget').innerText = `Sisa target pelunasan: Rp ${formattedSisa}`;
        
        modalBayar.show();
    }

    document.getElementById('formSetorTabungan')?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const nominalStr = inputNominalTabungan.value.replace(/\./g, '');
        const nominal = parseInt(nominalStr);

        if (!nominal || nominal < 10000) {
            document.getElementById('errorNominalTabungan').classList.remove('d-none');
            return;
        }
        document.getElementById('errorNominalTabungan').classList.add('d-none');

        const btn = document.getElementById('btnSubmitSetoran');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Memproses...';
        btn.disabled = true;

        const formData = {
            id_tabungan_hewan_qurban: document.getElementById('pay_id_tabungan').value,
            nominal: nominal,
            method: document.getElementById('pay_method_tabungan').value
        };

        try {
            const response = await fetch("{{ route('jamaah.tabungan.checkout') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();
            
            if (result.status === 'success') {
                 window.location.href = result.checkout_url;
            } else {
                throw new Error(result.message || 'Gagal memproses pembayaran');
            }
        } catch (error) {
            Swal.fire('Gagal', error.message, 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });


    // --- 3. LOGIK DETAIL TABUNGAN (MODAL BARU - DASHBOARD) ---
    
    const modalDetailEl = document.getElementById('modalDetailTabungan');
    const modalDetail = modalDetailEl ? new bootstrap.Modal(modalDetailEl) : null;

    function openDetailModal(id) {
        if(!modalDetail) return;
        
        document.getElementById('loadingDetail').style.display = 'block';
        document.getElementById('contentDetail').style.display = 'none';
        modalDetail.show();

        fetch(`/qurban-saya/${id}`)
        .then(res => {
            if(!res.ok) throw new Error('Gagal mengambil data');
            return res.json();
        })
        .then(data => {
            document.getElementById('detailTitle').innerText = 'Tabungan Qurban'; 
            document.getElementById('detailId').innerText = 'ID: #' + data.id_tabungan_hewan_qurban.substring(0, 8);
            
            const tglMulai = new Date(data.created_at).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
            document.getElementById('dTglMulai').innerText = tglMulai;

            const target = parseInt(data.total_harga_hewan_qurban);
            
            let terkumpul = 0;
            if(data.pemasukan_tabungan_qurban) {
                terkumpul = data.pemasukan_tabungan_qurban.reduce((acc, curr) => {
                    return (curr.status === 'success') ? acc + parseInt(curr.nominal) : acc;
                }, 0);
            }

            const sisa = Math.max(0, target - terkumpul);
            const persen = target > 0 ? Math.min(100, Math.round((terkumpul / target) * 100)) : 0;

            document.getElementById('dTarget').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(target);
            document.getElementById('dTerkumpul').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(terkumpul);
            document.getElementById('dSisa').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(sisa);
            
            document.getElementById('dPersen').innerText = persen + "%";
            document.getElementById('dProgressBar').style.width = persen + "%";

            document.getElementById('dTipe').innerText = data.saving_type;
            
            if (data.saving_type === 'cicilan') {
                const durasi = data.duration_months || 12;
                // [UPDATED] Logika Pembulatan di Modal Detail
                const rawCicilan = target / durasi;
                const estCicilan = Math.ceil(rawCicilan / 100) * 100;
                
                document.getElementById('dDurasi').innerText = durasi + " Bulan";
                document.getElementById('dCicilan').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(estCicilan) + " /bln";
            } else {
                document.getElementById('dDurasi').innerText = "Fleksibel";
                document.getElementById('dCicilan').innerText = "Bebas";
            }

            const listHewanEl = document.getElementById('dListHewan');
            let hewanHtml = '';
            if(data.details && data.details.length > 0) {
                data.details.forEach(d => {
                    const nama = d.hewan ? d.hewan.nama_hewan : '-';
                    const kat = d.hewan ? d.hewan.kategori_hewan : '';
                    hewanHtml += `
                        <div class="d-flex justify-content-between align-items-center bg-white p-2 border rounded mb-1">
                            <span class="small fw-bold text-dark"><i class="bi bi-tag-fill me-2 text-success"></i> ${d.jumlah_hewan} ekor ${nama}</span>
                            <span class="badge bg-light text-dark border">${kat}</span>
                        </div>
                    `;
                });
            } else {
                hewanHtml = '<p class="text-muted small">Data hewan tidak ditemukan.</p>';
            }
            listHewanEl.innerHTML = hewanHtml;

            const tbody = document.getElementById('dTableBody');
            tbody.innerHTML = '';
            const txList = data.pemasukan_tabungan_qurban || [];
            
            document.getElementById('dTotalTrans').innerText = txList.length + ' Transaksi';

            if(txList.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-3 text-muted small">Belum ada riwayat transaksi.</td></tr>`;
            } else {
                txList.sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal));

                txList.forEach(p => {
                    const tgl = new Date(p.tanggal).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: '2-digit'});
                    
                    let statusBadge = '';
                    let statusClass = '';
                    if(p.status === 'success') { statusBadge = 'Berhasil'; statusClass = 'bg-success'; }
                    else if(p.status === 'pending') { statusBadge = 'Pending'; statusClass = 'bg-warning text-dark'; }
                    else { statusBadge = 'Gagal'; statusClass = 'bg-danger'; }

                    let metode = p.metode_pembayaran ? p.metode_pembayaran.toUpperCase() : 'MANUAL';
                    if(metode === 'TRIPAY') metode = 'QRIS/VA';

                    tbody.innerHTML += `
                        <tr>
                            <td>${tgl}</td>
                            <td>
                                <div class="d-flex flex-column" style="line-height: 1.1;">
                                    <span class="fw-bold small">${metode}</span>
                                    <span class="text-muted" style="font-size: 0.65rem;">${p.order_id ? p.order_id.substring(0,10)+'...' : '-'}</span>
                                </div>
                            </td>
                            <td class="text-center"><span class="badge ${statusClass}" style="font-size: 0.65rem;">${statusBadge}</span></td>
                            <td class="text-end fw-bold text-dark small">+ Rp ${new Intl.NumberFormat('id-ID').format(p.nominal)}</td>
                        </tr>
                    `;
                });
            }

            document.getElementById('loadingDetail').style.display = 'none';
            document.getElementById('contentDetail').style.display = 'block';
        })
        .catch(err => {
            document.getElementById('loadingDetail').style.display = 'none';
            Swal.fire('Gagal', err.message, 'error');
            modalDetail.hide();
        });
    }

</script>
@endpush