@extends('layouts.public')

@section('title', 'Jadwal Adzan')

@push('styles')
{{-- 1. Tambahkan CSS Select2 --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Make sure content fills the screen height */
    .content-wrapper {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    .main-content {
        flex-grow: 1;
    }

    .card-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .jadwal-filter-controls {
        display: flex;
        gap: 0.5rem;
        align-items: center;
        flex-wrap: wrap; /* Agar responsif di mobile */
    }
    
    /* 2. Style untuk Select2 agar rapi */
    #lokasi-select {
        width: 200px;
    }
    .select2-container .select2-selection--single {
        height: calc(1.5em + 0.5rem + 2px); /* Samakan tinggi dgn input form-select-sm */
        padding: 0.25rem 0.5rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.5rem);
    }
    /* Style untuk dropdown hasil Select2 */
    .select2-container--open .select2-dropdown {
        z-index: 1056; /* Pastikan di atas elemen lain */
    }


    .table-container {
        margin-top: 1rem;
        height: calc(100vh - 250px); 
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 0 0 0.375rem 0.375rem;
        position: relative; /* For spinner */
    }

    .table-jadwal thead {
        position: sticky;
        top: 0;
        z-index: 3;
    }
    
    .table-jadwal tbody tr.table-primary {
        background-color: #cfe2ff !important;
        font-weight: bold;
    }

    /* --- Custom Loader --- */
    .spinner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 10;
        visibility: hidden;
        opacity: 0;
        transition: opacity 0.3s, visibility 0.3s;
        border-radius: 0 0 0.375rem 0.375rem;
    }
    .spinner-overlay.show {
        visibility: visible;
        opacity: 1;
    }
    .custom-loader {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .spinner-overlay .spinner-text {
        margin-top: 1rem;
        color: #333;
        font-weight: 500;
        font-size: 1.1rem;
    }
    /* --- End Custom Loader --- */

    @media (max-width: 768px) {
        .card-header-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        .jadwal-filter-controls {
            width: 100%;
        }
        /* Buat semua kontrol filter sama lebar di mobile */
        .jadwal-filter-controls .form-select,
        .jadwal-filter-controls .select2-container,
        .jadwal-filter-controls .btn {
            flex-grow: 1;
            width: 100% !important;
            margin-bottom: 0.5rem;
        }
        .table-container {
            height: calc(100vh - 300px); /* Sesuaikan tinggi mobile */
        }
    }
    
    .jadwal-sholat-title {
        font-size: 1.8rem;
    }
    
    .card-no-border {
        background-color: #f8f9fa !important;
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-no-border .card-header {
        border-bottom: none !important;
        background-color: transparent !important;
        padding-bottom: 0 !important;
    }
    
    @media (min-width: 768px) {
        #bulan-select {
            min-width: 150px;
        }
    }
</style>
@endpush

@section('content')
<div class="main-content">
    <div class="container py-4">
        <div class="card card-no-border">
            <div class="card-header card-header-flex">
                <div>
                    <h4 class="card-title mb-0 fw-bold jadwal-sholat-title">Jadwal Adzan</h4>
                    <p class="card-subtitle text-muted mb-0" id="card-subtitle">
                        {{-- Ini akan di-update oleh JS --}}
                        {{ $lokasi }} - {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
                    </p>
                </div>
                <div class="jadwal-filter-controls">
                    
                    {{-- ================================================= --}}
                    {{-- 3. Tambahkan Select2 Lokasi --}}
                    {{-- ================================================= --}}
                    <select id="lokasi-select" class="form-select form-select-sm">
                        {{-- Opsi default diisi oleh JS --}}
                    </select>
                    
                    <select name="bulan" id="bulan-select" class="form-select form-select-sm">
                        @foreach ($namaBulan as $num => $name)
                            <option value="{{ $num }}" {{ $num == $bulan ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <select name="tahun" id="tahun-select" class="form-select form-select-sm">
                        @foreach ($listTahun as $th)
                            <option value="{{ $th }}" {{ $th == $tahun ? 'selected' : '' }}>
                                {{ $th }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" id="filter-btn" class="btn btn-primary btn-sm">Lihat</button>
                </div>
            </div>

            @if (session('error'))
                <div class="alert alert-danger m-3">
                    {{ session('error') }}
                </div>
            @else
                <div class="table-container" id="table-container">
                    <div class="spinner-overlay" id="spinner">
                        <div class="custom-loader"></div>
                        <span class="spinner-text">Memuat Jadwal...</span>
                    </div>
                    @if (empty($jadwal))
                        <div class="alert alert-warning m-3">
                            Data jadwal tidak tersedia untuk periode ini.
                        </div>
                    @else
                        <table class="table table-striped table-hover table-bordered mb-0 table-jadwal">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-center">Tanggal</th>
                                    <th class="text-center">Imsak</th>
                                    <th class="text-center">Subuh</th>
                                    <th class="text-center">Terbit</th>
                                    <th class="text-center">Dhuha</th>
                                    <th class="text-center">Dzuhur</th>
                                    <th class="text-center">Ashar</th>
                                    <th class="text-center">Maghrib</th>
                                    <th class="text-center">Isya</th>
                                </tr>
                            </thead>
                            <tbody id="jadwal-tbody">
                                @php $today = now()->format('Y-m-d'); @endphp
                                @foreach ($jadwal as $item)
                                    @php
                                        $isToday = $item['date'] == $today;
                                        $rowId = $isToday ? 'today-row' : '';
                                    @endphp
                                    <tr id="{{ $rowId }}" class="{{ $isToday ? 'table-primary' : '' }}">
                                        <td class="text-center">{{ $item['tanggal'] }}</td>
                                        <td class="text-center">{{ $item['imsak'] }}</td>
                                        <td class="text-center">{{ $item['subuh'] }}</td>
                                        <td class="text-center">{{ $item['terbit'] }}</td>
                                        <td class="text-center">{{ $item['dhuha'] }}</td>
                                        <td class="text-center">{{ $item['dzuhur'] }}</td>
                                        <td class="text-center">{{ $item['ashar'] }}</td>
                                        <td class="text-center">{{ $item['maghrib'] }}</td>
                                        <td class="text-center">{{ $item['isya'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- 4. Tambahkan jQuery & JS Select2 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> {{-- Ganti alert() jadi SweetAlert --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterBtn = document.getElementById('filter-btn');
    const bulanSelect = document.getElementById('bulan-select');
    const tahunSelect = document.getElementById('tahun-select');
    const spinner = document.getElementById('spinner');
    const tableBody = document.getElementById('jadwal-tbody');
    const cardSubtitle = document.getElementById('card-subtitle');
    const tableContainer = document.getElementById('table-container');
    const namaBulan = @json($namaBulan);
    
    const defaultLokasiId = '{{ $kotaId }}';
    const defaultLokasiText = '{{ $lokasi }}';
    const lokasiSelect = $('#lokasi-select'); 

    lokasiSelect.select2({
        placeholder: 'Cari kota...',
        ajax: {
            // =================================================
            // PERBAIKAN DI SINI:
            // 'url' diubah dari string menjadi fungsi
            // =================================================
            url: function (params) {
                // Tambahkan keyword pencarian (params.term) ke path URL
                return 'https://api.myquran.com/v2/sholat/kota/cari/' + params.term;
            },
            dataType: 'json',
            delay: 250, 
            // 'data: function (params) { ... }' tidak diperlukan lagi
            processResults: function (data) {
                // Cek jika API merespon dengan 'status: true' dan ada 'data'
                if (data.status && data.data) {
                    return {
                        results: data.data.map(item => ({
                            id: item.id,
                            text: item.lokasi
                        }))
                    };
                } else {
                    // Jika API mengembalikan status false atau format salah
                    return { results: [] };
                }
            },
            cache: true
        },
        minimumInputLength: 3 
    });

    // Set nilai default untuk Select2
    var defaultOption = new Option(defaultLokasiText, defaultLokasiId, true, true);
    lokasiSelect.append(defaultOption).trigger('change');

    
    function scrollToToday() {
        const todayRow = document.getElementById('today-row');
        if (todayRow && tableContainer) {
            const rowTop = todayRow.offsetTop;
            const containerTop = tableContainer.offsetTop;
            tableContainer.scrollTop = rowTop - containerTop;
        }
    }

    // Initial scroll
    scrollToToday();

    filterBtn.addEventListener('click', function() {
        const bulan = bulanSelect.value;
        const tahun = tahunSelect.value;
        const lokasiId = lokasiSelect.val(); 
        
        const url = `{{ route('public.jadwal-adzan.api') }}?lokasi_id=${lokasiId}&bulan=${bulan}&tahun=${tahun}`;

        spinner.classList.add('show');
        filterBtn.disabled = true;
        filterBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memuat...';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                cardSubtitle.textContent = `${data.lokasi} - ${namaBulan[parseInt(bulan, 10)]} ${tahun}`;
                tableBody.innerHTML = '';

                const today = new Date();
                const todayDateString = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

                data.jadwal.forEach(item => {
                    const isToday = item.date === todayDateString;
                    const rowId = isToday ? 'today-row' : '';
                    const rowClass = isToday ? 'table-primary' : '';

                    const row = `
                        <tr id="${rowId}" class="${rowClass}">
                            <td class="text-center">${item.tanggal}</td>
                            <td class="text-center">${item.imsak}</td>
                            <td class="text-center">${item.subuh}</td>
                            <td class="text-center">${item.terbit}</td>
                            <td class="text-center">${item.dhuha}</td>
                            <td class="text-center">${item.dzuhur}</td>
                            <td class="text-center">${item.ashar}</td>
                            <td class="text-center">${item.maghrib}</td>
                            <td class="text-center">${item.isya}</td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
                
                scrollToToday();

            } else {
                Swal.fire('Gagal', data.message || 'Terjadi kesalahan tidak diketahui.', 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            let errorMessage = 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.';
            if (error.message) {
                errorMessage = error.message;
            } else if (error.errors) {
                errorMessage = 'Input tidak valid:\n' + Object.values(error.errors).flat().join('\n');
            }
            Swal.fire('Gagal', errorMessage, 'error');
        })
        .finally(() => {
            spinner.classList.remove('show');
            filterBtn.disabled = false;
            filterBtn.innerHTML = 'Lihat';
        });
    });
});
</script>
@endpush