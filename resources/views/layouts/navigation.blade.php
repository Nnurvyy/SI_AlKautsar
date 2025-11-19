<nav id="sidebar" class="sidebar d-flex flex-column">
    <div class="sidebar-header">
        <h5>Sistem Informasi</h5>
        <p>E-Masjid</p>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" 
               class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> Dashboard
            </a>
        </li>

        {{-- Pemasukan --}}
        <li class="nav-item">
            <a href="{{ route('admin.pemasukan.index') }}" 
               class="nav-link {{ request()->routeIs('admin.pemasukan.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-in-down"></i> Pemasukan
            </a>
        </li>

        {{-- Pengeluaran --}}
        <li class="nav-item">
            <a href="{{ route('admin.pengeluaran.index') }}" 
               class="nav-link {{ request()->routeIs('admin.pengeluaran.*') ? 'active' : '' }}">
                <i class="bi bi-box-arrow-up"></i> Pengeluaran
            </a>
        </li>

        {{-- Laporan Keuangan --}}
        <li class="nav-item">
            <a href="{{ route('admin.lapkeu.index') }}" 
               class="nav-link {{ request()->routeIs('admin.lapkeu.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i> Laporan Keuangan
            </a>
        </li>

        {{-- MENU BARU: TRANSAKSI DONASI --}}
        <li class="nav-item">
            <a href="{{ route('admin.transaksi-donasi.index') }}" 
               class="nav-link {{ request()->routeIs('admin.transaksi-donasi.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2"></i> Transaksi Donasi
            </a>
        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="bi bi-graph-up"></i> Grafik
            </a>
        </li>

        {{-- Khotib Jumat --}}
        <li class="nav-item">
            <a href="{{ route('admin.khotib-jumat.index') }}" 
               class="nav-link {{ request()->routeIs('admin.khotib-jumat.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Khotib Jumat
            </a>
        </li>

        {{-- Kajian --}}
        <li class="nav-item">
            <a href="{{ route('admin.kajian.index') }}" 
               class="nav-link {{ request()->routeIs('admin.kajian.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Kajian
            </a>
        </li>

        {{-- Stok & Inventori --}}
        <li class="nav-item">
            <a href="{{ route('admin.inventaris.index') }}" 
               class="nav-link {{ request()->routeIs('admin.inventaris.*') ? 'active' : '' }}">
                <i class="bi bi-collection"></i> Stok & Inventori
            </a>
        </li>

        {{-- Infaq Jumat --}}
        <li class="nav-item">
            <a href="{{ route('admin.infaq-jumat.index') }}" 
               class="nav-link {{ request()->routeIs('admin.infaq-jumat.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin"></i> Infaq Jumat
            </a>
        </li>

        {{-- Tabungan Qurban --}}
        <li class="nav-item">
            <a href="{{ route('admin.tabungan-qurban.index') }}" 
               class="nav-link {{ request()->routeIs('admin.tabungan-qurban.*') ? 'active' : '' }}">
                <i class="bi bi-wallet"></i> Tabungan Hewan Qurban
            </a>
        </li>

        {{-- Program (Mungkin ini maksudnya Program Donasi?) --}}
        <li class="nav-item">
            <a href="{{ route('admin.program-donasi.index') }}" 
               class="nav-link {{ request()->routeIs('admin.program-donasi.*') ? 'active' : '' }}">
                <i class="bi bi-box-heart"></i> Program Donasi
            </a>
        </li>
    </ul>

    <div class="logout-form">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-left me-2"></i> Keluar
            </button>
        </form>
    </div>
</nav>