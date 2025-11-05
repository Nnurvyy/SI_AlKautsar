@extends('layouts.public')

@section('title', 'Beranda Masjid Al-Kautsar 561')

@push('styles')
<style>
    /* ====================================================== */
    /* 1. STYLE MOBILE (DEFAULT)                              */
    /* ====================================================== */
    body {
        background-color: #ffffff; 
    }
    .header-card {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        border-radius: 0 0 24px 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .location-badge {
        background-color: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(5px);
        font-weight: 500;
    }
    .profile-icon {
        font-size: 1.8rem;
        color: white;
        text-decoration: none;
    }

    /* Jadwal Shalat (Mobile - Horizontal Scroll) */
    .prayer-times-mobile {
        display: flex;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding: 1rem 0;
        gap: 0.75rem;
    }
    .prayer-times-mobile::-webkit-scrollbar { display: none; }
    .prayer-card {
        flex: 0 0 auto;
        width: 80px;
        text-align: center;
        background: white;
        border-radius: 12px;
        padding: 0.75rem 0.5rem;
        border: 1px solid #eee;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .prayer-card .time { font-weight: 600; font-size: 0.9rem; }
    .prayer-card .name { font-size: 0.75rem; color: #6c757d; }
    .prayer-card.active {
        background-color: #198754; color: white;
    }
    .prayer-card.active .name { color: #f0f0f0; }

    /* Menu Grid (Mobile) */
    .menu-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    .menu-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #333;
    }
    .menu-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        transition: transform 0.2s ease;
    }
    .menu-item span { font-size: 0.8rem; font-weight: 500; text-align: center; }
    
    .bg-blue-light { background-color: #e7f0ff; color: #0d6efd; }
    .bg-green-light { background-color: #e8f5e9; color: #198754; }
    .bg-orange-light { background-color: #fff4e6; color: #fd7e14; }
    .bg-red-light { background-color: #ffebee; color: #dc3545; }
    .bg-gray-light { background-color: #f1f3f5; color: #6c757d; }

    /* Card "Jumat Ini" & "Kajian" (Mobile) */
    .info-card { 
        background: white;
        border-radius: 16px;
        padding: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #eee;
    }
    .info-avatar { 
        width: 60px;
        height: 60px;
        border-radius: 12px;
        object-fit: cover;
    }
    .kajian-card-mobile {
        display: flex;
        gap: 1rem;
        align-items: center;
    }
    .kajian-avatar-mobile {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* ====================================================== */
    /* 2. STYLE DESKTOP BARU (LG 992px+)                      */
    /* ====================================================== */
    @media (min-width: 992px) {
        body {
            background-color: #f8f9fa; /* Latar belakang abu-abu */
        }

        /* ====================================================== */
        /* PERUBAHAN CSS DI SINI                                  */
        /* ====================================================== */
        .header-card {
            /* DIUBAH: Samakan radius-nya dengan mobile */
            border-radius: 0 0 24px 24px;
            /* DIUBAH: Hapus sticky, biarkan statis di atas */
            position: relative; 
            top: auto;
            z-index: auto;
        }
        .header-card .container {
            max-width: 1140px;
            margin: 0 auto;
        }
        
        /* Wrapper untuk layout desktop 2 kolom */
        .desktop-layout-wrapper {
            max-width: 1140px;
            margin: 0 auto;
            /* Jarak dari header */
            padding-top: 2.5rem; 
            padding-bottom: 2.5rem;
        }

        /* Style untuk SEMUA card di desktop */
        .desktop-layout-wrapper .card {
            /* Diberi border-radius (sudah benar) */
            border-radius: 16px; 
            border: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        /* ... (Sisa style desktop tidak berubah) ... */

        /* Tata letak Menu Grid di Desktop */
        .menu-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }
        .menu-icon {
            width: 65px;
            height: 65px;
            font-size: 2rem;
        }
        .menu-item span {
            font-size: 0.9rem;
        }
        .menu-item:hover .menu-icon {
            transform: translateY(-5px);
        }

        /* Jadwal Shalat (Sidebar Desktop) */
        .prayer-times-desktop .prayer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 0;
            border-bottom: 1px dashed #eee;
        }
        .prayer-times-desktop .prayer-item:last-child {
            border-bottom: 0;
        }
        .prayer-times-desktop .prayer-item .name {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .prayer-times-desktop .prayer-item .time {
            font-weight: 600;
            font-size: 0.95rem;
        }
        .prayer-times-desktop .prayer-item.active {
            color: #198754;
            font-weight: 700;
        }
        .prayer-times-desktop .prayer-item.active .time {
            font-size: 1rem;
        }

        /* Card Khotib Desktop */
        .info-avatar {
            width: 70px;
            height: 70px;
        }

        /* Card Infaq (Sidebar) */
        .infaq-card .infaq-item {
            display: flex;
            justify-content: space-between;
            font-size: 0.95rem;
            padding: 0.5rem 0;
        }
        .infaq-card .infaq-total {
            border-top: 1px solid #eee;
            padding-top: 1rem;
            margin-top: 0.5rem;
        }
        .infaq-card .infaq-total .amount {
            font-size: 1.75rem;
            font-weight: 700;
            color: #198754;
            line-height: 1;
        }
        
        /* Card Kajian (Main Column) */
        .kajian-card-desktop {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        .kajian-avatar-desktop {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    }
</style>
@endpush

@section('content')
<div class="header-card">
    <div class="container p-4"> <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                @auth
                    <h5 class="fw-bold mb-0">Selamat Datang, {{ Auth::user()->nama }}</h5>
                    <span class="fw-light">di Masjid Al-Kautsar 561</span>
                @else
                    <h5 class="fw-bold mb-0">Selamat Datang</h5>
                    <span class="fw-light">di Masjid Al-Kautsar 561</span>
                @endauth
            </div>
            
            @auth
                @if(Auth::user()->role == 'publik')
                    <a href="{{ route('public.qurban') }}" class="profile-icon">
                        <i class="bi bi-person-circle"></i>
                    </a>
                @elseif(Auth::user()->role == 'admin')
                     <a href="{{ route('admin.dashboard') }}" class="profile-icon">
                        <i class="bi bi-speedometer2"></i>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="profile-icon">
                    <i class="bi bi-box-arrow-in-right"></i>
                </a>
            @endguest
        </div>
        <span class="badge rounded-pill location-badge">
            <i class="bi bi-geo-alt-fill"></i> Tasikmalaya
        </span>
    </div>
</div>


<div class="p-3 d-lg-none">
    <h6 class="fw-bold mb-2">Jadwal Shalat (Tasikmalaya)</h6>
    
    <div class="prayer-times-mobile" id="prayerTimesContainerMobile">
        <div class="prayer-card active">
            <div class="time" id="time-subuh-m">...</div>
            <div class="name">Subuh</div>
        </div>
        <div class="prayer-card">
            <div class="time" id="time-dzuhur-m">...</div>
            <div class="name">Dzuhur</div>
        </div>
        <div class="prayer-card">
            <div class="time" id="time-ashar-m">...</div>
            <div class="name">Ashar</div>
        </div>
        <div class="prayer-card">
            <div class="time" id="time-maghrib-m">...</div>
            <div class="name">Maghrib</div>
        </div>
        <div class="prayer-card">
            <div class="time" id="time-isya-m">...</div>
            <div class="name">Isya</div>
        </div>
    </div>

    <h6 class="fw-bold mt-4 mb-3">Menu Utama</h6>
    <div class="menu-grid">
        <a href="{{ route('public.jadwal-kajian') }}" class="menu-item">
            <div class="menu-icon bg-blue-light"><i class="bi bi-calendar-event"></i></div>
            <span>Jadwal Kajian</span>
        </a>
        <a href="{{ route('public.jadwal-khotib') }}" class="menu-item">
            <div class="menu-icon bg-green-light"><i class="bi bi-mic-fill"></i></div>
            <span>Khutbah Jumat</span>
        </a>
        
        @auth
            @if(Auth::user()->role == 'publik')
            <a href="{{ route('public.qurban') }}" class="menu-item">
                <div class="menu-icon bg-orange-light"><i class="bi bi-box-seam-fill"></i></div>
                <span>Qurban Saya</span>
            </a>
            @endif

            <a id="logoutButtonMobile" class="menu-item" style="cursor: pointer;">
                <div class="menu-icon bg-gray-light"><i class="bi bi-box-arrow-right"></i></div>
                <span>Logout</span>
            </a>
        @else
            <a href="{{ route('public.qurban') }}" class="menu-item">
                <div class="menu-icon bg-orange-light"><i class="bi bi-box-seam-fill"></i></div>
                <span>Qurban Saya</span>
            </a>
            <a href="#" class="menu-item">
                <div class="menu-icon bg-red-light"><i class="bi bi-cash-coin"></i></div>
                <span>Infaq Masjid</span>
            </a>
        @endguest
    </div>

    <h6 class="fw-bold mt-4 mb-3">Khutbah Jumat Ini</h6>
    @if($khotibJumatIni)
    <div class="info-card d-flex align-items-center gap-3">
        <img src="{{ $khotibJumatIni->foto_url }}" class="info-avatar" alt="Foto Khotib">
        <div>
            <h6 class="fw-bold mb-0">{{ $khotibJumatIni->nama_khotib }}</h6>
            <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                Tema: "{{ $khotibJumatIni->tema_khutbah }}"
            </p>
        </div>
    </div>
    @else
    <div class="info-card text-center text-muted">
        Jadwal Jumat ini belum tersedia.
    </div>
    @endif

    <h6 class="fw-bold mt-4 mb-3">Kajian Akan Datang</h6>
    <div class="info-card p-3">
        <div class="list-group list-group-flush">
            <a href="#" class="list-group-item list-group-item-action p-0 pb-3 mb-3 border-bottom kajian-card-mobile">
                <img src="https://via.placeholder.com/100" class="kajian-avatar-mobile" alt="Penceramah">
                <div>
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Kajian Subuh: Tafsir Al-Fatihah</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">Ustadz Adi Hidayat - Besok, 05:00</p>
                </div>
            </a>
            <a href="#" class="list-group-item list-group-item-action p-0 kajian-card-mobile">
                <img src="https://via.placeholder.com/100" class="kajian-avatar-mobile" alt="Penceramah">
                <div>
                    <h6 class="fw-bold mb-0" style="font-size: 0.9rem;">Kajian Maghrib: Fiqih Muamalah</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.8rem;">Ustadz Abdul Somad - 03 Nov, 18:30</p>
                </div>
            </a>
        </div>
    </div>
    
</div>


<div class="container-lg d-none d-lg-block desktop-layout-wrapper">
    <div class="row g-4">
        
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Menu Utama</h5>
                    <div class="menu-grid">
                        <a href="{{ route('public.jadwal-kajian') }}" class="menu-item">
                            <div class="menu-icon bg-blue-light"><i class="bi bi-calendar-event"></i></div>
                            <span>Jadwal Kajian</span>
                        </a>
                        <a href="{{ route('public.jadwal-khotib') }}" class="menu-item">
                            <div class="menu-icon bg-green-light"><i class="bi bi-mic-fill"></i></div>
                            <span>Khutbah Jumat</span>
                        </a>
                        
                        @auth
                            @if(Auth::user()->role == 'publik')
                            <a href="{{ route('public.qurban') }}" class="menu-item">
                                <div class="menu-icon bg-orange-light"><i class="bi bi-box-seam-fill"></i></div>
                                <span>Qurban Saya</span>
                            </a>
                            @endif
                            <a id="logoutButtonDesktop" class="menu-item" style="cursor: pointer;">
                                <div class="menu-icon bg-gray-light"><i class="bi bi-box-arrow-right"></i></div>
                                <span>Logout</span>
                            </a>
                        @else
                            <a href="{{ route('public.qurban') }}" class="menu-item">
                                <div class="menu-icon bg-orange-light"><i class="bi bi-box-seam-fill"></i></div>
                                <span>Qurban Saya</span>
                            </a>
                            <a href="#" class="menu-item">
                                <div class="menu-icon bg-red-light"><i class="bi bi-cash-coin"></i></div>
                                <span>Infaq Masjid</span>
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

            <h5 class="fw-bold mb-3">Khutbah Jumat Ini</h5>
            @if($khotibJumatIni)
            <div class="info-card d-flex align-items-center gap-3 mb-4">
                <img src="{{ $khotibJumatIni->foto_url }}" class="info-avatar" alt="Foto Khotib">
                <div>
                    <h6 class="fw-bold mb-0">{{ $khotibJumatIni->nama_khotib }}</h6>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem;">
                        Tema: "{{ $khotibJumatIni->tema_khutbah }}"
                    </p>
                </div>
            </div>
            @else
            <div class="info-card text-center text-muted mb-4 p-4">
                Jadwal Jumat ini belum tersedia.
            </div>
            @endif

            <h5 class="fw-bold mb-3">Kajian Akan Datang</h5>
            <div class="card">
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action kajian-card-desktop">
                            <img src="httpsG://via.placeholder.com/100" class="kajian-avatar-desktop" alt="Penceramah">
                            <div>
                                <h6 class="fw-bold mb-0">Kajian Subuh: Tafsir Al-Fatihah</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.9rem;">Ustadz Adi Hidayat - Besok, 05:00</p>
                            </div>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action kajian-card-desktop">
                            <img src="https://via.placeholder.com/100" class="kajian-avatar-desktop" alt="Penceramah">
                            <div>
                                <h6 class="fw-bold mb-0">Kajian Maghrib: Fiqih Muamalah</h6>
                                <p class="mb-0 text-muted" style="font-size: 0.9rem;">Ustadz Abdul Somad - 03 Nov, 18:30</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-2">Jadwal Shalat (Tasikmalaya)</h5>
                    <div class="prayer-times-desktop" id="prayerTimesContainerDesktop">
                        <div class="prayer-item active">
                            <span class="name">Subuh</span>
                            <span class="time" id="time-subuh-d">...</span>
                        </div>
                        <div class="prayer-item">
                            <span class="name">Dzuhur</span>
                            <span class="time" id="time-dzuhur-d">...</span>
                        </div>
                        <div class="prayer-item">
                            <span class="name">Ashar</span>
                            <span class="time" id="time-ashar-d">...</span>
                        </div>
                        <div class="prayer-item">
                            <span class="name">Maghrib</span>
                            <span class="time" id="time-maghrib-d">...</span>
                        </div>
                        <div class="prayer-item">
                            <span class="name">Isya</span>
                            <span class="time" id="time-isya-d">...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Informasi Infaq Jumat ({{ \Carbon\Carbon::now()->translatedFormat('F Y') }})</h5>
                    <div class="infaq-card">
                        <div class="infaq-item">
                            <span>Total Pemasukan Infaq</span>
                            <span class="fw-medium text-success">Rp 15.000.000</span>
                        </div>
                        <div class="infaq-item">
                            <span>Total Pengeluaran Infaq</span>
                            <span class="fw-medium text-danger">Rp 4.500.000</span>
                        </div>
                        <div class="infaq-total text-center">
                            <small class="text-muted">SALDO INFAQ SAAT INI</small>
                            <div class="amount">Rp 45.750.000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@auth
    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endauth
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ID KOTA TASIKMALAYA
    const KOTA_ID = '1218'; // KAB. TASIKMALAYA (sesuai data MyQuran)

    // Format tanggal: YYYY-MM-DD
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const tanggal = `${yyyy}-${mm}-${dd}`;

    // API baru MyQuran
    const API_URL = `https://api.myquran.com/v2/sholat/jadwal/${KOTA_ID}/${tanggal}`;

    fetch(API_URL)
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengambil data');
            return response.json();
        })
        .then(data => {
            if (data.status && data.data && data.data.jadwal) {
                const jadwal = data.data.jadwal;

                // Mobile
                document.getElementById('time-subuh-m').textContent = jadwal.subuh;
                document.getElementById('time-dzuhur-m').textContent = jadwal.dzuhur;
                document.getElementById('time-ashar-m').textContent = jadwal.ashar;
                document.getElementById('time-maghrib-m').textContent = jadwal.maghrib;
                document.getElementById('time-isya-m').textContent = jadwal.isya;

                // Desktop
                document.getElementById('time-subuh-d').textContent = jadwal.subuh;
                document.getElementById('time-dzuhur-d').textContent = jadwal.dzuhur;
                document.getElementById('time-ashar-d').textContent = jadwal.ashar;
                document.getElementById('time-maghrib-d').textContent = jadwal.maghrib;
                document.getElementById('time-isya-d').textContent = jadwal.isya;
            } else {
                throw new Error('Data API tidak valid');
            }
        })
        .catch(err => {
            console.error('Gagal memuat jadwal shalat:', err);
            // Tampilkan error di semua elemen waktu
            document.querySelectorAll('[id^="time-"]').forEach(el => {
                el.textContent = 'Error';
            });
        });
});
</script>


@auth
<script>
    function handleLogout(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logoutForm').submit();
            }
        });
    }

    const logoutButtonMobile = document.getElementById('logoutButtonMobile');
    const logoutButtonDesktop = document.getElementById('logoutButtonDesktop');

    if (logoutButtonMobile) {
        logoutButtonMobile.addEventListener('click', handleLogout);
    }
    if (logoutButtonDesktop) {
        logoutButtonDesktop.addEventListener('click', handleLogout);
    }
</script>
@endauth
@endpush