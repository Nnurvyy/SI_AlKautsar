<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFFFFF">
    <title>@yield('title') - Smart Masjid</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        html, body { margin: 0; padding: 0; }

        /* --- Mobile Top Nav (Hero) --- */
        .hero-top-nav {
            position: absolute; top: 0; left: 0; width: 100%; padding: 1rem; z-index: 10;
        }
        .hero-top-nav .navbar-brand { color: white; font-weight: 600; text-shadow: 0 1px 3px rgba(0,0,0,0.5); }
        
        /* Style untuk Foto Profil di Header Mobile */
        .hero-top-nav .profile-trigger {
            color: white; font-size: 1.5rem; text-decoration: none; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
            border: none; background: none; padding: 0; cursor: pointer;
        }
        .hero-top-nav .profile-img-nav {
            width: 36px; height: 36px; 
            border-radius: 50%; 
            object-fit: cover; 
            border: 2px solid white; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        /* Fallback jika tidak ada foto (Huruf Inisial) */
        .hero-top-nav .profile-initial-nav {
            width: 36px; height: 36px; 
            border-radius: 50%; 
            background-color: #198754; 
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 1rem;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* --- Bottom Nav --- */
        .navbar-bottom {
            position: fixed; bottom: 0; left: 0; right: 0; background-color: #ffffff;
            display: flex; justify-content: space-around; align-items: stretch;
            padding-top: 8px; padding-bottom: 4px; box-shadow: 0 -2px 5px rgba(0,0,0,0.08);
            z-index: 1010; border-top: 1px solid #dee2e6;
        }
        .navbar-bottom-item {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            text-decoration: none; color: #8a9a9d; font-size: 0.65rem; font-weight: 500;
            flex-grow: 1; background: none; border: none; padding: 0;
        }
        .navbar-bottom-item img {
            width: 24px; height: 24px; object-fit: contain; margin-bottom: 2px;
        }
        
        /* Khusus Foto Profil di Bottom Nav */
        .navbar-bottom-item .account-img {
            width: 26px; height: 26px; 
            border-radius: 50%; 
            object-fit: cover; 
            margin-bottom: 2px;
            border: 1px solid #dee2e6;
        }
        /* Inisial Huruf di Bottom Nav */
        .navbar-bottom-item .account-initial {
            width: 26px; height: 26px;
            border-radius: 50%;
            background-color: #198754; color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 0.75rem;
            margin-bottom: 2px;
        }

        .navbar-bottom-item.active { color: #4caf50; font-weight: 600; }
        .navbar-bottom-item.active .account-img { border-color: #4caf50; border-width: 2px; }

        /* --- Styles Lainnya --- */
        .feature-header { background-color: #ffffff; border-bottom: 1px solid #dee2e6; position: sticky; top: 0; z-index: 1020; }
        .feature-header .btn-back { font-size: 1.5rem; color: #333; text-decoration: none; }
        .feature-header .title { font-size: 1.15rem; font-weight: 600; color: #333; }
        
        /* Footer Styles */
        .footer { background-color: #f1f3f5; color: #6c757d; padding-top: 2rem; padding-bottom: 2rem; text-align: center; }
        .footer-social-links { margin-bottom: 1.5rem; }
        .footer-social-links .social-icon {
            display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;
            text-decoration: none; color: #ffffff; margin: 0 0.4rem; font-size: 1.1rem; transition: all 0.3s ease; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .footer-social-links .social-icon:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .bg-facebook { background-color: #3b5998; } .bg-twitter { background-color: #1da1f2; }
        .bg-youtube { background-color: #ff0000; } .bg-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        .bg-whatsapp { background-color: #25D366; }
        .footer-address { color: #495057; font-size: 0.85rem; line-height: 1.6; margin-bottom: 1rem; }
        .copyright-text { font-size: 0.8rem; color: #adb5bd; margin-top: 1rem; }

        /* Modal Bottom */
        .modal.fade .modal-dialog.modal-bottom { transform: translateY(100%); transition: transform 0.3s ease-out; }
        .modal.show .modal-dialog.modal-bottom { transform: translateY(0); }
        .modal-dialog.modal-bottom { position: fixed; bottom: 0; left: 0; right: 0; margin: 0; width: 100%; max-width: 100%; }
        .modal-dialog.modal-bottom .modal-content { border-radius: 1rem 1rem 0 0; border: none; }
        .modal-dialog.modal-bottom .modal-header { border-bottom: 1px solid #dee2e6; }
        .modal-dialog.modal-bottom .modal-title { font-size: 1rem; font-weight: 600; }
        .modal-list-icon { width: 28px; height: 28px; object-fit: contain; margin-right: 1rem; }

        /* Desktop Styles */
        @media (min-width: 992px) {
            .navbar-brand-icon { height: 32px; width: auto; }
            .navbar-nav .nav-link { display: flex; flex-direction: row; align-items: center; padding: 0.5rem; margin: 0 0.5rem; text-align: left; }
            .navbar-nav .nav-link .nav-icon-img { width: 24px; height: 24px; object-fit: contain; margin-right: 8px; }
            .navbar-nav .nav-link .nav-icon-text { font-size: 0.9rem; font-weight: 500; color: #6c757d; transition: color 0.2s ease; }
            .navbar-nav .nav-link.active .nav-icon-text { color: #4caf50 !important; font-weight: 600; }
            .navbar-nav .nav-link:not(.active):hover .nav-icon-text { color: #333; }
            .dropdown-icon-img { width: 20px; height: 20px; object-fit: contain; margin-right: 10px; vertical-align: -3px; }
        }
        
        @media (max-width: 991.98px) {
            .footer { padding-bottom: 80px; }
        }
    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- =========================================================== --}}
    {{-- 1. DESKTOP NAVBAR                                         --}}
    {{-- =========================================================== --}}
    <header class="d-none d-lg-block shadow-sm" style="position: sticky; top: 0; z-index: 1030; background-color: white;">
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('public.landing') }}">
                    <img src="{{ asset('images/icons/masjid.png') }}" alt="Brand Icon" class="navbar-brand-icon me-2">
                    <span>Smart Masjid</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link {{ Request::is('/') ? 'active' : '' }}" href="{{ route('public.landing') }}"><img src="{{ asset('images/icons/home.png') }}" class="nav-icon-img"><span class="nav-icon-text">Beranda</span></a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('public.jadwal-kajian*') ? 'active' : '' }}" href="{{ route('public.jadwal-kajian') }}"><img src="{{ asset('images/icons/kajian.png') }}" class="nav-icon-img"><span class="nav-icon-text">Kajian</span></a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('public.jadwal-adzan*') ? 'active' : '' }}" href="{{ route('public.jadwal-adzan') }}"><img src="{{ asset('images/icons/adzan.png') }}" class="nav-icon-img"><span class="nav-icon-text">Jadwal Adzan</span></a></li>
                        <li class="nav-item"><a class="nav-link {{ Request::routeIs('public.donasi*') ? 'active' : '' }}" href="{{ route('public.donasi') }}"><img src="{{ asset('images/icons/donasi.png') }}" class="nav-icon-img"><span class="nav-icon-text">Donasi</span></a></li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown"><img src="{{ asset('images/icons/more (1).png') }}" class="nav-icon-img"><span class="nav-icon-text">Lainnya</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('public.artikel') }}"><img src="{{ asset('images/icons/artikel.png') }}" class="dropdown-icon-img">Artikel</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.program') }}"><img src="{{ asset('images/icons/program.png') }}" class="dropdown-icon-img">Program</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.tabungan-qurban-saya') }}"><img src="{{ asset('images/icons/qurban.png') }}" class="dropdown-icon-img">Tabungan Qurban</a></li>
                                <li><a class="dropdown-item" href="{{ route('public.jadwal-khotib') }}"><img src="{{ asset('images/icons/khutbah-jumat.png') }}" class="dropdown-icon-img">Khutbah Jumat</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        @auth('pengurus')
                            {{-- DROPDOWN PENGURUS DI DESKTOP --}}
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                    @if(Auth::guard('pengurus')->user()->avatar)
                                        <img src="{{ Auth::guard('pengurus')->user()->avatar_url }}" class="rounded-circle me-2 object-fit-cover" style="width: 32px; height: 32px;">
                                    @else
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">{{ substr(Auth::guard('pengurus')->user()->name, 0, 1) }}</div>
                                    @endif
                                    <span class="text-dark fw-medium">{{ Auth::guard('pengurus')->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item" href="{{ route('pengurus.profile.edit') }}"><i class="bi bi-person me-2"></i> Profile</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pengurus.settings.edit') }}"><i class="bi bi-gear me-2"></i> Settings</a></li>
                                    <li><a class="dropdown-item" href="{{ route('pengurus.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger fw-bold"><i class="bi bi-box-arrow-right me-2"></i> Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @elseauth('jamaah')
                            <div class="dropdown">
                                <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                                    @if(Auth::guard('jamaah')->user()->avatar)
                                        <img src="{{ Auth::guard('jamaah')->user()->avatar_url }}" class="rounded-circle me-2 object-fit-cover" style="width: 32px; height: 32px;">
                                    @else
                                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-weight: bold;">{{ substr(Auth::guard('jamaah')->user()->name, 0, 1) }}</div>
                                    @endif
                                    <span class="text-dark fw-medium">{{ Auth::guard('jamaah')->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li><a class="dropdown-item" href="{{ route('jamaah.profile.edit') }}">Profil Saya</a></li>
                                    <li><a class="dropdown-item" href="{{ route('public.tabungan-qurban-saya') }}">Tabungan Qurban</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><form action="{{ route('logout') }}" method="POST">@csrf<button type="submit" class="dropdown-item text-danger">Logout</button></form></li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('auth.welcome') }}" class="btn btn-success btn-sm rounded-pill px-3"><i class="bi bi-box-arrow-in-right me-1"></i> Login</a>
                        @endauth
                    </div>
                </div>
            </div>
        </nav>
    </header>

    {{-- =========================================================== --}}
    {{-- 2. MOBILE HERO TOP NAV (KHUSUS UNTUK HALAMAN BERANDA)       --}}
    {{-- =========================================================== --}}
    @if(Request::is('/'))
        <nav class="hero-top-nav d-lg-none">
            <div class="container d-flex justify-content-between align-items-center px-4">
                <a class="navbar-brand" href="{{ url('/') }}">Smart Masjid</a>
                
                {{-- [PERBAIKAN] Cek Manual Guard: Jika TIDAK login Pengurus DAN TIDAK login Jamaah --}}
                @if(!Auth::guard('pengurus')->check() && !Auth::guard('jamaah')->check())
                    <a href="{{ route('auth.welcome') }}" class="profile-trigger">
                        <i class="bi bi-box-arrow-in-right"></i>
                    </a>
                @else
                    {{-- Dropdown User Mobile --}}
                    <div class="dropdown">
                        <a href="#" class="profile-trigger" data-bs-toggle="dropdown" aria-expanded="false">
                            @php 
                                $user = Auth::guard('pengurus')->check() ? Auth::guard('pengurus')->user() : Auth::guard('jamaah')->user(); 
                            @endphp
                            
                            {{-- Tampilan Foto Profil Mobile Atas --}}
                            @if($user->avatar)
                                <img src="{{ $user->avatar_url }}" alt="Profil" class="profile-img-nav">
                            @else
                                <div class="profile-initial-nav">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow mt-2 border-0 rounded-3" style="min-width: 200px;">
                            {{-- Header Nama --}}
                            <li class="px-3 py-2 border-bottom mb-1">
                                <div class="fw-bold text-truncate" style="max-width: 180px;">{{ $user->name }}</div>
                                <div class="small text-muted">{{ Auth::guard('pengurus')->check() ? 'Pengurus' : 'Jamaah' }}</div>
                            </li>

                            @if(Auth::guard('pengurus')->check())
                                {{-- MENU PENGURUS MOBILE --}}
                                <li><a class="dropdown-item py-2" href="{{ route('pengurus.profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i> Profile</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('pengurus.settings.edit') }}"><i class="bi bi-gear me-2 text-primary"></i> Settings</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('pengurus.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-primary"></i> Dashboard</a></li>
                            @else
                                {{-- MENU JAMAAH MOBILE --}}
                                <li><a class="dropdown-item py-2" href="{{ route('jamaah.profile.edit') }}"><i class="bi bi-person-gear me-2 text-success"></i> Profil Saya</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('public.tabungan-qurban-saya') }}"><i class="bi bi-bank me-2 text-success"></i> Tabungan Qurban</a></li>
                            @endif
                            
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger fw-bold">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </nav>
    @endif

    <div class="container-fluid p-0 flex-grow-1">
        @yield('content')
    </div>

    {{-- Footer Desktop (Sama) --}}
    <footer class="footer mt-auto">
        <div class="container">
            <div class="footer-social-links">
                @if (!empty($settings->social_facebook)) <a href="{{ $settings->social_facebook }}" class="social-icon bg-facebook" target="_blank"><i class="bi bi-facebook"></i></a> @endif
                @if (!empty($settings->social_twitter)) <a href="{{ $settings->social_twitter }}" class="social-icon bg-twitter" target="_blank"><i class="bi bi-twitter"></i></a> @endif
                @if (!empty($settings->social_youtube)) <a href="{{ $settings->social_youtube }}" class="social-icon bg-youtube" target="_blank"><i class="bi bi-youtube"></i></a> @endif
                @if (!empty($settings->social_instagram)) <a href="{{ $settings->social_instagram }}" class="social-icon bg-instagram" target="_blank"><i class="bi bi-instagram"></i></a> @endif
                @if (!empty($settings->social_whatsapp)) <a href="https://wa.me/{{ $settings->social_whatsapp }}" class="social-icon bg-whatsapp" target="_blank"><i class="bi bi-whatsapp"></i></a> @endif
            </div>
            <p class="footer-address"><strong>Masjid {{ $settings->nama_masjid }}</strong><br>{!! nl2br(e($settings->lokasi_nama)) !!}</p>
            <div class="copyright-text">Copyright © Masjid {{ $settings->nama_masjid }} {{ date('Y') }}</div>
        </div>
    </footer>

    {{-- =========================================================== --}}
    {{-- 3. MOBILE BOTTOM NAV (MODIFIED WITH "AKUN" TAB)             --}}
    {{-- =========================================================== --}}
    <nav class="navbar-bottom d-lg-none">
        <a href="{{ route('public.landing') }}" class="navbar-bottom-item {{ Request::is('/') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/home.png') }}" alt="Beranda"> 
            <span>Beranda</span>
        </a>

        <a href="{{ route('public.jadwal-kajian') }}" class="navbar-bottom-item {{ Request::routeIs('public.jadwal-kajian*') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/kajian.png') }}" alt="Kajian">
            <span>Kajian</span>
        </a>
        
        <a href="{{ route('public.donasi') }}" class="navbar-bottom-item {{ Request::routeIs('public.donasi*') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/donasi.png') }}" alt="Donasi">
            <span>Donasi</span>
        </a>

        <a href="#" class="navbar-bottom-item" data-bs-toggle="modal" data-bs-target="#modalLainnya">
            <img src="{{ asset('images/icons/more (1).png') }}" alt="Lainnya">
            <span>Lainnya</span>
        </a>

        {{-- [PERBAIKAN] ITEM BARU: AKUN DENGAN FOTO (Ganti @auth dengan cek manual) --}}
        @if(Auth::guard('pengurus')->check() || Auth::guard('jamaah')->check())
            @php 
                $user = Auth::guard('pengurus')->check() ? Auth::guard('pengurus')->user() : Auth::guard('jamaah')->user(); 
                $profileRoute = Auth::guard('pengurus')->check() ? route('pengurus.profile.edit') : route('jamaah.profile.edit');
            @endphp
            {{-- Jika Login: Ke Halaman Edit Profil --}}
            <a href="{{ $profileRoute }}" 
               class="navbar-bottom-item {{ Request::routeIs('*.profile.edit') ? 'active' : '' }}">
                
                @if($user->avatar)
                    <img src="{{ $user->avatar_url }}" alt="Akun" class="account-img">
                @else
                    {{-- Fallback jika belum ada foto (Inisial) --}}
                    <div class="account-initial">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <span>Akun</span>
            </a>
        @else
            {{-- Jika Belum Login: Ke Halaman Login --}}
            <a href="{{ route('auth.welcome') }}" class="navbar-bottom-item">
                <i class="bi bi-person-circle text-secondary" style="font-size: 24px; margin-bottom: 2px;"></i>
                <span>Masuk</span>
            </a>
        @endif
    </nav>

    {{-- Modal Lainnya --}}
    <div class="modal fade" id="modalLainnya" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-bottom"> 
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Fitur Lainnya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('public.jadwal-adzan') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/adzan.png') }}" class="modal-list-icon">
                            <span class="fw-medium">Jadwal Adzan</span>
                        </a>
                        <a href="{{ route('public.artikel') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/artikel.png') }}" class="modal-list-icon">
                            <span class="fw-medium">Artikel</span>
                        </a>
                        <a href="{{ route('public.program') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/program.png') }}" class="modal-list-icon">
                            <span class="fw-medium">Program</span>
                        </a>
                        <a href="{{ route('public.tabungan-qurban-saya') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/qurban.png') }}" class="modal-list-icon">
                            <span class="fw-medium">Tabungan Qurban</span>
                        </a>
                        <a href="{{ route('public.jadwal-khotib') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/khutbah-jumat.png') }}" class="modal-list-icon">
                            <span class="fw-medium">Khutbah Jumat</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>