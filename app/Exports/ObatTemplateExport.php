<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ObatTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            // Contoh baris kosong
            [
                '',
                'Paracetamol',
                1,
                1,
                1,
                1,
                1,
                1,
                2000,
                3000,
                10,
                '500mg',
                1,
                0,
                0,
                1,
                1
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Obat (Biarkan Kosong)',
            'Nama Obat',
            'Kategori ID',
            'Satuan ID',
            'Sediaan ID',
            'Pabrik ID',
            'Kreditur ID',
            'Komposisi ID',
            'Harga Beli',
            'Harga Jual',
            'Isi Obat',
            'Dosis',
            'Utuh Satuan',
            'Prekursor',
            'Psikotropika',
            'Resep Active',
            'Aktif',
        ];
    }
}
