<?php

namespace App\Exports;

use App\Models\Obat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ObatExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Obat::with(['kategori', 'sediaan', 'komposisi', 'satuan', 'pabrik', 'kreditur'])
            ->get()
            ->map(function ($obat) {
                return [
                    'kode_obat'    => $obat->kode_obat,
                    'nama_obat'    => $obat->nama_obat,
                    'kategori'     => $obat->kategori?->nama_kategori, // Pastikan nama kolomnya benar
                    'sediaan'      => $obat->sediaan?->nama_sediaan,
                    'komposisi'    => $obat->komposisi?->nama_komposisi,
                    'satuan'       => $obat->satuan?->nama_satuan,
                    'pabrik'       => $obat->pabrik?->nama_pabrik,
                    'kreditur'     => $obat->kreditur?->nama_kreditur,
                    'harga_beli'   => $obat->harga_beli,
                    'harga_jual'   => $obat->harga_jual,
                    'isi_obat'     => $obat->isi_obat,
                    'dosis'        => $obat->dosis,
                    'utuh_satuan'  => $obat->utuh_satuan,
                    'prekursor'    => $obat->prekursor,
                    'psikotropika' => $obat->psikotropika,
                    'resep_active' => $obat->resep_active,
                    'aktif'        => $obat->aktif,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Kode Obat',
            'Nama Obat',
            'Kategori',
            'Sediaan',
            'Komposisi',
            'Satuan',
            'Pabrik',
            'Kreditur',
            'Harga Beli',
            'Harga Jual',
            'Isi Obat',
            'Dosis',
            'Utuh Satuan',
            'Prekursor',
            'Psikotropika',
            'Resep Active',
            'Aktif'
        ];
    }
}
