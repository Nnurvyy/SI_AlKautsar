@extends('layouts.public')

@section('title', 'Beranda Masjid Al-Kautsar 561')

@push('styles')
    {{-- HANYA CSS yang dibutuhkan oleh landing page --}}
    <style>
        /* ================================== */
        /* 1. MOBILE FIRST STYLES (< 992px)    */
        /* ================================== */
        .hero-top-nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1rem;
            z-index: 10;
        }
        .hero-top-nav .navbar-brand {
            color: white;
            font-weight: 600;
        }
        .hero-top-nav .profile-icon {
            font-size: 1.8rem;
            color: white;
        }
        .hero-section {
            position: relative;
            height: 45vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            overflow: hidden;
        }
        #hero-bg-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            transition: filter 0.3s ease-in-out;
        }
        #hero-bg-image.blurred {
            filter: blur(4px);
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.2));
            z-index: 1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .hero-content h1 {
            font-weight: 700;
            font-size: 2.2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        }
        .hero-content p {
            font-size: 1rem;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
        }
        .about-card-container {
            margin-top: -50px;
            position: relative;
            z-index: 5;
        }
        .about-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding-top: 1.5rem;
            padding-bottom: 1.5rem;
            padding-left: 1rem;
            padding-right: 1rem;
            background-image: url('{{ asset('images/bgpattern1.jpeg') }}');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        .about-card-icon {
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            background-color: #f1f3f5;
            border-radius: 50%;
            display: inline-flex;
            font-size: 1.25rem;
            color: #495057;
            margin-top: -30px;
            margin-bottom: 0.75rem;
            border: 3px solid white;
            position: relative;
            z-index: 2;
        }
        .about-card h5 {
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #333;
        }
        .about-card p {
            font-size: 0.8rem;
            color: #333;
            margin-bottom: 0;
            line-height: 1.5;
        }
        .section-title {
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 1.25rem;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }
        .feature-item {
            padding: 0.75rem 0.5rem;
            border-radius: 12px;
            text-align: center;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            font-size: 0.8rem;
            background: linear-gradient(135deg, var(--start-color), var(--end-color));
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .feature-icon-img {
            width: 45px;
            height: 45px;
            object-fit: contain;
            margin-bottom: 0.5rem;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .feature-item:hover {
            transform: translateY(-5px);
        }
        .feature-item.bg-pink {
            --start-color: #fce4ec;
            --end-color: #f8bbd0;
            color: #e91e63;
        }
        .feature-item.bg-purple {
            --start-color: #ede7f6;
            --end-color: #d1c4e9;
            color: #9c27b0;
        }
        .feature-item.bg-orange {
            --start-color: #fff3e0;
            --end-color: #ffe0b2;
            color: #ff9800;
        }
        .feature-item.bg-blue {
            --start-color: #e3f2fd;
            --end-color: #bbdefb;
            color: #2196f3;
        }
        .feature-item.bg-green {
            --start-color: #e8f5e9;
            --end-color: #c8e6c9;
            color: #4caf50;
        }
        .feature-item.bg-teal {
            --start-color: #e0f2f1;
            --end-color: #b2dfdb;
            color: #009688;
        }
        .feature-section-bg {
            background-color: #dee2e6;
        }
        .prayer-times-in-card {
            display: flex;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            gap: 0.75rem;
            padding-top: 0.75rem;
        }
        .prayer-times-in-card::-webkit-scrollbar {
            display: none;
        }
        .prayer-times-in-card .prayer-card {
            flex: 0 0 auto;
            width: 75px;
            text-align: center;
            background: linear-gradient(135deg, #ffffff, #f1f3f5);
            border-radius: 12px;
            padding: 0.75rem 0.5rem;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.08);
            color: #333;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .prayer-times-in-card .prayer-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.12);
        }
        .prayer-times-in-card .prayer-card .time {
            font-weight: 700;
            font-size: 0.9rem;
            color: #000;
        }
        .prayer-times-in-card .prayer-card .name {
            font-size: 0.7rem;
            color: #333;
            font-weight: 500;
            line-height: 1.2;
        }
        .prayer-times-in-card .prayer-card.active {
            background: linear-gradient(135deg, #34c759, #28a745);
            color: white;
            border: none;
        }
        .prayer-times-in-card .prayer-card.active .time,
        .prayer-times-in-card .prayer-card.active .name {
            color: white;
        }

        /* */
        
        /* ================================== */
        /* 2. DESKTOP STYLES (>= 992px)        */
        /* ================================== */
        @media (min-width: 992px) {
            .hero-top-nav {
                display: none;
            }
            .hero-section {
                height: 60vh;
                border-radius: 0 0 24px 24px;
            }
            .hero-content h1 {
                font-size: 3.5rem;
            }
            .about-card-container {
                display: none;
            }
            .feature-grid {
                grid-template-columns: repeat(6, 1fr);
            }
            .prayer-times-desktop {
                background: white;
                border-radius: 16px;
                padding: 1.5rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }
            .prayer-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.8rem 0;
                border-bottom: 1px dashed #eee;
                font-size: 1rem;
            }
            .prayer-item:last-child {
                border-bottom: none;
            }
            .prayer-item .name {
                font-weight: 600;
            }
            .prayer-item .time {
                font-weight: 600;
            }
            .prayer-item.active {
                color: #34c759;
            }
        }
    </style>
@endpush

@section('content')

    <nav class="hero-top-nav d-lg-none">
        <div class="d-flex justify-content-between align-items-center">
            <span class="navbar-brand">Smart Masjid</span>
            @auth
                @if (Auth::user()->role == 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="profile-icon">
                        <i class="bi bi-speedometer2"></i>
                    </a>
                @else
                    <a href="#" class="profile-icon">
                        <i class="bi bi-person-circle"></i>
                    </a>
                @endif
            @else
                <a href="{{ route('auth.welcome') }}" class="profile-icon">
                    <i class="bi bi-box-arrow-in-right"></i>
                </a>
            @endauth
        </div>
    </nav>

    <section class="hero-section" id="hero-section">
        <img src="{{ asset('images/masjid.jpeg') }}" id="hero-bg-image" alt="Hero Background">
        <div class="hero-content">
            <h1>Masjid Al-Jabbar</h1>
            <p>Bandung, Jawa Barat</p>
        </div>
    </section>

    <div>

        <div class="container about-card-container d-lg-none">
            <div class="about-card">
                <div>
                    <h5>Jadwal Adzan</h5>
                    <p class="mb-2" style="font-size: 0.75rem; color: #555; margin-bottom: 0 !important;">
                        (Tasikmalaya)
                    </p>
                    <div class="prayer-times-in-card" id="prayerTimesContainerMobile">
                        <div class="prayer-card">
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
                </div>
            </div>
        </div>

        {{-- <div class="container about-card-container d-lg-none mt-3"> ... (Kartu Tentang Kami) ... </div> --}}
        {{-- <div class="container about-card-container d-lg-none mt-3"> ... (Kartu Smart Masjid) ... </div> --}}

        <div class="feature-section-bg mt-4 py-4">
            <div class="container px-4">
                <h4 class="section-title text-center mb-4">Fitur-Fitur</h4>
                <div class="feature-grid">
                    
                    {{-- (DIPERBARUI) Link-link fitur --}}
                    <a href="{{ route('public.jadwal-kajian') }}" class="feature-item bg-pink">
                        <img src="{{ asset('images/icons/kajian.png') }}" alt="Kajian Icon" class="feature-icon-img">
                        <span>Kajian</span>
                    </a>
                    <a href="{{ route('public.jadwal-khotib') }}" class="feature-item bg-purple">
                        <img src="{{ asset('images/icons/khutbah-jumat.png') }}" alt="Khutbah Jumat Icon" class="feature-icon-img">
                        <span>Khutbah Jumat</span>
                    </a>
                    <a href="{{ route('public.artikel') }}" class="feature-item bg-orange">
                        <img src="{{ asset('images/icons/artikel.png') }}" alt="Artikel Icon" class="feature-icon-img">
                        <span>Artikel</span>
                    </a>
                    <a href="{{ route('public.donasi') }}" class="feature-item bg-blue">
                        <img src="{{ asset('images/icons/donasi.png') }}" alt="Donasi Icon" class="feature-icon-img">
                        <span>Donasi</span>
                    </a>
                    <a href="{{ route('public.program') }}" class="feature-item bg-green">
                        <img src="{{ asset('images/icons/program.png') }}" alt="Program Icon" class="feature-icon-img">
                        <span>Program</span>
                    </a>
                    <a href="#" class="feature-item bg-teal">
                        <img src="{{ asset('images/icons/qurban.png') }}" alt="Tabungan Qurban Icon" class="feature-icon-img">
                        <span>Tabungan Qurban</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    {{-- (PENTING) Form logout juga harus ada di dalam @section('content') --}}
    @auth
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    @endauth
@endsection

@push('scripts')
    {{-- HANYA SCRIPT yang dibutuhkan oleh landing page --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const KOTA_ID = '1218';
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const tanggal = `${yyyy}-${mm}-${dd}`;
            const API_URL = `https://api.myquran.com/v2/sholat/jadwal/${KOTA_ID}/${tanggal}`;

            const createPrayerDate = (timeStr) => {
                const [hours, minutes] = timeStr.split(':');
                const date = new Date(tanggal);
                date.setHours(parseInt(hours, 10));
                date.setMinutes(parseInt(minutes, 10));
                date.setSeconds(0);
                return date;
            };

            fetch(API_URL)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil data');
                    return response.json();
                })
                .then(data => {
                    if (data.status && data.data && data.data.jadwal) {
                        const jadwal = data.data.jadwal;
                        const now = new Date(); 

                        const prayerTimes = [
                            { name: 'subuh', time: jadwal.subuh, id: 'time-subuh-m' },
                            { name: 'dzuhur', time: jadwal.dzuhur, id: 'time-dzuhur-m' },
                            { name: 'ashar', time: jadwal.ashar, id: 'time-ashar-m' },
                            { name: 'maghrib', time: jadwal.maghrib, id: 'time-maghrib-m' },
                            { name: 'isya', time: jadwal.isya, id: 'time-isya-m' }
                        ];

                        let nextPrayerElement = null;

                        prayerTimes.forEach(prayer => {
                            document.getElementById(prayer.id).textContent = prayer.time;
                        });

                        for (const prayer of prayerTimes) {
                            const prayerDate = createPrayerDate(prayer.time);
                            if (prayerDate > now) { 
                                nextPrayerElement = document.getElementById(prayer.id).closest('.prayer-card');
                                break; 
                            }
                        }
                        
                        if (!nextPrayerElement) {
                            nextPrayerElement = document.getElementById('time-subuh-m').closest('.prayer-card');
                        }

                        document.querySelectorAll('.prayer-times-in-card .prayer-card').forEach(card => {
                            card.classList.remove('active');
                        });

                        if (nextPrayerElement) {
                            nextPrayerElement.classList.add('active');
                        }
                    } else {
                        throw new Error('Data API tidak valid');
                    }
                })
                .catch(err => {
                    console.error('Gagal memuat jadwal shalat:', err);
                    document.querySelectorAll('[id^="time-"]').forEach(el => {
                        el.textContent = 'Error';
                    });
                });
            
            // SCRIPT INISIALISASI SWIPER TELAH DIHAPUS DARI SINI

            const scrollTopBtn = document.querySelector('.scroll-to-top');

            if (scrollTopBtn) {
                window.addEventListener('scroll', () => {
                    if (window.scrollY > 300) { // Muncul setelah scroll 300px
                        scrollTopBtn.classList.add('show');
                    } else {
                        scrollTopBtn.classList.remove('show');
                    }
                });

                // (Opsional) Efek smooth scroll saat diklik
                scrollTopBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            }

            const heroImage = document.getElementById('hero-bg-image');
            const heroSection = document.getElementById('hero-section');
            if (heroImage && heroSection) {
                const heroHeight = heroSection.offsetHeight;
                const scrollThreshold = heroHeight * 0.1;
                window.addEventListener('scroll', function() {
                    if (window.scrollY > scrollThreshold) {
                        heroImage.classList.add('blurred');
                    } else {
                        heroImage.classList.remove('blurred');
                    }
                });
            }
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
        </script>
    @endauth
@endpush