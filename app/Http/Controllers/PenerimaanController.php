<?php

namespace App\Http\Controllers;

use App\Models\Penerimaan;
use Illuminate\Http\Request;

class PenerimaanController extends Controller
{
    public function cetak($id)
    {
        $penerimaan = Penerimaan::with(['supplier', 'details.obat', 'details.satuan'])
            ->findOrFail($id);

        return view('penerimaan.print', compact('penerimaan'));
    }

    public function print($id)
    {
        return redirect()->route('penerimaan.print', $id);
    }
}
