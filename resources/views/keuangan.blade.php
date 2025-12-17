@extends('layouts.app')

@section('title', 'Keuangan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">
    
    {{-- ================= 1. INFO CARDS (STATISTIK) ================= --}}
    <div class="row g-4 mb-4">
        {{-- Total Pemasukan --}}
        <div class="col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">Total Pemasukan</p>
                        {{-- ID untuk update via JS --}}
                        <h5 class="fw-bold mb-0 text-success" id="textTotalPemasukan">Rp 0</h5>
                    </div>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center ms-3" style="width: 48px; height: 48px; min-width: 48px;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">Total Pengeluaran</p>
                        <h5 class="fw-bold mb-0 text-danger" id="textTotalPengeluaran">Rp 0</h5>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center ms-3" style="width: 48px; height: 48px; min-width: 48px;">
                        <i class="bi bi-graph-down-arrow fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Saldo Saat Ini (Mengikuti Filter) --}}
        <div class="col-md-4">
            <div class="card stat-card h-100 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1 small fw-bold">Saldo</p>
                        <h5 class="fw-bold mb-0 text-primary" id="textSaldo">Rp 0</h5>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center ms-3" style="width: 48px; height: 48px; min-width: 48px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= 2. FILTER & TOOLS ================= --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            {{-- Form Filter Utama --}}
            <form id="formFilterUtama">
                <div class="d-flex flex-wrap justify-content-between align-items-end gap-3">
                    
                    {{-- AREA FILTER (KIRI) --}}
                    <div class="d-flex flex-wrap gap-2 flex-grow-1">
                        
                        {{-- Cari Deskripsi --}}
                        <div style="min-width: 200px;">
                            <label class="form-label small fw-bold text-muted mb-1">Cari</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" id="searchInput" class="form-control border-start-0" placeholder="Deskripsi...">
                            </div>
                        </div>

                        {{-- Filter Tipe --}}
                        <div style="min-width: 150px;">
                            <label class="form-label small fw-bold text-muted mb-1">Tipe</label>
                            <select name="tipe_transaksi" class="form-select">
                                <option value="">Semua</option>
                                <option value="pemasukan">Pemasukan</option>
                                <option value="pengeluaran">Pengeluaran</option>
                            </select>
                        </div>

                        {{-- Filter Periode (Utama) --}}
                        <div style="min-width: 150px;">
                            <label class="form-label small fw-bold text-muted mb-1">Periode</label>
                            <select name="periode" id="filter-periode" class="form-select">
                                <option value="semua">Semua Waktu</option>
                                <option value="per_bulan" {{ date('d') <= 7 ? 'selected' : '' }}>Bulan</option>
                                <option value="per_tahun">Tahun</option>
                                <option value="rentang_waktu">Custom Tanggal</option>
                            </select>
                        </div>

                        {{-- INPUT DINAMIS (Muncul sesuai pilihan periode) --}}
                        
                        {{-- A. Input Bulan --}}
                        <div class="filter-option" id="filter-bulanan" style="display: none;">
                            <label class="form-label small fw-bold text-muted mb-1">Bulan & Tahun</label>
                            <div class="input-group">
                                <select name="bulan" class="form-select">
                                    @foreach(range(1,12) as $m)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->isoFormat('MMM') }}</option>
                                    @endforeach
                                </select>
                                <select name="tahun_bulanan" class="form-select">
                                    @foreach(range(date('Y'), 2020) as $y)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- B. Input Tahun --}}
                        <div class="filter-option" id="filter-tahunan" style="display: none;">
                            <label class="form-label small fw-bold text-muted mb-1">Tahun</label>
                            <select name="tahun_tahunan" class="form-select">
                                @foreach(range(date('Y'), 2020) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- C. Input Rentang Tanggal --}}
                        <div class="filter-option" id="filter-rentang" style="display: none;">
                            <label class="form-label small fw-bold text-muted mb-1">Rentang Tanggal</label>
                            <div class="input-group">
                                <input type="date" name="tanggal_mulai" class="form-control" value="{{ date('Y-m-01') }}">
                                <span class="input-group-text bg-white">-</span>
                                <input type="date" name="tanggal_akhir" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- Tombol Filter Icon --}}
                        <div class="d-flex align-items-end">
                            <button type="button" id="btnTerapkanFilter" class="btn btn-primary shadow-sm" title="Terapkan Filter">
                                <i class="bi bi-filter"></i>
                            </button>
                        </div>
                    </div>

                    {{-- AREA AKSI (KANAN) --}}
                    <div class="d-flex gap-2">
                        {{-- Tombol Export PDF (Mengambil data filter form ini) --}}
                        <button type="submit" formaction="{{ route('pengurus.keuangan.export.pdf') }}" formtarget="_blank" class="btn btn-outline-danger shadow-sm">
                            <i class="bi bi-file-pdf me-1"></i> PDF
                        </button>

                        <button type="button" class="btn btn-outline-secondary shadow-sm" id="btnKelolaKategori">
                            <i class="bi bi-tags me-1"></i> Kategori
                        </button>
                        <button type="button" class="btn btn-primary shadow-sm" id="btnTambahTransaksi">
                            <i class="bi bi-plus-lg me-1"></i> Transaksi
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= 3. TABEL TRANSAKSI ================= --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="tabelKeuangan">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori</th>
                            <th>Deskripsi</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-end">Saldo</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Data dimuat via JS --}}
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div id="paginationContainer" class="d-flex justify-content-between align-items-center mt-3">
                <span id="paginationInfo" class="small text-muted"></span>
                <nav id="paginationLinks"></nav>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL 1: FORM TRANSAKSI (TAMBAH/EDIT) ================= --}}
