@extends('layouts.app')

@section('title', 'Grafik Keuangan')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container-fluid p-4">

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            
            {{-- Header dan Filter yang Disesuaikan --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">Data Grafik Keuangan</h4>
                
                <div class="d-flex align-items-center">
                    <div class="me-2">
                        {{-- Filter utama yang menentukan rentang analisis --}}
                        <select class="form-select" id="filterRange">
                            <option value="7_days">7 Hari Terakhir</option>
                            <option value="30_days">30 Hari Terakhir</option>
                            <option value="12_months" selected>12 Bulan Terakhir</option>
                            <option value="current_year">Tahun Berjalan (YTD)</option>
                            <option value="5_years">5 Tahun Terakhir</option>
                        </select>
                    </div>
                    
                </div>
            </div>

            <div>

            </div>
            
            {{-- WADAH GRAFIK (Canvas untuk Chart.js) --}}
            <div class="chart-container" style="position: relative; height: 350px;">
                <canvas id="GrafikKeuangan"></canvas>
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
<script src="{{ asset('js/grafikkeuangan.js') }}"></script>
@endsection