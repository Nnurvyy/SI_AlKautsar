<nav id="sidebar" class="sidebar d-flex flex-column border-end shadow-sm bg-white">
    {{-- Header Sidebar --}}
    <div class="sidebar-header text-center py-4">
        <h5 class="fw-bold text-success ls-1 mb-1">
            <i class="bi bi-mosque me-2"></i>Smart Masjid
        </h5>
        <p class="text-muted small mb-0">{{ $settings->nama_masjid ?? 'Administrator' }}</p>
    </div>

    {{-- Menu List --}}
    <ul class="nav nav-pills flex-column mb-auto px-3 mt-3">
        
        {{-- DASHBOARD --}}
        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.dashboard') }}"
               class="nav-link {{ request()->routeIs('pengurus.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-fill"></i> 
                <span>Dashboard</span>
            </a>
        </li>

        {{-- SECTION KEUANGAN --}}
        <li class="nav-label mt-3 mb-2 px-3 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 1px;">Keuangan</li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.pemasukan.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.pemasukan.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-down-circle-fill"></i> 
                <span>Pemasukan</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.pengeluaran.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.pengeluaran.*') ? 'active' : '' }}">
                <i class="bi bi-arrow-up-circle-fill"></i> 
                <span>Pengeluaran</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.lapkeu.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.lapkeu.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> 
                <span>Laporan Keuangan</span>
            </a>
        </li>

        {{-- SECTION PROGRAM --}}
        <li class="nav-label mt-3 mb-2 px-3 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 1px;">Manajemen</li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.khotib-jumat.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.khotib-jumat.*') ? 'active' : '' }}">
                <i class="bi bi-mic-fill"></i> 
                <span>Khutbah Jumat</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.kajian.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.kajian.*') ? 'active' : '' }}">
                <i class="bi bi-book-half"></i> 
                <span>Kajian</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.inventaris.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.inventaris.index') ? 'active' : '' }}">
                <i class="bi bi-box-seam-fill"></i>
                <span>Barang Inventaris</span>
            </a>
        </li>

        {{-- SECTION DONASI & QURBAN --}}
        <li class="nav-label mt-3 mb-2 px-3 text-uppercase small fw-bold text-muted" style="font-size: 0.7rem; letter-spacing: 1px;">Sosial</li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.infaq-jumat.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.infaq-jumat.index') ? 'active' : '' }}">
                <i class="bi bi-coin"></i> 
                <span>Infaq</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.tabungan-qurban.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.tabungan-qurban.*') ? 'active' : '' }}">
                <i class="bi bi-piggy-bank-fill"></i> 
                <span>Tabungan Qurban</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.donasi.index') }}"
               class="nav-link {{ request()->routeIs('pengurus.donasi.*') ? 'active' : '' }}">
                <i class="bi bi-heart-fill"></i> 
                <span>Donasi</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.program.index') }}" 
               class="nav-link {{ request()->routeIs('pengurus.program.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-event-fill"></i> 
                <span>Program</span>
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="{{ route('pengurus.artikel.index') }}" 
               class="nav-link {{ request()->routeIs('pengurus.artikel.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i> 
                <span>Artikel</span>
            </a>
        </li>
    </ul>

    {{-- Logout Section --}}
    <div class="logout-form mt-auto p-3 border-top">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn btn btn-light text-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2">
                <i class="bi bi-box-arrow-left"></i> Keluar
            </button>
        </form>
    </div>
</nav>