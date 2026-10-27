@php
    $menu = [
        ['label' => 'Dashboard',           'icon' => 'layout-dashboard', 'route' => 'guru.dashboard',            'active' => 'guru.dashboard'],
        ['label' => 'Input absensi siswa', 'icon' => 'square-check',     'route' => 'guru.absensi-siswa.index',  'active' => 'guru.absensi-siswa.*'],
        ['label' => 'Absen saya',          'icon' => 'user',             'route' => 'guru.absen-saya.index',     'active' => 'guru.absen-saya.*'],
        ['label' => 'Rekap kelas',         'icon' => 'file-text',        'route' => 'guru.rekap-kelas.index',    'active' => 'guru.rekap-kelas.*'],
    ];
@endphp

<aside class="adm-sidebar" id="admSidebar">
    <a href="{{ route('guru.dashboard') }}" class="adm-brand">
        <span class="adm-brand-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo"></span>
        <span class="adm-brand-text">
            <strong>SMKN 9 Luwu</strong>
            <small>Monitoring Absensi</small>
        </span>
    </a>

    <div class="adm-menu-label">PANEL GURU</div>

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
