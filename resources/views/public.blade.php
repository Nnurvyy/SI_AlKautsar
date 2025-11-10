<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Masjid Al Kautsar 561')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        /* CSS untuk Navigasi Bawah (Duolingo-style) */
        body {
            /* Beri ruang di bawah agar konten tidak tertutup nav */
            padding-bottom: 70px;
        }

        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #ffffff;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-around;
            padding: 0.5rem 0;
            z-index: 1000;
        }
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            font-size: 0.7rem;
            color: #8e8e93;
            flex-grow: 1;
        }
        .bottom-nav-item i {
            font-size: 1.5rem;
            margin-bottom: 2px;
        }
        
        /* Warna Ikon Sesuai Permintaan */
        .bottom-nav-item .icon-home { color: #ff9500; } /* Oranye */
        .bottom-nav-item .icon-info { color: #007aff; } /* Biru */
        .bottom-nav-item .icon-keuangan { color: #34c759; } /* Hijau */
        .bottom-nav-item .icon-akun { color: #af52de; } /* Ungu */
        
        /* Style untuk link aktif */
        .bottom-nav-item.active {
            font-weight: bold;
            color: #000;
        }
        .bottom-nav-item.active i {
            /* Beri warna solid saat aktif */
            filter: grayscale(0%);
        }
        
        /* Sembunyikan navigasi bawah di desktop */
        @media (min-width: 992px) {
            body {
                padding-bottom: 0; /* Hapus padding di desktop */
            }
            .bottom-nav {
                display: none; /* Sembunyikan di desktop */
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    <main>
        @yield('content')
    </main>

    <nav class="bottom-nav d-lg-none">
        <a href="#" class="bottom-nav-item active">
            <i class="bi bi-house-door-fill icon-home"></i>
            <span>Home</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <i class="bi bi-info-circle-fill icon-info"></i>
            <span>Info</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <i class="bi bi-wallet-fill icon-keuangan"></i>
            <span>Keuangan</span>
        </a>
        <a href="#" class="bottom-nav-item">
            <i class="bi bi-person-circle icon-akun"></i>
            <span>Akun</span>
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>