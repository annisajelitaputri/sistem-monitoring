@props(['name' => 'image', 'n' => null, 's' => 20, 'w' => 1.8])

@php
    $nama = $n ?? $name;

    $icons = [
        'image'    => '<rect x="3" y="3" width="18" height="18" rx="3"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'arrow'    => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'shield'   => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/>',
        'pin'      => '<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0116 0z"/><circle cx="12" cy="10" r="3"/>',
        'phone'    => '<path d="M22 16.92v3a2 2 0 01-2.18 2 19.8 19.8 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.8 19.8 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.13.96.36 1.9.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0122 16.92z"/>',
        'mail'     => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 7l-10 6L2 7"/>',
        'clock'    => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
        'login'    => '<path d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/>',
        'news'     => '<path d="M4 4h13a2 2 0 012 2v14H6a2 2 0 01-2-2V4z"/><path d="M19 8h1a1 1 0 011 1v9a2 2 0 01-2 2"/><path d="M8 8h7M8 12h7M8 16h4"/>',
        'menu'     => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'users'    => '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'icon']) }}
     xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 24 24"
     width="{{ $s }}" height="{{ $s }}"
     fill="none" stroke="currentColor"
     stroke-width="{{ $w }}"
     stroke-linecap="round" stroke-linejoin="round"
     aria-hidden="true">{!! $icons[$nama] ?? $icons['image'] !!}</svg>