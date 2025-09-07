<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PesananController;
use App\Models\PesananDetail;

Route::get('/', function () {
    return view('pages.home');
});

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

Route::get('/pesanan', function () {
    return view('pages.pesanan');
});

Route::get('/pesanan/{id}/print', [PesananController::class, 'print'])->name('pesanan.print');
