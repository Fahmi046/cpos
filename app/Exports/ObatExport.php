<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ObatExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Obat::with(['pabrik', 'sediaan', 'satuan', 'komposisi', 'kreditur'])
            ->get()
            ->map(function ($obat) {
                return [
                    'kode_obat'    => $obat->kode_obat,
                    'nama_obat'    => $obat->nama_obat,
                    'pabrik'       => $obat->pabrik?->nama_pabrik,
                    'sediaan'      => $obat->sediaan?->nama_sediaan,    // Nama kemasan
                    'satuan'       => $obat->satuan?->nama_satuan,      // Nama satuan
                    'isi_obat'     => $obat->isi_obat,
                    'komposisi'    => $obat->komposisi?->nama_komposisi, // Nama komposisi
                    'hna'          => $obat->harga_beli,
                    'het'          => $obat->het,
                    'kreditur'     => $obat->kreditur?->nama,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Obat',
            'Nama Obat',
            'Pabrik',
            'Kemasan',    // kolom sediaan
            'Satuan',
            'Isi',
            'Komposisi',
            'HNA',
            'HET',
            'Kreditur'
        ];
    }
}
