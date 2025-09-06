<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;

class PesananController extends Controller
{
    public function print($id)
    {
        $pesanan = Pesanan::findOrFail($id);

        // Hilangkan / dan \ dari no_sp
        $safeName = str_replace(['/', '\\'], '-', $pesanan->no_sp);

        $pdf = \PDF::loadView('pages.print-pesanan', compact('pesanan'));
        return $pdf->download($safeName . '.pdf');
    }
}
