<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'SMKN 9 Luwu') | SMKN 9 Luwu</title>
<meta name="description" content="Website resmi SMK Negeri 9 Luwu - Sistem Monitoring Absensi Guru dan Siswa">

<link rel="icon" href="{{ asset('favicon.ico') }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Dela+Gothic+One&display=swap" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@stack('styles')
