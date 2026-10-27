@php
    // Menu sidebar. Ganti 'route' dengan nama route sungguhan setelah halamannya dibuat.
    $menu = [
        ['label' => 'Dashboard',        'icon' => 'layout-dashboard', 'route' => 'admin.dashboard',          'active' => 'admin.dashboard'],
        ['label' => 'Data siswa',       'icon' => 'users',            'route' => 'admin.siswa.index',        'active' => 'admin.siswa.*'],
        ['label' => 'Data guru',        'icon' => 'user',             'route' => 'admin.guru.index',         'active' => 'admin.guru.*'],
        ['label' => 'Data kelas',       'icon' => 'book',             'route' => 'admin.kelas.index',        'active' => 'admin.kelas.*'],
        ['label' => 'Absensi guru',     'icon' => 'square-check',     'route' => 'admin.absensi-guru.index', 'active' => 'admin.absensi-guru.*'],
        ['label' => 'Rekap dan ekspor', 'icon' => 'file-text',        'route' => 'admin.rekap.index',        'active' => 'admin.rekap.*'],
        ['label' => 'Kelola berita',    'icon' => 'newspaper',        'route' => 'admin.berita.index',       'active' => 'admin.berita.*'],
        ['label' => 'Kelola kegiatan',  'icon' => 'calendar-days',    'route' => 'admin.kegiatan.index',     'active' => 'admin.kegiatan.*'],
        ['label' => 'Akun pengguna',    'icon' => 'key-round',        'route' => 'admin.users.index',        'active' => 'admin.users.*'],
    ];
@endphp

<aside class="adm-sidebar" id="admSidebar">
    <a href="{{ route('admin.dashboard') }}" class="adm-brand">
        <span class="adm-brand-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo"></span>
        <span class="adm-brand-text">
            <strong>SMKN 9 Luwu</strong>
            <small>Monitoring Absensi</small>
        </span>
    </a>

    <div class="adm-menu-label">PANEL ADMIN / TU</div>

    <nav class="adm-menu">
        @foreach ($menu as $m)
            <a href="{{ Route::has($m['route']) ? route($m['route']) : '#' }}"
               class="{{ request()->routeIs($m['active']) ? 'active' : '' }}">
                <i data-lucide="{{ $m['icon'] }}"></i>
                <span>{{ $m['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="adm-logout">
        @csrf
        <button type="submit"><i data-lucide="log-out"></i> <span>Logout</span></button>
    </form>
</aside>