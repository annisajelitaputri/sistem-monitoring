<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard guru') | Guru SMKN 9 Luwu</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/guru-pages.css') }}">
    @stack('styles')
</head>
<body class="admin-body">

<div class="adm-shell">

    @include('partials.sidebar-guru')
    <div class="adm-backdrop" id="admBackdrop"></div>

    <div class="adm-main">
        {{-- ===== TOPBAR ===== --}}
        <header class="adm-topbar">
            <button class="adm-burger" id="admBurger" aria-label="Menu"><i data-lucide="menu"></i></button>

            <div class="adm-title">
                <h1>@yield('page-title', 'Dashboard guru')</h1>
                <p>@yield('page-subtitle')</p>
            </div>

            <div class="adm-top-right">
                <button class="adm-bell" aria-label="Notifikasi">
                    <i data-lucide="bell"></i>
                    <span class="dot"></span>
                </button>

                @php
                    $guruLogin = auth()->user()->guru_id ? \App\Models\Guru::find(auth()->user()->guru_id) : null;
                    $nama = $guruLogin->nama ?? auth()->user()->name ?? 'Guru';
                    $inisial = collect(explode(' ', trim($nama)))->filter()->take(2)
                                ->map(fn ($k) => strtoupper(mb_substr($k, 0, 1)))->implode('');
                @endphp
                <div class="adm-user">
                    <span class="avatar">{{ $inisial }}</span>
                    <strong>{{ $nama }}</strong>
                </div>
            </div>
        </header>

        {{-- ===== ISI HALAMAN ===== --}}
        <main class="adm-content">
            @if (session('success'))
                <div class="gp-alert gp-alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="gp-alert gp-alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="gp-alert gp-alert-error">
                    @foreach ($errors->all() as $e) <div>{{ $e }}</div> @endforeach
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="adm-footer">
            &copy; {{ date('Y') }} SMKN 9 Luwu | Sistem Monitoring Absensi Guru dan Siswa
        </footer>
    </div>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')
</body>
</html>
