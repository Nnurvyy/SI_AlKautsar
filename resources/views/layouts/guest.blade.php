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
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

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
            
            /* --- BACKGROUND BARU (CSS Murni) --- */
            background-color: #ffffff; /* Warna dasar Putih */
            
            /* Efek gradasi hijau lembut (seperti bercak cahaya hijau) */
            background-image: 
                radial-gradient(at 0% 0%, hsla(152, 60%, 85%, 1) 0px, transparent 50%), 
                radial-gradient(at 100% 100%, hsla(140, 60%, 90%, 1) 0px, transparent 50%);
            background-attachment: fixed;
            background-size: cover;
            /* ----------------------------------- */

            color: #2F3E3C; /* Ubah warna teks jadi gelap (Hijau tua) */
            display: flex;
            align-items: center; 
            justify-content: center;
        }

        /* ============================================== */
        /* == Tombol Back == */
        /* ============================================== */
        .back-link {
            position: absolute; 
            top: 1.5rem;
            left: 1.5rem;
            font-size: 1.8rem; 
            color: #198754; /* Hijau Masjid */
            text-decoration: none;
            transition: transform 0.2s;
            z-index: 10;
        }
        .back-link:hover {
            color: #0f5132; 
            transform: translateX(-5px);
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
            font-size: 5rem; /* Sedikit diperkecil agar proporsional */
            color: #198754; /* Logo jadi Hijau */
            margin-bottom: 1rem;
            text-shadow: 0 4px 10px rgba(25, 135, 84, 0.2);
        }

        h1 {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: #212529; /* Judul Hitam */
        }

        .subtitle {
            font-size: 1rem;
            color: #6c757d; /* Abu-abu untuk subtitle */
            margin-bottom: 3rem;
        }

        a.link {
            color: #198754;
            font-weight: 600;
            text-decoration: none;
        }
        a.link:hover {
            text-decoration: underline;
        }

        .auth-footer p, .auth-footer a {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .auth-footer a {
            font-weight: 600;
            color: #198754; 
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

        .form-control {
            width: 100%;
            background: transparent; 
            border: none;
            /* Border bawah hijau tipis */
            border-bottom: 1px solid #ced4da; 
            color: #212529; /* Teks input gelap */
            font-size: 1rem;
            padding: 0.75rem 0.25rem;
            transition: all 0.3s;
        }
        .form-control::placeholder {
            color: #adb5bd; /* Placeholder abu muda */
        }
        .form-control:focus {
            outline: none;
            border-bottom-color: #198754; /* Saat diklik jadi Hijau */
        }

        /* Fix Autofill Browser agar background tidak jadi kuning/putih blok */
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:hover, 
        .form-control:-webkit-autofill:focus, 
        .form-control:-webkit-autofill:active  {
            -webkit-text-fill-color: #212529 !important; /* Warna teks gelap */
            caret-color: #212529 !important;
            transition: background-color 5000s ease-in-out 0s;
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
            color: #6c757d;
            text-decoration: none;
        }
        .form-options a:hover {
            color: #198754;
            text-decoration: underline;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 0.25rem;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
            width: 20px; 
            height: 20px;
        }
        .password-toggle-icon:hover {
            color: #198754;
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
            border-radius: 12px; /* Lebih rounded */
            border: none;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 1rem; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .btn:active {
            transform: scale(0.98);
        }

        .btn-primary {
            background-color: #198754; /* Hijau Bold */
            color: #ffffff; 
        }
        .btn-primary:hover {
            background-color: #146c43;
            box-shadow: 0 6px 12px rgba(25, 135, 84, 0.2);
        }

        .btn-secondary {
            background-color: #ffffff;
            color: #198754;
            border: 1px solid #198754;
        }
        .btn-secondary:hover {
            background-color: #f8f9fa;
        }

        .btn-social {
            background-color: #ffffff;
            color: #333333;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #e9ecef;
        }
        .btn-social:hover {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }
        .btn-social i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
        }
        
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #adb5bd;
            font-size: 0.875rem;
            margin: 1.5rem 0;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e9ecef;
        }
        .divider:not(:empty)::before {
            margin-right: .5em;
        }
        .divider:not(:empty)::after {
            margin-left: .5em;
        }
        
        .error-message {
            color: #dc3545; 
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