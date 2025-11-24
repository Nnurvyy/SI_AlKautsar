<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFFFFF">
    <title>Dashboard - Smart Masjid</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="https://cdn.datatables.net/2.0.10/css/dataTables.bootstrap5.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')

</head>
<body>

    <nav class="top-navbar navbar navbar-expand-lg">
        <div class="container-fluid">

            <button class="btn btn-link" id="sidebar-toggle" type="button">
                <i class="bi bi-list"></i>
            </button>

            <div class="ms-4">
                <h4 class="fw-bold mb-0">@yield('title')</h4>
                <p class="text-muted mb-0 d-none d-sm-block">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p>
            </div>

            <ul class="navbar-nav ms-auto d-flex flex-row">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        
                        {{-- PERBAIKAN: Tampilkan Foto Profil Pengurus --}}
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="rounded-circle me-2 object-fit-cover" style="width: 40px; height: 40px;">
                        @else
                            {{-- Jika tidak ada foto, gunakan inisial dengan styling user-avatar (sesuai CSS admin lama) --}}
                            <div class="user-avatar me-2 bg-primary text-white d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; font-weight: bold;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif

                        <div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div> 
                            <div class="small text-muted">{{ Auth::user()->email }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('pengurus.profile.edit') }}">Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('pengurus.settings.edit') }}">Settings</a></li>
                        <li><a class="dropdown-item" href="{{ route('public.landing') }}">Landing Page</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-dropdown').submit();">
                                Logout
                            </a>
                            <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <div>
        @include('layouts.navigation')

        <div id="main-content" class="main-content">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.10/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.0.10/js/dataTables.bootstrap5.js"></script>
    <script>
        // Ambil elemen-elemen
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleButton = document.getElementById('sidebar-toggle');
        const backdrop = document.getElementById('sidebar-backdrop');

        // Fungsi untuk toggle
        function toggleSidebar() {
            const isMobile = window.innerWidth < 992;

            sidebar.classList.toggle('toggled');

            if (!isMobile) {
                mainContent.classList.toggle('toggled');
            }

            if (isMobile) {
                backdrop.classList.toggle('show');
            }
        }

        // Klik tombol hamburger
        toggleButton.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // Klik backdrop (untuk menutup di mobile)
        backdrop.addEventListener('click', function() {
            toggleSidebar();
        });

        // Klik link navigasi (untuk auto-tutup)
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const isMobile = window.innerWidth < 992;
                if (sidebar.classList.contains('toggled') && isMobile) {
                    toggleSidebar();
                }
            });
        });
    </script>
    <script>
        if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js').then(function(registration) {
            console.log('ServiceWorker berhasil didaftarkan.');
            }, function(err) {
            console.log('ServiceWorker gagal didaftarkan: ', err);
            });
        });
        }
    </script>

    @stack('scripts') </body>
</html>