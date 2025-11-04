<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h5>Sistem Informasi</h5>
        <p>E-Masjid</p>
    </div>

    <!-- ====================================================== -->
    <!--          SEMUA NAMA RUTE DI SINI DIPERBARUI            -->
    <!-- ====================================================== -->

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" aria-current="page">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pemasukan.index') }}" class="nav-link {{ request()->routeIs('admin.pemasukan.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i> Pemasukan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.pengeluaran') }}" class="nav-link {{ request()->routeIs('admin.pengeluaran') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up"></i> Pengeluaran
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.lapkeu.index') }}" class="nav-link {{ request()->routeIs('admin.lapkeu.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Laporan Keuangan
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i> Grafik
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.khotib-jumat.index') }}" class="nav-link {{ request()->routeIs('admin.khotib-jumat.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Khotib Jumat
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-person-fill"></i> Kajian
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-collection"></i> Stok & Inventori 
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-cash-coin"></i> Infaq Jumat
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-wallet"></i> Tabungan Hewan Qurban
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-file-earmark-text"></i> Laporan Tabungan Qurban
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-exclamation-triangle"></i> Peringatan
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
