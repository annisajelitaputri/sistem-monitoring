
@php
    $links = [
        'beranda' => 'Beranda',
        'profil' => 'Profil',
        'guru' => 'Guru',
        'kegiatan' => 'Kegiatan',
        'berita' => 'Berita',
        'kontak' => 'Kontak',
    ];
@endphp

<header class="nav-bar">
    <div class="wrap">
        <nav class="nav">
            <a class="brand" href="{{ route('beranda') }}">
                <img src="{{ asset('storage/publik/logo.png') }}" alt="Logo SMKN 9 Luwu">
                <span>SMKN 9 LUWU</span>
            </a>

            <div class="links">
                @foreach ($links as $r => $t)
                    <a
                        class="l {{ request()->routeIs($r) ? 'on' : '' }}"
                        href="{{ route($r) }}"
                    >
                        {{ $t }}
                    </a>
                @endforeach
            </div>

            <a class="btn-login" href="{{ route('login') }}">
                <x-icon name="login" />
                Login
            </a>

            <button
                class="nav-toggle"
                type="button"
                aria-label="Menu"
                aria-expanded="false"
            >
                <x-icon name="menu" />
            </button>
        </nav>
    </div>
</header>
