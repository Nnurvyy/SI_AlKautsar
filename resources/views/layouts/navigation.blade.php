<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h5>SI Keuangan</h5>
        <p>Pesantren Al Kautsar 561</p>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pemasukan') }}" class="nav-link {{ request()->routeIs('pemasukan') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i> Pemasukan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pengeluaran') }}" class="nav-link {{ request()->routeIs('pengeluaran') ? 'active' : '' }}">
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