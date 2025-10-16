<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesananController;

// Halaman Login (publik)
Route::get('/login', fn() => view('auth.login'))->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua halaman lain harus login
Route::middleware('auth')->group(function () {

    // Dashboard/Home
    Route::get('/', fn() => view('pages.dashboard'))->name('home');
    Route::get('/dashboard', fn() => view('pages.dashboard'))->name('dashboard');

    // -------------------------
    // Master Data (non-outlet)
    // -------------------------
    Route::middleware('checkRole:outlet')->group(function () {
        Route::get('/obat', fn() => view('pages.obat'));
        Route::get('/satuan-obat', fn() => view('pages.satuan-obat'));
        Route::get('/bentuk-sediaan', fn() => view('pages.bentuk-sediaan'));
        Route::get('/pabrik', fn() => view('pages.pabrik'))->name('pabrik.index');
        Route::get('/kategori-obat', fn() => view('pages.kategori-obat'));
        Route::get('/komposisi', fn() => view('pages.komposisi'));
        Route::get('/kreditur', fn() => view('pages.kreditur'));
        Route::get('/outlet', fn() => view('pages.outlet'));
        Route::get('/users', fn() => view('pages.users'));
    });

    // -------------------------
    // Penyediaan (non-outlet)
    // -------------------------
    Route::middleware('checkRole:outlet')->group(function () {
        Route::get('/pesanan', fn() => view('pages.pesanan'));
        Route::get('/pesanan/{id}/print', [PesananController::class, 'print'])->name('pesanan.print');
        Route::get('/penerimaan', fn() => view('pages.penerimaan'));
        Route::get('/kartu-stok', fn() => view('pages.kartu-stok'));
        Route::get('/permintaan', fn() => view('pages.permintaan'));
        Route::get('/mutasi', fn() => view('pages.mutasi'));
        Route::get('/mutasi/create', fn() => view('pages.mutasi-create'))->name('mutasi.create');
    });
});
