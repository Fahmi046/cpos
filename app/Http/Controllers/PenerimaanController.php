<?php

namespace App\Http\Controllers;

use App\Models\Penerimaan;
use Barryvdh\DomPDF\Facade\Pdf;

class PenerimaanController extends Controller
{
    /**
     * Tampilkan halaman cetak penerimaan.
     */
    public function print($id)
    {
        $penerimaan = Penerimaan::with(['kreditur', 'details.obat', 'details.satuan'])
            ->findOrFail($id);

        // arahkan ke folder 'pages'
        return view('pages.print-penerimaan', compact('penerimaan'));
    }
}
