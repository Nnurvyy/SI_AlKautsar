@extends('layouts.app')

@section('title', 'Khotib Jumat')

@section('content')

@php
    $khotibJumat = [
        (object)[
            'tanggal' => '01/11/2025',
            'nama_khotib_jumat' => 'Ustadz Ahmad Fauzi',
            'nama_imam_jumat' => 'Ustadz Rahman',
            'tema_khotib_jumat' => 'Makna Keikhlasan dalam Ibadah',
            'foto_khotib' => 'ustadz_ahmad.jpg',
        ],
        (object)[
            'tanggal' => '08/11/2025',
            'nama_khotib_jumat' => 'Ustadz Ridwan Syukur',
            'nama_imam_jumat' => 'Ustadz Ali Hasan',
            'tema_khotib_jumat' => 'Menjaga Persaudaraan Umat',
            'foto_khotib' => 'ustadz_ridwan.jpg',
        ],
    ];
@endphp

<div class="container-fluid p-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        
        <div class="d-flex align-items-center flex-wrap">
            <div class="input-group search-bar me-2" style="width: 300px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Cari khotib...">
            </div>

            <select class="form-select me-2" style="width: auto;">
                <option selected>Semua Tema</option>
                <option value="1">Keikhlasan</option>
                <option value="2">Persaudaraan</option>
            </select>

            <select class="form-select" style="width: auto;">
                <option selected>Semua Imam</option>
                <option value="1">Ustadz Rahman</option>
                <option value="2">Ustadz Ali Hasan</option>
            </select>
        </div>
        
        <div class="d-flex align-items-center mt-2 mt-md-0">
            <a href="#" class="btn btn-success btn-custom-padding d-flex align-items-center"> 
                <i class="bi bi-plus-circle me-2"></i>
                Tambah Khotib Jumat 
            </a>
        </div>
    </div>

    <div class="card transaction-table border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    
                    <thead class="table-light">
                        <tr>
                            <th scope="col" style="width: 15%;">Foto Khotib</th>
                            <th scope="col" style="width: 20%;">Nama Khotib</th>
                            <th scope="col" style="width: 20%;">Nama Imam</th>
                            <th scope="col">Tema Khotbah</th>
                            <th scope="col" style="width: 10%;">Tanggal</th>
                            <th scope="col" style="width: 8%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($khotibJumat as $item)
                        <tr>
                            <td>
                                <img src="{{ asset('storage/khotib_jumat/' . $item->foto_khotib) }}" 
                                     alt="{{ $item->nama_khotib_jumat }}" 
                                     class="rounded" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </td>
                            <td>{{ $item->nama_khotib_jumat }}</td>
                            <td>{{ $item->nama_imam_jumat }}</td>
                            <td>{{ $item->tema_khotib_jumat }}</td>
                            <td class="col-nowrap">{{ $item->tanggal }}</td>
                            <td class="text-center col-nowrap">
                                <a href="#" class="btn btn-sm me-1" title="Edit">
                                    <i class="bi bi-pencil text-primary fs-6"></i>
                                </a>
                                <a href="#" class="btn btn-sm" title="Hapus">
                                    <i class="bi bi-trash text-danger fs-6"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
