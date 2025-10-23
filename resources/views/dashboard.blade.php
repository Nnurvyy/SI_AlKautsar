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

        .sidebar {
            width: 280px;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            background-color: #ffffff; /* Sesuai gambar, sidebar-nya putih */
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .main-content {
            margin-left: 280px; /* Lebar yang sama dengan sidebar */
            transition: all 0.3s ease;
            padding: 0;
        }
        
        /* Style untuk menyembunyikan sidebar */
        .sidebar.toggled {
            margin-left: -280px;
        }
        .main-content.toggled {
            margin-left: 0;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
        }
        .sidebar-header h5 {
            color: #343a40;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .sidebar-header p {
            color: #6c757d;
            font-size: 0.85rem;
            margin-bottom: 0;
        }

        .sidebar .nav-link {
            padding: 0.75rem 1.5rem;
            color: #495057;
            font-weight: 500;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
            text-align: center;
        }
        .sidebar .nav-link.active {
            color: #0d6efd;
            background-color: #e9f0ff;
            border-right: 3px solid #0d6efd;
        }
        .sidebar .nav-link:hover {
            background-color: #f1f3f5;
        }
        
        /* Logout Button */
        .logout-form {
            padding: 0.75rem 1.5rem;
        }
        .logout-btn {
            display: flex;
            align-items: center;
            width: 100%;
            padding: 0.75rem;
            border: none;
            background: none;
            color: #dc3545;
            font-weight: 500;
            text-align: left;
            border-radius: 0.375rem;
        }
        .logout-btn:hover {
            background-color: #fdf2f2;
        }

        .top-navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            padding: 0.75rem 1.5rem;
        }
        
        /* Hamburger Button */
        #sidebar-toggle {
            font-size: 1.5rem;
            color: #343a40;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e0e7ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .stat-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .stat-card .card-body {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card-icon {
            font-size: 1.5rem;  
            width: 48px;       
            height: 48px;       
            border-radius: 50%; 
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .stat-card-icon.bg-success-subtle { color: #198754; }
        .stat-card-icon.bg-danger-subtle { color: #dc3545; }
        .stat-card-icon.bg-primary-subtle { color: #0d6efd; }
        .stat-card-icon.bg-warning-subtle { color: #ffc107; }

        .transaction-table .badge {
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div>
        <nav id="sidebar" class="sidebar d-flex flex-column">
            <div class="sidebar-header">
                <h5>SI Keuangan</h5>
                <p>Pesantren Al Kautsar 561</p>
            </div>

            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link active" aria-current="page">
                        <i class="bi bi-grid-fill"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-box-arrow-in-down"></i> Pemasukan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-box-arrow-up"></i> Pengeluaran
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-file-earmark-text"></i> Laporan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-graph-up"></i> Grafik
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-exclamation-triangle"></i> Peringatan
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-people"></i> Data Santri
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-building"></i> Divisi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="bi bi-person"></i> Pengguna
                    </a>
                </li>
            </ul>

            <div class="logout-form">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="bi bi-box-arrow-left me-2"></i>
                        Keluar
                    </button>
                </form>
            </div>
        </nav>

        <div id="main-content" class="main-content">
            
            <nav class="top-navbar navbar navbar-expand-lg">
                <div class="container-fluid">
                    
                    <button class="btn btn-link" id="sidebar-toggle" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <ul class="navbar-nav ms-auto d-flex flex-row align-items-center">
                        <li class="nav-item me-3">
                            <a class="nav-link" href="#">
                                <i class="bi bi-bell fs-5"></i>
                            </a>
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

            <div class="container-fluid p-4">
                
                <div class="mb-4">
                    <h2 class="fw-bold">Dashboard</h2>
                    <p class="text-muted">Kamis, 23 Oktober 2025</p>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div>
                                    <p class="text-muted mb-1">Total Pemasukan</p>
                                    <h4 class="fw-bold mb-0">Rp 2.000.001</h4>
                                </div>
                                <div class="stat-card-icon bg-success-subtle">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div>
                                    <p class="text-muted mb-1">Total Pengeluaran</p>
                                    <h4 class="fw-bold mb-0">Rp 0</h4>
                                </div>
                                <div class="stat-card-icon bg-danger-subtle">
                                    <i class="bi bi-graph-down-arrow"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div>
                                    <p class="text-muted mb-1">Saldo</p>
                                    <h4 class="fw-bold mb-0">Rp 2.000.001</h4>
                                </div>
                                <div class="stat-card-icon bg-primary-subtle">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3">
                        <div class="card stat-card">
                            <div class="card-body">
                                <div>
                                    <p class="text-muted mb-1">Total Santri Aktif</p>
                                    <h4 class="fw-bold mb-0">1</h4>
                                </div>
                                <div class="stat-card-icon bg-warning-subtle">
                                    <i class="bi bi-person-check"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card transaction-table border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Transaksi Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Tipe</th>
                                        <th scope="col">Kategori</th>
                                        <th scope="col">Deskripsi</th>
                                        <th scope="col" class="text-end">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>22/10/2025</td>
                                        <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill">Pemasukan</span></td>
                                        <td>Donasi</td>
                                        <td>Donasi</td>
                                        <td class="text-end text-success fw-bold">+ Rp 1.000.000</td>
                                    </tr>
                                    <tr>
                                        <td>22/10/2025</td>
                                        <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill">Pemasukan</span></td>
                                        <td>SPP</td>
                                        <td>SPP Santri</td>
                                        <td class="text-end text-success fw-bold">+ Rp 1.000.000</td>
                                    </tr>
                                    <tr>
                                        <td>22/10/2025</td>
                                        <td><span class="badge bg-success-subtle text-success-emphasis rounded-pill">Pemasukan</span></td>
                                        <td>Infaq</td>
                                        <td>pemasukan infaq</td>
                                        <td class="text-end text-success fw-bold">+ Rp 1</td>
                                    </tr>
                                    <tr>
                                        <td>21/10/2025</td>
                                        <td><span class="badge bg-danger-subtle text-danger-emphasis rounded-pill">Pengeluaran</span></td>
                                        <td>Listrik</td>
                                        <td>Bayar listrik bulan Oktober</td>
                                        <td class="text-end text-danger fw-bold">- Rp 500.000</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> </div> </div> <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('toggled');
            document.getElementById('main-content').classList.toggle('toggled');
        });
    </script>
</body>
</html>