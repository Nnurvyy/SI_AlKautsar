<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ config('app.name') }} - Donasi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Style -->
    <style>
        body {
            background-color: #f8f9fa;
        }

        nav {
            background-color: #198754;
        }

        nav a {
            color: white !important;
            font-weight: 500;
        }

        footer {
            background-color: #198754;
            color: white;
            padding: 20px 0;
            margin-top: 40px;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg shadow-sm">
        <div class="container">
            <a class="navbar-brand text-white fw-bold" href="/">
                SI Al-Kautsar
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item"><a class="nav-link" href="/donasi">Donasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="/jadwal-kajian">Kajian</a></li>
                    <li class="nav-item"><a class="nav-link" href="/jadwal-khotib">Khotib</a></li>
                    <li class="nav-item"><a class="nav-link" href="/artikel">Artikel</a></li>

                </ul>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div>
        @yield('content')
    </div>

    <!-- FOOTER -->
    <footer class="text-center">
        <div>© {{ date('Y') }} SI Al-Kautsar - Semua Hak Dilindungi</div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
