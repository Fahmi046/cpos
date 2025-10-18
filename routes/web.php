<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesananController;
use App\Livewire\PermintaanForm;

// -------------------------
// Halaman Login (Publik)
// -------------------------
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------
// Semua halaman setelah login
// -------------------------
Route::middleware('auth')->group(function () {

    // Dashboard (semua role bisa)
    Route::get('/', fn() => view('pages.dashboard'))->name('home');
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');

    // =========================================================
    // 🔒 HANYA UNTUK ADMIN / ROLE SELAIN OUTLET
    // =========================================================
    Route::middleware('checkRole:admin,superadmin,gudang')->group(function () {

        // --------- Master Data ----------
        Route::get('/obat', fn() => view('pages.obat'))->name('obat.index');
        Route::get('/satuan-obat', fn() => view('pages.satuan-obat'))->name('satuan-obat.index');
        Route::get('/bentuk-sediaan', fn() => view('pages.bentuk-sediaan'))->name('bentuk-sediaan.index');
        Route::get('/pabrik', fn() => view('pages.pabrik'))->name('pabrik.index');
        Route::get('/kategori-obat', fn() => view('pages.kategori-obat'))->name('kategori-obat.index');
        Route::get('/komposisi', fn() => view('pages.komposisi'))->name('komposisi.index');
        Route::get('/kreditur', fn() => view('pages.kreditur'))->name('kreditur.index');
        Route::get('/outlet', fn() => view('pages.outlet'))->name('outlet.index');
        Route::get('/users', fn() => view('pages.users'))->name('users.index');

        // --------- Penyediaan ----------
        Route::get('/pesanan', fn() => view('pages.pesanan'))->name('pesanan.index');
        Route::get('/pesanan/{id}/print', [PesananController::class, 'print'])->name('pesanan.print');
        Route::get('/penerimaan', fn() => view('pages.penerimaan'))->name('penerimaan.index');
        Route::get('/kartu-stok', fn() => view('pages.kartu-stok'))->name('kartu-stok.index');
        Route::get('/permintaan', fn() => view('pages.permintaan'))->name('permintaan.index');
        Route::get('/mutasi', fn() => view('pages.mutasi'))->name('mutasi.index');
        Route::get('/mutasi/create', fn() => view('pages.mutasi-create'))->name('mutasi.create');
    });

    // =========================================================
    // 🟢 KHUSUS ROLE OUTLET
    // =========================================================
    Route::middleware('checkRole:outlet')->group(function () {
        Route::get('/po', fn() => view('pages.po'))->name('po.index');
        Route::get('/stok-outlet', fn() => view('pages.stok-outlet'))->name('stok-outlet.index');
    });
});
