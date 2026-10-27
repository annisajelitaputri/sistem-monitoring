<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicSite\BerandaController;
use App\Http\Controllers\PublicSite\ProfilController;
use App\Http\Controllers\PublicSite\KegiatanController;
use App\Http\Controllers\PublicSite\BeritaController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\KelasController as AdminKelasController;
use App\Http\Controllers\Admin\GuruController as AdminGuruController;
use App\Http\Controllers\Admin\AbsensiGuruController as AdminAbsensiGuruController;
use App\Http\Controllers\Admin\RekapController as AdminRekapController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\KegiatanController as AdminKegiatanController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\AbsensiSiswaController as GuruAbsensiSiswaController;
use App\Http\Controllers\Guru\AbsenSayaController;
use App\Http\Controllers\Guru\RekapKelasController as GuruRekapKelasController;
use App\Http\Controllers\PublicSite\KontakController;


/*
|--------------------------------------------------------------------------
| Web Routes - SMKN 9 Luwu
|--------------------------------------------------------------------------
*/

// ===== HALAMAN PUBLIK =====
Route::get('/', [BerandaController::class, 'index'])->name('beranda');
Route::get('/profil', [ProfilController::class, 'index'])->name('profil');
Route::get('/guru', [ProfilController::class, 'tenagaPendidik'])->name('guru');
Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan');
Route::get('/berita', [BeritaController::class, 'index'])->name('berita');
// 2) Di bagian HALAMAN PUBLIK: ganti route /kontak lama dan tambahkan dua route detail
Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show');
Route::get('/berita/{berita}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('/kontak', [KontakController::class, 'index'])->name('kontak');

// ===== AUTH =====
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// ===== ADMIN / TU =====
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Data siswa & kelas
    Route::resource('siswa', AdminSiswaController::class)
        ->parameters(['siswa' => 'siswa'])
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('kelas', AdminKelasController::class)
        ->parameters(['kelas' => 'kelas'])
        ->only(['index', 'store', 'update', 'destroy']);

    // Data guru -> admin.guru.index / store / update / destroy
    Route::resource('guru', AdminGuruController::class)->only(['index', 'store', 'update', 'destroy']);

    // Absensi guru -> admin.absensi-guru.index / koreksi
    Route::get('absensi-guru', [AdminAbsensiGuruController::class, 'index'])->name('absensi-guru.index');
    Route::put('absensi-guru', [AdminAbsensiGuruController::class, 'koreksi'])->name('absensi-guru.koreksi');

    // Rekap kehadiran -> admin.rekap.index / pdf / excel
    Route::get('rekap', [AdminRekapController::class, 'index'])->name('rekap.index');
    Route::get('rekap/pdf', [AdminRekapController::class, 'pdf'])->name('rekap.pdf');
    Route::get('rekap/excel', [AdminRekapController::class, 'excel'])->name('rekap.excel');

    // Kelola berita -> admin.berita.index / store / update / destroy
    Route::resource('berita', AdminBeritaController::class)
        ->parameters(['berita' => 'berita'])
        ->only(['index', 'store', 'update', 'destroy']);

    // Kelola kegiatan -> admin.kegiatan.index / store / update / destroy
    Route::resource('kegiatan', AdminKegiatanController::class)
        ->parameters(['kegiatan' => 'kegiatan'])
        ->only(['index', 'store', 'update', 'destroy']);

    // Akun pengguna -> admin.users.index / store / update / destroy
    Route::resource('users', AdminUserController::class)
        ->parameters(['users' => 'user'])
        ->only(['index', 'store', 'update', 'destroy']);
});

// ===== GURU =====
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/guru/dashboard', [GuruDashboardController::class, 'index'])
        ->name('guru.dashboard');

    // Input absensi siswa
    Route::get('/guru/absensi-siswa', [GuruAbsensiSiswaController::class, 'index'])
        ->name('guru.absensi-siswa.index');
    Route::post('/guru/absensi-siswa', [GuruAbsensiSiswaController::class, 'store'])
        ->name('guru.absensi-siswa.store');

    // Absen saya
    Route::get('/guru/absen-saya', [AbsenSayaController::class, 'index'])
        ->name('guru.absen-saya.index');
    Route::post('/guru/absen-saya', [AbsenSayaController::class, 'store'])
        ->name('guru.absen-saya.store');

    // Rekap kelas
    Route::get('/guru/rekap-kelas', [GuruRekapKelasController::class, 'index'])
        ->name('guru.rekap-kelas.index');
    Route::get('/guru/rekap-kelas/pdf', [GuruRekapKelasController::class, 'pdf'])
        ->name('guru.rekap-kelas.pdf');
    Route::get('/guru/rekap-kelas/excel', [GuruRekapKelasController::class, 'excel'])
        ->name('guru.rekap-kelas.excel');
});