<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - E-Masjid</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

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
                <li class="nav-item me-3">
                    <a class="nav-link nav-bell-link" href="#"><i class="bi bi-bell fs-5"></i></a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar me-2">A</div>
                        <div>
                            <div class="fw-bold">{{ Auth::user()->name }}</div>
                            <div class="small text-muted">{{ Auth::user()->email }}</div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#">Profile</a></li>
                        <li><a class="dropdown-item" href="#">Settings</a></li>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
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
            
            // Di desktop, kita geser konten.
            // Di mobile, kita TIDAK geser konten (karena overlay).
            if (!isMobile) {
                mainContent.classList.toggle('toggled');
            }
            
            // Tampilkan/sembunyikan backdrop HANYA di mobile
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
                // Hanya tutup jika sidebar sedang terbuka dan di mobile
                if (sidebar.classList.contains('toggled') && isMobile) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>