@extends('layouts.public')

@section('title', 'Tabungan Qurban Saya')

@push('styles')
<style>
    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; margin-bottom: 0.5rem; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; margin-bottom: 1rem; }
    .user-summary-card { background: linear-gradient(45deg, #198754, #20c997); border: none; border-radius: 12px; color: white; box-shadow: 0 4px 15px rgba(25, 135, 84, 0.2); }
    .user-summary-card .total-label { font-size: 0.9rem; font-weight: 300; opacity: 0.9; margin-bottom: 0; }
    .user-summary-card .total-amount { font-size: 2.25rem; font-weight: 700; letter-spacing: -1px; }
    .user-summary-card .user-name { font-size: 1.2rem; font-weight: 600; }
    
    .card-tabungan { border: none; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); transition: transform 0.2s; border: 1px solid #f0f0f0; }
    .card-tabungan:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    
    .status-badge { font-size: 0.75rem; padding: 0.4em 0.8em; border-radius: 20px; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase; }
    
    /* Styling List Hewan di Card */
    .list-hewan-item { font-size: 0.9rem; color: #555; padding: 4px 0; border-bottom: 1px dashed #eee; }
    .list-hewan-item:last-child { border-bottom: none; }
    
    /* Modal Styling */
    .hewan-row { background: #f8f9fa; padding: 10px; border-radius: 8px; margin-bottom: 10px; border: 1px solid #e9ecef; }
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

    <div class="container py-5"> 
        {{-- Header & Summary --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-8">
                <h2 class="donasi-title-heading mb-1">Tabungan Qurban Saya</h2> 
                <p class="text-muted">Pantau ibadah qurban Anda dengan mudah.</p>
            </div>
            
            {{-- TOMBOL DESKTOP (Hanya muncul di layar MD ke atas) --}}
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
                    <div class="col-lg-6">
                        <div class="card card-tabungan h-100">
                            <div class="card-body p-4">
                                {{-- Header Card --}}
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">Tabungan #{{ substr($t->id_tabungan_hewan_qurban, 0, 8) }}</h6>
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
                                @php
                                    $terkumpul = $t->pemasukanTabunganQurban->sum('nominal');
                                    $target = $t->total_harga_hewan_qurban;
                                    $persen = ($target > 0) ? min(100, round(($terkumpul / $target) * 100)) : 0;
                                @endphp

                                <div class="mb-2 d-flex justify-content-between small fw-bold">
                                    <span class="text-success">Terkumpul: Rp {{ number_format($terkumpul, 0, ',', '.') }}</span>
                                    <span class="text-muted">Target: Rp {{ number_format($target, 0, ',', '.') }}</span>
                                </div>
                                <div class="progress mb-4" style="height: 10px; border-radius: 5px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persen }}%"></div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary" onclick="showHistory('{{ $t->id_tabungan_hewan_qurban }}')">
                                        <i class="bi bi-clock-history me-2"></i> Lihat Riwayat Setoran
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- TOMBOL MOBILE: TAMBAH TABUNGAN (Hanya muncul di Mobile) --}}
            <div class="d-grid gap-2 mb-5 d-md-none">
                <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBukaTabungan">
                    <i class="bi bi-plus-lg me-2"></i> Buka Tabungan Baru
                </button>
            </div>
        @endif
        

        {{-- [INI YANG HILANG TADI] - TOMBOL BANTUAN WA --}}
        @if(isset($masjidSettings) && !empty($masjidSettings->social_whatsapp))
            <div class="text-center mt-4 mb-5 pb-5"> 
                <div class="card border-0 bg-light p-4 rounded-4 d-inline-block shadow-sm" style="max-width: 500px;">
                    <h6 class="fw-bold mb-2">Butuh Bantuan atau Ingin Konfirmasi Setoran?</h6>
                    <p class="text-muted small mb-3">Hubungi admin kami untuk pertanyaan seputar tabungan qurban.</p>
                    <a href="https://wa.me/{{ $masjidSettings->social_whatsapp }}?text=Assalamu'alaikum Admin, saya jamaah atas nama {{ $user->name }} ingin bertanya seputar Tabungan Qurban." 
                       target="_blank" 
                       class="btn btn-success rounded-pill px-4 fw-bold">
                        <i class="bi bi-whatsapp me-2"></i> Hubungi Admin via WhatsApp
                    </a>
                </div>
            </div>
        @endif

    </div>

    {{-- ========================================== --}}
    {{-- MODAL 1: BUKA TABUNGAN BARU (MULTI ITEM) --}}
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
                        
                        {{-- Container Dinamis Hewan --}}
                        <div id="hewanContainer">
                            {{-- Row Default --}}
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

                        {{-- Display Total --}}
                        <div class="mt-4 p-3 bg-light rounded text-end border">
                            <small class="text-muted d-block">Estimasi Total Target Tabungan</small>
                            <h3 class="fw-bold text-success mb-0" id="displayGrandTotal">Rp 0</h3>
                        </div>

                        <hr class="my-4">

                        {{-- Opsi Tabungan --}}
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
                                <input type="number" name="duration_months" class="form-control" value="12" min="1" placeholder="Contoh: 12">
                                <div class="form-text small">Est. cicilan: <span id="estCicilan" class="fw-bold text-dark">-</span> /bulan</div>
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
    {{-- MODAL 2: RIWAYAT SETORAN --}}
    {{-- ========================================== --}}
    <div class="modal fade" id="modalRiwayat" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Riwayat Setoran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="loadingRiwayat" class="text-center py-5">
                        <div class="spinner-border text-success" role="status"></div>
                        <p class="text-muted mt-2 small">Memuat data...</p>
                    </div>
                    
                    <ul class="list-group list-group-flush" id="listRiwayatContent">
                        {{-- Data injected via JS --}}
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
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
    const hewanContainer = document.getElementById('hewanContainer');
    
    // --- 1. LOGIK FORM MULTI HEWAN ---
    
    function addHewanRow() {
        // Clone row pertama
        const firstRow = document.querySelector('.hewan-row');
        const clone = firstRow.cloneNode(true);
        
        // Reset Value
        clone.querySelector('select').value = "";
        clone.querySelector('input').value = 1;
        
        // Enable tombol hapus
        const btnRemove = clone.querySelector('.btn-remove');
        btnRemove.disabled = false;
        btnRemove.onclick = function() {
            this.closest('.hewan-row').remove();
            calculateGrandTotal();
        };

        // Attach Event Listeners baru
        clone.querySelector('select').onchange = calculateGrandTotal;
        clone.querySelector('input').oninput = calculateGrandTotal;

        hewanContainer.appendChild(clone);
    }

    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.hewan-row').forEach(row => {
            const select = row.querySelector('.select-hewan');
            const qty = parseFloat(row.querySelector('.input-qty').value) || 0;
            
            // Ambil harga dari attribut data-harga
            const option = select.options[select.selectedIndex];
            const harga = parseFloat(option ? option.getAttribute('data-harga') : 0);
            
            total += (harga * qty);
        });

        document.getElementById('displayGrandTotal').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(total);
        
        // Hitung estimasi cicilan
        const durasi = parseFloat(document.querySelector('input[name="duration_months"]').value) || 1;
        const est = (durasi > 0) ? Math.round(total / durasi) : 0;
        document.getElementById('estCicilan').innerText = "Rp " + new Intl.NumberFormat('id-ID').format(est);
        
        return total;
    }

    // Toggle Input Durasi
    const savingTypeEl = document.getElementById('savingType');
    const divDurasiEl = document.getElementById('divDuration');
    
    if(savingTypeEl) {
        savingTypeEl.addEventListener('change', function() {
            if(this.value === 'bebas') {
                divDurasiEl.style.display = 'none';
            } else {
                divDurasiEl.style.display = 'block';
            }
        });
    }

    // Listener input durasi untuk update estimasi
    const durationInput = document.querySelector('input[name="duration_months"]');
    if(durationInput) {
        durationInput.addEventListener('input', calculateGrandTotal);
    }


    // --- 2. LOGIK SUBMIT TABUNGAN ---

    function submitTabungan() {
        // Collect Data
        let items = [];
        let valid = true;

        document.querySelectorAll('.hewan-row').forEach(row => {
            const id = row.querySelector('.select-hewan').value;
            const qty = row.querySelector('.input-qty').value;
            
            if(!id || qty < 1) valid = false;
            
            if(id) {
                items.push({ id_hewan: id, qty: qty });
            }
        });

        if(!valid || items.length === 0) {
            Swal.fire('Error', 'Mohon lengkapi data hewan dan jumlahnya.', 'error');
            return;
        }

        const type = document.getElementById('savingType').value;
        const duration = document.querySelector('input[name="duration_months"]').value;

        // Tampilkan Loading
        Swal.fire({ title: 'Memproses...', didOpen: () => Swal.showLoading() });

        fetch("{{ route('jamaah.qurban.store') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                saving_type: type,
                duration_months: duration,
                items: items
            })
        })
        .then(async res => {
            const data = await res.json();
            if(!res.ok) throw new Error(data.message || 'Terjadi kesalahan');
            return data;
        })
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                confirmButtonColor: '#198754'
            }).then(() => {
                location.reload();
            });
        })
        .catch(err => {
            Swal.fire('Gagal', err.message, 'error');
        });
    }


    // --- 3. LOGIK SHOW HISTORY ---
    
    const modalRiwayatEl = document.getElementById('modalRiwayat');
    const modalRiwayat = modalRiwayatEl ? new bootstrap.Modal(modalRiwayatEl) : null;

    function showHistory(id) {
        if(!modalRiwayat) return;
        
        document.getElementById('loadingRiwayat').style.display = 'block';
        document.getElementById('listRiwayatContent').innerHTML = '';
        modalRiwayat.show();

        fetch(`/qurban-saya/${id}`)
        .then(res => {
            if(!res.ok) throw new Error('Gagal mengambil data');
            return res.json();
        })
        .then(data => {
            document.getElementById('loadingRiwayat').style.display = 'none';
            const listEl = document.getElementById('listRiwayatContent');
            
            if(!data.pemasukan_tabungan_qurban || data.pemasukan_tabungan_qurban.length === 0) {
                listEl.innerHTML = `
                    <li class="list-group-item text-center py-4">
                        <i class="bi bi-inbox text-muted fs-1 d-block mb-2"></i>
                        <span class="text-muted">Belum ada riwayat setoran.</span>
                    </li>`;
                return;
            }

            let html = '';
            data.pemasukan_tabungan_qurban.forEach(p => {
                const tgl = new Date(p.tanggal).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'});
                html += `
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <span class="d-block fw-bold text-dark">${tgl}</span>
                            <small class="text-muted">Setoran Tabungan</small>
                        </div>
                        <span class="fw-bold text-success fs-5">+ Rp ${new Intl.NumberFormat('id-ID').format(p.nominal)}</span>
                    </li>
                `;
            });
            listEl.innerHTML = html;
        })
        .catch(err => {
            document.getElementById('loadingRiwayat').style.display = 'none';
            document.getElementById('listRiwayatContent').innerHTML = `<li class="list-group-item text-center text-danger py-3">${err.message}</li>`;
        });
    }

</script>
@endpush