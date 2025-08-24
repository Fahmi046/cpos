<?php

use Illuminate\Support\Facades\Route;

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
