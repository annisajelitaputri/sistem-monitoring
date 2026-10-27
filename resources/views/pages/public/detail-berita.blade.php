@extends('layouts.situs')

@php
    $t = \App\Support\Tampil::tanggal($berita->tanggal, $berita->created_at ?? null);
    $kat = strtolower($berita->kategori);
@endphp

@section('title', $berita->judul)
@section('crumb', 'Berita')
@section('hero-title', $kat === 'pengumuman' ? 'Pengumuman' : 'Detail Berita')
@section('hero-text', 'Informasi resmi dari SMK Negeri 9 Luwu.')
@section('hero-icon', 'news')

@section('content')
    <article class="st-article">
        @if ($berita->gambar)
            <img class="st-article-img" src="{{ asset('storage/' . $berita->gambar) }}" alt="{{ $berita->judul }}">
        @endif

        <div class="st-article-body">
            <div class="st-row-top">
                <span class="st-tag {{ $kat === 'pengumuman' ? 'st-tag-yellow' : 'st-tag-blue' }}">{{ ucfirst($kat) }}</span>
                <span class="st-meta">
                    @include('partials.situs-icon', ['n' => 'calendar', 's' => 15])
                    {{ \App\Support\Tampil::panjang($t) }}
                </span>
            </div>
            <h2>{{ $berita->judul }}</h2>
            <div class="st-article-text">{!! nl2br(e($berita->isi)) !!}</div>

            <a href="{{ route('berita') }}" class="st-link st-back">&larr; Kembali ke berita</a>
        </div>
    </article>
@endsection
