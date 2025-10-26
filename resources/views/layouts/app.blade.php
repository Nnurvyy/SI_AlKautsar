<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SI Keuangan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { 
            background-color: #f8f9fa;
        }

        /* 1. Navbar (Header Atas) */
        .top-navbar {
            position: fixed; /* Selalu diam di atas */
            top: 0;
            right: 0;
            left: 0; /* Selalu full-width */
            z-index: 1030; /* Di atas segalanya */
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 0.75rem 1.5rem;
            height: 70px; 
        }

        /* 2. Sidebar (Navigasi) */
        .sidebar {
            width: 280px;
            position: fixed;
            /* Mulai di BAWAH navbar */
            top: 70px; 
            left: 0;
            bottom: 0; /* Full height di bawah navbar */
            z-index: 1000;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: margin-left 0.3s ease;
            overflow-y: auto;
        }

        /* 3. Konten Utama */
        .main-content {
            /* Mulai di BAWAH navbar */
            padding-top: 70px; 
            /* Geser ke kanan selebar sidebar */
            margin-left: 280px; 
            transition: margin-left 0.3s ease;
        }

        /* --- Style Sidebar --- */
        .sidebar-header {
            padding: 1.5rem; border-bottom: 1px solid #dee2e6; text-align: center;
        }
        .sidebar-header h5 {
            color: #343a40; font-weight: 600; margin-bottom: 0.25rem;
        }
        .sidebar-header p {
            color: #6c757d; font-size: 0.85rem; margin-bottom: 0;
        }
        .sidebar .nav-link {
            padding: 0.75rem 1.5rem; color: #495057; font-weight: 500;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem; width: 20px; text-align: center;
        }
        .sidebar .nav-link.active {
            color: #0d6efd; background-color: #e9f0ff; border-right: 3px solid #0d6efd;
        }
        .sidebar .nav-link:hover { background-color: #f1f3f5; }
        .logout-form { padding: 0.75rem 1.5rem; }
        .logout-btn {
            display: flex; align-items: center; width: 100%; padding: 0.75rem;
            border: none; background: none; color: #dc3545; font-weight: 500;
            text-align: left; border-radius: 0.375rem;
        }
        .logout-btn:hover { background-color: #fdf2f2; }
        #sidebar-toggle { font-size: 1.5rem; color: #343a40; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%; background-color: #e0e7ff;
            color: #4338ca; display: flex; align-items: center;
            justify-content: center; font-weight: 600;
        }
        /* --- Akhir Style Sidebar --- */


        /* === LOGIKA TOGGLE === */

        /* A. Desktop (default, > 992px) */
        .sidebar.toggled {
            margin-left: -280px; /* Sidebar sembunyi ke kiri */
        }
        .main-content.toggled {
            margin-left: 0; /* Konten jadi full-width */
        }
        
        @media (max-width: 1500px) {
        .row.g-4.mb-4 > .col-md-6.col-xl-3 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        }

        /* Saat layar kecil (HP), ubah jadi 1 kolom */
        @media (max-width: 700px) {
        .row.g-4.mb-4 > .col-md-6.col-xl-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        }

        @media (min-width: 992px) and (max-width: 1300px) {
    
            /* 1. Target container utama */
            /* paksa 2 grup (filter & tombol) untuk tidak 'wrap' (turun baris) */
            .d-flex.flex-wrap.justify-content-between.align-items-center.mb-4 {
                flex-wrap: nowrap;
                align-items: flex-start; /* Ratakan kedua kolom ke atas */
                gap: 1.5rem; /* Jarak antara kolom filter dan kolom tombol */
            }

            /* 2. Target Kolom Kiri (Grup Filter) */
            /* target 'div' anak PERTAMA dari container utama */
            .d-flex.flex-wrap.justify-content-between.align-items-center.mb-4 > div:first-child {
                /* Ubah arahnya jadi vertikal (menumpuk ke bawah) */
                flex-direction: column; 
                flex-basis: 60%; /* Beri lebar 60% */
                flex-grow: 1;
                gap: 0.75rem; /* Jarak antar search bar dan select box */
            }

            /* 3. Target Kolom Kanan (Grup Tombol) */
            /* target 'div' anak KEDUA dari container utama */
            .d-flex.flex-wrap.justify-content-between.align-items-center.mb-4 > div:last-child {
                flex-basis: 40%; /* Ambil sisa lebarnya */
                flex-grow: 1;
                justify-content: flex-end; /* Dorong tombol ke paling kanan */
                flex-wrap: nowrap; /* Pastikan tombolnya tidak 'wrap' */
            }

            /* 4. Target Item DI DALAM Kolom Kiri (PENTING!) */
            /* buat search bar dan select box jadi 100% lebar kolomnya */
            .d-flex.flex-wrap.justify-content-between.align-items-center.mb-4 > div:first-child > .input-group,
            .d-flex.flex-wrap.justify-content-between.align-items-center.mb-4 > div:first-child > .form-select {
                
                /* butuh !important untuk "mengalahkan" style inline di HTML
                (yaitu style="width: 300px;" dan style="width: auto;")
                */
                width: 100% !important; 
                
                /* Hapus margin kanan agar rata sempurna */
                margin-right: 0 !important;
            }
        }

        @media (max-width: 991.98px) {

        /* --- NAVBAR MOBILE (POSISI JUDUL & FONT) --- */
        .top-navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem 1rem;
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
        }

        /* Tombol toggle sidebar tetap di kiri */
        #sidebar-toggle {
            flex-shrink: 0;
            font-size: 1.25rem;
            margin-right: 0.5rem;
        }

        /* Judul halaman */
        .navbar-page-title {
            flex: 1;
            text-align: center;
            margin: 0;
        }
        .navbar-page-title h4 {
            font-size: 1rem;
            margin: 0;
        }
        .navbar-page-title p {
            display: none;
        }

        /* --- POSISI DROPDOWN AKUN DI MOBILE --- */
        .navbar-nav .dropdown-menu {
            position: absolute !important;
            right: 0.5rem !important;
            top: 100% !important;
            transform: none !important;
            min-width: 140px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            z-index: 2000;
        }


        /* Profil user di kanan */
        .navbar-nav {
            display: flex;
            align-items: center;
            flex-shrink: 0;
            margin-left: auto;
            gap: 0.5rem;
        }

        /* Avatar user dan nama */
        .user-avatar {
            width: 30px;
            height: 30px;
            font-size: 0.8rem;
        }
        .user-avatar + div {
            display: none; /* sembunyikan teks nama di layar kecil */
        }
        .top-navbar,
        .top-navbar.toggled { left: 0; padding-right: 0.75rem; }
        .top-navbar .container-fluid { padding-left: 0; padding-right: 0; display: flex; align-items: center; width: 100%; }
        #sidebar-toggle { position: static; transform: none; padding-left: 0.75rem; padding-right: 0.5rem; }

        .navbar-page-title { 
            /* Hapus flex-grow agar tidak mendorong */
            /* flex-grow: 1; */ 
            margin-left: 0; 
        }
        .navbar-page-title h4 { 
            font-size: 1rem; /* Kecilkan lagi judul halaman */
        }
        .navbar-page-title p { display: none !important; }

        .navbar-nav { flex-shrink: 0; margin-left: auto; /* Pastikan tetap di kanan */ }
        .nav-bell-link i.bi-bell { font-size: 1.1rem; }
        .user-avatar { width: 32px; height: 32px; font-size: 0.8rem; }
        .user-avatar + div > .fw-bold { font-size: 0.85rem; }
        .user-avatar + div > .small.text-muted { display: none !important; }
        /* --- AKHIR NAVBAR --- */

        /* --- KONTEN & SIDEBAR --- */
        .main-content, .main-content.toggled { margin-left: 0; }
        .sidebar { margin-left: -280px; z-index: 1040; }
        .sidebar.toggled { margin-left: 0; }
        .sidebar-backdrop { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); z-index: 1039; display: none; }
        .sidebar-backdrop.show { display: block; }
        /* --- AKHIR KONTEN & SIDEBAR --- */

        /* --- TABEL MOBILE --- */
        .transaction-table table { font-size: 0.65rem; table-layout: auto; }
        .transaction-table th { width: auto !important; }
        .transaction-table th, .transaction-table td { padding: 0.5rem 0.4rem; white-space: normal !important; }
        .transaction-table .col-amount, .transaction-table .col-action { white-space: nowrap !important; }
        .dashboard-table th, .dashboard-table td { padding-top: 0.6rem; padding-bottom: 0.6rem; }
        /* --- AKHIR TABEL MOBILE --- */

        /* --- FILTER/TOMBOL MOBILE --- */
        .d-flex.align-items-center.flex-wrap > *:not(:last-child) { margin-bottom: 0.5rem; }
        .d-flex.align-items-center.flex-wrap .input-group.search-bar, .d-flex.align-items-center.flex-wrap .form-select { width: 100% !important; margin-right: 0 !important; }
         .d-flex.align-items-center.w-100.w-lg-auto .btn { width: 50% !important; }
    }

         @media (min-width: 992px) and (max-width: 1199.98px) {
            
            .transaction-table th, 
            .transaction-table td {
                
                /* 1. MATIKAN PEMOTONGAN KATA */
                -webkit-hyphens: none;
                -ms-hyphens: none;
                hyphens: none;
                
                /* 2. JANGAN PECAH KATA DI TENGAH */
                word-break: keep-all; 
                
                /* 3. BIARKAN TEKS TURUN BARIS (WRAP) SECARA ALAMI */
                /* !important di sini untuk mengalahkan 'nowrap' 
                    jika ada di kode Anda yang lain */
                white-space: normal !important; 

                /* 4. Kecilkan font & padding sedikit biar muat */
                font-size: 0.875rem; /* (14px) */
                padding: 0.75rem 0.5rem; /* (atas/bawah 0.75, kiri/kanan 0.5) */
                vertical-align: middle;
            }
        }

        
        .stat-card {
            border: none; border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        .stat-card .card-body {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1.5rem; 
        }
        .stat-card .card-body .text-muted {
            font-size: 0.875rem; 
        }
        .stat-card-icon {
            font-size: 1.5rem; width: 48px; height: 48px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .bg-custom-green-light { background-color: #e6f9f0; }
        .text-custom-green { color: #28a745; }
        .bg-custom-red-light { background-color: #fdebec; }
        .text-custom-red { color: #dc3545; }
        .bg-custom-blue-light { background-color: #e9f0ff; }
        .text-custom-blue { color: #0d6efd; }
        .bg-custom-yellow-light { background-color: #fffbeb; }
        .text-custom-yellow { color: #ffc107; }

        .transaction-table {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        }

        .transaction-table table {
            font-size: 0.875rem; 
            table-layout: fixed;
            width: 100%;
            min-width: 600px; 
        }

        .transaction-table th,
        .transaction-table td {
            padding-top: .8rem;
            padding-bottom: .8rem;
            vertical-align: middle; 
        }
        .transaction-table td {
            overflow-wrap: break-word;
            word-break: break-word;
        }
        .col-nowrap {
            white-space: nowrap;
        }
        .dashboard-table th,
        .dashboard-table td {
            padding-top: 1rem;
            padding-bottom: 1rem;
            vertical-align: middle; 
        }
        .transaction-table .badge {
            font-weight: 500;
            font-size: inherit;     
            border-radius: 6px;    
            padding: 0.4em 0.7em;
        }
        .btn-custom-padding {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .input-group.search-bar .form-control,
        .input-group.search-bar .input-group-text {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .input-group.search-bar .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6; 
        }
        .input-group.search-bar {
            border: 1px solid #dee2e6;
            border-radius: 0.4rem; 
            overflow: hidden; 
            transition: box-shadow 0.15s ease-in-out;
        }
        .input-group.search-bar .form-control,
        .input-group.search-bar .input-group-text {
            border: none;
        }
        .input-group.search-bar:focus-within {
            border: 1px solid black;
            outline: 2px solid #3c68ab;
            outline-offset: 2px;
        }
        .input-group.search-bar .form-control::placeholder {
            color: #adb5bd;
            opacity: 1;
        }

        .nav-bell-link {
            padding-top: 0.9rem !important; 
            padding-bottom: 0.9rem !important;
        }
        
    </style>
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

        // 1. Klik tombol hamburger
        toggleButton.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleSidebar();
        });

        // 2. Klik backdrop (untuk menutup di mobile)
        backdrop.addEventListener('click', function() {
            toggleSidebar();
        });
        
        // 3. Sesuai permintaan: Klik link navigasi (untuk auto-tutup)
        const navLinks = document.querySelectorAll('#sidebar .nav-link');
        navLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const isMobile = window.innerWidth < 992;
                // Hanya tutup jika sidebar sedang terbuka DAN di mobile
                if (sidebar.classList.contains('toggled') && isMobile) {
                    toggleSidebar();
                }
            });
        });
    </script>
</body>
</html>