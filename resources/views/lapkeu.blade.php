@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')

<div class="container-fluid p-4">

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-filter me-2"></i>Filter Laporan
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('pengurus.lapkeu.index') }}" method="GET">
                
                <div class="row g-3 align-items-end mb-3">
                    
                    <div class="col-md-3">
                        <label for="tipe_transaksi" class="form-label">Tipe Transaksi</label>
                        <select id="tipe_transaksi" name="tipe_transaksi" class="form-select">
                            <option value="semua" {{ request('tipe_transaksi') == 'semua' ? 'selected' : '' }}>Pemasukan & Pengeluaran</option>
                            <option value="pemasukan" {{ request('tipe_transaksi') == 'pemasukan' ? 'selected' : '' }}>Pemasukan Saja</option>
                            <option value="pengeluaran" {{ request('tipe_transaksi') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran Saja</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter-periode" class="form-label">Periode Waktu</label>
                        <select id="filter-periode" name="periode" class="form-select">
                            <option value="semua" {{ request('periode') == 'semua' ? 'selected' : '' }}>Semua</option>
                            <option value="per_bulan" {{ request('periode') == 'per_bulan' ? 'selected' : '' }}>Per Bulan</option>
                            <option value="per_tahun" {{ request('periode') == 'per_tahun' ? 'selected' : '' }}>Per Tahun</option>
                            <option value="rentang_waktu" {{ request('periode') == 'rentang_waktu' ? 'selected' : '' }}>Rentang Waktu</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="filter-bulanan" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="filter_bulan" class="form-label">Bulan</label>
                                <select id="filter_bulan" name="bulan" class="form-select">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->locale('id')->monthName }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <label for="tahun_bulanan" class="form-label">Tahun</label>
                                <select id="tahun_bulanan" name="tahun_bulanan" class="form-select">
                                    <option value="2025" {{ request('tahun_bulanan') == '2025' ? 'selected' : '' }}>2025</option>
                                    <option value="2024" {{ request('tahun_bulanan') == '2024' ? 'selected' : '' }}>2024</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3" id="filter-tahunan" style="display: none;">
                        <label for="tahun_tahunan" class="form-label">Tahun</label>
                        <select id="tahun_tahunan" name="tahun_tahunan" class="form-select">
                            <option value="2025" {{ request('tahun_tahunan') == '2025' ? 'selected' : '' }}>2025</option>
                            <option value="2024" {{ request('tahun_tahunan') == '2024' ? 'selected' : '' }}>2024</option>
                        </select>
                    </div>

                    <div class="col-md-4" id="filter-rentang" style="display: none;">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                            </div>
                            <div class="col-6">
                                <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                            </div>
                        </div>
                    </div>

                </div> 
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="bi bi-search me-1"></i> Terapkan Filter
                        </button>

                        <button type="submit" formaction="{{ route('pengurus.lapkeu.export.pdf') }}" formtarget="_blank" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                        </button>
                    </div>
                </div> 
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pemasukan</p>
                        <h4 class="fw-bold mb-0 text-success">
                            Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Total Pengeluaran</p>
                        <h4 class="fw-bold mb-0 text-danger">
                            Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div>
                        <p class="text-muted mb-1">Saldo (Berdasarkan Filter)</p>
                        <h4 class="fw-bold mb-0 {{ $saldo < 0 ? 'text-danger' : 'text-primary' }}">
                            Rp {{ number_format($saldo, 0, ',', '.') }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Data Transaksi</h5>
            <small class="text-muted">
                Menampilkan {{ $transaksi->firstItem() ?? 0 }} - {{ $transaksi->lastItem() ?? 0 }} dari {{ $transaksi->total() }} transaksi
            </small> 
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 12%;">Tanggal</th>
                            <th scope="col" style="width: 10%;">Tipe</th>
                            <th scope="col" style="width: 15%;">Kategori</th>
                            <th scope="col" style="width: 10%;">Divisi</th>
                            <th scope="col">Deskripsi</th>
                            <th scope="col" style="width: 15%;" class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                            @php
                                $isPemasukan = $item->tipe == 'pemasukan';
                                $badgeClass = $isPemasukan ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger';
                                $textClass = $isPemasukan ? 'text-success' : 'text-danger';
                                $symbol = $isPemasukan ? '+' : '-';
                            @endphp
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }} rounded-pill">
                                        {{ ucfirst($item->tipe) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $item->kategori->nama_kategori_keuangan ?? '-' }}
                                </td>
                                <td>
                                    {{-- Note: Model Keuangan tidak ada kolom divisi, 
                                         jika kategori punya divisi bisa diganti $item->kategori->divisi --}}
                                    - 
                                </td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 300px;">
                                        {{ $item->deskripsi ?? '-' }}
                                    </span>
                                </td>
                                <td class="text-end fw-bold {{ $textClass }}">
                                    {{ $symbol }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted p-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    Tidak ada data transaksi yang sesuai filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4 d-flex justify-content-end">
                {{ $transaksi->withQueryString()->links() }}
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        const periodeFilter = document.getElementById('filter-periode');
        const filterBulanan = document.getElementById('filter-bulanan');
        const filterTahunan = document.getElementById('filter-tahunan');
        const filterRentang = document.getElementById('filter-rentang'); 

        function toggleFilterVisibility() {
            const selectedValue = periodeFilter.value;

            // Sembunyikan semua dulu
            filterBulanan.style.display = 'none';
            filterTahunan.style.display = 'none';
            filterRentang.style.display = 'none'; 

            // Tampilkan yang sesuai
            if (selectedValue === 'per_bulan') {
                filterBulanan.style.display = 'block';
            } else if (selectedValue === 'per_tahun') {
                filterTahunan.style.display = 'block';
            } else if (selectedValue === 'rentang_waktu') { 
                filterRentang.style.display = 'block';
            }
        }

        periodeFilter.addEventListener('change', toggleFilterVisibility);
        // Jalankan sekali saat load agar sesuai dengan old input (jika ada)
        toggleFilterVisibility();
    });
</script>
@endpush