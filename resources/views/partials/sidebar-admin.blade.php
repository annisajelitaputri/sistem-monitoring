@php
    $menu = [
        ['route' => 'admin.dashboard',          'match' => 'admin.dashboard',      'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
        ['route' => 'admin.siswa.index',        'match' => 'admin.siswa.*',        'icon' => 'users',            'label' => 'Data siswa'],
        ['route' => 'admin.guru.index',         'match' => 'admin.guru.*',         'icon' => 'user',             'label' => 'Data guru'],
        ['route' => 'admin.kelas.index',        'match' => 'admin.kelas.*',        'icon' => 'tablet',           'label' => 'Data kelas'],
        ['route' => 'admin.mapel.index',        'match' => 'admin.mapel.*',        'icon' => 'book-open',        'label' => 'Data mapel'],
        ['route' => 'admin.absensi-guru.index', 'match' => 'admin.absensi-guru.*', 'icon' => 'square-check',     'label' => 'Absensi guru'],
        ['route' => 'admin.rekap.index',        'match' => 'admin.rekap.*',        'icon' => 'file-text',        'label' => 'Rekap dan ekspor'],
        ['route' => 'admin.berita.index',       'match' => 'admin.berita.*',       'icon' => 'newspaper',        'label' => 'Kelola berita'],
        ['route' => 'admin.kegiatan.index',     'match' => 'admin.kegiatan.*',     'icon' => 'calendar-days',    'label' => 'Kelola kegiatan'],
        ['route' => 'admin.users.index',        'match' => 'admin.users.*',        'icon' => 'key-round',        'label' => 'Akun pengguna'],
    ];
@endphp

<aside class="adm-sidebar" id="admSidebar">
    <a href="{{ route('admin.dashboard') }}" class="adm-brand">
        <span class="adm-brand-logo">
            <img src="{{ asset('storage/publik/logo.png') }}" alt="Logo SMKN 9 Luwu">
        </span>
        <span class="adm-brand-text">
            <strong>SMKN 9 Luwu</strong>
            <small>Monitoring Absensi</small>
        </span>
    </a>

    <div class="adm-menu-label">PANEL ADMIN / TU</div>

    <nav class="adm-menu">
        @foreach ($menu as $m)
            <a href="{{ Route::has($m['route']) ? route($m['route']) : '#' }}"
               class="{{ request()->routeIs($m['match']) ? 'active' : '' }}">
                <i data-lucide="{{ $m['icon'] }}"></i>
                <span>{{ $m['label'] }}</span>
            </a>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="adm-logout">
        @csrf
        <button type="submit">
            <i data-lucide="log-out"></i>
            <span>Logout</span>
        </button>
    </form>
</aside>