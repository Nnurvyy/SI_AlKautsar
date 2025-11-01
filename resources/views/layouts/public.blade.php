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
    </style>
    @stack('styles')
</head>
<body class="pb-5">

    <div class="container-fluid p-0">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>