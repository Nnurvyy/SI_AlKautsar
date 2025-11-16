@extends('layouts.public')

@section('title', 'Jadwal Adzan')

@push('styles')
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
    }

    .table-container {
        /* Adjust height dynamically with calc, subtracting header/padding */
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

        /* Responsive adjustments */                                                         
        @media (max-width: 576px) {                                                          
            .card-header-flex {                                                              
                flex-direction: column;                                                      
                align-items: flex-start;                                                     
            }                                                                                
            .jadwal-filter-controls {                                                        
                width: 100%;                                                                 
            }                                                                                
            .jadwal-filter-controls .form-select,                                            
            .jadwal-filter-controls .btn {                                                   
                flex-grow: 1;                                                                
            }                                                                                
            .table-container {                                                               
                /* Taller on mobile to push footer down */                                   
                height: calc(100vh - 220px);                                                 
            }                                                                                
        }
        
            .jadwal-sholat-title {
        
                font-size: 1.8rem; /* Increased font size */
        
            }
        
        
        
            .card-no-border {
                background-color: #f8f9fa !important;
        
                border: none !important;
        
                box-shadow: none !important; /* Also remove shadow if any */
        
            }
        
                        .card-no-border .card-header {                                               
        
                                                                                                     
        
                            border-bottom: none !important;                                          
        
                                                                                                     
        
                            background-color: transparent !important;                                
        
                                                                                                     
        
                            padding-bottom: 0 !important; /* Adjust padding if needed after removing 
        
            border */                                                                                
        
                                                                                                     
        
                        }
        
            
        
                        @media (min-width: 768px) {
        
                            #bulan-select {
        
                                min-width: 150px;
        
                            }
        
                        }
        
                                                                                                     
        
                    </style>@endpush

@section('content')
<div class="main-content">
    <div class="container py-4">
        <div class="card card-no-border">
            <div class="card-header card-header-flex">
                <div>
                    <h4 class="card-title mb-0 fw-bold jadwal-sholat-title">Jadwal Sholat</h4>
                    <p class="card-subtitle text-muted mb-0" id="card-subtitle">
                        {{ $lokasi }} - {{ $namaBulan[(int)$bulan] }} {{ $tahun }}
                    </p>
                </div>
                <div class="jadwal-filter-controls">
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
        const url = `{{ route('public.jadwal-adzan.api') }}?bulan=${bulan}&tahun=${tahun}`;

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
                    // If response is not OK, try to parse JSON error
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update subtitle
                    cardSubtitle.textContent = `${data.lokasi} - ${namaBulan[parseInt(bulan, 10)]} ${tahun}`;

                    // Clear table
                    tableBody.innerHTML = '';

                    // Populate table
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
                    
                    // Scroll to today if it's the current month/year
                    scrollToToday();

                } else {
                    // Handle API-specific errors (e.g., data.success is false)
                    alert('Gagal memuat data: ' + (data.message || 'Terjadi kesalahan tidak diketahui.'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                let errorMessage = 'Terjadi kesalahan saat mengambil data. Silakan coba lagi.';
                if (error.message) {
                    errorMessage = error.message;
                } else if (error.errors) {
                    // Validation errors from Laravel
                    errorMessage = 'Input tidak valid:\n' + Object.values(error.errors).flat().join('\n');
                }
                alert(errorMessage);
            })
                        .finally(() => {                                                             
                            spinner.classList.remove('show');
                            filterBtn.disabled = false;
                            filterBtn.innerHTML = 'Lihat';
                        });    });
});
</script>
@endpush