<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#198754">
    <title>@yield('title') - Smart Masjid</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light-gray">

    <nav class="top-navbar navbar navbar-expand-lg bg-white border-bottom shadow-sm fixed-top">
        <div class="container-fluid px-4">
            
            <div class="d-flex align-items-center">
                <button class="btn btn-light rounded-circle shadow-sm border me-3 d-flex justify-content-center align-items-center" id="sidebar-toggle" type="button" style="width: 40px; height: 40px;">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">@yield('title')</h5>
                    <small class="text-muted d-none d-sm-block">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</small>
                </div>
            </div>

            <ul class="navbar-nav ms-auto d-flex flex-row align-items-center gap-3">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-1 rounded-pill border bg-light pe-3" href="#" role="button" data-bs-toggle="dropdown">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar_url }}" class="rounded-circle object-fit-cover" style="width: 32px; height: 32px;">
                        @else
                            <div class="rounded-circle bg-gradient-green text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; font-weight: bold; font-size: 0.9rem;">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="small fw-bold text-dark d-none d-md-block">{{ Auth::user()->name }}</span>
                    </a>
                    
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 p-2">
                        <li><h6 class="dropdown-header text-uppercase small fw-bold text-muted ls-1">Akun Saya</h6></li>
                        
                        {{-- Menu Profile --}}
                        <li>
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('pengurus.profile.edit') }}">
                                <i class="bi bi-person me-2 text-secondary"></i> Profile
                            </a>
                        </li>
                        
                        {{-- Menu Settings --}}
                        <li>
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('pengurus.settings.edit') }}">
                                <i class="bi bi-gear me-2 text-secondary"></i> Settings
                            </a>
                        </li>

                        {{-- Menu Landing Page (YANG HILANG TADI) --}}
                        <li>
                            <a class="dropdown-item rounded-2 py-2" href="{{ route('public.landing') }}">
                                <i class="bi bi-globe me-2 text-secondary"></i> Landing Page
                            </a>
                        </li>

                        <li><hr class="dropdown-divider my-2"></li>
                        
                        {{-- Menu Logout --}}
                        <li>
                            <a class="dropdown-item rounded-2 py-2 text-danger fw-bold" href="#" onclick="event.preventDefault(); document.getElementById('logout-form-dropdown').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                            <form id="logout-form-dropdown" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="sidebar-backdrop" id="sidebar-backdrop"></div>

    <div class="d-flex">
        @include('layouts.navigation')

        <div id="main-content" class="main-content w-100">
            @yield('content')
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('main-content');
        const toggleButton = document.getElementById('sidebar-toggle');
        const backdrop = document.getElementById('sidebar-backdrop');

        function toggleSidebar() {
            const isMobile = window.innerWidth < 992;
            sidebar.classList.toggle('toggled');
            if (!isMobile) mainContent.classList.toggle('toggled');
            if (isMobile) backdrop.classList.toggle('show');
        }

        toggleButton.addEventListener('click', (e) => { e.stopPropagation(); toggleSidebar(); });
        backdrop.addEventListener('click', toggleSidebar);
    </script>
    @stack('scripts') 
</body>
</html>