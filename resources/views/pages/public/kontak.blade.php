@extends('layouts.situs')

@section('title', 'Kontak')
@section('crumb', 'Kontak')
@section('hero-title', 'Hubungi Kami')
@section('hero-text', 'Punya pertanyaan seputar sekolah? Kunjungi kami atau hubungi lewat kontak resmi di bawah ini.')
@section('hero-icon', 'phone')

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
                <span class="st-info-ic st-ic-teal">@include('partials.situs-icon', ['n' => 'phone', 's' => 28, 'w' => 1.8])</span>
                <div>
                    <small>Telepon</small>
                    <strong><a href="tel:+62{{ ltrim(config('sekolah.telepon'), '0') }}">{{ config('sekolah.telepon') }}</a></strong>
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
