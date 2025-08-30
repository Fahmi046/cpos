<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ObatTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nama_obat',
            'kategori',
            'satuan',
            'sediaan',
            'pabrik',
            'komposisi',
            'kreditur',
            'harga_beli',
            'harga_jual',
            'isi_obat',
            'dosis',
            'utuh_satuan',
            'prekursor',
            'psikotropika',
            'resep_active',
            'aktif'
        ];
    }
}
