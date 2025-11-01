<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h5>Sistem Informasi</h5>
        <p>E-Masjid</p>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" aria-current="page">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('pemasukan.index') }}" class="nav-link {{ request()->routeIs('pemasukan.*') ? 'active' : '' }}">
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
                <i class="bi bi-file-earmark-text"></i> Laporan Keuangan
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i> Grafik
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('khotib-jumat.index') }}" class="nav-link {{ request()->routeIs('khotib-jumat.index') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Khotib Jumat
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-person-fill"></i> Kajian
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('inventaris') }}" class="nav-link {{ request()->routeIs('inventaris') ? 'active' : '' }}">
                <i class="bi bi-collection"></i> Stok & Inventori 
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('infaq-jumat') }}" class="nav-link {{ request()->routeIs('infaq-jumat') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Infaq Jumat
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-wallet"></i> Tabungan Hewan Qurban
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