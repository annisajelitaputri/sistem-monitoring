@extends('layouts.situs')

@php
    // Ubah nomor di config (mis. 087731242003) menjadi format internasional 6287731242003
    $wa = preg_replace('/\D/', '', config('sekolah.telepon'));
    $wa = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : $wa;
    $waPesan = rawurlencode('Assalamu\'alaikum, saya ingin bertanya tentang ' . config('sekolah.nama', 'SMKN 9 Luwu') . '.');
@endphp

@section('title', 'Kontak')
@section('crumb', 'Kontak')
@section('hero-title', 'Hubungi Kami')
@section('hero-text', 'Punya pertanyaan seputar sekolah? Kunjungi kami atau hubungi lewat kontak resmi di bawah ini.')
@section('hero-icon', 'phone')

@push('styles')
<style>
    .st-ic-green { background: linear-gradient(135deg, #16a34a, #25d366); }
</style>
@endpush

@section('content')
    <div class="st-kontak">

        <div class="st-info-list">
            <div class="st-info">
                <span class="st-info-ic st-ic-blue">@include('partials.situs-icon', ['n' => 'pin', 's' => 28, 'w' => 1.8])</span>
                <div>
                    <small>Alamat</small>
                    <strong>{{ config('sekolah.alamat') }}</strong>
                </div>
            </div>

            <div class="st-info">
                <span class="st-info-ic st-ic-green">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                </span>
                <div>
                    <small>WhatsApp</small>
                    <strong>
                        <a href="https://wa.me/{{ $wa }}?text={{ $waPesan }}" target="_blank" rel="noopener noreferrer">
                            {{ config('sekolah.telepon') }}
                        </a>
                    </strong>
                </div>
            </div>

            <div class="st-info">
                <span class="st-info-ic st-ic-purple">@include('partials.situs-icon', ['n' => 'mail', 's' => 28, 'w' => 1.8])</span>
                <div>
                    <small>Email</small>
                    <strong><a href="mailto:{{ config('sekolah.email') }}">{{ config('sekolah.email') }}</a></strong>
                </div>
            </div>

            <div class="st-info">
                <span class="st-info-ic st-ic-amber">@include('partials.situs-icon', ['n' => 'clock', 's' => 28, 'w' => 1.8])</span>
                <div>
                    <small>Jam layanan</small>
                    <strong>{{ config('sekolah.jam_hari') }}<br>{{ config('sekolah.jam_pukul') }}</strong>
                </div>
            </div>
        </div>

        {{-- Peta asli lokasi sekolah (Google Maps) --}}
        <div class="st-map">
            <iframe
                title="Peta lokasi {{ config('sekolah.nama') }}"
                src="https://maps.google.com/maps?q={{ config('sekolah.lat') }},{{ config('sekolah.lng') }}&z=17&output=embed"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen></iframe>

            <span class="st-map-label">Peta lokasi sekolah</span>
            <a href="{{ config('sekolah.maps_url') }}" target="_blank" rel="noopener noreferrer" class="st-map-btn">
                Buka di aplikasi peta @include('partials.situs-icon', ['n' => 'arrow', 's' => 16, 'w' => 2])
            </a>
        </div>

    </div>
@endsection