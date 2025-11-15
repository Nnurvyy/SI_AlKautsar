<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Masjid')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa; /* Latar belakang abu-abu muda */
        }

        html, body {
            margin: 0;
            padding: 0;
        }
        /* Style untuk header "siloed" */
        .feature-header {
            background-color: #ffffff;
            border-bottom: 1px solid #dee2e6;
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .feature-header .btn-back {
            font-size: 1.5rem;
            color: #333;
            text-decoration: none;
        }
        .feature-header .title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #333;
        }

        /* ================================== */
        /* CSS FOOTER */
        /* ================================== */
        .footer {
            background-color: #f1f3f5;
            color: #6c757d;
            padding-top: 2rem;
            padding-bottom: 2rem; 
            text-align: center;
        }
        .footer-social-links { margin-bottom: 1.5rem; }
        .footer-social-links .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            text-decoration: none;
            color: #ffffff;
            margin: 0 0.4rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .footer-social-links .social-icon:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .footer-address {
            color: #495057;
            font-size: 0.85rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        .copyright-text {
            font-size: 0.8rem;
            color: #adb5bd;
            margin-top: 1rem;
        }
        .bg-facebook { background-color: #3b5998; }
        .bg-twitter { background-color: #1da1f2; }
        .bg-youtube { background-color: #ff0000; }
        .bg-instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
        /* ================================== */
        /* AKHIR DARI CSS FOOTER */
        /* ================================== */


        /* ================================== */
        /* CSS UNTUK NAVBAR BAWAH */
        /* ================================== */
        .navbar-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background-color: #ffffff;
            display: flex;
            justify-content: space-around;
            align-items: stretch;
            padding-top: 8px;
            padding-bottom: 4px;
            box-shadow: 0 -2px 5px rgba(0,0,0,0.08);
            z-index: 1010;
            border-top: 1px solid #dee2e6;
        }
        .navbar-bottom-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #8a9a9d;
            font-size: 0.7rem;
            font-weight: 500;
            flex-grow: 1;
            /* */
            background: none;
            border: none;
        }
        .navbar-bottom-item img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            margin-bottom: 2px;
        }
        .navbar-bottom-item .bi {
            font-size: 26px; /* Ukuran ikon home & lainnya */
            margin-bottom: 2px;
            height: 28px; /* Samakan tinggi dgn gambar */
            display: flex;
            align-items: center;
        }
        .navbar-bottom-item.active {
            color: #4caf50;
            font-weight: 600;
        }

        
        /* ================================== */
        /* AKHIR DARI CSS NAVBAR BAWAH */
        /* ================================== */


        /* ================================== */
        /* */
        /* ================================== */
        .modal.fade .modal-dialog.modal-bottom {
            transform: translateY(100%); /* Mulai dari bawah */
            transition: transform 0.3s ease-out;
        }
        .modal.show .modal-dialog.modal-bottom {
            transform: translateY(0); /* Muncul ke atas */
        }
        .modal-dialog.modal-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0;
            width: 100%;
            max-width: 100%;
        }
        .modal-dialog.modal-bottom .modal-content {
            border-radius: 1rem 1rem 0 0; /* Sudut tumpul di atas */
            border: none;
        }
        .modal-dialog.modal-bottom .modal-header {
            border-bottom: 1px solid #dee2e6;
        }
        .modal-dialog.modal-bottom .modal-title {
            font-size: 1rem;
            font-weight: 600;
        }
        .modal-list-icon {
            width: 28px; /* Samakan dgn ikon navbar */
            height: 28px;
            object-fit: contain;
            margin-right: 1rem;
        }
        /* ================================== */
        /* */
        /* ================================== */


        /* Media query untuk desktop */
        @media (min-width: 992px) {
            /* Kosong, tidak perlu override body */
        }

        /* Media query untuk mobile */
        @media (max-width: 991.98px) {
            .footer {
                /* Padding ekstra agar copyright tidak tertutup navbar */
                padding-bottom: 80px;
            }
        }

    </style>
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    <div class="container-fluid p-0">
        @yield('content')
    </div>

    <footer class="footer mt-auto">
        <div class="container">
            <div class="footer-social-links">
                <a href="#" class="social-icon bg-facebook" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="social-icon bg-twitter" aria-label="Twitter"><i class="bi bi-twitter"></i></a>
                <a href="#" class="social-icon bg-youtube" aria-label="Youtube"><i class="bi bi-youtube"></i></a>
                <a href="#" class="social-icon bg-instagram" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            </div>
            <p class="footer-address">
                <strong>Masjid Al-Jabbar</strong><br>
                Jl. Al-Jabbar No. 1, Cimincrang, Gedebage<br>
                Kota Bandung, Jawa Barat 40292
            </p>
            <div class="copyright-text">
                Copyright © Masjid Al-Jabbar 2025
            </div>
        </div>
    </footer>

    <nav class="navbar-bottom d-lg-none">
        <a href="{{ route('public.landing') }}" class="navbar-bottom-item {{ Request::is('/') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/home.png') }}" alt="Beranda"> 
            <span>Beranda</span>
        </a>

        <a href="{{ route('public.jadwal-kajian') }}" class="navbar-bottom-item {{ Request::routeIs('public.jadwal-kajian*') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/kajian.png') }}" alt="Kajian">
            <span>Kajian</span>
        </a>

        <a href="{{ route('public.jadwal-adzan') }}" class="navbar-bottom-item {{ Request::routeIs('public.jadwal-adzan*') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/adzan.png') }}" alt="Adzan">
            <span>Adzan</span>
        </a>
        
        <a href="{{ route('public.donasi') }}" class="navbar-bottom-item {{ Request::routeIs('public.donasi*') ? 'active' : '' }}">
            <img src="{{ asset('images/icons/donasi.png') }}" alt="Donasi">
            <span>Donasi</span>
        </a>

        <a href="#" class="navbar-bottom-item" data-bs-toggle="modal" data-bs-target="#modalLainnya">
            <img src="{{ asset('images/icons/more (1).png') }}" alt="Lainnya">
            <span>Lainnya</span>
        </a>
    </nav>



    <div class="modal fade" id="modalLainnya" tabindex="-1" aria-labelledby="modalLainnyaLabel" aria-hidden="true">
        <div class="modal-dialog modal-bottom"> <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLainnyaLabel">Fitur Lainnya</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="list-group list-group-flush">
                        
                        <a href="{{ route('public.artikel') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 {{ Request::routeIs('public.artikel*') ? 'active' : '' }}">
                            <img src="{{ asset('images/icons/artikel.png') }}" alt="Artikel" class="modal-list-icon">
                            <span class="fw-medium">Artikel</span>
                        </a>

                        <a href="{{ route('public.program') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3 {{ Request::routeIs('public.program*') ? 'active' : '' }}">
                            <img src="{{ asset('images/icons/program.png') }}" alt="Program" class="modal-list-icon">
                            <span class="fw-medium">Program</span>
                        </a>

                        <a href="{{ route('public.tabungan-qurban-saya') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/qurban.png') }}" alt="Qurban" class="modal-list-icon">
                            <span class="fw-medium">Tabungan Qurban</span>
                        </a>
                        <a href="{{ route('public.jadwal-khotib') }}" class="list-group-item list-group-item-action d-flex align-items-center py-3">
                            <img src="{{ asset('images/icons/khutbah-jumat.png') }}" alt="Khotib" class="modal-list-icon">
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