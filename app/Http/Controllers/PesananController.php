<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;

class PesananController extends Controller
{
    public function print($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        // arahkan ke folder 'pages'
        return view('pages.print-pesanan', compact('pesanan'));
    }
}
