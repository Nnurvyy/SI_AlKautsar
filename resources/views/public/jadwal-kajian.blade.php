@extends('layouts.public')

@section('title', 'Jadwal Kajian')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<style>
    .donasi-title-heading { font-family: 'Poppins', sans-serif; font-weight: 700; font-size: 1.8rem; color: #333; margin-bottom: 0.5rem; }
    .donasi-title-sub { font-size: 1rem; color: #6c757d; margin-bottom: 1rem; }
    
    /* Style Card Event Desktop */
    .kajian-event-card { width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border: none; background-color: #ffffff; }
    .kajian-event-img { width: 100%; object-fit: cover; aspect-ratio: 16/9; }
    .kajian-event-content { padding: 1rem; }
    .kajian-event-content h6 { font-weight: 700; font-size: 1.25rem; color: #212529; margin-bottom: 0.25rem; }
    .kajian-event-content p { font-size: 1.0rem; color: #495057; margin-bottom: 0.5rem; line-height: 1.4; }
    .tanggal-badge { font-size: 0.9rem; font-weight: 600; padding: 0.5em 0.75em; background-color: #343a40; color: #ffffff; border-radius: 8px; display: inline-block; margin-top: 0.5rem; }

    /* Style Card List (Rutin & Event di Tab) */
    .kajian-list-card-new { border: none; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); margin-bottom: 1rem; overflow: hidden; background: linear-gradient(to bottom right, #ffffff, #f1f3f5); display: flex; flex-direction: row; }
    .kajian-list-card-new .card-img { object-fit: cover; width: 110px; height: 110px; border-radius: 12px 0 0 12px !important; }
    .kajian-list-card-new .card-body { padding: 0.75rem 1rem; display: flex; flex-direction: column; justify-content: space-between; flex: 1; min-height: 110px; }
    .kajian-list-card-new .card-title { font-size: 0.95rem; font-weight: 700; color: #212529; line-height: 1.3; }
    .kajian-list-card-new .card-text-tema { font-size: 0.85rem; color: #495057; }

    /* CSS Khusus Tab Mobile */
    .mobile-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 5px;
    }
    .mobile-tab-btn {
        border: 1px solid #e9ecef;
        background-color: white;
        color: #6c757d;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    .mobile-tab-btn.active {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }

    /* Desktop Media Query */
    @media (min-width: 992px) {
        .kajian-event-swiper { height: 100%; }
        .swiper-slide { height: auto; }
        .kajian-event-card { display: flex; height: 100%; }
        .kajian-event-img { aspect-ratio: 4 / 5; width: 100%; height: 100%; object-fit: cover; border-radius: 16px 0 0 16px !important; }
        .kajian-event-content { display: flex; flex-direction: column; justify-content: center; padding: 2rem; height: 100%; width: 100%; }
        
        .kajian-list-card-new { background: #f1f3f5; box-shadow: none; border-radius: 20px; padding: 0.75rem; display: flex; flex-direction: row; }
        .kajian-list-card-new .card-img { width: 110px; height: 110px; border-radius: 12px !important; object-fit: cover; min-height: auto; }
        .kajian-list-card-new .card-body { height: auto; min-height: 0; padding: 0.5rem 0 0.5rem 1rem; justify-content: space-between; flex: 1; }
        
        .pagination { margin-bottom: 0; }
        .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; }
        .page-link { color: #333; }
    }
</style>
@endpush

@section('content')

<div class="container"> 

    {{-- ============================================================ --}}
    {{-- DESKTOP LAYOUT (Tidak Diubah)                                --}}
    {{-- ============================================================ --}}
    <div class="d-none d-lg-block" style="padding-top: 2rem; padding-bottom: 2rem;">
        <div class="mb-4">
            <h1 class="donasi-title-heading" style="font-size: 2.2rem; margin-bottom: 0;">Kajian-Kajian</h1>
            <p class="donasi-title-sub">Ikuti kajian event dan rutin terbaru kami.</p>
        </div>

        <div class="row">
            {{-- KIRI: KAJIAN EVENT --}}
            <div class="col-lg-7">
                @if($kajianEvent->isEmpty())
                    <div class="alert alert-info text-center py-5">
                        <i class="bi bi-info-circle fs-1 mb-3 d-block"></i>
                        <h5>Belum ada jadwal kajian event.</h5>
                    </div>
                @else
                    <div class="swiper kajian-event-swiper">
                        <div class="swiper-wrapper">
                            @foreach($kajianEvent as $kajian)
                            <div class="swiper-slide">
                                <div class="kajian-event-card">
                                    <div class="row g-0 h-100 w-100">
                                        <div class="col-md-5">
                                            <img src="{{ $kajian->foto_url }}" alt="{{ $kajian->tema_kajian }}" class="kajian-event-img">
                                        </div>
                                        <div class="col-md-7">
                                            <div class="kajian-event-content">
                                                <h3 class="fw-bold donasi-title-heading text-primary" style="font-size: 1.2rem; letter-spacing: 1px;">KAJIAN EVENT</h3>
                                                <h6 class="fw-bold mt-2" style="font-size: 1.5rem;">{{ $kajian->nama_penceramah }}</h6>
                                                <p class="text-muted mb-3" style="font-size: 1.1rem;">"{{ $kajian->tema_kajian }}"</p>
                                                <div>
                                                    <span class="tanggal-badge">
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->translatedFormat('d F Y') }}
                                                        @if($kajian->waktu_kajian)
                                                            <span class="mx-1">|</span> 
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }} WIB
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($kajianEvent->count() > 1)
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- KANAN: KAJIAN RUTIN --}}
            <div class="col-lg-5">
                <h2 class="donasi-title-heading mb-4" style="font-size: 1.5rem;">Kajian Rutin</h2>
                
                @forelse($kajianRutin as $kajian)
                <div class="card kajian-list-card-new">
                    <img src="{{ $kajian->foto_url }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
                    <div class="card-body">
                        <div>
                            <h5 class="card-title">{{ $kajian->nama_penceramah }}</h5>
                            <p class="card-text-tema">"{{ $kajian->tema_kajian }}"</p>
                        </div>
                        <div>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->translatedFormat('d M Y') }}
                                @if($kajian->waktu_kajian)
                                    • {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4 border rounded bg-light">
                    <small>Belum ada jadwal kajian rutin.</small>
                </div>
                @endforelse

                <div class="d-flex justify-content-end mt-4">
                    {{ $kajianRutin->links() }} 
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MOBILE & TABLET LAYOUT (< 992px)                             --}}
    {{-- ============================================================ --}}
    <div class="d-lg-none">
        
        <div class="pt-4 pb-3">
            <h2 class="donasi-title-heading">Kajian-Kajian</h2>
            <p class="donasi-title-sub">Ikuti kajian event dan rutin terbaru kami.</p>
        </div>

        {{-- TABS MOBILE --}}
        <div class="mobile-tabs">
            <button class="mobile-tab-btn active" onclick="switchTab('semua', this)">Semua</button>
            <button class="mobile-tab-btn" onclick="switchTab('rutin', this)">Rutin</button>
            <button class="mobile-tab-btn" onclick="switchTab('event', this)">Event</button>
        </div>

        {{-- 1. KONTEN TAB: SEMUA (Default) --}}
        <div id="tab-content-semua">
            {{-- Event Slider --}}
            @if($kajianEvent->isNotEmpty())
                <div class="swiper kajian-event-swiper-mobile mb-4">
                    <div class="swiper-wrapper">
                        @foreach($kajianEvent as $kajian)
                        <div class="swiper-slide">
                            <div class="kajian-event-card">
                                <img src="{{ $kajian->foto_url }}" alt="{{ $kajian->tema_kajian }}" class="kajian-event-img">
                                <div class="kajian-event-content">
                                    <span class="badge bg-primary mb-2">Event</span>
                                    <h6 class="fw-bold">{{ $kajian->nama_penceramah }}</h6>
                                    <p class="text-muted small mb-2">"{{ $kajian->tema_kajian }}"</p>
                                    <span class="tanggal-badge" style="font-size: 0.8rem;">
                                        {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->translatedFormat('d M Y') }}
                                        @if($kajian->waktu_kajian)
                                            • {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @if($kajianEvent->count() > 1)
                    <div class="swiper-pagination"></div>
                    @endif
                </div>
            @else
                <div class="alert alert-info text-center py-5 mb-4 border-0 rounded-3 shadow-sm" style="background-color: #d1ecf1; color: #0c5460;">
                    <i class="bi bi-info-circle fs-1 d-block mb-3"></i>
                    <h5 class="fw-bold">Belum ada event</h5>
                    <p class="mb-0 small">Jadwal kajian event belum tersedia saat ini.</p>
                </div>
            @endif

            {{-- Rutin List --}}
            <h2 class="donasi-title-heading mt-4" style="font-size: 1.5rem;">Kajian Rutin</h2>
            
            @forelse($kajianRutin as $kajian)
            <div class="card kajian-list-card-new mb-3">
                <img src="{{ $kajian->foto_url }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
                <div class="card-body">
                    <div>
                        <h5 class="card-title" style="font-size: 1rem;">{{ $kajian->nama_penceramah }}</h5>
                        <p class="card-text-tema small">"{{ $kajian->tema_kajian }}"</p>
                    </div>
                    <div>
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center text-muted py-4 border rounded-3 bg-white shadow-sm mb-3">
                    <small>Tidak ada jadwal kajian rutin.</small>
                </div>
            @endforelse

            <div class="mt-3 pb-5">
                 {{ $kajianRutin->links() }}
            </div>
        </div>

        {{-- 2. KONTEN TAB: RUTIN (List Only) --}}
        <div id="tab-content-rutin" class="d-none">
            <h2 class="donasi-title-heading mb-3" style="font-size: 1.5rem;">Kajian Rutin</h2>
            @forelse($kajianRutin as $kajian)
            <div class="card kajian-list-card-new mb-3">
                <img src="{{ $kajian->foto_url }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
                <div class="card-body">
                    <div>
                        <h5 class="card-title" style="font-size: 1rem;">{{ $kajian->nama_penceramah }}</h5>
                        <p class="card-text-tema small">"{{ $kajian->tema_kajian }}"</p>
                    </div>
                    <div>
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center text-muted py-4 border rounded-3 bg-white shadow-sm mb-3">
                    <small>Tidak ada jadwal kajian rutin.</small>
                </div>
            @endforelse
            
            <div class="mt-3 pb-5">
                 {{ $kajianRutin->links() }}
            </div>
        </div>

        {{-- 3. KONTEN TAB: EVENT (List Style) --}}
        <div id="tab-content-event" class="d-none">
            <h2 class="donasi-title-heading mb-3" style="font-size: 1.5rem;">Kajian Event</h2>
            @forelse($kajianEvent as $kajian)
            <div class="card kajian-list-card-new mb-3">
                <img src="{{ $kajian->foto_url }}" class="card-img" alt="Foto {{ $kajian->nama_penceramah }}">
                <div class="card-body">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title" style="font-size: 1rem;">{{ $kajian->nama_penceramah }}</h5>
                            <span class="badge bg-primary" style="font-size: 0.6rem;">Event</span>
                        </div>
                        <p class="card-text-tema small">"{{ $kajian->tema_kajian }}"</p>
                    </div>
                    <div>
                        <span class="text-muted small">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ \Carbon\Carbon::parse($kajian->tanggal_kajian)->translatedFormat('d M Y') }}
                            @if($kajian->waktu_kajian)
                                • {{ \Carbon\Carbon::parse($kajian->waktu_kajian)->format('H:i') }}
                            @endif
                        </span>
                    </div>
                </div>
            </div>
            @empty
                <div class="text-center text-muted py-4 border rounded-3 bg-white shadow-sm mb-3">
                    <small>Tidak ada jadwal kajian event.</small>
                </div>
            @endforelse
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    function switchTab(tabName, btnElement) {
        // 1. Reset Tombol Aktif
        document.querySelectorAll('.mobile-tab-btn').forEach(btn => btn.classList.remove('active'));
        if(btnElement) btnElement.classList.add('active');

        // 2. Sembunyikan Semua Konten
        document.getElementById('tab-content-semua').classList.add('d-none');
        document.getElementById('tab-content-rutin').classList.add('d-none');
        document.getElementById('tab-content-event').classList.add('d-none');

        // 3. Tampilkan Konten Terpilih
        document.getElementById('tab-content-' + tabName).classList.remove('d-none');
        
        // 4. Simpan state di localStorage agar tidak reset saat pagination (opsional)
        localStorage.setItem('activeKajianTab', tabName);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Logic Tab Persistance (agar saat pagination tab rutin tetap aktif)
        const savedTab = localStorage.getItem('activeKajianTab');
        if(savedTab && document.getElementById('tab-content-' + savedTab)) {
            // Cari tombol yang sesuai
            const btns = document.querySelectorAll('.mobile-tab-btn');
            let activeBtn = btns[0]; // Default 'semua'
            if(savedTab === 'rutin') activeBtn = btns[1];
            if(savedTab === 'event') activeBtn = btns[2];
            
            switchTab(savedTab, activeBtn);
        }

        // Init Swiper Desktop
        if (document.querySelector('.kajian-event-swiper')) {
            new Swiper('.kajian-event-swiper', {
                loop: {{ $kajianEvent->count() > 1 ? 'true' : 'false' }},
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoHeight: false, 
                observer: true,
                observeParents: true,
            });
        }

        // Init Swiper Mobile
        if (document.querySelector('.kajian-event-swiper-mobile')) {
            new Swiper('.kajian-event-swiper-mobile', {
                loop: {{ $kajianEvent->count() > 1 ? 'true' : 'false' }},
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                spaceBetween: 20,
            });
        }
    });
</script>
@endpush