<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PesananController;

// Login page tetap publik
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Semua halaman lain harus login
Route::middleware('auth')->group(function () {

    // Home / Dashboard
    Route::get('/', function () {
        return view('pages.dashboard'); // home sekarang dashboard
    })->name('home');

    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Master Data
    Route::get('/obat', function () {
        return view('pages.obat');
    });
    Route::get('/satuan-obat', function () {
        return view('pages.satuan-obat');
    });
    Route::get('/bentuk-sediaan', function () {
        return view('pages.bentuk-sediaan');
    });
    Route::get('/pabrik', function () {
        return view('pages.pabrik');
    })->name('pabrik.index');
    Route::get('/kategori-obat', function () {
        return view('pages.kategori-obat');
    });
    Route::get('/komposisi', function () {
        return view('pages.komposisi');
    });
    Route::get('/kreditur', function () {
        return view('pages.kreditur');
    });
    Route::get('/outlet', function () {
        return view('pages.outlet');
    });
    Route::get('/users', function () {
        return view('pages.users');
    });

    // Penyediaan
    Route::get('/pesanan', function () {
        return view('pages.pesanan');
    });
    Route::get('/pesanan/{id}/print', [PesananController::class, 'print'])->name('pesanan.print');
    Route::get('/penerimaan', function () {
        return view('pages.penerimaan');
    });
    Route::get('/kartu-stok', function () {
        return view('pages.kartu-stok');
    });
    Route::get('/permintaan', function () {
        return view('pages.permintaan');
    });
    Route::get('/mutasi', function () {
        return view('pages.mutasi');
    });
    Route::get('/mutasi/create', function () {
        return view('pages.mutasi-create');
    })->name('mutasi.create');
});
