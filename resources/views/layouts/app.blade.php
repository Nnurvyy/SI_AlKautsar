<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SI Keuangan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            width: 280px; min-height: 100vh; position: fixed; top: 0; left: 0;
            z-index: 100; background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: all 0.3s ease;
        }
        .main-content {
            margin-left: 280px; transition: all 0.3s ease; padding: 0;
        }
        .sidebar.toggled { margin-left: -280px; }
        .main-content.toggled { margin-left: 0; }
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
        .top-navbar {
            background-color: #ffffff; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 0.75rem 1.5rem;
        }
        #sidebar-toggle { font-size: 1.5rem; color: #343a40; }
        .user-avatar {
            width: 40px; height: 40px; border-radius: 50%; background-color: #e0e7ff;
            color: #4338ca; display: flex; align-items: center;
            justify-content: center; font-weight: 600;
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
        
        /* CSS Untuk Tabel */
        .transaction-table table {
            font-size: 0.875rem; 
        }
        .transaction-table th,
        .transaction-table td {
            padding-top: .8rem;
            padding-bottom: .8rem;
            vertical-align: middle; 
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
            border: 1px solid black;          /* border hitam */
            outline: 2px solid #3c68ab;       /* border biru di luar */
            outline-offset: 2px;              /* jarak antara hitam dan biru */
        }

        .input-group.search-bar .form-control::placeholder {
            color: #adb5bd; /* Warna abu-abu yang lebih terang */
            opacity: 1; /* Pastikan warnanya tidak transparan di semua browser */
        }

        
    </style>
</head>
<body>

    <div>
        @include('layouts.navigation')

        <div id="main-content" class="main-content">
            
            <nav class="top-navbar navbar navbar-expand-lg">
                <div class="container-fluid">
                    
                    <button class="btn btn-link" id="sidebar-toggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="ms-4"> 
                        <h4 class="fw-bold mb-0">@yield('title')</h4> 
                        <p class="text-muted mb-0 d-none d-sm-block">{{ now()->locale('id')->translatedFormat('l, d F Y') }}</p> 
                    </div>

                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center">
                        <li class="nav-item me-3">
                            <a class="nav-link" href="#"><i class="bi bi-bell fs-5"></i></a>
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

            @yield('content')

        </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('toggled');
            document.getElementById('main-content').classList.toggle('toggled');
        });
    </script>
</body>
</html>