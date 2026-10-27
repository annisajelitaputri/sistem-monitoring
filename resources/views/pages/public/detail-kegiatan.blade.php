@extends('layouts.situs')

@php $t = \App\Support\Tampil::tanggal($kegiatan->tanggal, $kegiatan->created_at ?? null); @endphp

@section('title', $kegiatan->judul)
@section('crumb', 'Kegiatan')
@section('hero-title', 'Detail Kegiatan')
@section('hero-text', 'Dokumentasi kegiatan di SMKN 9 Luwu.')
@section('hero-icon', 'image')

@section('content')
    <article class="st-article">
        @if ($kegiatan->foto)
            <img class="st-article-img" src="{{ asset('storage/' . $kegiatan->foto) }}" alt="{{ $kegiatan->judul }}">
        @endif

        <div class="st-article-body">
            <p class="st-meta">
                @include('partials.situs-icon', ['n' => 'calendar', 's' => 15])
                {{ \App\Support\Tampil::panjang($t) }}
            </p>
            <h2>{{ $kegiatan->judul }}</h2>
            <div class="st-article-text">{!! nl2br(e($kegiatan->deskripsi ?: 'Belum ada deskripsi.')) !!}</div>

            <a href="{{ route('kegiatan') }}" class="st-link st-back">&larr; Kembali ke kegiatan</a>
        </div>
    </article>
@endsection
