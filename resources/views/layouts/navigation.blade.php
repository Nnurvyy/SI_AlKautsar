<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h5>Sistem Informasi</h5>
        <p>Smart Masjid</p>
    </div>

    <!-- ====================================================== -->
    <!--       SEMUA 'admin.' DIGANTI JADI 'pengurus.'          -->
    <!-- ====================================================== -->

    <ul class="nav nav-pills flex-column mb-auto">
        {{-- DASHBOARD --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.dashboard') }}"
               class="nav-link {{ request()->routeIs('pengurus.dashboard') ? 'active' : '' }}"
               aria-current="page">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>

        {{-- PEMASUKAN --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.pemasukan.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.pemasukan.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i> Pemasukan
            </a>
        </li>

        {{-- PENGELUARAN --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.pengeluaran') }}"
               class="nav-link {{ request()->routeIs('pengurus.pengeluaran') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up"></i> Pengeluaran
            </a>
        </li>

        {{-- LAPORAN KEUANGAN --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.lapkeu.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.lapkeu.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Laporan Keuangan
            </a>
        </li>

        {{-- GRAFIK (Belum ada rute di web.php, biarkan #) --}}
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i> Grafik
            </a>
        </li>

        {{-- KHOTIB JUMAT --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.khotib-jumat.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.khotib-jumat.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Khotib Jumat
            </a>
        </li>

        {{-- KAJIAN --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.kajian.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.kajian.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Kajian
            </a>
        </li>

        {{-- STOK & INVENTORI --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.inventaris.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.inventaris.index') ? 'active' : '' }}">
                <i class="bi bi-collection"></i> Stok & Inventori
            </a>
        </li>

        {{-- INFAQ JUMAT --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.infaq-jumat.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.infaq-jumat.index') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Infaq Jumat
            </a>
        </li>

        {{-- TABUNGAN HEWAN QURBAN --}}
        <li class="nav-item">
            <a href="{{ route('pengurus.tabungan-qurban.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.tabungan-qurban.*') ? 'active' : '' }}">
                <i class="bi bi-wallet"></i> Tabungan Hewan Qurban
            </a>
        </li>

        {{-- PROGRAM & DONASI (Belum ada rute di web.php) --}}
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i> Program
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i> Donasi
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
