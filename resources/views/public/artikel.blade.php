@extends('layouts.public')

@section('title', 'Artikel & Berita')

@push('styles')
<style>
    /* ================================================= */
    /* == STYLE JUDUL == */
    /* ================================================= */
    .donasi-title-heading {
        font-family: 'Poppins', sans-serif; 
        font-weight: 700;
        font-size: 1.8rem;
        color: #333;
        margin-bottom: 0.5rem;
    }

    /* ================================================= */
    /* == STYLE ARTIKEL CARD == */
    /* ================================================= */
    .artikel-card {
        width: 100%;
        border: none;
        border-radius: 12px; 
        background-color: #fff; /* Ubah ke putih agar bersih, atau #d1d6db sesuai request */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); 
        margin-bottom: 1.5rem;
        overflow: hidden; 
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%; /* Agar tinggi kartu seragam */
        display: flex;
        flex-direction: column;
    }
    .artikel-card:hover {
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); 
    }

    .artikel-card-body {
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        flex: 1; /* Mengisi sisa ruang */
    }

    /* FOTO DI DALAM BODY */
    .artikel-card-img {
        width: 100%;
        aspect-ratio: 16 / 9; /* Rasio standar */
        object-fit: cover; 
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .artikel-card-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.5rem;
        line-height: 1.4;
        
        /* Batasi judul max 2 baris */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;  
        overflow: hidden;
    }

    .artikel-card-sinopsis {
        font-size: 0.95rem;
        color: #6c757d;
        margin-bottom: 1rem;
        flex-grow: 1; /* Mendorong tanggal/tombol ke bawah */
    }

    .artikel-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto; /* Tempel di bawah */
        border-top: 1px solid #f0f0f0;
        padding-top: 1rem;
    }

    .artikel-card-date {
        font-size: 0.85rem;
        color: #999;
        margin-bottom: 0;
    }

    .btn-artikel {
        font-weight: 600;
        font-size: 0.85rem;
        color: #ffffff;
        background-color: #0d6efd;
        border: none;
        border-radius: 50px;
        padding: 0.4rem 1rem;
        text-decoration: none;
        transition: background-color 0.2s ease;
        cursor: pointer;
    }
    .btn-artikel:hover {                                                
        background-color: #0b5ed7;                 
        color: #ffffff;                                                              
    }
    
    /* GRID SYSTEM */
    .artikel-list-wrapper {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    @media (min-width: 768px) {
        .artikel-list-wrapper {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (min-width: 992px) {
        .artikel-list-wrapper {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* STYLE MODAL DETAIL */
    #modalArtikelImg {
        width: 100%;
        aspect-ratio: 16 / 9;  /* <-- INI KUNCINYA: Paksa rasio landscape */
        object-fit: cover;     /* Agar gambar terpotong rapi (tidak gepeng) */
        border-radius: 12px;
        margin-bottom: 1.5rem;
        background-color: #f8f9fa; /* Placeholder warna loading */
    }

    /* Style untuk gambar-gambar lain di dalam isi artikel (Body) */
    #modalArtikelBody img {
        max-width: 100%;
        height: auto; /* Biarkan rasio asli untuk gambar di dalam teks */
        border-radius: 8px;
        margin: 1rem 0;
    }

    .modal-detail-date {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0; /* Reset margin bottom */
    }
    
    .modal-detail-author {
        font-weight: 600;
        color: #0d6efd;
        font-size: 0.9rem;
    }
                                                                         
</style>
@endpush

@section('content')

<div class="py-5 bg-light">
    <div class="container">
        
        {{-- Judul Halaman --}}
        <div class="row mb-4">
            <div class="col-12 text-center text-lg-start">
                <h2 class="donasi-title-heading">Artikel & Berita</h2>
                <p class="text-muted">Wawasan dan informasi terbaru seputar kegiatan dan keislaman.</p>
            </div>
        </div>

        {{-- LIST ARTIKEL --}}
        @if($artikel->isEmpty())
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-journal-text fs-1 mb-3 d-block"></i>
                <h5>Belum ada artikel yang diterbitkan.</h5>
            </div>
        @else
            <div class="artikel-list-wrapper">
                
                @foreach($artikel as $item)
                <div class="card artikel-card">
                    <div class="card-body artikel-card-body">
                        {{-- Foto --}}
                        <img src="{{ $item->foto_url }}" class="artikel-card-img" alt="{{ $item->judul_artikel }}">
                        
                        {{-- Judul --}}
                        <h5 class="artikel-card-title">{{ $item->judul_artikel }}</h5>
                        
                        {{-- Sinopsis (Isi yang sudah dipotong di Controller) --}}
                        <p class="artikel-card-sinopsis">
                            {{ $item->sinopsis }}...
                        </p>
                        
                        {{-- Info Bawah --}}
                        <div class="artikel-info">
                            <span class="artikel-card-date">
                                <i class="bi bi-calendar3 me-1"></i> 
                                {{ \Carbon\Carbon::parse($item->tanggal_terbit_artikel)->translatedFormat('d M Y') }}
                            </span>
                            
                            {{-- Tombol Trigger Modal --}}
                            <button type="button" class="btn-artikel" onclick="showDetailArtikel('{{ $item->id_artikel }}')">
                                Baca Selengkapnya
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>

            {{-- Pagination --}}
            <div class="mt-5 d-flex justify-content-center">
                {{ $artikel->links() }}
            </div>
        @endif

    </div>
</div>

{{-- MODAL DETAIL ARTIKEL --}}
<div class="modal fade" id="modalArtikelPublic" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable"> <div class="modal-content border-0 shadow">
            
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalArtikelTitle">Loading...</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body p-4">
                {{-- Loading Spinner --}}
                <div id="modalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                {{-- Konten Artikel --}}
                <div id="modalContent" class="d-none">
                    {{-- Foto Detail --}}
                    <img id="modalArtikelImg" src="" class="w-100 rounded mb-3 shadow-sm" alt="Detail Foto">
                    
                    {{-- Meta Info --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                        <span class="modal-detail-date" id="modalArtikelDate"></span>
                        <span class="modal-detail-author" id="modalArtikelAuthor"></span>
                    </div>

                    {{-- Isi Full --}}
                    <div id="modalArtikelBody" class="typography">
                        </div>
                </div>
            </div>
            
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Fungsi untuk memanggil detail artikel via AJAX
    function showDetailArtikel(id) {
        // 1. Buka Modal
        const modalElement = document.getElementById('modalArtikelPublic');
        const modal = new bootstrap.Modal(modalElement);
        modal.show();

        // 2. Reset Tampilan (Tampilkan Loading, Sembunyikan Konten)
        document.getElementById('modalLoading').classList.remove('d-none');
        document.getElementById('modalContent').classList.add('d-none');
        document.getElementById('modalArtikelTitle').innerText = 'Memuat...';

        // 3. Fetch Data dari Controller
        fetch(`/artikel/detail/${id}`)
            .then(response => {
                if (!response.ok) throw new Error('Gagal memuat data');
                return response.json();
            })
            .then(data => {
                // 4. Isi Data ke Modal
                document.getElementById('modalArtikelTitle').innerText = data.judul_artikel;
                // SESUAI MODEL ANDA SEKARANG
                document.getElementById('modalArtikelImg').src = data.foto_url;
                document.getElementById('modalArtikelDate').innerHTML = '<i class="bi bi-calendar-event me-2"></i>' + data.formatted_date;
                document.getElementById('modalArtikelAuthor').innerHTML = '<i class="bi bi-person-circle me-2"></i>' + data.penulis_artikel;
                document.getElementById('modalArtikelBody').innerHTML = data.isi_artikel;

                // 5. Tampilkan Konten
                document.getElementById('modalLoading').classList.add('d-none');
                document.getElementById('modalContent').classList.remove('d-none');
            })
            .catch(error => {
                console.error(error);
                document.getElementById('modalArtikelTitle').innerText = 'Terjadi Kesalahan';
                document.getElementById('modalLoading').classList.add('d-none');
            });
    }
</script>
@endpush