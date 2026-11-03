@extends('layouts.situs')

@php $t = \App\Support\Tampil::tanggal($kegiatan->tanggal, $kegiatan->created_at ?? null); @endphp

@section('title', $kegiatan->judul)

@push('styles')
<style>
    .kd-wrap { padding: 130px 0 80px; }

    .kd-article {
        max-width: 780px; margin: 0 auto;
        overflow: hidden; background: #fff;
        border: 1px solid #e8edf8; border-radius: 28px;
        box-shadow: 0 24px 60px rgba(15, 40, 120, .16);
    }
    .kd-img {
        display: block; width: 100%; height: 340px; object-fit: cover;
    }
    .kd-body { padding: 30px 38px 36px; }
    .kd-body h2 {
        margin: 12px 0 16px; font-size: 28px; font-weight: 700;
        line-height: 1.25; color: #0a1560;
    }
    .kd-text { font-size: 15.5px; line-height: 1.8; color: #27345f; }
    .kd-back { margin-top: 26px; }

    @media (max-width: 760px) {
        .kd-wrap { padding: 110px 0 60px; }
        .kd-img { height: 220px; }
        .kd-body { padding: 22px 20px 26px; }
        .kd-body h2 { font-size: 23px; }
    }
</style>
@endpush

@section('content')
<section class="kd-wrap">
    <div class="st-container">
        <article class="kd-article">
            @if ($kegiatan->foto)
                <img class="kd-img" src="{{ asset('storage/' . $kegiatan->foto) }}" alt="{{ $kegiatan->judul }}">
            @endif

            <div class="kd-body">
                <p class="st-meta">
                    @include('partials.situs-icon', ['n' => 'calendar', 's' => 15])
                    {{ \App\Support\Tampil::panjang($t) }}
                </p>
                <h2>{{ $kegiatan->judul }}</h2>
                <div class="kd-text">{!! nl2br(e($kegiatan->deskripsi ?: 'Belum ada deskripsi.')) !!}</div>

                <a href="{{ route('kegiatan') }}" class="st-link kd-back">&larr; Kembali ke kegiatan</a>
            </div>
        </article>
    </div>
</section>
@endsection