@extends('layouts.app')

@section('title', 'Grafik Keuangan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            
            {{-- Header dan Filter (Sesuai Screenshot) --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Data Grafik Keuangan</h4>
                
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        <select class="form-select" id="filterPeriode">
                            <option value="bulan" selected>Per Bulan</option>
                            <option value="tahun">Per Tahun</option>
                        </select>
                    </div>
                    <div>
                        <select class="form-select" id="filterTahun">
                            <option value="2025" selected>2025</option>
                            {{-- Tahun lainnya akan di-load atau di-generate oleh JS --}}
                        </select>
                    </div>
                </div>
            </div>

            {{-- Legenda Warna (Sesuai Screenshot) --}}
            <div class="d-flex align-items-center mb-4">
                <div class="me-4 d-flex align-items-center">
                    {{-- Warna #198754 adalah warna default 'success' di Bootstrap --}}
                    <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #198754;"></span>
                    <span class="text-muted">Pemasukan</span>
                </div>
                <div class="d-flex align-items-center">
                    {{-- Warna #dc3545 adalah warna default 'danger' di Bootstrap --}}
                    <span class="d-inline-block rounded-circle me-2" style="width: 12px; height: 12px; background-color: #dc3545;"></span>
                    <span class="text-muted">Pengeluaran</span>
                </div>
            </div>
            
            {{-- WADAH GRAFIK (Canvas untuk Chart.js) --}}
            <div class="chart-container" style="position: relative; height: 350px;">
                <canvas id="financialChart"></canvas> 
            </div>

            {{-- WADAH LABEL BULAN (Sumbu X) --}}
            {{-- Ini perlu dibuat terpisah dari canvas jika kita ingin meniru tampilan screenshot secara presisi dengan garis sumbu X yang terpisah dari canvas Chart.js, atau biarkan Chart.js menanganinya. Saya buatkan wadah ID jika Anda ingin custom label X --}}
            <div id="chartLabels" class="mt-4">
                {{-- Label Jan, Feb, Mar... akan diisi dan diatur posisinya oleh JavaScript --}}
            </div>

        </div>
    </div>
    
</div>

{{-- SCRIPT EXTERNAL --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
{{-- Tempatkan script JS Anda di sini. Misal: --}}
<script src="{{ asset('js/financial_chart.js') }}"></script> 
@endsection