<div class="modal fade" id="modalTransaksi" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <form id="formTransaksi">
                <input type="hidden" id="id_keuangan" name="id_keuangan">
                
                <div class="modal-header border-0 pb-0 pt-4 px-4">
                    <h4 class="modal-title fw-bold" id="modalTransaksiTitle">Tambah Transaksi</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Jenis Transaksi --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Jenis Transaksi</label>
                        <select class="form-select py-2" name="tipe" id="selectTipeTransaksi" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="pemasukan">Pemasukan (+)</option>
                            <option value="pengeluaran">Pengeluaran (-)</option>
                        </select>
                    </div>

                    {{-- Nominal --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nominal</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text fw-bold" id="spanRpPrefix">Rp</span>
                            <input type="text" class="form-control fs-4 fw-bold" name="nominal_display" id="inputNominal" required placeholder="0" autocomplete="off">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Tanggal</label>
                            <input type="date" class="form-control" name="tanggal" id="inputTanggal" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Kategori</label>
                            <select class="form-select" name="id_kategori_keuangan" id="selectKategoriTransaksi" required disabled>
                                <option value="">Pilih Jenis Dulu</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small text-muted">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="inputDeskripsi" rows="2" placeholder="Keterangan transaksi..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary fw-bold px-4" id="btnSimpanTransaksi">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================= MODAL 2: KELOLA KATEGORI ================= --}}
<div class="modal fade" id="modalKategori" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold">Kelola Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Filter Jenis Kategori di Modal --}}
                <div class="mb-3">
                    <label class="form-label small text-muted">Pilih Jenis Kategori:</label>
                    <select class="form-select" id="selectTipeKategori">
                        <option value="pemasukan">Kategori Pemasukan</option>
                        <option value="pengeluaran">Kategori Pengeluaran</option>
                    </select>
                </div>

                {{-- Form Tambah/Edit Kategori --}}
                <form id="formKategori" class="mb-4">
                    <input type="hidden" id="id_kategori" name="id_kategori">
                    <input type="hidden" id="inputTipeKategoriHidden" name="tipe"> 
                    
                    <div class="input-group shadow-sm">
                        <input type="text" class="form-control bg-light" id="nama_kategori" name="nama_kategori_keuangan" placeholder="Nama kategori baru..." required>
                        <button class="btn btn-primary" type="submit" id="btnSimpanKategori"><i class="bi bi-plus-lg"></i></button>
                        <button type="button" class="btn btn-secondary d-none" id="btnBatalEditKategori"><i class="bi bi-x-lg"></i></button>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-muted mb-0">Daftar Kategori</h6>
                    <span class="badge bg-light text-secondary rounded-pill" id="totalKategoriBadge">0</span>
                </div>
                
                {{-- Container List Kategori --}}
                <div id="listKategoriContainer" class="px-1 py-1 bg-light rounded" style="max-height: 250px; overflow-y: auto;">
                    {{-- List via JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const URL_API_TRANSAKSI = '/pengurus/keuangan'; 
    const URL_API_KATEGORI = '/pengurus/kategori-keuangan';
    
    // Variabel Global untuk Saldo Awal (dipakai untuk hitung mundur di tabel)
    let STARTING_BALANCE = 0; 
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const token = document.querySelector('meta[name="csrf-token"]').content;
    const tbody = document.querySelector('#tabelKeuangan tbody');
    let currentPage = 1;

    // --- INIT MODAL ---
    const modalTransaksiBS = new bootstrap.Modal(document.getElementById('modalTransaksi'));
    const modalKategoriBS = new bootstrap.Modal(document.getElementById('modalKategori'));

    // --- FORMAT RUPIAH INPUT ---
    const inputNominal = document.getElementById('inputNominal');
    inputNominal.addEventListener('keyup', function(e) {
        let value = this.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
        this.value = rupiah;
    });

    // ============================================================
    // 1. FILTER PERIODE LOGIC (Show/Hide Inputs)
    // ============================================================
    const periodeSelect = document.getElementById('filter-periode');
    const filterBulanan = document.getElementById('filter-bulanan');
    const filterTahunan = document.getElementById('filter-tahunan');
    const filterRentang = document.getElementById('filter-rentang');

    function toggleFilterInputs() {
        const val = periodeSelect.value;
        filterBulanan.style.display = 'none';
        filterTahunan.style.display = 'none';
        filterRentang.style.display = 'none';

        if (val === 'per_bulan') filterBulanan.style.display = 'block';
        if (val === 'per_tahun') filterTahunan.style.display = 'block';
        if (val === 'rentang_waktu') filterRentang.style.display = 'block';
    }

    periodeSelect.addEventListener('change', toggleFilterInputs);
    toggleFilterInputs(); // Jalankan saat load

    // ============================================================
    // 2. LOAD DATA (FETCH API)
    // ============================================================
    document.getElementById('btnTerapkanFilter').addEventListener('click', () => {
        currentPage = 1;
        loadData();
    });

    async function loadData() {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';
        
        // Ambil data dari form filter utama
        const form = document.getElementById('formFilterUtama');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        params.append('page', currentPage);

        try {
            const res = await fetch(`${URL_API_TRANSAKSI}/data?${params.toString()}`);
            const response = await res.json();
            
            // A. Update Kartu Statistik di Atas
            updateInfoCards(response.stats);

            // B. Update Tabel
            renderTable(response.table_data.data);
            
            // C. Update Pagination
            renderPagination(response.table_data);

        } catch (e) {
            console.error(e);
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Gagal memuat data. Silakan coba lagi.</td></tr>';
        }
    }

    function updateInfoCards(stats) {
        const fmt = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(num);

        document.getElementById('textTotalPemasukan').textContent = fmt(stats.pemasukan);
        document.getElementById('textTotalPengeluaran').textContent = fmt(stats.pengeluaran);
        document.getElementById('textSaldo').textContent = fmt(stats.saldo);
        
        // Simpan saldo ini untuk patokan perhitungan mundur di tabel
        STARTING_BALANCE = stats.saldo;
    }

    function renderTable(data) {
        tbody.innerHTML = '';
        if(data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-5">Tidak ada data transaksi yang ditemukan.</td></tr>';
            return;
        }

        // TIDAK PERLU LAGI VARIABEL runningBalance ATAU STARTING_BALANCE DISINI
        // Karena data backend sudah membawa nilai saldo yang benar per baris

        data.forEach(item => {
            const isMasuk = item.tipe === 'pemasukan';
            
            const badgeTipe = isMasuk 
                ? '<span class="badge bg-success bg-opacity-10 text-success">Pemasukan</span>' 
                : '<span class="badge bg-danger bg-opacity-10 text-danger">Pengeluaran</span>';
            
            const colorClass = isMasuk ? 'text-success' : 'text-danger';
            const symbol = isMasuk ? '+ ' : '- ';
            
            // Ambil saldo yang sudah dihitung backend
            const saldoRow = item.saldo_berjalan_formatted;

            const row = `
                <tr>
                    <td>${new Date(item.tanggal).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'})}</td>
                    <td>${badgeTipe}</td>
                    <td>${item.kategori?.nama_kategori_keuangan || '-'}</td>
                    <td>${item.deskripsi || '-'}</td>
                    <td class="text-end fw-bold ${colorClass}">${symbol} Rp ${new Intl.NumberFormat('id-ID').format(item.nominal)}</td>
                    
                    <td class="text-end fw-bold ${saldoRow < 0 ? 'text-danger' : 'text-primary'}">
                        Rp ${new Intl.NumberFormat('id-ID').format(saldoRow)}
                    </td>
                    
                    <td class="text-center">
                        <button class="btn btn-sm btn-light border text-primary rounded-circle shadow-sm" onclick="window.editTransaksi('${item.id_keuangan}')" title="Edit"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm" onclick="window.hapusTransaksi('${item.id_keuangan}')" title="Hapus"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>`;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }

    function renderPagination(response) {
        const linksContainer = document.getElementById('paginationLinks');
        linksContainer.innerHTML = ''; 
        
        if(response.total === 0) return;

        let ul = document.createElement('ul');
        ul.className = 'pagination justify-content-end mb-0';

        response.links.forEach(link => {
            let li = document.createElement('li');
            li.className = `page-item ${link.active ? 'active' : ''} ${link.url ? '' : 'disabled'}`;
            let btn = document.createElement('button');
            btn.className = 'page-link shadow-none';
            
            let label = link.label;
            // Ubah simbol navigasi
            if (label.includes('Previous') || label.includes('&laquo;')) label = '<';
            else if (label.includes('Next') || label.includes('&raquo;')) label = '>';
            
            btn.innerHTML = label;
            
            if (link.url) {
                btn.onclick = (e) => {
                    e.preventDefault();
                    const urlObj = new URL(link.url);
                    currentPage = urlObj.searchParams.get('page');
                    loadData();
                };
            }
            li.appendChild(btn);
            ul.appendChild(li);
        });
        linksContainer.appendChild(ul);
        document.getElementById('paginationInfo').textContent = `Menampilkan ${response.from || 0} - ${response.to || 0} dari ${response.total} data`;
    }

    // ============================================================
    // 3. LOGIC TRANSAKSI (MODAL)
    // ============================================================
    
    // Buka Modal Tambah
    document.getElementById('btnTambahTransaksi').addEventListener('click', () => {
        document.getElementById('formTransaksi').reset();
        document.getElementById('id_keuangan').value = '';
        document.getElementById('modalTransaksiTitle').textContent = "Tambah Transaksi";
        
        const selectTipe = document.getElementById('selectTipeTransaksi');
        selectTipe.value = ""; 
        selectTipe.disabled = false;
        changeTransactionStyle(""); 
        
        const selectKat = document.getElementById('selectKategoriTransaksi');
        selectKat.innerHTML = '<option value="">-- Pilih Jenis Dulu --</option>';
        selectKat.disabled = true;

        modalTransaksiBS.show();
    });

    // Change Style saat Pilih Tipe
    document.getElementById('selectTipeTransaksi').addEventListener('change', function() {
        const tipe = this.value;
        changeTransactionStyle(tipe);
        loadDropdownKategoriForTransaction(tipe);
    });

    function changeTransactionStyle(tipe) {
        const btnSimpan = document.getElementById('btnSimpanTransaksi');
        const spanRp = document.getElementById('spanRpPrefix');
        const title = document.getElementById('modalTransaksiTitle');
        
        if (tipe === 'pemasukan') {
            btnSimpan.className = 'btn btn-success fw-bold px-4';
            spanRp.className = 'input-group-text fw-bold text-white bg-success';
            title.className = 'modal-title fw-bold text-success';
        } else if (tipe === 'pengeluaran') {
            btnSimpan.className = 'btn btn-danger fw-bold px-4';
            spanRp.className = 'input-group-text fw-bold text-white bg-danger';
            title.className = 'modal-title fw-bold text-danger';
        } else {
            btnSimpan.className = 'btn btn-primary fw-bold px-4';
            spanRp.className = 'input-group-text fw-bold';
            title.className = 'modal-title fw-bold';
        }
    }

    async function loadDropdownKategoriForTransaction(tipe, selectedId = null) {
        const select = document.getElementById('selectKategoriTransaksi');
        if (!tipe) {
            select.innerHTML = '<option value="">-- Pilih Jenis Dulu --</option>';
            select.disabled = true;
            return;
        }

        select.disabled = true;
        select.innerHTML = '<option>Loading...</option>';

        try {
            const res = await fetch(`${URL_API_KATEGORI}/data?tipe=${tipe}`);
            const data = await res.json();
            
            select.innerHTML = '<option value="">-- Pilih Kategori --</option>';
            data.forEach(cat => {
                const isSelected = selectedId == cat.id_kategori_keuangan ? 'selected' : '';
                select.innerHTML += `<option value="${cat.id_kategori_keuangan}" ${isSelected}>${cat.nama_kategori_keuangan}</option>`;
            });
            select.disabled = false;
        } catch (e) {
            select.innerHTML = '<option>Gagal memuat</option>';
        }
    }

    // Submit Transaksi
    document.getElementById('formTransaksi').addEventListener('submit', async (e) => {
        e.preventDefault();
        const btn = document.getElementById('btnSimpanTransaksi');
        const originalText = btn.innerHTML;
        btn.disabled = true; btn.innerHTML = 'Loading...';

        const formData = new FormData(e.target);
        formData.set('nominal', document.getElementById('inputNominal').value.replace(/\./g, ''));
        
        const id = document.getElementById('id_keuangan').value;
        let url = URL_API_TRANSAKSI;
        if (id) {
            url += `/${id}`;
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: formData
            });
            const data = await res.json();
            
            if(!res.ok) throw new Error(data.message || 'Error');

            modalTransaksiBS.hide();
            Swal.fire('Sukses', data.message, 'success');
            loadData(); // Reload Data
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        } finally {
            btn.disabled = false; btn.innerHTML = originalText;
        }
    });

    // Edit Transaksi
    window.editTransaksi = async (id) => {
        try {
            const res = await fetch(`${URL_API_TRANSAKSI}/${id}`);
            const data = await res.json();

            document.getElementById('id_keuangan').value = data.id_keuangan;
            document.getElementById('modalTransaksiTitle').textContent = "Edit Transaksi";
            
            const selectTipe = document.getElementById('selectTipeTransaksi');
            selectTipe.value = data.tipe;
            // Kunci tipe saat edit agar tidak kacau
            // selectTipe.disabled = true; 
            
            changeTransactionStyle(data.tipe);
            
            await loadDropdownKategoriForTransaction(data.tipe, data.id_kategori_keuangan);

            document.getElementById('inputNominal').value = new Intl.NumberFormat('id-ID').format(data.nominal);
            document.getElementById('inputTanggal').value = data.tanggal;
            document.getElementById('inputDeskripsi').value = data.deskripsi;

            modalTransaksiBS.show();
        } catch (e) {
            Swal.fire('Error', 'Gagal memuat data', 'error');
        }
    };

    // Hapus Transaksi
    window.hapusTransaksi = async (id) => {
        const c = await Swal.fire({
            title: 'Hapus?', text: 'Data tidak bisa kembali', icon: 'warning',
            showCancelButton: true, confirmButtonColor: '#d33'
        });
        if(c.isConfirmed) {
            await fetch(`${URL_API_TRANSAKSI}/${id}`, { 
                method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } 
            });
            loadData();
        }
    };

    // ============================================================
    // 4. LOGIC KATEGORI (MODAL)
    // ============================================================
    
    // Buka Modal Kategori
    document.getElementById('btnKelolaKategori').addEventListener('click', () => {
        document.getElementById('selectTipeKategori').value = 'pemasukan';
        loadListKategoriByTipe('pemasukan');
        modalKategoriBS.show();
    });

    // Filter Tipe di Modal Kategori
    document.getElementById('selectTipeKategori').addEventListener('change', function() {
        loadListKategoriByTipe(this.value);
        resetFormKategori();
    });

    async function loadListKategoriByTipe(tipe) {
        const container = document.getElementById('listKategoriContainer');
        container.innerHTML = '<div class="text-center p-3">Loading...</div>';
        document.getElementById('inputTipeKategoriHidden').value = tipe; 

        try {
            const res = await fetch(`${URL_API_KATEGORI}/data?tipe=${tipe}`);
            const data = await res.json();
            
            document.getElementById('totalKategoriBadge').textContent = data.length;
            container.innerHTML = '';
            
            if(data.length === 0) {
                container.innerHTML = '<p class="text-center text-muted small p-3">Belum ada kategori.</p>';
                return;
            }

            data.forEach(cat => {
                container.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center p-2 mb-1 border-bottom bg-white">
                        <span>${cat.nama_kategori_keuangan}</span>
                        <div>
                            <button class="btn btn-sm text-primary" onclick="editKategori('${cat.id_kategori_keuangan}', '${cat.nama_kategori_keuangan}')"><i class="bi bi-pencil"></i></button>
                            <button class="btn btn-sm text-danger" onclick="hapusKategori('${cat.id_kategori_keuangan}')"><i class="bi bi-trash"></i></button>
                        </div>
                    </div>`;
            });
        } catch(e) { container.innerHTML = 'Error loading data'; }
    }

    // Submit Kategori
    document.getElementById('formKategori').addEventListener('submit', async (e) => {
        e.preventDefault();
        const tipeDipilih = document.getElementById('selectTipeKategori').value;
        const id = document.getElementById('id_kategori').value;
        const nama = document.getElementById('nama_kategori').value;
        
        let url = URL_API_KATEGORI;
        let method = 'POST';
        let bodyData = { nama_kategori_keuangan: nama, tipe: tipeDipilih };

        if(id) { url += `/${id}`; method = 'PUT'; }

        try {
            const res = await fetch(url, {
                method: method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                body: JSON.stringify(bodyData)
            });
            if(res.ok) {
                resetFormKategori();
                loadListKategoriByTipe(tipeDipilih);
            } else { alert('Gagal menyimpan kategori'); }
        } catch(e) { console.error(e); }
    });

    // Helper Edit Kategori
    window.editKategori = (id, nama) => {
        document.getElementById('id_kategori').value = id;
        document.getElementById('nama_kategori').value = nama;
        document.getElementById('btnSimpanKategori').innerHTML = '<i class="bi bi-check-lg"></i>';
        document.getElementById('btnBatalEditKategori').classList.remove('d-none');
    };
    
    document.getElementById('btnBatalEditKategori').addEventListener('click', resetFormKategori);

    function resetFormKategori() {
        document.getElementById('id_kategori').value = '';
        document.getElementById('nama_kategori').value = '';
        document.getElementById('btnSimpanKategori').innerHTML = '<i class="bi bi-plus-lg"></i>';
        document.getElementById('btnBatalEditKategori').classList.add('d-none');
    }

    // Hapus Kategori
    window.hapusKategori = async (id) => {
        if(!confirm('Hapus kategori?')) return;
        const tipe = document.getElementById('selectTipeKategori').value;
        await fetch(`${URL_API_KATEGORI}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token } });
        loadListKategoriByTipe(tipe);
    }

    // --- EXECUTE INITIAL LOAD ---
    loadData();
});
</script>
@endpush