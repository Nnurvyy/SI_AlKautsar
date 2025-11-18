<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FFFFFF">
    <title>{{ config('app.name', 'E-Masjid') }} - @yield('title', 'Selamat Datang')</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ============================================== */
        /* == RESET & GLOBAL STYLES == */
        /* ============================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #2F3E3C; 
            background-size: cover;
            background-position: center center;
            background-attachment: fixed;
            color: #ffffff;
            display: flex;
            align-items: center; 
            justify-content: center;
            /* == GANTI URL BACKGROUND DI SINI == */
            background-image: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.5)), url("{{ asset('images/bg_login.jpeg') }}");
        }

        /* ============================================== */
        /* == Tombol Back == */
        /* ============================================== */
        .back-link {
            position: absolute; 
            top: 1.5rem;
            left: 1.5rem;
            font-size: 1.8rem; 
            color: #B0C4C0; 
            text-decoration: none;
            transition: color 0.2s;
            z-index: 10;
        }
        .back-link:hover {
            color: #ffffff; 
        }

        /* ============================================== */
        /* == STRUKTUR LAYOUT UTAMA == */
        /* ============================================== */
        .auth-screen {
            width: 100%;
            max-width: 400px; 
            min-height: 100vh; 
            display: flex;
            flex-direction: column;
            padding: 2.5rem 1.5rem; 
            position: relative; 
        }

        .auth-content {
            flex-grow: 1; 
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        
        .auth-footer {
            text-align: center;
            padding-top: 1rem;
        }

        /* ============================================== */
        /* == ELEMEN UMUM == */
        /* ============================================== */
        .logo {
            font-size: 7rem; 
            color: #ffffff;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            font-size: 1rem;
            color: #B0C4C0; 
            margin-bottom: 3rem;
        }

        a.link {
            color: #ffffff;
            font-weight: 600;
            text-decoration: underline;
        }

        .auth-footer p, .auth-footer a {
            font-size: 0.9rem;
            color: #B0C4C0;
        }
        .auth-footer a {
            font-weight: 600;
            color: #ffffff; 
            text-decoration: none;
        }

        /* ============================================== */
        /* == FORM STYLES (Sign In & Sign Up) == */
        /* ============================================== */
        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
            position: relative;
        }

        /* DIUBAH: !important pada background dihapus */
        .form-control {
            width: 100%;
            background: transparent; 
            border: none;
            border-bottom: 1px solid #B0C4C0;
            color: #ffffff;
            font-size: 1rem;
            padding: 0.75rem 0.25rem;
            transition: border-color 0.2s;
        }
        .form-control::placeholder {
            color: #B0C4C0;
        }
        .form-control:focus {
            outline: none;
            border-bottom-color: #ffffff;
        }

        /* * DIUBAH: Perbaikan untuk style autofill browser (agar transparan) */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover, 
        .form-control:-webkit-autofill:focus, 
        .form-control:-webkit-autofill:active  {
            /* 1. Paksa warna teks dan kursor jadi putih */
            -webkit-text-fill-color: #ffffff !important; 
            caret-color: #ffffff !important;
            
            /* 2. Gunakan 'transition hack' untuk menunda background-color */
            /* Ini akan "menipu" browser agar tidak menampilkan background-nya */
            transition: background-color 5000s ease-in-out 0s;
            
            /* 3. Hapus box-shadow hack yang lama */
            -webkit-box-shadow: none;
            box-shadow: none;
        }
        
        .form-options {
            text-align: right;
            margin-top: -0.5rem;
            margin-bottom: 1.5rem;
        }
        .form-options a {
            font-size: 0.875rem;
            color: #B0C4C0;
            text-decoration: none;
        }
        .form-options a:hover {
            text-decoration: underline;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 0.25rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #B0C4C0;
            width: 20px; 
            height: 20px;
        }
        
        /* ============================================== */
        /* == BUTTON STYLES == */
        /* ============================================== */
        .btn {
            display: block;
            width: 100%;
            padding: 0.8rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.2s;
            margin-bottom: 1rem; 
        }

        .btn-primary {
            background-color: #B0C4C0; 
            color: #2F3E3C; 
        }
        .btn-primary:hover {
            background-color: #ffffff;
        }

        .btn-secondary {
            background-color: transparent;
            color: #ffffff;
            border: 1px solid #ffffff;
        }
        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn-social {
            background-color: #ffffff;
            color: #333333;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-social i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #B0C4C0;
            font-size: 0.875rem;
            margin: 1.5rem 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #B0C4C0;
        }
        .divider:not(:empty)::before {
            margin-right: .5em;
        }
        .divider:not(:empty)::after {
            margin-left: .5em;
        }
        
        .error-message {
            color: #ff8a8a; 
            font-size: 0.875rem;
            text-align: left;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>

    @yield('content')
    
    @stack('scripts')

</body>
</html>