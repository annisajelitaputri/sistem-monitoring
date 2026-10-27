<!DOCTYPE html>
<html lang="id">
<head>
    @include('partials.header')
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>
<body class="login-body">

<div class="lg-wrap">

    {{-- ============ PANEL KIRI ============ --}}
    <aside class="lg-left">
        <div class="lg-left-bg" style="background-image: url('{{ asset('assets/images/hero.jpg') }}');"></div>
        <div class="lg-left-overlay"></div>

        <div class="lg-left-inner">
            <a href="{{ route('beranda') }}" class="lg-brand">
                <span class="lg-brand-logo"><img src="{{ asset('assets/images/logo.png') }}" alt="Logo SMKN 9 Luwu"></span>
                <span class="lg-brand-name">SMKN 9 LUWU</span>
            </a>

            <div class="lg-hero">
                <span class="lg-badge"><i class="dot"></i> Sistem Monitoring Absensi</span>
                <h1>Selamat datang kembali</h1>
                <p>Masuk untuk mengisi absensi, memantau kehadiran kelas, dan mengunduh rekap bulanan guru dan siswa dalam satu sistem yang tertata.</p>

                <ul class="lg-checks">
                    <li><i data-lucide="check"></i> Absensi guru dan siswa</li>
                    <li><i data-lucide="check"></i> Rekap bulanan PDF dan Excel</li>
                    <li><i data-lucide="check"></i> Monitoring kehadiran kelas</li>
                </ul>
            </div>

            <div class="lg-copy">&copy; {{ date('Y') }} SMKN 9 Luwu</div>
        </div>
    </aside>

    {{-- ============ PANEL KANAN ============ --}}
    <section class="lg-right">
        <span class="lg-plus gold" style="top:68px; right:56px;">+</span>
        <span class="lg-plus blue" style="top:636px; right:30px;">+</span>
        <span class="lg-plus cyan" style="bottom:56px; left:48px;">+</span>
        <span class="lg-circle c1"></span>
        <span class="lg-circle c2"></span>
        <span class="lg-circle c3"></span>

        <div class="lg-box">
            <a href="{{ route('beranda') }}" class="lg-back"><i data-lucide="arrow-left"></i> Kembali ke beranda</a>

            <div class="lg-card">
                <span class="lg-eyebrow">MASUK</span>
                <h2>Masuk ke akun</h2>
                <p class="lg-sub">Gunakan akun yang diberikan oleh admin sekolah.</p>

                @if ($errors->any())
                    <div class="lg-alert"><i data-lucide="circle-alert"></i> {{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('login.post') }}" novalidate>
                    @csrf

                    <label class="lg-label" for="login">NIP atau email</label>
                    <div class="lg-field @error('login') has-error @enderror">
                        <i data-lucide="user"></i>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" placeholder="NIP" autocomplete="username" autofocus required>
                    </div>

                    <label class="lg-label" for="password">Kata sandi</label>
                    <div class="lg-field @error('password') has-error @enderror">
                        <i data-lucide="lock"></i>
                        <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
                        <button type="button" class="lg-eye" id="togglePw" aria-label="Tampilkan kata sandi">
                            <i data-lucide="eye-off"></i>
                        </button>
                    </div>

                    <div class="lg-row">
                        <label class="lg-check">
                            <input type="checkbox" name="remember" value="1" {{ old('remember', true) ? 'checked' : '' }}>
                            <span class="box"></span>
                            Ingat saya
                        </label>
                        <a href="#" class="lg-forgot" onclick="alert('Silakan hubungi admin sekolah untuk mengatur ulang kata sandi.'); return false;">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="lg-submit">Masuk <i data-lucide="log-in"></i></button>
                </form>

                <div class="lg-foot">Belum memiliki akun? Hubungi admin sekolah.</div>
            </div>
        </div>
    </section>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.lucide) lucide.createIcons();

        var btn = document.getElementById('togglePw');
        var input = document.getElementById('password');
        btn.addEventListener('click', function () {
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.innerHTML = '<i data-lucide="' + (show ? 'eye' : 'eye-off') + '"></i>';
            lucide.createIcons();
        });
    });
</script>
</body>
</html>
